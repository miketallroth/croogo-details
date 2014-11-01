<?php

    $d = $this->request->data;
	$d['Params'] = DetailsUtility::convertTypes($d['Params']);
    $t = isset($d['Type']['alias']) ? $d['Type']['alias'] : false;
    $p = isset($d['Params']['detail']) ? $d['Params']['detail'] : false;

    if ($t && $p) {
        $detailModelName = Inflector::classify($t) . 'Detail';
        $detailFields = ClassRegistry::init($detailModelName)->schema();
    	$typeId = $d['Type']['id'];

        $jsReady = '';

        if (empty($detailFields)) {
            echo "<div>Error: No Database Table Found</div>";
            //return;
        }

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
                array('icon' => $_icons['move-up'], 'tooltip' => __d('croogo', 'Move up'))
            );
            echo $this->Croogo->adminRowAction('',
                array('plugin' => 'details', 'controller' => 'details', 'action' => 'movedown', $typeId, $fieldName),
                array('icon' => $_icons['move-down'], 'tooltip' => __d('croogo', 'Move down'))
            );
            echo $this->Croogo->adminRowAction('',
                array('plugin' => 'details', 'controller' => 'details', 'action' => 'edit', $typeId, $fieldName),
                array('icon' => $_icons['update'], 'tooltip' => __d('croogo', 'Edit this item'))
            );
            echo ' ' . $this->Croogo->adminRowAction('',
				array('plugin' => 'details', 'controller' => 'details', 'action' => 'delete', $typeId, $fieldName),
                array(
                    'icon' => $_icons['delete'],
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
		    array('plugin'=>'details', 'controller'=>'details', 'action'=>'add_field', $typeId),
		    array('class'=>'add-field')
	    );
    }
?>


