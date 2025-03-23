<?php
$options='';
$options= "<option value=\"\">--Select Room--</option>";

	 foreach ($rooms as $camp=>$room) {
	     $options .= "<optgroup label=\"".$camp."\">";
	     foreach($room as $room_id=>$room_name){
	        $options.='<option value="'.$room_id.'">'.$room_name.'</option>'."\n";
	     }
	     $options .= "</optgroup>";
	}
	//echo $options;
if(count($rooms) == 0 )
	$options = "<option value=\"\">No Room Available</option>";
echo $options;

?>
