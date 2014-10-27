<?php

	$t = $type['Type']['alias'];
	$p = $type['Params'];

	// distinguish between add and edit
	$isEdit = false;
	if (strpos($this->params['action'], 'edit') !== false) {
		$isEdit = true;
	}

	$detailModelName = Inflector::classify($t) . 'Detail';
	$detailFields = ClassRegistry::init($detailModelName)->schema();

	$jsReady = '';

	foreach ($detailFields as $fieldName => $meta) {

		$f = "{$detailModelName}.{$fieldName}";
		$jsLabel = Inflector::classify(str_replace('.','_',$f));

		if ($fieldName == 'id') {
			if ($isEdit) {
				echo $this->Form->input($f);
			}
			continue;
		}
		if ($fieldName == 'node_id') {
			if ($isEdit) {
				echo $this->Form->input($f, array('type'=>'hidden', 'value'=>$this->data['Node']['id']));
			}
			continue;
		}

		switch ($meta['type']) {
		case 'datetime':
			echo $this->Form->input($f, array('class'=>'datetimepicker', 'type'=>'text'));
			$jsReady .= "\$('#{$jsLabel}').datetimepicker({dateFormat: 'yy-mm-dd', timeFormat: 'hh:mm:ss'});";
			break;
		case 'boolean':
			echo $this->Form->input($f, array('class'=>'checkbox', 'type'=>'checkbox'));
			break;
		case 'integer':
		default:
			echo $this->Form->input($f, array('class'=>'text', 'type'=>'text'));
		}
	}

	if (!empty($jsReady)) {
		$this->Html->script(array('/details/js/jquery.datetimepicker'), array('inline'=>false));
		$this->Html->css(array('/details/css/theme'), null, array('inline'=>false));
		$js = '$(document).ready(function(){' . $jsReady . '});';
		echo '<script type="text/javascript">';
		echo $js;
		echo '</script>';
	}

?>
