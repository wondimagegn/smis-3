<?php
if (isset($error['NO_PLACEMENT_SETTING_FOUND'][0])) { ?>
	<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span><?= $error['NO_PLACEMENT_SETTING_FOUND'][0]; ?></div>
	<?php
}
if (!count($students)) { ?>
	<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Unable to find list of students which need preparation for placement. This happens either all students are ready for placement or no student is found in the given search criteria.</div>
	<?php
} else { //debug($students);
	echo $this->element('prepare_placement');
}
