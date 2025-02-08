<!-- <div class="box">
    <div class="box-body">
       <div class="row">
	  		<div class="large-12 columns"> -->
				<?php
				$gradeList = array();
				if (isset($section_and_course_detail['Course']['GradeType']['Grade'])) {
					foreach ($section_and_course_detail['Course']['GradeType']['Grade'] as $key => $value) {
						$gradeList[$value['grade']] = $value['grade'];
					}
					$gradeList['NG'] = 'NG';
					$gradeList['I'] = 'I';
					$gradeList['W'] = 'W';
					$gradeList['DO'] = 'DO';

				}
				echo $this->Form->hidden('PublishedCourse.id', array('value' => $section_and_course_detail['PublishedCourse']['id'])); ?>

				<?php
				if ($view_only) { ?>
					<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>To Manage <?= (isset($selected_course_title_code) ? $selected_course_title_code : 'this Published course'); ?> Exam Result and Grade <?= (isset($selected_section_name) && !empty($selected_section_name) ? '  for ' . $selected_section_name . ' section' : '' ); ?>, <?= (isset($assigned_instructor_fullname_and_username) && !empty($assigned_instructor_fullname_and_username) ? $assigned_instructor_fullname_and_username : ' the assigned instructor'); ?> account should be closed/deactivated by the system administrator. The department can request an account deactivation request for staffs that are on study leave or left the university permanently.</div>
					<?php
				} else { 
					if (isset($grade_scale['error']) || empty($grade_scale)) { 
						if (isset($grade_scale['error'])) { ?>
							<div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;"><span style='margin-right: 15px;'></span><?= $grade_scale['error']; ?></div>
							<?php
						} else { ?>
							<div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;"><span style='margin-right: 15px;'></span>Grade scale for the selected course is not found in the system.</div>
							<?php
						}
					} else { 
						if ((isset($grade_scale) && !empty($grade_scale['Course']['id']) && $grade_scale['Course']['thesis'] == 1 && ($grade_scale['Course']['Curriculum']['program_id'] == PROGRAM_PhD || $grade_scale['Course']['Curriculum']['program_id'] == PROGRAM_POST_GRADUATE)) && isset($grade_scale['GradeType']['used_in_gpa']) && $grade_scale['GradeType']['used_in_gpa'] == 1) { ?>
							<hr>
							<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;"><span style='margin-right: 15px;'></span>Currently, <?= $grade_scale['Course']['course_code_title']; ?> course is set as a <?=$grade_scale['Course']['Curriculum']['program_id'] == PROGRAM_POST_GRADUATE ? 'Thesis/Projecct' : 'Dissertation'; ?> course and associated to "<?= $grade_scale['GradeScale']['name']; ?>" from "<?= $grade_scale['GradeType']['type']; ?>" grading type which uses point values of the awarded grades in CGPA calculations. Please communicate the department and check the correctness of the grade type specified on <?= $grade_scale['Course']['Curriculum']['curriculum_detail']; ?> curriculum before submitting the grades.</div>
							<hr>
							<?php
						} ?>
						<hr>
						<div style="border:1px solid #91cae8; padding:10px;">
							<span><input type="button" value="Show Grade Scale" onclick="showHideGradeScale()" id="ShowHideGradeScale" class="tiny radius button bg-blue"> &nbsp;&nbsp;&nbsp;</span>
							<?php
							if (isset($grade_submission_status) && ($grade_submission_status['grade_submited'] || $grade_submission_status['grade_submited_partially'] ||  $grade_submission_status['grade_submited_fully'])) { ?>
								<span><input type="button" value="Show Grade Distribution" onclick="showHideGradeStatistics()" id="ShowHideGradeDistribution" class="tiny radius button bg-blue"></span>
								<?php
							} ?>
							<div class="row">
								<div class="large-7 columns">
									<!-- AJAX GRADE SCALE LOADING -->
									<div id="GradeScale"></div>
									<!-- END AJAX GRADE SCALE LOADING -->
								</div>
								<div class="large-2 columns">&nbsp;</div>
								<div class="large-3 columns">
									<!-- AJAX GRADE DISTRIBUTION LOADING -->
									<div id="GradeDistribution"></div>
									<!-- END AJAX GRADE DISTRIBUTION LOADING -->
								</div>
							</div>
						</div>
						<hr>
						<?php
					} ?>

					<h6 class="fs14 text-black">Assigned Instructor: <?= (isset($assigned_instructor_fullname_with_rank) && !empty($assigned_instructor_fullname_with_rank) ? $assigned_instructor_fullname_with_rank : 'Not assigned'); ?></h6>

					<h6 class="fs14 text-gray">Please select students and enter corresponding grade</h6>

					<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
					<br>

					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<?php
								if (isset($selected_section_detailed) && !empty($selected_section_detailed) && is_array($selected_section_detailed)) { ?>
									<tr>
										<td colspan="6" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
											<span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?= $selected_section_detailed[5]; ?></span>
												<br>
												<span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
													<?= ($selected_section_detailed[4] != 'Pre/1st' ? $selected_section_detailed[1] : $selected_section_detailed[0]) . ' &nbsp; | &nbsp; ' .  $selected_section_detailed[2] . ' &nbsp; | &nbsp; ' . $selected_section_detailed[3];  ?><br>
													<?= isset($selected_course_title_code) && !empty($selected_course_title_code) ? 'Course: <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">' . $selected_course_title_code . '</span><br>' : ''; ?>
													<?= (isset($selected_section_detailed[6]) ? 'Curriculum: <span class="text-gray" style="font-size: 13px; font-weight: normal"><i>'. $selected_section_detailed[6] . '</i></span>' : ''); ?>
													<br>
												</span>
											</span>
											<span class="text-black" style="padding-top: 14px; font-size: 14px; font-weight: bold">
												
											</span>
										</td>
									</tr>
									<?php

								} ?>
								<tr>
									<th style="width:4%" class="center"><div style="margin-left: 25%;"><?= $this->Form->input('select_all', array('type' => 'checkbox', 'id' => 'select-all', 'div' => false, 'label' => false)); ?></div></th>
									<th style="width:3%" class="center">#</th>
									<th class="vcenter">Student Name</th>
									<th class="center">Sex</th>
									<th class="center">Student ID</th>
									<th class="center">Grade</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$st_count = 0;
								$checkBoxCount = 0;

								if (!empty($student_course_register_and_adds['register'])) { ?>
									<tr>
										<td colspan='2'></td>
										<td colspan='4'>Registered Students</td>
									</tr>
									<?php
									foreach ($student_course_register_and_adds['register'] as $key => $student) {
										$st_count++; ?>
										<tr>
											<td class="center">
												<?php
												if (empty($student['ExamGrade'])) {
													if (isset($student['CourseRegistration']['id']) && !empty($student['CourseRegistration']['id'])) {
														$checkBoxCount++;
														echo '<div style="margin-left: 25%;">' . $this->Form->input('ExamResult.' . $st_count . '.gp', array('type' => 'checkbox', 'class' => 'checkbox1', 'label' => false, 'id' => 'StudentSelection' . $st_count)). '</div>';
														echo $this->Form->input('ExamResult.' . $st_count . '.course_registration_id', array('type' => 'hidden', 'value' => $student['CourseRegistration']['id']));
													} else {
														echo '**';
													}
												} else {
													echo '**';
												}  ?>
											</td>
											<td class="center"><?= $st_count; ?></td>
											<td class="vcenter"><?= $student['Student']['first_name'] . ' ' . $student['Student']['middle_name'] . ' ' . $student['Student']['last_name']; ?></td>
											<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' :(strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : trim($student['Student']['gender']))); ?></td>
											<td class="center"><?= $student['Student']['studentnumber']; ?></td>
											<td class="center"><?= (isset($student['LatestGradeDetail']['ExamGrade']) && !empty($student['LatestGradeDetail']['ExamGrade'])) ? '<b>'. $student['LatestGradeDetail']['ExamGrade']['grade'] . '</b>' : '<br style="line-height: 0.5;">' . $this->Form->input('ExamResult.' . $st_count . '.grade', array('label' => false, 'type' => 'select', 'options' => $gradeList, 'empty' => ' [ Select ] ', 'disabled' => (isset($student['CourseRegistration']['id']) && !empty($student['CourseRegistration']['id']) ? false: true))); ?></td>
										</tr>
										<?php
									}
								}

								if (!empty($student_course_register_and_adds['add'])) { ?>
									<tr>
										<td colspan='2'></td>
										<td colspan='4'>Added Students</td>
									</tr>
									<?php
									foreach($student_course_register_and_adds['add'] as $key => $course) {
										$st_count++; ?>
										<tr>
											<td class="center">
												<?php 
												if (empty($course['ExamGrade'])) {
													if (isset($student['CourseAdd']['id']) && !empty($student['CourseAdd']['id'])) {
														$checkBoxCount++;
														echo '<div style="margin-left: 15%;">' . $this->Form->input('ExamResult.' . $st_count . '.gp', array('type' => 'checkbox', 'class' => 'checkbox1', 'label' => false, 'id' => 'StudentSelection' . $st_count)) . '</div>';
														echo $this->Form->input('ExamResult.' . $st_count . '.course_add_id', array('type' => 'hidden', 'value' => $student['CourseAdd']['id']));
													} else {
														echo '**';
													}

												} else {
													echo '**';
												} ?>
											</td>
											<td class="center"><?= $st_count; ?></td>
											<td class="vcenter"><?= $student['Student']['first_name'] . ' ' . $student['Student']['middle_name'] . ' ' . $student['Student']['last_name']; ?></td>
											<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' :(strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : trim($student['Student']['gender']))); ?></td>
											<td class="center"><?= $student['Student']['studentnumber']; ?></td>
											<td class="center"><?= (isset($student['LatestGradeDetail']['ExamGrade']) && !empty($student['LatestGradeDetail']['ExamGrade'])) ? '<b>' . $student['LatestGradeDetail']['ExamGrade']['grade'] . '</b>' : '<br style="line-height: 0.5;">' . $this->Form->input('ExamResult.' . $st_count . '.grade', array('label' => false, 'type' => 'select', 'options' => $gradeList, 'empty' => ' [ Select] ', 'disabled' => (isset($student['CourseAdd']['id']) && !empty($student['CourseAdd']['id']) ? false : true))); ?></td>
										</tr>
										<?php
									}
								} ?>
							</tbody>
						</table>
						</div>
						<hr>
						<?= $this->Form->submit(__('Save Selected Grades ', true), array('name' => 'saveGrade', 'id' => 'saveGrade', 'class' => 'tiny radius button bg-blue', 'disabled' => ($checkBoxCount == 0 ? true: false), 'div' => false)); ?>
					<?php 
				} ?>
	  		<!-- </div>
		</div>
    </div>
