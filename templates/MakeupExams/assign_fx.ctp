<?php ?>
<script>
$(document).ready(function () {
	$("#PublishedCourse").change(function(){
		//serialize form data
		window.location.replace("/makeupExams/assign_fx/"+$("#PublishedCourse").val());
	});
});

</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<?php echo $this->Form->create('MakeupExam',array('novalidate' => true));?>

<div class="smallheading"><?php echo __('Assign Fx Supplementary Exam ');?></div>
<?php echo $this->element('publish_course_filter_by_dept'); ?>
<?php //debug($students_with_ng); 
if(!empty($students_with_fx)) {
?>
<div class="info-box info-message"><span></span>After you create a supplementary exam for Fx, the system will make available the student for exam result entry and grade submition to the instructor curentlly assigned to the selected course the student is taking.</div>
<table class="fs14">
	<tr>
		<td style="width:30%">Minute Number</td>
		<td style="width:70%"><?php echo $this->Form->input('MakeupExam.minute_number', array('label' => false, 'class' => 'fs14','value'=>'Fx Retake','readonly'=>'true','required')); ?></td>
	</tr>
</table>

<table class="fs14">
<tr>
	<th><?php echo $this->Form->input('select_all', array('type' => 'checkbox', 'id' => 'select-all','div' => false, 'label' => false, 'onchange' => 'check_uncheck(this.id)')); ?>Select All</th>
	<th>Full Name</th>
	<th>Student ID</th>
	<th>Current Grade</th>
	<th>Section</th>
	<th></th>
</tr>
<?php
$st_count = 0;
$isAllMakeUpApplied=count($students_with_fx);
foreach($students_with_fx as $key => $student) {
   debug($student);
	$st_count++;
	?>
	<tr>
		<td style="width:10%">
			<?php
			/* 
			if(empty($makeup_exam['ExamGrade']) && empty($makeup_exam['ExamResult']))
						echo $this->Html->link(__('Delete'), array('action' => 'delete', $makeup_exam['id']), null, sprintf(__('Are you sure you want to delete %s \'s makeup exam?'), $makeup_exam['student_name']));
					else
			*/
			if($student['makeupalreadyapplied']){
				echo 'Applied';
				$isAllMakeUpApplied--;
			} else {
				echo $this->Form->input('MakeupExam.'.$st_count.'.gp', array('type' => 'checkbox', 'label' => false,'class'=>'checkbox1', 'id' => 'StudentSelection'.$st_count));
			}
			echo $this->Form->input('MakeupExam.'.$st_count.'.student_id', array('type' => 'hidden', 'value' => $student['student_id']));
			if(isset($student['course_registration_id'])){
              echo $this->Form->input('MakeupExam.'.$st_count.'.course_registration_id', array('type' => 'hidden', 'value' => $student['course_registration_id']));
			} else if(isset($student['course_add_id'])){
			echo $this->Form->input('MakeupExam.'.$st_count.'.course_add_id', array('type' => 'hidden', 'value' => $student['course_add_id']));
		   }
		   /*
			echo $this->Form->input('MakeupExam.'.$st_count.'.published_course_id', array('type' => 'hidden', 'value' => $student['published_course_id']));
			*/
					?>
		</td>
		<td style="width:15%"><?php echo $student['full_name']; ?></td>
		<td style="width:10%"><?php echo $student['studentnumber']; ?></td>
		<td style="width:5%"><?php echo $student['grade'];?></td>
		<td style="width:10%"><?php
		echo $selectedPublishedCourseDetail['Section']['name'].'('.$selectedPublishedCourseDetail['YearLevel']['name'].')';
		?></td>
		<td style="width:10%">
			<?php 
			if(empty($student['result']) && $student['makeupalreadyapplied'] )
						echo $this->Html->link(__('Delete'), array('action' => 'deleteFxMakeupAssignment', $student['makeupalreadyapplied']), null, sprintf(__('Are you sure you want to delete %s \'s makeup exam?'), $student['full_name']));
			
			?>
		</td>
	</tr>
	<?php
}

echo "How many ".$isAllMakeUpApplied;
?>
</table>
<table>
<?php if(isset($sectionsHaveSameCourses) && !empty($sectionsHaveSameCourses)) { ?>
    <tr>
		<td style="width:25%">In which section students are going to take exam?</td>
		<td style="width:25%"><?php echo $this->Form->input('MakeupExam.exam_published_course_id', array('id' => 'ExamPublishedCourse', 'label' => false, 'type' => 'select', 'class' => 'fs14', 'options' => $sectionsHaveSameCourses)); ?></td>
	</tr>
	<?php } ?>
</table>
<?php
if($isAllMakeUpApplied!=0){
echo $this->Form->submit(__('Assign Fx Makeup'), array('name' => 'assignFxMakeupExam', 'div' => false,'class'=>'tiny radius button bg-blue')); 
}
}
?>
<?php echo $this->Form->end(); ?>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
