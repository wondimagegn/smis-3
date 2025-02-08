<script type="text/javascript">
	<?php
	if (isset($grade_scale['GradeScaleDetail'])) {
		$grade_scale_count = 0;
		foreach ($grade_scale['GradeScaleDetail'] as $key => $scale_detail) { ?>
			grade_scale[<?php echo $grade_scale_count; ?>] = Array();
			grade_scale[<?php echo $grade_scale_count; ?>][0] = <?php echo $scale_detail['minimum_result']; ?>;
			grade_scale[<?php echo $grade_scale_count; ?>][1] = <?php echo $scale_detail['maximum_result']; ?>;
			grade_scale[<?php echo $grade_scale_count; ?>][2] = '<?php echo $scale_detail['grade']; ?>';
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
}

if ($view_only) { ?>
	<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>To Manage this Published course Exam Result and Grade, the assigned instructor account should be closed/deactivated by the system administrator. To achieve this, you can request an account deactivation request for your staffs that are on study leave or left the university permanentyly in Security > Users > Deactivate User.</div>
	<?php
}

if (count($students) > 0 && !empty($course_detail) && !empty($section_detail) && !empty($exam_types)){
	echo '<br><h6 class="fs14 text-gray">' . $course_detail['course_code_title'] . ' exam result entry for ' . $section_detail['name'] . ' section.</h6>';//. ((isset($lastGradeSubmissionDate) && $lastGradeSubmissionDate > date('Y-m-d')) || (isset($grade_submission_status) && $grade_submission_status['grade_submited'] == 0) ? '<strong style="color:red;"> The result you type will be automatically saved.</strong>': '' ). '</h6>';
	if (isset($course_assignment_detail[0]) && !empty($course_assignment_detail[0])) {
		echo '<h6 class="fs14 text-gray">Instructor: ' . $course_assignment_detail[0]['Staff']['Title']['title'] . ' ' . $course_assignment_detail[0]['Staff']['full_name'] . ' (' . $course_assignment_detail[0]['Staff']['Position']['position'] . ')' . '</h6>';
		echo '<br style="line-height:0.5;">';
	}
}

if (empty($published_course_id)) { ?>
	<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>Please select a course.</div>
	<?php
} else if (count($student_makeup) <= 0 && count($student_makeup) <= 0) { ?>
	<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>The system unable to find list of students who are assigned for result/supplementary exam entry for the course you selected. Please contact your department for more information.</div>
	<?php
} else {

	if (!empty($exam_types) && count($student_makeup) > 0) {
		echo '<hr><h6 class="fs14 text-gray">Students who are taking makeup exam for '. $course_detail['course_code_title'] . ' course.</h6><hr>';
		$students_process = $student_makeup;
		$makeup_exam = true;
		$count = ((count($students)*count($exam_types))+(count($student_adds)*count($exam_types))+1);
		$st_count = (count($students)+count($student_adds));
		$in_progress = (count($students)+count($student_adds));
		$this->set(compact('students_process', 'count', 'st_count', 'in_progress', 'makeup_exam'));
		echo $this->element('exam_sheet_grade_entry');
	}
}
	
if (!$view_only && (count($student_makeup) > 0 || count($student_adds) > 0 || count($students) > 0) && !empty($exam_types)) { ?>
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
} else { ?>

	<?php 
} ?>
