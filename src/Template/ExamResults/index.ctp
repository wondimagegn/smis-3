<script type="text/javascript">
//Sub Cat Combo 1
$(document).ready(function () {
	$(".AYS").change(function(){
		//serialize form data
		$("#flashMessage").remove();
		var ay = $("#AcadamicYear").val();
		$("#PublishedCourse").empty();
		$("#AcadamicYear").attr('disabled', true);
		$("#PublishedCourse").attr('disabled', true);
		$("#Semester").attr('disabled', true);
		$("#ExamResultDiv").empty();
		$("#ExamResultDiv").append('<p>Loading ...</p>');
		//get form action
		var formUrl = '/course_instructor_assignments/get_assigned_courses_of_instructor_by_section_for_combo/'+ay+'/'+$("#Semester").val();
		$.ajax({
			type: 'get',
			url: formUrl,
			data: ay,
			success: function(data,textStatus,xhr){
					$("#PublishedCourse").empty();
					$("#PublishedCourse").append(data);
						//Items list
						var pc = $("#PublishedCourse").val();
						//get form action
						var formUrl = '/examResults/get_exam_result_view_page/'+pc;
						$.ajax({
							type: 'get',
							url: formUrl,
							data: pc,
							success: function(data,textStatus,xhr){
									$("#AcadamicYear").attr('disabled', false);
									$("#PublishedCourse").attr('disabled', false);
									$("#Semester").attr('disabled', false);
									$("#ExamResultDiv").empty();
									$("#ExamResultDiv").append(data);
							},
							error: function(xhr,textStatus,error){
									alert(textStatus);
							}
						});
						//End of items list
			},
			error: function(xhr,textStatus,error){
					alert(textStatus);
			}
		});
		
		return false;
	});
//Items List
	$("#PublishedCourse").change(function(){
		//serialize form data
		var pc = $("#PublishedCourse").val();
		$("#ExamResultDiv").empty();
		$("#ExamResultDiv").append('<p>Loading ...</p>');
		//get form action
		var formUrl = '/examResults/get_exam_result_view_page/'+pc;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: pc,
			success: function(data,textStatus,xhr){
					$("#ExamResultDiv").empty();
					$("#ExamResultDiv").append(data);
			},
			error: function(xhr,textStatus,error){
					alert(textStatus);
			}
		});
		
		return false;
	});
});
</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            

<div class="examResults index">
<?php echo $this->Form->create('ExamResult');?>
<div class="smallheading"><?php echo __('Course Exam Result View'); ?></div>
<table cellspacing="0" cellpadding="0">
	<tr>
		<td style="width:25%"><?php echo $this->Form->input('acadamic_year', array('class' => 'AYS', 'id' => 'AcadamicYear', 'style' => 'width:75px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => $defaultacademicyear)); ?></td>
		<td style="width:20%"><?php echo $this->Form->input('semester', array('class' => 'AYS', 'id' => 'Semester', 'style' => 'width:75px', 'type' => 'select', 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => $selected_semester)); ?></td>
		<td style="width:55%"><?php echo $this->Form->input('published_course_id', array('id' => 'PublishedCourse', 'label' => 'Assigned Course', 'type' => 'select', 'options' => $publishedCourses, 'default' => $published_course_combo_id)); ?></td>
	</tr>
</table>
<div id="ExamResultDiv">
<?php
if(count($publishedCourses) <= 1) {
	echo '<div id="flashMessage" class="info-box message-box" style="text-align:center"><span></span>Please select academic year and semester to get list of courses you are assigned.</div>';
}
else {
	echo '<div id="flashMessage" class="info-box message-box" style="text-align:center"><span></span>Please select a course to get list of students to view exam result.</div>';
}
?>
</div>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
