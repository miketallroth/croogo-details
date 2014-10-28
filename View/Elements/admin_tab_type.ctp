<?php

// steps:
// if the type already exists (we are editing),
//  then get the schema and display each field with delete link and column name and type,
//  and display a single add new field link.
// if the type does not exist (we are adding),
//  then simply display a single add new field link.

	//print_r($this->request->data);

	$d = $this->request->data;
	$t = (isset($d['Type']['alias'])) ? $d['Type']['alias'] : null;

	if ($t) {

        $detailModelName = Inflector::classify($t) . 'Detail';
        $detailFields = ClassRegistry::init($detailModelName)->schema();

        $jsReady = '';

        foreach ($detailFields as $fieldName => $meta) {

            if ($fieldName == 'id' || $fieldName == 'node_id') {
                continue;
            }

            //echo "<div>{$fieldName}</div><div>{$meta['type']}</div>";
		    /*
		    echo $this->Form->input('AppointmentDetail.id');
		    echo $this->Form->input('AppointmentDetail.node_id', array('type'=>'hidden', 'value'=>$this->data['Node']['id']));
		    echo $this->Form->input('AppointmentDetail.start_date', array('class'=>'datetimepicker', 'type'=>'text'));
		    echo $this->Form->input('AppointmentDetail.end_date', array('class'=>'datetimepicker', 'type'=>'text'));
	 	    */

            echo $this->Details->field($fieldName, $meta['type']);

        }
	}

	echo $this->Html->link(
		__d('croogo','Add another field'),
		array('plugin'=>'details', 'controller'=>'details', 'action'=>'add_detail'),
		array('class'=>'add-detail')
	);

    if (!empty($jsReady)) {
        $this->Html->script(array('/details/js/jquery.datetimepicker'), array('inline'=>false));
        $this->Html->css(array('/details/css/theme'), null, array('inline'=>false));
        $js = '$(document).ready(function(){' . $jsReady . '});';
        echo '<script type="text/javascript">';
        echo $js;
        echo '</script>';
    }

?>
