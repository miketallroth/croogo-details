<?php

App::uses('CakeEventListener', 'Event');
App::uses('DetailsUtility', 'Details.Lib');

/**
 * Details Event Handler
 *
 * @category Event
 * @package  Details
 * @license  Copyright 2014, Clear Sky Web Services. All rights reserved.
 * @link     http://www.goclearsky.com
 *
 * A sample event handler to be copied and customized into your plugin to
 * customize the formatting of your Details. Also copy Details/Config/events.php
 * and modify for your own situation.
 */
class DetailsEventHandler implements CakeEventListener {

/**
 * implementedEvents
 *
 * @return array
 */
	public function implementedEvents() {
		return array(
			'Helper.Nodes.afterSetNode' => array(
				'callable' => 'afterSetNode',
			),
		);
	}

	/**
	 * Modifies the Node according to custom needs.
	 * Can modify any field value.
	 *
	 * Example:
	 * 	Appointment content type with param "detail=true", and
	 * 	appointment_details table with start_date field.
	 *
	 *	public function afterSetNode($event) {
	 *		// convenience var to LayoutHelper
	 *		$Layout = $event->subject->Layout;
	 *
	 *		// set the Node Detail field for use in custom view templates
	 *		$startDate = $Layout->node('AppointmentDetail.start_date');
	 *		$startDate = date(Configure::read('Reading.date_time_format'), strtotime($startDate));
	 *		$event->subject->Layout->setNodeField('AppointmentDetail.start_date', $startDate);
	 *
	 *		// alter the Node body field for use in default view templates
	 *		$modifiedBody = $Layout->node('body');
	 *		$modifiedBody .= '<p>[Modified by DetailsEventHelper]</p>';
	 *		$modifiedBody .= '<p>'.$Layout->node('AppointmentDetail.start_date').'</p>';
	 *		$event->subject->Layout->setNodeField('body',$modifiedBody);
	 *	}
	 */
	public function afterSetNode($event) {

		if (!Configure::read('Details.enableDefaultBodyUpdate')) {
			return;
		}

		$Layout = $event->subject->Layout;
		$type = $Layout->node('type');
		$typeDef = ClassRegistry::init('Taxonomy.Type')->find('first', array(
			'conditions' => array(
				'alias' => $type,
			),
		));

		$typeDef['Params'] = DetailsUtility::convertTypes($typeDef['Params']);
		$p = (isset($typeDef['Params']['detail'])) ? $typeDef['Params']['detail'] : false;

		// Do nothing if this is not a Detail type
		if (!$p) {
			return;
		}

		$detailModelName = Inflector::classify($type) . 'Detail';
		if (isset($Layout->node[$detailModelName])) {
			$detailFields = ClassRegistry::init($detailModelName)->schema();
			$extra = '<dl class="' . $type . '-detail">';
			foreach ($detailFields as $fieldName => $meta) {
				if ($fieldName == 'id' || $fieldName == 'node_id') {
					continue;
				}
				$field = $Layout->node[$detailModelName][$fieldName];
				$extra .= '<dt>' . Inflector::humanize($fieldName) . '</dt><dd>';
				switch ($meta['type']) {
				case 'datetime':
					if(!empty($field)) {
						$dateFormat = Configure::read('Reading.date_time_format');
						$extra .= date($dateFormat, strtotime($field));
					}
					break;
				case 'boolean':
					$extra .= $field ? 'true' : 'false';
					break;
				case 'integer':
				default:
					$extra .= $field;
				}
				$extra .= '</dd>';
			}
			$extra .= '</dl>';

			$modifiedBody = $Layout->node('body') . $extra;
			$event->subject->Layout->setNodeField('body',$modifiedBody);
		}
	}

}
