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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form definition for blocks/panopto/insert_lti_links.php.
 *
 * @package block_panopto
 * @author Leon Stringer <leon.stringer@ucl.ac.uk>
 * @copyright 2025 onwards UCL {@link https://www.ucl.ac.uk/}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class panopto_insert_lti_links_form extends moodleform {
    /**
     * @var string $title
     */
    protected $title = '';

    /**
     * @var string $description
     */
    protected $description = '';

    /**
     * Form definition for blocks/panopto/insert_lti_links.php.
     */
    public function definition() {
        $mform = & $this->_form;

        $categories = core_course_category::make_categories_list('', 0, ' / ');
        $mform->addElement('autocomplete', 'category', get_string('selectcategory', 'block_panopto'), $categories);
        $mform->addHelpButton('category', 'selectcategory', 'block_panopto');

        $this->add_action_buttons(true, get_string('beginaddltilinks', 'block_panopto'));
    }
}
