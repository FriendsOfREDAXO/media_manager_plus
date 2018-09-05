<?php
	class media_manager_plus_frontend extends media_manager_plus {
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
	}
?>