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
 * Block definition class for the block_recommend_course plugin.
 *
 * @package    block_recommend_course
 * @copyright  2025 Justaddwater <contact@justaddwater.in>
 * @author     Himanshu Saini
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');
require_login();
$context = context_system::instance();

$PAGE->set_url(new moodle_url('/blocks/recommend_course/all.php'));
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('pluginname', 'block_recommend_course'));
$PAGE->set_heading(get_string('pluginname', 'block_recommend_course'));

global $DB, $USER;

// Fetch all recommendations for user.
$sql = "SELECT rec.id as rec_id, rec.sender_id,
               sender.firstname, sender.lastname,
               rec.receiver_id, rec.created_on,
               c.id as courseid, c.fullname, c.shortname
          FROM {recommend_course_recommends} rec
          JOIN {course} c ON rec.course_id = c.id
          JOIN {user} sender ON sender.id = rec.sender_id
         WHERE rec.receiver_id = :userid
      ORDER BY rec.created_on DESC";

$records = $DB->get_records_sql($sql, ['userid' => $USER->id]);
 // Include DataTables
 $PAGE->requires->jquery();
 $PAGE->requires->css('/blocks/recommend_course/css/style.css');
 $PAGE->requires->css('/blocks/recommend_course/css/datatables.min.css');
 $PAGE->requires->js_call_amd('block_recommend_course/init_datatable', 'DTinit', array('#recommend-courses-table', array(
    'paging' => true,
    'searching' => true,
    'info' => true,
    'pageLength' => 25,
 )));
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('all_recommendation', 'block_recommend_course'));

if ($records) {
    $table = new html_table();
    $table->id = 'recommend-courses-table';
    $table->head = [get_string('course'), get_string('recommeded_by', 'block_recommend_course'), get_string('time')];
    foreach ($records as $rec) {
        $courseurl = new moodle_url('/course/view.php', ['id' => $rec->courseid]);
        $senderurl = new moodle_url('/user/profile.php', ['id' => $rec->sender_id]);
        $row = [
            html_writer::link($courseurl, format_string($rec->fullname)),
            html_writer::link($senderurl, fullname($rec)),
            userdate($rec->created_on)
        ];
        $table->data[] = $row;
    }
    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('nobottomcourses', 'block_recommend_course'), 'notifymessage');
}
// Add "Recommend a Course" button at bottom
$recommendurl = new moodle_url('/blocks/recommend_course/recommend_course.php');
echo html_writer::div(
    html_writer::link(
        $recommendurl,
        get_string('button', 'block_recommend_course'),
        ['class' => 'btn btn-primary mt-3']
    ),
    'text-center mt-4'
);
echo $OUTPUT->footer();
