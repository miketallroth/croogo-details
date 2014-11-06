<?php

App::uses('DetailsAppController', 'Details.Controller');
App::uses('DetailsUtility', 'Details.Lib');

/**
 * Details Controller
 *
 * @category Details.Controller
 * @package  Details
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://github.com/miketallroth/croogo-details
 */
class DetailsController extends DetailsAppController {

	public $uses = array(
		'Taxonomy.Type',
	);

/**
 * Components
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Search.Prg' => array(
			'presetForm' => array(
				'paramType' => 'querystring',
			),
			'commonProcess' => array(
				'paramType' => 'querystring',
				'filterEmpty' => true,
			),
		),
	);

/**
 * Preset Variable Search
 *
 * @var array
 * @access public
 */
	public $presetVars = true;



	public function beforeFilter() {
		Configure::write('Cache.disable', true);
		clearCache(null, 'models', null);
		parent::beforeFilter();
	}

/**
 * Admin add field
 *
 * @return void
 * @access public
 */
	public function admin_add($typeId = null) {
		$this->set('title_for_layout', __d('croogo', 'Add Field'));

		// set the redirect array
		$redir = array('plugin' => 'taxonomy', 'controller' => 'types', 'action' => 'edit', $typeId);

		// check for invalid typeId
		if (empty($typeId)) {
			$this->Session->setFlash(__d('croogo', 'Type not recognized'), 'flash', array('class' => 'error'));
			return $this->redirect($redir);
		}

		// get the type meta data
		list($Detail, $typeName, $className, $tableName, $detailFields) = $this->_getTypeInfo($typeId);

		if (!empty($this->request->data)) {

			$colName = Inflector::slug(strtolower($this->request->data['Type']['name']));
			$colDef = $this->_makeSqlDef($this->request->data['Type']);
			$Detail->query("ALTER TABLE {$tableName} ADD {$colName} {$colDef}");

			DetailsUtility::resetSource($Detail, $className);

			$this->Session->setFlash(__d('croogo', 'The Detail Field has been added'), 'flash', array('class' => 'success'));
			$this->redirect($redir);
		}

		$this->set('typeName', $typeName);
		$this->set('typeId', $typeId);
	}

/**
 * Admin edit field
 *
 * @return void
 * @access public
 */
	public function admin_edit($typeId = null, $name = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Field'));

		// set the redirect array
		$redir = array('plugin' => 'taxonomy', 'controller' => 'types', 'action' => 'edit', $typeId);

		// check for invalid typeId
		if (empty($typeId) || empty($name)) {
			$this->Session->setFlash(__d('croogo', 'Type or field not recognized'), 'flash', array('class' => 'error'));
			return $this->redirect($redir);
		}

		// get the type meta data
		list($Detail, $typeName, $className, $tableName, $detailFields) = $this->_getTypeInfo($typeId);

		if (!empty($this->request->data)) {
			$old_name = $name;
			$new_name = Inflector::slug(strtolower($this->request->data['Type']['name']));
			$colDef = $this->_makeSqlDef($this->request->data['Type']);

			$query = "ALTER TABLE {$tableName} CHANGE {$old_name} {$new_name} {$colDef}";
			$Detail->query("ALTER TABLE {$tableName} CHANGE {$old_name} {$new_name} {$colDef}");

			DetailsUtility::resetSource($Detail, $className);

			$this->Session->setFlash(__d('croogo', 'The Detail Field has been changed'), 'flash', array('class' => 'success'));
			$this->redirect(array('plugin'=>'taxonomy', 'controller'=>'types', 'action' => 'edit', $typeId));
		} else {
			$detailFields[$name]['name'] = $name;
			$this->request->data['Type'] = $detailFields[$name];
		}

		$this->set('typeName', $typeName);
		$this->set('typeId', $typeId);
	}

/**
 * Admin moveup
 *
 * @param string $type content type being modified
 * @param string $name name of detail field
 * @return void
 * @access public
 */
	public function admin_moveup($typeId = null, $name = null) {

		// set the redirect array
		$redir = array('plugin' => 'taxonomy', 'controller' => 'types', 'action' => 'edit', $typeId);

		// check for invalid typeId
		if (empty($typeId)) {
			$this->Session->setFlash(__d('croogo', 'Type not recognized'), 'flash', array('class' => 'error'));
			return $this->redirect($redir);
		}

		// get the type meta data
		list($Detail, $typeName, $className, $tableName, $detailFields) = $this->_getTypeInfo($typeId);

		// create a map to find the field two above
		$mapKeys = array_keys($detailFields);
		$mapValues = array_keys($detailFields);
		$slice1 = array_slice($mapKeys, 0, 2);
		$slice2 = array_slice($mapKeys, 2);
		$mapKeys = array_merge($slice2, $slice1);
		$map = array_combine($mapKeys, $mapValues);

		// check for invalid field name
		if (empty($name) || !isset($map[$name])) {
			$this->Session->setFlash(__d('croogo', 'Field not recognized'), 'flash', array('class' => 'error'));
			return $this->redirect($redir);
		}

		// if its 'id', then we are at the top, report the error and return
		if ($map[$name] == 'id') {
			$this->Session->setFlash(__d('croogo', 'Field is already at the top'), 'flash', array('class' => 'error'));
			return $this->redirect($redir);
		}

		// else construct the alter query to move this 'after' the field two in front
		// ALTER TABLE <$tableName> CHANGE <$fieldName> <$fieldName> <$colDef> AFTER <$twoBefore>
		$colDef = $this->_makeSqlDef($detailFields[$name]);
		$Detail->query("
			ALTER TABLE {$tableName}
			CHANGE {$name} {$name} {$colDef}
			AFTER {$map[$name]}
		");

		DetailsUtility::resetSource($Detail, $className);

		$this->Session->setFlash(__d('croogo', 'Field moved'), 'flash', array('class' => 'success'));
		$this->redirect($redir);
	}

/**
 * Admin movedown
 *
 * @param string $type content type being modified
 * @param string $name name of detail field
 * @return void
 * @access public
 */
	public function admin_movedown($typeId = null, $name = null) {

		// set the redirect array
		$redir = array('plugin' => 'taxonomy', 'controller' => 'types', 'action' => 'edit', $typeId);

		// check for invalid typeId
		if (empty($typeId)) {
			$this->Session->setFlash(__d('croogo', 'Type not recognized'), 'flash', array('class' => 'error'));
			return $this->redirect($redir);
		}

		// get the type meta data
		list($Detail, $typeName, $className, $tableName, $detailFields) = $this->_getTypeInfo($typeId);

		// create a map to find the field one below
		$mapKeys = array_keys($detailFields);
		$mapValues = array_keys($detailFields);
		$slice1 = array_slice($mapKeys, 0, count($mapKeys)-1);
		$slice2 = array_slice($mapKeys, count($mapKeys)-1, 1);
		$mapKeys = array_merge($slice2, $slice1);
		$map = array_combine($mapKeys, $mapValues);

		// check for invalid field name
		if (empty($name) || !isset($map[$name])) {
			$this->Session->setFlash(__d('croogo', 'Field not recognized'), 'flash', array('class' => 'error'));
			return $this->redirect($redir);
		}

		// if its 'id', then we are at the bottom, report the error and return
		if ($map[$name] == 'id') {
			$this->Session->setFlash(__d('croogo', 'Field is already at the bottom'), 'flash', array('class' => 'error'));
			return $this->redirect($redir);
		}

		// else construct the alter query to move this 'after' the field two in front
		// ALTER TABLE <$tableName> CHANGE <$fieldName> <$fieldName> <$colDef> AFTER <$twoBefore>
		$colDef = $this->_makeSqlDef($detailFields[$name]);
		$Detail->query("
			ALTER TABLE {$tableName}
			CHANGE {$name} {$name} {$colDef}
			AFTER {$map[$name]}
		");

		DetailsUtility::resetSource($Detail, $className);

		$this->Session->setFlash(__d('croogo', 'Field moved'), 'flash', array('class' => 'success'));
		$this->redirect($redir);
	}

/**
 * Admin delete_field
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_delete_field($typeId = null, $name = null) {

		// set the redirect array
		$redir = array('plugin' => 'taxonomy', 'controller' => 'types', 'action' => 'edit', $typeId);

		// check for invalid typeId
		if (empty($typeId)) {
			$this->Session->setFlash(__d('croogo', 'Type not recognized'), 'flash', array('class' => 'error'));
			return $this->redirect($redir);
		}

		// get the type meta data
		list($Detail, $typeName, $className, $tableName, $detailFields) = $this->_getTypeInfo($typeId);

		// check for invalid field name
		if (empty($name) || !isset($detailFields[$name])) {
			$this->Session->setFlash(__d('croogo', 'Field not recognized'), 'flash', array('class' => 'error'));
			return $this->redirect($redir);
		}

		$Detail->query("ALTER TABLE {$tableName} DROP {$name}");

		DetailsUtility::resetSource($Detail, $className);

		$this->Session->setFlash(__d('croogo', 'Field deleted'), 'flash', array('class' => 'success'));
		return $this->redirect($redir);
	}


	/**
	 * _getTypeInfo
	 *
	 * @param integer $typeId the id of the content type
	 * @return array the parsed meta data of the content type
	 */
	function _getTypeInfo($typeId) {
		$typeDef = ClassRegistry::init('Taxonomy.Type')->find('first', array(
			'conditions' => array(
				'Type.id' => $typeId,
			),
		));
		$typeName = Inflector::classify($typeDef['Type']['alias']);
		$className = $typeName . 'Detail';
		$tableName = Inflector::tableize($className);

		// find details table meta information
		$Detail = ClassRegistry::init($className);
		$detailFields = $Detail->schema();
		return array($Detail, $typeName, $className, $tableName, $detailFields);
	}


	/**
	 * _makeSqlDef
	 *
	 * @param array $meta schema def to convert to sql def string
	 * @return string sql def string
	 */
	function _makeSqlDef($meta) {
		$type = $default = '';
		switch ($meta['type']) {
		case 'integer':
			$length = empty($meta['length']) ? 11 : $meta['length'];
			$type = "integer({$length})";
			$type .= ($meta['unsigned']) ? ' unsigned' : '';
			$default = empty($meta['default']) ? 0 : $meta['default'];
			break;
		case 'string':
			$length = empty($meta['length']) ? 255 : $meta['length'];
			$type = "varchar({$length})";
			$default = empty($meta['default']) ? "''" : "'{$meta['default']}'";
			break;
		case 'datetime':
			$type = 'datetime';
			$default = empty($meta['default']) ? 0 : "'{$meta['default']}'";
			break;
		default:
			$type = $meta['type'];
			$default = empty($default) ? '' : $default;
		}

		$null = ($meta['null']) ? 'null' : 'not null';
		$default = empty($default) ? '' : "default {$default}";

		$def = "{$type} {$null} {$default}";

		return $def;
	}

}
