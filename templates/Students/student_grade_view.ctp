<?php ?>
<?php ?>
<script type="text/javascript">


function toggleView(obj) {
	if($('#c'+obj.id).css("display") == 'none')
		$('#i'+obj.id).attr("src", '/img/minus2.gif');
	else
		$('#i'+obj.id).attr("src", '/img/plus2.gif');
	$('#c'+obj.id).toggle("slow");
}

</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="examGrades student_grade_view">
<?php echo $this->Form->create('ExamGrade');?>
<div class="smallheading"><?php echo __('Student Examination Grade Report');?></div>
<?php
if(!empty($acadamic_years)) {
	?>
	<p class="fs13">Please select academic year and semester for which you want to get grade report.</p>
	<table cellspacing="0" cellpadding="0" class="fs14">
		<tr>
			<td style="width:12%">Academic Year:</td>
			<td style="width:20%"><?php echo $this->Form->input('academic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acadamic_years, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
			<td style="width:8%">Semester:</td>
			<td style="width:25%"><?php echo $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => (isset($semester_selected) ? $semester_selected : false))); ?></td>
			<td style="width:35%"><?php echo $this->Form->submit(__('Grade Report'), array('name' => 'myGradeReport','class'=>'tiny radius button bg-blue','div' => false)); ?></td>
		</tr>
	</table>
	<?php
	if(isset($student_copy)) {
		
		if(!empty($student_copy['courses'])) {
		?>
<style>
.low_padding_table tr td{
padding:2px
}
</style>
<table class="low_padding_table fs13">
	<tr>
		<td style="width:12%; font-weight:bold">Fullname:</td>
		<td style="width:30%"><?php echo $student_copy['Student']['full_name']; ?></td>
		<td style="width:12%; font-weight:bold">ID:</td>
		<td style="width:46%"><?php echo $student_copy['Student']['studentnumber']; ?></td>
	</tr>

	<tr>
		<td style="width:12%; font-weight:bold">Program:</td>
		<td style="width:30%"><?php echo $student_copy['Program']['name']; ?></td>
		<td style="width:12%; font-weight:bold">Program Type:</td>
		<td style="width:46%"><?php echo $student_copy['ProgramType']['name']; ?></td>
	</tr>
	<tr>
		<td style="font-weight:bold"><?php echo (strpos(strtolower($student_copy['College']['name']), 'institute') !== false ? 'Institute' : 'College'); ?>:</td>
		<td><?php echo $student_copy['College']['name']; ?></td>
		<td style="font-weight:bold">Department:</td>
		<td><?php echo $student_copy['Department']['name']; ?></td>
	</tr>
	<tr>
		<td style="font-weight:bold">Section:</td>
		<td><?php echo $student_copy['Section']['name']; ?></td>
		<td style="font-weight:bold">Year Level:</td>
		<td><?php echo $student_copy['YearLevel']['name']; ?></td>
	</tr>
	<tr>
		<td style="font-weight:bold">Academic Year:</td>
		<td><?php echo $student_copy['academic_year']; ?></td>
		<td style="font-weight:bold">Semester:</td>
		<td><?php echo $student_copy['semester']; ?></td>
	</tr>
</table>
<table class="low_padding_table fs13">
	<tr>
		<th style="width:5%">N<u>o</u></th>
		<th style="width:13%">Course Code</th>
		<th style="width:35%">Course Title</th>
		<th style="width:10%; text-align:center">Credit Hour</th>
		<th style="width:10%; text-align:center">Grade</th>
		<th style="width:10%; text-align:center">Grade Point</th>
		<th style="width:17%"></th>
	</tr>
		<?php
$c_count = 0;
$credit_hour_sum = 0;
$grade_point_sum = 0;
foreach($student_copy['courses'] as $key => $course_reg_add) {
$c_count++;
if(isset($course_reg_add['Grade']['grade'])) {
	if(isset($course_reg_add['Grade']['used_in_gpa']) && $course_reg_add['Grade']['used_in_gpa'] == 1) {
		$credit_hour_sum += $course_reg_add['Course']['credit'];
		$grade_point_sum += ($course_reg_add['Grade']['point_value']*$course_reg_add['Course']['credit']);
	}
	else if(strcasecmp($course_reg_add['Grade']['grade'], 'I') == 0) {
		$credit_hour_sum += $course_reg_add['Course']['credit'];
	}
}
else {
	$credit_hour_sum += $course_reg_add['Course']['credit'];
}
?>
	<tr>
		
        <td onclick="toggleView(this)" id="<?php echo $c_count; ?>"><?php echo $this->Html->image('plus2.gif', array('id' => 'i'.$c_count)); ?></td>

		<td><?php echo $course_reg_add['Course']['course_code']; ?></td>
		<td><?php echo $course_reg_add['Course']['course_title']; ?></td>
		<td style="text-align:center"><?php echo $course_reg_add['Course']['credit']; ?></td>
		<td style="text-align:center"><?php echo (isset($course_reg_add['Grade']['grade']) ? $course_reg_add['Grade']['grade'] : '---'); ?></td>
		<td style="text-align:center"><?php echo (isset($course_reg_add['Grade']['grade']) && isset($course_reg_add['Grade']['used_in_gpa']) && $course_reg_add['Grade']['used_in_gpa'] == 1 ? ($course_reg_add['Grade']['point_value']*$course_reg_add['Course']['credit']) : '---'); ?></td>
		<td>&nbsp;</td>
	</tr>

   <tr id="c<?php echo $c_count; ?>" style="display:none">
		<td colspan="7">
			<table>
			
			<?php 
                         $totalResult=0;
			 $examSetupCount=count($course_reg_add['ExamType']);
foreach($course_reg_add['ExamType'] as $examResultKey=>$ExamResultEntry) { ?>
			<tr>
				<td><?php echo $ExamResultEntry['ExamType']['exam_name']. '('.$ExamResultEntry['ExamType']['percent'].'%)' ?></td>
				<td><?php echo $ExamResultEntry['ExamResult']['result']; 

$totalResult+=$ExamResultEntry['ExamResult']['result']; 
?></td>
			</tr>

			<?php } ?>
			<tr>
			       <td> Total(100%) </td>
			       <td><?php echo $totalResult;?> </td>
			</tr>
			
			</table>
		</td>
	</tr>
<?php
}
?>
	<tr>
		<td colspan="3" style="text-align:right; font-weight:bold">TOTAL</td>
		<td style="text-align:center; font-weight:bold"><?php echo ($credit_hour_sum != 0 ? $credit_hour_sum : '---'); ?></td>
		<td>&nbsp;</td>
		<td style="text-align:center; font-weight:bold"><?php echo ($grade_point_sum != 0 ? $grade_point_sum : '---'); ?></td>
		<td>&nbsp;</td>
	</tr>
</table>
<table class="low_padding_table fs13">
	<tr>
		<td style="width:30%">
			<table>
				<tr>
					<td colspan="2" style="font-weight:bold">Previous</td>
				</tr>
				<tr>
					<td style="width:50%">Credit Hour Taken: </td>
					<td style="width:50%"><?php echo (isset($student_copy['PreviousStudentExamStatus']['previous_credit_hour_sum']) ? $student_copy['PreviousStudentExamStatus']['previous_credit_hour_sum'] : '---'); ?></td>
				</tr>
				<tr>
					<td>Grade Point Earned: </td>
					<td><?php echo (isset($student_copy['PreviousStudentExamStatus']['previous_grade_point_sum']) ? $student_copy['PreviousStudentExamStatus']['previous_grade_point_sum'] : '---'); ?></td>
				</tr>
				<tr>
					<td>SGPA: </td>
					<td><?php echo (isset($student_copy['PreviousStudentExamStatus']['sgpa']) ? $student_copy['PreviousStudentExamStatus']['sgpa'] : '---'); ?></td>
				</tr>
				<tr>
					<td>CGPA:</td>
					<td><?php echo (isset($student_copy['PreviousStudentExamStatus']['cgpa']) ? $student_copy['PreviousStudentExamStatus']['cgpa'] : '---'); ?></td>
				</tr>
				<tr>
					<td>Status:</td>
					<td<?php echo (isset($student_copy['PreviousStudentExamStatus']['academic_status_id']) ? ($student_copy['PreviousStudentExamStatus']['academic_status_id'] == 4 ? ' class="rejected"' : ($student_copy['PreviousStudentExamStatus']['academic_status_id'] == 3 ? ' class="on-process"' : ' class="accepted"')) : ''); ?>><?php echo (isset($student_copy['PreviousAcademicStatus']['name']) ? $student_copy['PreviousAcademicStatus']['name'] : '---'); ?></td>
				</tr>
			</table>
		</td>
		<td style="width:5%"></td>
		<td style="width:30%">
			<table>
				<tr>
					<td colspan="2" style="font-weight:bold">This Semester</td>
				</tr>
				<tr>
					<td style="width:50%">Credit Hour Taken: </td>
					<td style="width:50%"><?php echo ($credit_hour_sum != 0 ? $credit_hour_sum : '---'); ?></td>
				</tr>
				<tr>
					<td>Grade Point Earned: </td>
					<td><?php echo ($grade_point_sum != 0 ? $grade_point_sum : '---'); ?></td>
				</tr>
				<tr>
					<td>SGPA: </td>
					<td><?php echo (isset($student_copy['StudentExamStatus']['sgpa']) ? $student_copy['StudentExamStatus']['sgpa'] : '---'); ?></td>
				</tr>
				<tr>
					<td>CGPA:</td>
					<td><?php echo (isset($student_copy['StudentExamStatus']['cgpa']) ? $student_copy['StudentExamStatus']['cgpa'] : '---'); ?></td>
				</tr>
				<tr>
					<td>Status:</td>
					<td<?php echo (isset($student_copy['StudentExamStatus']['academic_status_id']) ? ($student_copy['StudentExamStatus']['academic_status_id'] == 4 ? ' class="rejected"' : ($student_copy['StudentExamStatus']['academic_status_id'] == 3 ? ' class="on-process"' : ' class="accepted"')) : ''); ?>><?php echo (isset($student_copy['AcademicStatus']['name']) ? $student_copy['AcademicStatus']['name'] : '---'); ?></td>
				</tr>
			</table>
		</td>
		<td style="width:5%"></td>
		<td style="width:30%">
			<table>
				<tr>
					<td colspan="2" style="font-weight:bold">Cumulative Academic Status</td>
				</tr>
				<tr>
					<td style="width:60%">Total Credit Hour Taken: </td>
					<td style="width:40%"><?php 
					if($credit_hour_sum != 0 && isset($student_copy['PreviousStudentExamStatus']['previous_credit_hour_sum'])) {
						echo $student_copy['PreviousStudentExamStatus']['previous_credit_hour_sum'] + $credit_hour_sum;
					}
					else if($credit_hour_sum != 0) {
						echo $credit_hour_sum;
					}
					else {
						echo '---';
					}
					?></td>
				</tr>
				<tr>
					<td>Total Grade Point Earned: </td>
					<td><?php 
					if($grade_point_sum != 0 && isset($student_copy['PreviousStudentExamStatus']['previous_grade_point_sum'])) {
						echo $student_copy['PreviousStudentExamStatus']['previous_grade_point_sum'] + $grade_point_sum;
					}
					else if($grade_point_sum != 0) {
						echo $grade_point_sum;
					}
					else {
						echo '---';
					}
					?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php
	}
	else {
		echo '<div id="flashMessage" class="info-box info-message"><span></span>There is neither course registration nor course add for the selected academic year and semester. Please select another academic year and/or semester.</div>';
	}
	}
}
else {
	echo '<div id="flashMessage" class="info-box info-message"><span></span>You do not have any course registration. Please use this feature after course registration to view your exam result and grade.</div>';
}
?>
<?php echo $this->Form->end(); ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
