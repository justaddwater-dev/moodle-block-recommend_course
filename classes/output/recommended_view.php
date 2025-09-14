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
 * @package    block_recommend_course
 * @copyright  2025 Justaddwater <contact@justaddwater.in>
 * @author     Himanshu Saini
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_recommend_course\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use core_course\external\course_summary_exporter;

class recommended_view implements renderable, templatable {

    /**
     * The courses_view constructor.
     *
     * @param array $courses list of courses.
     * @param array $coursesprogress list of courses progress.
     */
    protected $myrecommendations;     
    public function __construct($myrecommendations) {
        $this->myrecommendations = $myrecommendations;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array
     */
   public function export_for_template(renderer_base $output) {
        global $CFG, $DB, $OUTPUT;
        require_once($CFG->dirroot.'/course/lib.php');
        require_once($CFG->libdir . '/filelib.php');
    
        // Build courses view data structure.
        $availableview = [];
    
        foreach ($this->myrecommendations as $mid => $recommendation) {
            // get the course display info.
            $course = $DB->get_record('course', ['id' => $recommendation->courseid], '*', IGNORE_MISSING);
            if (!$course) {
                // Skip if course not found.
                continue;
            }
    
            $coursecontext = \context_course::instance($course->id);
    
            try {
                $exporter = new \core_course\external\course_summary_exporter($course, ['context' => $coursecontext]);
                // Use $OUTPUT here (renderer) — exporter provides courseimage when possible.
                $exportedcourse = $exporter->export($OUTPUT);
            } catch (\Throwable $e) {
                debugging("Exporter failed for course {$course->id}, category {$course->category}: " . $e->getMessage(), DEBUG_DEVELOPER);
    
                // Minimal fallback object so template still has expected keys.
                $exportedcourse = (object)[
                    'id' => $course->id,
                    'fullname' => format_string($course->fullname),
                    'summary' => '',
                    'summaryformat' => FORMAT_HTML,
                    'viewurl' => (new \moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
                    // we'll compute image below
                ];
            }
    
            // Convert summary to plain text (safe).
            $coursesummary = !empty($exportedcourse->summary)
                ? content_to_text($exportedcourse->summary, $exportedcourse->summaryformat)
                : '';
    
            // ---------------------------
            // Robust image logic (replace the previous problematic call)
            // ---------------------------
            $imageurl = '';
    
            // 1) If exporter returned a courseimage, use it.
            if (!empty($exportedcourse->courseimage)) {
                $imageurl = $exportedcourse->courseimage;
            } else {
                // 2) Look for course overviewfiles (the proper Moodle filearea).
                $fs = get_file_storage();
                $files = $fs->get_area_files($coursecontext->id, 'course', 'overviewfiles', 0, 'sortorder ASC', false);
    
                foreach ($files as $file) {
                    if ($file->is_valid_image()) {
                        $imageurl = moodle_url::make_pluginfile_url(
                            $file->get_contextid(),
                            $file->get_component(),
                            $file->get_filearea(),
                            $file->get_itemid(),
                            $file->get_filepath(),
                            $file->get_filename()
                        )->out(false);
                        break;
                    }
                }
    
                // 3) Last-resort: Moodle's generated/abstract image
                if (empty($imageurl)) {
                    $imageurl = $OUTPUT->get_generated_image_for_id($course->id);
                }
            }
    
            // Attach final computed values to exportedcourse (keys your template expects).
            $exportedcourse->url = new \moodle_url('/course/view.php', ['id' => $recommendation->courseid]);
            $exportedcourse->image = $imageurl;
            $exportedcourse->summary = $coursesummary;
            $exportedcourse->sendername = $recommendation->firstname . ' ' . $recommendation->lastname;
    
            $availableview['courses'][] = $exportedcourse;
        }
    
        return $availableview;
    }

}
