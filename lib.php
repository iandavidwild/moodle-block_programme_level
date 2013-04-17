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

// Fix bug: moving the block means the ual_api local plugin is no longer loaded. We'll need to specify the path to
// the lib file directly. See https://moodle.org/mod/forum/discuss.php?d=197997 for more information...
require_once($CFG->dirroot . '/local/ual_api/lib.php');

/**
 * This class creates a course level tree. It shows the relationship between Moodle courses - which will be specific
 * to a given institution.
 */
class programme_level_tree implements renderable {
    public $context;
    public $courses;
    public function __construct() {
        global $USER, $CFG;
        $this->context = get_context_instance(CONTEXT_USER, $USER->id);

        $api = ual_api::getInstance();
        // is ual_mis class loaded?
        if (isset($api)) {

            $ual_username = $api->get_ual_username($USER->username);

            $programmes = $api->get_user_programmes($ual_username);

            $this->courses = $this->construct_tree_view($programmes);

        }

        // TODO warn if local plugin 'ual_api' is not installed.
    }

    private function construct_tree_view($programmes) {
        global $USER;

        // Create a reference array of programmes

        $api = ual_api::getInstance();
        if(isset($api)) {
            $reference_programmes = array();
            if(!empty($programmes)) {
                foreach($programmes as $programme) {
                    $programme_code = $programme->get_aos_code().$programme->get_aos_period().$programme->get_acad_period();
                    $programme->set_user_enrolled($api->get_enrolled($USER->id, $programme->get_moodle_course_id()));
                    $reference_programmes[$programme_code] = $programme;
                }
            }
        }

        return $reference_programmes;
    }
}

?>