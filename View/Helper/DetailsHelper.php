<?php
/**
 * Details Helper
 *
 * Details helper for adding custom detail data onto pages.
 *
 * @category Helper
 * @package  Details
 * @version  1.0
 * @author   Mike Tallroth <mike.tallroth@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://github.com/miketallroth/croogo-details
 */
class DetailsHelper extends AppHelper {
/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
	public $helpers = array(
		'Html',
		'Layout',
        'Form',
	);

    public $settings = array(
        'deleteUrl' => array(
            'admin' => true, 'plugin' => 'details',
            'controller' => 'details', 'action' => 'delete_detail',
        ),
    );

/**
 * beforeRender
 */
	public function beforeRender($viewFile) {
		if ($this->_View->Layout->isLoggedIn()) {
			return $this->_View->Croogo->adminScript('Details.admin');
		}
	}


/**
 * Called before LayoutHelper::nodeBody()
 * 
 * This is a generic 
 * 
 * Integrate your custom field formatting code into the flow via an
 * Event listener using Helper.Nodes.afterSetNode.
 *
 * @return string
 */
	/*
	public function beforeNodeBody() {
		$type = $this->Layout->node['Node']['type'];
		$detailModel = Inflector::classify($type) . 'Detail';
		$detailFields = ClassRegistry::init($detailModel)->schema();
		print_r($detailFields);
		print_r($this->Layout->node);
		$out = '';
		foreach ($detailFields as $fieldName => $meta) {
			if ($meta['type'] == 'datetime') {
			if(!empty($this->Layout->node[$detailModel][$fieldName])) {
				//$out .= '<div class="' . $type . '-detail">';
					//From: '.date(Configure::read('Reading.date_time_format'), strtotime($this->Layout->node['AppointmentDetail']['start_date'])).'<br />
					//To: '.date(Configure::read('Reading.date_time_format'), strtotime($this->Layout->node['AppointmentDetail']['end_date'])).'
				//</div>';
			}
			}
		}
	}
	 */

/**
 * Details field: with name/type fields
 *
 * @param string $colName (optional) column name
 * @param string $colType (optional) column type
 * @param array $options (optional) options
 * @return string
 *
 * column types from MySQL
 *     'string'        // 255
 *     'text'          // over 255
 *     'biginteger'    // 12 to 20
 *     'integer'       // 11 or less
 *     'float'         // float
 *     'decimal'       // float
 *     'datetime'      // datetime Y-m-d H:i:s
 *     'timestamp'     // datetime Y-m-d H:i:s
 *     'time'          // time H:i:s
 *     'date'          // date Y-m-d
 *     'binary'        // blob
 *     'boolean'       // 1 bit
 *
 */
    public function field($colName = '', $colType = null, $options = array()) {
        $inputClass = $this->Layout->cssClass('formInput');
        $_types = array(
            'string', 'text', 'biginteger', 'integer', 'float',
            'decimal', 'datetime', 'time', 'date', 'binary', 'boolean',
        );
        $_types = Hash::combine($_types, '{n}', '{n}');
        $_options = array(
            'colName' => array(
                'label' => __d('croogo', 'Column Name'),
                'value' => $colName,
            ),
            'colType' => array(
                'label' => __d('croogo', 'Column Type'),
                'value' => $colType,
                'type' => 'select',
                'options' => $_types,
            ),
        );

        if ($inputClass) {
            $_options['colName']['class'] = $_options['colType']['class'] = $inputClass;
        }
        $options = Hash::merge($_options, $options);
        $uuid = String::uuid();

        $fields = '';
        $fields .= $this->Form->input('Detail.' . $uuid . '.colName', $options['colName']);
        $fields .= $this->Form->input('Detail.' . $uuid . '.colType', $options['colType']);
        $this->Form->unlockField('Detail.' . $uuid . '.colName');
        $this->Form->unlockField('Detail.' . $uuid . '.colType');
        $fields = $this->Html->tag('div', $fields, array('class' => 'fields'));

        $deleteUrl = $this->settings['deleteUrl'];
        $deleteUrl[] = $uuid;
        $actions = $this->Html->link(
            __d('croogo', 'Remove'),
            $deleteUrl,
            array('class' => 'remove-detail', 'rel' => $uuid)
        );
        $actions = $this->Html->tag('div', $actions, array('class' => 'actions'));

        $output = $this->Html->tag('div', $actions . $fields, array('class' => 'detail'));
        return $output;
    }



}
