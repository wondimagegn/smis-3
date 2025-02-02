<script type="text/javascript">
$(document).ready(function () {
	$("#GenerateExamSchedule").click(function(){
		$("#flashMessage").remove();
		$("#GenerateExamScheduleInfo").empty();
		$("#GenerateExamScheduleInfo").append('<p style="padding-top:20px" class="info-box info-message"><span></span>Schedule is being generated and it will take some time to process. Please be patent till it get finished.</p>');
		return true;
	});
});
</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="examSchedule form">
	<?php //echo $this->Form->create(array('controller' => 'exam_schedule', 'action' => 'generate_exam_schedule'));

echo $this->Form->create('ExamSchedule');
	?>
<?php
if(isset($unable_to_schedule) && !empty($unable_to_schedule)) {
	?>
	<table class="fs12">
	<?php
	foreach($unable_to_schedule as $k => $v) {
	?>
	<tr>
		<td><?php echo $v['course'].' (Section: '.$v['section'].')'; ?></td>
	</tr>
	<tr>
		<td>Reason: <?php echo $v['reason']; ?></td>
	</tr>
	<?php
	}
	?>
	<table>
	<?php
}
?>
	<div class="smallheading">Generate Exam Schedule</div>
	<p class="fs13"><strong>Important Note:</strong> If exam schedule is different for different program types, then run the schedule for each independent program types. Otherwise the system will make the schedule for the same courses (from the same curriculum) on the same day and session.</p>
	<table>
		<tr>
			<td style="width:10%">Academic Year:</td>
			<td style="width:24%"><?php echo $this->Form->input('acadamic_year', array('options' => $acadamicYears, 'label' => false, 'style' => 'width:150px')); ?></td>
			<td style="width:8%">Semester:</td>
			<td style="width:25%"><?php echo $this->Form->input('semester', array('options' => $semesters, 'label' => false, 'style' => 'width:150px')); ?></td>
			<td style="width:8%">Program:</td>
			<td style="width:25%"><?php echo $this->Form->input('program_id', array('options' => $programs, 'label' => false, 'style' => 'width:200px')); ?></td>
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
			<style>
				div.checkbox label {
					font-size:12px
				}
			</style>
			<fieldset style="margin:0px; padding:0px">
				<legend style="font-weight:bold"><?php echo __('Avoid Add Students Schedule Conflict'); ?></legend>
					<table cellpadding="0" cellspacing="0" style="margin:0px; padding:0px; width:95%; margin-left:15px">
						<tr>
							<td style="margin:0px; padding:0px; background:transparent"><?php echo $this->Form->input('to_be_scheduled_course_add_in_college', array('type' => 'checkbox', 'label' => 'Who add to be scheduled published course (Within College)', 'checked' => ((!isset($this->request->data['ExamSchedule']['to_be_scheduled_course_add_in_college']) || $this->request->data['ExamSchedule']['to_be_scheduled_course_add_in_college'] == 1) ? 'checked' : 'false'))); ?></td>
						</tr>
						<tr>
							<td style="margin:0px; padding:0px; background:transparent"><?php echo $this->Form->input('to_be_scheduled_course_add_cross_college', array('type' => 'checkbox', 'label' => 'Who add to be scheduled published course (Cross College)', 'checked' => ((!isset($this->request->data['ExamSchedule']['to_be_scheduled_course_add_in_college']) || $this->request->data['ExamSchedule']['to_be_scheduled_course_add_in_college'] == 1) ? 'checked' : 'false'))); ?></td>
						</tr>
						<tr>
							<td style="margin:0px; padding:0px; background:transparent"><?php echo $this->Form->input('section_students_add_in_college', array('type' => 'checkbox', 'label' => 'To be scheduled published course section students who add courses in another section (Within College)', 'checked' => ((!isset($this->request->data['ExamSchedule']['to_be_scheduled_course_add_in_college']) || $this->request->data['ExamSchedule']['to_be_scheduled_course_add_in_college'] == 1) ? 'checked' : 'false'))); ?></td>
						</tr>
						<tr>
							<td style="margin:0px; padding:0px; background:transparent"><?php echo $this->Form->input('section_students_add_cross_college', array('type' => 'checkbox', 'label' => 'To be scheduled published course section students who add courses in another section (Cross College)', 'checked' => ((!isset($this->request->data['ExamSchedule']['to_be_scheduled_course_add_in_college']) || $this->request->data['ExamSchedule']['to_be_scheduled_course_add_in_college'] == 1) ? 'checked' : 'false'))); ?></td>
						</tr>
						<tr>
							<td class="fs13" style="margin:0px; padding:0px; background:transparent; border-bottom:0px">
								Important Note: Even if you avoid cross college add students exam conflict, conflict at any time can be raised if other colleges didn't avoid add students exam schedule conflict. You can get latest conflict report and exam schedule adjustment tool from "Exam Schedule View".
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td colspan="6">
			<?php echo $this->Form->submit(__('Generate Exam Schedule'), array('name' => 'generateExamSchedule','class'=>'tiny radius button bg-blue', 'id' => 'GenerateExamSchedule', 'div' => false)); ?>
			<div id="GenerateExamScheduleInfo"></div>
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
