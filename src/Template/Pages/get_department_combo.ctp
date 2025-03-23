<?php
$options = "";
if (isset($departments) && count($departments) > 0) {
	$options .= "<option value=0>[ Select Department ]</option>";
	foreach ($departments as $department_id => $departmentName) {
		$options .= "<option value=\"" . $department_id . "\">" . $departmentName . "</option>";
	}
} else if (count($departments) == 0) {
	$options = "<option value=''>[ No Department Found, Try Changing Filters ]</option>";
}
echo $options;