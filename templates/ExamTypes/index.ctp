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
		$("#ExamSetupDiv").empty();
		$("#ExamSetupDiv").append('<p>Loading ...</p>');
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
						var formUrl = '/examTypes/get_exam_type_view_page/'+pc;
						$.ajax({
							type: 'get',
							url: formUrl,
							data: pc,
							success: function(data,textStatus,xhr){
									$("#AcadamicYear").attr('disabled', false);
									$("#PublishedCourse").attr('disabled', false);
									$("#Semester").attr('disabled', false);
									$("#ExamSetupDiv").empty();
									$("#ExamSetupDiv").append(data);
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
		$("#ExamSetupDiv").empty();
		$("#ExamSetupDiv").append('<p>Loading ...</p>');
		//get form action
		var formUrl = '/examTypes/get_exam_type_view_page/'+pc;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: pc,
			success: function(data,textStatus,xhr){
					$("#ExamSetupDiv").empty();
					$("#ExamSetupDiv").append(data);
			},
			error: function(xhr,textStatus,error){
					alert(textStatus);
			}
		});
		
		return false;
	});
});


//Sub Cat Combo 2
$(document).ready(function () {
	$("#ItemMainCategoryList2").change(function(){
		//serialize form data
		$("#flashMessage").remove();
		var formData = $("#ItemMainCategoryList2").val();
		$("#ItemSubCategoryList2").empty();
		$("#ItemSubCategoryList2").attr('disabled', true);
		$("#RelatedItem").attr('disabled', true);
		//get form action
		var formUrl = '/item_sub_categories/get_sub_cat_combo/'+formData;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data,textStatus,xhr){
					$("#ItemSubCategoryList2").attr('disabled', false);
					$("#ItemSubCategoryList2").empty();
					$("#ItemSubCategoryList2").append(data);
						//Items list
						var subCat = $("#ItemSubCategoryList2").val();
						$("#RelatedItem").empty();
						//get form action
						var formUrl = '/items/get_items_combo/'+subCat;
						$.ajax({
							type: 'get',
							url: formUrl,
							data: subCat,
							success: function(data,textStatus,xhr){
									$("#RelatedItem").attr('disabled', false);
									$("#RelatedItem").empty();
									$("#RelatedItem").append(data);
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
	$("#ItemSubCategoryList2").change(function(){
		//serialize form data
		$("#flashMessage").remove();
		var subCat = $("#ItemSubCategoryList2").val();
		$("#RelatedItem").attr('disabled', true);
		$("#RelatedItem").empty();
		//get form action
		var formUrl = '/items/get_items_combo/'+subCat;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: subCat,
			success: function(data,textStatus,xhr){
					$("#RelatedItem").attr('disabled', false);
					$("#RelatedItem").empty();
					$("#RelatedItem").append(data);
			},
			error: function(xhr,textStatus,error){
					alert(textStatus);
			}
		});
		
		return false;
	});
});
</script>
<div class="examTypes index">
<div class="smallheading"><?php echo __('Course Exam Setup View'); ?></div>
<table cellspacing="0" cellpadding="0">
	<tr>
		<td style="width:25%"><?php echo $this->Form->input('acadamic_year', array('class' => 'AYS', 'id' => 'AcadamicYear', 'style' => 'width:75px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => $defaultacademicyear)); ?></td>
		<td style="width:20%"><?php echo $this->Form->input('semester', array('class' => 'AYS', 'id' => 'Semester', 'style' => 'width:75px', 'type' => 'select', 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => false)); ?></td>
		<td style="width:55%"><?php echo $this->Form->input('published_course_id', array('id' => 'PublishedCourse', 'label' => 'Assigned Course', 'type' => 'select', 'options' => $publishedCourses, 'default' => false)); ?></td>
	</tr>
</table>
<div id="ExamSetupDiv">
<?php
if(count($exam_types) > 0) {
?>
<table cellspacing="0" cellpadding="0" id="exam_setup">
	<tr>
		<th style="width:25%">Exam Type</th>
		<th style="width:15%">In Percent</th>
		<th style="width:10%">Order</th>
		<th style="width:20%">Date Created</th>
		<th style="width:30%">Date Modified</th>
	</tr>
<?php
foreach($exam_types as $key => $exam_type) {
?>
	<tr>
		<td><?php echo $exam_type['ExamType']['exam_name']; ?></td>
		<td><?php echo $exam_type['ExamType']['percent'].'%'; ?></td>
		<td><?php echo ($exam_type['ExamType']['order'] != 0 ? $exam_type['ExamType']['order'] : '---' ); ?></td>
		<td><?php echo $this->Format->humanize_date($exam_type['ExamType']['created']); ?></td>
		<td><?php echo $this->Format->humanize_date($exam_type['ExamType']['modified']); ?></td>
	</tr>
<?php
	}
?>
</table>
	<?php
	}
else
	echo '<p>There is not exam setup for the selected acadamic year, semester and course.</p>';
?>
</div>
</div>
