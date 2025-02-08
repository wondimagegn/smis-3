<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class=" icon-print" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Student Copy Printing'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<?= $this->Form->create('ExamGrade'); ?>

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
								<?= $this->Form->submit(__('Get Student Details'), array('name' => 'continueStudentCopyPrint', 'id' => 'getStudentDetails', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
							</div>
						</div>
					</fieldset>
				</div>
				<hr>

				<?php
				if (!empty($student_copy)) { ?>
					<table cellspacing="0" cellpadding="0" class="fs13 table-borderless">
						<tr>
							<td style="width:15%; font-weight:bold">Full Name:</td>
							<td style="width:85%"><?= $student_copy['student_detail']['Student']['first_name'] . ' ' . $student_copy['student_detail']['Student']['middle_name'] . ' ' . $student_copy['student_detail']['Student']['last_name']; ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Student ID:</td>
							<td><?= $student_copy['student_detail']['Student']['studentnumber']; ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Sex:</td>
							<td><?= ucwords(strtolower($student_copy['student_detail']['Student']['gender'])); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Program:</td>
							<td><?= $student_copy['student_detail']['Program']['name']; ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Program Type:</td>
							<td><?= $student_copy['student_detail']['ProgramType']['name']; ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">College:</td>
							<td><?= $student_copy['student_detail']['College']['name']; ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Department:</td>
							<td><?= (!empty($student_copy['student_detail']['Department']['name']) ? $student_copy['student_detail']['Department']['name'] : 'Freshman Program'); ?></td>
						</tr>
					</table>
					
					<hr>
					<?= $this->element('cost_sharing_due_and_payment'); ?>
					<hr>
					<?= $this->element('student_clearance_list'); ?>

					<?php
					if (isset($student_copy['courses_taken']) && !empty($student_copy['courses_taken'])) { ?>
						<hr>
						<h6 class="fs14 text-gray" style="margin-bottom:0px; font-weight:bold">Student copy display settings</h6>
						<hr>
						<table cellpadding="0" cellspacing="0" class="fs13 table-borderless">
							<tr>
								<td style="width:20%">Semesters on One Side:</td>
								<td style="width:80%"><?= $this->Form->input('no_of_semester', array('label' => false, 'type' => 'select', 'style' => 'width:150px', 'div' => false, 'default' => 3, 'options' => array(2 => 2, 3 => 3, 4 => 4, 5 => 5))); ?> <span style="font-size:11px; padding-left:10px;">(Number of semesters to display on one side of the student copy)</span></td>
							</tr>
							<tr>
								<td>Text Padding:</td>
								<td><?= $this->Form->input('course_justification', array('label' => false, 'type' => 'select', 'style' => 'width:150px', 'div' => false, 'default' => 1, 'options' => array(0 => 0, 1 => 1, 2 => 2))); ?> <span style="font-size:11px;  padding-left:10px;">(The space around each text)</span></td>
							</tr>
							<tr>
								<td>Font Size:</td>
								<td><?= $this->Form->input('font_size', array('label' => false, 'type' => 'select', 'style' => 'width:150px', 'div' => false, 'default' => 29, 'options' => $font_size_options)); ?> <span style="font-size:11px;  padding-left:10px;"></span></td>
							</tr>
						</table>
						<hr>
						<?= $this->Form->input('id', array('value' => $student_copy['student_detail']['Student']['id'])); ?>
						<?= $this->Form->submit(__('Get Student Copy'), array('name' => 'displayStudentCopyPrint', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
						<?php
					}
					//debug($student_copy);
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