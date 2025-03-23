<?php ?>
<script type="text/javascript">
$(document).ready(function () {
	$("#PublishedCourse").change(function(){
		//serialize form data
		var pc_id = $("#PublishedCourse").val();
		window.location.replace("/attendances/<?php echo $this->request->action; ?>/"+(pc_id != 0 ? pc_id : ''));
	});
});	
function toggleViewFullId(id) {
	if($('#'+id).css("display") == 'none') {
		$('#'+id+'Img').attr("src", '/img/minus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Hide Filter');
		}
	else {
		$('#'+id+'Img').attr("src", '/img/plus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Display Filter');
		}
	$('#'+id).toggle("slow");
}
</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="attendances <?php echo $this->request->action; ?>" style="margin-bottom:0px">
<?php echo $this->Form->create('Attendance');?>
<div class="smallheading"><?php echo __('View instructor\'s attendance');?></div>
<div onclick="toggleViewFullId('ListPublishedCourse')"><?php 
	if (!empty($publishedCourses) && count($publishedCourses) > 1) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span><?php
		}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span><?php
		}
?></div>
<div id="ListPublishedCourse" style="display:<?php echo (!empty($publishedCourses) && count($publishedCourses) > 1 ? 'none' : 'display'); ?>">
<table class="fs14" style="margin-bottom:0px">
	<tr>
		<td style="width:15%">Acadamic Year:</td>
		<td style="width:25%"><?php echo $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($selected_acadamic_year) && !empty($selected_acadamic_year) ? $selected_acadamic_year : $defaultacademicyear))); ?></td>
		<td style="width:15%">Semester:</td>
		<td style="width:55%"><?php echo $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => $selected_semester)); ?></td>
	</tr>
	<tr>
		<td>Program:</td>
		<td><?php echo $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programs, 'default' => $program_id)); ?></td>
		<td>Program Type:</td>
		<td><?php echo $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $program_types, 'default' => $program_type_id)); ?></td>
	</tr>
	<tr>
		<td colspan="4">
		<?php echo $this->Form->submit(__('List Published Courses'), array('name' => 'listPublishedCourses', 'div' => false,'class'=>'tiny radius button bg-blue')); ?>
		</td>
	</tr>
</table>
</div>
<?php
if(!empty($publishedCourses) && count($publishedCourses) > 1) {
?>
<table class="fs14" style="margin-bottom:0px">
	<tr>
		<td style="width:15%">Published Courses:</td>
		<td colspan="3" style="width:85%">
<?php
	echo $this->Form->input('published_course_id', array('id' => 'PublishedCourse', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $publishedCourses, 'default' => $published_course_combo_id));
?>
		</td>
	</tr>
</table>
<?php
	}
if(isset($attendance_taken_date_list) && !empty($attendance_taken_date_list)) {
?>
<table class="fs14" style="margin-top:0px">
	<tr>
		<td style="width:17%">Attendance Start Date:</td>
		<td style="width:20%"><?php echo $this->Form->input('attendance_start_date', array('id' => 'AttendanceStartDate', 'label' => false, 'type' => 'select', 'options' => $attendance_taken_date_list)); ?></td>
		<td style="width:17%">Attendance End Date:</td>
		<td style="width:46%"><?php echo $this->Form->input('attendance_end_date', array('id' => 'AttendanceEndDate', 'label' => false, 'type' => 'select', 'options' => $attendance_taken_date_list)); ?></td>
	</tr>
</table>
<?php
echo $this->Form->submit(__('View Attendance'), array('name' => 'viewCourseAttendance', 'div' => false));
}
?>
<div id="AttendanceDiv">
<?php echo $this->element('attendance_view_sheet'); ?>
</div>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
