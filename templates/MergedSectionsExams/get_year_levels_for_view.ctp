<?php
if (!empty($yearLevels)) {
?>
    <option value=0>All</option>
	<?php 
	foreach($yearLevels as $yearLevelId=>$yearLevelName){
	
	echo '<option value="'.$yearLevelId.'">'.$yearLevelName.'</option>'."\n";
	}

} else if(empty($yearLevels)){
?>
	<option value=0>All</option>
<?php
}
?>
