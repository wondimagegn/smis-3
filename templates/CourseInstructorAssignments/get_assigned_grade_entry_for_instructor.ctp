<?php
if (isset($publishedCourses) && count($publishedCourses) > 0) { ?>
	<option value="">[ Select Course ]</option>
	<?php
	foreach ($publishedCourses as $id => $course) {
		echo "<optgroup label='" . $id . "'>";
		foreach ($course as $key => $value) {
			echo "<option value='" . $key . "'>" . $value . "</option>";
		}
		echo "</optgroup>";
	}
} else { ?>
	<option value="">[ No Assigned Courses Found, Try Changing Filters ]</option>
	<?php
} ?>