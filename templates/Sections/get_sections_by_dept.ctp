
<?php
if (isset($sections) && count($sections) > 0) { ?>
	<option value="">[ Select Section ]</option>
	<?php
	foreach ($sections as $id => $section) {
		echo "<optgroup label='" . $id . "'>";
		foreach ($section as $key => $value) {
			echo "<option value='" . $key . "'>" . $value . "</option>";
		}
		echo "</optgroup>";
	}
} else { ?>
	<option value="">[ No Active Section Found ]</option>
	<?php
} ?>
