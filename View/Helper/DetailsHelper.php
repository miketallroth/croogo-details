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
	);
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
 * Details field: with key/value fields
 *
 * @param string $key (optional) key
 * @param string $value (optional) value
 * @param integer $id (optional) ID of Meta
 * @param array $options (optional) options
 * @return string
 */
    public function field($key = '', $value = null, $id = null, $options = array()) {
        $inputClass = $this->Layout->cssClass('formInput');
        $_options = array(
            'key' => array(
                'label' => __d('croogo', 'Key'),
                'value' => $key,
            ),
            'value' => array(
                'label' => __d('croogo', 'Value'),
                'value' => $value,
                'type' => 'textarea',
                'rows' => 2,
            ),
        );
        if ($inputClass) {
            $_options['key']['class'] = $_options['value']['class'] = $inputClass;
        }
        $options = Hash::merge($_options, $options);
        $uuid = String::uuid();

        $fields = '';
        if ($id != null) {
            $fields .= $this->Form->input('Meta.' . $uuid . '.id', array('type' => 'hidden', 'value' => $id));
            $this->Form->unlockField('Meta.' . $uuid . '.id');
        }
        $fields .= $this->Form->input('Meta.' . $uuid . '.key', $options['key']);
        $fields .= $this->Form->input('Meta.' . $uuid . '.value', $options['value']);
        $this->Form->unlockField('Meta.' . $uuid . '.key');
        $this->Form->unlockField('Meta.' . $uuid . '.value');
        $fields = $this->Html->tag('div', $fields, array('class' => 'fields'));

        $id = is_null($id) ? $uuid : $id;
        $deleteUrl = $this->settings['deleteUrl'];
        $deleteUrl[] = $id;
        $actions = $this->Html->link(
            __d('croogo', 'Remove'),
            $deleteUrl,
            array('class' => 'remove-meta', 'rel' => $id)
        );
        $actions = $this->Html->tag('div', $actions, array('class' => 'actions'));

        $output = $this->Html->tag('div', $actions . $fields, array('class' => 'meta'));
        return $output;
    }



}
