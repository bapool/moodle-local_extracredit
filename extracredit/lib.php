<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Hook to add extra credit checkbox to activity grade settings
 */
function local_extracredit_coursemodule_standard_elements($formwrapper, $mform) {
    global $CFG;
    
    // Only add if the module has grading
    $modname = $formwrapper->get_current()->modulename;
    
    if (plugin_supports('mod', $modname, FEATURE_GRADE_HAS_GRADE, false)) {
        // Create the extra credit checkbox element
        $element = $mform->createElement('advcheckbox', 'extracredit', 
            get_string('extracredit', 'local_extracredit'), null, null, array(0, 1));
        
        // Insert it before the grade category element (which is in the Grade section)
        $mform->insertElementBefore($element, 'gradecat');
        
        // Add help button
        $mform->addHelpButton('extracredit', 'extracredit', 'local_extracredit');
        $mform->setType('extracredit', PARAM_INT);
        
        // Disable if grade is set to none/no grade
        $mform->disabledIf('extracredit', 'grade[modgrade_type]', 'eq', 'none');
        $mform->disabledIf('extracredit', 'grade', 'eq', 0);
        
        // Disable if using scale instead of points
        $mform->disabledIf('extracredit', 'grade[modgrade_type]', 'eq', 'scale');
        
        // Hide completely if outcomes/indicators are being used
        if (plugin_supports('mod', $modname, FEATURE_GRADE_OUTCOMES, false)) {
            $mform->hideIf('extracredit', 'assessed', 'eq', 0);  // Hide if not graded
            // Check if the form has outcome fields - if any outcome is selected, hide extra credit
            if ($mform->elementExists('outcome_0')) {
                // If outcomes exist in the form, hide the extra credit checkbox when any outcome is used
                $mform->hideIf('extracredit', 'outcome_0', 'neq', 0);
            }
        }
    }
}

/**
 * Hook to load existing extra credit value when editing activity
 */
function local_extracredit_coursemodule_definition_after_data($formwrapper, $mform) {
    global $DB;
    
    $cm = $formwrapper->get_coursemodule();
    
    if (!$cm) {
        return;
    }
    
    // Check if this activity uses outcomes - if so, don't load extra credit
    $outcomes = grade_outcome::fetch_all_available($cm->course);
    if ($outcomes) {
        // Check if any outcomes are actually used by this activity
        $sql = "SELECT COUNT(*) 
                FROM {grade_items} 
                WHERE itemtype = 'mod' 
                AND itemmodule = :modulename 
                AND iteminstance = :iteminstance 
                AND courseid = :courseid 
                AND outcomeid IS NOT NULL 
                AND outcomeid > 0";
        
        $params = array(
            'modulename' => $formwrapper->get_current()->modulename,
            'iteminstance' => $cm->instance,
            'courseid' => $cm->course
        );
        
        $outcomecount = $DB->count_records_sql($sql, $params);
        
        // If outcomes are used, don't process extra credit
        if ($outcomecount > 0) {
            return;
        }
    }
    
    // Load the grade item - make sure we only get the main item, not outcome items
    $gradeitem = grade_item::fetch(array(
        'itemtype' => 'mod',
        'itemmodule' => $formwrapper->get_current()->modulename,
        'iteminstance' => $cm->instance,
        'courseid' => $cm->course,
        'itemnumber' => 0  // This ensures we get the main grade item, not outcome items
    ));
    
    // Check both fields since different aggregation methods use different fields
    // Also verify it's a point-based grade before checking
    if ($gradeitem && $gradeitem->gradetype == GRADE_TYPE_VALUE && 
        ($gradeitem->aggregationcoef > 0 || $gradeitem->aggregationcoef2 > 0)) {
        $mform->setDefault('extracredit', 1);
    }
}

/**
 * Hook to save extra credit setting after module is created/updated
 */
function local_extracredit_coursemodule_edit_post_actions($data, $course) {
    global $CFG, $DB;
    require_once($CFG->libdir . '/gradelib.php');
    
    // Get the extra credit value - default to 0 if not set
    $extracredit = isset($data->extracredit) ? $data->extracredit : 0;
    
    // Only process if extra credit is checked
    if (!$extracredit) {
        return $data;
    }
    
    // Check if the activity is set to have NO point grade (grade == 0 or grade type is none)
    // This includes outcome-only activities
    if (isset($data->grade) && $data->grade == 0) {
        debugging('Skipping extra credit - activity has no point grade', DEBUG_DEVELOPER);
        return $data;
    }
    
    // Additional check: if grade[modgrade_type] is set to 'none', skip
    if (isset($data->grade['modgrade_type']) && $data->grade['modgrade_type'] == 'none') {
        debugging('Skipping extra credit - grade type is none', DEBUG_DEVELOPER);
        return $data;
    }
    
    // Additional check: if using scale instead of points, skip
    if (isset($data->grade['modgrade_type']) && $data->grade['modgrade_type'] == 'scale') {
        debugging('Skipping extra credit - using scale instead of points', DEBUG_DEVELOPER);
        return $data;
    }
    
    // Fetch the main grade item for this activity (itemnumber = 0, not outcomes)
    $params = array(
        'itemtype' => 'mod',
        'itemmodule' => $data->modulename,
        'iteminstance' => $data->instance,
        'courseid' => $data->course,
        'itemnumber' => 0  // Main grade item, not outcome items
    );
    
    $gradeitem = grade_item::fetch($params);
    
    // If no grade item exists, skip (shouldn't happen but safety check)
    if (!$gradeitem) {
        debugging('Skipping extra credit - no grade item found', DEBUG_DEVELOPER);
        return $data;
    }
    
    // Only apply extra credit to point-based grades (GRADE_TYPE_VALUE)
    // Skip if using scales (GRADE_TYPE_SCALE) or outcomes (GRADE_TYPE_NONE)
    if ($gradeitem->gradetype == GRADE_TYPE_VALUE && $gradeitem->grademax > 0) {
        debugging('Applying extra credit to point-based grade item', DEBUG_DEVELOPER);
        
        // Set BOTH coefficients so it works with any aggregation method
        // aggregationcoef is used by: Simple Weighted Mean, Mean with extra credits
        // aggregationcoef2 is used by: Natural, Sum of Grades
        $gradeitem->aggregationcoef = 1;
        $gradeitem->aggregationcoef2 = 1;
        
        $gradeitem->update();
        
        debugging('Extra credit applied: aggregationcoef = ' . $gradeitem->aggregationcoef . 
                 ', aggregationcoef2 = ' . $gradeitem->aggregationcoef2, DEBUG_DEVELOPER);
    } else {
        debugging('Skipping extra credit - not a point-based grade (gradetype=' . 
                 $gradeitem->gradetype . ', grademax=' . $gradeitem->grademax . ')', DEBUG_DEVELOPER);
    }
    
    return $data;
}
