<?php

App::uses('DetailsAppController', 'Details.Controller');

/**
 * Details Controller
 *
 * @category Details.Controller
 * @package  Details
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://github.com/miketallroth/croogo-details
 */
class DetailsController extends DetailsAppController {

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

/**
 * Admin delete field
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_delete_field($id = null) {
		$Meta = ClassRegistry::init('Meta.Meta');
		$success = false;
		if ($id != null && $Meta->delete($id)) {
			$success = true;
		} else {
			if (!$Meta->exists($id)) {
				$success = true;
			}
		}

		$success = array('success' => $success);
		$this->set(compact('success'));
		$this->set('_serialize', 'success');
	}

/**
 * Admin add field
 *
 * @return void
 * @access public
 */
	public function admin_add_field() {
		$this->layout = 'ajax';
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

		// check for invalid typeId
		if (empty($typeId)) {
			$this->Session->setFlash(__d('croogo', 'Type not recognized'), 'flash', array('class' => 'error'));
			return $this->redirect($redir);
		}

		// get the type meta data
		list($Detail, $className, $tableName, $detailFields) = $this->_getTypeInfo($typeId);

		// create a map to find the field two above
		$mapKeys = array_keys($detailFields);
		$mapValues = array_keys($detailFields);
		$slice1 = array_slice($mapKeys, 0, 2);
		$slice2 = array_slice($mapKeys, 2);
		$mapKeys = array_merge($slice2, $slice1);
		$map = array_combine($mapKeys, $mapValues);

		// set the redirect array
		$redir = array('plugin' => 'taxonomy', 'controller' => 'types', 'action' => 'edit', $typeId);

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

		// check for invalid typeId
		if (empty($typeId)) {
			$this->Session->setFlash(__d('croogo', 'Type not recognized'), 'flash', array('class' => 'error'));
			return $this->redirect($redir);
		}

		// get the type meta data
		list($Detail, $className, $tableName, $detailFields) = $this->_getTypeInfo($typeId);

		// create a map to find the field one below
		$mapKeys = array_keys($detailFields);
		$mapValues = array_keys($detailFields);
		$slice1 = array_slice($mapKeys, 0, count($mapKeys)-1);
		$slice2 = array_slice($mapKeys, count($mapKeys)-1, 1);
		$mapKeys = array_merge($slice2, $slice1);
		$map = array_combine($mapKeys, $mapValues);

		// set the redirect array
		$redir = array('plugin' => 'taxonomy', 'controller' => 'types', 'action' => 'edit', $typeId);

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

		$this->Session->setFlash(__d('croogo', 'Field moved'), 'flash', array('class' => 'success'));
		$this->redirect($redir);
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
		$className = Inflector::classify($typeDef['Type']['alias']) . 'Detail';
		$tableName = Inflector::tableize($className);

		// find details table meta information
		$Detail = ClassRegistry::init($className);
		$detailFields = $Detail->schema();
		return array($Detail, $className, $tableName, $detailFields);
	}


	/**
	 * _makeSqlDef
	 *
	 * @param array $meta schema def to convert to sql def string
	 * @return string sql def string
	 */
	function _makeSqlDef($meta) {
		switch ($meta['type']) {
		case 'integer':
			$meta['type'] = "integer({$meta['length']})";
			$meta['type'] .= ($meta['unsigned']) ? ' unsigned' : '';
			break;
		case 'datetime':
			break;
		default:
		}

		$def = $meta['type'];
		$def .= ($meta['null']) ? ' null' : ' not null';
		$def .= ' default' . (!is_null($meta['default'])) ? $meta['default'] : ' null';

		return $def;
	}

}
