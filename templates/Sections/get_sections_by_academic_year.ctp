<option value="0">[ Select Section ]</option>
<?php
if (isset($sections) && count($sections) > 0) {
	foreach ($sections as $id => $section) {
		echo "<optgroup label='" . $id . "'>";
		foreach ($section as $key => $value) {
			echo "<option value='" . $key . "'>" . $value . "</option>";
		}
		echo "</optgroup>";
	}
} ?>
