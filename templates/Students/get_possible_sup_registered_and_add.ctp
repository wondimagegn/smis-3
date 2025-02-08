<option value="0">[ Select Course ]</option>
<?php
if (isset($published_courses) && count($published_courses) > 0) {
	foreach ($published_courses as $id => $courses) {
		echo "<optgroup label='" . $id . "'>";
		foreach ($courses as $key => $course) {
			echo "<option value='" . $key . "'>" . $course . "</option>";
		}
		echo "</optgroup>";
	}
} ?>
