<?php
require('../../config.php');
require_login();

global $DB, $PAGE, $OUTPUT;

// Get course id
$courseid = optional_param('courseid', 0, PARAM_INT);

// Course context
$context = context_course::instance($courseid);

// Allow Admin OR Course Teacher
if (!is_siteadmin() && !has_capability('moodle/course:update', $context)) {
    die("Access denied - Only Admin or Teacher allowed");
}

// Page header
echo $OUTPUT->header();

// Heading
echo "<h2 style='color:#333;'>📚 Enrollment Requests</h2>";

// Approve logic
if (isset($_POST['approve'])) {
    if (!empty($_POST['users'])) {

        foreach ($_POST['users'] as $userid) {

            // Enrol user
            $enrol = enrol_get_plugin('manual');
            $instances = enrol_get_instances($courseid, true);

            foreach ($instances as $instance) {
                if ($instance->enrol == 'manual') {
                    $enrol->enrol_user($instance, $userid);
                }
            }

            // Update status
            $DB->set_field('course_requests', 'status', 'approved', [
                'userid' => $userid,
                'courseid' => $courseid
            ]);
        }

        echo "<p style='color:green;'>✅ Users enrolled successfully!</p>";
    }
}

// Get pending requests
$requests = $DB->get_records('course_requests', [
    'status' => 'pending',
    'courseid' => $courseid
]);

// Form start
echo '<form method="post">';
echo '<table border="1" cellpadding="10">';
echo '<tr><th>Select</th><th>Student Name</th></tr>';

// Loop requests
foreach ($requests as $req) {

    $user = $DB->get_record('user', ['id' => $req->userid]);

    echo "<tr>
        <td><input type='checkbox' name='users[]' value='{$req->userid}'></td>
        <td>" . fullname($user) . "</td>
    </tr>";
}

echo '</table>';
echo '<br>';

// Hidden course id
echo '<input type="hidden" name="courseid" value="'.$courseid.'">';

// Button
echo '<button type="submit" name="approve" 
style="padding:10px 15px; background:#28a745; color:white; border:none; border-radius:6px;">
Approve Selected
</button>';

echo '</form>';

// Footer
echo $OUTPUT->footer();
?>