<?php
namespace local_enrolmultiselect\type\settings;

use \local_enrolmultiselect\base;
use \local_enrolmultiselect\config;
use \local_enrolmultiselect\utils;
use \local_enrolmultiselect\search;
use \local_enrolmultiselect\type\basedepartment; 

class department extends base{
    use user;

    
    /**
     *
     * @var type 
     */
    protected $enrolInstance;

    /**
     * 
     * @global type $CFG
     * @param string $name
     * @param array $options
     */
    public function __construct($name, $options) {
        global $CFG;
        
        require_once($CFG->dirroot . '/lib/accesslib.php');
        
        $this->config = new config( $options['plugin'], $this->configName, $this->propertyFromConfigToDisplay );
        $this->enrolInstance = $options['enrol_instance'];
        parent::__construct($name, $options);
    }

    protected function get_options() {
        global $CFG;
        $options = parent::get_options();

        $options['file'] = 'enrol/warwickguest/classes/selector/type/user/department.php';
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
        
        $searchObject = new search( $search , $this->propertyFromConfigToDisplay, $this->searchanywhere );
        $designations = self::extractFlatConfig( $this->enrolInstance, $search ? $searchObject : null, 'customtext2');
        
        if(!$designations)
            return array();
        
        $results = array(); // The results array we are building up.
        foreach ($designations as $key=>$designation) {
        
            $group = $this->getGroupName( $designation );
            $designation->id = $designation->{$this->propertyFromConfigToDisplay};
            $results[ $group ][] = $designation ;
        }

        return $results;
    }
    
    public function getGroups() {
        return self::GROUPS;
    }
    
    public function getDefaultGroup() {
        return self::DEFAULT_GROUP;
    }
}