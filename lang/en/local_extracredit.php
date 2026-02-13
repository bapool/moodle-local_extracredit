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

$string['pluginname'] = 'Extra Credit Checkbox';
$string['extracredit'] = 'Extra credit';
$string['extracredit_help'] = 'When checked, this activity will be marked as extra credit in the gradebook. Extra credit points are added on top of the course total.';
$string['privacy:metadata'] = 'The Extra Credit plugin does not store any personal data. It only modifies grade item settings to mark activities as extra credit.';
