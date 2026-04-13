<?php
require_once(__DIR__ . '/../../config.php');
require_login();

global $USER, $PAGE, $OUTPUT;

// Page setup
$PAGE->set_url('/local/aerobay_dashboard/index.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Aerobay Dashboard');
$PAGE->set_heading('Aerobay Dashboard');

echo $OUTPUT->header();

// User Info Card UI
echo "<div style='max-width:500px;margin:20px auto;padding:20px;background:#f8f9fa;border-radius:15px;box-shadow:0 5px 15px rgba(0,0,0,0.1);'>
        <h2 style='text-align:center;'>👋 Welcome, ".fullname($USER)."</h2>
        
        <hr>

        <p><strong>👤 Username:</strong> {$USER->username}</p>
        <p><strong>📧 Email:</strong> {$USER->email}</p>
        <p><strong>🆔 User ID:</strong> {$USER->id}</p>
      </div>";

echo $OUTPUT->footer();

