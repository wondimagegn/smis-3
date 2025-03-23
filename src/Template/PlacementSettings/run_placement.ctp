<?= $this->Form->create('PlacementSetting', array('onSubmit' => 'return checkForm(this);')); ?>
<script type="text/javascript">
	function appliedFor() {
		$("#PlacementSummary").empty();
		$("#PlacementSummary").append('<p>Loading ...</p>');
		//get form action
		var formUrl = '/PlacementSettings/get_placement_statistics_summary';
		$.ajax({
			type: 'POST',
			url: formUrl,
			data: $('form').serialize(),
			success: function(data, textStatus, xhr) {
				$("#PlacementSummary").empty();
				$("#PlacementSummary").append(data);
				$("#AppliedFor").attr('disabled', false);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}

	function refreshDiv(chkPassport) {
		appliedFor();
	}
</script>

<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Run Auto placement.'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div class="examTypes form">

					<div style="margin-top: -30px;">
						<hr>
						<fieldset style="padding-bottom: 15px; margin-bottom: 15px;">
							<div class="large-3 columns">
								<?= $this->Form->input('PlacementSetting.academic_year', array('onchange' => 'appliedFor()', 'id' => 'AcademicYear', 'label' => 'Academic Year: ', 'style' => 'width:150px', 'type' => 'select', 'options' => $availableAcademicYears, 'default' => (isset($this->request->data['PlacementSetting']['academic_year']) ? $this->request->data['PlacementSetting']['academic_year'] : (isset($latestACYRoundAppliedFor['academic_year']) ? $latestACYRoundAppliedFor['academic_year'] : '')))); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('PlacementSetting.round', array('onchange' => 'appliedFor()', 'id' => 'PlacementRound', 'label' => 'Placement Round: ', 'style' => 'width:150px', 'type' => 'select', 'options' => Configure::read('placement_rounds'),  'default' => (isset($this->request->data['PlacementSetting']['round']) ? $this->request->data['PlacementSetting']['round'] : (isset($latestACYRoundAppliedFor) ? $latestACYRoundAppliedFor['round'] : '')))); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('PlacementSetting.program_id', array('id' => 'ProgramId', 'label' => 'Program: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programs)); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('PlacementSetting.program_type_id', array( 'id' => 'ProgramTypeId', 'label' => 'Program Type: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programTypes)); ?>
							</div>
							<div class="large-6 columns">
								<?= $this->Form->input('PlacementSetting.applied_for', array('options' => $appliedForList, /* 'options' => $allUnits, */ 'id' => 'AppliedFor', 'type' => 'select', 'label' => 'Applied for Students in: ', 'empty' => '[ Select Applied Unit ]', 'style' => 'width:90%;', 'onchange' => 'appliedFor()')); ?>
							</div>
							<div class="large-3 columns">
								&nbsp;
							</div>
							<div class="large-3 columns">
								&nbsp;
							</div>
						</fieldset>
					</div>


					<!-- AJAX LOADING -->

					<div id="PlacementSummary">

					</div>

					<!-- END AJAX LOADING -->

					<?php
					if (!empty($autoplacedstudents)) {
						$summery = $autoplacedstudents['auto_summery']; ?>
						<hr>
						<br>
						<table  cellpadding="5" cellspacing="0" class="table" style="border: 0;">
							<tbody>
								<tr>
									<td><?= $this->Html->link($this->Html->image("pdf_icon.gif", array("alt" => "Print to PDF")) . ' Export to PDF', array('action' => "print_autoplaced_pdf"), array('escape' => false)); ?></td>
									<td><?= $this->Html->link($this->Html->image("xls-icon.gif", array('alt' => 'Export To Xls')) . ' Export to Excel', array('action' => "export_autoplaced_xls"), array('escape' => false)); ?></td>
								</tr>
							</tbody>
						</table>
						<br>
						<table  cellpadding="0" cellspacing="0" class="table">
							<tbody>
								<tr>
									<th colspan=3 class="vcenter"><h6 class="fs14 text-black">Summery of Auto Placement</h6></th>
								</tr>
								<tr>
									<th class="vcenter" style="width: 45%">Department</th>
									<th class="center">Competitive Assignment</th>
									<th class="center">Privilaged Quota Assignment</th>
								</tr>
								<?php
								foreach ($summery as $sk => $sv) { ?>
									<tr>
										<td class="vcenter"><?= $sk; ?></td>
										<td class="center"><?= $sv['C']; ?></td>
										<td class="center"><?= $sv['Q']; ?></td>
									</tr>
									<?php	
								} ?>
							</tbody>
						</table>
						<?php
						unset($autoplacedstudents['auto_summery']);

						foreach ($autoplacedstudents as $key => $data) { ?>
							<br>
							<table  cellpadding="0" cellspacing="0" class="table">
								<tr>
									<td colspan=12 class="vcenter"><h6 class="fs14 text-black"><?= $key ?></h6></td>
								</tr>
								<tr>
									<th class="center" style="width: 3%">#</th>
									<th class="vcenter" style="width: 15%">Full Name</th>
									<th class="center">Sex</th>
									<th class="center">Student Number</th>
									<th class="center">Assignment Type</th>
									<th class="center">Total Result</th>
									<th class="center">Preference Order</th>
									<th class="vcenter" style="width: 15%">Assigned To</th>
									<th class="center">Academic Year</th>
									<th class="center">Approval</th>
									<th class="center">Placement Type </th>
									<th class="center">Placement Based</th>
								</tr>
								<?php
								$i = 0;
								foreach ($data as $acceptedStudent) { ?>
									<tr>
										<td class="vcenter"><?= ++$i; ?></td>
										<td class="venter"><?= $acceptedStudent['AcceptedStudent']['full_name']; ?></td>
										<td class="center"><?= $acceptedStudent['AcceptedStudent']['sex']; ?></td>
										<td class="center"><?= $acceptedStudent['AcceptedStudent']['studentnumber']; ?></td>
										<td class="center"><?= $acceptedStudent['PlacementParticipatingStudent']['placementtype']; ?></td>
										<td class="center"><?= $acceptedStudent['PlacementParticipatingStudent']['total_placement_weight']; ?></td>
										<td class="center">
											<?php
											if (!empty($acceptedStudent['AcceptedStudent']['PlacementPreference'])) {
												foreach ($acceptedStudent['AcceptedStudent']['PlacementPreference'] as $key => $value) {
													if ($value['placement_round_participant_id'] == $acceptedStudent['PlacementParticipatingStudent']['placement_round_participant_id']) {
														echo $value['preference_order'];
														break;
													}
												}
											} ?>
										</td>
										<td class="vcenter"><?= $acceptedStudent['PlacementRoundParticipant']['name']; ?></td>
										<td class="center"><?= $acceptedStudent['PlacementRoundParticipant']['academic_year']; ?></td>
										<td class="center"><?= isset($acceptedStudent['PlacementParticipatingStudent']['status']) ? 'Approved' : 'Not Approved'; ?></td>
										<td class="center"><?= $acceptedStudent['PlacementParticipatingStudent']['placementtype']; ?></td>
										<td class="center"><?= $acceptedStudent['PlacementParticipatingStudent']['placement_based'] == 'C' ? 'Competitive' : 'Quota'; ?></td>
									</tr>
									<?php 
								} ?>
							</table>
							<?php
						}
					} ?>             
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script>

	var form_being_submitted = false;

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Running Auto Placement, please wait a moment...");
			form.runAutomPlacement.disabled = true;
			return false;
		}

		form.runAutomPlacement.value = 'Running Auto Placement...';
		form_being_submitted = true;
		return true; 
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>
