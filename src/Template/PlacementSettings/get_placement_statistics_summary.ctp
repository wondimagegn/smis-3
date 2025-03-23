<?php
if (!isset($placementSummary['totalStudentReadyForPlacement'])) {
	echo '<div id="flashMessage" class="info-box info-message"><span></span>The system unable to find summary report for the placement.</div>';
} else { 
		
	echo $this->element('placement_summary');
}
