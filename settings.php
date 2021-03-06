<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Auto enrolment plugin settings and presets.
 *
 * @package     enrol_warwickauto
 * @author      Eugene Venter <eugene@catalyst.net.nz>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


$pluginName = 'enrol_warwickauto';
$designationUrl = new moodle_url( "/local/enrolmultiselect/designation.php", [ 'plugin_name' => $pluginName ] );
$departmentUrl = new moodle_url( "/local/enrolmultiselect/department.php", [ 'plugin_name' => $pluginName ] );
    
$ADMIN->add('enrolments', new admin_category('enrol_warwickauto', 'Warwick Auto enrolment', true ));
$ADMIN->add('enrol_warwickauto', new admin_externalpage("{$pluginName}_departments", 'Departments', $departmentUrl, ['moodle/site:config','enrol/warwickauto:nonsiteadminconfig']));
$ADMIN->add('enrol_warwickauto', new admin_externalpage("{$pluginName}_designations", 'Designations', $designationUrl, ['moodle/site:config','enrol/warwickauto:nonsiteadminconfig']));

if ($ADMIN->fulltree) {

    require_once($CFG->dirroot.'/enrol/warwickauto/lib.php');

    $settings->add(new admin_setting_heading('enrol_warwickauto_dep','',"<a href='{$departmentUrl}'>Department Settings</a> | <a href='{$designationUrl}'>Designation Settings</a>"));
    $settings->add(new admin_setting_heading('enrol_warwickauto_defaults',
        get_string('enrolinstancedefaults', 'admin'), get_string('enrolinstancedefaults_desc', 'admin')));

    $settings->add(new enrol_warwickauto_hidden('enrol_warwickauto/defaultenrol', get_string('defaultenrol', 'enrol'), get_string('defaultenrol_desc', 'enrol'), 0));

    $settings->add(new enrol_warwickauto_hidden('enrol_warwickauto/status', get_string('status', 'enrol_warwickauto'), get_string('status_desc', 'enrol_warwickauto'), ENROL_INSTANCE_DISABLED));

    $settings->add(new enrol_warwickauto_hidden('enrol_warwickauto/enrolon', get_string('enrolon', 'enrol_warwickauto'), get_string('enrolon_desc', 'enrol_warwickauto'), ENROL_WARWICKAUTO_COURSE_VIEWED));

    // Clear the observer cache to ensure observers for any newly-installed plugins are added
    if (!empty($PAGE->url) && strstr($PAGE->url, 'section=enrolsettingsauto')) {
        $cache = \cache::make('core', 'observers');
        $cache->delete('all');
    }

    if (!during_initial_install()) {
        $options = get_default_enrol_roles(context_system::instance());
        $student = get_archetype_roles('student');
        $student = reset($student);
        $settings->add(new admin_setting_configselect('enrol_warwickauto/roleid',
            get_string('defaultrole', 'enrol_warwickauto'), get_string('defaultrole_desc', 'enrol_warwickauto'), $student->id, $options));
    }

    $settings->add(new admin_setting_configcheckbox('enrol_warwickauto/sendcoursewelcomemessage',
        get_string('sendcoursewelcomemessage', 'enrol_warwickauto'), get_string('sendcoursewelcomemessage_help', 'enrol_warwickauto'), 1));
}
