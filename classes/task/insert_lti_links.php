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
 * Insert mod_lti instance into courses in the selected category.
 *
 * @package block_panopto
 * @author Leon Stringer <leon.stringer@ucl.ac.uk>
 * @copyright  2025 onwards UCL {@link https://www.ucl.ac.uk/}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_panopto\task;

require_once(dirname(__FILE__) . '/../../lib/lti/panoptoblock_lti_utility.php');

/**
 * Insert mod_lti instance into courses in the selected category.
 */
class insert_lti_links extends \core\task\adhoc_task {

    /**
     * The main execution function of the class
     */
    public function execute() {
        global $DB;

        $data = (array) $this->get_custom_data();
        $category = $data['categoryid'];

        // Array of the selected category and its subcategories.
        $categories = \core_course_category::get($category)->get_all_children_ids();
        array_unshift($categories, $category);

        // Array of courses which already have a matching mod_lti instance.
        $sql = "SELECT cm.course
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module
                  JOIN {lti} lti ON cm.instance = lti.id
                  JOIN {course_sections} cs ON cm.section = cs.id 
                 WHERE m.name = 'lti' AND cs.section = 0 AND lti.name = :name";
        $courseswithtool = $DB->get_fieldset_sql($sql, ['name' => get_string('panopto_course_tool', 'block_panopto')]);

        foreach ($DB->get_records('course') as $course) {
            // If the course is in selected category or one its subcategories,
            // and it doesn't already have the mod_lti instance.
            if (in_array($course->category, $categories)
                    && !in_array($course->id, $courseswithtool)) {
                $tool = \panoptoblock_lti_utility::get_course_tool($course->id);

                if (!empty($tool)) {
                    \panoptoblock_lti_utility::insert_course_tool($course->id,
                            $tool);
                    mtrace("Inserted LTI tool into course with ID $course->id");
                }
            }
        }
    }

    /**
     * Used to indicate if the task should be re-run if it fails.
     *
     * @return bool true if the task should be retried until it succeeds, false otherwise.
     */
    public function retry_until_success(): bool {
        return false;
    }
}
