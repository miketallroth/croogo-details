<?php

App::uses('DetailsUtility', 'Details.Lib');
App::uses('ParamsBehavior', 'Croogo.Model.Behavior');

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
            Configure::write('Cache.disable',true);
            clearCache(null, 'models', null);
	}

	/**
	 * afterSave
	 *
	 * Looks thru params field for 'detail', if true then we need to ensure
	 * associated table exists.
	 */
	public function afterSave(Model $Model, $created, $options = array()) {
            CakeLog::write('debug','in DTB afterSave');
            CakeLog::write('debug',print_r($Model->name,true));

		// simplify model params
		$Model->data['Params'] = $Model->Behaviors->Params->paramsToArray($Model,
			$Model->data['Type']['params']);

		if (isset($Model->data['Params']['detail']) && $Model->data['Params']['detail']) {
			$alias = $Model->data['Type']['alias'];
            CakeLog::write('debug','found the detail ' . $alias);
			$detailModelName = Inflector::classify($alias) . 'Detail';
			$tableName = Inflector::tableize($detailModelName);

			// make sure setting has the Type listed
			$Setting = ClassRegistry::init('Settings.Setting');
			$setting = $Setting->find('first', array(
				'conditions' => array(
					'key' => 'Details.hookTypes',
				),
			));
            if (strlen($setting['Setting']['value']) > 0) {
			    $hookTypes = explode(',',$setting['Setting']['value']);
            } else {
                $hookTypes = array();
            }

            CakeLog::write('debug',print_r($hookTypes,true));
			if (!in_array($alias,$hookTypes)) {
            CakeLog::write('debug','missing from settings ' . $alias);
				$hookTypes[] = $alias;
            CakeLog::write('debug',print_r($hookTypes,true));
				$setting['Setting']['value'] = implode(',',$hookTypes);
				$Setting->save($setting);
			}
			// make sure details table exists
			$dataSource = ConnectionManager::getDataSource('default');
			$dbName = $dataSource->config['database'];
			$tables = $Model->query(
				"select TABLE_NAME ".
				"from INFORMATION_SCHEMA.TABLES ".
				"where TABLE_SCHEMA='{$dbName}'"
			);
            //CakeLog::write('debug',print_r($CMC,true));
            Configure::write('Cache.disable',true);
            clearCache(null, 'models', null);
			$tables = Hash::extract($tables, '{n}.TABLES.TABLE_NAME');
			if (!in_array($tableName, $tables)) {
            CakeLog::write('debug','missing from database ' . $alias);
				$Model->query(
					"create table {$tableName} (".
					"`id` int(10) not null auto_increment, ".
					"`node_id` int(10) default null, ".
					"primary key (`id`))"
				);
            /*
		        $DM = ClassRegistry::init($detailModelName);
                $DM->getDataSource()->cacheSources = false;
		        ClassRegistry::flush();
                Croogo::hookModelProperty('Node', 'hasOne', array($detailModelName => array(
                    'className' => $detailModelName,
                    'foreignKey' => 'node_id',
                    'dependent' => true,
                )));
             */
            $tempDs = $Model->getDataSource()->config;
            CakeLog::write('debug',print_r($tempDs,true));
            $CMC = ConnectionManager::$config;
            ConnectionManager::$config->detailstemp = $tempDs;
            CakeLog::write('debug',print_r($CMC,true));
            //Configure::write('Cache.disable',true);
            //clearCache(null, 'models', null);
	        $DM = ClassRegistry::init($detailModelName);
            $DM->setDataSource('detailstemp');

			}
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
