<?php
	class media_manager_plus {
        const LAZY_LOAD = 'lazysizes.min-5.3.2.js';
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

		public static function getTypeName($id) {
            $sql = rex_sql::factory();

            $typeName = $sql->getArray('SELECT `name` FROM `'.rex::getTablePrefix().'media_manager_type` WHERE `id` = ?', [$id]);

            if(empty($typeName))
                return null;

            return $typeName[0]['name'];
        }

        public static function getResolutionTypeId($name, $resolution) {
            $sql = rex_sql::factory();

            $resolutionType = $sql->getArray('SELECT `id` FROM `'.rex::getTablePrefix().'media_manager_type` WHERE `name` = ?', [$name.'@'.$resolution]);
            if (empty($resolutionType)) {
                return null;
            }
            return $resolutionType[0]['id'];
        }

        public static function deleteAllEffectTypesByResolution($resolutionTypeId) {
            $sql = rex_sql::factory();

            $sql->setQuery('DELETE FROM `'.rex::getTablePrefix().'media_manager_type_effect` WHERE `type_id` = ?', [$resolutionTypeId]);
        }

        public static function  getAllEffects($typeId) {
		    $sql = rex_sql::factory();

            return $sql->getArray('SELECT * FROM `'.rex::getTablePrefix().'media_manager_type_effect` WHERE `type_id` = ? ORDER BY `priority` ASC', [$typeId]);
        }

        public static function insertTypeEffect($resolutionTypeId, $effect, $parameters, $prio) {
		    $sql = rex_sql::factory();

            $sql->setQuery('INSERT INTO `'.rex::getTablePrefix().'media_manager_type_effect` 
                                    (`type_id`, `effect`, `parameters`, `priority`, `updatedate`, `updateuser`, `createdate`, `createuser`) 
                                    VALUES (?,?,?,?,NOW(),"media_manager_plus",NOW(),"media_manager_plus")', [$resolutionTypeId, $effect, json_encode($parameters), $prio]);
        }

        public static function prepareEffectParameters($effect, $factor) {
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
            return $parameters;
        }

        public static function getLazyLoad() {
            return '<script src="assets/addons/media_manager_plus/vendor/js/'.self::LAZY_LOAD.'" defer type="text/javascript"></script>';
        }
	}
?>