<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_local_extracredit_install() {
    // No database tables needed for this plugin
    // It uses existing Moodle grade_items table
    return true;
}
