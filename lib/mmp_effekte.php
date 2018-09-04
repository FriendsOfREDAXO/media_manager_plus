<?php
/**
 *
 * User: markus
 * Date: 03.09.18
 * Time: 13:07
 */

class mmp_effekte {

    /**
     * Kopiert sämtliche Gruppen-Effekte von ($from) dem Breakpoint, zu ($to) dem Breakpoint
     * @param int $from
     * @param int $to
     */
    public static function duplicate($from, $to) {
        $sql = rex_sql::factory();

        $fromTypes = $sql->getArray("SELECT `id` FROM `".rex::getTablePrefix()."media_manager_type` WHERE `subgroup` = ? or id = ? ORDER BY `id` ASC", [$from, $from]);
        $toTypes = $sql->getArray("SELECT `id` FROM `".rex::getTablePrefix()."media_manager_type` WHERE `subgroup` = ? or id = ? ORDER BY `id` ASC", [$to, $to]);

        if (count($fromTypes) == count($toTypes)) {
            foreach ($fromTypes as $index => $fromType) {
                $effects = self::getEffectsByTypeId($fromType['id']);
                foreach ($effects as $effect) {
                    self::add($toTypes[$index]['id'], $effect['effect'], $effect['parameters'], $effect['priority'], $effect['updateuser'], $effect['createuser']);
                }
            }
        } else {
            die('Fehler: Unterschiedliche anzahl breakpoints');
        }
        unset($sql);
    }

    /**
     * Liefert ein Array von allen Effekten anhand der type_id
     * @param int $id
     * @return mixed
     */
    public static function getEffectsByTypeId($id) {
        return rex_sql::factory()->getArray("SELECT * FROM `".rex::getTablePrefix()."media_manager_type_effect` WHERE `type_id` = ? ORDER BY `priority` ASC", [$id]);
    }

    /**
     * Fügt einen weiteren Effekt zu einem Breakpoint Typ.
     * @param int $type_id
     * @param $effect
     * @param $parameters
     * @param $priority
     * @param $updateuser
     * @param $createuser
     * @return boolean
     */
    public static function add($type_id, $effect, $parameters, $priority, $updateuser, $createuser) {
        $sql = rex_sql::factory();
        $sql->setTable(rex::getTablePrefix().'media_manager_type_effect')
            ->setValue('type_id', $type_id)
            ->setValue('effect', $effect)
            ->setValue('parameters', $parameters)
            ->setValue('priority', $priority)
            ->setValue('updatedate', date('Y-m-d H:i:s', time()))
            ->setValue('updateuser', $updateuser)
            ->setValue('createdate', date('Y-m-d H:i:s', time()))
            ->setValue('createuser', $createuser);

        if($sql->insert())
            return true;

        return false;
    }

    /**
     * löscht eine Gruppe anhand
     * @param int $group_id
     */
    public static function deleteByGroupId($group_id) {
        $sql = rex_sql::factory();

        $types = $sql->getArray("SELECT `id` FROM `".rex::getTablePrefix()."media_manager_type` WHERE `group` = ?", [$group_id]);

        foreach ($types as $type) {
            $sql->setQuery('DELETE FROM `'.rex::getTablePrefix().'media_manager_type_effect` WHERE `type_id` = ?', [$type['id']]);
        }

        $sql->setQuery('DELETE FROM `'.rex::getTablePrefix().'media_manager_type` WHERE `id` = ? OR `group` = ?', [$group_id, $group_id]);

        unset($sql);
    }
}

?>