<?php
class media_manager_plus_backend extends media_manager_plus {
    public static function generateEffectForGroup($group_id) {
        // get current type
        $currentType = media_manager_plus::getTypeName($group_id);

        if (empty($currentType))
            return;

        // add effects to each type of this subgroup
        $resolutions = self::getResolutions();
        foreach ($resolutions as $resolution => $factor) {
            //Start - get type with this resolution
            $resolutionType = self::getResolutionTypeId($currentType, $resolution);
            if (empty($resolutionType))
                continue;

            self::deleteAllEffectTypesByResolution($resolutionType);

            //Start - get all effects for this type's parent
            $effects = self::getAllEffects($group_id);

            if (empty($effects))
                continue;

            foreach ($effects as $effect) {
                $parameters = self::prepareEffectParameters($effect, $factor);

                // save effect to this type
                self::insertTypeEffect($resolutionType, $effect['effect'], $parameters, $effect['priority']);
            }
        }

        // delete cache
        rex_media_manager::deleteCache();
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