<?php
$options = "";
if (isset($sectionOrganizedByYearLevel) && !empty($sectionOrganizedByYearLevel)) {
   foreach($sectionOrganizedByYearLevel as $id => $section) {
		echo "<option value='".$id."'>".$section."</option>";
	}
}
?>
