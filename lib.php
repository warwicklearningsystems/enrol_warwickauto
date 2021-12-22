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
 * Auto enrolment plugin.
 *
 * @package     enrol_warwickauto
 * @author      Eugene Venter <eugene@catalyst.net.nz>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('ENROL_WARWICKAUTO_COURSE_VIEWED', 1);
define('ENROL_WARWICKAUTO_MOD_VIEWED', 2);
define('ENROL_WARWICKAUTO_LOGIN', 3);

class enrol_warwickauto_plugin extends enrol_plugin {

    /**
     * Returns optional enrolment information icons.
     *
     * This is used in course list for quick overview of enrolment options.
     *
     * We are not using single instance parameter because sometimes
     * we might want to prevent icon repetition when multiple instances
     * of one type exist. One instance may also produce several icons.
     *
     * @param array $instances all enrol instances of this type in one course
     * @return array of pix_icon
     */
    public function get_info_icons(array $instances) {
        return array(new pix_icon('i/courseevent', get_string('pluginname', 'enrol_warwickauto')));
    }

    public function roles_protected() {
        // Users may tweak the roles later.
        return false;
    }

    public function allow_unenrol(stdClass $instance) {
        // Users with unenrol cap may unenrol other users manually.
        return true;
    }

    public function allow_manage(stdClass $instance) {
        // Users with manage cap may tweak status.
        return true;
    }

