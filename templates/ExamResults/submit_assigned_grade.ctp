<?= $this->Form->create('ExamResult'); ?>
<script type="text/javascript">
	var grade_scale = Array();
	<?php
	if (isset($grade_scale['GradeScaleDetail'])) {
		$grade_scale_count = 0;
		foreach ($grade_scale['GradeScaleDetail'] as $key => $scale_detail) { ?>
			grade_scale[<?= $grade_scale_count; ?>] = Array();
			grade_scale[<?= $grade_scale_count; ?>][0] = <?= $scale_detail['minimum_result']; ?>;
			grade_scale[<?= $grade_scale_count; ?>][1] = <?= $scale_detail['maximum_result']; ?>;
			grade_scale[<?= $grade_scale_count; ?>][2] = '<?= $scale_detail['grade']; ?>';
			<?php
			$grade_scale_count++;
		}
	} ?>

	/* function updateExamGradeChange(obj, st_count) {
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
	} */

	function submitGrdeChangeRequest(id, st_count) 
	{
		if (document.getElementById('' + id + '_result_' + st_count + '').value == "") {
			alert('You are required to submit exam result.');
			return false;
		} else if (document.getElementById('' + id + '_reason_' + st_count + '').value == "") {
			alert('You are required to submit exam grade change reason.');
			return false;
		} else {
			return true;
		}
	}

	function toggleView(obj) 
	{
		if ($('#c' + obj.id).css("display") == 'none') {
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		} else {
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		}
		$('#c' + obj.id).toggle("slow");
	}

	var grade = new Array();
	
	/* function updateExamTotal(obj, row, exam_num, percent, exam_name, sum_it) {
		var sum = 0;
		var result=0;
		var invalid = false;
		var result_found = false;
		if(obj.value != "" && isNaN(obj.value)) {
				alert('Please enter a valid result.');
				$('#'+obj.id).focus();
				$('#'+obj.id).select();
				return false;
		}
		else if (obj.value != "" && parseFloat(obj.value) > parseFloat(percent) ) {
			alert('The maximum value of "'+exam_name+'" exam result is '+percent+'.');
				$('#'+obj.id).focus();
				$('#'+obj.id).select();
				return false;
		}
		else if (obj.value != "" && parseFloat(obj.value) < 0) {
			alert('The minimum value of "'+exam_name+'" exam result is 0.');
				$('#'+obj.id).focus();
				$('#'+obj.id).select();
				return false;
		}
		if(!sum_it)
			return true;
		//contingency ???
		for(var i = 1; i <= exam_num; i++) {
			result = $('#result_'+row+'_'+i).val();
			
			if(isNaN(result)) {
				invalid = true;
				
				break;
			}
			else {
				if(result != "") {
					sum += parseFloat(result);
					result_found = true;
				}
			}
		}
		if(invalid) {
			$('#total_100_'+row).empty();
			$('#total_100_'+row).append('---');
		}
		else {
			$('#total_100_'+row).empty();
			if(result_found)
				$('#total_100_'+row).append(sum);
			else
				$('#total_100_'+row).append('---');
		}
	} */

	function updateExamTotal(obj, row, exam_num, percent, exam_name, sum_it) 
	{
		var sum = 0;
		var result = 0;
		var invalid = false;
		var result_found = false;

		if (obj.value != "" && isNaN(obj.value)) {
			/*
			alert('Please enter a valid result.');
			$('#'+obj.id).focus();
			$('#'+obj.id).blur();
			*/
			return false;
		} else if (obj.value != "" && parseFloat(obj.value) > parseFloat(percent)) {
			// alert('The maximum value of "'+exam_name+'" exam result is '+percent+'.');
			$('#' + obj.id).focus();
			$('#' + obj.id).select();
			return false;
		} else if (obj.value != "" && parseFloat(obj.value) < 0) {
			// alert('The minimum value of "'+exam_name+'" exam result is 0.');
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
					sum += parseFloat(result);
					result_found = true;
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

		/* var autoSaveResult = result;

		$.ajax({
			url: "/examResults/autoSaveResult",
			type: 'POST',
			data: $('form').serialize(),
			success: function(data) {}
		}); 
		return result; */
	}

	function confirmGradeSubmitCancelation() {
		return confirm("Are you sure you want to cancel your grade submission? Only Grades that are rolled back for resubmission by the registrar, those rejected by the departmeent(if any) and submitted grades pending for department approval will be canceled for resubmission. All accepted grades, either by the department or registrar will stay intact.");
	}

	function courseInProgress(c, obj) {
		if (obj.checked) {
			grade[c] = window.document.getElementById('G_'+c).innerHTML;
			window.document.getElementById('G_'+c).innerHTML = '**';
		} else {
			window.document.getElementById('G_'+c).innerHTML = grade[c];
		}
	}

	function showHideGradeScale() 
	{
		if ($("#ShowHideGradeScale").val() == 'Show Grade Scale') {
			var p_course_id = $("#PublishedCourse").val();
			$("#GradeScale").empty();
			$("#GradeScale").append('Loading ...');
			var formUrl = '/published_courses/get_course_grade_scale/'+p_course_id;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: p_course_id,
				success: function(data,textStatus,xhr){
					$("#GradeScale").empty();
					$("#GradeScale").append(data);
					$("#ShowHideGradeScale").attr('value', 'Hide Grade Scale');
				},
				error: function(xhr,textStatus,error) {
					alert(textStatus);
				}
			});
		} else {
			$("#GradeScale").empty();
			$("#ShowHideGradeScale").attr('value', 'Show Grade Scale');
		}
		return false;
	}

	function showHideGradeStatistics() 
	{
		if ($("#ShowHideGradeDistribution").val() == 'Show Grade Distribution') {
			var p_course_id = $("#PublishedCourse").val();
			$("#GradeDistribution").empty();
			$("#GradeDistribution").append('Loading ...');
				var formUrl = '/published_courses/get_course_grade_stats/'+p_course_id;
				$.ajax({
					type: 'get',
					url: formUrl,
					data: p_course_id,
					success: function(data,textStatus,xhr) {
						$("#GradeDistribution").empty();
						$("#GradeDistribution").append(data);
						$("#ShowHideGradeDistribution").attr('value', 'Hide Grade Distribution');
					},
					error: function(xhr,textStatus,error) {
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
		$(".AYS").change(function() {
			//serialize form data
			$("#flashMessage").remove();
			var ay = $("#AcadamicYear").val();
			$("#PublishedCourse").empty();
			$("#AcadamicYear").attr('disabled', true);
			$("#PublishedCourse").attr('disabled', true);
			$("#Semester").attr('disabled', true);
			$("#ExamResultDiv").empty();
			$("#ExamResultDiv").append('<p>Loading ...</p>');
			//get form action
			var formUrl = '/course_instructor_assignments/get_assigned_grade_entry_for_instructor/' + ay + '/'+$("#Semester").val();
			$.ajax({
				type: 'get',
				url: formUrl,
				data: ay,
				success: function(data,textStatus,xhr) {
					$("#PublishedCourse").empty();
					$("#PublishedCourse").append(data);
						//Items list
						var pc = $("#PublishedCourse").val();
						if (pc != null || pc != '') {
						//get form action
						var formUrl = '/examResults/get_registrar_assigned_grade_entry_form/' + pc;
						$.ajax({
							type: 'get',
							url: formUrl,
							data: pc,
							success: function(data,textStatus,xhr) {
								$("#AcadamicYear").attr('disabled', false);
								$("#PublishedCourse").attr('disabled', false);
								$("#Semester").attr('disabled', false);
								$("#ExamResultDiv").empty();
								$("#ExamResultDiv").append(data);
							},
							error: function(xhr,textStatus,error){
								alert(textStatus);
							}
						});
					} else {
						$("#AcadamicYear").attr('disabled', false);
						$("#PublishedCourse").attr('disabled', false);
						$("#Semester").attr('disabled', false);
					}	
					//End of items list
				},
				error: function(xhr,textStatus,error) {
					alert(textStatus);
				}
			});
			
			return false;
		});

		//Students list
		$("#PublishedCourse").change(function() {
			//serialize form data
			$("#flashMessage").remove();
			$("#AcadamicYear").attr('disabled', true);
			$("#PublishedCourse").attr('disabled', true);
			$("#Semester").attr('disabled', true);
			var pc = $("#PublishedCourse").val();
			$("#ExamResultDiv").empty();
			$("#ExamResultDiv").append('<p>Loading ...</p>');
			//get form action
			var formUrl = '/examResults/get_registrar_assigned_grade_entry_form/' + pc;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: pc,
				success: function(data,textStatus,xhr){
					$("#ExamResultDiv").empty();
					$("#ExamResultDiv").append(data);
					$("#AcadamicYear").attr('disabled', false);
					$("#PublishedCourse").attr('disabled', false);
					$("#Semester").attr('disabled', false);
				},
				error: function(xhr,textStatus,error){
					alert(textStatus);
				}
			});
			
			return false;
		});
	});
</script>
<div class="box" >
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Submit Registrar Assigned Grade Assignments'); ?></span>
        </div>
    </div>
    <div class="box-body" >
    	<div class="row">
			<div class="large-12 columns">
				<div class="examResults form" ng-app="resultEntryForm">
					<div style="margin-top: -30px;">
                    	<fieldset style="padding-bottom: 5px;padding-top: 10px;">
                        	<!-- <legend>&nbsp;&nbsp; Search &nbsp;&nbsp;</legend> -->
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('acadamic_year', array('class' => 'AYS', 'id' => 'AcadamicYear', 'label' => 'Acadamic Year: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($selected_acadamic_year) ? $selected_acadamic_year : $defaultacademicyear))); ?>
								</div>
								<div class="large-2 columns">
									<?= $this->Form->input('semester', array('class' => 'AYS', 'id' => 'Semester', 'label' => 'Semester: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => Configure::read('semesters'), 'default' => $selected_semester)); ?>
								</div>
								<div class="large-7 columns">
									<?= $this->Form->input('published_course_id', array('id' => 'PublishedCourse', 'label' => 'Assigned Course: ', 'type' => 'select', 'options' => $publishedCourses, 'default' => (isset($published_course_combo_id) && !empty($published_course_combo_id) ? $published_course_combo_id : ''), 'style' => 'width:95%;')); ?>
								</div>
							</div>
						</fieldset>
					</div>

					<div id="ExamResultDiv" ng-controller="resultEntryFormCntrl">
						<?php
						if (!empty($students) || !empty($student_adds) || !empty($student_makeup)) {
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
								<?php
							}

							//echo '<p style="font-size:14px">'.$course_detail['course_title'].' ('.$course_detail['course_code'].') '.' exam result entry for '.$section_detail['name'].' section.</p>';
							echo '<br><h6 class="fs14 text-gray">' . $course_detail['course_code_title'] . ' exam result entry for ' . $section_detail['name'] . ' section.'; // ((isset($lastGradeSubmissionDate) && $lastGradeSubmissionDate > date('Y-m-d')) || (isset($grade_submission_status) && $grade_submission_status['grade_submited'] == 0) ? '<strong style="color:red;"> The result you type will be automatically saved.</strong>': '' ). '</h6>';
							if (isset($course_assignment_detail[0]) && !empty($course_assignment_detail[0])) {
								echo '<h6 class="fs14 text-gray">Instructor: ' . $course_assignment_detail[0]['Staff']['Title']['title'] . ' ' . $course_assignment_detail[0]['Staff']['full_name'] . ' (' . $course_assignment_detail[0]['Staff']['Position']['position'] . ')' . '</h6>';
								echo '<br style="line-height:0.5;">';
							} ?>

							<?php
							if (count($student_makeup) > 0) {
								echo '<hr><h6 class="fs14 text-gray">Students who are taking makeup exam for '. $course_detail['course_code_title'] . ' course.</h6><hr>';
								$students_process = $student_makeup;
								$makeup_exam = true;
								$count = ((count($students) * count($exam_types)) + (count($student_adds) * count($exam_types)) + 1);
								$st_count = (count($students) + count($student_makeup));
								$in_progress = (count($students) + count($student_makeup));
								$this->set(compact('students_process', 'count', 'st_count', 'in_progress', 'makeup_exam'));
								echo $this->element('exam_sheet_grade_entry');
							}

							if ($display_grade) { ?>
								<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>If a student fails to take exams which are set as mandatory in the exam setup, the system will automatically give NG to the student.</div>
								<?php
							} ?>
						
							<?php
							if (!$view_only) { ?>
								<div style="overflow-x:auto;">
									<table cellpadding="0" cellspacing="0" class="table">
										<tr>

											<td style="width:20%">
												<?php
												$button_options = array('name' => 'saveExamResult', 'div' => false, 'class' => 'tiny radius button bg-blue');
												if ($display_grade || ($grade_submission_status['grade_submited_fully'] && $grade_submission_status['grade_dpt_rejected'] == 0)) {
													$button_options['disabled'] = 'true';
												}
												echo $this->Form->submit(__('Save Exam Result'), $button_options);
												?>
											</td>

											<td style="width:20%">
												<?php
												$button_options = array();
												$button_options['name'] = 'previewExamGrade';
												$button_options['div'] = 'false';
												$button_options['class'] = 'tiny radius button bg-blue';

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
													echo '<p>Make sure you save exam result before you preview grade.</p>';
												} ?>
											</td>

											<td style="width:20%">
												<?php
												$button_options = array();
												$button_options['name'] = 'submitExamGrade';
												$button_options['div'] = 'false';
												$button_options['class'] = 'tiny radius button bg-blue';
												//$button_options['onclick'] = 'return confirm("Please make sure that you save exam result before you preview. Do you want to continue to preview grade?")';

												if (!$display_grade || ($grade_submission_status['grade_submited_fully'] && $grade_submission_status['grade_dpt_rejected'] == 0)) {
													$button_options['disabled'] = 'true';
													echo $this->Form->submit(__('Submit Grade'), $button_options);
												} else {
													echo $this->Form->submit(__('Submit Grade'), $button_options);
												}

												//echo $this->Form->submit(__('Submit Grade'), $button_options);

												if (!($grade_submission_status['grade_submited_fully'] && $grade_submission_status['grade_dpt_rejected'] == 0) && !$display_grade) {
													echo '<p>Preview exam grade before you submit grade.</p>';
												} ?>
											</td>

											<td style="width:25%">
												<?php //debug($grade_submission_status);
												$button_options = array('name' => 'cancelExamGrade', 'div' => false, 'class' => 'tiny radius button bg-blue');
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

											<td style="width:15%">
												<?php 
												if (/* isset($students_process) && count($students_process) */ $grade_submission_status['grade_submited']) {
													$button_options = array('name' => 'exportExamGrade', 'div' => false, 'class' => 'tiny radius button bg-blue');
													$button_options['disabled'] = 'false';
													echo $this->Form->submit(__('Export MarkSheet', true), $button_options);
												} else {
													echo '&nbsp;';
												} ?>
											</td>

										</tr>
									</table>
								</div>
								<?php
							}
						} else if (count($publishedCourses) <= 1) { ?>
							<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>Please select Academic Year and Semester to get list of courses you are assigned.</div>
							<?php
						} else { ?>
							<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>Please Select a Course to get list of students to enter exam result.</div>
							<?php
						} ?>
					</div>
				</div>
	  		</div>
		</div>
    </div>
</div>
<?= $this->Form->end(); ?>
