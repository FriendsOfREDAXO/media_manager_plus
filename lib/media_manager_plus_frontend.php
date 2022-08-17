<?php
	class media_manager_plus_frontend extends media_manager_plus {
		public static function generateBackgroundImage($selector, $mediatype, $filename, $filenamesByBreakpoint = []) {
            $filenames = self::getFilenamesByBreakpoints($filename, $filenamesByBreakpoint);
			
			$str = '';
			foreach (self::getBreakpoints() as $breakpoint => $mediaquery) {
				foreach (self::getResolutions() as $resolution => $factor) {
					if ($resolution == 'lazy') continue;
					
					$str .= '@media only screen and '.$mediaquery.' and (-webkit-min-device-pixel-ratio: '. (int)$factor .'), only screen and (min--moz-device-pixel-ratio: '. (int)$factor .'), only screen and '.$mediaquery.' and (-o-min-device-pixel-ratio: '. (int)$factor .'/1), only screen and '.$mediaquery.' and (min-device-pixel-ratio: '. (int)$factor .'), only screen and '.$mediaquery.' and (min-resolution: '. (int)($factor * 96) .'dpi), only screen and '.$mediaquery.' and (min-resolution: '. (int)$factor .'dppx) {'.PHP_EOL;
					$str .= '  '.$selector.' {'.PHP_EOL;
					$str .= '    background-image:url('.self::getPictureUrl($filenames[$breakpoint], $mediatype, $breakpoint.'@'.$resolution).');'.PHP_EOL;
					$str .= '  }'.PHP_EOL;
					$str .= '}'.PHP_EOL;
				}
			}
			
			return $str;
		}

		private static function getSourceTags($filenames, $mediatype, $lazyload) {
			$str = '';
			foreach (self::getBreakpoints() as $breakpoint => $mediaquery) {
				if ($breakpoint == 'fallback') continue;

				$str .= '<source media="'.$mediaquery.'" ';

				if($lazyload) {
					$str .= 'srcset="';
					$str .= self::getPictureUrl($filenames[$breakpoint], $mediatype, $breakpoint . '@lazy');
					$str .= '"';
				}

				//Start - generate data-srcset
				$str .= ' data-srcset="';
				foreach (self::getResolutions() as $resolution => $factor) {
					if ($resolution == 'lazy') {
						continue;
					}

					$str .= self::getPictureUrl($filenames[$breakpoint], $mediatype, $breakpoint.'@'.$resolution).' '.$resolution.',';
				}
				$str = substr($str, 0, -1);
				//End - generate data-srcset

				$str .= '">'.PHP_EOL;
			}
			return $str;
		}

		private static function getImgTag($filename, $mediatype, $lazyload, $classes) {
			$imgSrcPath = self::getPictureUrl($filename, $mediatype, 'fallback@1x');

			$imgSrcSetPath = '';
			foreach (self::getResolutions() as $resolution => $factor) {
				if ($resolution == 'lazy') continue;
				$imgSrcSetPath .= self::getPictureUrl($filename, $mediatype, 'fallback@'.$resolution).' '.$resolution.',';
			}
			$imgSrcSetPath = substr($imgSrcSetPath, 0, -1);

			if (!$lazyload) {
				$imgSrc = $imgSrcPath;
				$imgSrcSet = $imgSrcSetPath;

				$imgLazySrc = '';
				$imgLazySrcSet = '';
			} else {
				$imgSrcLazy = self::getPictureUrl($filename, $mediatype, 'fallback@lazy');

				$imgSrc = $imgSrcLazy;
				$imgLazySrc = ' data-src="'.$imgSrcPath.'"';

				$imgSrcSet = $imgSrcLazy;

				$imgLazySrcSet = ' data-srcset="';
				$imgLazySrcSet .= $imgSrcSetPath;
				$imgLazySrcSet .= '"'.PHP_EOL;
			}

			return '<img '.(count($classes) > 0 ? 'class="'.implode(' ', $classes).'"' : '').' src="'.$imgSrc.'"'.$imgLazySrc.' srcset="'.$imgSrcSet.'"'.$imgLazySrcSet.' alt="'.self::getAltString($filename).'">'.PHP_EOL;

		}
		
		public static function generatePictureTag($mediatype, $filename, $filenamesByBreakpoint = [], $lazyload = true) {
		    $filenames = self::getFilenamesByBreakpoints($filename, $filenamesByBreakpoint);
			
			$str = '';
			$str  = rex_extension::registerPoint(new rex_extension_point('MMP_BEFORE_PICTURETAG', $str, ['mediatype' => $mediatype, 'filename' => $filename, 'filenamesByBreakpoint' => $filenamesByBreakpoint, 'lazyload' => (bool)$lazyload]));
			$str .= '<picture>'.PHP_EOL;

			$str .= self::getSourceTags($filenames, $mediatype, $lazyload);

			$classes = [];
			$imgtag = '';
			
			if ($lazyload) $classes[] = 'lazyload';

			$classes = rex_extension::registerPoint(new rex_extension_point('MMP_IMG_CLASS', $classes, ['mediatype' => $mediatype, 'filename' => $filename, 'filenamesByBreakpoint' => $filenamesByBreakpoint, 'lazyload' => (bool)$lazyload]));
			if(!is_array($classes))
                throw new rex_exception('only array allowed at MMP_IMG_CLASS return value');

			$imgtag = rex_extension::registerPoint(new rex_extension_point('MMP_IMGTAG', $imgtag, ['mediatype' => $mediatype, 'filename' => $filename, 'filenamesByBreakpoint' => $filenamesByBreakpoint, 'lazyload' => (bool)$lazyload, 'classes' => $classes]));

			if ($imgtag == '')
				$imgtag = self::getImgTag($filename, $mediatype, $lazyload, $classes);
			
			$str .= $imgtag;
			$str .= '</picture>'.PHP_EOL;
			
			$str  = rex_extension::registerPoint(new rex_extension_point('MMP_AFTER_PICTURETAG', $str, ['mediatype' => $mediatype, 'filename' => $filename, 'filenamesByBreakpoint' => $filenamesByBreakpoint, 'lazyload' => boolval($lazyload)]));
			
			return $str;
		}

		private static function getPictureUrl($filename, $mediatype, $group) {
            if(rex_addon::get('media_manager_autorewrite')->isAvailable()) {
                if(version_compare(rex::getVersion(), '5.7.0', '>=')) {
                    $imgSrcPath = rex_media_manager::getUrl($mediatype.'-'.$group, $filename);
                } else {
                    $imgSrcPath = mm_auto::rewrite($mediatype.'-'.$group, $filename);
                }
            } else {
                $imgSrcPath = 'index.php?rex_media_type=' . $mediatype . '-' . $group . '&rex_media_file=' . $filename;
            }
            return $imgSrcPath;
        }

		private static function getAltString($filename = '') {
            $alt = '';
            $alt = rex_extension::registerPoint(new rex_extension_point('MMP_IMG_ALT', $alt, ['filename' => $filename]));

            if($alt !== '')
                return $alt;

		    if(!is_object(rex_media::get($filename)))
		        return '';

            return addslashes(rex_media::get($filename)->getTitle());
        }

        private static function getFilenamesByBreakpoints($filename, $filenamesByBreakpoint = []) {
            $filenames = [];
            foreach (self::getBreakpoints() as $breakpoint => $mediaquery) {
                if (isset($filenamesByBreakpoint[$breakpoint])) {
                    $filenames[$breakpoint] = $filenamesByBreakpoint[$breakpoint];
                } else {
                    $filenames[$breakpoint] = $filename;
                }
            }
            return $filenames;
        }
	}
?>
