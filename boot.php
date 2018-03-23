<?php
	if (rex::isBackend()) {
		rex_extension::register('REX_FORM_SAVED', function (rex_extension_point $ep) {
			$params = $ep->getParams();
			$formParams = $params['form']->getParams();
			
			switch ($formParams['page']) {
				case 'media_manager_plus/groups':
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
									$subgroup_id = $params['sql']->getLastId();
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
					if (!empty($formParams['effects'])) {
						media_manager_plus::generateEffectForGroup($formParams['type_id']);
					}
				break;
			}
		});
		
		//Start - inject media_manager
			if (rex_request::get('page') == 'media_manager/types' && rex_request::get('effects') == '1' && rex_request::get('func') == 'delete') {
				rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
					$type_id = rex_request::get('type_id');
					media_manager_plus::generateEffectForGroup($type_id);
					
					rex_response::sendRedirect(rex_url::backendPage('media_manager_plus/groups'));
				}, rex_extension::LATE);
			}
			
			rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
				$subject = $ep->getSubject();
				
				//Start - hide mediamanager in navigation
					$subject = str_replace('<li id="rex-navi-page-media-manager" class="rex-has-icon"><a href="index.php?page=media_manager/types"><i class="rex-icon rex-icon-media"></i> Media Manager</a></li>', '', $subject);
					$subject = str_replace('<li id="rex-navi-page-media-manager" class="active rex-has-icon"><a href="index.php?page=media_manager/types"><i class="rex-icon rex-icon-media"></i> Media Manager</a></li>', '', $subject);
				//End - hide mediamanager in navigation
				
				//Start - reroute mediamanager-urls
					$subject = str_replace('<a href="index.php?page=media_manager/types">', '<a href="index.php?page=media_manager_plus/groups">', $subject);
				//End - reroute mediamanager-urls
				
				if (rex_request::get('page') == 'media_manager/types' && rex_request::get('effects') == '1' && rex_request::get('func') == 'edit') {
					//Start - hide deletebutton
						$subject = preg_replace('/<button id="rex-media-manager-type-effect-[^-]*-delete"[^>]*>[^<]*<\/button>/', '', $subject);
					//End - hide deletebutton
				}
				
				$ep->setSubject($subject);
			}, rex_extension::EARLY);
		//End - inject media_manager
	}
?>