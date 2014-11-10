<?php

class DetailsUtility extends Object {

	/**
	 * convertTypes()
	 *
	 * @param $params The params array to be converted
	 * @return The converted array
	 *
	 * Converts values in the params array to boolean or
	 * numeric as appropriate. Strings of 'true', 'yes', 'on'
	 * get converted to boolean true. Strings of 'false', 'no',
	 * 'off' get converted to boolean false.
	 *
	 * Should be unnecessary in Croogo 2.2.0+
	 */
	static public function convertTypes($params = array()) {

		if (empty($params)) {
			return array();
		}

		foreach ($params as $key => $value) {
			if (is_string($value)) {
				$num_check = filter_var($value, FILTER_VALIDATE_INT, array('flags'=>FILTER_FLAG_ALLOW_HEX));
				$bool_check = filter_var($value, FILTER_VALIDATE_BOOLEAN, array('flags'=>FILTER_NULL_ON_FAILURE));
				if ($num_check !== false) {
					$params[$key] = $num_check;
				} else if (!is_null($bool_check)) {
					$params[$key] = $bool_check;
				}
			}
		}

		return $params;

	}

	static public function resetSource($Model, $detailModelName) {
		$tempDs = $Model->getDataSource()->config;
		ConnectionManager::$config->detailstemp = $tempDs;
		clearCache(null, 'models', null);
		$DM = ClassRegistry::init($detailModelName);
		$DM->setDataSource('detailstemp');
	}
}
