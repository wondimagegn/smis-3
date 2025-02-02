<?php ?>

<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="examGrades manage_ng">
<?php echo $this->Form->create('ExamGrade',array('novalidate' => true));?>
<div class="smallheading"><?php echo __('View Cheating Students');?></div>
<div onclick="toggleViewFullId('ListPublishedCourse')"><?php 
	if (!empty($studentsWithCheatingCases)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span><?php
		}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span><?php
		}
?></div>
<div id="ListPublishedCourse" style="display:<?php echo (!empty($studentsWithCheatingCases) ? 'none' : 'display'); ?>">
<table cellspacing="0" cellpadding="0" class="fs14">
	<tr>
		<td style="width:15%">Academic Year:</td>
		<td style="width:20%"><?php echo $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
		<td style="width:15%">Semester:</td>
		<td style="width:50%"><?php echo $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => (isset($semester_selected) ? $semester_selected : false))); ?></td>
	</tr>
	<tr>
		<td>Program:</td>
		<td><?php echo $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programs, 'default' => (isset($program_id) ? $program_id : false))); ?></td>
		<td>Program Type:</td>
		<td><?php echo $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $program_types, 'default' => (isset($program_type_id) ? $program_type_id : false))); ?></td>
	</tr>
	<?php
	if(!(isset($departments[0]) && $departments[0] == 0)) {
	?>
	<tr>
		<td>Department:</td>
		<td colspan="3"><?php echo $this->Form->input('department_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $departments, 'default' => (isset($department_id) ? $department_id : false))); ?></td>
	</tr>
	<?php
	}
	?>
	<tr>
		<td colspan="6">
		<?php echo $this->Form->submit(__(( isset($search_button_label) && !empty($search_button_label) ? $search_button_label : 'List Cheating ')), array('name' => 'viewCheatingStudentList','class'=>'tiny radius button bg-blue', 'div' => false)); ?>
		</td>
	</tr>
</table>
</div>

<?php 

if(isset($studentsWithCheatingCases) && !empty($studentsWithCheatingCases)) {
?>

<table>
<tr>
    <th style="width:5%">S.No</th>
	<th style="width:18%">Full Name</th>
	<th style="width:12%">Student ID</th>
	<th style="width:10%">Previous Cheating Count</th>
	<th style="width:25%">Current Course</th>
	<th style="width:25%">Current Cheating Grade</th>
	
</tr>
<?php
$count = 0;
foreach($studentsWithCheatingCases as $key => $student) {
	$count++;
	?>
	<tr>
	    <td><?php echo $count; ?></td>
		<td><?php echo $student['full_name']; ?></td>
		<td><?php echo $student['studentnumber']; ?></td>
		<td><?php echo $student['previousCheatingCount']; ?></td>
		<td><?php echo $student['recentCheatingCourse']; ?></td>
		<td><?php echo $student['grade']; ?></td>
		
		
	</tr>
	<?php
}
?>
</table>
<?php

}
?>
<?php echo $this->Form->end(); ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

<script>
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
