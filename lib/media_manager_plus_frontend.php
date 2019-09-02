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
			$str  = rex_extension::registerPoint(new rex_extension_point('MMP_BEFORE_PICTURETAG', $str, ['mediatype' => $mediatype, 'filename' => $filename, 'filenamesByBreakpoint' => $filenamesByBreakpoint, 'lazyload' => boolval($lazyload)]));
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
			$classes = [];
			$imgtag = '';
			
			if ($lazyload) $classes[] = 'lazyload';

			$classes = rex_extension::registerPoint(new rex_extension_point('MMP_IMG_CLASS', $classes, ['mediatype' => $mediatype, 'filename' => $filename, 'filenamesByBreakpoint' => $filenamesByBreakpoint, 'lazyload' => boolval($lazyload)]));

			$imgtag = rex_extension::registerPoint(new rex_extension_point('MMP_IMGTAG', $imgtag, ['mediatype' => $mediatype, 'filename' => $filename, 'filenamesByBreakpoint' => $filenamesByBreakpoint, 'lazyload' => boolval($lazyload)]));

			if ($imgtag == '') {

				$imgSrcPath = 'index.php?rex_media_type='.$mediatype.'-fallback@1x'.'&rex_media_file='.$filename;

				$imgSrcSetPath = '';
				foreach (self::getResolutions() as $resolution => $factor) {
						if ($resolution == 'lazy') continue;
						$imgSrcSetPath .= 'index.php?rex_media_type='.$mediatype.'-fallback@'.$resolution.''.'&rex_media_file='.$filename.' '.$resolution.',';
				}
				$imgSrcSetPath = substr($imgSrcSetPath, 0, -1);

				if (!$lazyload) {

					$imgSrc = $imgSrcPath;
					$imgSrcSet = $imgSrcSetPath;

					$imgLazySrc = '';
					$imgLazySrcSet = '';

				} else {
					$imgSrcLazy = 'index.php?rex_media_type='.$mediatype.'-fallback@lazy'.'&rex_media_file='.$filename;

					$imgSrc = $imgSrcLazy;
					$imgLazySrc = ' data-src="'.$imgSrcPath.'"';

					$imgSrcSet = $imgSrcLazy;

					$imgLazySrcSet .= ' data-srcset="';
					$imgLazySrcSet .= $imgSrcSetPath;
					$imgLazySrcSet .= '"'.PHP_EOL;

				}

				$imgtag = '	<img '.(sizeof($classes) > 0 ? 'class="'.implode(' ', $classes).'"' : '').' src="'.$imgSrc.'"'.$imgLazySrc.'srcset="'.$imgSrcSet.'"'.$imgLazySrcSet.' alt="'.addslashes(rex_media::get($filename)->getTitle()).'">'.PHP_EOL;

			}
			
			$str .= $imgtag;
			$str .= '</picture>'.PHP_EOL;
			
			$str  = rex_extension::registerPoint(new rex_extension_point('MMP_AFTER_PICTURETAG', $str, ['mediatype' => $mediatype, 'filename' => $filename, 'filenamesByBreakpoint' => $filenamesByBreakpoint, 'lazyload' => boolval($lazyload)]));
			
			return $str;
		}
	}
?>