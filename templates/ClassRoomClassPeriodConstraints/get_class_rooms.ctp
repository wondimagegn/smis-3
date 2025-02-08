<?php 
/*echo $this->Form->input('ClassRoomClassPeriodConstraint.class_room_id',array('id'=>'ajax_class_room_display','empty'=>
				'---Select Class Rooms---'));*/
?>
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
