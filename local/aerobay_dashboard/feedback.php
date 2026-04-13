<?php
require_once(__DIR__ . '/../../config.php');
require_login();

global $DB, $USER, $PAGE, $OUTPUT;

// Page setup
$PAGE->set_url('/local/aerobay_dashboard/index.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Feedback Form');
$PAGE->set_heading('Feedback Form');

echo $OUTPUT->header();


// ✅ ROLE DETECTION (FINAL FIX)
$isAdmin = is_siteadmin($USER);
$isTeacher = !$isAdmin && has_capability('moodle/course:update', context_system::instance());
$isStudent = !$isAdmin && !$isTeacher;


// ✅ FORM SUBMIT (ONLY STUDENT)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isStudent) {

    require_sesskey(); // security

    $name = required_param('name', PARAM_TEXT);
    $message = required_param('message', PARAM_TEXT);

    $record = new stdClass();
    $record->userid = $USER->id;
    $record->name = $name;
    $record->message = $message;
    $record->timecreated = time();

    $DB->insert_record('aerobay_feedback', $record);

    echo "<div style='color:green;text-align:center;margin-top:20px;'>✅ Feedback submitted successfully!</div>";
}


// 🎨 COMMON CSS
echo '
<style>
.feedback-container {
    max-width: 600px;
    margin: 40px auto;
    padding: 25px;
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    font-family: Arial, sans-serif;
}

.feedback-container h3 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.feedback-input {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    transition: 0.3s;
}

.feedback-input:focus {
    border-color: #007BFF;
    outline: none;
    box-shadow: 0 0 5px rgba(0,123,255,0.3);
}

.feedback-btn {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #007BFF, #00C6FF);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

.feedback-btn:hover {
    background: linear-gradient(135deg, #0056b3, #0096c7);
}

.role-box {
    text-align:center;
    margin-top:20px;
    font-weight:bold;
    font-size:18px;
}
</style>
';


// 👨‍🎓 STUDENT VIEW → SHOW FORM ONLY
if ($isStudent) {

    echo '<div class="role-box">Your Feedback is very helpful for us!</div>';

    echo '
    <div class="feedback-container">
        <h3>Submit Your Feedback</h3>

        <form method="POST">
            <input type="hidden" name="sesskey" value="'.sesskey().'">

            <input type="text" name="name" placeholder="Your Name" required class="feedback-input">

            <textarea name="message" rows="4" placeholder="Your Message" required class="feedback-input"></textarea>

            <button type="submit" name="submit" value="1" class="feedback-btn">
                Submit Feedback
            </button>
        </form>
    </div>
    ';
}


//  TEACHER + 🛠 ADMIN VIEW → SHOW FEEDBACK ONLY
if ($isTeacher || $isAdmin) {

    echo '<div class="role-box">';
    
    if ($isAdmin) {
        echo ' Admin Dashboard';
    } else {
        echo 'Teacher Dashboard';
    }

    echo '</div>';

    $records = $DB->get_records('aerobay_feedback');

    echo "<div style='max-width:600px;margin:30px auto;'>";
    echo "<h3 style='text-align:center;'> Student Feedback</h3>";

    if ($records) {
        foreach ($records as $r) {
            echo "
            <div style='background:#fff;padding:15px;margin-bottom:15px;border-radius:12px;
            box-shadow:0 4px 15px rgba(0,0,0,0.08);'>

                <div style='font-weight:bold;color:#007BFF;font-size:16px;'>
                    👤 ".format_string($r->name)."
                </div>

                <div style='margin-top:8px;color:#555;font-size:14px;'>
                    ".format_text($r->message, FORMAT_HTML)."
                </div>

                <div style='margin-top:10px;font-size:12px;color:#999;'>
                    🕒 ".date('d M Y, h:i A', $r->timecreated)."
                </div>
            </div>
            ";
        }
    } else {
        echo "<p style='text-align:center;color:#777;'>No feedback yet.</p>";
    }

    echo "</div>";
}

echo $OUTPUT->footer();