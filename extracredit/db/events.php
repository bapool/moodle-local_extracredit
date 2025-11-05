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
 * Extra Credit Plugin 
 *
 * This plugin adds an Extra Credit checkbox to all gradable activities in Moodle.
 * When enabled, the activity's points are marked as extra credit in the gradebook.
 *
 * @package    local_extracredit
 * @copyright  2025 Brian A. Pool, National Trail Local Schools
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$callbacks = array(
    array(
        'hook' => 'coursemodule_standard_elements',
        'callback' => 'local_extracredit_coursemodule_standard_elements',
    ),
    array(
        'hook' => 'coursemodule_definition_after_data',
        'callback' => 'local_extracredit_coursemodule_definition_after_data',
    ),
    array(
        'hook' => 'coursemodule_edit_post_actions',
        'callback' => 'local_extracredit_coursemodule_edit_post_actions',
    ),
);
