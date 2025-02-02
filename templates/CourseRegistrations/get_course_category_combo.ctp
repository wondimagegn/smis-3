<?php
$options = "";
if (isset($courseLists) && !empty($courseLists)) {
  foreach($courseLists as $course_id =>$coursename){
	 $options .= "<option value=\"".$course_id."\">".$coursename."</option>";
   }
}

if(count($courseLists) == 0)
	$options = "<option value=\"\"></option>";
echo $options;
?>
 
