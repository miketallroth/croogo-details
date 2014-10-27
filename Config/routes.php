<?php
	// TODO find type info from params field


	$route = 'appointments';
	$plugin = strtolower('Nodes');
	$controller = strtolower('Nodes');

	CroogoRouter::connect("/{$route}", array('plugin' => $plugin, 'controller' => $controller, 'action' => 'index', 'type' => 'appointment'));


/*
	$types = ClassRegistry::init('Taxonomy.Type')->find('all', array(
		'cache' => array(
			'name' => 'types',
			'config' => 'croogo_types',
		),
	));

	$alias = '';
	$base_model_name = '';
	$model_name = '';
	$route = '';

	$types = array(
		array(
			'Params' => array(
				'detail' => true,
				'plugin' => false,
				'model' => 'ApptDetail',
				'controller' => 'ApptsController',
				'helper' => 'Appts',





	foreach ($types as $type) {
		$p = $type['Params'];
		if (isset($p['detail']) && $p['detail']) {
			if (isset($p['model']) && $p['model']) {
				if (strpos($p['model']
				$root_model_name = $p['model'];
			} else {
				$root_model_name = Inflector::classify($type['Type']['alias']);
			}
			$route = (isset($p['route']) && $p['route']) ? $p['route'] : Inflector::pluralize($root_model_name);
			$plugin = (isset($p['plugin'])) ?  $p['plugin'] : 'test';
		}
		CroogoRouter::connect("/{$route}", array('plugin' => $plugin, 'controller' => $controller, 'action' => 'index'));
	}



	$model = 'ApptDetail';
	$route = 'appts';

 */

	// let's do just this first one
	//CroogoRouter::connect('/events', array('plugin' => 'event', 'controller' => 'events', 'action' => 'index'));
	//CroogoRouter::connect('/events/calendar', array('plugin' => 'event', 'controller' => 'events', 'action' => 'calendar'));
