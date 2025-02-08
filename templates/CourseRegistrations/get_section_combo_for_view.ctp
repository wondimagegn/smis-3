<?php
if (count($sectionOrganizedByYearLevel) > 0) { 
	$options = "";
	if (isset($sectionOrganizedByYearLevel) && !empty($sectionOrganizedByYearLevel)) {
		foreach($sectionOrganizedByYearLevel as $id => $section) {
			echo "<option value='".$id."'>".$section."</option>";
		}
	}
} else { ?>
	<option value='-1'>[ No Results, Try Changing Search Filters ]</option>
	<?php
}