    /**
     * Sets up navigation entries.
     *
     * @param stdClass $instancesnode
     * @param stdClass $instance
     * @return void
     */
    public function add_course_navigation($instancesnode, stdClass $instance) {
        if ($instance->enrol !== 'warwickauto') {
             throw new coding_exception('Invalid enrol instance type!');
        }

        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/warwickauto:config', $context)) {
            $managelink = new moodle_url('/enrol/warwickauto/edit.php',
                array('courseid' => $instance->courseid, 'id' => $instance->id));
            $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
        }
    }

    /**
     * Returns edit icons for the page with list of instances
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        if ($instance->enrol !== 'warwickauto') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid);

        $icons = array();

        if (has_capability('enrol/warwickauto:config', $context)) {
            $editlink = new moodle_url("/enrol/warwickauto/edit.php",
                array('courseid' => $instance->courseid, 'id' => $instance->id));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('t/edit', get_string('edit'), 'core',
                array('class' => 'smallicon')));
        }

        return $icons;
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        global $DB;

        $context = context_course::instance($courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) || !has_capability('enrol/warwickauto:config', $context)) {
            return null;
        }

        if ($DB->record_exists('enrol', array('courseid' => $courseid, 'enrol' => 'warwickauto'))) {
            return null;
        }

        return new moodle_url('/enrol/warwickauto/edit.php', array('courseid' => $courseid));
    }

    /**
     * Creates course enrol form, checks if form submitted
     * and enrols user if necessary. It can also redirect.
     *
     * @param stdClass $instance
     * @return string html text, usually a form in a text box
     */

    /**
     * Add new instance of enrol plugin with default settings.
     * @param stdClass $course
     * @return int id of new instance
     */
    public function add_default_instance($course) {
        $fields = $this->get_instance_defaults();

        return $this->add_instance($course, $fields);
    }

    /**
     * Returns defaults for new instances.
     * @return array
     */
    public function get_instance_defaults() {

        $fields = array();
        $fields['status']          = $this->get_config('status');
        $fields['roleid']          = $this->get_config('roleid');
        $fields['customint2']      = $this->get_config('sendcoursewelcomemessage');
        $fields['customint3']      = $this->get_config('enrolon');
        $fields['customtext2']     = $this->get_config('modviewmods');

        return $fields;
    }

    /**
     * Get the instance of this plugin attached to a course if any
     * @param int $courseid id of course
     * @param bool $onlyenabled only return an enabled instance
     * @return object|bool $instance or false if not found
     */
    public function get_instance_for_course($courseid, $onlyenabled=true) {
        global $DB;
        $params = array('enrol' => 'warwickauto', 'courseid' => $courseid);
        if (!empty($onlyenabled)) {
            $params['status'] = ENROL_INSTANCE_ENABLED;
        }

        return $DB->get_record('enrol', $params);
    }

    /**
     * Attempt to automatically enrol current user in course without any interaction,
     * calling code has to make sure the plugin and instance are active.
     *
     * This hook is called from the course view page.
     *
     * This should return either a timestamp in the future or false.
     *
     * @param stdClass $instance course enrol instance
     * @return bool|int false means not enrolled, integer means timeend
     */
    public function try_autoenrol(stdClass $instance) {
        global $USER, $DB;


        $customtext3 = $instance->customtext3;
        $customtext4 = $instance->customtext4;

        $arr_designations = json_decode($customtext3);
        $arr_departments = json_decode($customtext4);

        $array_designation = array();
        $array_department = array();

        foreach ($arr_designations as $key_designations) {
            $array_designation[] = strtoupper(trim($key_designations->phone2));
        }

        foreach ($arr_departments as $key_departments) {
            $array_department[] = strtoupper(trim($key_departments->department));
        }

        $designation = strtoupper(trim($USER->phone2));
        $department = strtoupper(trim($USER->department));

        $allow = false;

        if (in_array($department, $array_department)) {
            $allow = true;
            if (in_array($designation, $array_designation)) {
                $allow = true;
            } else {
                $allow = false;
            }
            if (empty($array_designation)) {
                $allow = true;
            }
            if (trim($designation)=="") {
                $allow = true;
            }
        }

        if (in_array($designation, $array_designation) & trim($designation)!="") {
            $allow = true;
            if (in_array($department, $array_department)) {
                $allow = true;
            } else {
                $allow = false;
            }
            if (empty($array_department)) {
                $allow = true;
            }
            if (trim($department)=="") {
                $allow = true;
            }

        }
        

        if ($allow == false) {
            return false;
        }



        if ($instance->customint3 != ENROL_WARWICKAUTO_COURSE_VIEWED) {
            return false;
        }

        // Prevent guest user from being enrolled
        if (isguestuser()) {
            return false;
        }

        /*
        $designation = new \enrol_warwickauto\multiselect\designation('designations_add', [
            'plugin' => 'enrol_warwickauto',
            'enrol_instance' => $instance
        ]);

        if (!$designation->userAllowed())
            return false;

        $department = new \enrol_warwickauto\multiselect\department('departments_add', [
            'plugin' => 'enrol_warwickauto',
            'enrol_instance' => $instance
        ]);

        if( !$department->userAllowed() )
            return false;
        */

        $this->enrol_user($instance, $USER->id, $instance->roleid);
        // Send welcome message.
        if ($instance->customint2) {
            \enrol_warwickauto\observer::schedule_welcome_email($instance, $USER->id);
        }

        return 0;

    }



    /**
     * Send welcome email to specified user.
     *
     * @param stdClass $instance
     * @param stdClass $user user record
     * @return void
     */
    public function email_welcome_message($instance, $user) {
        global $CFG, $DB, $PAGE;

        $course = $DB->get_record('course', array('id' => $instance->courseid), '*', MUST_EXIST);

        $a = new stdClass();
        $a->coursename = format_string($course->fullname, true);
        $a->profileurl = "{$CFG->wwwroot}/user/view.php?id={$user->id}&course={$course->id}";
        $strmgr = get_string_manager();

        if (trim($instance->customtext1) !== '') {
            $message = $instance->customtext1;
            $message = str_replace('{$a->coursename}', $a->coursename, $message);
            $message = str_replace('{$a->profileurl}', $a->profileurl, $message);
            if (strpos($message, '<') === false) {
                // Plain text only.
                $messagetext = $message;
                $messagehtml = text_to_html($messagetext, null, false, true);
            } else {
                // This is most probably the tag/newline soup known as FORMAT_MOODLE.
                $messagehtml = format_text($message, FORMAT_MOODLE, array('para' => false, 'newlines' => true, 'filter' => true));
                $messagetext = html_to_text($messagehtml);
            }
        } else {
            $messagetext = $strmgr->get_string('welcometocoursetext', 'enrol_warwickauto', $a, $user->lang);
            $messagehtml = text_to_html($messagetext, null, false, true);
        }

        $subject = $strmgr->get_string('welcometocourse', 'enrol_warwickauto', format_string($course->fullname, true), $user->lang);
        $subject = str_replace('&amp;', '&', $subject);

        $rusers = array();
        if (!empty($CFG->coursecontact)) {
            $context = context_course::instance($course->id);
            $croles = explode(',', $CFG->coursecontact);
            list($sort, $sortparams) = users_order_by_sql('u');
            // We only use the first user.
            $i = 0;
            do {
                $rusers = get_role_users($croles[$i], $context, true, '',
                    'r.sortorder ASC, ' . $sort, null, '', '', '', '', $sortparams);
                $i++;
            } while (empty($rusers) && !empty($croles[$i]));
        }
        if ($rusers) {
            $contact = reset($rusers);
        } else {
            $contact = core_user::get_support_user();
        }

        // Send welcome email.
        email_to_user($user, $contact, $subject, $messagetext, $messagehtml);
    }

    /**
     * Returns the user who is responsible for auto enrolments in given instance.
     *
     * Usually it is the first editing teacher - the person with "highest authority"
     * as defined by sort_by_roleassignment_authority() having 'enrol/warwickauto:manage'
     * capability.
     *
     * @param int $instanceid enrolment instance id
     * @return stdClass user record
     */
    protected function get_enroller($instanceid) {
        global $DB;

        if ($this->lasternollerinstanceid == $instanceid and $this->lasternoller) {
            return $this->lasternoller;
        }

        $instance = $DB->get_record('enrol', array('id' => $instanceid, 'enrol' => $this->get_name()), '*', MUST_EXIST);
        $context = context_course::instance($instance->courseid);

        if ($users = get_enrolled_users($context, 'enrol/warwickauto:manage')) {
            $users = sort_by_roleassignment_authority($users, $context);
            $this->lasternoller = reset($users);
            unset($users);
        } else {
            $this->lasternoller = parent::get_enroller($instanceid);
        }

        $this->lasternollerinstanceid = $instanceid;

        return $this->lasternoller;
    }

    /**
     * Gets an array of the user enrolment actions.
     *
     * @param course_enrolment_manager $manager
     * @param stdClass $ue A user enrolment object
     * @return array An array of user_enrolment_actions
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        $actions = array();
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;
        if ($this->allow_unenrol($instance) && has_capability("enrol/warwickauto:unenrol", $context)) {
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('unenrol', 'enrol'), $url,
                array('class' => 'unenrollink', 'rel' => $ue->id));
        }
        if ($this->allow_manage($instance) && has_capability("enrol/warwickauto:manage", $context)) {
            $url = new moodle_url('/enrol/editenrolment.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/edit', ''), get_string('edit'), $url,
                array('class' => 'editenrollink', 'rel' => $ue->id));
        }
        return $actions;
    }

    /**
     * Restore instance and map settings.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $course
     * @param int $oldid
     */
    public function restore_instance(restore_enrolments_structure_step $step, stdClass $data, $course, $oldid) {
        global $DB;
        if ($step->get_task()->get_target() == backup::TARGET_NEW_COURSE) {
            $merge = false;
        } else {
            $merge = array(
                'courseid'   => $data->courseid,
                'enrol'      => $this->get_name(),
                'roleid'     => $data->roleid,
            );
        }
        if ($merge and $instances = $DB->get_records('enrol', $merge, 'id')) {
            $instance = reset($instances);
            $instanceid = $instance->id;
        } else {
            $instanceid = $this->add_instance($course, (array)$data);
        }
        $step->set_mapping('enrol', $oldid, $instanceid);
    }

    /**
     * Restore user enrolment.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $instance
     * @param int $oldinstancestatus
     * @param int $userid
     */
    public function restore_user_enrolment(restore_enrolments_structure_step $step, $data, $instance, $userid, $oldinstancestatus) {
        $this->enrol_user($instance, $userid, null, 0, 0, $data->status);
    }

    /**
     * Restore role assignment.
     *
     * @param stdClass $instance
     * @param int $roleid
     * @param int $userid
     * @param int $contextid
     */
    public function restore_role_assignment($instance, $roleid, $userid, $contextid) {
        role_assign($roleid, $userid, $contextid, 'enrol_'.$this->get_name(), $instance->id);
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/warwickauto:config', $context);
    }

     /**
      * Is it possible to delete enrol instance via standard UI?
      *
      * @param stdClass $instance
      * @return bool
      *
      * Added by A.Pawelczak 2018, adam.pawelczak@neokrates.pl
      *
      */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/warwickauto:config', $context);
    }

    /**
     * Returns true if the current user can add a new instance of enrolment plugin in course.
     * @param int $courseid
     * @return boolean
     */
    public function can_add_instance($courseid) {
        global $DB;

        $context = context_course::instance($courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/warwickauto:config', $context)) {
            return false;
        }

        return true;
    }

    /**
     *
     * @param stdClass $instance
     * @param array $data
     * @return bool
     */
    public function update_instance($instance, $data) {

        $instance->customtext1    = empty($data->customtext1) ? '' : $data->customtext1;
        $instance->customtext2    = empty($data->customtext2) ? '' : implode(',', array_keys($data->customtext2));
        $instance->customtext3    = null;
        $instance->customtext4    = null;

        if( !empty( $data->designations_add ) ){
            $designation = new \enrol_warwickauto\multiselect\designation('designations_add', [
                'plugin' => 'enrol_warwickauto',
                'enrol_instance' => $instance
            ]);

            $instance->customtext3 = $designation->valuesToAdd( $data->designations_add );
        }

        if( !empty( $data->departments_add ) ){

            $department = new \enrol_warwickauto\multiselect\department('departments_add', [
                'plugin' => 'enrol_warwickauto',
                'enrol_instance' => $instance
            ]);

            $instance->customtext4 = $department->valuesToAdd( $data->departments_add );
        }

        return parent::update_instance($instance, $data);
    }

}
