<?php
	class media_manager_plus {
		public static function getBreakpoints() {
			$sql = rex_sql::factory();
			$breakpoints = $sql->getArray("SELECT name, mediaquery FROM `".rex::getTablePrefix()."media_manager_plus_breakpoints` ORDER BY `name` ASC");
			unset($sql);

			array_push($breakpoints, ['name'=>'fallback', 'mediaquery'=>'']);
			$breakpointsCombined = array_combine(array_column($breakpoints, 'name'), array_column($breakpoints, 'mediaquery'));
			
			return $breakpointsCombined;
		}
		
		public static function getResolutions() {
			return [
				'1x' => 1.0,
				'2x' => 2.0,
				'3x' => 3.0,
				'lazy' => 0.5
			];
		}
	}
?>