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
 * Course recommendation statistics page for block_recommend_course plugin.
 *
 * This page displays statistics for the most and least recommended courses.
 * It fetches data from the database and presents it in a structured format.
 *
 * @package    block_recommend_course
 * @copyright  2025 Justaddwater <contact@justaddwater.in>
 * @author     Himanshu Saini
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

global $DB, $USER, $PAGE, $OUTPUT;

// Ensure the user is logged in
require_login();

// Set the context to system level
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/blocks/recommend_course/stats_table.php'));

// Check if the user has permission to view statistics
if (!has_capability('block/recommend_course:viewstats', $context)) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('nopermission', 'block_recommend_course'), 'error');
    echo '<a href="' . new moodle_url('/my/') . '" class="btn btn-primary">'. get_string('back_dashboard', 'block_recommend_course') . '</a>';
    echo $OUTPUT->footer();
    exit;
}

// Set up page properties
$pluginname = get_string('pluginname', 'block_recommend_course');
$title = get_string('course_recommendations_stats', 'block_recommend_course');
$PAGE->set_url(new moodle_url('/blocks/recommend_course/stats_table.php'));
$PAGE->set_title($pluginname.' : '.$title);
$PAGE->set_heading($pluginname);
$PAGE->set_pagelayout('standard'); // Uses Moodle’s standard UI layout
$PAGE->requires->css('/blocks/recommend_course/css/style.css');

// Fetch the top 5 most recommended courses
$top_sql = "SELECT course_id, COUNT(*) AS recommendation_count 
            FROM {recommend_course_recommends} 
            GROUP BY course_id 
            ORDER BY recommendation_count DESC 
            LIMIT 5";
$top_recommended_courses = $DB->get_records_sql($top_sql);

// Fetch the bottom 5 least recommended courses
$bottom_sql = "SELECT course_id, COUNT(*) AS recommendation_count 
               FROM {recommend_course_recommends} 
               GROUP BY course_id 
               ORDER BY recommendation_count ASC 
               LIMIT 5";
$bottom_recommended_courses = $DB->get_records_sql($bottom_sql);

// Output page header
echo $OUTPUT->header();
include('includes/_manage_nav.php');
echo html_writer::start_tag('div', ['class' => 'block_recommend_course-row']);
// ===================== TOP 5 RECOMMENDED COURSES =====================
echo html_writer::start_tag('div', ['class' => 'block_recommend_course-col-50']);
echo $OUTPUT->heading(get_string('mostrecommended', 'block_recommend_course'), 4);

if ($top_recommended_courses) {
    $table = new html_table();
    $table->head = [
        get_string('course', 'block_recommend_course'),
        get_string('totalrecommendations', 'block_recommend_course')
    ];
    $count = 1;

    foreach ($top_recommended_courses as $course) {
        // Fetch course details securely
        $course_data = $DB->get_record('course', ['id' => $course->course_id], 'fullname');

        // Generate course link (if course exists)
        $course_link = $course_data 
            ? html_writer::link(new moodle_url('/course/view.php', ['id' => $course->course_id]), $course_data->fullname)
            : get_string('unknowncourse', 'block_recommend_course');

        // Add data to table
        $table->data[] = [$course_link, $course->recommendation_count];
    }

    // Output the table
    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('notopcourses', 'block_recommend_course'), 'info');
}

echo html_writer::end_tag('div'); // End top recommended courses section

// ===================== BOTTOM 5 RECOMMENDED COURSES =====================
echo html_writer::start_tag('div', ['class' => 'block_recommend_course-col-50']);
echo $OUTPUT->heading(get_string('leastrecommended', 'block_recommend_course'), 4);

if ($bottom_recommended_courses) {
    $table = new html_table();
    $table->head = [
        get_string('course', 'block_recommend_course'),
        get_string('totalrecommendations', 'block_recommend_course')
    ];    
    $count = 1;

    foreach ($bottom_recommended_courses as $course) {
        // Fetch course details securely
        $course_data = $DB->get_record('course', ['id' => $course->course_id], 'fullname');

        // Generate course link (if course exists)
        $course_link = $course_data 
            ? html_writer::link(new moodle_url('/course/view.php', ['id' => $course->course_id]), $course_data->fullname)
            : get_string('unknowncourse', 'block_recommend_course');

        // Add data to table
        $table->data[] = [$course_link, $course->recommendation_count];
    }

    // Output the table
    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('nobottomcourses', 'block_recommend_course'), 'info');
}

echo html_writer::end_tag('div'); 
echo html_writer::end_tag('div'); 

// Output page footer
echo $OUTPUT->footer();
