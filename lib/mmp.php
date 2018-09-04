<?php
/**
 * Created by PhpStorm.
 * User: markus
 * Date: 03.09.18
 * Time: 16:16
 */

class mmp {


    /**
     * Gibt das übergebene Bild im picture tag aus, mit den jeweiligen ratio format (1, 2 & 3)
     * @param $datei
     * @param $gruppen_typ
     * @param $alt
     * @param string $class
     * @param string $params
     * @return string
     */
    public static function image($datei, $gruppen_typ, $alt, $class = '', $params = '') {
        $src = '<picture>'.PHP_EOL;
        $src .= self::imageSrcSet($datei, $gruppen_typ);
        $src .= '<img data-src="'.self::rewrite($datei, $gruppen_typ.'-org').'" alt="'.$alt.'" class="'.$class.'"  src="'.self::rewrite($datei, $gruppen_typ.'-org').'" '.$params.'>'.PHP_EOL;
        $src .= '</picture>';
        return $src;
    }

    /**
     * Gibt einen source tag mit den jeweiligen Auflösungen für pixel ratio 1-fach, 2-fach und 3-fach zurück
     * @param $filename
     * @param $type
     * @return string
     */
    public static function imageSrcSet($filename, $type) {
        if($type == null || '') $type = 'default';

        $m = rex_media::get($filename);
        $sql = rex_sql::factory();
        $sql->setQuery("Select * from ".rex::getTablePrefix()."media_manager_plus_breakpoints");

        $sizes = [];
        for($i=0; $i < $sql->getRow(); $i++) {
            if($sql->getValue('name') != 'ico')
                $sizes[$sql->getValue('name')] = $sql->getValue('mediaquery');
            $sql->next();
        }

        if(!is_object($m))
            return '';

        // TODO bereitstellung der Bilder in verschiedenen Formaten, könnte hier übergeben werden um auch z.B. webp Format mit auszugeben.
        $filetypes = [$m->getExtension() => $m->getType()];

        $srcset = '';

        if($m->getExtension() == 'png' || $m->getExtension() == 'jpg') {
            unset($filetypes['jp2']);
        }
        foreach($sizes as $size_name => $size) {
            foreach($filetypes as $ft => $ft_type) {
                $x1 = self::base_url().self::rewrite($filename, $type.'-'.$size_name.($m->getExtension() == $ft ? '' : '_'.$ft).'@1x');
                $x2 = self::base_url().self::rewrite($filename, $type.'-'.$size_name.($m->getExtension() == $ft ? '' : '_'.$ft).'@2x').' 2x';
                $x3 = self::base_url().self::rewrite($filename, $type.'-'.$size_name.($m->getExtension() == $ft ? '' : '_'.$ft).'@3x').' 3x';
                $srcset .= '<source data-srcset="'.$x1.', '.$x2.', '.$x3.'" media="'.$size.'" type="'.$ft_type.'">'.PHP_EOL;
            }
        }

        return $srcset;
    }

    public static function base_url($withoutfolder = false) {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != '' ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].($withoutfolder == false ? rex_url::frontend() : '');
    }

    public function rewrite($filename, $type) {
        if(class_exists('mm_autorewrite')) {
            return mm_autorewrite::rewrite($filename, $type);
        } else {
            return 'index.php?rex_media_type='.$type.'&rex_media_file='.$filename;
        }
    }
}