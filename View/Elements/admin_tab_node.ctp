<?php
	$this->Html->script(array('/event/js/jquery.datetimepicker'), array('inline'=>false));
	$this->Html->css(array('/event/css/theme'), null, array('inline'=>false));

	$type = $this->Nodes->field('type');
	print_r($type);
	$typeDef = ClassRegistry::init('Taxonomy.Type')->find('first', array(
		'conditions' => array(
			'alias' => $type,
		),
	));

	$p = $typeDef['Params'];

	$detailModelName = Inflector::classify($type) . 'Detail';
	$detailFields = ClassRegistry::init($detailModelName)->schema();

	/*
	$extra = '';
	 */

	foreach ($detailFields as $fieldName => $meta) {
		if ($fieldName == 'id') {
			echo $this->Form->input('AppointmentDetail.id');
			continue;
		}
		if ($fieldName == 'node_id') {
			echo $this->Form->input('AppointmentDetail.node_id', array('type'=>'hidden', 'value'=>$this->data['Node']['id']));
			continue;
		}

		$field = $Layout->node[$detailModelName][$fieldName];
		/*
		$extra .= '<div class="' . $type . '-detail">';
		$extra .= Inflector::humanize($fieldName) . ': ';
		 */
		switch ($meta['type']) {
		case 'datetime':
			/*
			if(!empty($field)) {
				$dateFormat = Configure::read('Reading.date_time_format');
				$extra .= date($dateFormat, strtotime($field));
			}
			 */
			echo $this->Form->input($detailModelName . '.' . $field, array('class'=>'datetimepicker', 'type'=>'text'));
			break;
		case 'boolean':
			/*
			$extra .= $field ? 'true' : 'false';
			 */
			echo $this->Form->input($detailModelName . '.' . $field, array('class'=>'checkbox', 'type'=>'checkbox'));
			break;
		case 'integer':
		default:
			/*
			$extra .= $field;
			 */
			echo $this->Form->input($detailModelName . '.' . $field, array('class'=>'text', 'type'=>'text'));
		}
		/*
		$extra .= '</div>';
		 */
	}

	/*
		$modifiedBody = $Layout->node('body') . $extra;
		$event->subject->Layout->setNodeField('body',$modifiedBody);
	}
	 */

?>

<script type="text/javascript">
	$(document).ready(function(){
		$('#AppointmentDetailStartDate').datetimepicker({
			dateFormat: 'yy-mm-dd',
			timeFormat: 'hh:mm:ss'

		});
		$('#AppointmentDetailEndDate').datetimepicker({
			dateFormat: 'yy-mm-dd',
			timeFormat: 'hh:mm:ss'

		});

	});
</script>





