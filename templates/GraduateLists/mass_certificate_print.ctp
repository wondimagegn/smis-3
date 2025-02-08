<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class=" icon-print" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Mass Certificate Printing'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div style="margin-top: -30px;"><hr></div>
				
				<div class="form">
					<?= $this->Form->create('GraduateList', array('data-abide', 'onSubmit' => 'return checkForm(this);')); ?>
					<blockquote>
						<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<p style="text-align:justify;"><span class="fs14"> This tool will help you to mass print student certificates. <!-- By providing a search criteria you can find the list of students who are graduated. -->
						<ol>
							<li>While Printing student copies, Identify transfered students from other universities who have course exemptions(if any) and use Individual Student Copy Option for them. Using individual student copy for such students will remove possible duplicates of exempted courses in each page of the student copy.</li>
						</ol>
					</p>
					</blockquote>
					<hr>

					<div onclick="toggleViewFullId('ListPublishedCourse')">
						<?php
						if (!empty($student_lists)) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
							<?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
							<?php
						} ?>
					</div>
					<div id="ListPublishedCourse" style="display:<?= (!empty($student_lists) ? 'none' : 'display'); ?>">

						<fieldset style="padding-bottom: 5px;">
							<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => 'Program: ', 'type' => 'select', 'options' => $programs, 'default' => $default_program_id, 'style' => 'width:90%',)); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => 'Program Type: ', 'type' => 'select', 'options' => $program_types, 'style' => 'width:90%', 'default' => $default_program_type_id)); ?>
								</div>
								<div class="large-6 columns">	
									<?= $this->Form->input('department_id', array('id' => 'DepartmentID', 'class' => 'fs14', 'label' => 'Department: ', 'type' => 'select', 'options' => $departments, 'default' => $default_department_id, 'style' => 'width:90%')); ?>
								</div>
							</div>
							<div class="row">
								<?php
								if (!isset($this->data['GraduateList'])) { ?>
									<div class="AcadamicYearDiv large-3 columns" id="AcadamicYearDiv" style="display:none">
										<?= $this->Form->input('acadamic_year', array('id' => 'AdmissionYear', 'label' => 'Admission Year: ', 'class' => 'fs14', 'style' => 'width:90%', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?>
									</div>
									<?php
								}  else { ?>
									<div class="AcadamicYearDiv large-3 columns" id="AcadamicYearDiv" style="<?php isset($this->data['GraduateList']['graduated']) && $this->data['GraduateList']['graduated'] == '0' ? 'display:block;' : 'display:none;' ?>">
										<?= $this->Form->input('acadamic_year', array('id' => 'AdmissionYear', 'label' => 'Admission Year: ', 'class' => 'fs14', 'style' => 'width:90%', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?>
									</div>
									<?php
								} ?>
								<div class="large-3 columns">	
									<?= $this->Form->input('name', array('id' => 'name', 'class' => 'fs14', 'label' => 'Student Name: ', 'placeholder' => 'Optional Student Name...', 'style' => 'width:90%', 'maxlength' => 25)); ?>
								</div>
								<div class="large-6 columns">
									<?= $this->Form->input('studentnumber', array('id' => 'studentnumber', 'class' => 'fs14', 'label' => 'Student ID: ',  'placeholder' => 'Optional Student ID...','style' => 'width:50%', 'maxlength' => 25)); ?>
								</div>
							</div>
							<?php
							if (!isset($this->data['GraduateList'])) { ?>
								<div class="GraduateDateDiv row" id="GraduateDateDiv" style="display:block;">
									<?php
									$yFrom = Configure::read('Calendar.graduateListStartYear');
									$yTo = date('Y');
									?>
									<div class="large-6 columns">
										<?= $this->Form->input('graduate_date_from', array('label' => 'Graduate From: ', 'type' => 'date', 'minYear' => $yFrom, 'maxYear' => $yTo, 'default' => false, 'style' => 'width:25%')); ?>
									</div>
									<div class="large-6 columns">
										<?= $this->Form->input('graduate_date_to', array('label' => 'Graduate To: ', 'type' => 'date', 'minYear' => $yFrom, 'maxYear' => $yTo, 'default' =>  date('Y-m-d'), 'style' => 'width:25%')); ?>
									</div>
								</div>
								<?php
							}  else { ?>
								<div class="GraduateDateDiv row" id="GraduateDateDiv" style="<?php isset($this->data['GraduateList']['graduated']) && $this->data['GraduateList']['graduated'] == '1' ? 'display:block;' : 'display:none;' ?>">
									<?php
									$yFrom = Configure::read('Calendar.graduateListStartYear');
									$yTo = date('Y');
									?>
									<div class="large-6 columns">
										<?= $this->Form->input('graduate_date_from', array('label' => 'Graduate From: ', 'type' => 'date', 'minYear' => $yFrom, 'maxYear' => $yTo, 'default' => false, 'style' => 'width:25%')); ?>
									</div>
									<div class="large-6 columns">
										<?= $this->Form->input('graduate_date_to', array('label' => 'Graduate To: ', 'type' => 'date', 'minYear' => $yFrom, 'maxYear' => $yTo, 'default' =>  date('Y-m-d'), 'style' => 'width:25%')); ?>
									</div>
								</div>
								<?php
							} ?>

							<div class="row">
								<div class="large-6 columns">
									<?= $this->Form->input('certificate_type', array('label' => 'Certificate Type: ', 'type' => 'select', 'id' => 'certificateType', 'onchange' => 'toggleFields("certificateType")', 'style' => 'width:50%', 'div' => false, 'default' => 'graduation_certificate', 'options' => $certificate_type_options)); ?>
								</div>
								<div class="large-6 columns">
									<?= $this->Form->input('graduated', array('label' => 'Graduated: ', 'type' => 'select', 'id' => 'graduated', 'onchange' => ' toggleACYField()', 'style' => 'width:60px', 'div' => false, 'style' => 'width:45%', 'default' => 1, 'options' => array(1 => 'Yes', 0 => 'No'))); ?>
								</div>
							</div>
							<hr>
							<?= $this->Form->submit('List Students', array('name' => 'listStudentsForCertficatePrint', 'id' => 'listStudents', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
						</fieldset>
					</div>
					<hr>
					<?php
					if (isset($student_lists) && empty($student_lists)) { ?>
						<div id="flashMessage" class="info-box info-message"><span style='margin-right: 15px;'></span>There is no student in the selected criteria</div>
						<?php
					} else if (isset($student_lists) && !empty($student_lists)) {

						if (isset($this->data['GraduateList']['certificate_type']) &&  $this->data['GraduateList']['certificate_type'] == "student_copy") { ?>
							<div onclick="toggleViewFullId('StudentCopyDisplaySetting')" class="StudentCopyDisplaySettingDiv" style="<?php isset($this->data['GraduateList']['certificate_type']) && $this->data['GraduateList']['certificate_type'] == "student_copy" ? 'display:block;' : 'display:none;' ?>">
								<?php
								if (!empty($student_lists)) {
									echo $this->Html->image('plus2.gif', array('id' => 'DisplaySettingImg')); ?>
									<span style="font-size:10px; vertical-align:top; font-weight:bold" id="DisplaySetting"> Display Student Copy Setting</span>
									<?php
								} else {
									echo $this->Html->image('minus2.gif', array('id' => 'DisplaySettingImg')); ?>
									<span style="font-size:10px; vertical-align:top; font-weight:bold" id="DisplaySetting"> Hide Student Copy Setting</span>
									<?php
								} ?>
							</div>
							<?php
						} else { ?>
							<div onclick="toggleViewFullId('StudentCopyDisplaySetting')" id="StudentCopyDisplaySettingDiv" class="StudentCopyDisplaySettingDiv" style="display:none;">
								<?php
								if (!empty($student_lists)) {
									echo $this->Html->image('plus2.gif', array('id' => 'DisplaySettingImg')); ?>
									<span style="font-size:10px; vertical-align:top; font-weight:bold" id="DisplaySetting"> Display Student Copy Setting</span>
									<?php
								} else {
									echo $this->Html->image('minus2.gif', array('id' => 'DisplaySettingImg')); ?>
									<span style="font-size:10px; vertical-align:top; font-weight:bold" id="DisplaySetting"> Hide Student Copy Setting</span>
									<?php
								} ?>
							</div>
							<?php
						} ?>

						<div id="StudentCopyDisplaySetting" class="StudentCopyDisplaySettingDiv" style="display:none;">
							<br>
							<table cellpadding="0" cellspacing="0" class="fs13 table-borderless">
								<tr>
									<td style="width:20%">Semesters on One Side:</td>
									<td style="width:80%"><?= $this->Form->input('Setting.no_of_semester', array('label' => false, 'type' => 'select', 'style' => 'width:150px', 'div' => false, 'default' => 3, 'options' => array(2 => 2, 3 => 3, 4 => 4, 5 => 5))); ?><span style="font-size:12px; padding-left:10px;">(Number of semesters to display on one side of the student copy)</span></td>
								</tr>
								<tr>
									<td>Text Padding:</td>
									<td><?= $this->Form->input('Setting.course_justification', array('label' => false, 'type' => 'select', 'style' => 'width:150px', 'div' => false, 'default' => 1, 'options' => array(0 => 0, 1 => 1, 2 => 2))); ?> <span style="font-size:12px; padding-left:10px;">(The space around each text)</span></td>
								</tr>
								<tr>
									<td>Font Size:</td>
									<td><?= $this->Form->input('Setting.font_size', array('label' => false, 'type' => 'select', 'style' => 'width:150px', 'div' => false, 'default' => 29, 'options' => $font_size_options)); ?><span style="font-size:12px; padding-left:10px;"></span></td>
								</tr>
							</table>
						</div>
						<hr>

						<h6 class="fs14 text-gray">Please select student(s) for whom you want to prepare <span id="selectedCertificateType"></span>.</h6>
						<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
						<br>

						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<td class="center" style="width:3%"> &nbsp; <?= $this->Form->input('select_all', array('type' => 'checkbox', 'id' => 'select-all', 'label' => false)); ?><!-- <label for="select-all">&nbsp;&nbsp;All</label> --> </td>
										<td class="center" style="width:3%">#</td>
										<td class="vcenter" style="width:25%">Student Name</td>
										<td class="center" style="width:8%">Sex</td>
										<td class="vcenter" style="width:10%">Student ID</td>
										<td class="center" style="width:15%">Program</td>
										<td class="vcenter">Department</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$st_count = 0;
									$counter = 1;
									foreach ($student_lists as $key => $student) { 
										//debug($student); 
										//debug(!empty($student['Student']['department_id']) && !empty($departmentsss) ? $departmentsss[$student['Student']['department_id']] : '' );
										$st_count++; ?>
										<tr>
											<td class="center">
												<div style="margin-left: 15%;"><?= $this->Form->input('Student.' . $st_count . '.gp', array('type' => 'checkbox', 'label' => false, 'class' => 'checkbox1', 'id' => 'Student' . $st_count)); ?></div>
												<?= $this->Form->input('Student.' . $st_count . '.student_id', array('type' => 'hidden', 'value' => $student['Student']['id'])); ?>
											</td>
											<td class="center"><?= $counter++; ?></td>
											<td class="vcenter"><?= $student['Student']['full_name']; ?></td>
											<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : $student['Student']['gender'])); ?></td>
											<td class="vcenter"><?= $student['Student']['studentnumber']; ?></td>
											<td class="center"><?= (isset($student['Program']['name']) && !empty($student['Program']['name']) ? $student['Program']['name'] : (!empty($student['Student']['program_id']) && !empty($programs) ? $programs[$student['Student']['program_id']] : '' )); ?></td>
											<td class="vcenter"><?= (isset($student['Department']['name']) && !empty($student['Department']['name']) ? $student['Department']['name'] : (!empty($student['Student']['department_id']) && !empty($departmentsss) ? $departmentsss[$student['Student']['department_id']] : '' )); ?></td>
										</tr>
										<?php
									} ?>
								</tbody>
							</table>
						</div>
						<hr>

						<?php
						if (isset($this->data['GraduateList']) && $this->data['GraduateList']['certificate_type'] == 'temporary_degree' ) {
							if ($this->data['GraduateList']['program_id'] != PROGRAM_UNDEGRADUATE && $this->data['GraduateList']['program_id'] != PROGRAM_PGDT) { ?>
								<div class="row">
									<div class="large-3 columns">
									<?= $this->Form->input('have_agreement', array('type' => 'checkbox')); ?>
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
							} 
						} ?>

						<?= $this->Form->submit(__('Get Certificate', true), array('name' => 'getStudentCertficate', 'id' => 'getStudentCertficate', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
						<?php
						//debug($students_in_section);
					} ?>
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	/*
	var number_of_students = <?= (isset($student_lists) ? count($student_lists) : 0); ?>;
	function check_uncheck(id) {
		var checked = ($('#'+id).attr("checked") == 'checked' ? true : false);
		for(i = 1; i <= number_of_students; i++) {
			$('#Student'+i).attr("checked", checked);
		}
	}
	*/

	$('#selectedCertificateType').html($("#certificateType option:selected").text());

	function toggleFields(id) {
		$('#selectedCertificateType').html($("#certificateType option:selected").text());

		if ($("#" + id).val() == 'student_copy') {
			$(".StudentCopyDisplaySettingDiv").show();
		} else {
			$(".StudentCopyDisplaySettingDiv").hide();
		}

		if ($("#certificateType").val() != 'student_copy' && $("#graduated").val() == 0) {
			//alert($("#graduated").val());
			$("#graduated").val(1);
		}
	}


	if ($("#graduated").val() == '1') {
		$(".AcadamicYearDiv").hide();
		$(".GraduateDateDiv").show();
	} else {
		$(".AcadamicYearDiv").show();
		$(".GraduateDateDiv").hide();
	}

	function toggleACYField() {
		
		if ($("#graduated").val() == '1') {
			$(".AcadamicYearDiv").hide();
			$(".GraduateDateDiv").show();
		} else {
			$(".AcadamicYearDiv").show();
			$(".GraduateDateDiv").hide();
		}
	}

	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none') {
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		} else {
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		}
		$('#c' + obj.id).toggle("slow");
	}

	function toggleViewFullId(id) {
		if ($('#' + id).css("display") == 'none') {
			$('#' + id + 'Img').attr("src", '/img/minus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append(' Hide Filter');
		} else {
			$('#' + id + 'Img').attr("src", '/img/plus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append(' Display Filter');
		}
		$('#' + id).toggle("slow");
	}

	var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {

		if (form.graduated.value == 0 && form.certificateType.value != 'student_copy') { 
			alert('You can\'t select "No" for Graduated field while selecting "' + form.certificateType.options[form.certificateType.selectedIndex].text + '" Certificate Type.');
			//form.graduated.setAttribute('value', '1');
			form.graduated.value = '1';
			form.graduated.focus();
			return false;
		}

		if (form_being_submitted) {
			alert("Getting Student List, please wait a moment...");
			form.listStudents.disabled = true;
			return false;
		}

		form.listStudents.value = 'Getting Student List...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	var generating_ceriificate = false;

	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	$('#getStudentCertficate').click(function() {
		var isValid = true;
		//var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="data[Student]"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

		var cerType = $("#certificateType option:selected").text();

		//alert(checkedOne);
		//alert(cerType);

		if (!checkedOne) {
			alert('At least one student must be selected for ' + cerType + ' printing.');
			validationMessageNonSelected.innerHTML = 'At least one student must be selected for ' + cerType + ' printing.';
			isValid = false;
			return false;
		}

		if (generating_ceriificate) {
			alert('Generating ' + cerType + ', please wait a moment...');
			$('#getStudentCertficate').attr('disabled', true);
			isValid = false;
			return false;
		}

		if (!generating_ceriificate && isValid) {
			$('#getStudentCertficate').val('Generating ' + cerType + '...');
			generating_ceriificate = true;
			isValid = true
			return true;
		} else {
			return false;
		}
	});

	/* if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	} */
</script>