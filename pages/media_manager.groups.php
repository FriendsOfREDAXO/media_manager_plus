<?php
	$func = rex_request('func', 'string');
	$group_id = rex_request('group_id', 'int');
	
	$success = '';
	$error = '';
	
	switch ($func) {
		case 'delete':
			if ($group_id > 0) {
				$sql = rex_sql::factory();
				
				//Start - get all types in this group
					$types = $sql->getArray("SELECT `id` FROM `".rex::getTablePrefix()."media_manager_type` WHERE `group` = ?", [$group_id]);
				//End - get all types in this group
				
				foreach ($types as $type) {
					$sql->setQuery('DELETE FROM `'.rex::getTablePrefix().'media_manager_type_effect` WHERE `type_id` = ?', [$type['id']]);
				}
				
				$sql->setQuery('DELETE FROM `'.rex::getTablePrefix().'media_manager_type` WHERE `id` = ? OR `group` = ?', [$group_id, $group_id]);
				
				unset($sql);
			}
			
			$func = '';
		break;
		case 'copyEffects':
			$from = rex_request('from', 'int');
			$to = rex_request('to', 'int');
			
			$sql = rex_sql::factory();
			
			//Start - get all types in this group
				$fromTypes = $sql->getArray("SELECT `id` FROM `".rex::getTablePrefix()."media_manager_type` WHERE `subgroup` = ? or `id` = ? ORDER BY `id` ASC", [$from, $from]);
				$toTypes = $sql->getArray("SELECT `id` FROM `".rex::getTablePrefix()."media_manager_type` WHERE `subgroup` = ? or `id` = ? ORDER BY `id` ASC", [$to, $to]);
				
				if (count($fromTypes) == count($toTypes)) {
					foreach ($fromTypes as $index => $fromType) {
						//Start - get all effects of this type
							$effects = $sql->getArray("SELECT * FROM `".rex::getTablePrefix()."media_manager_type_effect` WHERE `type_id` = ? ORDER BY `priority` ASC", [$fromType['id']]);
							foreach ($effects as $effect) {
								$sql->setQuery("INSERT INTO `".rex::getTablePrefix()."media_manager_type_effect` (`type_id`, `effect`, `parameters`, `priority`, `updatedate`, `updateuser`, `createdate`, `createuser`) VALUES (?,?,?,?,NOW(),?,NOW(),?)", [$toTypes[$index]['id'], $effect['effect'], $effect['parameters'], $effect['priority'], $effect['updateuser'], $effect['createuser']]);
							}
						//End - get all effects of this type
					}
				} else {
					die('Fehler: Unterschiedliche anzahl breakpoints');
				}
				
			//End - get all types in this group
			
			unset($sql);
			
			$func = '';
		break;
	}
	
	if ($success != '') {
		echo rex_view::success($success);
	}
	
	if ($error != '') {
		echo rex_view::error($error);
	}

	if ($func == '') {
		$list = rex_list::factory("SELECT `id`, `name`, `group` FROM `".rex::getTablePrefix()."media_manager_type` WHERE `description` = 'generated' AND `group` = 0 AND subgroup = 0 ORDER BY `name` ASC", 100);
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage($this->i18n('groups_norowsmessage'));

		// icon column
		$thIcon = '<a href="'.$list->getUrl(['func' => 'add']).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon rex-icon-mediatype"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'edit', 'group_id' => '###id###']);
		
		$list->setColumnLabel('name', $this->i18n('groups_column_name'));
		$list->setColumnFormat('name', 'custom', function ($params) {
			$list = $params['list'];
			$name = '';
			$name .= '<strong>';
			$name .= $list->getValue('name');
			$name .= '<span class="btn-group-xs">';
			$name .= '  <a href="'.$list->getUrl(['func' => 'edit', 'group_id' => $list->getValue('id')]).'" class="btn btn-edit" title="editieren"><i class="rex-icon rex-icon-edit"></i></a>';
			$name .= '  <a href="'.$list->getUrl(['func' => 'delete', 'group_id' => $list->getValue('id')]).'" class="btn btn-delete" title="'.$this->i18n('groups_action_delete').'" data-confirm="'.$this->i18n('groups_action_delete').'?"><i class="rex-icon rex-icon-delete"></i></a>';
			$name .= '</span>';
			$name .= '</strong>';
			
			$name .= '<br>';
			$name .= '<br>';
			
			//Start - get groups for this type
				$sql = rex_sql::factory();
				$groupsByBreakpoints = $sql->getArray('SELECT * FROM `'.rex::getTablePrefix().'media_manager_type` WHERE `group` = ? AND subgroup = 0', [$list->getValue('id')]);
				
				if (!empty($groupsByBreakpoints)) {
					foreach ($groupsByBreakpoints as $groupsByBreakpoint) {
						$name .= '<div class="panel panel-default">';
						$name .= '  <header class="panel-heading collapsed" data-toggle="collapse" data-target="#collapse-type-'.$groupsByBreakpoint['id'].'-group-'.$groupsByBreakpoint['group'].'-subgroup-'.$groupsByBreakpoint['subgroup'].'">';
						$name .= '    <div class="panel-title">';
						$name .= '      '.$groupsByBreakpoint['name'];
						$name .= '      <span class="btn-group-xs">';
						$name .= '        <a href="'.rex_url::backendPage('media_manager/types', ['type_id' => $groupsByBreakpoint['id'], 'effects' => '1']).'" class="btn btn-edit" title="editieren"><i class="rex-icon rex-icon-edit"></i></a>';
						$name .= '      </span>';
						$name .= '    </div>';
						$name .= '  </header>';
						$name .= '  <div id="collapse-type-'.$groupsByBreakpoint['id'].'-group-'.$groupsByBreakpoint['group'].'-subgroup-'.$groupsByBreakpoint['subgroup'].'" class="panel-collapse collapse">';
						
						$groupsByResolutions = $sql->getArray('SELECT * FROM `'.rex::getTablePrefix().'media_manager_type` WHERE `subgroup` = ?', [$groupsByBreakpoint['id']]);
						if (!empty($groupsByResolutions)) {
							$name .= '<ul class="list-group">';
							foreach ($groupsByResolutions as $groupsByResolutionIndex => $groupsByResolution) {
								$hasEffects = false;
								
								$name .= '<li class="list-group-item">';
								$name .= $groupsByResolution['name'];
								
								//Start - get effects for this type
									$effects = $sql->getArray('SELECT * FROM '.rex::getTablePrefix().'media_manager_type_effect WHERE type_id = ? ORDER by priority', [$groupsByResolution['id']]);
									if (!empty($effects)) {
										$name .= '<br><br>';
										$name .= '<div class="panel panel-default" style="margin-bottom:0px;">';
										$name .= '  <header class="panel-heading collapsed" data-toggle="collapse" data-target="#collapse-type-'.$groupsByBreakpoint['id'].'-group-'.$groupsByBreakpoint['group'].'-subgroup-'.$groupsByBreakpoint['subgroup'].'-effects-'.$groupsByResolution['id'].'">';
										$name .= '    <div class="panel-title">'.count($effects).' Effekte</div>';
										$name .= '  </header>';
										$name .= '  <div id="collapse-type-'.$groupsByBreakpoint['id'].'-group-'.$groupsByBreakpoint['group'].'-subgroup-'.$groupsByBreakpoint['subgroup'].'-effects-'.$groupsByResolution['id'].'" class="panel-collapse collapse">';
										$name .= '    <table class="table">';
										
										foreach ($effects as $effect) {
											$name .= '      <tr>';
											$name .= '        <td colspan="3">'.rex_i18n::msg('media_manager_effect_'.$effect['effect']).'</td>';
											$name .= '      </tr>';
											
											$parameters = json_decode($effect['parameters'], true);
											
											//Start - init class
												$className = 'rex_effect_'.$effect['effect'];
												$effectObj = new $className();
												$effectParams = $effectObj->getParams();
											//End - init class
											
											if (!empty($effectParams) && !empty($parameters['rex_effect_'.$effect['effect']])) {
												foreach ($parameters['rex_effect_'.$effect['effect']] as $key => $value) {
													//Start - get correct label
														foreach ($effectParams as $effectParam) {
															if ($key == 'rex_effect_'.$effect['effect'].'_'.$effectParam['name']) {
																$label = $effectParam['label'];
																break;
															}
														}
													//End - get correct label
													
													$name .= '      <tr>';
													$name .= '        <td>&nbsp;</td>';
													$name .= '        <td>'.$label.'</td>';
													$name .= '        <td>'.$value.'</td>';
													$name .= '      </tr>';
												}
											}
										}
										
										$name .= '    </table>';
										$name .= '  </div>';
										$name .= '</div>';
									}
								//End - get effects for this type
								
								$name .= '</li>';
								if (empty($effects) && $groupsByResolutionIndex == (count($groupsByResolutions) - 1)) {
									$name .= '<li class="list-group-item">';
									$name .= '<div class="btn-toolbar">';
									foreach ($groupsByBreakpoints as $groupsByBreakpointSubround) {
										if ($groupsByBreakpointSubround['name'] == $groupsByBreakpoint['name']) continue;
										$name .= '	<a href="'.rex_url::backendPage('media_manager/'.rex_be_controller::getCurrentPagePart(2), ['func' => 'copyEffects', 'from' => $groupsByBreakpointSubround['id'], 'to' => $groupsByBreakpoint['id']]).'" class="btn btn-update">Übernehmen von '.$groupsByBreakpointSubround['name'].'</a>';
									}
									$name .= '</div>';
									
									$name .= '</li>';
								}
							}
							
							$name .= '</ul>';
						}
						
						$name .= '  </div>';
						$name .= '</div>';
					}
				}
			//End - get groups for this type
			
			return $name;
		});
		
		$list->removeColumn('id');
		$list->removeColumn('group');
		
		$content = $list->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('content', $content, false);
		$content = $fragment->parse('core/page/section.php');

		echo $content;
	} else if ($func == 'add' || $func == 'edit') {
		$group_id = rex_request('group_id', 'int');
		
		if ($func == 'edit') {
			$formLabel = $this->i18n('groups_formcaption_edit');
		} elseif ($func == 'add') {
			$formLabel = $this->i18n('groups_formcaption_add');
		}
		
		rex_extension::register('REX_FORM_CONTROL_FIELDS', function (rex_extension_point $ep) {
			$controlFields = $ep->getSubject();
			$controlFields['apply'] = '';
			$controlFields['delete'] = '';
			return $controlFields;
		});
		
		$form = rex_form::factory(rex::getTablePrefix().'media_manager_type', '', 'id='.$group_id);
		
		$form->addErrorMessage(REX_FORM_ERROR_VIOLATE_UNIQUE_KEY, rex_i18n::msg('media_manager_error_type_name_not_unique'));
		
		//Start - add name-field
			$field = $form->addTextField('name');
			$field->setLabel($this->i18n('groups_label_name'));
		//End - add name-field
		
		//Start - add description-field
			$field = $form->addHiddenField('description', 'generated');
		//End - add description-field
		
		if ($func == 'edit') {
			$form->addParam('group_id', $group_id);
		}
		
		$content = $form->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content, false);
		$content = $fragment->parse('core/page/section.php');

		echo $content;
	}
