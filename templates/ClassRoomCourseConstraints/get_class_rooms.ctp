<?php
if (!empty($classRooms)) {
?>
    <option value=0>Select Class Room</option>
	<?php 
	foreach($classRooms as $classRoomId=>$ClassRoomName){
	
	echo '<option value="'.$classRoomId.'">'.$ClassRoomName.'</option>'."\n";
	}

} else if(empty($classRooms)){
?>
	<option value=0>No Class Room</option>
<?php
}
?>
