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
 * @package     enrol_warwickauto
 * @author      Eugene Venter <eugene@catalyst.net.nz>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('edit_form.php');

$courseid   = required_param('courseid', PARAM_INT);
$instanceid = optional_param('id', 0, PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

require_login($course);
require_capability('enrol/warwickauto:config', $context);

$PAGE->set_url('/enrol/warwickauto/edit.php', array('courseid' => $course->id, 'id' => $instanceid));
$PAGE->set_pagelayout('admin');
$PAGE->requires->jquery();

$return = new moodle_url('/enrol/instances.php', array('id' => $course->id));
if (!enrol_is_enabled('warwickauto')) {
    redirect($return);
}

/** @var enrol_warwickauto_plugin $plugin */
$plugin = enrol_get_plugin('warwickauto');

if ($instanceid) {
    $instance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'warwickauto', 'id' => $instanceid), '*', MUST_EXIST);

} else {
    require_capability('moodle/course:enrolconfig', $context);
    // No instance yet, we have to add new instance.
    navigation_node::override_active_url(new moodle_url('/enrol/instances.php', array('id' => $course->id)));

    $instance = (object)$plugin->get_instance_defaults();
    $instance->id       = null;
    $instance->courseid = $course->id;
    $instance->status   = ENROL_INSTANCE_ENABLED; // Do not use default for automatically created instances here.
}

$mform = new enrol_warwickauto_edit_form(null, array($instance, $plugin, $context));

if ($mform->is_cancelled()) {
    redirect($return);

} else if ($data = $mform->get_data()) {
    if ($instance->id) {
        $reset = ($instance->status != $data->status);

        $plugin->update_instance($instance, $data);

        if ($reset) {
            $context->mark_dirty();
        }

    } else {
        $fields = array(
            'status'          => $data->status,
            'name'            => $data->name,
            'customint2'      => $data->customint2,
            'customint3'      => $data->customint3,
            'customtext1'     => empty($data->customtext1) ? '' : $data->customtext1,
            'customtext2'     => empty($data->customtext2) ? '' : implode(',', array_keys($data->customtext2)),
            'customtext3'     => null,
            'customtext4'     => null,
            'roleid'          => $data->roleid
        );

        if( !empty( $data->designations_add ) ){
            $designation = new \enrol_warwickauto\multiselect\designation('designations_add', [
                'plugin' => 'enrol_warwickauto',
                'enrol_instance' => $instance
            ]);

            $fields[ 'customtext3' ] = $designation->valuesToAdd( $data->designations_add );
        }

        if( !empty( $data->departments_add ) ){
            $department = new \enrol_warwickauto\multiselect\department('departments_add', [
                'plugin' => 'enrol_warwickauto',
                'enrol_instance' => $instance
            ]);

            $fields[ 'customtext4' ] = $department->valuesToAdd( $data->departments_add );
        }

        $plugin->add_instance($course, $fields);
    }

    redirect($return);
}

$PAGE->set_heading($course->fullname);
$PAGE->set_title(get_string('pluginname', 'enrol_warwickauto'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'enrol_warwickauto'));
$mform->display();
echo $OUTPUT->footer();
