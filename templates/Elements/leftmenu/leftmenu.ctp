<?php
$controller = str_replace('_', '', $this->request->params['controller']);
$action = !empty($this->request->params['action']) ? $this->request->params['action'] : 'index';
?>
<ul id='menu-showhide' class="topnav slicknav">
	<li>
		<a href="/pages/academic_calender" title="Academic Calendar" class="tooltip-tip" <?= !empty($action) && $action == 'academic_calender' ? 'id="menu-select"' :''; ?>>
			<i class="fontello-calendar-1 "></i>
			<span>Academic Calendar</span>
		</a>
	</li>
	<li>
		<a href="#" class="tooltip-tip" title="Transcript" <?= !empty($action) && ($action == 'official_transcript_request' || $action == 'official_request_tracking' ) ? 'id="menu-select"' :''; ?>>
			<i class=" icon-window"></i>
			<span>Transcript</span>
		</a>
		<ul>
			<li><a href="/pages/official_transcript_request" title="Transcript Request">Transcript Request</a></li>
			<li><a href="/pages/official_request_tracking" title="Transcript Request">Official request status tracking</a></li>
		</ul>
	</li>

	<li>
		<a href="#" class="tooltip-tip" title="Admission" <?= !empty($action) && ($action == 'admission' || $action == 'online_admission_tracking' ) ? 'id="menu-select"' :''; ?>>
			<i class=" fontello-vcard"></i>
			<span>Admission</span>
		</a>
		<ul>
			<li><a href="/pages/admission" title="Online Admission">Online Admission</a></li>
			<li><a href="/pages/online_admission_tracking" title="Track Online Admission Status">Track Online Admission Status</a></li>
		</ul>
	</li>

	<li>
		<a href="#" class="tooltip-tip" title="Admission" <?= !empty($action) && $action == 'member_registration' ? 'id="menu-select"' :''; ?>>
			<i class=" fontello-college"></i>
			<span>Alumni</span>
		</a>
		<ul class="vertical menu">
			<li><a href="/alumni/member_registration" title="Alumni Registration">Alumni Registration</a></li>
		</ul>
	</li>
</ul>