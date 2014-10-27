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

		// ACL: set ACOs with permissions
		$controller->Croogo->addAco('Appts');
		$controller->Croogo->addAco('Appts/Appts/admin_index');
		$controller->Croogo->addAco('Appts/Appts/index', array('registered', 'public'));
		$controller->Croogo->addAco('Appts/Appts/view', array('registered', 'public'));
		$controller->Croogo->addAco('Appts/Appts/calendar', array('registered', 'public'));

		App::uses('CroogoPlugin', 'Extensions.Lib');
		$CroogoPlugin = new CroogoPlugin();
//		$CroogoPlugin->migrate('Details');

		//Ignore the cache since the tables wont be inside the cache at this point
		//$db->cacheSources = false;

	}

	public function beforeDeactivation(Controller $controller) {
		return true;
	}

	public function onDeactivation(Controller $controller) {
		$controller->Croogo->removeAco('Appts');
	}

 }
