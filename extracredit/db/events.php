<?php
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
