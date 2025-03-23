<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Cancel Auto/Manual NG to ' . (!empty($applicable_grades) ? implode(', ', array_keys($applicable_grades)) : 'F') .' Grade Conversions'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div style="margin-top: -20px;">
					<hr>
					<?= $this->Form->create('ExamGrade'); ?>

					<?php 
					//if (!$turn_off_search) { ?>
					<blockquote>
						<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<span style="text-align:justify;" class="fs14 text-gray">This tool will help you to <b style="text-decoration: underline;"><i><?= (!empty($applicable_grades) ? ' cancel autoumatic or manual NG to ' . implode(', ', array_keys($applicable_grades)) . ' conversions converted automatically by the system or manually by the privilaged registrar'  : ''); ?></i>.</b><br>
						<ol class="fs14">
							<li>If the auto NG to F conversion frequent or done untimely by the system, please consult with main registrar to update days available for auto NG to F conversion per program and program type.</li>
							<?= (DELETE_ASSESMENT_AND_ASSOCIATED_RECORDS_ON_NG_CANCELATION == 1 && 0 ? '<li> <b style="text-decoration: underline; color: red;"><i>WARNING: This Server is set to Delete all assesment data, associated Grades, Course registrations, Course Adds while cancelling NG Grades.</i></b></li>' : '<li><i class="rejected">Canceling Auto/Manual NG grade in this page here will retain student regigtration and assesment data if any available, allowing the assigned course instructor to submit grade again by filling the missing assesments.</i></li><li><i>Use <b>"Manage Missing Registration and Wrong NG Managemet"</b> from <a href="/students/student_academic_profile" target="_blank">Student Academic Profile</a> to completely remove all assesment data, associated Grades, Course registrations, Course Adds while cancelling NG Grades.</i></li>'); ?></span>
							<li>The staus of the selected student(s) will be generated from the begining up to the academic year and semester you're managing Auto/Manual NG Conversion cancellation.</li>
						</ol>
					</blockquote>

					<hr>

					<div onclick="toggleViewFullId('ListPublishedCourse')">
						<?php
						if (!empty($examGradeChanges)) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Display Filter</span>
							<?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Hide Filter</span>
							<?php
						} ?>
					</div>
					

					<div id="ListPublishedCourse" style="display:<?= (!empty($examGradeChanges) ? 'none' : 'display'); ?>">
						<fieldset style="padding-top: 25px; padding-bottom: 0px;">
							<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('acadamic_year', array('label' => 'Acadamic Year: ', 'type' => 'select', 'style' => 'width:90%', 'options' => $acyear_list, 'default' => (isset($selected_acadamic_year) ? $selected_acadamic_year : (isset($previous_academicyear) && !empty($previous_academicyear) ? $previous_academicyear : $defaultacademicyear)))); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('semester', array('options' => Configure::read('semesters'), 'type' => 'select', 'style' => 'width:90%', 'label' => 'Semester: '/* , 'empty' => '[ Select Semester ]' */)); ?>
								</div>
								<div class="large-3 columns">
									<h6 class="fs13 text-gray"><b>Program: </b></h6>
									<?= $this->Form->input('program_id', array('id' => 'program_id', 'label' => false, 'type' => 'select', 'options' => $programs, 'multiple' => 'checkbox', 'div' => false)); ?>
								</div>
								<div class="large-3 columns">
									<h6 class="fs13 text-gray"><b>Program Type: </b></h6>
									<?= $this->Form->input('program_type_id', array('options' => $programTypes, 'id' => 'program_type_id', 'label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'div' => false)); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-6 columns">
									<?php
									if (isset($colleges) && !empty($colleges)) {
										echo $this->Form->input('college_id', array('label' => 'College: ', 'style' => 'width:90%'));
									} else if (isset($departments) && !empty($departments)) {
										echo $this->Form->input('department_id', array('label' => 'Department: ', 'style' => 'width:90%'));
									} ?>
								</div>
								
								<div class="large-3 columns">
									<?php // $gradeList['NG'] = 'NG'; 
									if (!empty($applicable_grades)) {
										$applicable_grades = array('0' => '[ Any Converted Grade ]') + $applicable_grades;
									} ?>
									<?= $this->Form->input('grade', array('style' => 'width:90%', 'label' => 'Converted Grade: ', 'type' => 'select', 'options' => $applicable_grades, 'default' => '0')); ?>
								</div>
								<div class="large-3 columns">
								</div>
							</div>
							<hr>
							<?= $this->Form->submit(__('Search for NG Grades'), array('name' => 'listPublishedCourses', 'id' => 'listPublishedCourses', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
						</fieldset>
						
					</div>
					<hr>
					<?php
					//} ?>

					<div id="cancel_ng_form">
						<?php
						if (isset($examGradeChanges) && !empty($examGradeChanges)) {
							$st_count = 0;
							foreach ($examGradeChanges as $td => $studList) {
								$tableHeadDetail = explode('~', $td); ?>
								<!-- <hr> -->

								<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
								<br>

								<div style="overflow-x:auto;">
									<table cellpadding="0" cellspacing="0" class="table">
										<thead>
											<tr>
												<td colspan="10" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
													<span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?= $tableHeadDetail[1]; ?></span>
														<br>
														<span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
															<?= $tableHeadDetail[0]; ?><br>
															<?= $tableHeadDetail[2] .' &nbsp; | &nbsp; ' . $tableHeadDetail[3]; ?>
															<br>
														</span>
													</span>
													<span class="text-black" style="padding-top: 14px; font-size: 14px; font-weight: bold">
														
													</span>
												</td>
											</tr>
											<tr>
												<th class="center"><?= $this->Form->input('select_all', array('type' => 'checkbox', 'id' => 'select-all', 'div' => false, 'label' => false)); ?></th>
												<th class="vcenter" style="width: 3%;">#</th>
												<th class="vcenter" style="width: 22%;">Student Name</th>
												<th class="center" style="width: 5%;">Sex</th>
												<th class="center" style="width: 10%;">Student ID</th>
												<th class="center">Course</th>
												<th class="center">Previous</th>
												<th class="center">Converted</th>
												<th class="center" style="width: 10%;">Date</th>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach ($studList as $key => $grade_change) { ?>
												<tr>
													<td class="center">
														<?php
														$notification_message = '';
														if (isset($grade_change['Student']['haveAssesmentData']) && $grade_change['Student']['haveAssesmentData']) {
															//echo 'x';
															echo $this->Form->input('ExamGrade.' . $st_count . '.ng_grade_with_assesment', array('type' => 'hidden', 'value' => 1));
															$notification_message = '<br><span class="on-process">(Have assesment data)</span>'; // . (' &nbsp; <a href="/examGrades/manage_ng/' . (isset($grade_change['Student']['p_crs_id']) && !empty($grade_change['Student']['p_crs_id']) ? $grade_change['Student']['p_crs_id'] : '') . '" target="_blank">Manage NG</a>');
														} else {
															echo $this->Form->input('ExamGrade.' . $st_count . '.ng_grade_with_assesment', array('type' => 'hidden', 'value' => 0));
														} ?>
														<div style="margin-left: 15%;"><?= $this->Form->input('ExamGrade.' . $st_count . '.gp', array('type' => 'checkbox', 'label' => false, 'id' => 'ExamGrade' . $st_count, 'class' => 'checkbox1')); ?></div>
														<?= $this->Form->input('ExamGrade.' . $st_count . '.id', array('type' => 'hidden', 'value' => $grade_change['ExamGrade']['id'])); ?>
														<?= $this->Form->input('ExamGrade.' . $st_count . '.exam_grade_id', array('type' => 'hidden', 'value' => $grade_change['ExamGrade']['id'])); ?>
														<?= $this->Form->input('ExamGrade.' . $st_count . '.student_id', array('type' => 'hidden', 'value' => $grade_change['Student']['id'])); ?>
													</td>
													<td class="center"><?= ++$st_count; ?></td>
													<td class="vcenter"><?= $grade_change['Student']['first_name'] . ' ' . $grade_change['Student']['middle_name'] . ' ' . $grade_change['Student']['last_name']; ?></td>
													<td class="center"><?= (strcasecmp(trim($grade_change['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($grade_change['Student']['gender']), 'female') == 0 ? 'F' : '')); ?></td>
													<td class="center"><?= $grade_change['Student']['studentnumber']; ?></td>
													<td class="center">
														<?= $grade_change['Course']['course_title'] . ' (' . $grade_change['Course']['course_code'] . ')'; ?>
														<?= (!empty($notification_message) ? $notification_message : ''); ?>
													</td>
													<td class="center"><?= $grade_change['ExamGrade']['grade']; ?></td>
													<td class="center"><?= $grade_change['ExamGradeChange'][0]['grade']; ?></td>
													<td class="center"><?= $this->Time->format("M j, Y", $grade_change['ExamGrade']['created'], NULL, NULL); ?></td>
												</tr>
												<?php
												//$st_count++;
											} ?>
										</tbody>
									</table>
								</div>
								<br>
								<?php
							} ?>

							<?= '<hr>'. $this->Form->submit(__('Cancel NG Grade Conversion'), array('id' => 'cancelNGGrade', 'name' => 'cancelNGGrade', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>

							<?php
						} ?>
					</div>

					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none')
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		else
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
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

	$('#listPublishedCourses').click(function() {
		
		$('#listPublishedCourses').val('Searching for NG Grades...');
		
		$("#cancelNGGrade").attr('disabled', true);
		
		if ($('#cancel_ng_form').length) {
			$("#cancel_ng_form").hide();
		}

		if ($('#cancelNGGrade').length) {
			$("#cancelNGGrade").attr('disabled', true);
		}

		if ($('#select-all').length) {
			$("#select-all").prop('checked', false);
		}

		$('input[type="checkbox"][name^="data[ExamGrade]"]').each(function() {
            const namePatternSelected = /data\[ExamGrade\]\[\d+\]\[gp\]/;
            if (namePatternSelected.test($(this).attr('name')) && $(this).is(':checked')) {
                $(this).prop('checked', false);
            }
        });

	});

	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	var delete_all_assment_data = <?= DELETE_ASSESMENT_AND_ASSOCIATED_RECORDS_ON_NG_CANCELATION; ?>;
	delete_all_assment_data = 0;

	var form_being_submitted = false;

	$(document).ready(function() {
        $('#cancelNGGrade').click(function() {
			var checkboxes = document.querySelectorAll('input[type="checkbox"]');
			var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

			if (!checkedOne) {
				alert('At least one student NG converted grade must be selected to Cancel NG Grade Conversion.');
				validationMessageNonSelected.innerHTML = 'At least one student NG converted grade must be selected to Cancel NG Grade Conversion.';
				return false;
			}

			if (form_being_submitted) {
				alert("Canceling NG Grades, please wait a moment...");
				$('#cancelNGGrade').attr('disabled', true);
				return false;
			}

			var confirmm = false;

			if (delete_all_assment_data == 1) {
            	confirmm = confirm('WARNING!! This Server is set to delete all data including registration and assesments while cancelling NG Grades. Are you sure you want to cancel the selected Student(s) NG grades?, This action is NOT RECOVERABLE AND COMPLETELY DELETES ALL ASSOCIATED ASSESMENT DATA recorded for the selected student(s)?');
			} else {
				confirmm =  confirm('Are you sure you want to cancel NG Grades of the selected student NG grades? Canceling NG here will retain student registration/adds if assesment data is available, allowing the course instructor to submit grade again by filling the missing assesments. If there are selected any NG grades without any assesment, they will be permanently deleted. Are you sure you want to cancel the selected NG grades?');
			}

			if (!form_being_submitted && confirmm) {
				$('#cancelNGGrade').val('Canceling Selected NG Conversions...');
				if ($("#listPublishedCourses").length) {
					$("#listPublishedCourses").attr('disabled', true);
				}
				form_being_submitted = true;
				return true;
			} else {
				return false;
			}
        });
    });

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>