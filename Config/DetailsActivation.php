<?php
/**
 * Details Activation
 *
 * Activation class for Details plugin.
 *
 * @package  Details
 * @author   Mike Tallroth <mike.tallroth@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://github.com/miketallroth/croogo-details
 */
class DetailsActivation {

	const TABLES_CONFIG_OPTION = 'Details.hookTypes';
	const TABLES_TITLE = 'Details Content Types';
	const TABLES_DESC = 'Details content types to be hooked';

	public function beforeActivation(Controller $controller) {
		return true;
	}

	public function onActivation(Controller $controller) {
		$controller->Setting->create();
		$controller->Setting->save(array(
			'key' => self::TABLES_CONFIG_OPTION,
			'value' => '',
			'title' => self::TABLES_TITLE,
			'description' => self::TABLES_DESC,
			'input_type' => 'text',
			'editable' => 1,
			'weight' => 100,
			'params' => '',
		));
	}

	public function beforeDeactivation(Controller $controller) {
		return true;
	}

	public function onDeactivation(Controller $controller) {
	}

 }
