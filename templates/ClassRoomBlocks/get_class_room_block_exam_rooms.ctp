<?php
echo '<option value="0">--- Select Class Room ---</option>';
foreach($classRooms as $id => $room_code) {
	echo '<option value="'.$id.'">'.$room_code.'</option>';
}
?>
