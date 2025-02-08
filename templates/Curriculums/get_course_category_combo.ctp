<?php
if (isset($courseCategories) && !empty($courseCategories)) {
	$options = "<option value=''>[ All or Select ]</option>";
	foreach ($courseCategories as $courseCategory_id => $courseCategory_name) {
		$options .= "<option value=\"" . $courseCategory_id . "\">" . $courseCategory_name . "</option>";
	}
}

if (count($courseCategories) == 0) {
	$options = "<option value=''>[ No Course Category Found]</option>";
}
echo $options;
