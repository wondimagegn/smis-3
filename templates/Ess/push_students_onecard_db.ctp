<script>
function toggleView(obj) {
	if($('#c'+obj.id).css("display") == 'none')
		$('#i'+obj.id).attr("src", '/img/minus2.gif');
	else
		$('#i'+obj.id).attr("src", '/img/plus2.gif');
	$('#c'+obj.id).toggle("slow");
}
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
            
<div class="studentList form">
<?php echo $this->Form->create('Ess');?>
<div class="smallheading"><?php echo __('Add Student to One Card Database'); ?></div>
<div onclick="toggleViewFullId('ListPublishedCourse')">
<?php 
	if (!empty($students_for_onecard_list)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span><?php
		}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span><?php
		}
?></div>
<div id="ListPublishedCourse" style="display:<?php echo (!empty($students_for_onecard_list) ? 'none' : 'display'); ?>">
<table cellspacing="0" cellpadding="0" class="fs13">
	<tr>
		<td style="width:10%">Program:</td>
		<td style="width:25%"><?php echo $this->Form->input('Search.program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programs, 
		'default' => $default_program_id)); ?></td>
		<td style="width:12%">
		Program Type:</td>
		<td style="width:53%">
		<?php echo $this->Form->input('Search.program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $program_types, 'default' => $default_program_type_id)); ?>
		</td>
	</tr>
	<tr>
		<td style="width:10%">Department:</td>
		<td style="width:25%"><?php echo $this->Form->input('Search.department_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $departments, 'default' => $default_department_id)); ?></td>

		<td style="width:10%">Admission Year:</td>
		<td style="width:25%"><?php echo $this->Form->input('Search.academicyear',array('id'=>'academicyear',
            'label' => false,'type'=>'select','options'=>$acyear_array_data)); ?></td>
	</tr>
	<tr>
		<td colspan="6">
		<?php echo $this->Form->submit(__('List Students'), array('name' => 'listStudentsForOneCardList','class'=>'tiny radius button bg-blue', 'div' => false)); ?>
		</td>
	</tr>
</table>
</div>
<?php
if(!empty($students_for_onecard_list)) {
$count = 1;
?>
<p class="fs14">Below is list of students who have taken electronic card but they are not added to the turnstell and cafe panch devices. Please select the students you want to add.
</p>
<table class="student_list">
		<tr>
			<th><?php echo 'Select/ Unselect All <br/>'.$this->Form->checkbox("Search.SelectAll", 
            array('id' => 'select-all','checked'=>'')); ?> </th>
			<th>S.No</th>
			<th>Student Name</th>
			<th>ID</th>
			<th>Sex</th>
			<th>Department</th>
		</tr>

		<?php 
		$s_count = 1;
		$count=0;
		foreach($students_for_onecard_list as $key => $student) {
		?>
		<tr>
		<td>
<?php 
echo $this->Form->input('Ess.'.$count.'.id', array('type' => 'hidden', 'value' => $student['Student']['id']));
echo $this->Form->input('Ess.'.$count.'.include', array('type' => 'checkbox', 'label' => false,'class'=>'checkbox1'));
?>
		</td>
		
		<td><?php echo $s_count++; ?></td>
		<td><?php echo $this->Html->link(__($student['Student']['full_name']), array('controller' => 'students', 'action' => 'view', $student['Student']['id']), array('target' => '_blank')); ?>
		</td>
		<td><?php echo $student['Student']['studentnumber']; ?>
		</td>
		<td><?php echo (strcasecmp($student['Student']['gender'], 'male') == 0 ? 'M' : 'F'); ?></td>
		
		<td><?php echo $student['Department']['name'] ?></td>
		</tr>
		
		<?php 
		$count++;
		}
		?>
</table>
<?php 
echo $this->Form->submit(__('Push Studens To One Card Database'), array('name' => 'addStudentToOneCardDb','class'=>'tiny radius button bg-blue', 'div' => false));
}else if(isset($this->request->data) && empty($students_for_onecard_list)) {
	echo '<div id="flashMessage" class="info-box info-message"><span></span>The system unable to find list of students who have taken electronic card.</div>';
}
echo $this->Form->end();
?>
</div>
</div> <!-- end of columns 12 -->
</div> <!-- end of row -->
</div> <!-- end of box-body -->
</div><!-- end of box -->