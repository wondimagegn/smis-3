<?=  $this->Form->create('ExamResult'); ?>
<script type="text/javascript">
	var grade_scale = Array();
	<?php
	if (isset($grade_scale['GradeScaleDetail'])) {
		$grade_scale_count = 0;
		foreach ($grade_scale['GradeScaleDetail'] as $key => $scale_detail) { ?>
			grade_scale[<?=  $grade_scale_count; ?>] = Array();
			grade_scale[<?=  $grade_scale_count; ?>][0] = <?= $scale_detail['minimum_result']; ?>;
			grade_scale[<?=  $grade_scale_count; ?>][1] = <?= $scale_detail['maximum_result']; ?>;
			grade_scale[<?=  $grade_scale_count; ?>][2] = '<?= $scale_detail['grade']; ?>';
			<?php
			$grade_scale_count++;
		}
	} ?>

	function updateExamGradeChange(obj, st_count) {
		if (obj.value != "" && isNaN(obj.value)) {
			alert('Please enter a valid result.');
			$('#' + obj.id).focus();
			$('#' + obj.id).select();
			$('#GradeChangeResult_grade_' + st_count).empty();
			$('#GradeChangeResult_grade_' + st_count).append('---');
			return false;
		} else if (obj.value != "" && parseFloat(obj.value) > 100) {
			alert('The maximum value of exam result is 100.');
			$('#' + obj.id).focus();
			$('#' + obj.id).select();
			$('#GradeChangeResult_grade_' + st_count).empty();
			$('#GradeChangeResult_grade_' + st_count).append('---');
			return false;
		} else if (obj.value != "" && parseFloat(obj.value) < 0) {
			alert('The minimum value of exam result is 0.');
			$('#' + obj.id).focus();
			$('#' + obj.id).select();
			$('#GradeChangeResult_grade_' + st_count).empty();
			$('#GradeChangeResult_grade_' + st_count).append('---');
			return false;
		} else {
			for (var i = 0; i < grade_scale.length; i++) {
				if (parseFloat(obj.value) >= grade_scale[i][0] && parseFloat(obj.value) <= grade_scale[i][1]) {
					$('#GradeChangeResult_grade_' + st_count).empty();
					$('#GradeChangeResult_grade_' + st_count).append(grade_scale[i][2]);
				}
			}
			return true;
		}
	}

	function submitGrdeChangeRequest(id, st_count) {

		if (document.getElementById('' + id + '_result_' + st_count + '').value == "") {
			alert('You are required to submit exam result.');
			return false;
		} else if (document.getElementById('' + id + '_reason_' + st_count + '').value == "") {
			alert('You are required to submit exam grade change reason.');
			return false;
		} else {
			return true;
		}
		/*
			if($('#'+id+'_result_'+st_count).attr('value') == "") {
			
				alert('You are required to submit exam result');
				return false;
			}
			else if($('#'+id+'_reason_'+st_count).attr('value') == "") {
				alert('You are required to submit exam grade change reason');
				return false;		
			}
			else
				return true
		*/
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
			$('#' + id + 'Txt').append('Hide Filter');
		} else {
			$('#' + id + 'Img').attr("src", '/img/plus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append('Display Filter');
		}
		$('#' + id).toggle("slow");
	}

	var grade = new Array();

	function updateExamTotal(obj, row, exam_num, percent, exam_name, sum_it) {
		
		var sum = 0;
		var result = 0;
		var invalid = false;
		var result_found = false;

		if (obj.value != "" && isNaN(obj.value)) {
			alert('Please enter a valid result.');
			$('#' + obj.id).focus();
			$('#' + obj.id).select();
			return false;
		} else if (obj.value != "" && parseFloat(obj.value) > parseFloat(percent)) {
			alert('The maximum value of "' + exam_name + '" exam result is ' + percent + '.');
			$('#' + obj.id).focus();
			$('#' + obj.id).select();
			return false;
		} else if (obj.value != "" && parseFloat(obj.value) < 0) {
			alert('The minimum value of "' + exam_name + '" exam result is 0.');
			$('#' + obj.id).focus();
			$('#' + obj.id).select();
			return false;
		}

		if (!sum_it) {
			return true;
		}
		//contingency ???
		for (var i = 1; i <= exam_num; i++) {
			result = $('#result_' + row + '_' + i).val();
			//alert('#result_'+row+'_'+i);
			//alert(result);
			//alert(percent)
			if (isNaN(result)) {
				invalid = true;
				//alert('Please enter a valid result.');
				//$('#result_'+row+'_'+i).focus();
				//$('#result_'+row+'_'+i).select();
				break;
			} else {
				if (result != "") {
					//if(parseFloat(result) > parseFloat(percent)) {
					/*alert('The maximum value of "'+exam_name+'" exam result is '+percent+'.');
					$('#result_'+row+'_'+i).focus();
					$('#result_'+row+'_'+i).select();
					
					break;	
					*/
					//}
					//else {
					sum += parseFloat(result);
					result_found = true;
					//}
				}
			}
		}

		if (invalid) {
			$('#total_100_' + row).empty();
			$('#total_100_' + row).append('---');
		} else {
			$('#total_100_' + row).empty();
			if (result_found) {
				$('#total_100_' + row).append(sum);
			} else {
				$('#total_100_' + row).append('---');
			}
		}
	}

	function confirmGradeSubmitCancelation() {
		return confirm("Are you sure you want to cancel your grade submission? Only Grades that are rolled back for resubmission by the registrar, those rejected by the departmeent(if any) and submitted grades pending for department approval will be canceled for resubmission. All accepted grades, either by the department or registrar will stay intact.");
	}

	function courseInProgress(c, obj) {
		if (obj.checked) {
			grade[c] = window.document.getElementById('G_' + c).innerHTML;
			window.document.getElementById('G_' + c).innerHTML = '**';
		} else {
			window.document.getElementById('G_' + c).innerHTML = grade[c];
		}
	}

	function showHideGradeScale() {
		if ($("#ShowHideGradeScale").val() == 'Show Grade Scale') {
			var p_course_id = $("#PublishedCourse").val();
			$("#GradeScale").empty();
			$("#GradeScale").append('Loading ...');
			var formUrl = '/published_courses/get_course_grade_scale/' + p_course_id;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: p_course_id,
				success: function (data, textStatus, xhr) {
					$("#GradeScale").empty();
					$("#GradeScale").append(data);
					$("#ShowHideGradeScale").attr('value', 'Hide Grade Scale');
				},
				error: function (xhr, textStatus, error) {
					alert(textStatus);
				}
			});
		} else {
			$("#GradeScale").empty();
			$("#ShowHideGradeScale").attr('value', 'Show Grade Scale');
		}

		return false;
	}

	function showHideGradeStatistics() {
		var p_course_id = $("#PublishedCourse").val();
		//alert(p_course_id);
		if ($("#ShowHideGradeDistribution").val() == 'Show Grade Distribution') {
			//var p_course_id = $("#PublishedCourse").val();
			$("#GradeDistribution").empty();
			$("#GradeDistribution").append('Loading ...');
			var formUrl = '/published_courses/get_course_grade_stats/' + p_course_id;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: p_course_id,
				success: function(data, textStatus, xhr) {
					$("#GradeDistribution").empty();
					$("#GradeDistribution").append(data);
					$("#ShowHideGradeDistribution").attr('value', 'Hide Grade Distribution');
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});
		} else {
			$("#GradeDistribution").empty();
			$("#ShowHideGradeDistribution").attr('value', 'Show Grade Distribution');
		}

		return false;
	}

	$(document).ready(function () {
		$("#PublishedCourse").change(function () {
			//serialize form data
			$("#flashMessage").remove();
			$("#PublishedCourse").attr('disabled', true);
			var pc = $("#PublishedCourse").val();
			$("#ExamResultDiv").empty();

			if (pc != '' && pc > 1) {
				$("#ExamResultDiv").append('<p>Loading ...</p>');
				//get form action
				var formUrl = '/examResults/get_exam_result_entry_form/' + pc;
				$.ajax({
					type: 'get',
					url: formUrl,
					data: pc,
					success: function (data, textStatus, xhr) {
						$("#ExamResultDiv").empty();
						$("#ExamResultDiv").append(data);
						$("#PublishedCourse").attr('disabled', false);
					},
					error: function (xhr, textStatus, error) {
						alert(textStatus);
					}
				});

				return false;
			} else {
				$("#PublishedCourse").attr('disabled', false);
			}
			return false;
		});
	});
