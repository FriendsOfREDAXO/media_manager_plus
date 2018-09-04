<?php
	class media_manager_plus {
		
		public static function generateBackgroundImage($selector, $mediatype, $filename, $filenamesByBreakpoint = []) {
			//Start - define filename by breakpoint
				$filenames = [];
				foreach (self::getBreakpoints() as $breakpoint => $mediaquery) {
					if (in_array($breakpoint, $filenamesByBreakpoint)) {
						$filenames[$breakpoint] = $filenamesByBreakpoint[$breakpoint];
					} else {
						$filenames[$breakpoint] = $filename;
					}
				}
			//End - define filename by breakpoint
			
			$str = '';
			
			foreach (self::getBreakpoints() as $breakpoint => $mediaquery) {
				foreach (self::getResolutions() as $resolution => $factor) {
					if ($resolution == 'lazy') continue;
					
					$str .= '@media only screen and '.$mediaquery.' and (-webkit-min-device-pixel-ratio: '.intval($factor).'), only screen and (min--moz-device-pixel-ratio: '.intval($factor).'), only screen and '.$mediaquery.' and (-o-min-device-pixel-ratio: '.intval($factor).'/1), only screen and '.$mediaquery.' and (min-device-pixel-ratio: '.intval($factor).'), only screen and '.$mediaquery.' and (min-resolution: '.intval($factor * 96).'dpi), only screen and '.$mediaquery.' and (min-resolution: '.intval($factor).'dppx) {'.PHP_EOL;
					$str .= '  '.$selector.' {'.PHP_EOL;
					$str .= '    background-image:url(index.php?rex_media_type='.$mediatype.'-'.$breakpoint.'@'.$resolution.'&rex_media_file='.$filenames[$breakpoint].');'.PHP_EOL;
					$str .= '  }'.PHP_EOL;
					$str .= '}'.PHP_EOL;
				}
			}
			
			return $str;
		}
		
		public static function generatePictureTag($mediatype, $filename, $filenamesByBreakpoint = [], $lazyload = true) {
			//Start - define filename by breakpoint
				$filenames = [];
				foreach (self::getBreakpoints() as $breakpoint => $mediaquery) {
					if (in_array($breakpoint, $filenamesByBreakpoint)) {
						$filenames[$breakpoint] = $filenamesByBreakpoint[$breakpoint];
					} else {
						$filenames[$breakpoint] = $filename;
					}
				}
			//End - define filename by breakpoint
			
			$defaultImg = rex_url::media($filename);
			
			$str = '';
			$str .= '<picture>'.PHP_EOL;
			
			if (!$lazyload) {
				foreach (self::getBreakpoints() as $breakpoint => $mediaquery) {
					$str .= '<source media="'.$mediaquery.'" ';
					
					//Start - generate srcset
						$str .= 'srcset="';
						foreach (self::getResolutions() as $resolution => $factor) {
							if ($resolution == 'lazy') {
								continue;
							}
							
							$str .= 'index.php?rex_media_type='.$mediatype.'-'.$breakpoint.'@'.$resolution.'&rex_media_file='.$filenames[$breakpoint].' '.$resolution.',';
						}
						$str = substr($str, 0, -1);
					//End - generate srcset
					
					$str .= '">'.PHP_EOL;
				}
			} else {
				foreach (self::getBreakpoints() as $breakpoint => $mediaquery) {
					$str .= '<source media="'.$mediaquery.'" ';
					
					//Start - generate srcset
						$str .= 'srcset="';
						$str .= 'index.php?rex_media_type='.$mediatype.'-'.$breakpoint.'@lazy&rex_media_file='.$filenames[$breakpoint];
						$str .= '"';
					//End - generate srcset
					
					//Start - generate data-srcset
						$str .= ' data-srcset="';
						foreach (self::getResolutions() as $resolution => $factor) {
							if ($resolution == 'lazy') {
								continue;
							}
							
							$str .= 'index.php?rex_media_type='.$mediatype.'-'.$breakpoint.'@'.$resolution.'&rex_media_file='.$filenames[$breakpoint].' '.$resolution.',';
						}
						$str = substr($str, 0, -1);
					//End - generate data-srcset
					
					$str .= '">'.PHP_EOL;
				}
			}
			
			$str .= '	<img '.(($lazyload) ? 'class="lazyload"' : '').' src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="'.addslashes(rex_media::get($filename)->getTitle()).'">'.PHP_EOL;
			
			$str .= '</picture>'.PHP_EOL;
			
			return $str;
		}
		
		public static function getBreakpoints() {
			$sql = rex_sql::factory();
			$breakpoints = $sql->getArray("SELECT name, mediaquery FROM `".rex::getTablePrefix()."media_manager_plus_breakpoints` ORDER BY `name` ASC");
			unset($sql);
			
			$breakpointsCombined = array_combine(array_column($breakpoints, 'name'), array_column($breakpoints, 'mediaquery'));
			
			return $breakpointsCombined;
		}
		
		public static function getResolutions() {
			return [
				'1x' => 1.0,
				'2x' => 2.0,
				'3x' => 3.0,
				'lazy' => 0.5
			];
		}
		
		public static function isGroup($type_id) {
			$sql = rex_sql::factory();
			$type = $sql->getArray('SELECT * FROM `'.rex::getTablePrefix().'media_manager_type` WHERE `id` = ?', [$type_id]);
			unset($sql);
			
			if (!empty($type[0])) {
				if ($type[0]['group'] != '0' || $type[0]['description'] == 'generated') {
					return true;
				}
			}
			
			return false;
		}
		
		public static function generateEffectForGroup($group_id) {
			$sql = rex_sql::factory();
			
			//Start - get current type
				$currentType = $sql->getArray('SELECT `name` FROM `'.rex::getTablePrefix().'media_manager_type` WHERE `id` = ?', [$group_id]);
			//End - get current type
			
			if (!empty($currentType)) {
				//Start - add effects to each type of this subgroup
					$resolutions = self::getResolutions();
					foreach ($resolutions as $resolution => $factor) {
						//Start - get type with this resolution
							$resolutionType = $sql->getArray('SELECT `id` FROM `'.rex::getTablePrefix().'media_manager_type` WHERE `name` = ?', [$currentType[0]['name'].'@'.$resolution]);
							if (!empty($resolutionType)) {
								//Start - delete all effects for this type
									$sql->setQuery('DELETE FROM `'.rex::getTablePrefix().'media_manager_type_effect` WHERE `type_id` = ?', [$resolutionType[0]['id']]);
								//End - delete all effects for this type
								
								//Start - get all effects for this type's parent
									$effects = $sql->getArray('SELECT * FROM `'.rex::getTablePrefix().'media_manager_type_effect` WHERE `type_id` = ? ORDER BY `priority` ASC', [$group_id]);
									if (!empty($effects)) {
										foreach ($effects as $effect) {
											$parameters = json_decode($effect['parameters'], true);
											
											if (!empty($parameters['rex_effect_'.$effect['effect']])) {
												foreach ($parameters['rex_effect_'.$effect['effect']] as $key => $value) {
													if ((strpos($key, 'height') !== false || strpos($key, 'width') !== false && is_numeric($value)) && $value !== '') {
														$parameters['rex_effect_'.$effect['effect']][$key] = $value * $factor;
													}
												}
											}
											
											//Start - save effect to this type
												$sql->setQuery('INSERT INTO `'.rex::getTablePrefix().'media_manager_type_effect` (`type_id`, `effect`, `parameters`, `priority`, `updatedate`, `updateuser`, `createdate`, `createuser`) VALUES (?,?,?,?,NOW(),"media_manager_plus",NOW(),"media_manager_plus")', [$resolutionType[0]['id'], $effect['effect'], json_encode($parameters), $effect['priority']]);
											//End - save effect to this type
										}
									}
								//End - get all effects for this type's parent
							}
						//End - get type with this resolution
					}
				//End - add effects to each type of this subgroup
				
				//Start - delete cache
					rex_media_manager::deleteCache();
				//End - delete cache
			}
			
			unset($sql);
		}
	}
?>