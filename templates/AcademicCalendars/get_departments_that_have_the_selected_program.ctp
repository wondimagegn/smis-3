<?php
if (isset($error['NO_PLACEMENT_SETTING_FOUND'][0])) { ?>
	<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span><?= $error['NO_PLACEMENT_SETTING_FOUND'][0]; ?></div>
	<?php
}
if (!count($college_department)) { ?>
	<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Unable to find list of department which need academic calendar definition.</div>
	<?php
} else { //debug($students);
	//echo $this->element('set_academic_calendar');
    echo $this->element('set_academic_calendar_test');
}