<?php
if (!empty($campus_classRoomBlocks)) {
?>
    <option value=0>All</option>
	<?php 
	foreach($campus_classRoomBlocks as $blockId=>$blockName){
	
	echo '<option value="'.$blockId.'">'.$blockName.'</option>'."\n";
	}

} else if(empty($campus_classRoomBlocks)){
?>
	<option value=0>All</option>
<?php
}
?>
