<!-- <div class="box">
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns"> -->
				<hr>
				<?php
				if (isset($section_and_course_detail['CourseInstructorAssignment'][0]['Staff']) && !empty($section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['id'])) { ?>
					<h6 class="fs14 text-black"><span class="text-gray">Instructor: </span><?= $section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. ' . $section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['full_name'] . ' (' . $section_and_course_detail['CourseInstructorAssignment'][0]['Staff']['Position']['position'] . ')'; ?></h6>
					<h6 class="fs14 text-black"><span class="text-gray">Course:  </span><?= $section_and_course_detail['Course']['course_code_title'] . '  &nbsp;&nbsp; | &nbsp;&nbsp; '. (empty($section_and_course_detail['Section']['Curriculum']['id']) ? 'Credit' : (count(explode('ECTS', $section_and_course_detail['Section']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit')) . ': ' . $section_and_course_detail['Course']['credit'] ; ?></h6>
					<?php
				} else { ?>
					<h6 class="fs14 text-black"><span class="text-gray">Instructor:  </span><span class="text-red">Not Assigned</span></h6>
					<h6 class="fs14 text-black"><span class="text-gray">Course:  </span><?= $section_and_course_detail['Course']['course_code_title'] . '  &nbsp;&nbsp; | &nbsp;&nbsp; '. (empty($section_and_course_detail['Section']['Curriculum']['id']) ? 'Credit' : (count(explode('ECTS', $section_and_course_detail['Section']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit')) . ': ' . $section_and_course_detail['Course']['credit'] ; ?></h6>
					<?php
				} ?>
				<hr>

				<!-- <h6 class="fs14 text-gray">Please select Student/s and rollback the course back to the instructor for grade resubmission.</h6> -->

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
					<div id="flashMessage" class="info-box info-message"><span></span>This course grade entry is possible by the assigned instructor. Incase if s/he the instructor left the department his/her account should be closed by the system administrator so that you will able to enter grade.</div>
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
							<span><input type="button" value="Show Grade Distribution" onclick="showHideGradeStatistics()" id="ShowHideGradeDistribution" class="tiny radius button bg-blue"></span>
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

					<h6 class="fs14 text-gray">Please select students and you want to rollback grade submission</h6>

					<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
					<br>

					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table fs14">
							<thead>
								<tr>
                                    <td colspan="6" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5">
                                        <span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?= $section_and_course_detail['Section']['name'] . ' ' . (isset($section_and_course_detail['Section']['YearLevel']['name'])  ?  ' (' . $section_and_course_detail['Section']['YearLevel']['name']  : ' (Pre/1st') . ', ' . $section_and_course_detail['Section']['academicyear'] . ')'; ?></span>
                                        	<br>
                                            <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold"> 
                                             	<?= (isset($section_and_course_detail['Section']['Department']) && !empty($section_and_course_detail['Section']['Department']['name']) ? $section_and_course_detail['Section']['Department']['name'] :  $section_and_course_detail['Section']['College']['name'] . ' Pre/Freshman'); ?> <?= (isset($section_and_course_detail['Section']['Program']['name']) && !empty($section_and_course_detail['Section']['Program']['name']) ? ' &nbsp; | &nbsp; ' . $section_and_course_detail['Section']['Program']['name'] : ''); ?> <?= (isset($section_and_course_detail['Section']['ProgramType']['name']) && !empty($section_and_course_detail['Section']['ProgramType']['name']) ? ' &nbsp; | &nbsp; ' . $section_and_course_detail['Section']['ProgramType']['name'] : ''); ?>
                                                <br>
                                            </span>
                                        </span>
                                        <span class="text-gray" style="padding-top: 15px; font-size: 13px; font-weight: bold"> 
                                            Curriculum: <?= (!empty($section_and_course_detail['Section']['Curriculum']['id']) ? $section_and_course_detail['Section']['Curriculum']['name'] . ' - ' . $section_and_course_detail['Section']['Curriculum']['year_introduced'] . ' (' .(empty($section_and_course_detail['Section']['Curriculum']['id']) ? 'Credit' : (count(explode('ECTS', $section_and_course_detail['Section']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit')) . ')' : 'N/A'); ?> 
											<!-- <br style="line-height: 0.35;"> -->
                                        </span>
                                    </td>
                                </tr>
								<tr>
									<th class="center" style="width: 4%;"><div style="margin-left: 25%;"><?= $this->Form->input('select_all', array('type' => 'checkbox', 'id' => 'select-all', 'div' => false, 'label' => false)); ?></div></th>
									<th class="center" style="width: 3%;">#</th>
									<th class="vcenter">Student Name</th>
									<th class="center">Sex</th>
									<th class="center">Student ID</th>
									<th class="center">Grade</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$st_count = 0;
								$checkBoxCount = 0; ?>
								<tr>
									<td colspan='2'></td>
									<td colspan='4' class="vcenter"><b>Registered Students</b></td>
								</tr>
								<?php
								//debug($student_course_register_and_adds['register']);
								foreach ($student_course_register_and_adds['register'] as $key => $student) {
									//debug($student);
									if ($student['Student']['graduated'] == 0) {
										$st_count++; ?>
										<tr>
											<td class="center">
												<?php
												if (isset($student['ExamGrade']) && !empty($student['ExamGrade']) && isset($student['AnyExamGradeIsOnProcess']) && !$student['AnyExamGradeIsOnProcess']) {
													$checkBoxCount++;
													echo '<div style="margin-left: 25%;">' . $this->Form->input('ExamResult.' . $st_count . '.gp', array('type' => 'checkbox', 'class' => 'checkbox1', 'label' => false, 'id' => 'StudentSelection' . $st_count)) . '</div>';
													echo $this->Form->input('ExamResult.' . $st_count . '.exam_grade_id', array('type' => 'hidden', 'value' => $student['LatestGradeDetail']['ExamGrade']['id']));
													echo $this->Form->input('ExamResult.' . $st_count . '.student_id', array('type' => 'hidden', 'value' => $student['Student']['id']));
												} else {
													echo '**';
												} ?>
											</td>
											<td class="center"><?= $st_count; ?></td>
											<td class="vcenter"><?= $student['Student']['first_name'] . ' ' . $student['Student']['middle_name'] . ' ' . $student['Student']['last_name']; ?></td>
											<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' :(strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : $student['Student']['gender'])); ?></td>
											<td class="center"><?= $student['Student']['studentnumber']; ?></td>
											<td class="center"><?= (isset($student['LatestGradeDetail']['ExamGrade']) && !empty($student['LatestGradeDetail']['ExamGrade']['grade']) && isset($student['AnyExamGradeIsOnProcess']) && !$student['AnyExamGradeIsOnProcess'] ? $student['LatestGradeDetail']['ExamGrade']['grade'] : (isset($student['AnyExamGradeIsOnProcess']) && $student['AnyExamGradeIsOnProcess'] ? 'On Process' : '--')); ?></td>
										</tr>
										<?php
									}
								}

								if (!empty($student_course_register_and_adds['add'])) { ?>
									<tr>
										<td colspan='2'></td>
										<td class="vcenter" colspan='4'><b>Added Students</b></td>
									</tr>
									<?php
									foreach ($student_course_register_and_adds['add'] as $key => $student) {
										if ($student['Student']['graduated'] == 0) {
											$st_count++; ?>
											<tr>
												<td class="center">
													<?php
													if (isset($student['ExamGrade']) && !empty($student['ExamGrade']) && isset($student['AnyExamGradeIsOnProcess']) && !$student['AnyExamGradeIsOnProcess']) {
														$checkBoxCount++;
														echo '<div style="margin-left: 25%;">' . $this->Form->input('ExamResult.' . $st_count . '.gp', array('type' => 'checkbox', 'class' => 'checkbox1', 'label' => false, 'id' => 'StudentSelection' . $st_count)) . '</div>';
														echo $this->Form->input('ExamResult.' . $st_count . '.exam_grade_id', array('type' => 'hidden', 'value' => $student['LatestGradeDetail']['ExamGrade']['id']));
														echo $this->Form->input('ExamResult.' . $st_count . '.student_id', array('type' => 'hidden', 'value' => $student['Student']['id']));
													} else {
														echo '**';
													} ?>
												</td>
												<td class="center"><?= $st_count; ?></td>
												<td class="vcenter"><?= $student['Student']['first_name'] . ' ' . $student['Student']['middle_name'] . ' ' . $student['Student']['last_name']; ?></td>
												<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' :(strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : $student['Student']['gender'])); ?></td>
												<td class="center"><?= $student['Student']['studentnumber']; ?></td>
												<td class="center"><?= (isset($student['LatestGradeDetail']['ExamGrade']) && !empty($student['LatestGradeDetail']['ExamGrade']['grade']) && isset($student['AnyExamGradeIsOnProcess']) && !$student['AnyExamGradeIsOnProcess'] ? $student['LatestGradeDetail']['ExamGrade']['grade'] : (isset($student['AnyExamGradeIsOnProcess']) && $student['AnyExamGradeIsOnProcess'] ? 'On Process' : '--')); ?></td>
											</tr>
											<?php
										}
									}
								} ?>
							</tbody>
						</table>
					</div>
					<hr>

					<?php
					if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $checkBoxCount > 0) { ?>
						<?= $this->Form->submit(__('Rollback Selected Student Grades', true), array('name' => 'rollback', 'id' => 'SubmitID','class' => 'tiny radius button bg-blue', 'div' => false)); ?>
						<?php
					}
					if (!$st_count) { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No Grades are found for Rolling Back. Either there are no students registered/added or all students in the section are graduated.</div>
						<?php
					}
				} ?>
			<!-- </div>
		</div>
	</div>
</div> -->

<script type="text/javascript">

	var form_being_submitted = false;

	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	$(document).ready(function() {
        $('#SubmitID').click(function() {
			var checkboxes = document.querySelectorAll('input[type="checkbox"]');
			var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

			var acy_and_semester =  document.getElementById("AcadamicYear").value + ', semester: ' + document.getElementById("Semester").value;

			if (!checkedOne) {
				alert('At least one student must be selected to rollback grade submission!');
				validationMessageNonSelected.innerHTML = 'At least one student must be selected to rollback grade submission!';
				return false;
			}

			if (form_being_submitted) {
				alert('Rolling back selected students grades back to the instructor, please wait a moment...');
				$('#SubmitID').attr('disabled', true);
				if ($("#PublishedCourse").length) {
					$('#PublishedCourse').attr('disabled', true);
				}
				return false;
			}

            var confirmmm = confirm('Are you sure you want to rollback the selected students exam grade submissions? While rolling back exam grade submissions, make sure that the assigned course instructor is active and able to resubmit the grades and the academic calendar for grade submission for ' + acy_and_semester + ' is open. Are you sure you want to proceed?');

			if (!form_being_submitted && confirmmm) {
				$('#SubmitID').val('Rolling back Selected Student Grades...');
				if ($("#listPublishedCourses").length) {
					$("#listPublishedCourses").attr('disabled', true);
				}
				if ($("#PublishedCourse").length) {
					$('#PublishedCourse').attr('disabled', true);
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