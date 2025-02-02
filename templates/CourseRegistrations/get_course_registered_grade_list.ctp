<?php
if (isset($grade_scale) && !empty($grade_scale)) {
	foreach ($grade_scale as $key => $value) {
		echo "<option value='" . $key . "'>" . $value . "</option>";
	} 

	/* if (isset($grade_scale_details) && ! empty($grade_scale_details)) {
		$grade_scale_details = $grade_scale['GradeScaleDetail'];
		foreach ($grade_scale_details as $key => $grade_scale_detail) {
			echo "<option value='" . $grade_scale_detail['grade'] . "'>" . $grade_scale_detail['grade'] . " (" . $grade_scale_detail['minimum_result'] . " - " . $grade_scale_detail['maximum_result'] . ")</option>";
		}
	} */
}
//echo "<option value='NG'>NG</option>";
