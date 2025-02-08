<?php
if (count($students) <= 0) { ?>
	<div id="flashMessage" class="info-box info-message"><span></span> &nbsp;&nbsp; The system unable to find list of students  who are in the selected section elegible for filling preferences.</div>
	<?php
} else { 
	echo $this->element('preference_filling_sheet');
}
