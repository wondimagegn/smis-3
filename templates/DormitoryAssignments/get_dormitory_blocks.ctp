<?php

if (!empty($fine_formatted_dormitories)) {
?>
    <option value=0>All</option>
	<?php 
	foreach($fine_formatted_dormitories as $campus=>$dormitoryBlock){
		echo '<optgroup label="'.$campus.'">';
		foreach($dormitoryBlock as $dormitoryBlockId=>$dormitoryBlockName){
			echo '<option value="'.$dormitoryBlockId.'">'.$dormitoryBlockName.'</option>'."\n";
		}
		echo '</optgroup>';
	}
} 
?>
