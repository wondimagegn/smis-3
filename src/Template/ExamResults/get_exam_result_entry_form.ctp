<script type="text/javascript">
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
</script>
<?php
if (!empty($published_course_id)) {
	if (isset($grade_scale['error']) || empty($grade_scale)) { 
		if (isset($grade_scale['error'])) { ?>
			<hr>
			<div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;"><span style='margin-right: 15px;'></span><?= $grade_scale['error']; ?></div>
			<hr>
			<?php
		} else { ?>
			<hr>
			<div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;"><span style='margin-right: 15px;'></span>Grade scale for the selected course is not found in the system.</div>
			<hr>
			<?php
		}
	} else {
		if ((isset($grade_scale) && !empty($grade_scale['Course']['id']) && $grade_scale['Course']['thesis'] == 1 && ($grade_scale['Course']['Curriculum']['program_id'] == PROGRAM_PhD || $grade_scale['Course']['Curriculum']['program_id'] == PROGRAM_POST_GRADUATE)) && isset($grade_scale['GradeType']['used_in_gpa']) && $grade_scale['GradeType']['used_in_gpa'] == 1) { ?>
			<hr>
			<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;"><span style='margin-right: 15px;'></span>Currently, <?= $grade_scale['Course']['course_code_title']; ?> course is set as a <?=$grade_scale['Course']['Curriculum']['program_id'] == PROGRAM_POST_GRADUATE ? 'Thesis/Projecct' : 'Dissertation'; ?> course and associated to "<?= $grade_scale['GradeScale']['name']; ?>" from "<?= $grade_scale['GradeType']['type']; ?>" grading type which uses point values of the awarded grades in CGPA calculations. Please communicate the department and check the correctness of the grade type specified on <?= $grade_scale['Course']['Curriculum']['curriculum_detail']; ?> curriculum before submitting the grades.</div>
			<hr>
			<?php
		} 
		if (empty($exam_types) && !$view_only) { ?>
			<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>You need to create Exam Setup before you enter Exam Result.</div>
			<?php
			if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
				echo "<a href='/examTypes/exam_type_mgt_for_instructor/" . $published_course_id . "' class='tiny radius button bg-blue'>Create Exam Setup</a>";
			} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR) {
				echo "<a href='/examTypes/add/" . $published_course_id . "' class='tiny radius button bg-blue'>Create Exam Setup</a>";
			}
		} else { ?>
			<div style="border:1px solid #91cae8; padding:10px;">
				<span><input type="button" value="Show Grade Scale" onclick="showHideGradeScale()" id="ShowHideGradeScale" class="tiny radius button bg-blue"> &nbsp;&nbsp;&nbsp; </span>
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
	}
}

if ($view_only) { ?>
	<hr>
	<div id="flashMessage" class="warning-box warning-message fs15" style="font-family: 'Times New Roman', Times, serif; text-align: justify;"><span style="margin-right: 15px;"></span>To manage this published course exam result and grade, <?= isset($course_assignment_detail[0]) && !empty($course_assignment_detail[0]) ? ' ' . $course_assignment_detail[0]['Staff']['full_name'] . '\'s' : 'the assigned instructor\'s'; ?>  account should be closed by the system administrator first. To achieve this, <?= (isset($show_user_deactivation_link) && $show_user_deactivation_link) ? ' you can initiate a user account deactivation request for ' . (isset($course_assignment_detail[0]) && !empty($course_assignment_detail[0]) ? $course_assignment_detail[0]['Staff']['full_name'] . ' and other staffs in your department' : 'your department stafss')  . ' that are on study leave or left the university permanentyly.<br><br> <a href="/users/deactivate_account/2" target="_blank">Deactivate User Account</a>. <br><br> NB: Any account deactivation request should be confirmed by a system administrator with in 72 hours to be effective else, you have to reinitiate your deactivation request again.' : ((isset($course_assignment_detail[0]) && !empty($course_assignment_detail[0]) ? $course_assignment_detail[0]['Staff']['full_name'] . '\'s department  ' : ' assigned instructor\'s department ') . ' can initiate a user deactivation request in Security > Users > Users > Deactivate user.'); ?></div>
	<hr>
	<?php
}

if (count($students) > 0 && !empty($course_detail) && !empty($section_detail) && !empty($exam_types)) {
	echo '<br><h6 class="fs14 text-gray">' . $course_detail['course_code_title'] . ' exam result entry for ' . $section_detail['name'] . ' section.'. (/* (!$view_only) ||  */(isset($lastGradeSubmissionDate) && $lastGradeSubmissionDate > date('Y-m-d')) || (isset($grade_submission_status) && $grade_submission_status['grade_submited'] == 0) ? '<strong style="color:red;"> The result you type will be automatically saved.</strong>': '' ). '</h6>';
	if (isset($course_assignment_detail[0]) && !empty($course_assignment_detail[0])) {
		echo '<h6 class="fs14 text-gray">Instructor: ' . $course_assignment_detail[0]['Staff']['Title']['title'] . ' ' . $course_assignment_detail[0]['Staff']['full_name'] . ' (' . $course_assignment_detail[0]['Staff']['Position']['position'] . ')</h6>';
		if (isset($lastGradeSubmissionDate) && $lastGradeSubmissionDate < date('Y-m-d')) {
			echo '<h6 class="fs14 text-red">Grade submission deadline was ' . $this->Time->timeAgoInWords($lastGradeSubmissionDate, array('format' => 'M j, Y', 'end' => '1 month', 'accuracy' => array('days' => 'days'))) . '  and submission is closed.</h6>';
		}
	}
	echo '<br style="line-height:0.5;">';
}

