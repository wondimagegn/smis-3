<option value="0">[ Select Section ]</option>
<?php
if (isset($student_sections) && count($student_sections) > 0) {
	foreach ($student_sections as $id => $section) {
		echo "<optgroup label='" . $id . "'>";
		foreach ($section as $key => $value) {
			echo "<option value='" . $key . "'>" . $value . "</option>";
		}
		echo "</optgroup>";
	}
}
//echo $this->Form->input($input_name, array('id' => ($input_id != "" ? $input_id : false), 'label' => ($label != "" ? $label : false), 'type' => 'select', 'options' => $student_sections, 'default' => false));
?>
