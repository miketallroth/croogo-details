<?php
CakeLog::write('debug',print_r($this->request->data,true));

$this->extend('/Common/admin_edit');

$this->Html->addCrumb('', '/admin', array('icon' => $_icons['home']))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Types'), array('plugin' => 'taxonomy', 'controller' => 'types', 'action' => 'index'))
	->addCrumb(__d('croogo', $typeName), array('plugin' => 'taxonomy', 'controller' => 'types', 'action' => 'edit', $typeId));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb(__d('croogo', 'Edit Detail Field'), '/' . $this->request->url);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__d('croogo', 'Add Detail Field'), '/' . $this->request->url);
}

$this->append('form-start', $this->Form->create('Type'));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Detail Field'), '#detail-main');
$this->end();

$this->append('tab-content');

	echo $this->Html->tabStart('detail-main') .
		$this->Form->input('id') .
		$this->Form->input('name', array(
			'label' => __d('croogo', 'Field Name'),
		)) .
		$this->Form->input('type', array(
			'label' => __d('croogo', 'Field Type'),
			'options' => array(
				'string' => 'string',
				'text' => 'text',
				'integer' => 'integer',
				'float' => 'float',
				'decimal' => 'decimal',
				'datetime' => 'datetime',
				'timestamp' => 'timestamp',
				'time' => 'time',
				'date' => 'date',
				'boolean' => 'boolean',
			),
		)) .
		$this->Form->input('null', array(
			'label' => __d('croogo', 'Null Allowed'),
			'type' => 'checkbox',
		)) .
		$this->Form->input('default', array(
			'label' => __d('croogo', 'Default Value'),
		)) .
		$this->Form->input('length', array(
			'label' => __d('croogo', 'Length (valid only for string, text, integer, float, decimal)'),
		)) .
		$this->Form->input('unsigned', array(
			'label' => __d('croogo', 'Unsigned (valid only for integer)'),
			'type' => 'checkbox',
		));
	echo $this->Html->tabEnd();

$this->end();

$this->append('form-end', $this->Form->end());
