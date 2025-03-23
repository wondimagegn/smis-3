<?php
if (!empty($dormitories)) {
?>
    <option value=0>All</option>
	<?php 
	foreach($dormitories as $dormitoryId=>$dormitoryName){
	
	echo '<option value="'.$dormitoryId.'">'.$dormitoryName.'</option>'."\n";
	}

} 
?>
