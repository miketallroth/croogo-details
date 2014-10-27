<?php
	$this->Html->script(array('/event/js/jquery.datetimepicker'), array('inline'=>false));
	$this->Html->css(array('/event/css/theme'), array('inline'=>false));

	echo "<div>TODO: genericize Details/View/Elements/admin_tab* to be usable for all attribute types</div>";

    echo $this->Form->input('AppointmentDetail.start_date', array('class'=>'datetimepicker', 'type'=>'text'));
    echo $this->Form->input('AppointmentDetail.end_date', array('class'=>'datetimepicker', 'type'=>'text'));
?>

<script type="text/javascript">
	$(document).ready(function(){
		$('#AppointmentDetailStartDate').datetimepicker({
			dateFormat: 'yy-mm-dd',
			timeFormat: 'hh:mm'

		});
		$('#AppointmentDetailEndDate').datetimepicker({
			dateFormat: 'yy-mm-dd',
			timeFormat: 'hh:mm'

		});

	});
</script>
