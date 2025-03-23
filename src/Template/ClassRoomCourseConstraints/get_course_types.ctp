<?php
if (!empty($courseTypes)) {
?>
    <option value="0">---Please Select Course Type---</option>
	<?php 
	foreach($courseTypes as $courseTypeId=>$courseTypeName){
	
	echo '<option value="'.$courseTypeId.'">'.$courseTypeName.'</option>'."\n";
	}

} else if(empty($courseTypes)){
?>
	<option value="0">---Please Select Course Type---</option>
<?php
}
?> 
