<?php

require_once($CFG->libdir.'/adminlib.php');

defined('MOODLE_INTERNAL') || die();

class enrol_warwickauto_hidden extends admin_setting_configtext{
    
    public function __construct($name, $visiblename, $description, $defaultsetting, $paramtype = PARAM_RAW, $cols = '60', $rows = '8') {
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype, $cols, $rows);
    }

    public function output_html($data, $query = '') {

        global $OUTPUT;

        $default = $this->get_defaultsetting();
        $context = (object) [
            'size' => $this->size,
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data,
            'forceltr' => $this->get_force_ltr(),
        ];

        $element = $OUTPUT->render_from_template('enrol_warwickauto/setting_confighidden', $context);

        return $element;
    }
}