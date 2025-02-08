<script>
var number_of_students = <?php echo (isset($student_registers) ? (count($student_registers)+count($student_adds)) : 0); ?>;
function check_uncheck(id) {
	var checked = ($('#'+id).attr("checked") == 'checked' ? true : false);
	for(i = 1; i <= number_of_students; i++) {
		$('#StudentSelection'+i).attr("checked", checked);
	}
}

$(document).ready(function () {
	$("#PublishedCourse").change(function(){
		//serialize form data
		var pc_id = $("#PublishedCourse").val();
		window.location.replace("/attendances/take_attendance/"+pc_id);
	});
	
	$("#AttendanceDate").change(function(){
		//serialize form data
		var pc_id = $("#PublishedCourse").val();
		var a_d = $("#AttendanceDate").val();
		window.location.replace("/attendances/take_attendance/"+pc_id+'/'+a_d);
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
<div class="attendances take_attendance">
<?php echo $this->Form->create('Attendance');?>
<div class="smallheading"><?php echo __('Take Attendance'); ?></div>
<table cellspacing="0" cellpadding="0" class="fs14">
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
<?php
if(isset($attendance_to_be_taken_dates)) {
	if(count($attendance_to_be_taken_dates) > 1) {
?>
	<tr>
		<td>Attendance Date:</td>
		<td colspan="3"><?php echo $this->Form->input('attendance_date', array('id' => 'AttendanceDate', 'label' => false, 'type' => 'select', 'options' => $attendance_to_be_taken_dates, 'default' => $selected_attendance_date)); ?></td>
	</tr>
<?php
	}
	else {
?>
	<tr>
		<td colspan="4" class="rejected">There is no attendance date to display. It is because either schedule is not defined or course is old enough or course grade is submitted.</td>
	</tr>
<?php
	}
}
?>
</table>
<div id="AttendanceDiv">
<?php
if(isset($student_registers) || isset($student_adds)) {
	echo $this->Form->input('select_all', array('type' => 'checkbox', 'id' => 'SelectAll', 'div' => false, 'label' => false, 'onchange' => 'check_uncheck(this.id)')).' Select All';
	$st_count = 0;
}
if(isset($student_registers)) {
	if(!empty($student_registers)) {
		?>
		<p class="fs13">Students who register for <u><?php echo $course_detail['course_title'].' ('.$course_detail['course_code'].')'; ?></u> course in <u><?php echo $section_detail['name']; ?></u> section.</p>
		<table class="less_height">
			<tr>
				<th style="width:3%">N<u>o</u></th>
				<th style="width:25%">Student Name</th>
				<th style="width:12%">ID</th>
				<th style="width:10%">Attendance</th>
				<th style="width:50%">Remark</th>
			</tr>
		<?php
		foreach($student_registers as $key => $student_register) {
			$st_count++;
			?>
			<tr>
				<td><?php echo $st_count; ?></td>
				<td><?php 
					echo $this->Form->input('Student.'.$st_count.'.student_id', array('type' => 'hidden', 'value' => $student_register['Student']['id']));
					echo $student_register['Student']['first_name'].' '.$student_register['Student']['middle_name'].' '.$student_register['Student']['last_name']; 
				?></td>
				<td><?php echo $student_register['Student']['studentnumber']; ?></td>
				<td><?php 
				if(!empty($student_register['Attendance'])) {
					echo $this->Form->input('Student.'.$st_count.'.id', array('type' => 'hidden', 'value' => $student_register['Attendance']['id']));
					echo $this->Form->input('Student.'.$st_count.'.attendance', array('type' => 'checkbox', 'label' => false, 'id' => 'StudentSelection'.$st_count, 'checked' => ($student_register['Attendance']['attendance'] == 1 ? true : false)));
				}
				else {
					echo $this->Form->input('Student.'.$st_count.'.attendance', array('type' => 'checkbox', 'label' => false, 'id' => 'StudentSelection'.$st_count));
				}
				?></td>
				<td><?php 
				echo $this->Form->input('Student.'.$st_count.'.remark', array('type' => 'text', 'label' => false, 'style' => 'width:250px', 'value' => (!empty($student_register['Attendance']) ? $student_register['Attendance']['remark'] : ''))); ?></td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
}
if(isset($student_adds)) {
	if(!empty($student_adds)) {
		?>
		<p class="fs13">Students who add <u><?php echo $course_detail['course_title'].' ('.$course_detail['course_code'].')'; ?></u> course in <u><?php echo $section_detail['name']; ?></u> section.</p>
		<table class="less_height">
			<tr>
				<th style="width:3%">N<u>o</u></th>
				<th style="width:25%">Student Name</th>
				<th style="width:12%">ID</th>
				<th style="width:10%">Attendance</th>
				<th style="width:50%">Remark</th>
			</tr>
		<?php
		foreach($student_adds as $key => $student_add) {
			$st_count++;
			?>
			<tr>
				<td><?php echo $st_count; ?></td>
				<td><?php 
					echo $this->Form->input('Student.'.$st_count.'.student_id', array('type' => 'hidden', 'value' => $student_add['Student']['id']));
					echo $student_add['Student']['first_name'].' '.$student_add['Student']['middle_name'].' '.$student_add['Student']['last_name']; 
				?></td>
				<td><?php echo $student_add['Student']['studentnumber']; ?></td>
				<td><?php 
				if(!empty($student_add['Attendance'])) {
					echo $this->Form->input('Student.'.$st_count.'.id', array('type' => 'hidden', 'value' => $student_add['Attendance']['id']));
					echo $this->Form->input('Student.'.$st_count.'.attendance', array('type' => 'checkbox', 'label' => false, 'id' => 'StudentSelection'.$st_count, 'checked' => ($student_add['Attendance']['attendance'] == 1 ? true : false)));
				}
				else {
					echo $this->Form->input('Student.'.$st_count.'.attendance', array('type' => 'checkbox', 'label' => false, 'id' => 'StudentSelection'.$st_count));
				}
				?></td>
				<td><?php 
				echo $this->Form->input('Student.'.$st_count.'.remark', array('type' => 'text', 'label' => false, 'style' => 'width:250px', 'value' => (!empty($student_add['Attendance']) ? $student_add['Attendance']['remark'] : ''))); ?></td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
}
if((isset($student_registers) || isset($student_adds))) {
	if((empty($student_registers) && empty($student_adds))) {
		echo '<div id="flashMessage" class="info-box info-message"><span></span>There is no student in the selected section who register and/or add '.($course_detail['course_title'].' ('.$course_detail['course_code'].')').' course.</div>';
	}
	else {
		echo $this->Form->submit(__('Record Attendance'), array('name' => 'getGradeReport', 'div' => false));
	}
}
?>
</div>
<?php echo $this->Form->end(); ?>
</div>
