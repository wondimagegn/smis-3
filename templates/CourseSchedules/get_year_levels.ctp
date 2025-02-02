<?php
if (isset($yearLevels) && !empty($yearLevels)) { ?>
	<option value=''>[ All or Select ]</option>
	<?php
	foreach ($yearLevels as $yearLevelId => $yearLevelName) {
		echo '<option value="' . $yearLevelId . '">' . $yearLevelName . '</option>' . "\n";
	}
} else if (empty($yearLevels)) { ?>
	<option value=''>[ No Year Level ]</option>
	<?php
} ?>