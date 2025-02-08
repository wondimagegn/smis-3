<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class=" icon-print" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Temporary Student Degree Printing'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<?= $this->Form->create('GraduateList'); ?>

				<div style=" margin-top: -30px;">
					<hr>
					<fieldset style="padding-top: 15px; padding-bottom: 5px;">
						<!-- <legend>&nbsp;&nbsp; Student Number/ID: &nbsp;&nbsp;</legend> -->
						<div class="row">
							<div class="large-3 columns">
								&nbsp;
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('studentnumber', array('placeholder' => 'Type Student ID here...', 'id' => 'studentNumber', 'label' => false)); ?>
							</div>
							<div class="large-5 columns">
								<?= $this->Form->submit(__('Get Student Details'), array('name' => 'continueTemporaryDegreePrint', 'id' => 'getStudentDetails', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
							</div>
						</div>
					</fieldset>
				</div>
				<hr>

				<?php
				if (!empty($temporary_degree)) { ?>

					<table cellspacing="0" cellpadding="0" class="fs13 table-borderless">
						<tr>
							<td style="width:15%; font-weight:bold">Full Name:</td>
							<td style="width:85%"><?= $temporary_degree['student_detail']['Student']['full_name']; ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Student ID:</td>
							<td><?= $temporary_degree['student_detail']['Student']['studentnumber']; ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Sex:</td>
							<td><?= ucwords(strtolower($temporary_degree['student_detail']['Student']['gender'])); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Program:</td>
							<td><?= $temporary_degree['student_detail']['Program']['name']; ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Program Type:</td>
							<td><?= $temporary_degree['student_detail']['ProgramType']['name']; ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">College:</td>
							<td><?= $temporary_degree['student_detail']['College']['name']; ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Department:</td>
							<td><?= (!empty($temporary_degree['student_detail']['Department']['name']) ? $temporary_degree['student_detail']['Department']['name'] : 'Freshman Program'); ?></td>
						</tr>
					</table>
					<hr>
					
					<?= $this->element('cost_sharing_due_and_payment'); ?>
					<hr>
					<?= $this->element('student_clearance_list'); ?>
					<hr>

					<?= $this->Form->input('id', array('value' => $temporary_degree['student_detail']['Student']['id'])); ?>
					
					<?php 
					if ($temporary_degree['student_detail']['Student']['program_id'] != PROGRAM_UNDEGRADUATE && $temporary_degree['student_detail']['Student']['program_id'] != PROGRAM_PGDT) { ?>
						<hr>
						<div class="row">
							<div class="large-3 columns">
							<?= $this->Form->input('have_agreement', array('type' => 'checkbox', 'checked' => 'checked')); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('in_service', array('type' => 'checkbox')); ?>
							</div>
							<div class="large-6 columns">
								&nbsp;
							</div>
						</div>
						<hr>
						<?php
					} ?>
					
					<?= $this->Form->submit(__('Get Temporary Degree'), array('name' => 'displayTemporaryDegreePrint', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
					<?php
				} ?>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	var form_being_submitted = false;

	$('#getStudentDetails').click(function(event) {
		var isValid = true;
		var studentNumber = $('#studentNumber').val();

		if (studentNumber == '') {
			event.preventDefault();
			$('#studentNumber').focus();
            isValid = false;
            return false;
		}

		if (form_being_submitted) {
			alert('Fetching Student Details, please wait a moment or refresh your page...');
			$('#getStudentDetails').attr('disabled', true);
			isValid = false;
			return false;
		}

		if (!form_being_submitted && isValid) {
			$('#getStudentDetails').val('Fetching Student Details...');
			form_being_submitted = true;
			isValid = true
			return true;
		} else {
			return false;
		}
	});
</script>