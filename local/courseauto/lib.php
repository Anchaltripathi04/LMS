<?php

defined('MOODLE_INTERNAL') || die();

function local_courseauto_course_created($event) {
    global $DB;

    $courseid = $event->objectid;
    $userid = $event->userid;

    // Get manual enrol plugin
    $enrol = enrol_get_plugin('manual');
    $instances = enrol_get_instances($courseid, true);

    foreach ($instances as $instance) {
        if ($instance->enrol == 'manual') {

            // Check if already enrolled
            $context = context_course::instance($courseid);

            if (!is_enrolled($context, $userid)) {

                // Role ID 3 = Teacher (default)
                $enrol->enrol_user($instance, $userid, 3);
            }
        }
    }
}