<?php
require('../../config.php');
require_login();

// Context (system or course)
//$context = context_system::instance();
$courseid = required_param('courseid', PARAM_INT);
$course = get_course($courseid);
$context = context_course::instance($course->id);

// Capability check (VERY IMPORTANT)
require_capability('local/request:manage', $context);

// Page setup (this makes it follow Moodle theme automatically)
$PAGE->set_url('/local/request/manage.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Manage Requests');
$PAGE->set_heading('User Enrollment Requests');

// Output header
echo $OUTPUT->header();
echo $OUTPUT->heading('Pending Enrollment Requests');

// Handle actions (approve / delete)
$action = optional_param('action', '', PARAM_TEXT);
$userid = optional_param('userid', 0, PARAM_INT);

if ($action && $userid) {
    require_sesskey();

    if ($action == 'approve') {
        // Enrol user (example: manual enrol)
        $courseid = required_param('courseid', PARAM_INT);

        $enrol = enrol_get_plugin('manual');
        $instances = enrol_get_instances($courseid, true);

        foreach ($instances as $instance) {
            if ($instance->enrol == 'manual') {
                $enrol->enrol_user($instance, $userid, 5); // 5 = student role
            }
        }

        echo $OUTPUT->notification('User enrolled successfully', 'notifysuccess');
    }

    if ($action == 'delete') {
        delete_user($DB->get_record('user', ['id' => $userid]));
        echo $OUTPUT->notification('User deleted', 'notifyproblem');
    }
}

// Fetch pending users (custom table)
$requests = $DB->get_records('local_request', ['status' => 'pending']);

if ($requests) {

    echo html_writer::start_tag('table', ['class' => 'generaltable']);
    echo html_writer::start_tag('tr');
    echo html_writer::tag('th', 'User');
    echo html_writer::tag('th', 'Email');
    echo html_writer::tag('th', 'Actions');
    echo html_writer::end_tag('tr');

    foreach ($requests as $req) {
        $user = $DB->get_record('user', ['id' => $req->userid]);

        $approveurl = new moodle_url('/local/request/manage.php', [
            'action' => 'approve',
            'userid' => $user->id,
            'courseid' => $req->courseid,
            'sesskey' => sesskey()
        ]);

        $deleteurl = new moodle_url('/local/request/manage.php', [
            'action' => 'delete',
            'userid' => $user->id,
            'sesskey' => sesskey()
        ]);

        echo html_writer::start_tag('tr');
        echo html_writer::tag('td', fullname($user));
        echo html_writer::tag('td', $user->email);

        echo html_writer::tag('td',
            html_writer::link($approveurl, 'Approve', ['class' => 'btn btn-success']) . ' ' .
            html_writer::link($deleteurl, 'Delete', ['class' => 'btn btn-danger'])
        );

        echo html_writer::end_tag('tr');
    }

    echo html_writer::end_tag('table');

} else {
    echo $OUTPUT->notification('No pending requests', 'notifyinfo');
}

// Footer
echo $OUTPUT->footer();