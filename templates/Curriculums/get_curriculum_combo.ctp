<?php
if (isset($curriculums) && !empty($curriculums)) {
	$options = "<option value=''>[ Select Curriculum ]</option>";
	foreach ($curriculums as $curriculums_id => $curriculums_name) {
		$options .= "<option value=\"" . $curriculums_id . "\">" . $curriculums_name . "</option>";
	}
} else if (count($curriculums) == 0) {
	$options = "<option value=''>No Active Curriculum Found</option>";
}
echo $options; ?>