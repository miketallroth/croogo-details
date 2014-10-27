<?php

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
	Croogo::hookModelProperty('Node', 'hasOne', array('AppointmentDetail' => array(
		//'className' => 'Appointments.AppointmentDetail',
		'className' => 'AppointmentDetail',
		'foreignKey' => 'node_id',
		'dependent' => true,
	)));

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
 * an extra tab with title 'Appointment' will be shown with markup generated
 * from the plugin's admin_tab_node element.
 *
 * TODO Get this from the type's 'title' attribute.
 */
	Croogo::hookAdminTab('Nodes/admin_add', 'Details', 'Appointments.admin_tab_node_add', array('type'=>array('appointment')));
	Croogo::hookAdminTab('Nodes/admin_edit', 'Details', 'Appointments.admin_tab_node', array('type'=>array('appointment')));


	Croogo::hookAdminTab('Types/admin_add', 'Details', 'Details.admin_tab_type_add');
	Croogo::hookAdminTab('Types/admin_edit', 'Details', 'Details.admin_tab_type');
