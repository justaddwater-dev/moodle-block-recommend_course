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
require_once("../../config.php");
require_once('recommendcourse_form.php');
global $DB, $USER;
require_login();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/blocks/recommend_course/recommend_course.php');
$title = get_string('title', 'block_recommend_course');
$mform = new recommendcourse_form();

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot);
} else if ($fromform = $mform->get_data()) {
    //adding data to db
    if (count($fromform->users) > 0 && $fromform->course) {

        foreach ($fromform->users as $receiver) {
            $temp = new stdClass();
            $temp->sender_id = $USER->id;
            $temp->receiver_id = $receiver;
            $temp->course_id = $fromform->course;
            $temp->created_on = date('Y-m-d H:i:s');
            $DB->insert_record('recommend_course_recommends', $temp);
        }

        redirect("$CFG->wwwroot/blocks/recommend_course/recommend_course.php", get_string('add_success', 'block_recommend_course'), null, \core\output\notification::NOTIFY_SUCCESS);
    } else
        redirect("$CFG->wwwroot/blocks/recommend_course/recommend_course.php", get_string('add_error', 'block_recommend_course'), null, \core\output\notification::NOTIFY_ERROR);
} else {

    $PAGE->set_pagelayout('incourse');
    $PAGE->set_title($title);
    $PAGE->set_heading($title);
    $PAGE->set_cacheable(true);


    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}