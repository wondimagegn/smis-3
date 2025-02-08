<?php
if (!empty($sections)) { ?>
	<option value=''>[ Select Section ]</option>
	<?php
	foreach ($sections as $id => $section) {
		echo "<optgroup label='" . $id . "'>";
		foreach ($section as $key => $value) {
			echo "<option value='" . $key . "'>" . $value . "</option>";
		}
		echo "</optgroup>";
	}
} else if (empty($sections)) { ?>
	<option value=''> <?= (empty($college_id) ? '[ No College Selected ]' : (empty($department_id) ? '[ No Department Selected ]' : '[ No Active ' . (isset($year_level_name) ? ($department_id == -1 ? 'Freshman' : $year_level_name . ' year') : '') . ' Section Found ]')); ?></option>
	<?php
} ?>

