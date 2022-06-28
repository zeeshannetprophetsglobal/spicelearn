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
 * Class containing data for my overview block.
 *
 * @package    block_owncourse
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_owncourse\output;
//namespace block_myoverview\output;
defined('MOODLE_INTERNAL') || die();


use block_myoverview\output\main;
use renderable;
use renderer_base;
use templatable;
use stdClass;


require_once($CFG->dirroot . '/blocks/myoverview/lib.php');

/**
 * Class containing data for my overview block.
 *
 * @copyright  2018 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursetab extends main  {


   
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array Context variables for the template
     * @throws \coding_exception
     *
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $USER;

        $nocoursesurl = $output->image_url('courses', 'block_myoverview')->out();

        $customfieldvalues = $this->get_customfield_values_for_export();
        $selectedcustomfield = '';
        if ($this->grouping == BLOCK_MYOVERVIEW_GROUPING_CUSTOMFIELD) {
            foreach ($customfieldvalues as $field) {
                if ($field->value == $this->customfieldvalue) {
                    $selectedcustomfield = $field->name;
                    break;
                }
            }
            // If the selected custom field value has not been found (possibly because the field has
            // been changed in the settings) find a suitable fallback.
            if (!$selectedcustomfield) {
                $this->grouping = $this->get_fallback_grouping(get_config('block_myoverview'));
                if ($this->grouping == BLOCK_MYOVERVIEW_GROUPING_CUSTOMFIELD) {
                    // If the fallback grouping is still customfield, then select the first field.
                    $firstfield = reset($customfieldvalues);
                    if ($firstfield) {
                        $selectedcustomfield = $firstfield->name;
                        $this->customfieldvalue = $firstfield->value;
                    }
                }
            }
        }
        $preferences = $this->get_preferences_as_booleans();
        $availablelayouts = $this->get_formatted_available_layouts_for_export();
        $sort = '';
        if ($this->sort == BLOCK_MYOVERVIEW_SORTING_SHORTNAME) {
            $sort = 'shortname';
        } else {
            $sort = $this->sort == BLOCK_MYOVERVIEW_SORTING_TITLE ? 'fullname' : 'ul.timeaccess desc';
        }
        
        $defaultvariables = [
            'totalcoursecount' => count(enrol_get_all_users_courses($USER->id, true)),
            'nocoursesimg' => $nocoursesurl,
            'grouping' => $this->grouping,
            'sort' => $sort,
            // If the user preference display option is not available, default to first available layout.
            'view' => in_array($this->view, $this->layouts) ? $this->view : reset($this->layouts),
            'paging' => $this->paging,
            'layouts' => $availablelayouts,
            'displaycategories' => $this->displaycategories,
            'displaydropdown' => (count($availablelayouts) > 1) ? true : false,
            'displaygroupingallincludinghidden' => $this->displaygroupingallincludinghidden,
            'displaygroupingall' => $this->displaygroupingall,
            'displaygroupinginprogress' => $this->displaygroupinginprogress,
            'displaygroupingfuture' => $this->displaygroupingfuture,
            'displaygroupingpast' => $this->displaygroupingpast,
            'displaygroupingfavourites' => $this->displaygroupingfavourites,
            'displaygroupinghidden' => $this->displaygroupinghidden,
            'displaygroupingselector' => $this->displaygroupingselector,
            'displaygroupingcustomfield' => $this->displaygroupingcustomfield && $customfieldvalues,
            'customfieldname' => $this->customfiltergrouping,
            'customfieldvalue' => $this->customfieldvalue,
            'customfieldvalues' => $customfieldvalues,
            'selectedcustomfield' => $selectedcustomfield,
            'showsortbyshortname' => $CFG->courselistshortnames,
        ];
        return array_merge($defaultvariables, $preferences);

    }
     
}
