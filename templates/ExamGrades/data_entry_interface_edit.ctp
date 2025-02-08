<script>
	var image = new Image();
	image.src = '/img/busy.gif';

	var number_of_students = <?= (isset($publishedCourses['courses']) ? count($publishedCourses['courses']) : 0); ?>;
	
	function check_uncheck(id) {
		var checked = ($('#' + id).attr("checked") == 'checked' ? true : false);
		for (i = 1; i <= number_of_students; i++) {
			$('#StudentSelection' + i).attr("checked", checked);
		}
	}

	$(document).ready(function () {
		$("#Section").change(function () {
			//serialize form data
			var s_id = $("#Section").val();
			window.location.replace("/exam_grades/<?= $this->action; ?>/" + s_id + "/" + $("#SemesterSelected").val());
		});
	});

	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none')
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		else {
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

	//Sub cat combo
	function updateDepartmentCollege(id) {
		//serialize form data
		var formData = $("#college_id_" + id).val();
		$("#college_id_" + id).attr('disabled', true);
		$("#department_id_" + id).attr('disabled', true);
		$("#section_id_" + id).attr('disabled', true);
		$("#add_button_disable").attr('disabled', true);
		//get form action
		var formUrl = '/departments/get_department_combo/' + formData + '/' + 1;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function (data, textStatus, xhr) {
				$("#department_id_" + id).attr('disabled', false);
				$("#college_id_" + id).attr('disabled', false);
				$("#department_id_" + id).empty();
				$("#department_id_" + id).append(data);
				
				//student lists
				var subCat = $("#department_id_" + id).val();
				$("#section_id_" + id).attr('disabled', true);

				//get form action
				var formUrl = '/sections/get_sections_by_dept_data_entry/' + subCat;
				$.ajax({
					type: 'get',
					url: formUrl,
					data: subCat,
					success: function (data, textStatus, xhr) {
						$("#section_id_" + id).attr('disabled', false);
						$("#add_button_disable").attr('disabled', false);
						$("#section_id_" + id).empty();
						$("#section_id_" + id).append(data);
					},
					error: function (xhr, textStatus, error) {
						alert(textStatus);
					}
				});

			},
			error: function (xhr, textStatus, error) {
				alert(textStatus);
			}
		});

		return false;
	}

	//Sub cat combo
	function updateSection(id) {
		//serialize form data
		var formData = $("#department_id_" + id).val();
		$("#section_id_" + id).attr('disabled', true);
		$("#college_id_" + id).attr('disabled', true);
		$("#department_id_" + id).attr('disabled', true);
		$("#add_button_disable").attr('disabled', true);
		//get form action
		var formUrl = '/sections/get_sections_by_dept_data_entry/' + formData;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function (data, textStatus, xhr) {
				$("#section_id_" + id).attr('disabled', false);
				$("#college_id_" + id).attr('disabled', false);
				$("#department_id_" + id).attr('disabled', false);
				$("#add_button_disable").attr('disabled', false);
				$("#section_id_" + id).empty();
				$("#section_id_" + id).append(data);
			},
			error: function (xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}

	function updatePublishedCourse(id, addParams) {
		//serialize form data
		var formData = $("#section_id_" + id).val();

		$("#college_id_" + id).attr('disabled', true);
		$("#section_id_" + id).attr('disabled', true);
		$("#department_id_" + id).attr('disabled', true);
		$("#add_button_disable").attr('disabled', true);
		$("#get_published_add_courses_id_" + id).empty().html('<img src="/img/busy.gif" class="displayed" >');

		//get form action
		var formUrl = '/examGrades/getPublishedAddCourses/' + formData + '/' + addParams;

		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function (data, textStatus, xhr) {
				$("#section_id_" + id).attr('disabled', false);
				$("#department_id_" + id).attr('disabled', false);
				$("#college_id_" + id).attr('disabled', false);
				$("#add_button_disable").attr('disabled', false);
				$("#get_published_add_courses_id_" + id).empty();
				$("#get_published_add_courses_id_" + id).append(data);
			},
			error: function (xhr, textStatus, error) {
				alert(textStatus);
				$("#get_published_add_courses_id_" + id).empty();
				$("#section_id_" + id).attr('disabled', false);
				$("#department_id_" + id).attr('disabled', false);
				$("#college_id_" + id).attr('disabled', false);
				$("#add_button_disable").attr('disabled', false);
			}
		});

		return false;
	}
