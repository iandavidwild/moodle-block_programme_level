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
 * Main block file
 *
 * @package    block
 * @subpackage programme_level
 * @copyright  2012-13 University of London Computer Centre
 * @author     Ian Wild {@link http://moodle.org/user/view.php?id=325899}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This class builds a programme level block on any page page which displays programme links to a user
 *
 * @copyright 2012 Ian Wild
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_programme_level extends block_base {

    /** @var int Trim characters from the right */
    const TRIM_RIGHT = 1;
    /** @var int Trim characters from the left */
    const TRIM_LEFT = 2;
    /** @var int Trim characters from the center */
    const TRIM_CENTER = 3;

    /**
     * Standard init function, sets block title and version number
     *
     * @return void
     */
    public function init() {
        $this->title = get_string('programmelevel', 'block_programme_level');
    }

    /**
     * Standard specialization function
     *
     * @return void
     */
    public function specialization() {
        $this->title = get_string('programmelevel', 'block_programme_level');
    }

    /**
     * Returns the attributes to set for this block
     *
     * This function returns an array of HTML attributes for this block including
     * the defaults.
     * {@link block_tree::html_attributes()} is used to get the default arguments
     * and then we check whether the user has enabled hover expansion and add the
     * appropriate hover class if it has.
     *
     * @return array An array of HTML attributes
     */
    function html_attributes() {
        $attributes = parent::html_attributes();
        $attributes['class'] .= ' course_menu';

        return $attributes;
    }

    /**
     * Standard get content function returns $this->content containing the block HTML etc
     *
     * @return stdClass
     */
    public function get_content() {
        global $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }
        if (empty($this->instance)) {
            return null;
        }

        $trimmode = 1;
        $trimlength = 50;
        $showhiddencourses = true;

        if (!empty($this->config->trimmode)) {
            $trimmode = (int)$this->config->trimmode;
        }

        if (!empty($this->config->trimlength)) {
            $trimlength = (int)$this->config->trimlength;
        }
        
        // Do we show hidden courses?
        $context = get_context_instance(CONTEXT_SYSTEM);
        $showhiddencourses = has_capability('block/programme_level:show_hidden_courses', $context);

        // load userdefined title and make sure it's never empty
        if (empty($this->config->title)) {
            $this->title = get_string('programmelevel','block_programme_level');
        } else {
            $this->title = $this->config->title;
        }

        $this->content = new stdClass();

        $this->content->text = '';
        $this->content->footer = '';
        if (isloggedin() && !isguestuser()) {   // Show the block
            $this->content = new stdClass();

            //TODO: add capability check here?

            $renderer = $this->page->get_renderer('block_programme_level');

            $courseid = $COURSE->id;
            if(!$courseid) {
                $courseid = 1;  // Assume we are on the site front page
            }
            $this->content->text = $renderer->programme_level_tree($trimmode, $trimlength, $courseid, $showhiddencourses);
            $this->content->footer = '';

        }
        return $this->content;
    }

    /**
     * Standard function - does the block allow configuration for specific instances of itself
     * rather than sitewide?
     *
     * @return bool false
     */
    public function instance_allow_config() {
        return false;
    }

    /**
     * Standard function - there will already be a 'sticky' course level block on a course page so prevent an
     * editing teacher from adding one.
     *
     * @return bool false
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * The 'My Moodle' block cannot be hidden by default as it is integral to
     * the navigation of Moodle.
     *
     * @return false
     */
    function  instance_can_be_hidden() {
        return false;
    }

    /**
     * An instance can't be docked for the same reasons as for instance_can_be_hidden
     *
     * @return bool true or false depending on whether the instance can be docked or not.
     */
    function instance_can_be_docked() {
        return false;
    }

    /**
     * Don't allow anyone other than an administrator to delete this block as it's integral to
     * the navigation of Moodle.
     *
     * @return boolean
     */
    function user_can_edit() {
        $context = get_context_instance(CONTEXT_SYSTEM);
        return (has_capability('block/programme_level:can_edit', $context));
    }
}
