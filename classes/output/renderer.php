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
 * Recommend a Course block renderer
 *
 * @package    block_recommend_course
 * @copyright  2025 Justaddwater <contact@justaddwater.in>
 * @author     Himanshu Saini
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_recommend_course\output;
defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;
class renderer extends plugin_renderer_base {

    /**
     * Return the main content for the Available Courses block.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_recentcourses(main $main) {
        return $this->render_from_template('block_recommend_course/main', $main->export_for_template($this));
    }
}
