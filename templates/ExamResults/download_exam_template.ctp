<?php

if ($view_only) { ?>
	<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>To manage this published course exam result and grade, the assigned instructor account should be closed by the system administrator.</div>
	<?php
}
if (count($students) > 0 && !empty($course_detail) && !empty($section_detail) && !empty($exam_types)) {
}

if (empty($published_course_id)) { ?>
	<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>Please select assigned course from the list.</div>
	<?php
} else if (empty($exam_types)) {

	//$students_process = $students;
	if (count($students)) { ?>
		<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>Please Create Exam Setup before importing results.</div>
		<?php
		if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT && isset($published_course_id) && !empty($published_course_id)) {
			echo "<a href='/examTypes/exam_type_mgt_for_instructor/" . $published_course_id . "'>Create Exam Setup</a>";
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR && isset($published_course_id) && !empty($published_course_id)) {
			echo "<a href='/examTypes/add/" . $published_course_id . "'>Create Exam Setup</a>";
		}
	} else { ?>
		<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>Students must registered before importing result so come back later when registration is completed.</div>
		<?php
	}
} else if (count($students) <= 0 && count($student_adds) <= 0) { ?>
	<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>The system is unable to find list of students who are registered for the course you selected. Please contact your department/registrar for more information.</div>
	<?php
} else { 

	//debug($students);

	// check if grade was generated and submitted show message import is impossible, and give preview
	if (isset($checkIfGradeSubmitted) && !empty($checkIfGradeSubmitted)) { ?>

		<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>Exam grade has already been submitted. you can not modify it by uploading exam results rather, you can request grade change online.</div>

		<?php

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
			echo '<hr><h6 class="fs16 text-gray">Students who add ' . $course_detail['course_title'] . ' (' . $course_detail['course_code'] . ') course from other section/s.</h6><hr>';
			$students_process = $student_adds;
			$makeup_exam = false;
			$count = ((count($students) * count($exam_types)) + 1);
			$st_count = count($students);
			$in_progress = count($students);
			$this->set(compact('students_process', 'count', 'st_count', 'in_progress', 'makeup_exam'));
			echo $this->element('exam_sheet');
		}

		if (!empty($exam_types) && count($student_makeup) > 0) { //debug($student_makeup);
			echo '<hr><h6 class="fs16 text-gray">Students who are taking makeup exam for ' . $course_detail['course_title'] . ' (' . $course_detail['course_code'] . ') course.</h6><hr>';
			$students_process = $student_makeup;
			$makeup_exam = true;
			$count = ((count($students) * count($exam_types)) + (count($student_adds) * count($exam_types)) + 1);
			$st_count = (count($students) + count($student_adds));
			$in_progress = (count($students) + count($student_adds));
			$this->set(compact('students_process', 'count', 'st_count', 'in_progress', 'makeup_exam'));
			echo $this->element('exam_sheet');
		}
	} else { ?>

		<hr>
		<blockquote>
			<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
			<span style="text-align:justify;" class="fs14 text-gray">
				<b class="text-black">
					<i>Before importing the excel,
					<ol>
						<li>Make sure that exam Type is defined for your course.</li>
						<li>No asssesment is filled to any of your students.</li>
						<li>Download the Excel Import Template below and save the file in <span class="rejected">CSV (comma delimited)</span> format</li>
						<li><span class="rejected">Do not delete or modify the first row headers. (IMPORTANT)</span></li>
						<li>Populate all the assesments for all students, remove students from the file if any of the assesment is missing or doesn't have any assesment.</li>
						<li>Upload your CSV file here. You will be redirected to Exam Result Management up on sucessfull import of student results.</li>
						<li>You can <span class="rejected">upload only once per course</span>. You can use Exam Grade Managemet for submitting/modifying exam grade and grade changes.</li>
					</ol>
					</i> 
				</b> 
			<br>
			<a href='/examTypes/assessement_template/<?= $published_course_id; ?>'>Download Import Excel Template(<?= count($students) ? count($students) : 0  ?> students)</a></li>
		</blockquote>
		<hr>

		<fieldset style="padding-bottom: 5px;padding-top: 5px;">
			<legend>&nbsp; &nbsp; &nbsp; Upload your CSV &nbsp; &nbsp; &nbsp;</legend>
			<div class="row">
				<div class="large-6 columns">
					<br>
					<?= $this->Form->file('File'); ?>
				</div>
				<div class="large-6 columns">
					<br>
					<?= $this->Form->submit('Upload', array('class' => 'tiny radius button bg-blue')); ?>
				</div>
			</div>
		</fieldset>
		<?php
	}
}
