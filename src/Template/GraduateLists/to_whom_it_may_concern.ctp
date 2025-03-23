<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class=" icon-print" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('To Whom It May Concern Letter Printing'); ?></span>
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
								<?= $this->Form->submit(__('Get Student Details'), array('name' => 'continueLanguageProficiencyLetterPrint', 'id' => 'getStudentDetails', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
							</div>
						</div>
					</fieldset>
				</div>
				<hr>
				
				<?php
				if (!empty($graduation_letter)) { ?>
					
					<table cellspacing="0" cellpadding="0" class="fs13 table-borderless">
						<tr>
							<td style="width:15%; font-weight:bold">Full Name:</td>
							<td style="width:85%"><?= $graduation_letter['student_detail']['Student']['first_name'] . ' ' . $graduation_letter['student_detail']['Student']['middle_name'] . ' ' . $graduation_letter['student_detail']['Student']['last_name']; ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Student ID:</td>
							<td><?= $graduation_letter['student_detail']['Student']['studentnumber']; ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Sex:</td>
							<td><?= ucwords(strtolower($graduation_letter['student_detail']['Student']['gender'])); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Program:</td>
							<td><?= $graduation_letter['student_detail']['Program']['name']; ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Program Type:</td>
							<td><?= $graduation_letter['student_detail']['ProgramType']['name']; ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">College:</td>
							<td><?= $graduation_letter['student_detail']['College']['name']; ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Department:</td>
							<td><?= (!empty($graduation_letter['student_detail']['Department']['name']) ? $graduation_letter['student_detail']['Department']['name'] : 'Freshman Program'); ?></td>
						</tr>
					</table>
					<hr>
					<?php
					if (!empty($graduation_letter_template)) {
						echo $this->Form->input('id', array('value' => $graduation_letter['student_detail']['Student']['id']));
						echo $this->Form->submit('Get Letter', array('name' => 'displayLanguageProficiencyLetterPrint', 'div' => false, 'class' => 'tiny radius button bg-blue'));
					}
				}
				echo $this->Form->end(); ?>
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
