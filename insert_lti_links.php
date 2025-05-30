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
 * Select a category and insert the LTI tool into that category's courses.
 *
 * @package block_panopto
 * @author Leon Stringer <leon.stringer@ucl.ac.uk>
 * @copyright  2025 onwards UCL {@link https://www.ucl.ac.uk/}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once(dirname(__FILE__) . '/classes/panopto_insert_lti_links_form.php');

require_login();
$context = context_system::instance();
$PAGE->set_context($context);
require_capability('block/panopto:provision_multiple', $context);
$returnurl = optional_param('return_url', $CFG->wwwroot . '/admin/settings.php?section=blocksettingpanopto', PARAM_LOCALURL);

$urlparams['return_url'] = $returnurl;

$PAGE->set_url('/blocks/panopto/insert_lti_links.php', $urlparams);
$PAGE->set_pagelayout('base');
$mform = new panopto_insert_lti_links_form($PAGE->url);

if ($mform->is_cancelled()) {
    redirect(new moodle_url($returnurl));
} else {
    $buildcategorytitle = get_string('insertltilinktocourses', 'block_panopto');
    $PAGE->set_title($buildcategorytitle);
    $PAGE->set_heading($buildcategorytitle);

    $PAGE->navbar->add(get_string('blocks'), new moodle_url('/admin/blocks.php'));
    $PAGE->navbar->add(get_string('pluginname', 'block_panopto'), new moodle_url('/admin/settings.php?section=blocksettingpanopto'));

    $PAGE->navbar->add($buildcategorytitle, new moodle_url($PAGE->url));

    echo $OUTPUT->header();

            error_reporting(E_ALL);
            error_log(__FILE__ . ':' . __FUNCTION__ . ':' . __LINE__ . " ");
    if ($data = $mform->get_data()) {
            error_log(__FILE__ . ':' . __FUNCTION__ . ':' . __LINE__ . " \$data->category == $data->category");
        $task = new \block_panopto\task\insert_lti_links();
        $task->set_custom_data(['categoryid' => $data->category]);

        if ($taskid = \core\task\manager::queue_adhoc_task($task)) {
            error_log(__FILE__ . ':' . __FUNCTION__ . ':' . __LINE__ . " \$taskid == $taskid");
            $task->set_id($taskid);
            echo "<p>" . get_string('viewtasklog', 'block_panopto', new moodle_url('/admin/tasklogs.php', ['filter' => '\block_panopto\task\insert_lti_links '])) . "</p>";
        }

        echo "<a href='$returnurl'>" . get_string('back_to_config', 'block_panopto') . '</a>';
    } else {
        $mform->display();
    }

    echo $OUTPUT->footer();
}
