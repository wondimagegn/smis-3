<?php ?>
<script>
$(document).ready(function () {
	$("#PublishedCourse").change(function(){
		//serialize form data
		var pc_id = $("#PublishedCourse").val();
		window.location.replace("/attendances/<?php echo $this->request->action; ?>/"+pc_id);
	});
	
	$(".AYS").change(function(){
		//serialize form data
		$("#flashMessage").remove();
		var ay = $("#AcadamicYear").val();
		$("#PublishedCourse").empty();
		$("#AcadamicYear").attr('disabled', true);
		$("#PublishedCourse").attr('disabled', true);
		$("#Semester").attr('disabled', true);
		$("#AttendanceDiv").empty();
		//$("#AttendanceDiv").append('<p>Loading ...</p>');
		//get form action
		var formUrl = '/course_instructor_assignments/get_assigned_courses_of_instructor_by_section_for_combo/'+ay+'/'+$("#Semester").val();
		$.ajax({
			type: 'get',
			url: formUrl,
			data: ay,
			success: function(data,textStatus,xhr){
					$("#PublishedCourse").empty();
					$("#PublishedCourse").append(data);
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
<style>
table.less_height tr td {
	padding:4px;
}
</style>

<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="attendances <?php echo $this->request->action; ?>">
<?php echo $this->Form->create('Attendance');?>
<div class="smallheading"><?php echo __('View Attendance'); ?></div>
<table cellspacing="0" cellpadding="0" class="fs14" style="margin-bottom:0px">
	<tr>
		<td style="width:15%">Academic Year:</td>
		<td style="width:17%"><?php echo $this->Form->input('acadamic_year', array('class' => 'AYS', 'id' => 'AcadamicYear', 'label' => false, 'style' => 'width:100px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => $selected_acadamic_year)); ?></td>
		<td style="width:10%">Semester:</td>
		<td style="width:58%"><?php echo $this->Form->input('semester', array('class' => 'AYS', 'id' => 'Semester', 'label' => false, 'style' => 'width:100px', 'type' => 'select', 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => $selected_semester)); ?></td>
	</tr>
	<tr>
		<td style="width:15%">Assigned Course:</td>
		<td colspan="3"><?php echo $this->Form->input('published_course_id', array('id' => 'PublishedCourse', 'label' => false, 'type' => 'select', 'options' => $publishedCourses, 'default' => $published_course_combo_id)); ?></td>
	</tr>
</table>
<?php
if(isset($attendance_taken_date_list) && !empty($attendance_taken_date_list)) {
?>
<table class="fs14" style="margin-bottom:5px">
	<tr>
		<td style="width:17%">Attendance Start Date:</td>
		<td style="width:20%"><?php echo $this->Form->input('attendance_start_date', array('id' => 'AttendanceStartDate', 'label' => false, 'type' => 'select', 'options' => $attendance_taken_date_list)); ?></td>
		<td style="width:17%">Attendance End Date:</td>
		<td style="width:46%"><?php echo $this->Form->input('attendance_end_date', array('id' => 'AttendanceEndDate', 'label' => false, 'type' => 'select', 'options' => $attendance_taken_date_list)); ?></td>
	</tr>
</table>
<?php
echo $this->Form->submit(__('View Attendance'), array('name' => 'viewCourseAttendance','class'=>'tiny radius button bg-blue', 'div' => false));
}
?>
<div id="AttendanceDiv">
<?php echo $this->element('attendance_view_sheet'); ?>
</div>
<?php echo $this->Form->end(); ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
