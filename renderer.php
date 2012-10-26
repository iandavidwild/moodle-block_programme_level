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
 * Print programme level tree
 *
 * @package    block_programme_level
 * @copyright  2012 University of London Computer Centre
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/blocks/programme_level/lib.php');

class block_programme_level_renderer extends plugin_renderer_base {

    /** @var int Trim characters from the right */
    const TRIM_RIGHT = 1;
    /** @var int Trim characters from the left */
    const TRIM_LEFT = 2;
    /** @var int Trim characters from the center */
    const TRIM_CENTER = 3;

    private $trimmode = self::TRIM_RIGHT;
    private $trimlength = 50;
    private $courseid = 0;

    /**
     * Prints programme level tree view
     * @return string
     */
    public function programme_level_tree($trimmode, $trimlength, $courseid) {
        $this->trimmode = $trimmode;
        $this->trimlength = $trimlength;
        $this->courseid = $courseid;

        return $this->render(new programme_level_tree);
    }

    /**
     * provides the html contained in the course level block - including the tree itself and the links at the bottom
     * of the block to 'all courses' and 'all programmes'.
     *
     * @param render_programme_level_tree $tree
     * @return string
     */
    public function render_programme_level_tree(programme_level_tree $tree) {
        global $CFG;

        $module = array('name'=>'block_programme_level', 'fullpath'=>'/blocks/programme_level/module.js', 'requires'=>array('yui2-treeview'));

        if (empty($tree) ) {
            $html = $this->output->box(get_string('noprogrammes', 'block_programme_level'));
        } else {

            $htmlid = 'programme_level_tree_'.uniqid();
            $this->page->requires->js_init_call('M.block_programme_level.init_tree', array(false, $htmlid));
            $html = '<div id="'.$htmlid.'">';
            $html .= $this->htmllize_tree($tree->courses);
            $html .= '</div>';
        }

        // Add 'View all programmes' link to bottom of block...


        $html .= html_writer::empty_tag('hr');
        $viewprogrammes_lnk = $CFG->wwwroot.'/blocks/programme_level/view.php?id='.$this->courseid.'&tab=1';
        $attributes = array();
        $html .= html_writer::link($viewprogrammes_lnk, get_string('view_all_programmes', 'block_programme_level'), $attributes);

        return $html;
    }

    /**
     * Converts the course tree into something more meaningful.
     *
     * @param $tree
     * @param int $indent
     * @return string
     */
    protected function htmllize_tree($tree, $indent=0) {
        global $CFG;

        $yuiconfig = array();
        $yuiconfig['type'] = 'html';

        $result = '<ul>';

        if(empty($tree)) {
            $result .= html_writer::tag('li', get_string('nothingtodisplay'));
        } else {
            foreach ($tree as $node) {

                $course_shortname = $this->trim($node->get_shortname());
                $attributes = array('title'=>$course_shortname);
                $moodle_url = $CFG->wwwroot.'/course/view.php?id='.$node->get_id();
                $content = html_writer::link($moodle_url, $course_shortname, $attributes);
                $attributes = array('yuiConfig'=>json_encode($yuiconfig));

                $children = $node->get_children();
                $parentids = $node->get_parentids();

                if($children == null) {
                    // if this course has parents and indent>0 then display it.
                    if($indent>0) {
                        $result .= html_writer::tag('li', $content, $attributes);
                    } elseif (empty($parentids)) {
                        $result .= html_writer::tag('li', $content, $attributes);
                    }

                } else {
                    // if this has parents OR it doesn't have parents or children then we need to display it...???
                    $result .= html_writer::tag('li', $content.$this->htmllize_tree($children, $indent+1), $attributes);
                }
            }
        }
        $result .= '</ul>';

        return $result;
    }

    /**
     * Trims the text and shorttext properties of this node and optionally
     * all of its children.
     *
     * @param string $text The text to truncate
     * @return string
     */
    private function trim($text) {
        $result = $text;

        switch ($this->trimmode) {
            case self::TRIM_RIGHT :
                if (textlib::strlen($text)>($this->trimlength+3)) {
                    // Truncate the text to $long characters
                    $result = textlib::substr($text, 0, $this->trimlength).'...';
                }
                break;
            case self::TRIM_LEFT :
                if (textlib::strlen($text)>($this->trimlength+3)) {
                    // Truncate the text to $long characters
                    $result = '...'.textlib::substr($text, textlib::strlen($text)-$this->trimlength, $this->trimlength);
                }
                break;
            case self::TRIM_CENTER :
                if (textlib::strlen($text)>($this->trimlength+3)) {
                    // Truncate the text to $long characters
                    $length = ceil($this->trimlength/2);
                    $start = textlib::substr($text, 0, $length);
                    $end = textlib::substr($text, textlib::strlen($text)-$this->trimlength);
                    $result = $start.'...'.$end;
                }
                break;
        }
        return $result;
    }
}


