<?php
if (!empty($colleges)) {
?>
    <option value=0>--Select College--</option>
	<?php 
	foreach($colleges as $collegeId=>$collegeName){
	
	echo '<option value="'.$collegeId.'">'.$collegeName.'</option>'."\n";
	}

} else if(empty($colleges)){
?>
	<option></option>
<?php
}
?>
