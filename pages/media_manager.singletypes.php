<?php
	$func = rex_request('func', 'string');
	
	if ($func == '') {
		$list = rex_list::factory("SELECT `id`, `name`, `description`, `status` FROM `".rex::getTablePrefix()."media_manager_type` WHERE `description` NOT LIKE 'generated%' AND `group` = 0 AND `subgroup` = 0 ORDER BY `status` ASC, `name` ASC");
		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage(rex_i18n::msg('media_manager_type_no_types'));
		
		// icon column
		$thIcon = '<a href="'.str_replace('media_manager/singletypes', 'media_manager/types', $list->getUrl(['func' => 'add'])).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);
		
		$list->setColumnParams('name', ['id' => '###id###', 'func' => 'edit']);
		$list->setColumnLabel('name', rex_i18n::msg('media_manager_type_name'));
		$list->setColumnFormat('name', 'custom', function ($params) {
			$list = $params['list'];
			$name = '<b>' . $list->getValue('name') . '</b>';
			$name .= ($list->getValue('description') != '') ? '<br /><span class="rex-note">' . $list->getValue('description') . '</span>' : '';
			return $name;
		});
		
		//Start - column 'functions'
			// functions column spans 5 data-columns
			$list->addColumn(rex_i18n::msg('media_manager_type_functions'), '', -1, ['<th class="rex-table-action" colspan="5">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
			$list->setColumnParams(rex_i18n::msg('media_manager_type_functions'), ['type_id' => '###id###', 'effects' => 1]);
			$list->setColumnFormat(rex_i18n::msg('media_manager_type_functions'), 'custom', function ($params) {
				$list = $params['list'];
				return str_replace('media_manager/singletypes', 'media_manager/types', $list->getColumnLink(rex_i18n::msg('media_manager_type_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('media_manager_type_effekts_edit')));
			});
		//End - column 'functions'
		
		//Start - column 'deleteCache'
			$list->addColumn('deleteCache', '', -1, ['', '<td class="rex-table-action">###VALUE###</td>']);
			$list->setColumnParams('deleteCache', ['func' => 'delete_cache', 'type_id' => '###id###']);
			$list->addLinkAttribute('deleteCache', 'data-confirm', rex_i18n::msg('media_manager_type_cache_delete') . ' ?');
			$list->setColumnFormat('deleteCache', 'custom', function ($params) {
				$list = $params['list'];
				return str_replace('media_manager/singletypes', 'media_manager/types', $list->getColumnLink('deleteCache', '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('media_manager_type_cache_delete')));
			});
		//End - column 'deleteCache'
		
		//Start - column 'editType'
			$list->addColumn('editType', '', -1, ['', '<td class="rex-table-action">###VALUE###</td>']);
			$list->setColumnParams('editType', ['func' => 'edit', 'type_id' => '###id###']);
			$list->setColumnFormat('editType', 'custom', function ($params) {
				$list = $params['list'];
				return str_replace('media_manager/singletypes', 'media_manager/types', $list->getColumnLink('editType', '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('media_manager_type_edit')));
			});
		//End - column 'editType'
		
		//Start - column 'copyType'
			$list->addColumn('copyType', '', -1, ['', '<td class="rex-table-action">###VALUE###</td>']);
			$list->setColumnParams('copyType', ['func' => 'copy', 'type_id' => '###id###']);
			$list->setColumnFormat('copyType', 'custom', function ($params) {
				$list = $params['list'];
				return str_replace('media_manager/singletypes', 'media_manager/types', $list->getColumnLink('copyType', '<i class="rex-icon rex-icon-duplicate"></i> ' . rex_i18n::msg('media_manager_type_copy')));
			});
		//End - column 'copyType'
		
		//Start - column 'deleteType'
			$list->addColumn('deleteType', '', -1, ['', '<td class="rex-table-action">###VALUE###</td>']);
			$list->setColumnParams('deleteType', ['type_id' => '###id###', 'func' => 'delete']);
			$list->addLinkAttribute('deleteType', 'data-confirm', rex_i18n::msg('delete') . ' ?');
			$list->setColumnFormat('deleteType', 'custom', function ($params) {
				$list = $params['list'];
				if ($list->getValue('status') == 1) {
					// remove delete link on internal types (status == 1)
					return '<small class="text-muted">' . rex_i18n::msg('media_manager_type_system') . '</small>';
				}
				return str_replace('media_manager/singletypes', 'media_manager/types', $list->getColumnLink('deleteType', '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('media_manager_type_delete')));
			});
		//End - column 'deleteType'
		
		$list->removeColumn('id');
		$list->removeColumn('description');
		$list->removeColumn('status');
		
		$content = $list->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('content', $content, false);
		$content = $fragment->parse('core/page/section.php');
		
		echo $content;
	} else if ($func == 'add' || $func == 'edit') {
		$id = rex_request('id', 'int');
		
		if ($func == 'edit') {
			$formLabel = $this->i18n('breakpoints_formcaption_edit');
		} elseif ($func == 'add') {
			$formLabel = $this->i18n('breakpoints_formcaption_add');
		}
		
		$form = rex_form::factory(rex::getTablePrefix().'media_manager_plus_breakpoints', '', 'id='.$id);
		
		//Start - add name-field
			$field = $form->addTextField('name');
			$field->setLabel($this->i18n('breakpoints_label_name'));
		//End - add name-field
		
		//Start - add mediaquery-field
			$field = $form->addTextField('mediaquery');
			$field->setLabel($this->i18n('breakpoints_label_mediaquery'));
		//End - add mediaquery-field
		
		if ($func == 'edit') {
			$form->addParam('id', $id);
		}
		
		$content = $form->get();
		
		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content, false);
		$content = $fragment->parse('core/page/section.php');
		
		echo $content;
	}
?>