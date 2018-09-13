<?php
/**
 * Erzeugt eine picture Tag ausgabe mit den jeweiligen media breakpoints sowie die ausgaben für die jeweiligen
 * Display ratios.
 *
 * @param $mediatype
 * @param $filename
 * @param $filenamesByBreakPoint
 * @param $lazyload
 * @return bool|string
 */
function mmp_generatePictureTag($mediatype, $filename, $filenamesByBreakPoint = [], $lazyload = true) {
    return mmp::generatePictureTag($mediatype, $filename, $filenamesByBreakPoint, $lazyload);
}

