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
 * Adds new instance of enrol_warwickauto to specified course
 * or edits current instance.
 *
 * @package enrol_warwickauto
 * @author Eugene Venter <eugene@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class enrol_warwickauto_edit_form extends moodleform {

    protected function definition() {
        global $DB;

        $mform = $this->_form;

        // Clear the observer cache to ensure observers for any newly-installed plugins are added
        $cache = \cache::make('core', 'observers');
        $cache->delete('all');

        list($instance, $plugin, $context) = $this->_customdata;

        $mform->addElement('header', 'header', get_string('pluginname', 'enrol_warwickauto'));

        $mform->addElement('text', 'name', get_string('custominstancename', 'enrol'));
        $mform->setType('name', PARAM_TEXT);

        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        $mform->addElement('select', 'status', get_string('status', 'enrol_warwickauto'), $options);
        $mform->addHelpButton('status', 'status', 'enrol_warwickauto');

        $options = array(ENROL_WARWICKAUTO_COURSE_VIEWED => get_string('courseview', 'enrol_warwickauto'),
                         ENROL_WARWICKAUTO_LOGIN => get_string('userlogin', 'enrol_warwickauto'),
                         ENROL_WARWICKAUTO_MOD_VIEWED    => get_string('modview', 'enrol_warwickauto'));
        $mform->addElement('select', 'customint3', get_string('enrolon', 'enrol_warwickauto'), $options);
        $mform->addHelpButton('customint3', 'enrolon', 'enrol_warwickauto');

        $mods = \enrol_warwickauto\helper::get_mods_with_viewed_event();
        $modgroup = array();
        foreach ($mods as $modname) {
            $modgroup[] = $mform->createElement('checkbox', $modname, '', get_string('pluginname', "mod_{$modname}"));
        }
        $mform->addGroup($modgroup, 'customtext2', get_string('modviewmods', 'enrol_warwickauto'), '<br>', true);
        $mform->disabledIf('customtext2', 'customint3', 'neq', ENROL_WARWICKAUTO_MOD_VIEWED);

        $roles = $this->extend_assignable_roles($context, $instance->roleid);
        $mform->addElement('select', 'roleid', get_string('role', 'enrol_warwickauto'), $roles);

        $mform->addElement('advcheckbox', 'customint2', get_string('sendcoursewelcomemessage', 'enrol_warwickauto'));
        $mform->addHelpButton('customint2', 'sendcoursewelcomemessage', 'enrol_warwickauto');

        $mform->addElement('textarea', 'customtext1', get_string('customwelcomemessage', 'enrol_warwickauto'), array('cols' => '60', 'rows' => '8'));
        $mform->addHelpButton('customtext1', 'customwelcomemessage', 'enrol_warwickauto');
        $mform->disabledIf('customtext1', 'customint2', 'notchecked');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons(true, ($instance->id ? null : get_string('addinstance', 'enrol')));

        $instance->customtext2 = array_flip(explode(',', $instance->customtext2));
        $instance->customtext2 = array_map(
            function ($a) {
                return 1;
            },
            $instance->customtext2
        );
        $this->set_data($instance);
    }

    /**
     * Gets a list of roles that this user can assign for the course as the default for auto-enrolment.
     *
     * @param context $context the context.
     * @param integer $defaultrole the id of the role that is set as the default for auto-enrolment
     * @return array index is the role id, value is the role name
     */
    protected function extend_assignable_roles($context, $defaultrole) {
        global $DB;

        $roles = get_assignable_roles($context, ROLENAME_BOTH);
        if (!isset($roles[$defaultrole])) {
            if ($role = $DB->get_record('role', array('id' => $defaultrole))) {
                $roles[$defaultrole] = role_get_name($role, $context, ROLENAME_BOTH);
            }
        }
        return $roles;
    }
}