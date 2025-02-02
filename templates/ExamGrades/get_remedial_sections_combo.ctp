<?php
$options = "";
debug($remedialSectionOrganized);

if (isset($remedialSectionOrganized) && !empty($remedialSectionOrganized)) {
   
   foreach($remedialSectionOrganized as $id => $section) {
		echo "<option value='".$id."'>".$section."</option>";
	}
}
?>
