<?php
class media_manager_plus_backend extends media_manager_plus {
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
                                    if ((strpos($key, 'height') !== false || strpos($key, 'width') !== false) && $value !== '') {
                                        $after = '';
                                        if(!is_numeric($value) && strpos($value, 'px')) {
                                            $value = (int)$value;
                                            $after = 'px';
                                        }
                                        $parameters['rex_effect_'.$effect['effect']][$key] = round($value * $factor, 0).$after;
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
}
?>