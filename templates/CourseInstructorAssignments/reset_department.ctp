<?php
# /app/views/course_instructor_assignments/reset_departments.ctp
?>
<?php
if (!empty($departments)) {
?>
    <option>Select Dept.</option>
	<?php 
	foreach($departments as $departmentId=>$departmentName){
	
	echo '<option value="'.$departmentId.'">'.$departmentName.'</option>'."\n";
	}

} else if(empty($departments)){
?>
	<option>No Department</option>
<?php
}
?>
