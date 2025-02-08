<?php
if (!empty ($publishedCourses)) { ?>
	<option value="">[ Select Course ]</option>
	<?php
} else { ?>
	<option value="">[ Select Academic Year & Semester ]</option>
	<?php
} 

if (count($publishedCourses) > 0) {
	foreach ($publishedCourses as $id => $course) {
		echo "<optgroup label='" . $id . "'>";
		foreach ($course as $key => $value) {
			echo "<option value='" . $key . "'>" . $value . "</option>";
		}
		echo "</optgroup>";
	}
} ?>