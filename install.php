<?php
	rex_sql_table::get(rex::getTable('media_manager_type'))
	->ensureColumn(new rex_sql_column('group', 'int(10)'))
	->ensureColumn(new rex_sql_column('subgroup', 'int(10)'))
	->alter();
