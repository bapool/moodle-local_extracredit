# Extra Credit Plugin for Moodle

Add an "Extra Credit" checkbox to all gradable activities in Moodle, allowing teachers to mark assignments, quizzes, and other activities as extra credit.

![Moodle Plugin](https://img.shields.io/badge/Moodle-4.3%2B-orange.svg)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)
![License](https://img.shields.io/badge/License-GPLv3-green.svg)

## Description

This local plugin enhances Moodle's gradebook functionality by adding an "Extra Credit" checkbox to all gradable activities. When enabled, the activity's points are marked as extra credit in the gradebook, allowing students to earn points above the maximum course total.

The plugin works seamlessly with all Moodle gradebook aggregation methods:
- Natural
- Simple weighted mean of grades
- Mean of grades (with extra credits)
- Sum of grades

## Features

- ✅ Adds "Extra Credit" checkbox to all gradable activities (Assignments, Quizzes, Lessons, etc.)
- ✅ Automatically hides/disables the checkbox for inappropriate grade types
- ✅ Works with all Moodle gradebook aggregation methods
- ✅ Preserves existing extra credit settings when editing activities
- ✅ Safe handling of mixed activities (points + outcomes)
- ✅ Privacy API compliant
- ✅ No database tables required
- ✅ Cross-database compatible (MySQL, MariaDB, PostgreSQL)

## Requirements

- Moodle 4.3 or later
- PHP 8.0 or later
- MySQL 8.0+, MariaDB 10.6+, or PostgreSQL 13+

## Installation

### Method 1: Install from Moodle Plugins Directory (Recommended)

1. Log in to your Moodle site as an administrator
2. Navigate to **Site administration > Plugins > Install plugins**
3. Search for "Extra Credit"
4. Click **Install** and follow the prompts

### Method 2: Manual Installation via ZIP

1. Download the latest release ZIP file from the [Moodle Plugins Directory](https://moodle.org/plugins/) or [GitHub Releases](../../releases)
2. Log in to your Moodle site as an administrator
3. Navigate to **Site administration > Plugins > Install plugins**
4. Upload the ZIP file
5. Click **Install plugin from the ZIP file**
6. Follow the on-screen prompts to complete installation

### Method 3: Manual Installation via File System

1. Download the latest release or clone this repository
2. Copy the `extracredit` folder to `/path/to/moodle/local/`
3. The directory structure should be: `/local/extracredit/`
4. Log in to your Moodle site as an administrator
5. Navigate to **Site administration > Notifications**
6. Follow the prompts to complete the installation

## Usage

### For Teachers

1. Create or edit any gradable activity (Assignment, Quiz, Lesson, etc.)
2. Scroll to the **Grade** section
3. You will see an **Extra credit** checkbox
4. Check the box to mark this activity as extra credit
5. Save the activity
6. The activity will now be calculated as extra credit in the gradebook

### When Extra Credit is Available

The Extra Credit checkbox appears and is enabled for:
- ✅ Activities with **point-based grades** (Type: Point)
- ✅ Activities with a maximum grade greater than 0

### When Extra Credit is Disabled/Hidden

The Extra Credit checkbox is automatically disabled or hidden for:
- ❌ Activities with **no grade** (Type: None)
- ❌ Activities using **scales** instead of points
- ❌ Activities using **outcomes only** (no point grade)

### Mixed Activities (Points + Outcomes)

If an activity has both a point grade AND outcomes:
- ✅ The Extra Credit checkbox is available
- ✅ Extra credit applies **only to the point grade**
- ✅ Outcomes are graded normally (not affected by extra credit)

## How It Works

The plugin modifies the `aggregationcoef` and `aggregationcoef2` fields in the Moodle `grade_items` table:
- Sets both fields to `1` when extra credit is enabled
- Sets both fields to `0` when extra credit is disabled

This ensures compatibility with all Moodle gradebook aggregation methods, as different methods use different coefficient fields.

## Compatibility

### Grade Types
| Grade Type | Extra Credit Support |
|------------|---------------------|
| Point (numeric) | ✅ Yes |
| Scale | ❌ No (disabled) |
| None | ❌ No (hidden) |
| Outcomes only | ❌ No (hidden) |
| Points + Outcomes | ✅ Yes (points only) |

### Aggregation Methods
| Aggregation Method | Extra Credit Support |
|-------------------|---------------------|
| Natural | ✅ Yes |
| Simple weighted mean of grades | ✅ Yes |
| Mean of grades (with extra credits) | ✅ Yes |
| Sum of grades | ✅ Yes |
| Mean of grades | ✅ Yes |
| Weighted mean of grades | ✅ Yes |
| Median of grades | ✅ Yes |
| Lowest grade | ✅ Yes |
| Highest grade | ✅ Yes |

### Tested On
- Moodle 4.3 LTS
- Moodle 4.4
- Moodle 4.5
- Ubuntu 22.04 LTS
- MySQL 8.0
- MariaDB 10.6
- PostgreSQL 13+

## Troubleshooting

### Extra Credit checkbox doesn't appear
**Solution:** Verify the activity supports point-based grading. The checkbox will not appear for activities set to "No grade" or using scales.

### Error when grading outcome-only activities
**Solution:** This was fixed in version 1.0. If upgrading from an earlier version, run this SQL query (replace `mdl_` with your table prefix):

```sql
UPDATE mdl_grade_items 
SET aggregationcoef = 0, aggregationcoef2 = 0 
WHERE itemtype = 'mod' 
AND itemnumber = 0 
AND gradetype = 0 
AND (aggregationcoef > 0 OR aggregationcoef2 > 0);
```

Then purge all caches: **Site administration > Development > Purge all caches**

### Extra credit not calculating correctly
**Solution:** Check your course gradebook aggregation method. Extra credit works with all methods but calculates differently depending on the method chosen.

### Checkbox appears but is disabled/grayed out
**Solution:** This is expected behavior when:
- Grade type is set to "None"
- Grade is set to 0 points
- Activity is using a scale instead of points

## Uninstallation

1. Navigate to **Site administration > Plugins > Plugins overview**
2. Find "Extra Credit" in the list
3. Click **Uninstall**
4. Follow the prompts

**Note:** Uninstalling will remove the Extra Credit checkbox from activities, but existing extra credit flags in the gradebook will remain. To remove all extra credit flags, run this SQL query before uninstalling:

```sql
UPDATE mdl_grade_items 
SET aggregationcoef = 0, aggregationcoef2 = 0 
WHERE itemtype = 'mod' 
AND (aggregationcoef > 0 OR aggregationcoef2 > 0);
```

## Privacy

This plugin does not store any personal data. It only modifies grade item settings to mark activities as extra credit. The plugin is fully compliant with Moodle's Privacy API and GDPR requirements.

## Contributing

Contributions are welcome! Please:

1. Fork this repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please ensure your code:
- Follows [Moodle coding style](https://moodledev.io/general/development/policies/codingstyle)
- Includes appropriate PHPDoc comments
- Has been tested with developer debugging enabled
- Works with both MySQL/MariaDB and PostgreSQL

## Support

- **Bug Reports:** [GitHub Issues](../../issues)
- **Documentation:** This README and inline code comments
- **Moodle Forums:** [Plugins Forum](https://moodle.org/mod/forum/view.php?id=44)

## License

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <http://www.gnu.org/licenses/>.

## Author

**Brian A. Pool**  
National Trail Local Schools

## Changelog

### Version 1.0 (2024-11-05)
- Initial stable release
- Support for all Moodle gradebook aggregation methods
- Automatic handling of outcome-only activities
- Safe handling of mixed point + outcome activities
- Ability to enable and disable extra credit on activities
- Compatibility with scales and no-grade activities
- Privacy API implementation
- Cross-database compatibility (MySQL, MariaDB, PostgreSQL)

## Acknowledgments

Special thanks to the Moodle community for their support and feedback during development.

---

**If you find this plugin useful, please consider starring the repository!** ⭐
