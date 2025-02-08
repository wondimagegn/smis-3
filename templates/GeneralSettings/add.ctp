<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Set General Setting'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<?= $this->Form->create('GeneralSetting', array('id' => 'GeneralSettingAddForm', 'onSubmit' => 'return checkForm(this);')); ?>

				<?php
				if (is_numeric(DEFAULT_SEMESTER_COUNT_FOR_ACADEMIC_YEAR)) {
					$maxSemesters = DEFAULT_SEMESTER_COUNT_FOR_ACADEMIC_YEAR;
				} else {
					$maxSemesters = 3;
				} ?>

				<div style="overflow-x:auto;">
					<table cellpadding="0" cellspacing="0" class="table">
						<tbody>
							<tr>
								<?php //echo $this->Form->input('id');  ?>
								<td style="width: 33%;">
									<span id="validation-message_program" class="text-red fs13"></span>
									<div style="padding: 5%;"><?= $this->Form->input('program_id', array('id' => 'ProgramID', 'type' => 'select', 'multiple' => 'checkbox')); ?></div>
								</td>
								<td style="width: 34%;"></td>
								<td style="width: 33%;">
									<span id="validation-message_program_type" class="text-red fs13"></span>
									<div style="padding: 5%;"><?= $this->Form->input('program_type_id', array('id' => 'ProgramTypeID', 'type' => 'select', 'multiple' => 'checkbox')); ?></d>
								</td>
							</tr>
							<tr>
								<td><?= $this->Form->input('daysAvaiableForGradeChange', array('label' => 'Days Available For Grade Change', 'type' => 'number', 'step' => '7', 'min' => '7', 'max' => DEFAULT_DAYS_AVAILABLE_FOR_GRADE_CHANGE, 'value' => DEFAULT_DAYS_AVAILABLE_FOR_GRADE_CHANGE)); ?></td>
								<td><?= $this->Form->input('daysAvaiableForNgToF', array('label' => 'Days Available For NG To F', 'type' => 'number', 'step' => '7', 'min' => '7', 'max' => DEFAULT_DAYS_AVAILABLE_FOR_NG_TO_F, 'value' => DEFAULT_DAYS_AVAILABLE_FOR_NG_TO_F)); ?></td>
								<td><?= $this->Form->input('daysAvaiableForDoToF', array('label' => 'Days Available For DO To F', 'type' => 'number', 'step' => '7', 'min' => '7', 'max' => DEFAULT_DAYS_AVAILABLE_FOR_DO_TO_F, 'value' => DEFAULT_DAYS_AVAILABLE_FOR_DO_TO_F)); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->input('daysAvailableForFxToF', array('label' => 'Days Available For Fx To F', 'type' => 'number', 'step' => '7', 'min' => '7', 'max' => DEFAULT_DAYS_AVAILABLE_FOR_FX_TO_F, 'value' => DEFAULT_DAYS_AVAILABLE_FOR_FX_TO_F)); ?></td>
								<td><?= $this->Form->input('minimumCreditForStatus', array('label' => 'Minimum Credit For Status Generation', 'type' => 'number', 'step' => '1', 'min' => DEFAULT_MINIMUM_CREDIT_FOR_STATUS, 'max' => DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER, 'value' => DEFAULT_MINIMUM_CREDIT_FOR_STATUS)); ?></td>
								<td><?= $this->Form->input('maximumCreditPerSemester', array('label' => 'Maximum Credit For Semester', 'type' => 'number', 'step' => '1', 'min' => DEFAULT_MINIMUM_CREDIT_FOR_STATUS, 'max' => DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER, 'value' => DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER)); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->input('weekCountForAcademicYear', array('label' => 'Week Count For Academic Year', 'type' => 'number', 'step' => '4', 'min' => '4',  'max' => DEFAULT_WEEK_COUNT_FOR_ACADEMIC_YEAR, 'value' => DEFAULT_WEEK_COUNT_FOR_ACADEMIC_YEAR)); ?></td>
								<td><?= $this->Form->input('semesterCountForAcademicYear', array('label' => 'Semester Count For Academic Year', 'type' => 'number', 'step' => '1', 'min' => '1', 'max' => (count(Configure::read('semesters'))), 'value' => $maxSemesters)); ?></td>
								<td><?= $this->Form->input('weekCountForOneSemester', array('label' => 'Week Count For One Semester', 'type' => 'number', 'step' => '4', 'min' => '4', 'max' => (DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER * (count(Configure::read('semesters')))), 'value' => DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER)); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->input('daysAvailableForStaffEvaluation', array('label' => 'Days Available For Staff Evaluation', 'type' => 'number', 'step' => '7', 'min' => '7', 'max' => DEFAULT_DAYS_AVAILABLE_FOR_STAFF_EVALUATION, 'value' => DEFAULT_DAYS_AVAILABLE_FOR_STAFF_EVALUATION)); ?></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td colspan="3" class="vcenter"><?= $this->Form->input('onlyAllowCourseAddForFailedGrades', array('label' => 'Only Allow Course Add For Failed Courses')); ?> <?= ONLY_ALLOW_COURSE_ADD_FOR_FAILED_GRADES !== 'AUTO' && is_numeric(ONLY_ALLOW_COURSE_ADD_FOR_FAILED_GRADES) && ONLY_ALLOW_COURSE_ADD_FOR_FAILED_GRADES == 1  ? '<span  class="rejected">System-wide setting for this field is set to only allow course adds from "Faild course grades". Editing this will not change anything.</span>' : (ONLY_ALLOW_COURSE_ADD_FOR_FAILED_GRADES !== 'AUTO' && is_numeric(ONLY_ALLOW_COURSE_ADD_FOR_FAILED_GRADES) && ONLY_ALLOW_COURSE_ADD_FOR_FAILED_GRADES == 0  ? '<span  class="accepted">System-wide setting for this field is set to "allow any courses to be added". Editing this will not change anything.</span>' : '<span  class="accepted">System-wide setting for this field is set to "Auto" Mode. The system will allow/deny course adds based on the setting of this field.</span>') ?></td>
							</tr>
							<tr>
								<td colspan="3" class="vcenter"><?= $this->Form->input('allowCourseAddFromHigherYearLevelSections', array('label' => 'Allow Course Add From Higher Year Level Sections')); ?> <?= ALLOW_COURSE_ADD_FROM_HIGHER_YEAR_LEVEL_SECTIONS !== 'AUTO' && is_numeric(ALLOW_COURSE_ADD_FROM_HIGHER_YEAR_LEVEL_SECTIONS) && ALLOW_COURSE_ADD_FROM_HIGHER_YEAR_LEVEL_SECTIONS == 0  ? '<span  class="rejected">System-wide setting for this field is set to only allow course adds from "Current Year Level and Below". Editing this will not change anything.</span>' : (ALLOW_COURSE_ADD_FROM_HIGHER_YEAR_LEVEL_SECTIONS !== 'AUTO' && is_numeric(ALLOW_COURSE_ADD_FROM_HIGHER_YEAR_LEVEL_SECTIONS) && ALLOW_COURSE_ADD_FROM_HIGHER_YEAR_LEVEL_SECTIONS == 1  ? '<span  class="accepted">System-wide setting for this field is set to "Allow Course Adds from Any Year Level". Editing this will not change anything.</span>' : '<span  class="accepted">System-wide setting for this field is set to "Auto" Mode. The system will allow/deny course adds based on the setting of this field.</span>') ?></td>
							</tr>
							<tr>
								<td colspan="3"><?= $this->Form->input('allowRegistrationWithoutPayment', array('label' => 'Allow Registration Without Payment')); ?></td>
							</tr>
							<tr>
								<td colspan="3"><?= $this->Form->input('allowMealWithoutCostsharing', array('label' => 'Allow Meal Without Cost-sharing')); ?></td>
							</tr>
							<tr>
								<td colspan="3"><?= $this->Form->input('allowStaffEvaluationAfterGradeSubmission', array('label' => 'Allow Staff Evaluation After Grade Submission')); ?></td>
							</tr>
							<tr>
								<td colspan="3"><?= $this->Form->input('allowStudentsGradeViewWithouInstructorsEvalution', array('label' => 'Allow Students  Grade View Without Instructors Evalution')); ?></td>
							</tr>
							<tr>
								<td colspan="3"><?= $this->Form->input('notifyStudentsGradeByEmail', array('label' => 'Notify Student Grade By Email')); ?></td>
							</tr>
							<tr>
								<td colspan="3" class="vcenter"><?= $this->Form->input('allowGradeReportPdfDownloadToStudents', array('label' => 'Allow Students to Download Grade Report in PDF')); ?> <?= ALLOW_GRADE_REPORT_PDF_DOWNLOAD_TO_STUDENTS !== 'AUTO' && is_numeric(ALLOW_GRADE_REPORT_PDF_DOWNLOAD_TO_STUDENTS) && ALLOW_GRADE_REPORT_PDF_DOWNLOAD_TO_STUDENTS == 0  ? '<span  class="rejected">System-wide setting for this field is set to not allow students to download grade reports in PDF. Editing this will not change anything.</span>' : (ALLOW_GRADE_REPORT_PDF_DOWNLOAD_TO_STUDENTS !== 'AUTO' && is_numeric(ALLOW_GRADE_REPORT_PDF_DOWNLOAD_TO_STUDENTS) && ALLOW_GRADE_REPORT_PDF_DOWNLOAD_TO_STUDENTS == 1  ? '<span  class="accepted">System-wide setting for this field is set to allow students to download Grade Reports in PDF. Editing this will not change anything.</span>' : '<span  class="accepted">System-wide setting for this field is set to "Auto" Mode. The system will allow/deny Grade reports to be downloaded by students in PDF on the setting of this field.</span>') ?></td>
							</tr>
							<tr>
								<td colspan="3" class="vcenter"><?= $this->Form->input('allowRegistrationSlipPdfDownloadToStudents', array('label' => 'Allow Students to Download Grade Report in PDF')); ?> <?= ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS !== 'AUTO' && is_numeric(ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS) && ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS == 0  ? '<span  class="rejected">System-wide setting for this field is set to not allow students to download Registration Slips in PDF. Editing this will not change anything.</span>' : (ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS !== 'AUTO' && is_numeric(ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS) && ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS == 1  ? '<span  class="accepted">System-wide setting for this field is set to allow students to download Registration Slips in PDF. Editing this will not change anything.</span>' : '<span  class="accepted">System-wide setting for this field is set to "Auto" Mode. The system will allow/deny Registration Slips to be downloaded by students in PDF on the setting of this field.</span>') ?></td>
							</tr>
							<tr>
								<td colspan="3" class="vcenter"><?= $this->Form->input('allowStudentsToResetPasswordByEmail', array('label' => 'Allow Students to Reset Password By Email')); ?> <?= ALLOW_STUDENTS_TO_RESET_PASSWORD_BY_EMAIL !== 'AUTO' && is_numeric(ALLOW_STUDENTS_TO_RESET_PASSWORD_BY_EMAIL) && ALLOW_STUDENTS_TO_RESET_PASSWORD_BY_EMAIL == 0  ? '<span  class="rejected">System-wide setting for this field is set to not allow students to reset passwords by email. Editing this will not change anything.</span>' : (ALLOW_STUDENTS_TO_RESET_PASSWORD_BY_EMAIL !== 'AUTO' && is_numeric(ALLOW_STUDENTS_TO_RESET_PASSWORD_BY_EMAIL) && ALLOW_STUDENTS_TO_RESET_PASSWORD_BY_EMAIL == 1  ? '<span  class="accepted">System-wide setting for this field is set to allow students to reset passwords by email. Editing this will not change anything.</span>' : '<span  class="accepted">System-wide setting for this field is set to "Auto" Mode. The system will allow/deny students to reset password by email on the setting of this field.</span>') ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<hr>
				<?= $this->Form->submit(__('Set Setting'), array('id' => 'saveSetting', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	var form_being_submitted = false;

	const validationMessageProgram = document.getElementById('validation-message_program');
	const validationMessageProgramType = document.getElementById('validation-message_program_type');

	var checkForm = function(form) {

		var checkedProgram = $('input[name="data[GeneralSetting][program_id][]"]:checked').length;
		var checkedProgramType = $('input[name="data[GeneralSetting][program_type_id][]"]:checked').length;

		if (checkedProgram == 0 && checkedProgramType == 0) {
			validationMessageProgram.innerHTML = 'At least one Program must be selected';
			validationMessageProgramType.innerHTML = 'At least one Program Type must be selected';
			alert("Program Type and Program Type are not selected, Please Select at least one from both Program and Program Types.");
			form.ProgramID.focus();
			return false;
		} else if (checkedProgram == 0) {
			validationMessageProgram.innerHTML = 'At least one Program must be selected';
			alert("Program is not selected, Please Select at least one Program from the list.");
			form.ProgramID.focus();
			return false;
		} else if (checkedProgramType == 0) {
			validationMessageProgramType.innerHTML = 'At least one Program Type must be selected';
			alert("Program Type is not selected, Please Select at least one Program Type from the list.");
			form.ProgramTypeID.focus();
			return false;
		}

		if (form_being_submitted) {
			alert("Saving General Setting, please wait a moment...");
			form.saveSetting.disabled = true;
			return false;
		}

		form.saveSetting.value = 'Saving General Setting...';
		form_being_submitted = true;
		return true;
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}

	//var checkboxes = document.querySelectorAll('input[type="checkbox"]');
	//var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);


	/* function findFirstSelectedCheckbox(checkboxes) {
		return Array.from(checkboxes).find(
			checkbox => checkbox.checked,
		);
	}

	// get an array of all selected checkboxes
	function findAllSelectedCheckboxes(checkboxes) {
		return Array.from(checkboxes).filter(
			checkbox => checkbox.checked,
		);
	}

	// check if at least one checkbox is checked
	function atLeastOneCheckboxChecked(checkboxes) {
		return Array.from(checkboxes).some(
			checkbox => checkbox.checked,
		);
	}

	const form = document.getElementById('GeneralSettingAddForm');

	const checkboxes = document.querySelectorAll(
		'input[type="checkbox"]',
	);

	const validationMessage = document.getElementById(
		'validation-message',
	);

	// check if at least one checkbox is checked when
	// submitting the form
	form.addEventListener('submit', event => {
		event.preventDefault();

		if (!atLeastOneCheckboxChecked(checkboxes)) {
			console.log('None of the checkboxes are checked');

			validationMessage.innerHTML = 'At least once checkbox must be checked';
			return;
		}

		validationMessage.innerHTML = 'Form submitted successfully';

		console.log('At least one checkbox is checked');
		console.log(findFirstSelectedCheckbox(checkboxes));
		console.log(findAllSelectedCheckboxes(checkboxes));
		console.log(findAllSelectedCheckboxes(checkboxes).length);
	}); */
</script>