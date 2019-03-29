<?php
namespace enrol_warwickauto\multiselect;

use \local_enrolmultiselect\type\available\potential_designation as availablepotentialdesignation;

class potential_designation extends availablepotentialdesignation{

    protected $field = 'customtext3';

    /**
     * 
     * @global type $CFG
     * @param string $name
     * @param array $options
     */
    public function __construct($name, $options) {
        parent::__construct($name, $options);
    }  
}