if (empty($published_course_id)) { ?>
	<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>Please Select a Course to get list of students to enter exam result.</div>
	<?php
} else if (empty($exam_types) && isset($this->data['ExamResult']['published_course_id']) && !empty($this->data['ExamResult']['published_course_id'])) { ?>
	<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>You need to create Exam Setup before you enter Exam Result.</div>
	<?php
	if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
		echo "<a href='/examTypes/exam_type_mgt_for_instructor/" . $published_course_id . "' class='tiny radius button bg-blue'>Create Exam Setup</a>";
	} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR) {
		echo "<a href='/examTypes/add/" . $published_course_id . "' class='tiny radius button bg-blue'>Create Exam Setup</a>";
	}
} else if (count($students) == 0 && count($student_adds) == 0) { ?>
	<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>The system is unable to find list of students who are registered or added for the course you selected. Please contact your department/registrar for further information.</div>
	<?php
} else { 
	//debug($students);
	
	if (isset($lastGradeSubmissionDate) && !empty($lastGradeSubmissionDate)) {
		$this->set(compact('lastGradeSubmissionDate'));
	}
	
	if (!empty($exam_types) && count($students) > 0) {
		$in_progress = 0;
		$students_process = $students;
		$makeup_exam = false;
		$count = 1;
		$st_count = 0;
		$this->set(compact('students_process', 'count', 'st_count', 'in_progress', 'makeup_exam'));
		echo $this->element('exam_sheet');
	}

	if (!empty($exam_types) && count($student_adds) > 0) {
		echo '<hr><h6 class="fs16 text-gray">Students who added ' . $course_detail['course_code_title'] . ' course from other sections.</h6><hr>';
		$students_process = $student_adds;
		$makeup_exam = false;
		$count = ((count($students) * count($exam_types)) + 1);
		$st_count = count($students);
		$in_progress = count($students);
		$this->set(compact('students_process', 'count', 'st_count', 'in_progress', 'makeup_exam'));
		echo $this->element('exam_sheet');
	}

	if (!empty($exam_types) && count($student_makeup) > 0) {
		echo '<hr><h6 class="fs16 text-gray">Students who are taking Makeup Exam for ' . $course_detail['course_code_title'] . ' course.</h6><hr>';
		$students_process = $student_makeup;
		$makeup_exam = true;
		$count = ((count($students) * count($exam_types)) + (count($student_adds) * count($exam_types)) + 1);
		$st_count = (count($students) + count($student_adds));
		$in_progress = (count($students) + count($student_adds));
		$this->set(compact('students_process', 'count', 'st_count', 'in_progress', 'makeup_exam'));
		echo $this->element('exam_sheet');
	}
}

if (!$view_only && (count($student_makeup) > 0 || count($student_adds) > 0 || count($students) > 0) && !empty($exam_types)) { ?>
	<br style="line-height:0.5;">
	<table cellpadding="0" cellspacing="0" class="table">
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
				
				//$button_options['onclick'] = 'return confirm("Please make sure that you save exam result before you preview. Do you want to continue to preview grade?")';
				if (!$display_grade || ($grade_submission_status['grade_submited_fully'] && $grade_submission_status['grade_dpt_rejected'] == 0)) {
					$button_options['disabled'] = 'true';
				}
				//debug($lastGradeSubmissionDate);

				if (isset($lastGradeSubmissionDate) && $lastGradeSubmissionDate < date('Y-m-d')) {
					//echo '<span style="color:red">   Grade Submission  was ' . $this->Format->humanTiming($lastGradeSubmissionDate) . ' ago  and submission is closed.</span>';
					echo '<span style="color:red">Grade submission deadline was ' . $this->Time->timeAgoInWords($lastGradeSubmissionDate, array('format' => 'M j, Y', 'end' => '1 month', 'accuracy' => array('days' => 'days'))) . '  and submission is closed.</span>';
				} else {
					echo $this->Form->submit(__('Submit Grade'), $button_options);
				}

				/*
				if ($grade_submission_status['grade_submited_fully']) {
					echo '<p>All exam grade is submited.</p>';
				}
				else */

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
					echo '<p>Cancellation is available only when grade is submitted &amp; pending approval.</p>';
				} else {
					echo '<p>Cancellation is only for grades pending department approval.</p>';
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
	<?php
} else { 
	if (/* isset($students_process) &&  */ isset($grade_submission_status['grade_submited']) && $grade_submission_status['grade_submited']) { ?>
		<hr>
		<div class="row">
			<div class="large-2 columns">
				<?php
				$button_options = array('name' => 'exportExamGrade', 'div' => false, 'class' => 'tiny radius button bg-blue');
				$button_options['disabled'] = 'false';
				echo $this->Form->submit(__('Export Xls', true), $button_options);
				?>
			</div>
			<div class="large-2 columns">
				<?php
				// $button_options = array('name' => 'exportExamGradePDF', 'div' => false, 'class' => 'tiny radius button bg-blue');
				// $button_options['disabled'] = 'false';
				// echo $this->Form->submit(__('Export PDF', true), $button_options);
				?>
			</div>
			<div class="large-8 columns">
			</div>
		</div>
		<?php
	}
} ?>