</div> -->

<script>
	
	var form_being_submitted = false;

	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	$('#saveGrade').click(function(event) {

		//$('form').removeAttr('novalidate');
		$('form').attr('novalidate', 'novalidate');

		var isValid = true;

		//var minuteNumber = $('#minuteNumber').val();

		/* if (minuteNumber == '') { 
			//form.minuteNumber.focus();
			event.preventDefault(); // Prevent form submission
			$('#minuteNumber').focus();
			isValid = false;
			return false;
		} */

		let atLeastOneSelectedGrade = false;

        $('select[name^="data[ExamResult]"]').each(function() {
            const namePattern = /data\[ExamResult\]\[\d+\]\[grade\]/;
            if (namePattern.test($(this).attr('name')) && $(this).val()) {
                atLeastOneSelectedGrade = true;
                return false; // Break out of the loop
            }
        });

		

		//alert(atLeastOneSelected);

        if (!atLeastOneSelectedGrade) {
            event.preventDefault(); // Prevent form submission
			isValid = false;
            alert('Please select at least one student grade before submitting the form.');
			validationMessageNonSelected.innerHTML = 'Please select at least on student grade before submitting the form.';
        }

		let nonEmptyGradeCount = 0;
		let nonEmptySelectedCount = 0;

		$('select[name^="data[ExamResult]"]').each(function() {
			const namePatternGrade = /data\[ExamResult\]\[\d+\]\[grade\]/;
			if (namePatternGrade.test($(this).attr('name')) && $(this).val()) {
				nonEmptyGradeCount++;
			}
		});

		$('input[type="checkbox"][name^="data[ExamResult]"]').each(function() {
			const namePatternSelected = /data\[ExamResult\]\[\d+\]\[gp\]/;
			if (namePatternSelected.test($(this).attr('name')) && $(this).is(':checked')) {
				nonEmptySelectedCount++;
			}
		});

		//alert(nonEmptyGradeCount);
		//alert(nonEmptySelectedCount);

		if (nonEmptyGradeCount != nonEmptySelectedCount) {
			event.preventDefault(); // Prevent form submission
			var student_lebel = (nonEmptySelectedCount > 1 ? 'students' : 'student');
			var grade_lebel = (nonEmptyGradeCount > 1 ? 'grades' : 'grade');
			isValid = false;
            alert('You selected ' + nonEmptySelectedCount + ' ' + student_lebel + ' but selected ' + nonEmptyGradeCount + ' ' + grade_lebel + ', please correct your selection.');
			validationMessageNonSelected.innerHTML = 'You selected ' + nonEmptySelectedCount + ' ' + student_lebel + ' but selected ' + nonEmptyGradeCount + ' ' + grade_lebel + ', please correct your selection.';
		}

		// remove the validation cheking of the form after minitue number and atleast on student grade is selected
		$('form').attr('novalidate', 'novalidate');

		if (form_being_submitted) {
			alert("Managing NG grade for the selected students, please wait a moment...");
			$('#saveGrade').attr('disabled', true);
			return false;
		}

		if (!form_being_submitted && isValid) {
			$('#saveGrade').val('Saving Selected Grades...');
			form_being_submitted = true;
			return true;
		} else {
			return false;
		}
	});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>