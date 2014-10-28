<?php
	$this->Html->script(array('/event/js/jquery.datetimepicker'), array('inline'=>false));
	$this->Html->css(array('/event/css/theme'), null, array('inline'=>false));

	echo "<div>TODO: genericize Details/View/Elements/admin_tab* to be usable for all attribute types</div>";

	/*
    echo $this->Form->input('AppointmentDetail.id');
    echo $this->Form->input('AppointmentDetail.node_id', array('type'=>'hidden', 'value'=>$this->data['Node']['id']));
    echo $this->Form->input('AppointmentDetail.start_date', array('class'=>'datetimepicker', 'type'=>'text'));
    echo $this->Form->input('AppointmentDetail.end_date', array('class'=>'datetimepicker', 'type'=>'text'));
	 */

	echo $this->Html->link(
		__d('croogo','Add another field'),
		array('plugin'=>'details', 'controller'=>'details', 'action'=>'add_detail'),
		array('class'=>'add-detail')
	);
?>
