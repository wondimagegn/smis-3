<?php
$options = "";
		
if (isset($departmentList) && !empty($departmentList)) {
   foreach($departmentList as $id => $name) {
		
			echo "<option value='".$id."'>".$name."</option>";
		
	}
}
?>
