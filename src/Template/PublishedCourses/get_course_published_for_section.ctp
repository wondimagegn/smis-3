<option value="0">[ Select Course ]</option>
<?php
if (!empty($published_courses_list)) {
	foreach ($published_courses_list as $key => $course) {
		echo "<option value='" . $key . "'>" . $course . "</option>";
	}
}
?>
