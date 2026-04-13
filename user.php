<?php
require_once(__DIR__ . '/config.php');
require_login(); // Ensure user is logged in

global $DB;

// Get all users (limit for safety)
$users = $DB->get_records('user', null, '', 'id, firstname, lastname, email');

echo "<h2>User List</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
      </tr>";

foreach ($users as $user) {
    echo "<tr>";
    echo "<td>{$user->id}</td>";
    echo "<td>{$user->firstname}</td>";
    echo "<td>{$user->lastname}</td>";
    echo "<td>{$user->email}</td>";
    echo "</tr>";
}

echo "</table>";
?>