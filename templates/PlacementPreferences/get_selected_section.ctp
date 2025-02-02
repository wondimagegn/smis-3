<?php
if (!empty($sections)) { ?>
	<option value="">[ Select Section ]</option>
	<?php
} else { ?>
	<option value="">[ No Active Section Found ]</option>
	<?php
} 

if (count($sections) > 0) {
	foreach ($sections as $id => $section) {
		echo "<optgroup label='" . $id . "'>";
			foreach ($section as $key => $value) {
				echo "<option value='" . $key . "'>" . $value . "</option>";
			}
		echo "</optgroup>";
	}
} ?>