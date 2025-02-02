<?php
if (!empty($placementRoundParticipants)) { ?>
	<option value="">--- Select Preference Choice ---</option>
	<?php
} else { ?>
	<option value="">--- Select Preference Choice ---</option>
	<?php
} ?>

<?php
if (count($placementRoundParticipants) > 0) {
	foreach ($placementRoundParticipants as $id => $value) {
		echo "<option value='" . $id . "'>" . $value . "</option>";
	}
}
?>