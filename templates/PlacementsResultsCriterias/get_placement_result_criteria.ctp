<?php
$options .= "";
		
if (isset($resultList) && !empty($resultList)) {
   foreach($resultList as $id => $result) {
		
			echo "<option value='".$id."'>".$result."</option>";
		
	}
}
?>
