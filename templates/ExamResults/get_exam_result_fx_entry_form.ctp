<script type="text/javascript">
<?php
if(isset($grade_scale['GradeScaleDetail'])) {
	$grade_scale_count = 0;
	foreach($grade_scale['GradeScaleDetail'] as $key => $scale_detail) {
		?>
		grade_scale[<?php echo $grade_scale_count; ?>] = Array();
		grade_scale[<?php echo $grade_scale_count; ?>][0] = <?php echo $scale_detail['minimum_result']; ?>;
		grade_scale[<?php echo $grade_scale_count; ?>][1] = <?php echo $scale_detail['maximum_result']; ?>;
		grade_scale[<?php echo $grade_scale_count; ?>][2] = '<?php echo $scale_detail['grade']; ?>';
		<?php
		$grade_scale_count++;
	}
}
?>
</script>
<?php
if(!empty($published_course_id)) {
	?>
	<div style="border:1px solid #91cae8; padding:3px; margin-bottom:10px">
	   <span><input type="button" value="Show Grade Scale" onclick="showHideGradeScale()" id="ShowHideGradeScale" class="tiny radius button bg-blue"> &nbsp;&nbsp;&nbsp;</span>
	   <span><input type="button" value="Show Grade Distribution" onclick="showHideGradeStatistics()" id="ShowHideGradeDistribution" class="tiny radius button bg-blue"></span>
		
		<div style="margin-top:10px" id="GradeScale"></div>

		
		<div style="margin-top:10px" id="GradeDistribution"></div>
	</div>
	<?php
}

if($view_only) {
	echo '<div id="flashMessage" class="info-box info-message"><span></span>To manage this published course exam result and grade, the assigned instructor account should be closed by the system administrator.</div>';
}
if(count($students) > 0 && !empty($course_detail) && !empty($section_detail) && !empty($exam_types)){
	echo '<p style="font-size:14px">'.$course_detail['course_title'].' ('.$course_detail['course_code'].') '.' exam result entry for '.$section_detail['name'].' section.</p>';
	if(isset($course_assignment_detail[0]) && !empty($course_assignment_detail[0])){
		echo '<p style="font-size:14px">Instructor:'.$course_assignment_detail[0]['Staff']['Title']['title'].' '.$course_assignment_detail[0]['Staff']['full_name'].'('.$course_assignment_detail[0]['Staff']['Position']['position'].')';
	
	     if(isset($lastGradeSubmissionDate) && $lastGradeSubmissionDate < date('Y-m-d')){
           echo '<span style="color:red">   Grade Submission  was '.$this->Format->humanTiming($lastGradeSubmissionDate).' ago ! Please submit as soon possible.</span>';
		} 
		echo '</p>';
	}
   
 }

if(empty($published_course_id)) {
	echo '<div id="flashMessage" class="info-box info-message"><span></span>Please select a course.</div>';
} else if(count($student_makeup) <= 0 && count($student_makeup) <= 0) {
	echo '<div id="flashMessage" class="info-box info-message"><span></span>The system unable to find list of students who are assigned Fx supplementary exam for the course you selected. Please contact your department for more information.</div>';
}
else {
	if(!empty($exam_types) && count($student_makeup) > 0) 
	{
	echo '<p style="font-size:14px">Students who are taking makeup exam for '.$course_detail['course_title'].' ('.$course_detail['course_code'].') course.</p>';
		$students_process = $student_makeup;
		$makeup_exam = true;
		$count = ((count($students)*count($exam_types))+(count($student_adds)*count($exam_types))+1);
		$st_count = (count($students)+count($student_adds));
		$in_progress = (count($students)+count($student_adds));
		$this->set(compact('students_process', 'count', 'st_count', 'in_progress', 'makeup_exam'));
		echo $this->element('exam_sheet_fx');
	}
	
}
	
