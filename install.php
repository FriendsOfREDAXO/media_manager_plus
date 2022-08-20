<?php
$addon = rex_addon::get('media_manager_plus');


	rex_sql_table::get(rex::getTable('media_manager_type'))
	->ensureColumn(new rex_sql_column('group', 'int(10)'))
	->ensureColumn(new rex_sql_column('subgroup', 'int(10)'))
    ->ensureColumn(new rex_sql_column('breakpointId', 'int(10)'))
	->alter();


    rex_sql_table::get(rex::getTable('media_manager_plus_breakpoints'))
        ->ensureColumn(new rex_sql_column('prio', 'int(10)'))
        ->ensureColumn(new rex_sql_column('createdate', 'datetime'), 'prio')
        ->ensureColumn(new rex_sql_column('createuser', 'varchar(255)'), 'createdate')
        ->ensureColumn(new rex_sql_column('updatedate', 'datetime'), 'createuser')
        ->ensureColumn(new rex_sql_column('updateuser', 'varchar(255)'), 'updatedate')
        ->alter();

    $s = rex_sql::factory();
    $breakpoints = $s->getArray('Select * from '.rex::getTablePrefix().'media_manager_plus_breakpoints order by id');
    $i = 1;
    foreach($breakpoints as $bp) {
        if($bp['prio'] === 0) {
            $s->setQuery("Update " . rex::getTablePrefix() . "media_manager_plus_breakpoints set prio=" . $i . " where id=".$bp['id']);
            $i++;
        }
    }