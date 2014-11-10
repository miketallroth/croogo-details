<?php

App::uses('DetailsUtility', 'Details.Lib');

/**
 * Detail Behavior
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
class DetailBehavior extends ModelBehavior {

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
	 * beforeFind
	 *
	 * @param $model the model this behavior is attached to
	 * @param $query the query data
	 * @return the modified query data
	 */
	public function beforeFind(Model $model, $query) {
		$type = null;
		if ($model->type != null) {
			$type = $model->type;
		} else if (array_key_exists('Node.type',$query['conditions'])) {
			$type = $query['conditions']['Node.type'];
		}
		// TODO
		// get detail model name from the type's param info
		if ($type != null) {
			$dmName = $this->_getDetailModelName($type);
			if ($dmName) {
				if (array_key_exists('contain',$query)) {
					$query['contain'] = Hash::merge(array($dmName),$query['contain']);
				} else {
					$query['contain'] = array($dmName);
				}
			}
		}
		return $query;
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

		if (empty($type)) {
			return null;
		}

		$alias = null;
		$type['Params'] = DetailsUtility::convertTypes($type['Params']);
		$p = (isset($type['Params']['detail'])) ? $type['Params']['detail'] : false;
		if ($p) {
			$alias = Inflector::classify($targetType) . 'Detail';
		}
		return $alias;
	}

}
