<?php

App::uses('DetailsUtility', 'Details.Lib');

/**
 * DetailType Behavior
 *
 * PHP version 5
 *
 * @category Behavior
 * @package  Details
 * @version  1.0
 * @author   Mike Tallroth <mike.tallroth@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://github.com/miketallroth/croogo-details
 */
class DetailTypeBehavior extends ModelBehavior {

	/**
	 * Setup
	 *
	 * @param object $model
	 * @param array  $config
	 * @return void
	 */
	public function setup(Model $model, $config = Array()) {
		if (is_string($config)) {
				$config = array($config);
		}
		$this->settings[$model->alias] = $config;
	}

	/**
	 * afterSave
	 *
	 * Looks thru params field for 'detail', if true then we ensure
	 * associated table exists.
	 */
	public function afterSave(Model $Model, $created, $options = array()) {
		CakeLog::write('debug',print_r($options,true));
		CakeLog::write('debug',print_r($Model->data,true));
		$Model->data['Type']['params'] = DetailsUtility::convertTypes($Model->data['Type']['params']);
		if ($Model->data['Type']['params']['detail']) {
		} else {
		}

	}

	/**
	 * _getDetailModelName
	 *
	 * //Look in the params of the node type to find the model property.
	 * //If nothing, just assume and use Inflector.
	 * For now, just use Inflector.
	 *
	 * @param $type The Node type
	 * @param @inclPlugin Include the plugin name, if provided.
	 * @return The name of the associated detail model
	 */
	protected function _getDetailModelName($targetType, $inclPlugin = false) {
		$type = ClassRegistry::init('Taxonomy.Type')->find('first', array(
			'conditions' => array(
				'alias' => $targetType,
			),
		));

		$alias = null;
		$type['Params'] = DetailsUtility::convertTypes($type['Params']);
		$p = (isset($type['Params']['detail'])) ? $type['Params']['detail'] : false;
		if ($p) {
			$alias = Inflector::classify($targetType) . 'Detail';
		}
		return $alias;
	}

}
