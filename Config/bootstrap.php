<?php

/**
 * Get all types to search for types with Detail
 */
	$Type = ClassRegistry::init('Taxonomy.Type');
	$typeDefs = $Type->find('all');

	foreach ($typeDefs as $index => $typeDef) {
		foreach ($typeDef['Params'] as $key => $value) {
			if (is_string($value)) {
				$num_check = filter_var($value, FILTER_VALIDATE_INT, array('flags'=>FILTER_FLAG_ALLOW_HEX));
				$bool_check = filter_var($value, FILTER_VALIDATE_BOOLEAN, array('flags'=>FILTER_NULL_ON_FAILURE));
				if ($num_check !== false) {
					$typeDefs[$index]['Params'][$key] = $num_check;
				} else if (!is_null($bool_check)) {
					$typeDefs[$index]['Params'][$key] = $bool_check;
				}
			}
		}
	}

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
			$dt = Configure::read('Details.tables');

			// Create any details tables that don't exist
			if (strpos($dt, $detailModelName) === false) {
				$tableName = Inflector::tableize($detailModelName);

				$tables = $Type->query('show tables');
				$tables = Hash::extract($tables, '{n}.TABLE_NAMES.Tables_in_slagit');
				if (!in_array($tableName, $tables)) {
					$Type->query("CREATE TABLE {$tableName} (`id` int(10) not null auto_increment, `node_id` int(10) default null, primary key (`id`))");
				}
				$Setting = ClassRegistry::init('Settings.Setting');
				$setting = $Setting->find('first', array(
					'conditions' => array(
						'key' => 'Details.tables',
					),
				));
				$setting['Setting']['value'] .= "{$detailModelName},";
				$Setting->save($setting);
			}

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
		if (isset($p['detail'])) {
			$types[] = $typeDef['Type']['alias'];
		}
	}
	Croogo::hookAdminTab('Nodes/admin_add', 'Details', 'Details.admin_tab_node', array('type'=>$types));
	Croogo::hookAdminTab('Nodes/admin_edit', 'Details', 'Details.admin_tab_node', array('type'=>$types));

	Croogo::hookAdminBox('Types/admin_edit', 'Details', 'Details.admin_box_type');
