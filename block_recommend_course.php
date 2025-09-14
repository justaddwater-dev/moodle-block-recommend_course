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
class block_recommend_course extends block_base
{

    public function init() {
        $this->title = get_string('blocktitle', 'block_recommend_course');
    }

    public function get_content() {
        global $OUTPUT, $CFG, $DB, $USER;

        // Detect sidebar vs content region.
        $is_sidebar = in_array($this->instance->region, ['side-pre', 'side-post']);
        $renderable = new block_recommend_course\output\main($is_sidebar);
        $renderer = $this->page->get_renderer('block_recommend_course');
        
        $this->content = new stdClass();
        $this->content->text = $renderer->render($renderable);
        
        return $this->content;
        
    }
    

    /**
     * Defines in which pages this block can be added.
     *
     * @return array of the pages where the block can be added.
     */
    public function applicable_formats()
    {
        return [
            'admin' => false,
            'site-index' => true,
            'course-view' => true,
            'mod' => true,
            'my' => true,
        ];
    }
}