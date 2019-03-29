<?php
namespace enrol_warwickguest\selector\type\user;

use \enrol_warwickguest\selector\type\settings\department;
use \enrol_warwickguest\selector\type\traits\user;
use \enrol_warwickguest\selector\search;

class potential_department extends department{
    
    use user;
    
    public function __construct($name, $options) {
        $this->enrolInstance = $options['enrol_instance'];
        parent::__construct($name, $options);
    }

    protected function get_options() {
        global $CFG;
        $options = parent::get_options();

        $options['file'] = 'enrol/warwickguest/classes/selector/type/settings/potential_department.php';
        $options['plugin'] =  $this->plugin;
        $options['enrol_instance'] = $this->enrolInstance;
        return $options;
    }
    
    /**
     * 
     * @global type $DB
     * @param string $search
     * @return type
     */
    public function find_users($search) {
        global $DB;
        
        $searchObject = new search( $search, $this->propertyFromConfigToDisplay, $this->searchanywhere );        
        $availableDepartments = parent::find_users( $search );
        
        $results = $this->filterStoredValues( $availableDepartments, $searchObject, 'customtext2' );

        return is_array( $results ) ? $results : $availableDepartments;
    }
}

