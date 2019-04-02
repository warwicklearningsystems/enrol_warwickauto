Auto enrolment plugin for Moodle (http://moodle.org/)
=====================================================

[![Build Status](https://travis-ci.org/bynare/moodle-enrol_warwickauto.svg?branch=master)](https://travis-ci.org/bynare/moodle-enrol_warwickauto)

Ever wanted to simplify the enrolment process for some of your courses by just auto enrolling users, based on actions they take within the system? This plugin helps you out with this, as you're able to configure which user actions should trigger a course enrolment for a user :)

Auto enrolment can be configured for the following scenarios:

* Auto enrolment upon course view

* Auto enrolment on login (new in 2.8)

* Auto enrolment upon activity/activities view

The plugin also allows you to configure a welcome message to be sent to the user upon enrolment.

# [Warwick Auto enrolment]

The _Warwick Auto enrolment_ plugin is an extension of the _Auto enrolment_ plugin as shown above. It adds two further restrictions, department and designation. It allows for the administration of a definitive list of departments/designations from which to restrict user auto enrolment based on those user properties, e.g. Joe Bloggs is in the 'Law School' department and has the 'lecturer' designation. The _Warwick Auto enrolment_ plugin has been configured against a course to only allow the 'Economics' department. Joe Bloggs will not be automatically enrolled on to that particular course.

## Configuration
Configuration of the plugin is done in two stages, _settings_ and _course_. Both the department and designation fields are free-text fields stored against the user record, which has inevitably resulted in an unwieldy list. Therefore, in order to present a more contained list to users that will configure the plugin against a course, the settings page presents an opportunity to tame these lists.

#### Settings

To complete the first stage of the configuration of the plugin go to _Site Administration > Plugins > Enrolments > Warwick Auto enrolment._

Simply select _Designation Settings_ or _Department Settings_. Both pages operate in exactly the same manor. You will be presented with a multiselect which displays all the available designations/departments on the right-hand side (_available list_) of the element. Select the required item(s) and click 'Add'. The selected values will now be displayed on the left-hand side of the element, and will be presented in the _available list_ of the _course_ configuration of the plugin for the same options.

To remove items, simply select and click 'Remove'. _Note: although this will remove the value(s) from the appropriate available list of the course config, it does not perform a deep removal from courses that may already contain the value(s) in their config for this plugin. In those instances the value will still be available until it is removed from the course config._

#### Course

To the final stage of the configuration go to _Dashboard > My courses > {course} > Users > Enrolment methods > Warwick Auto Enrolment._

The Designation and Department elements are presented directly on this form and can be configured in exactly the same way as described above.
