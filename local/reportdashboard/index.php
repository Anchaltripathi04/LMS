<?php
require('../../config.php');
require_login();

$courseid = required_param('courseid', PARAM_INT);

$context = context_course::instance($courseid);

// Allow teacher/admin
if (!is_siteadmin() && !has_capability('moodle/course:update', $context)) {
    die("Access denied");
}

echo $OUTPUT->header();
echo "<h2>Course Report Dashboard</h2>";

// Get users
$users = get_enrolled_users($context);

// Get quizzes
$quizzes = $DB->get_records('quiz', ['course' => $courseid]);

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Student</th>";

foreach ($quizzes as $quiz) {
    echo "<th>{$quiz->name}</th>";
}
echo "</tr>";

foreach ($users as $user) {

    echo "<tr>";
    echo "<td>".fullname($user)."</td>";

    foreach ($quizzes as $quiz) {

        $grade = $DB->get_record('quiz_grades', [
            'userid' => $user->id,
            'quiz' => $quiz->id
        ]);

        echo "<td>";
        echo $grade ? $grade->grade : "Not Attempted";
        echo "</td>";
    }

    echo "</tr>";
}

echo "</table>";

echo $OUTPUT->footer();