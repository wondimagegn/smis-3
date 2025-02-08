<?php ?>
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
<div class="senateLists form">
<?php echo $this->Form->create('Readmission');?>
<div class="smallheading"><?php echo __('Apply Readmission On Behalf Student '); ?></div>
<div onclick="toggleViewFullId('ListPublishedCourse')"><?php 
	if (!empty($students_for_readmission_list)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span><?php
		}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span><?php
		}
?></div>
<div id="ListPublishedCourse" style="display:<?php echo (!empty($students_for_readmission_list) ? 'none' : 'display'); 
?>">

 <p class="fs16">
                    <strong> Important Note: </strong> 
                    The system will display all students in the selected criteria without restriction
                    and gives you information for your decision making. You are responsible for 
                    wrong readmission application on behalf of the selected students so make
                    sure the readmission appliction for the right student who needs readmission 
                    application.
                    
    </p>

<table cellspacing="0" cellpadding="0" class="fs13">
	<tr>
			<td style="width:12%">Readmission Academic Year:</td>
			<td style="width:20%"><?php echo $this->Form->input('academic_year',
			 array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
			<td style="width:8%"> Readmission Semester:</td>
			<td style="width:25%"><?php echo $this->Form->input('semester', 
			array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => (isset($semester_selected) ? $semester_selected : false))); ?></td>
			
	</tr>
	<tr>
		<td style="width:10%">Program:</td>
		<td style="width:25%"><?php echo $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programs, 'default' => $default_program_id)); ?></td>
		<td style="width:12%">Program Type:</td>
		<td style="width:53%"><?php echo $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $program_types, 'default' => $default_program_type_id)); ?></td>
	</tr>
	<tr>
	
	
		  	<td  style="width:10%">Department:</td>
		<td style="width:25%"><?php echo $this->Form->input('department_id', array('id' => 'ProgramType', 
		'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $departments, 'default' => 
		$default_department_id)); ?></td>
		
		
		
		<td style="width:12%">Student Name:</td>
		<td style="width:53%"><?php echo $this->Form->input('name', array('id' => 'Name', 'class' => 'fs14',
		 'label' => false)); ?></td>

	</tr>
	<tr>
		<td colspan="6">
		<?php echo $this->Form->submit(__('List Students'), array('name' => 'listStudentsForReadmission', 
		'div' => false)); ?>
		</td>
	</tr>
</table>
</div>
<?php
if(!empty($students_for_readmission_list)) {

?>

<style>
table.summery tr td{
padding:2px;
}
table.student_list tr td{
padding:2px;
}
</style>
<?php
$count = 1;
foreach($students_for_readmission_list as $c_id => $students) {
	?>
	<table class='fs13 summery'>
		<tr>
			<td style="width:22%">Department:</td>
			<td style="width:78%; font-weight:bold"><?php 
			    if (!empty($students[0]['Department']['name'])) {
			            echo $students[0]['Department']['name']; 
			    } else {
			         echo "Pre/Department Non Assigned"; 
			    }
			   
			
			?></td>
		</tr>
		<tr>
			<td>Program:</td>
			<td style="font-weight:bold"><?php echo $students[0]['Program']['name']; ?></td>
		</tr>
		<tr>
			<td>Curriculum:</td>
			<td style="font-weight:bold"><?php 
			   
			     if (!empty($students[0]['Curriculum']['name'])) {
			            echo $students[0]['Curriculum']['name'];
			    } else {
			         echo "Pre/Department Non Assigned"; 
			    }
			   
			    
			?></td>
		</tr>	
	</table>
	<table class="student_list">
		<tr>
			<th style="width:4%"></th>
			<th style="width:4%"></th>
			<th style="width:2%">S.No</th>
			<th style="width:27%">Student Name</th>
			<th style="width:9%">ID</th>
			<th style="width:5%">Sex</th>
		
			<th style="width:9%">CGPA</th>
			<th style="width:30%">MCGPA</th>
		</tr>
	<?php
	$s_count = 1;
	foreach($students as $key => $student) {
		if($key == 0)
			continue;
		?>
		<tr style="color:<?php echo (empty($student['criteria']['error']) ? 'green' : 'red'); ?>">
		
				<td style="background-color:white"><?php 
				echo $this->Form->input('Student.'.$count.'.id', array('type' => 'hidden', 'value' => $student['Student']['id']));
				echo $this->Form->input('Student.'.$count.'.include_readmission', array('type' => 'checkbox', 'label' => false));
				?>
				</td>
				<?php if (isset($student['criteria']['error']) && !empty($student['criteria']['error'])) { ?>
			   <td style="background-color:white" onclick="toggleView(this)" 
				id="<?php echo $count; ?>"><?php echo $this->Html->image('plus2.gif', 
				array('id' => 'i'.$count, 'div' => false, 'align' => 'left')); ?>?</td>
				
				<?php } else { ?>
				  <td style="background-color:white">&nbsp;</td>
				<?php } ?>
				
			<td style="background-color:white"><?php echo $s_count++; ?></td>
			<td style="background-color:white"><?php echo $this->Html->link(__($student['Student']['full_name']), array('controller' => 'students', 'action' => 'view', $student['Student']['id']), 
			array('target' => '_blank', 'style' =>  'font-weight:normal; color:'.(empty($student['criteria']['error']) 
			? 'green' : 'red'))); ?></td>
			<td style="background-color:white"><?php echo $student['Student']['studentnumber']; ?></td>
			<td style="background-color:white"><?php echo (strcasecmp($student['Student']['gender'], 'male') == 0 ? 'M' : 'F'); ?></td>
			
			<td style="background-color:white"><?php echo $student['cgpa']; ?></td>
			<td style="background-color:white"><?php echo $student['mcgpa']; ?></td>
		</tr>
			<?php
		if(isset($student['criteria']['error']) && !empty($student['criteria']['error'])) {
		?>
		<tr id="c<?php echo $count; ?>" style="display:none">
			<td colspan="8" style="background-color:#f0f0f0">
				<?php 
				    echo $student['criteria']['error'];
				?>
			</td>
		</tr>
		<?php
		}
	
	
		$count++;
	}
	?>
	</table>
	<?php
	}//End of each curriculum students
echo $this->Form->submit(__('Add Student to Readmission List'), 
array('name' => 'addStudentToReadmissionList', 'div' => false));
}
else if(isset($this->request->data) && empty($students_for_readmission_list)) {
	echo '<div id="flashMessage" class="info-box info-message"><span></span>The system unable to find 
	list of students who need readmission application for selected criteria.</div>';
}
?>
<?php echo $this->Form->end();?>
</div>
