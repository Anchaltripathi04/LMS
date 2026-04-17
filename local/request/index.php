<?php
require('../../config.php');
require_login();

global $DB, $USER, $OUTPUT, $PAGE;

$PAGE->set_url('/local/request/index.php');
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();
echo "<h2>Enrollment Requests</h2>";

// Only teacher/admin
if (!has_capability('moodle/course:update', context_system::instance())) {
    die("Access denied");
}

// Get teacher courses
$courseids = $DB->get_fieldset_sql("
    SELECT c.id
    FROM {course} c
    JOIN {context} ctx ON ctx.instanceid = c.id
    JOIN {role_assignments} ra ON ra.contextid = ctx.id
    WHERE ra.userid = ?
", [$USER->id]);

if (empty($courseids)) {
    echo "No courses assigned";
    echo $OUTPUT->footer();
    exit;
}

list($insql, $params) = $DB->get_in_or_equal($courseids);

// Fetch requests
$requests = $DB->get_records_sql("
    SELECT * FROM {course_requests}
    WHERE courseid $insql AND status = 'pending'
", $params);

// APPROVE / REJECT
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['users'])) {

    foreach ($_POST['users'] as $data) {

        list($userid, $courseid) = explode('-', $data);

        if (isset($_POST['approve'])) {

            $enrol = enrol_get_plugin('manual');
            $instances = enrol_get_instances($courseid, true);

            foreach ($instances as $instance) {
                if ($instance->enrol == 'manual') {
                    $enrol->enrol_user($instance, $userid);
                }
            }

            $DB->set_field('course_requests', 'status', 'approved', [
                'userid' => $userid,
                'courseid' => $courseid
            ]);
        }

        if (isset($_POST['reject'])) {

            $DB->set_field('course_requests', 'status', 'rejected', [
                'userid' => $userid,
                'courseid' => $courseid
            ]);
        }
    }

    echo "<p>Action Done ✅</p>";
}

// TABLE
echo '<form method="post">';
echo '<table border="1" cellpadding="10">';
echo '<tr>
<th><input type="checkbox" onclick="toggleAll(this)"></th>
<th>Student</th>
<th>Course</th>
</tr>';

foreach ($requests as $req) {

    $user = $DB->get_record('user', ['id' => $req->userid]);
    $course = $DB->get_record('course', ['id' => $req->courseid]);

    echo "<tr>
    <td><input type='checkbox' name='users[]' value='{$req->userid}-{$req->courseid}'></td>
    <td>{$user->firstname} {$user->lastname}</td>
    <td>{$course->fullname}</td>
    </tr>";
}

echo '</table><br>';

echo '
<button name="approve" style="background:green;color:white;padding:10px;">Approve</button>
<button name="reject" style="background:red;color:white;padding:10px;">Reject</button>
';

echo '</form>';

// Select all
echo '
<script>
function toggleAll(source) {
    let checkboxes = document.querySelectorAll("input[name=\'users[]\']");
    checkboxes.forEach(cb => cb.checked = source.checked);
}
</script>
';

echo $OUTPUT->footer();