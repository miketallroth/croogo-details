<?php

App::uses('DetailsUtility','Details.Lib');


/**
 * Behavior
 *
 * Behavior "contains" the associated models so they don't get removed.
 * Use priority 1 so this behavior runs before Containable. Need to
 * have behaviors loaded first since we use Taxonomy.Type immediately.
 */
	Croogo::hookBehavior('Node', 'Details.Detail', array(
		'priority' => 1,
	));
	Croogo::hookBehavior('Type', 'Details.DetailType', array('priority'=>1));

/**
 * Get all types to search for types with Detail
 */
	$Type = ClassRegistry::init('Taxonomy.Type');
	$Utility = new DetailsUtility();
	$typeDefs = $Type->find('all');
	foreach ($typeDefs as $index => $typeDef) {
		$typeDef['Params'] = $Utility::convertTypes($typeDef['Params']);
	}
	Configure::write('Details.typeDefs',$typeDefs);

/**
 * hasOne relationship
 *
 * Now, Node hasOne <model>Detail. Dependent so deletes work.
 */
	foreach ($typeDefs as $typeDef) {
		$p = $typeDef['Params'];
		if (isset($p['detail']) && $p['detail']) {
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
		if (isset($p['detail']) && $p['detail']) {
			$types[] = $typeDef['Type']['alias'];
		}
	}
	Croogo::hookAdminTab('Nodes/admin_add', 'Details', 'Details.admin_tab_node', array('type'=>$types));
	Croogo::hookAdminTab('Nodes/admin_edit', 'Details', 'Details.admin_tab_node', array('type'=>$types));
	Croogo::hookAdminBox('Types/admin_edit', 'Details', 'Details.admin_box_type');

/**
 * Settings menu
 * Place this between Writing and Comment in Settings menu
 */
	CroogoNav::add('sidebar', 'settings.children.details', array(
		'title' => 'Details',
		'url' => array(
			'admin' => true,
			'plugin' => false,
			'controller' => 'settings',
			'action' => 'settings/prefix/Details',
		),
		'weight' => 50,
	));