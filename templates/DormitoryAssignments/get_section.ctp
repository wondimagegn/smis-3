<?php

if (!empty($sections)) {
?>
    <option value=0>All</option>
	<?php 
	foreach($sections as $secid=>$secname){
	
	echo '<option value="'.$secid.'">'.$secname.'</option>'."\n";
	}

}
?>
