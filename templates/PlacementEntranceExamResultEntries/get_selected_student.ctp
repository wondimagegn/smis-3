<?php
if (count($students) <= 0) { ?>
	<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Unable to find list of students that need entrance exam entry in the selected section.</div>
	<?php
} else { //debug($students);
	echo $this->element('exam_sheet_entrance');
}
