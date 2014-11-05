<?php

	// routes=true in the content type's params field automatically hooks
	// the singular version of the content type to the index.
	// Here, we hook the pluralized version of the content type to the index
	$typeDefs = Configure::read('Details.typeDefs');
	foreach ($typeDefs as $typeDef) {
		$p = $typeDef['Params'];
		if (isset($p['detail']) && $p['detail']) {
			$route = Inflector::pluralize($typeDef['Type']['alias']);
			CroogoRouter::connect("/{$route}", array(
				'plugin' => 'nodes',
				'controller' => 'nodes',
				'action' =>  'index',
				'type' => $typeDef['Type']['alias'],
			));
		}
	}