</script>

<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Submit Grade on behalf of Instructor'); ?></span>
        </div>
    </div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div style="margin-top: -20px;"></div>

				<div class="examResults index">
					<!-- <hr>
					<h6 class="fs16 text-gray">View Exam Result and Grade. You can also submit grade on behalf of the instructor if the assigned instructor account is deactivated.</h6>
					<hr> -->
					<hr>
					<div onclick="toggleViewFullId('ListPublishedCourse')">
						<?php
						if (!empty($publishedCourses)) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Display Filter</span>
							<?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Hide Filter</span>
							<?php
						} ?>
					</div>
					<div id="ListPublishedCourse" style="display:<?=  (!empty($publishedCourses) ? 'none' : 'display'); ?>">
						<fieldset style="padding-bottom: 5px;padding-top: 5px;">
                        	<!-- <legend>&nbsp;&nbsp; Search &nbsp;&nbsp;</legend> -->
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => 'Acadamic Year: ', 'class' => 'fs14', 'style' => 'width:90%;', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($selected_acadamic_year) ? $selected_acadamic_year : (isset($academic_year) && !empty($academic_year) ? $academic_year : $defaultacademicyear)))); ?>
								</div>
								<div class="large-3 columns">
									<?=  $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'label' => 'Semester: ', 'style' => 'width:90%;', 'options' => Configure::read('semesters'), 'default' => $semester)); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => 'Program: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programs, 'default' => isset($program_id) ? $program_id : "")); ?>
								</div>
								<div class="large-3 columns">
									<?=  $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => 'Program Type: ', 'type' => 'select', 'style' => 'width:90%;',  'options' => $program_types, 'default' => $program_type_id)); ?>
								</div>
							</div>
							<hr>
							<?=  $this->Form->submit(__('List Published Courses'), array('name' => 'listPublishedCourses', 'id' => 'listPublishedCourses', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
						</fieldset>
					</div>
					<hr>
					
					<?php
					if (!empty($publishedCourses)) { ?>
						<div id="show_published_courses_drop_down">
							<table class="fs14" cellpadding="0" cellspacing="0" class='table'>
								<tr>
									<td style="width:25%;" class="center">Published Courses</td>
									<td colspan="3">
										<div class="large-10 columns">
										<br>
										<?= $this->Form->input('published_course_id', array('style' => 'width: 90%;', 'id' => 'PublishedCourse', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $publishedCourses, 'default' => $published_course_combo_id)); ?>
										</div>
									</td>
								</tr>
							</table>
						</div>
						<?php
					}

					if (/* 1 ||  */!empty($publishedCourses)) { ?>

						<div id="ExamResultDiv">
							<?php
							if (!empty($exam_types) && (!empty($students) || !empty($student_adds) || !empty($student_makeup))) {
								if ((isset($grade_scale) && !empty($grade_scale['Course']['id']) && $grade_scale['Course']['thesis'] == 1 && ($grade_scale['Course']['Curriculum']['program_id'] == PROGRAM_PhD || $grade_scale['Course']['Curriculum']['program_id'] == PROGRAM_POST_GRADUATE)) && isset($grade_scale['GradeType']['used_in_gpa']) && $grade_scale['GradeType']['used_in_gpa'] == 1) { ?>
									<hr>
									<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;"><span style='margin-right: 15px;'></span>Currently, <?= $grade_scale['Course']['course_code_title']; ?> course is set as a <?=$grade_scale['Course']['Curriculum']['program_id'] == PROGRAM_POST_GRADUATE ? 'Thesis/Projecct' : 'Dissertation'; ?> course and associated to "<?= $grade_scale['GradeScale']['name']; ?>" from "<?= $grade_scale['GradeType']['type']; ?>" grading type which uses point values of the awarded grades in CGPA calculations. Please communicate the department and check the correctness of the grade type specified on <?= $grade_scale['Course']['Curriculum']['curriculum_detail']; ?> curriculum before submitting the grades.</div>
									<hr>
									<?php
								} ?>
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
								
								<?= '<br><h6 class="fs14 text-gray">' . $course_detail['course_code_title'] . ' exam result entry for ' . $section_detail['name'] . ' section.'. (/* (!$view_only) ||  */(isset($lastGradeSubmissionDate) && $lastGradeSubmissionDate > date('Y-m-d')) || (isset($grade_submission_status) && $grade_submission_status['grade_submited'] == 0) ? '<strong style="color:red;"> The result you type will be automatically saved.</strong>': '' ). '</h6>'; ?>
								<?php
								$in_progress = 0;
								$students_process = $students;
								$makeup_exam = false;
								$count = 1;
								$st_count = 0;
								$this->set(compact('students_process', 'count', 'st_count', 'in_progress', 'makeup_exam'));
								echo $this->element('exam_sheet'); 
								

								if (count($student_adds) > 0) {
									echo '<hr><h6 class="fs16 text-gray">Students who added ' . $course_detail['course_code_title'] . ' course from other sections.</h6><hr>';
									$students_process = $student_adds;
									$makeup_exam = false;
									$count = ((count($students) * count($exam_types)) + 1);
									$st_count = count($students);
									$in_progress = count($students);
									$this->set(compact('students_process', 'count', 'st_count', 'in_progress', 'makeup_exam'));
									echo $this->element('exam_sheet'); 
								}

								if (count($student_makeup) > 0) {
									echo '<hr><h6 class="fs16 text-gray">Students who are taking Makeup Exam for ' . $course_detail['course_code_title'] . ' course.</h6><hr>';
									$students_process = $student_makeup;
									$makeup_exam = true;
									$count = ((count($students) * count($exam_types)) + (count($student_adds) * count($exam_types)) + 1);
									$st_count = (count($students) + count($student_adds));
									$in_progress = (count($students) + count($student_adds));
									$this->set(compact('students_process', 'count', 'st_count', 'in_progress', 'makeup_exam'));
									echo $this->element('exam_sheet');
								}

								if ($display_grade) { ?>
									<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span></span>If a student fails to take exams which are set as mandatory in the exam setup, the system will automatically give NG to the student.</div>
									<?php
								} 
								
								if (!$view_only) { ?>
									<table>
										<tr>
											<td style="width:20%">
												<?php
												$button_options = array('name' => 'saveExamResult', 'div' => false, 'class' => 'tiny radius button bg-blue');
												if ($display_grade || ($grade_submission_status['grade_submited_fully'] && $grade_submission_status['grade_dpt_rejected'] == 0)) {
													$button_options['disabled'] = 'true';
												}
												echo $this->Form->submit(__('Save Exam Result'), $button_options); ?>
											</td>
											<td style="width:20%">
												<?php
												$button_options = array();
												$button_options['name'] = 'previewExamGrade';
												$button_options['div'] = 'false';
												$button_options['class'] = 'tiny radius button bg-blue';
												//$button_options['onclick'] = 'return confirm("Please make sure that you save exam result before you preview. Do you want to continue to preview grade?")';
												if (!$grade_submission_status['scale_defined'] || ($grade_submission_status['grade_submited_fully'] && $grade_submission_status['grade_dpt_rejected'] == 0)) {
													$button_options['disabled'] = 'true';
												}
												if (!$display_grade) {
													echo $this->Form->submit(__('Save & Preview Grade'), $button_options);
												} else {
													echo $this->Form->submit(__('Cancel Preview'), array('name' => 'cancelExamGradePreview', 'div' => false, 'class' => 'tiny radius button bg-blue'));
												}
												if (!$grade_submission_status['scale_defined']) {
													echo '<p>Grade scale is not defined.</p>';
												} else if (!$display_grade && !($grade_submission_status['grade_submited_fully'] && $grade_submission_status['grade_dpt_rejected'] == 0)) {
													//echo '<p>Make sure you save exam result before you preview grade.</p>';
												} ?>
											</td>
											<td style="width:20%">
												<?php
												$button_options = array();
												$button_options['name'] = 'submitExamGrade';
												$button_options['div'] = 'false';
												$button_options['class'] = 'tiny radius button bg-blue';
												if (!$display_grade || ($grade_submission_status['grade_submited_fully'] && $grade_submission_status['grade_dpt_rejected'] == 0)) {
													$button_options['disabled'] = 'true';
												}
												echo $this->Form->submit(__('Submit Grade'), $button_options); 

												if (!($grade_submission_status['grade_submited_fully'] && $grade_submission_status['grade_dpt_rejected'] == 0) && !$display_grade)
													echo '<p>Preview exam grade before you submite grade.</p>';
												?>
											</td>
											<td style="width:40%">
												<?php //debug($grade_submission_status);
												$button_options = array('name' => 'cancelExamGrade', 'class' => 'tiny radius button bg-blue', 'div' => false);
												if (!$grade_submission_status['grade_submited'] || ($grade_submission_status['grade_submited'] && $grade_submission_status['grade_dpt_approved_fully'])) {
													$button_options['disabled'] = 'true';
												} else {
													$button_options['onClick'] = 'return confirmGradeSubmitCancelation()';
												}
												echo $this->Form->submit(__('Cancel Submited Grade'), $button_options);
												if (isset($button_options['disabled'])) {
													echo '<p>Cancelation is available only when grade is submited &amp; pending approval.</p>';
												} else {
													echo '<p>Cancelation is only for grades pending department approval.</p>';
												} ?>
											</td>
										</tr>
									</table>
									<?php
								}
							} else if (count($publishedCourses) <= 1 && empty($this->request->data)) { ?>
								<div id="flashMessage" class="info-box info-message" style="font-family: T'imes New Roman', Times, serif;"><span style="margin-right: 15px;"></span>Please select academic year, semester, program and program type to get list of published courses.</div>
								<?php
							} else if (count($publishedCourses) > 1) { ?>
								<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>Please select a published course to view exam result and grade and possibly to submit exam grade if instructor account is closed.</div>
								<?php
							}
							?>
						</div>
						<?php
					} ?>
					
				</div>
			</div>
		</div>
	</div>
</div>
<?=  $this->Form->end(); ?>

<script>
	$('#listPublishedCourses').click(function(event) {
		$('#listPublishedCourses').val('Looking for Published Courses...');
		$('#PublishedCourse').val(0);
		$("#show_published_courses_drop_down").hide();
		if ($('#ExamResultDiv').length) {
			$("#ExamResultDiv").empty();
			$("#ExamResultDiv").hide();
		}
		if ($('#select-all').length) {
			$("#select-all").prop('checked', false);
		}
	});
</script>