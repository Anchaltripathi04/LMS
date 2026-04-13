<?php
require_once(__DIR__ . '/../../config.php');
require_login();

global $DB, $PAGE, $OUTPUT;

// Page setup
$PAGE->set_url('/local/myusers/index.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('User List');
$PAGE->set_heading('User List');

echo $OUTPUT->header();

// Fetch users
$users = $DB->get_records('user', null, '', 'id, firstname, lastname, email');

// Display
echo "<h2>All Users</h2>";
echo "<table class='table table-bordered'>";
echo "<tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
      </tr>";

foreach ($users as $user) {
    echo "<tr>
            <td>{$user->id}</td>
            <td>{$user->firstname}</td>
            <td>{$user->lastname}</td>
            <td>{$user->email}</td>
          </tr>";
}

echo "</table>";

echo $OUTPUT->footer();