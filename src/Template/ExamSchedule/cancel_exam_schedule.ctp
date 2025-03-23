<script type="text/javascript">
$(document).ready(function () {
	$("#CancelExamSchedule").click(function(){
		if(confirm('Are you sure you want to cancel the exam schedule?')) {
			return true;
		}
		else {
			return false;
		}
	});
});
</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="examSchedule form">
	<?php //echo $this->Form->create(array('controller' => 'exam_schedule', 'action' => 'cancel_exam_schedule'));

echo $this->Form->create('ExamSchedule');
	 ?>
	<div class="smallheading">Cancel Exam Schedule</div>
	<table>
		<tr>
			<td style="width:10%">Academic Year:</td>
			<td style="width:24%"><?php echo $this->Form->input('acadamic_year', array('options' => $acadamicYears, 'label' => false, 'style' => 'width:150px')); ?></td>
			<td style="width:8%">Semester:</td>
			<td style="width:25%"><?php echo $this->Form->input('semester', array('options' => $semesters, 'label' => false, 'style' => 'width:150px')); ?></td>
			<td style="width:8%">Program:</td>
			<td style="width:25%"><?php echo $this->Form->input('program_id', array('options' => $programs, 'label' => false, 'style' => 'width:200px;')); ?></td>
		</tr>
		<tr>
			<td>Program Type:</td>
			<td><?php echo $this->Form->input('program_type_id', array('options' => $programTypes, 'label' => false, 'style' => 'width:200px;height:auto;', 'multiple' => true)); ?></td>
			<td>Department:</td>
			<td><?php echo $this->Form->input('department_id', array('options' => $departments, 'label' => false, 'style' => 'width:200px;height:auto;', 'multiple' => true)); ?></td>
			<td>Year Level:</td>
			<td><?php echo $this->Form->input('year_level', array('options' => $yearLevels, 'label' => false, 'style' => 'width:200px;height:auto;', 'multiple' => true)); ?></td>
		</tr>
		<tr>
			<td colspan="6">
			<?php echo $this->Form->submit(__('Cancel Exam Schedule'), array('name' => 'cancelExamSchedule', 'id' => 'CancelExamSchedule','class'=>'tiny radius button bg-blue', 'div' => false)); ?>
			</td>
		</tr>
	</table>
	<?php
	
	?>
	<?php echo $this->Form->end();?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
