<?php

	$d = $this->request->data;
	$d['Params'] = DetailsUtility::convertTypes($d['Params']);
	$t = isset($d['Type']['alias']) ? $d['Type']['alias'] : false;
	$p = isset($d['Params']['detail']) ? $d['Params']['detail'] : false;

	$typeId = $d['Type']['id'];

	if ($t && $p) {
		$detailModelName = Inflector::classify($t) . 'Detail';
		try {
			$detailFields = ClassRegistry::init($detailModelName)->schema();
		} catch (MissingTableException $e) {
			$detailFields = null;
		}

		$jsReady = '';

		if (empty($detailFields)) {

			echo "<div>Error: No Database Table Found. Re-save this type and try editing again. A forced reload in your browser may be required.</div>";

		} else {

			echo '<div class="row-fluid">';

			echo '<table class="table table-stripped">';
			$tableHeaders = $this->Html->tableHeaders(array(
				'Name', 'Type', 'Actions'
			));
			echo $this->Html->tag('thead', $tableHeaders);

			echo "<tbody>";

			foreach ($detailFields as $fieldName => $meta) {
				if ($fieldName == 'id' || $fieldName == 'node_id') {
					continue;
				}

				$f = Inflector::humanize($fieldName);
				echo "<tr>";
				echo "<td>{$f}</td>";
				echo "<td>{$meta['type']}</td>";
				echo '<td><div class="item-actions">';

				echo $this->Croogo->adminRowAction('',
					array('admin' => true, 'plugin' => 'details', 'controller' => 'details', 'action' => 'moveup', $typeId, $fieldName),
					array('icon' => $this->Theme->getIcon('move-up'), 'tooltip' => __d('croogo', 'Move up'))
				);
				echo $this->Croogo->adminRowAction('',
					array('plugin' => 'details', 'controller' => 'details', 'action' => 'movedown', $typeId, $fieldName),
					array('icon' => $this->Theme->getIcon('move-down'), 'tooltip' => __d('croogo', 'Move down'))
				);
				echo $this->Croogo->adminRowAction('',
					array('plugin' => 'details', 'controller' => 'details', 'action' => 'edit', $typeId, $fieldName),
					array('icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item'))
				);
				echo ' ' . $this->Croogo->adminRowAction('',
					array('plugin' => 'details', 'controller' => 'details', 'action' => 'delete_field', $typeId, $fieldName),
					array(
						'icon' => $this->Theme->getIcon('delete'),
						'class' => 'delete',
						'tooltip' => __d('croogo', 'Remove this item'),
						'rowAction' => 'delete',
					),
					__d('croogo', 'Are you sure?')
				);
				echo "</td></tr>";
			}
			echo "</tbody></table></div>";

			echo $this->Html->link(
				__d('croogo','Add another field'),
				array('plugin'=>'details', 'controller'=>'details', 'action'=>'add', $typeId),
				array('class'=>'add')
			);
			echo $this->Html->link(
				__d('croogo','Disable details'),
				array('plugin'=>'details', 'controller'=>'details', 'action'=>'toggle', $typeId, 'off'),
				array('class'=>'delete', 'style'=>'float:right;')
			);
		}
	} else {
		echo '&nbsp;';
		echo $this->Html->link(
			__d('croogo','Enable details'),
			array('plugin'=>'details', 'controller'=>'details', 'action'=>'toggle', $typeId, 'on'),
			array('class'=>'delete', 'style'=>'float:right;')
		);
	}
?>
