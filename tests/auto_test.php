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
 * Auto enrolment plugin tests.
 *
 * @package     enrol_warwickauto
 * @autor       Eugene Venter <eugene@catalyst.net.nz>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/enrol/warwickauto/lib.php');

class enrol_warwickauto_testcase extends advanced_testcase {

    public function test_basics() {
        // disabled by default
        $this->assertFalse(enrol_is_enabled('warwickauto'));

        // correct enrol instance
        $plugin = enrol_get_plugin('warwickauto');
        $this->assertInstanceOf('enrol_warwickauto_plugin', $plugin);

        // default config checks
        $this->assertEquals('1', get_config('enrol_warwickauto', 'defaultenrol'));
        $this->assertEquals('1', get_config('enrol_warwickauto', 'status'));
        $this->assertEquals(ENROL_WARWICKAUTO_COURSE_VIEWED, get_config('enrol_warwickauto', 'enrolon'));
        $this->assertEquals('1', get_config('enrol_warwickauto', 'sendcoursewelcomemessage'));
        $this->assertEquals('', get_config('enrol_warwickauto', 'modviewmods'));
    }
}
