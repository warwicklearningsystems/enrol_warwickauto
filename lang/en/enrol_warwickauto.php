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
 * Strings for component 'enrol_warwickauto', language 'en'.
 *
 * @package     enrol_warwickauto
 * @author      Eugene Venter <eugene@catalyst.net.nz>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['customwelcomemessage'] = 'Custom welcome message';
$string['customwelcomemessage_help'] = 'A custom welcome message may be added as plain text or Moodle-auto format, including HTML tags and multi-lang tags.

The following placeholders may be included in the message:

* Course name {$a->coursename}
* Link to user\'s profile page {$a->profileurl}';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during Warwick Auto enrolment.';
$string['editenrolment'] = 'Edit enrolment';
$string['enrolon'] = 'Enrol on';
$string['enrolon_help'] = 'Choose the event that should trigger Warwick Auto enrolment.

**Course view** - Enrol a user upon course view.<br>

**User login** - Enrol users as soon as they log in.<br>

**Course activity/resource view** - Enrol a user when one of the selected activities/resources is viewed.<br>
*NOTE:* this option requires a Guest access enrol instance. ';
$string['enrolon_desc'] = 'Event which will trigger an Warwick Auto enrolment.';
$string['courseview'] = 'Course view';
$string['modview'] = 'Course activity/resource view';
$string['modviewmods'] = 'Activities/resources';
$string['modviewmods_desc'] = 'Viewing any of the selected resources/activities will trigger an Warwick Auto enrolment.';
$string['pluginname'] = 'Warwick Auto enrolment';
$string['pluginname_desc'] = 'The Warwick Auto enrolment plugin automatically enrols users upon course/activity/resource view.';
$string['privacy:metadata'] = 'The Warwick Auto enrolment plugin does not store any personal data.';
$string['requirepassword'] = 'Require enrolment key';
$string['requirepassword_desc'] = 'Require enrolment key in new courses and prevent removing of enrolment key from existing courses.';
$string['role'] = 'Default assigned role';
$string['warwickauto:config'] = 'Configure auto enrol instances';
$string['warwickauto:manage'] = 'Manage enrolled users';
$string['warwickauto:unenrol'] = 'Unenrol users from course';
$string['warwickauto:unenrolself'] = 'Unenrol self from the course';
$string['warwickauto:nonsiteadminconfig'] = 'Non-site admin management of auto-enrolment config';
$string['sendcoursewelcomemessage'] = 'Send course welcome message';
$string['sendcoursewelcomemessage_help'] = 'If enabled, users receive a welcome message via email when they get auto enrolled.';
$string['status'] = 'Allow Warwick Auto enrolments';
$string['status_desc'] = 'Allow Warwick Auto enrolments of users into course by default.';
$string['status_help'] = 'This setting determines whether this auto enrol plugin is enabled for this course.';
$string['unenrol'] = 'Unenrol user';
$string['unenroluser'] = 'Do you really want to unenrol "{$a->user}" from course "{$a->course}"?';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';
$string['userlogin'] = 'User login';
$string['welcometocourse'] = 'Welcome to {$a}';
$string['welcometocoursetext'] = 'Welcome to {$a->coursename}!

If you have not done so already, you should edit your profile page so that we can learn more about you:

{$a->profileurl}';
