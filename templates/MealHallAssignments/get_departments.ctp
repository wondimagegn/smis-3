<?php
if (!empty($departments)) {
?>
    <option value=0>All</option>
	<?php 
	foreach($departments as $departmentId=>$departmentName){
	
	echo '<option value="'.$departmentId.'">'.$departmentName.'</option>'."\n";
	}

} else if(empty($departments)){
?>
	<option value=0>All</option>
<?php
}
?>
