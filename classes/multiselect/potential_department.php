<?php
namespace enrol_warwickauto\multiselect;

use \local_enrolmultiselect\type\available\potential_department as availablepotentialdepartment;

class potential_department extends availablepotentialdepartment{

    protected $field = 'customtext4';

    /**
     * 
     * @param type $name
     * @param type $options
     */
    public function __construct($name, $options) {
        parent::__construct($name, $options);
    }
}