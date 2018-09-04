<?php
	$func = rex_request('func', 'string');
	
	$success = '';
	$error = '';
	
	switch ($func) {
		case 'delete':
            $group_id = rex_request('group_id', 'int');
			if ($group_id > 0) {
                mmp_effekte::deleteByGroupId($group_id);
			}
			
			$func = '';
		break;
		case 'copyEffects':
			$from = rex_request('from', 'int');
			$to = rex_request('to', 'int');

			mmp_effekte::duplicate($from, $to);
			
			$func = '';
		break;
	}
	
	if ($success != '') {
		echo rex_view::success($success);
	}
	
	if ($error != '') {
		echo rex_view::error($error);
	}

	if ($func == '') {
	    echo mmp_groups::getList();
	} else if ($func == 'add' || $func == 'edit') {
	    echo mmp_groups::getEdit($func);
	}
