<?php
echo '<option value="0">--- Select Invigilator ---</option>';
foreach($staffs as $id => $full_name) {
	echo '<option value="'.$id.'">'.$full_name.'</option>';
}
?>
