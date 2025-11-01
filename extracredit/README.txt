Extra Credit Plugin for Moodle
================================

Plugin Name: local_extracredit
Version: 1.0
Release Date: October 22, 2024
Requires: Moodle 4.3+
Tested on: Moodle 4.5.7
Maturity: STABLE


DESCRIPTION
-----------
This local plugin adds an "Extra Credit" checkbox to all gradable activities in Moodle. 
When enabled, the activity's points are marked as extra credit in the gradebook, allowing 
students to earn points above the maximum course total.

The plugin works with all native Moodle aggregation methods:
- Natural
- Simple weighted mean of grades
- Mean of grades (with extra credits)
- Sum of grades


FEATURES
--------
- Adds an Extra Credit checkbox to all gradable activities (Assignments, Quizzes, Lessons, etc.)
- Automatically hides/disables the checkbox for inappropriate grade types:
  * Activities with no grade (Type: None)
  * Activities using scales instead of points
  * Activities using outcomes only
- Works with all Moodle gradebook aggregation methods
- Preserves existing extra credit settings when editing activities
- Safe handling of mixed activities (points + outcomes)


INSTALLATION
------------
1. Extract the plugin files to: /path/to/moodle/local/extracredit/

2. The directory structure should look like:
   /local/extracredit/
   ├── lib.php
   ├── version.php
   ├── lang/
   │   └── en/
   │       └── local_extracredit.php
   └── README.txt

3. Log in to Moodle as an administrator

4. Navigate to: Site administration > Notifications

5. Follow the prompts to complete the installation

6. No additional configuration is required


USAGE
-----
For Teachers:

1. Create or edit any gradable activity (Assignment, Quiz, Lesson, etc.)

2. In the Grade section, you will see an "Extra Credit" checkbox

3. Check the box to mark this activity as extra credit

4. Save the activity

5. The activity will now be calculated as extra credit in the gradebook


COMPATIBILITY
-------------
- Point-based grades: YES - Extra credit works perfectly
- Scale-based grades: NO - Checkbox is automatically disabled
- Outcome-only activities: NO - Checkbox is automatically hidden
- Points + Outcomes: YES - Extra credit applies to the point grade only


IMPORTANT NOTES
---------------
- The Extra Credit checkbox only appears for activities that support point-based grading
- The checkbox is automatically disabled or hidden for inappropriate grade types
- Extra credit only applies to point values, not to scales or outcomes
- When an activity has both points and outcomes, extra credit applies only to the point grade
- The plugin sets both aggregationcoef and aggregationcoef2 to ensure compatibility with all 
  Moodle aggregation methods


UPGRADING FROM OLDER VERSIONS
------------------------------
If upgrading from a version prior to 2024102200:

1. Back up your Moodle database before upgrading

2. Replace the plugin files in /local/extracredit/

3. Visit Site administration > Notifications to complete the upgrade

4. Run this SQL to clean up any corrupted outcome-only activities:
   (Replace 'mdl_' with your table prefix if different)

   UPDATE mdl_grade_items 
   SET aggregationcoef = 0, aggregationcoef2 = 0 
   WHERE itemtype = 'mod' 
   AND itemnumber = 0 
   AND gradetype = 0 
   AND (aggregationcoef > 0 OR aggregationcoef2 > 0);

5. Purge all caches: Site administration > Development > Purge all caches


TROUBLESHOOTING
---------------
Problem: Extra Credit checkbox doesn't appear
Solution: Verify the activity supports point-based grading. The checkbox will not appear 
         for activities set to "No grade" or using scales.

Problem: Error when grading outcome-only activities
Solution: Run the cleanup SQL query in the "Upgrading" section above, then purge caches.

Problem: Extra credit not calculating correctly in gradebook
Solution: Check your course gradebook aggregation method. Extra credit works with all 
         methods but calculates differently depending on the method chosen.

Problem: Checkbox appears but is disabled/grayed out
Solution: This is expected behavior when:
         - Grade type is set to "None"
         - Grade is set to 0 points
         - Activity is using a scale instead of points


TECHNICAL DETAILS
-----------------
The plugin uses three Moodle hooks:

1. local_extracredit_coursemodule_standard_elements()
   - Adds the Extra Credit checkbox to activity forms
   - Handles visibility and disabled states based on grade type

2. local_extracredit_coursemodule_definition_after_data()
   - Loads existing extra credit settings when editing activities
   - Ensures checkbox state matches current gradebook settings

3. local_extracredit_coursemodule_edit_post_actions()
   - Saves the extra credit setting to the gradebook
   - Sets aggregationcoef and aggregationcoef2 for broad compatibility
   - Validates grade type before applying extra credit


DATABASE CHANGES
----------------
This plugin does not create any new database tables. It modifies existing grade_items records:
- Sets aggregationcoef = 1 (for weighted mean aggregations)
- Sets aggregationcoef2 = 1 (for natural/sum aggregations)


UNINSTALLATION
--------------
1. Navigate to: Site administration > Plugins > Plugins overview

2. Find "local_extracredit" in the list

3. Click "Uninstall"

4. Follow the prompts

Note: Uninstalling will remove the Extra Credit checkbox from activities, but existing 
extra credit flags in the gradebook will remain. To remove all extra credit flags:

   UPDATE mdl_grade_items 
   SET aggregationcoef = 0, aggregationcoef2 = 0 
   WHERE itemtype = 'mod' 
   AND (aggregationcoef > 0 OR aggregationcoef2 > 0);


SUPPORT
-------
For issues, questions, or feature requests, please contact the plugin maintainer.


LICENSE
-------
This program is free software: you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation, either 
version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.


CREDITS
-------
Author: Brian A. Pool
Organization: National Trail Local Schools
Developed for Moodle 4.5+ environments
Tested on Ubuntu 22.04 with PHP 8.x


CHANGELOG
---------
Version 1.0 (2024102200)
- Initial stable release
- Support for all Moodle gradebook aggregation methods
- Automatic handling of outcome-only activities
- Safe handling of mixed point + outcome activities
- Compatibility with scales and no-grade activities
