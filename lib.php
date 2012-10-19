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
 * This class creates a course level tree. It shows the relationship between Moodle courses - which will be specific
 * to a given institution.
 */
class programme_level_tree implements renderable {
    public $context;
    public $courses;
    public function __construct() {
        global $USER, $CFG;
        $this->context = get_context_instance(CONTEXT_USER, $USER->id);

        // is ual_mis class loaded?
        if (class_exists('ual_mis')) {
            $mis = new ual_mis();

            $this->courses = $mis->get_programmes_tree($USER->idnumber);
        }

        // TODO warn if local plugin 'ual_api' is not installed.
    }
}

?>