</script>

<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Student Exam Grade Data Entry Interface'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div class="examGrades <?= $this->action; ?>">
					<div style="margin-top: -30px;">
						<?= $this->Form->create('ExamGrade'/* , array('onSubmit' => 'return checkForm(this);') */); ?>
						<hr>
						<blockquote>
							<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
							<p style="text-align:justify;"><span class="fs16">This tool will help you to enter student registration and grade. The system identifies the academic year and semester in which student is not registred and allow you to register the student and to enter the corrosponding grade(s). <br><span class="text-red">The selected academic year and semester will be matched only if the student has registration and grade for that academic year and semester, and display those courses with corresponding grade.</span>
						</blockquote>

						<hr>
						<div onclick="toggleViewFullId('ListSection')">
							<?php
							if (!empty($publishedCourses)) {
								echo $this->Html->image('plus2.gif', array('id' => 'ListSectionImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt"> Display Filter</span>
								<?php
							} else {
								echo $this->Html->image('minus2.gif', array('id' => 'ListSectionImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt"> Hide Filter</span>
								<?php
							}
							?>
						</div>
						<div id="ListSection" style="display:<?= (!empty($sections) ? 'none' : 'display'); ?>">

							<fieldset style="padding-bottom: 5px;padding-top: 15px;">
								<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
								<div class="row">
									<div class="large-3 columns">
										<?= $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => 'Academic Year: ', 'style' => 'width:90%', 'options' => $acyear_list, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('semester', array('id' => 'Semester', 'label' => 'Semester: ', 'style' => 'width:90%', 'options' => Configure::read('semesters'), 'default' => (isset($semester_selected) ? $semester_selected : false))); ?>
										<?= (isset($semester_selected) ? $this->Form->input('semester_selected', array('id' => 'SemesterSelected', 'type' => 'hidden', 'value' => $semester_selected)) : ''); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('studentnumber', array('id' => 'StudentNumber', 'class' => 'fs14', 'label' => 'Student ID: ', 'style' => 'width:90%', 'type' => 'text', 'placeholder' => 'Type Student ID...', /* 'required', */ 'maxlength' => 25)); ?>
									</div>
									<div class="large-3 columns">
										<?=  $this->Form->input('password', array('id' => 'Password', 'style' => 'width:90%', 'type' => 'password', 'label' => 'Password: ', 'placeholder' => 'Your Password here..', 'required')); ?>
									</div>
								</div>
								<hr>
								<?= $this->Form->submit(__('Get Courses', true), array('name' => 'listPublishedCourse', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
							</fieldset>
						</div>
						<hr>
					</div>

					<?php
					if (!empty($student_academic_profile)) { ?>
						<hr>
							<strong><?= $student_academic_profile['BasicInfo']['Student']['first_name'] . ' ' . $student_academic_profile['BasicInfo']['Student']['middle_name'] . ' ' . $student_academic_profile['BasicInfo']['Student']['last_name'] . ' (' . (isset($student_academic_profile['BasicInfo']['Department']['name']) ? $student_academic_profile['BasicInfo']['Department']['name'] : $student_academic_profile['BasicInfo']['College']['name'] . ($student_academic_profile['BasicInfo']['Program']['id'] == PROGRAM_REMEDIAL ? ' - Remedial Program' : ' - Pre/Freshman')) . ')'; ?></strong>
						<hr>
						<?php
					} ?>

					<?php
					if (!empty($publishedCourses['courses'])) { 
						
						$allowed_grades_for_deletion = Configure::read('allowed_grades_for_deletion'); ?>

						<div onclick="toggleViewFullId('Profile')">
							<?php
							if (!empty($student_academic_profile)) {
								echo $this->Html->image('plus2.gif', array('id' => 'ListProfileImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListProfileTxt"> Display Student Academic Profile</span>
								<?php
							} else {
								echo $this->Html->image('minus2.gif', array('id' => 'ListProfileImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListProfileTxt"> Hide Student Academic Profile</span>
								<?php
							} ?>
						</div>

						<div id="Profile" style="display:<?= (!empty($student_academic_profile) ? 'none' : 'display'); ?>">
							<?= $this->element('student_academic_profile'); ?>
						</div>
						<hr>

						<h6 class="fs13">Please select course(s) and enter/adjust corresponding grade. <i>Please note that : <?= 'Only grades ' . join(', ', $allowed_grades_for_deletion). ' are allowed to delete.'; ?></i></h6>
						
						<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
						<br>

						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<th style="width:5%" class="center"><div style="margin-left: 30%;"><?= $this->Form->input('select_all', array('type' => 'checkbox', 'id' => 'select-all', 'div' => false, 'label' => false)); ?></div></th>
									<th style="width:40%" class="vcenter">Course Title</th>
									<th style="width:15%" class="center">Course Code</th>
									<th style="width:15%" class="center"><?= (isset($student_academic_profile['Curriculum']['type_credit']) ? (count(explode('ECTS', $student_academic_profile['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit') : 'Credit'); ?></th>
									<th style="width:15%" class="center">ACY/SEM</th>
									<th style="width:20%" class="center">Grade</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$st_count = 0;
								$checkBoxCount = 0;
								$show_delete_button = 0;
								$totalRegisteredCourses = 0;
								//debug($publishedCourses['courses']);
								
								foreach ($publishedCourses['courses'] as $key => $course) {
									$st_count++; 
									$selectBoxEnabled = 1; ?>
									<tr>
										<td class="center">
											<?php
											$have_instructor_assignment = 0;
											$instructor_name = '';
											$gradeScaleError = '';
											$gradeScaleID = '';

											if (!empty($course['PublishedCourse']['CourseInstructorAssignment'])) {
												$have_instructor_assignment = 1;
												$instructor_name = '<br><span class="text-gray">Instructor: '. $course['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. '. $course['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['full_name'] . ' (' . $course['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Position']['position'] . ')</span>';
											}
											
											if (!empty($course['CourseInstructorAssignment'])) {
												$have_instructor_assignment = 1;
												$instructor_name = '<br><span class="text-gray">Instructor: '. $course['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. '. $course['CourseInstructorAssignment'][0]['Staff']['full_name'] . ' (' . $course['CourseInstructorAssignment'][0]['Staff']['Position']['position'] . ')</span>';
											} 

											if (!isset($course['CourseRegistration']['id'])) {
												$selectBoxEnabled = 0;
											} else {
												$totalRegisteredCourses++;
											}

											if (isset($course['CourseRegistration']) && !empty($course['CourseRegistration']['id']) /* (isset($course['CourseRegistration']) && !empty($course['CourseRegistration']['id']) && (!isset($course['Grade']) || empty($course['Grade']))) || (isset($course['Grade']) && !empty($course['Grade']['grade']) && !empty($allowed_grades_for_deletion) && in_array($course['Grade']['grade'], $allowed_grades_for_deletion)) */) {
												
												if ((isset($course['Grade']) && empty($course['Grade']['grade'])) || (isset($course['PublishedCourse']['grade']) && empty($course['PublishedCourse']['grade']['grade']))) {
													$show_delete_button++;
												} else if (!empty($allowed_grades_for_deletion) && isset($course['Grade']['grade']) && in_array($course['Grade']['grade'], $allowed_grades_for_deletion)) {
													$show_delete_button++;
												} else if (!empty($allowed_grades_for_deletion) && isset($course['PublishedCourse']['grade']) && in_array($course['PublishedCourse']['grade']['grade'], $allowed_grades_for_deletion)) {
													$show_delete_button++;
												}

												//$show_delete_button++;
											}

											$gradeList = array();

											if (isset($course['Grade']['grade_scale_id']) && $course['Grade']['grade_scale_id'] > 0 ) {

												$gradesScaleDetails = ClassRegistry::init('GradeScaleDetail')->find('all', array('conditions' => array('GradeScaleDetail.grade_scale_id' => $course['Grade']['grade_scale_id']), 'contain' => array('Grade', 'GradeScale')));

												if (!empty($gradesScaleDetails)) {
													foreach ($gradesScaleDetails as $key => $detail) {
														$gradeList[$detail['Grade']['grade']] = $detail['Grade']['grade'];
														$grade_scale_name = $detail['GradeScale']['name'];
														$gradeScaleID = $detail['GradeScale']['id'];
													}
													//debug($gradeScaleID);
												}

											} else if (isset($course['PublishedCourse']['grade_scale_id']) && $course['PublishedCourse']['grade_scale_id'] > 0) {

												$gradesScaleDetails = ClassRegistry::init('GradeScaleDetail')->find('all', array('conditions' => array('GradeScaleDetail.grade_scale_id' => $course['PublishedCourse']['grade_scale_id']), 'contain' => array('Grade', 'GradeScale')));

												if (!empty($gradesScaleDetails)) {
													foreach ($gradesScaleDetails as $key => $detail) {
														$gradeList[$detail['Grade']['grade']] = $detail['Grade']['grade'];
														$grade_scale_name = $detail['GradeScale']['name'];
														$gradeScaleID = $detail['GradeScale']['id'];
													}
													//debug($gradeScaleID);
												}
											} else  {
												if (isset($course['Course']['GradeType']['Grade'])) {
													foreach ($course['Course']['GradeType']['Grade'] as $key => $value) {
														$gradeList[$value['grade']] = $value['grade'];
													}
												}
												//debug($gradeScaleID);
											}

											$gradeList['NG'] = 'NG';
											$gradeList['I'] = 'I';
											$gradeList['W'] = 'W';
											$gradeList['DO'] = 'DO';

											if (isset($course['Grade']['grade']) && !empty($course['Grade']['grade']) && !in_array($course['Grade']['grade'], $gradeList)) {
												$gradeScaleError =  'Grade Scale Error: ' . $course['Grade']['grade'] . ' Grade is not found in ' . $grade_scale_name;
											}

											if (isset($course['PublishedCourse']['grade']['grade']) && !empty($course['PublishedCourse']['grade']['grade']) && !in_array($course['PublishedCourse']['grade']['grade'], $gradeList)) {
												$gradeScaleError =  'Grade Scale Error: ' . $course['PublishedCourse']['grade']['grade'] . ' Grade is not found in ' . $grade_scale_name;
											}

											if ((isset($graduated) && $graduated > 0 ) || !empty($gradeScaleError) || !isset($course['CourseRegistration']['id']) /* || $have_instructor_assignment == 0 */ /* || (!empty($course['Grade']) && !in_array($course['Grade'], $allowed_grades_for_deletion)) */ /*  && $st_count != 1 */) {
												echo '**';
											} else if (empty($course['Grade'])) {
												$checkBoxCount++;
												echo '<div style="margin-left: 30%;">'. $this->Form->input('CourseRegistration.' . $st_count . '.gp', array('type' => 'checkbox', 'class' => 'checkbox1', 'label' => false, 'id' => 'StudentSelection' . $st_count)) . '</div>';
												echo $this->Form->input('CourseRegistration.' . $st_count . '.student_id', array('type' => 'hidden', 'value' => $student_academic_profile['BasicInfo']['Student']['id']));
											} else {
												echo '<div style="margin-left: 30%;">'. $this->Form->input('CourseRegistration.' . $st_count . '.gp', array('type' => 'checkbox', 'class' => 'checkbox1', 'label' => false, 'id' => 'StudentSelection' . $st_count)) . '</div>';
												echo $this->Form->input('CourseRegistration.' . $st_count . '.student_id', array('type' => 'hidden', 'value' => $student_academic_profile['BasicInfo']['Student']['id']));
											} ?>
										</td>
										<td class="vcenter">
											<?= $course['Course']['course_title']; ?>
											<?php
											if ($have_instructor_assignment) {
												echo $instructor_name;
											} else {
												echo '<br><span class="on-process">No instructor assignment found for this course, Grade Entry not allowed.</span>';
											} 

											if (!empty($gradeScaleError)) {
												echo '<br><span class="rejected">' . $gradeScaleError . '</span>';
											} 
											
											if (!isset($course['CourseRegistration']['id'])) {
												echo '<br><span class="on-process">Please, Maintain Missing Registration Before Grade Entry.</span>';
											} 

											if ((isset($course['Grade']) && empty($course['Grade']['grade'])) || (isset($course['PublishedCourse']['grade']) && empty($course['PublishedCourse']['grade']['grade']))) {
												echo '<br><span class="rejected">NOTICE: You can\'t change/delete the grade once you submit it.</span>';
											} ?>
										</td>
										<td class="center"><?= $course['Course']['course_code']; ?></td>
										<td class="center"><?= $course['Course']['credit']; ?></td>
										<td class="center">
											<?php
											if (!empty($course['PublishedCourse']['academic_year'])) {
												echo $course['PublishedCourse']['academic_year'] . '/' . $course['PublishedCourse']['semester'];
											} else {
												echo $course['PublishedCourse']['academic_year'] . '/' . $course['PublishedCourse']['semester'];
											} ?>
										</td>
										<td class="center">
											<?php
											//debug($course['Course']['grade_scale_id']);

											if (!isset($course['Grade']) && empty($course['Grade'])) {
												
												if (isset($course['CourseRegistration']) && !empty($course['CourseRegistration']['id'])) {
													echo $this->Form->hidden('CourseRegistration.' . $st_count . '.id', array('value' => $course['CourseRegistration']['id']));
												}

												echo $this->Form->hidden('CourseRegistration.' . $st_count . '.academic_year', array('value' => $course['PublishedCourse']['academic_year']));
												echo $this->Form->hidden('CourseRegistration.' . $st_count . '.semester', array('value' => $course['PublishedCourse']['semester']));
												echo $this->Form->hidden('CourseRegistration.' . $st_count . '.year_level_id', array('value' => $course['PublishedCourse']['year_level_id']));
												echo $this->Form->hidden('CourseRegistration.' . $st_count . '.section_id', array('value' => $course['PublishedCourse']['section_id']));
												
												
												if (!empty($gradeScaleID) && $gradeScaleID > 0) {
													echo $this->Form->hidden('CourseRegistration.' . $st_count . '.grade_scale_id', array('value' => $gradeScaleID));
												} else if (isset($course['Course']['grade_scale_id'])) {
													echo $this->Form->hidden('CourseRegistration.' . $st_count . '.grade_scale_id', array('value' => $course['Course']['grade_scale_id']));
												} else if (isset($course['PublishedCourse']['grade_scale_id'])) {
													echo $this->Form->hidden('CourseRegistration.' . $st_count . '.grade_scale_id', array('value' => $course['PublishedCourse']['grade_scale_id']));
												}

												echo $this->Form->hidden('CourseRegistration.' . $st_count . '.published_course_id', array('value' => $course['PublishedCourse']['id']));
												echo $this->Form->input('CourseRegistration.' . $st_count . '.student_id', array('type' => 'hidden', 'value' => $student_academic_profile['BasicInfo']['Student']['id']));

											}

											if (isset($course['PublishedCourse']['id'])) {
												echo $this->Form->hidden('CourseRegistration.' . $st_count . '.published_course_id', array('value' => $course['PublishedCourse']['id']));
											}

											if (isset($course['Course']['grade_scale_id'])) {
												echo $this->Form->hidden('CourseRegistration.' . $st_count . '.grade_scale_id', array('value' => $course['Course']['grade_scale_id']));
											} else if (isset($course['PublishedCourse']['grade_scale_id'])) {
												echo $this->Form->hidden('CourseRegistration.' . $st_count . '.grade_scale_id', array('value' => $course['PublishedCourse']['grade_scale_id']));
											}

											if (!empty($course['CourseRegistration']['id'])) {
												echo $this->Form->hidden('CourseRegistration.' . $st_count . '.id', array('label' => false, 'value' => $course['CourseRegistration']['id']));
											}

											if (!empty($course['CourseAdd']['id'])) {
												echo $this->Form->hidden('CourseRegistration.' . $st_count . '.course_add_id', array('label' => false, 'value' => $course['CourseAdd']['id']));
											}

											if (isset($course['PublishedCourse']['grade']['grade_id']) && !empty($course['PublishedCourse']['grade']['grade_id'])) {
												$grade_id = $course['PublishedCourse']['grade']['grade_id'];
											} else if (isset($course['Grade']['grade_id']) && !empty($course['Grade']['grade_id'])) {
												$grade_id = $course['Grade']['grade_id'];
											}

											if (isset($grade_id) && !empty($grade_id)) {
												echo $this->Form->hidden('CourseRegistration.' . $st_count . '.grade_id', array('label' => false, 'value' => $grade_id));
												$grade_id = null;
											}

											if (isset($course['Grade']['grade']) && !empty($course['Grade']['grade']) && !in_array($course['Grade']['grade'], $gradeList)) {
												$gradeList = array();
												$gradeList[$course['Grade']['grade']] = $course['Grade']['grade'];
												echo $this->Form->input('CourseRegistration.' . $st_count . '.grade', array('label' => false, 'style' => 'width: 60%;', 'type' => 'select', /* 'empty' => '[ Select ]', */ 'options' => $gradeList, 'value' => $course['Grade']['grade'], ((isset($graduated) && $graduated > 0) || $selectBoxEnabled == 0 /* || $have_instructor_assignment == 0 */ /* || (!empty($course['Grade']) && !in_array($course['Grade'], $allowed_grades_for_deletion)) */ ?  'disabled' : '')));
											} else if (isset($course['Grade']['grade']) && !empty($course['Grade']['grade']) && in_array($course['Grade']['grade'], $gradeList)) {
												if (ALLOW_REGISTRAR_ADMIN_TO_CHANGE_VALID_GRADES && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) {
													echo $this->Form->input('CourseRegistration.' . $st_count . '.grade', array('label' => false, 'style' => 'width: 60%;', 'type' => 'select', 'empty' => '[ Select ]', 'options' => $gradeList, 'value' => (isset($course['Grade']['grade']) ? $course['Grade']['grade'] : '' ), ((isset($graduated) && $graduated > 0) || $selectBoxEnabled == 0 /* || $have_instructor_assignment == 0 */ /* || (!empty($course['Grade']) && !in_array($course['Grade'], $allowed_grades_for_deletion)) */ ?  'disabled' : '')));
												} else {
													$gradeList = array();
													$gradeList[$course['Grade']['grade']] = $course['Grade']['grade'];
													echo $this->Form->input('CourseRegistration.' . $st_count . '.grade', array('label' => false, 'style' => 'width: 60%;', 'type' => 'select', /* 'empty' => '[ Select ]', */ 'options' => $gradeList, 'value' => $course['Grade']['grade'], ((isset($graduated) && $graduated > 0)  || $selectBoxEnabled == 0 /* || $have_instructor_assignment == 0 */ /* || (!empty($course['Grade']) && !in_array($course['Grade'], $allowed_grades_for_deletion)) */ ?  'disabled' : '')));
												}
											} else {

												if (!isset($course['Grade']) && empty($course['Grade'])) {
													if (isset($course['PublishedCourse']['grade']['grade']) && !empty($course['PublishedCourse']['grade']['grade']) && !in_array($course['PublishedCourse']['grade']['grade'], $gradeList)) {
														$gradeList = array();
														$gradeList[$course['PublishedCourse']['grade']['grade']] = $course['PublishedCourse']['grade']['grade'];
														echo $this->Form->input('CourseRegistration.' . $st_count . '.grade', array('label' => false, 'style' => 'width: 60%;', 'type' => 'select', 'options' => $gradeList, /* 'empty' => '[ Select ]', */ 'value' => $course['PublishedCourse']['grade']['grade'], (isset($graduated) && $graduated > 0 || $selectBoxEnabled == 0 ? 'disabled' : '')));
													} else if (isset($course['PublishedCourse']['grade']['grade']) && !empty($course['PublishedCourse']['grade']['grade']) && in_array($course['PublishedCourse']['grade']['grade'], $gradeList)) {
														if (ALLOW_REGISTRAR_ADMIN_TO_CHANGE_VALID_GRADES && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) {
															echo $this->Form->input('CourseRegistration.' . $st_count . '.grade', array('label' => false, 'style' => 'width: 60%;', 'type' => 'select', 'empty' => '[ Select ]', 'options' => $gradeList, 'value' => (isset($course['Grade']['grade']) ? $course['Grade']['grade'] : '' ), ((isset($graduated) && $graduated > 0) || $selectBoxEnabled == 0 /* || $have_instructor_assignment == 0 */ /* || (!empty($course['Grade']) && !in_array($course['Grade'], $allowed_grades_for_deletion)) */ ?  'disabled' : '')));
														} else {
															$gradeList = array();
															$gradeList[$course['PublishedCourse']['grade']['grade']] = $course['PublishedCourse']['grade']['grade'];
															echo $this->Form->input('CourseRegistration.' . $st_count . '.grade', array('label' => false, 'style' => 'width: 60%;', 'type' => 'select', 'options' => $gradeList, /* 'empty' => '[ Select ]', */ 'value' => $course['PublishedCourse']['grade']['grade'], (isset($graduated) && $graduated > 0 || $selectBoxEnabled == 0 ? 'disabled' : '')));
															//echo $this->Form->input('CourseRegistration.' . $st_count . '.grade', array('label' => false, 'style' => 'width: 60%;', 'type' => 'select', 'options' => $gradeList, 'empty' => '[ Select ]', 'value' => $course['PublishedCourse']['grade']['grade'], (isset($graduated) && $graduated > 0 ? 'disabled' : '')));
														}
													} else {
														echo $this->Form->input('CourseRegistration.' . $st_count . '.grade', array('label' => false, 'style' => 'width: 60%;', 'type' => 'select', 'options' => $gradeList, 'empty' => '[ Select ]', ((isset($graduated) && $graduated > 0) || $selectBoxEnabled == 0 /* || $have_instructor_assignment == 0 */ || (!empty($course['Grade']) && !in_array($course['Grade'], $allowed_grades_for_deletion)) ? 'disabled' : '')));
													}
												} else {
													echo $this->Form->input('CourseRegistration.' . $st_count . '.grade', array('label' => false, 'style' => 'width: 60%;', 'type' => 'select', 'empty' => '[ Select ]', 'options' => $gradeList, 'value' => (isset($course['Grade']['grade']) ? $course['Grade']['grade'] : '' ), ((isset($graduated) && $graduated > 0) || $selectBoxEnabled == 0 /* || $have_instructor_assignment == 0 */ /* || (!empty($course['Grade']) && !in_array($course['Grade'], $allowed_grades_for_deletion)) */ ?  'disabled' : '')));
												} 
											} ?>
										</td>
									</tr>
									<?php
								} ?>

								<tr>
									<?php
									if ($graduated > 0) { ?>
										<td>&nbsp;</td>
										<td colspan="5" class="vcenter"><span class="text-red"><b>Graduated student record, Student Profile is locked and archived!</b></span></td>
										<?php
									} else {
										if (!empty($course['PublishedCourse']['academic_year']) && $totalRegisteredCourses) { ?>
											<td colspan="6" class="vcenter">
												<?= $this->Html->link('Add Courses', '#', array('data-animation' => "fade", 'data-reveal-id' => 'myModalAdd', 'data-reveal-ajax' => '/examGrades/getAddCoursesDataEntry/' . $student_academic_profile['BasicInfo']['Student']['id'] . '/' . str_replace('/', '-', $course['PublishedCourse']['academic_year']) . '/' . $course['PublishedCourse']['semester'])); ?>
											</td>
											<?php
										} else if ($totalRegisteredCourses) { ?>
											<td colspan="6" class="vcenter">
												<?= $this->Html->link('Add Courses', '#', array('data-animation' => "fade", 'data-reveal-id' => 'myModalAdd', 'data-reveal-ajax' => '/examGrades/getAddCoursesDataEntry/' . $student_academic_profile['BasicInfo']['Student']['id'] . '/' . str_replace('/', '-', $this->request->data['ExamGrade']['acadamic_year']) . '/' . $this->request->data['ExamGrade']['semester'])); ?>
											</td>
											<?php
										}
									} ?>
								</tr>
							</tbody>
						</table>

						<?php
						if (!$graduated) { ?>
							<hr>
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->submit(__('Save ', true), array('name' => 'saveGrade', 'id' => 'saveGrade', 'class' => 'tiny radius button bg-blue', /* 'disabled' => ($checkBoxCount ? false : true), */ 'div' => false)); ?>
								</div>
								<?php 
								if ($show_delete_button && $checkBoxCount || (ALLOW_REGISTRAR_ADMIN_TO_DELETE_VALID_GRADES && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1)) { ?>
									<div class="large-3 columns">
										<?= $this->Form->submit(__('Delete ', true), array('name' => 'deleteGrade', 'id' => 'deleteGrade',  'class' => 'tiny radius button bg-red', 'div' => false)); ?>
									</div>
									<?php
								} ?>
								<div class="large-6 columns">
									&nbsp;
								</div>
							</div>
							<?php
						} ?>
						<?php
					} else { ?>
						<!-- <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No published courses found to register <?php //echo isset($student_academic_profile['BasicInfo']['Student']['full_name']) ? $student_academic_profile['BasicInfo']['Student']['full_name'] . ' (' . $student_academic_profile['BasicInfo']['Student']['studentnumber'] . ')' : 'the student'; ?> for <?= isset($this->data['ExamGrade']) ? $this->data['ExamGrade']['acadamic_year']  . ' academic year, semster ' . $this->data['ExamGrade']['semester'] : ' the selected academic year and semster'; ?>.</div> -->
						<?php
					} ?>

					<?= $this->Form->end(); ?>
				</div>

				<!-- COURSE ADD MODAL START -->
				<div id="myModalAdd" class="reveal-modal" data-reveal>

				</div>
				<!-- COURSE ADD MODAL END -->

			</div>
		</div>
	</div>
</div>
<script type='text/javascript'>

	var form_being_submitted = false;
	
	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

    //$(document).ready(function() {
        $('#saveGrade').click(function() {
			var checkboxes = document.querySelectorAll('input[type="checkbox"]');
			var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

			if ($('#deleteGrade').length) { 
				//'Element exists
				$('#deleteGrade').attr('disabled', true);
			}

			if (!checkedOne) {
				alert('At least one course must be selected to maintain Student\'s Grade entry or update.');
				validationMessageNonSelected.innerHTML = 'At least one course must be selected to maintain Student\'s Grade entry or update.';
				return false;
			}

			if (form_being_submitted) {
				alert("Maintaining grade entry for selected exam grades, please wait a moment...");
				$('#saveGrade').attr('disabled', true);
				if ($('#deleteGrade').length) {
					$('#deleteGrade').attr('disabled', true);
				}
				return false;
			}

            var confirmm = confirm('Are you sure you want to save or change the selected course grades? This will new grades or permanently change existing course grades! Are you sure to proceed?');

			if (confirmm) {
				$('#saveGrade').val('Maintaining Grade Entry..');
				form_being_submitted = true;
				if ($('#deleteGrade').length) {
					$('#deleteGrade').attr('disabled', true);
				}
				return true;
			} else {
				return false;
			}
        });

		$('#deleteGrade').click(function() {
			var checkboxes = document.querySelectorAll('input[type="checkbox"]');
			var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

			if (!checkedOne) {
				alert('At least one course must be selected to delete Existing Grade.');
				validationMessageNonSelected.innerHTML = 'At least one course must be selected to delete Existing Grade.';
				return false;
			}

			if (form_being_submitted) {
				alert("Deleting selected exam grades, please wait a moment...");
				$('#deleteGrade').attr('disabled', true);
				if ($('#saveGrade').length) { 
					$('#saveGrade').attr('disabled', true);
				}
				return false;
			}

            var confirmm = confirm('Are you sure you want to delete the selected course grades from the system? This will permanently delete existing course grades and assesment if any, and it is not recovorable!! Are you sure to delete the selected grades?');

			if (confirmm) {
				$('#deleteGrade').val('Deleting selected Grades..');
				form_being_submitted = true;
				if ($('#saveGrade').length) { 
					$('#saveGrade').attr('disabled', true);
				}
				return true;
			} else {
				return false;
			}
        });
    //});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>