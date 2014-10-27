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

}
