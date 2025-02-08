<option value="0">[ Select Student ]</option>
<?php
if(isset($students) && count($students) > 0) {
	foreach($students as $key => $name) {
		echo "<option value='".$key."'>".$name."</option>";
	}
}
?>
<?php
//echo $this->Form->input($input_name, array('id' => ($input_id != "" ? $input_id : false), 'label' => ($label != "" ? $label : false), 'type' => 'select', 'options' => $students, 'default' => false));
?>