if(!$view_only && (count($student_makeup) > 0 || count($student_adds) > 0 || count($students) > 0) && !empty($exam_types)) {
?>
<table>
	<tr>
		<td style="width:20%">
		<?php
		$button_options = array('name'=>'saveExamResult','div'=>false,'class'=>'tiny radius button bg-blue');
		if($display_grade || ($grade_submission_status['grade_submited_fully'] && $grade_submission_status['grade_dpt_rejected'] == 0))
			$button_options['disabled'] = 'true';
		echo $this->Form->submit(__('Save Exam Result'), $button_options); 
		?>
		</td>
		<td style="width:20%">
		<?php
		$button_options = array();
		$button_options['name'] = 'previewExamGrade';
		$button_options['div'] = 'false';
		$button_options['class']='tiny radius button bg-blue';
		if(!$grade_submission_status['scale_defined'] || ($grade_submission_status['grade_submited_fully'] && $grade_submission_status['grade_dpt_rejected'] == 0))
			$button_options['disabled'] = 'true';
		if(!$display_grade)
			echo $this->Form->submit(__('Save & Preview Grade'), $button_options);
		else
			echo $this->Form->submit(__('Cancel Preview'), array('name' => 'cancelExamGradePreview', 'div' => false,'class'=>'tiny radius button bg-blue'));
		if(!$grade_submission_status['scale_defined'])
			echo '<p>Grade scale is not defined.</p>';
		else if(!$display_grade && !($grade_submission_status['grade_submited_fully'] && $grade_submission_status['grade_dpt_rejected'] == 0))
			//echo '<p>Make sure you save exam result before you preview grade.</p>';
		?>
		</td>
		<td style="width:20%">
		<?php
		$button_options = array();
		$button_options['name'] = 'submitExamGrade';
		$button_options['div'] = 'false';
		$button_options['class']='tiny radius button bg-blue';
		
		if(!$display_grade || ($grade_submission_status['grade_submited_fully'] && $grade_submission_status['grade_dpt_rejected'] == 0))
			$button_options['disabled'] = 'true';
		
		if(isset($lastGradeSubmissionDate) && $lastGradeSubmissionDate < date('Y-m-d')){
           echo '<span style="color:red">   Grade Submission  was '.$this->Format->humanTiming($lastGradeSubmissionDate).' ago  and submission is closed.</span>';
		} else {
			echo $this->Form->submit(__('Submit Grade'), $button_options);
		} 

		if(!($grade_submission_status['grade_submited_fully'] && $grade_submission_status['grade_dpt_rejected'] == 0) && !$display_grade)
			echo '<p>Preview exam grade before you submit grade.</p>';
		?>
		</td>
		<td style="width:25%">
		<?php 
			$button_options = array('name'=>'cancelExamGrade','div'=>false,
'class'=>'tiny radius button bg-blue');
			if(!$grade_submission_status['grade_submited'] || ($grade_submission_status['grade_submited'] && $grade_submission_status['grade_dpt_approved_fully']))
				$button_options['disabled'] = 'true';
			else
				$button_options['onClick'] = 'return confirmGradeSubmitCancelation()';
			echo $this->Form->submit(__('Cancel Submited Grade'), $button_options); 
			if(isset($button_options['disabled']))
				echo '<p>Cancellation is available only when grade is submitted &amp; pending approval.</p>';
			else
				echo '<p>Cancellation is only for grades pending department approval.</p>';
		?>
		</td>
		<td style="width:15%">
		   <?php 

			$button_options = array('name'=>'exportExamGrade','div'=>false,
'class'=>'tiny radius button bg-blue');
			$button_options['disabled'] = 'false';
			echo $this->Form->submit(__('Export MarkSheet', true), $button_options); 
		  ?>
		</td>
	</tr>
</table>
<?php
} else {
?>
<table>
	<tr>
	
		<td style="width:25%">
		 <?php 

			$button_options = array('name'=>'exportExamGradePDF','div'=>false,
'class'=>'tiny radius button bg-blue');
			$button_options['disabled'] = 'false';
			echo $this->Form->submit(__('Export PDF', true), $button_options); 
		  ?>
		</td>
		<td style="width:15%">
		   <?php 

			$button_options = array('name'=>'exportExamGrade','div'=>false,
'class'=>'tiny radius button bg-blue');
			$button_options['disabled'] = 'false';
			echo $this->Form->submit(__('Export Xls', true), $button_options); 
		  ?>
		</td>
        	<td style="width:20%">
	
		</td>
		<td style="width:20%">
		
		</td>
		<td style="width:20%">
	
		</td>
	</tr>
</table>
<?php 
}
?>
