<?php

class mmp_grouptypes {


    public static function add(rex_form $form, int $subgroup_id) {
        $breakpoints = media_manager_plus::getBreakpoints();
        $resolutions = media_manager_plus::getResolutions();
        foreach ($breakpoints as $breakpoint => $mediaquery) {
            $sql->setQuery('INSERT INTO '.$form->getTableName().' (`status`, `name`, `description`, `group`, `subgroup`) VALUES (?,?,?,?,?)', ['0', $_POST[$form->getName()]['name'].'-'.$breakpoint, 'generated [group '.$group_id.']', $group_id, 0]);
            echo $sql->getError();
            $subgroup_id = $params['sql']->getLastId();
            foreach ($resolutions as $resolution => $factor) {
                $sql->setQuery('INSERT INTO '.$form->getTableName().' (`status`, `name`, `description`, `group`, `subgroup`) VALUES (?,?,?,?,?)', ['0', $_POST[$form->getName()]['name'].'-'.$breakpoint.'@'.$resolution, 'generated [group '.$group_id.', subgroup '.$subgroup_id.']', $group_id, $subgroup_id]);
                echo $sql->getError();
            }
        }
    }
}