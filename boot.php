<?php
	if (rex::isBackend()) {
		rex_extension::register('REX_FORM_SAVED', function (rex_extension_point $ep) {
			$params = $ep->getParams();
			$formParams = $params['form']->getParams();
			
			switch ($formParams['page']) {
				case 'media_manager/groups':
					switch ($params['form']->isEditMode()) {
						case '0': //add
							$group_id = $params['sql']->getLastId();
							$sql = rex_sql::factory();
							
							//Start - add grouped types
								$breakpoints = media_manager_plus::getBreakpoints();
								$resolutions = media_manager_plus::getResolutions();
								foreach ($breakpoints as $breakpoint => $mediaquery) {
									$sql->setQuery('INSERT INTO '.$params['form']->getTableName().' (`status`, `name`, `description`, `group`, `subgroup`) VALUES (?,?,?,?,?)', ['0', $_POST[$params['form']->getName()]['name'].'-'.$breakpoint, 'generated [group '.$group_id.']', $group_id, 0]);
									echo $sql->getError();
                                    $subgroup_id = $sql->getLastId();
									foreach ($resolutions as $resolution => $factor) {
										$sql->setQuery('INSERT INTO '.$params['form']->getTableName().' (`status`, `name`, `description`, `group`, `subgroup`) VALUES (?,?,?,?,?)', ['0', $_POST[$params['form']->getName()]['name'].'-'.$breakpoint.'@'.$resolution, 'generated [group '.$group_id.', subgroup '.$subgroup_id.']', $group_id, $subgroup_id]);
										echo $sql->getError();
									}
								}
								
								unset($sql);
							//End - add grouped types
						break;
						case '1': //edit
							$sql = rex_sql::factory();
						
							//Start - edit grouped types
								$breakpoints = media_manager_plus::getBreakpoints();
								$resolutions = media_manager_plus::getResolutions();
								foreach ($breakpoints as $breakpoint => $mediaquery) {
									$sql->setQuery('UPDATE '.$params['form']->getTableName().' SET name = ? WHERE name LIKE ? AND `group` = ?', [$_POST[$params['form']->getName()]['name'].'-'.$breakpoint, '%-'.$breakpoint, $formParams['group_id']]);
									
									foreach ($resolutions as $resolution => $factor) {
										$sql->setQuery('UPDATE '.$params['form']->getTableName().' SET name = ? WHERE name LIKE ? AND `group` = ?', [$_POST[$params['form']->getName()]['name'].'-'.$breakpoint.'@'.$resolution, '%-'.$breakpoint.'@'.$resolution, $formParams['group_id']]);
									}
								}
								
								unset($sql);
							//End - edit grouped types
						break;
					}
				break;
				case 'media_manager/types':
					if (media_manager_plus_backend::isGroup($formParams['type_id'])) {
						if (!empty($formParams['effects'])) {
							media_manager_plus_backend::generateEffectForGroup($formParams['type_id']);
						}
					} else {
						rex_response::sendRedirect(rex_url::backendPage('media_manager/singletypes'));
					}
				break;
			}
		});
		
		//Start - inject media_manager
			if (rex_request::get('page') == 'media_manager/types' && rex_request::get('func') == 'copy' && rex_request::get('type_id') != '0') {
				rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) { //todo: not the best EP, but it works
					rex_response::sendRedirect(rex_url::backendPage('media_manager/singletypes'));
				}, rex_extension::LATE);
			}
			
			if (rex_request::get('page') == 'media_manager/types' && rex_request::get('func') == 'delete_cache' && rex_request::get('type_id') != '0') {
				rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) { //todo: not the best EP, but it works
					rex_response::sendRedirect(rex_url::backendPage('media_manager/singletypes'));
				}, rex_extension::LATE);
			}
			
			if (rex_request::get('page') == 'media_manager/types' && rex_request::get('func') == 'delete' && rex_request::get('type_id') != '') {
				rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) { //todo: not the best EP, but it works
					rex_response::sendRedirect(rex_url::backendPage('media_manager/singletypes'));
				}, rex_extension::LATE);
			}
			
			if (rex_request::get('page') == 'media_manager/types' && rex_request::get('effects') == '1' && rex_request::get('func') == 'delete') {
				rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) { //todo: not the best EP, but it works
					$type_id = rex_request::get('type_id');
					if (media_manager_plus_backend::isGroup($type_id)) {
						media_manager_plus_backend::generateEffectForGroup($type_id);
						rex_response::sendRedirect(rex_url::backendPage('media_manager/groups'));
					} else {
						rex_response::sendRedirect(rex_url::backendPage('media_manager/singletypes'));
					}
				}, rex_extension::EARLY);
			}
		//End - inject media_manager
		
		rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
			$subject = $ep->getSubject();
			
			//Start - remove page-tab
				$subject = str_replace('<li><a href="index.php?page=media_manager/types">'.rex_i18n::msg('media_manager_subpage_types').'</a></li>', '', $subject);
				$subject = str_replace('<li class="active "><a href="index.php?page=media_manager/types">'.rex_i18n::msg('media_manager_subpage_types').'</a></li>', '', $subject);
			//End - remove page-tab
			
			//Start - remove backbutton
				$subject = str_replace('<a class="btn btn-back" href="index.php?page=media_manager/types">'.rex_i18n::msg('media_manager_back').'</a>', '', $subject);
			//End - remove backbutton
			
			//Start - remove deletebutton
				if (rex_request::get('page') == 'media_manager/types' && rex_request::get('effects') == '1' && rex_request::get('func') == 'edit' && media_manager_plus_backend::isGroup(rex_request::get('type_id'))) {
					$subject = preg_replace('/<button id="rex-media-manager-type-effect-[^-]*-delete"[^>]*>[^<]*<\/button>/', '', $subject);
				}
			//End - remove deletebutton
			
			//Start - reroute navigation
				$subject = str_replace('<a href="index.php?page=media_manager/types"><i class="rex-icon rex-icon-media"></i> Media Manager</a>', '<a href="index.php?page=media_manager/overview"><i class="rex-icon rex-icon-media"></i> Media Manager</a>', $subject);
			//End - reroute navigation
				
			$ep->setSubject($subject);
		}, rex_extension::EARLY);
	}


class_alias('media_manager_plus_frontend', 'mmp');
?>