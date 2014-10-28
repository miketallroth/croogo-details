<?php

/**
 * Get all types to search for types with Detail
 */
	$typeDefs = ClassRegistry::init('Taxonomy.Type')->find('all');

/**
 * Routes
 *
 * Plugin/Details/Config/routes.php will be loaded
 */
	Croogo::hookRoutes('Details');

/**
 * Behavior
 *
 * Behavior "contains" the associated models so they don't get removed.
 * Use priority 1 so this behavior runs before Containable.
 */
	Croogo::hookBehavior('Node', 'Details.Detail', array(
		'priority' => 1,
	));

/**
 * hasOne relationship
 *
 * Now, Node hasOne Appt. Dependent so deletes work.
 */
	foreach ($typeDefs as $typeDef) {
		$p = $typeDef['Params'];
		if (isset($p['detail'])) {
			$detailModelName = Inflector::classify($typeDef['Type']['alias']) . 'Detail';
			Croogo::hookModelProperty('Node', 'hasOne', array($detailModelName => array(
				'className' => $detailModelName,
				'foreignKey' => 'node_id',
				'dependent' => true,
			)));
		}
	}

/**
 * Helper
 *
 * Adjust output values prior to display.
 */
	Croogo::hookHelper('Nodes', 'Details.Details');
	Croogo::hookHelper('Types', 'Details.Details');

/**
 * Admin tab
 *
 * When adding/editing Content (Nodes),
 * An extra tab with title 'Details' will be shown with markup generated
 * from the plugin's admin_tab_node element.
 */
	$types = array();
	foreach ($typeDefs as $typeDef) {
		$p = $typeDef['Params'];
		if (isset($p['detail'])) {
			$types[] = $typeDef['Type']['alias'];
		}
	}
	Croogo::hookAdminTab('Nodes/admin_add', 'Details', 'Details.admin_tab_node', array('type'=>$types));
	Croogo::hookAdminTab('Nodes/admin_edit', 'Details', 'Details.admin_tab_node', array('type'=>$types));

	Croogo::hookAdminTab('Types/admin_add', 'Details', 'Details.admin_tab_type');
	Croogo::hookAdminTab('Types/admin_edit', 'Details', 'Details.admin_tab_type');
	Croogo::hookAdminBox('Types/admin_edit', 'Details', 'Details.admin_tab_type');
