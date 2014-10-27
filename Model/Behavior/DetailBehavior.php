<?php
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
			$dmName = $this->_getDetailAlias($type);
			if (array_key_exists('contain',$query)) {
				$query['contain'] = Hash::merge(array($dmName),$query['contain']);
			} else {
				$query['contain'] = array($dmName);
			}
		}
		//print_r($query);
		return $query;
	}

	/**
	 * _getDetailAlias
	 *
	 * Look in the params of the node type to find the model property.
	 * If nothing, just assume and use Inflector.
	 *
	 * @param $type The Node type
	 * @param @inclPlugin Include the plugin name, if provided.
	 * @return The name of the associated detail model
	 */
	protected function _getDetailAlias($targetType, $inclPlugin = false) {
		$types = ClassRegistry::init('Taxonomy.Type')->find('all', array(
			'cache' => array(
				'name' => 'types',
				'config' => 'croogo_types',
			),
		));
		$alias = null;
		foreach ($types as $type) {
			$p = $type['Params'];
			if (isset($p['detail']) && $p['detail']) {
				if (isset($p['model']) && $p['model']) {
					$alias = $p['model'];
				}
				//print_r($types);
				if (!$inclPlugin) {
					$ex = explode('.',$alias);
					$alias = (count($ex) > 1) ? $alias = $ex[1] : $alias = $ex[0];
				}
				//print_r($alias);
				break;
			}
		}
		return $alias;
	}

}
