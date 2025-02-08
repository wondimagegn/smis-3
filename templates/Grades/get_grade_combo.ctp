<?php
 $options = "";
 foreach($grades as $id => $category){
	 $options .= "<option value=\"".$id."\">".$category."</option>";
	}
if(count($grades) == 0)
	$options = "<option value=\"\"></option>";
echo $options;
 ?>
