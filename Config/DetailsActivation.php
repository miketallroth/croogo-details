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

	public function beforeActivation(Controller $controller) {
		return true;
	}

	public function onActivation(Controller $controller) {
		$controller->Setting->create();
		$controller->Setting->save(array(
			'key' =>         'Details.hookTypes',
			'value' =>       '',
			'title' =>       'Hook Content Types',
			'description' => 'Content types activated with details',
			'input_type' =>  'text',
			'editable' =>    0,
			'weight' =>      100,
			'params' =>      '',
		));

		$controller->Setting->create();
		$controller->Setting->save(array(
			'key' =>         'Details.enableDefaultBodyUpdate',
			'value' =>       true,
			'title' =>       'Enable Default Body Update',
			'description' => 'Inserts default formatted detail fields into node body '.
								'(disable when using custom views)',
			'input_type' =>  'checkbox',
			'editable' =>    1,
			'weight' =>      101,
			'params' =>      '',
		));
	}

	public function beforeDeactivation(Controller $controller) {
		return true;
	}

	public function onDeactivation(Controller $controller) {
	}

 }
