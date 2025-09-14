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
 * Recommed a course form class for the block_recommend_course plugin.
 *
 * @package    block_recommend_course
 * @copyright  2025 Justaddwater <contact@justaddwater.in>
 * @author     Himanshu Saini
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("$CFG->libdir/formslib.php");
class recommendcourse_form extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $CFG, $DB, $USER;

        $mform = $this->_form;

        //add course dropdown
        $courses = $DB->get_records_sql("
            SELECT id, fullname, visible 
            FROM {course} 
            WHERE visible = 1 AND id != 1
            ORDER BY fullname ASC
        ");

        $coursenames = array();
        foreach ($courses as $course) {
            $coursenames[$course->id] = $course->fullname;
        }
        $select = $mform->addElement('select', 'course', get_string('select_course', 'block_recommend_course'), $coursenames);

        //add user multi select
        $sql = "SELECT id, firstname, lastname, username FROM {user} WHERE deleted = 0 AND suspended = 0 AND id <> ? ORDER BY firstname ASC";
        $users = $DB->get_records_sql($sql, [$USER->id]);

        $usernames = array();
        foreach ($users as $user) {
            //if($this->check_user_can_view_profile($user)) 
            $usernames[$user->id] = $user->firstname . ' ' . $user->lastname . ' (' . $user->username . ')';
        }
        $options = array(
            'multiple' => true,
            'noselectionstring' => get_string('noselection_string', 'block_recommend_course')
        );
        $mform->addElement('autocomplete', 'users', get_string('select_users', 'block_recommend_course'), $usernames, $options);

        $this->add_action_buttons(true, 'Submit');
    }
    
    
    private function check_user_can_view_profile($targetuser, $viewer = null) {
        global $USER;
    
        if (is_null($viewer)) {
            $viewer = $USER;
        }
    
        if (!empty($targetuser->deleted)) {
            return false;
        }
    
        $context = context_user::instance($targetuser->id);
        return has_capability('moodle/user:viewdetails', $context, $viewer);
    }
    
}