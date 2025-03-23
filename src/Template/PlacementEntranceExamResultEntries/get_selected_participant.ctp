<?php
if (!empty($placementRoundParticipants)) {?>
	<option value="">[ Select Exam Result Target ]</option>
	<?php
} else { ?>
	<option value="">[ Select Exam Result Target ]</option>
	<?php
}
if (count($placementRoundParticipants) > 0) {
	foreach ($placementRoundParticipants as $id => $value) {
		echo "<option value='" . $id . "'>" . $value . "</option>";
	}
} ?>