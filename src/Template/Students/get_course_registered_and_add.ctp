<option value="0">--- Select Course ---</option>
<?php
/*
if(count($published_courses) > 0) {
	if(count($published_courses['Course Registered']) > 0) {
		echo "<optgroup label='Course Registered'>";
		foreach($published_courses['Course Registered'] as $key => $name) {
			echo "<option value='".$key."~register'>".$name."</option>";
		}
		echo "</optgroup>";
	}
	
	if(count($published_courses['Course Added']) > 0) {
		echo "<optgroup label='Course Added'>";
		foreach($published_courses['Course Added'] as $key => $name) {
			echo "<option value='".$key."~add'>".$name."</option>";
		}
		echo "</optgroup>";
	}
}
*/
?>

<?php
if(count($published_courses) > 0) {
	foreach($published_courses as $id => $courses) {
		echo "<optgroup label='".$id."'>";
		foreach($courses as $key => $course) {
			echo "<option value='".$key."'>".$course."</option>";
		}
		echo "</optgroup>";
	}
}
?>
