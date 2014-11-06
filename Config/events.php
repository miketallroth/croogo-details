<?php

// TODO split out DetailsHelperEventHandler from DetailsEventHandler to be separately enabled.
// TODO create a setting so you can enable/disable default DetailsHelperEventHandler data formatting.

$config = array(
	'EventHandlers' => array(
		'Details.DetailsEventHandler' => array(
			'priority' => 20,
		),
	),
);
