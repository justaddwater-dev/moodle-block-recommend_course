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
 * Recommend a course page for the block_recommend_course plugin.
 *
 * @package    block_recommend_course
 * @copyright  2025 Justaddwater <contact@justaddwater.in>
 * @author     Himanshu Saini
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require_once('../../config.php');
 global $DB, $USER, $PAGE;
 
 require_login();
 $context = context_system::instance();
 $PAGE->set_context($context);
 $PAGE->set_url(new moodle_url('/blocks/recommend_course/history.php'));

 if (!has_capability('block/recommend_course:viewstats', $context)) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('nopermission', 'block_recommend_course'), 'error');
    echo '<a href="' . new moodle_url('/my/') . '" class="btn btn-primary">'. get_string('back_dashboard', 'block_recommend_course') . '</a>';
    echo $OUTPUT->footer();
    exit;
}
 // Set up page parameters
 $pluginname = get_string('pluginname', 'block_recommend_course');
 $title = get_string('historytitle', 'block_recommend_course');
 $PAGE->set_url(new moodle_url('/blocks/recommend_course/recommendations.php'));
 $PAGE->set_title($pluginname.' : '.$title);
 $PAGE->set_heading($pluginname);
 $PAGE->set_pagelayout('standard'); 
 
 // Include DataTables
 $PAGE->requires->jquery();
 $PAGE->requires->css('/blocks/recommend_course/css/style.css');
 $PAGE->requires->css('/blocks/recommend_course/css/datatables.min.css');
 $PAGE->requires->js_call_amd('block_recommend_course/init_datatable', 'DTinit', array('#recommended-table', array(
    'paging' => true,
    'searching' => true,
    'info' => true,
    'pageLength' => 25,
 )));
 echo $OUTPUT->header();
include('includes/_manage_nav.php');
 
 $sql = "SELECT * FROM {recommend_course_recommends}";
 $recommended_courses = $DB->get_records_sql($sql);
 
 if ($recommended_courses) {
     echo '<div class="table-wrapper">';
     echo '<table id="recommended-table" class="table table-bordered table-striped">';
     echo '<thead>
             <tr>
                <th>' . get_string('recommeded_by', 'block_recommend_course') . '</th>
                <th>' . get_string('recommendedto', 'block_recommend_course') . '</th>
                <th>' . get_string('course', 'block_recommend_course') . '</th>
                <th>' . get_string('recommendeddate', 'block_recommend_course') . '</th>
             </tr>
           </thead>';
     echo '<tbody>';
 
     $user_sql = "SELECT firstname, lastname FROM {user} WHERE id = :user_id";
     $course_sql = "SELECT fullname FROM {course} WHERE id = :course_id";
 
     foreach ($recommended_courses as $course) {
         $sender = $DB->get_record_sql($user_sql, ['user_id' => $course->sender_id]);
         $receiver = $DB->get_record_sql($user_sql, ['user_id' => $course->receiver_id]);
         $course_data = $DB->get_record_sql($course_sql, ['course_id' => $course->course_id]);
 
         $course_url = new moodle_url('/course/view.php', ['id' => $course->course_id]);
         $course_link = html_writer::link($course_url, $course_data->fullname);
 
         $timestamp = strtotime($course->created_on);
 
         echo '<tr>';
         echo '<td>' . $sender->firstname . ' ' . $sender->lastname . '</td>';
         echo '<td>' . $receiver->firstname . ' ' . $receiver->lastname . '</td>';
         echo '<td>' . $course_link . '</td>';
         echo '<td>' . userdate($timestamp, '%a, %d %b %Y, %H:%M') . '</td>';
         echo '</tr>';
     }
 
     echo '</tbody>';
     echo '</table>';
     echo '</div>';
 } else {
    echo '<p>' . get_string('nocoursesfound', 'block_recommend_course') . '</p>';
 }

echo $OUTPUT->footer(); 
