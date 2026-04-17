<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A drawer based layout for the moove theme.
 *
 * @package    theme_moove
 * @copyright  2022 Willian Mano {@link https://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
user_preference_allow_ajax_update('drawer-open-index', PARAM_BOOL);
user_preference_allow_ajax_update('drawer-open-block', PARAM_BOOL);

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING')) {
    $blockdraweropen = true;
}

$extraclasses = ['uses-drawers'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}

$themesettings = new \theme_moove\util\settings();

if (!$themesettings->enablecourseindex) {
    $courseindex = '';
} else {
    $courseindex = core_course_drawer();
}

if (!$courseindex) {
    $courseindexopen = false;
}

$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $secondary = $PAGE->secondarynav;

    if ($secondary->get_children_key_list()) {
        $tablistnav = $PAGE->has_tablist_secondary_navigation();
        $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
        $secondarynavigation = $moremenu->export_for_template($OUTPUT);
        $extraclasses[] = 'has-secondarynavigation';
    }

    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);
//for teacher course box
$isAdmin = is_siteadmin($USER);
$isTeacher = !$isAdmin && has_capability('moodle/course:update', context_system::instance());

if ($isAdmin || $isTeacher) {
    $extraclasses[] = 'no-progress';
}

if (!$isAdmin && $isTeacher) {
    $extraclasses[] = 'teacher-role';
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);

$isAdmin = is_siteadmin($USER);
$isTeacher = has_capability('moodle/course:update', context_system::instance());

$templatecontext['isteacher'] = (!$isAdmin && $isTeacher);

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
    'enablecourseindex' => $themesettings->enablecourseindex
];

$templatecontext = array_merge($templatecontext, $themesettings->footer());

echo $OUTPUT->render_from_template('theme_moove/drawers', $templatecontext);


if (!isset($_SESSION)) {
    session_start();
}

global $USER;

// Ensure popup only on dashboard
if (
    basename($_SERVER['PHP_SELF']) == 'index.php' &&
    strpos($_SERVER['REQUEST_URI'], '/my/') !== false &&
    !isset($_SESSION['popup_shown'])
) 

    $username = fullname($USER);
    $coursecount = count(enrol_get_users_courses($USER->id));

    // ROLE DETECTION
    $isTeacher = has_capability('moodle/course:update', context_system::instance());
    $isStudent = !$isTeacher;

    // DIFFERENT MESSAGE FOR STUDENT / TEACHER
   // CORRECT ROLE DETECTION
$isAdmin = is_siteadmin($USER);

// Teacher = has capability BUT not admin
$isTeacher = !$isAdmin && has_capability('moodle/course:update', context_system::instance());

// Student = neither admin nor teacher
$isStudent = !$isAdmin && !$isTeacher;

global $USER;

// check if popup already shown permanently
$popupshown = get_user_preferences('popup_shown', 0, $USER);

// ensure only on dashboard + first time ever
if (
    basename($_SERVER['PHP_SELF']) == 'index.php' &&
    strpos($_SERVER['REQUEST_URI'], '/my/') !== false &&
    !$popupshown
) {


// MESSAGE LOGIC
if ($isAdmin) {

    $roleMessage = "You are logged in as Admin";

    $extraAction = '<a href="/admin/index.php" style="
        padding:10px 15px;
        background:#e91e63;
        color:white;
        text-decoration:none;
        border-radius:6px;
        margin-right:8px;">
        Admin Panel
    </a>';

} else if ($isTeacher) {

    $roleMessage = "You are logged in as Teacher ";

    $extraAction = '<a href="/course/management.php" style="
        padding:10px 15px;
        background:#ff9800;
        color:white;
        text-decoration:none;
        border-radius:6px;
        margin-right:8px;">
        Manage Courses
    </a>';

} else {

    $roleMessage = ($coursecount > 0)
        ? "You are enrolled in ".$coursecount." courses "
        : "You are not enrolled in any course yet 😢";

    $extraAction = '<a href="/my/courses.php" style="
        padding:10px 15px;
        background:#28a745;
        color:white;
        text-decoration:none;
        border-radius:6px;
        margin-right:8px;">
        My Courses
    </a>';
}

    $popup = '
    <script>
    document.addEventListener("DOMContentLoaded", function() {

        setTimeout(function() {

            let popup = document.createElement("div");

            popup.innerHTML = `
            <div id="welcomePopup" style="
                position:fixed;
                top:0;
                left:0;
                width:100%;
                height:100%;
                background:rgba(0,0,0,0.7);
                display:flex;
                justify-content:center;
                align-items:center;
                z-index:9999;">

                <div style="
                    background:white;
                    padding:30px;
                    border-radius:20px;
                    width:340px;
                    text-align:center;">

                    <h2>Welcome '.$username.' 🎉</h2>

                    <p style="font-size:15px;color:#555;">
                        '.$roleMessage.'
                    </p>

                    <div style="margin-top:20px;">
                        '.$extraAction.'

                        <button onclick="closePopup()" style="
                            padding:10px 15px;
                            background:#007BFF;
                            color:white;
                            border:none;
                            border-radius:6px;">
                            Continue
                        </button>
                    </div>
                </div>
            </div>
            `;

            document.body.appendChild(popup);

        }, 400);

    });

    function closePopup() {
        let el = document.getElementById("welcomePopup");
        if (el) el.remove();
    }
    </script>
    ';

    echo $popup;

    set_user_preference('popup_shown', 1, $USER);
}
if ($quiz_not_attempted) {
    echo "<script>alert('Test is not submitted yet. Please complete your test.');</script>";
}

?>
