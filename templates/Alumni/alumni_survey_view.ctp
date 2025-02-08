<?php echo $this->Form->create('Alumnus');?>
<script>
$(function() {
	$("#Department").customselect();
}); 	
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
        <div>
<div class="smallheading"><?php echo __('View Baseline Survey Questionnaire');?></div>
<div onclick="toggleViewFullId('ListDepartment')"><?php 
	if (!empty($sections)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListSectionImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Display Filter</span><?php
		}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListSectionImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Hide Filter</span><?php
		}
?></div>
<div id="ListDepartment" style="display:<?php echo (!empty($sections) ? 'none' : 'display'); ?>">
<table cellspacing="0" cellpadding="0" class="fs14">
	<tr>
		<td>Graduation Year</td>
		<td><?php echo $this->Form->input('Search.gradution_academic_year', array('id' => 'GradutionAcademicYear', 
		'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select',
		 'options' => $acyear_array_data, 
		 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
		 
		<td>Department:</td>
		<td><?php //echo $this->Form->input('Search.department_id', array('id' => 'Department', 'class' => 'custom-select', 'label' => false, 'type' => 'select', 'options' => $departments)); ?>
		
		<?php echo $this->Form->input('Search.department_id', array('id' => 'ProgramType', 'class' => 'fs13', 'label' => false, 'type' => 'select', 'options' => $departments, 'default' => $default_department_id)); ?>
		
		</td>
		
	
	</tr>
	<tr>
		<td>Program:</td>
		<td><?php echo $this->Form->input('Search.program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programs)); ?></td>
		<td>Program Type:</td>
		<td><?php echo $this->Form->input('Search.program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programTypes)); ?></td>
	</tr>
    <tr>
		<td>Name:</td>
		<td><?php echo $this->Form->input('Search.name', array('id' => 'name', 'class' => 'fs14', 'label' => false)); ?></td>
		<td>Limit:</td>
		<td><?php echo $this->Form->input('Search.limit', array('id' => 'limit', 'class' => 'fs14', 'label' => false)); ?></td>
		
	</tr>
   
	<tr>
		<td colspan="6">
		<?php echo $this->Form->submit(__('Get Alumni'), array('name' => 'listAlumni','class'=>'tiny radius button bg-blue','div' => false)); ?>
		</td>
	</tr>
</table>
</div>

<?php

if(isset($alumni) && empty($alumni)) {
	echo '<div id="flashMessage" class="info-box info-message"><span></span>There is no alumni student in the selected section</div>';
}
else if(isset($alumni) && !empty($alumni)) {
	?>
	<p class="fs13">Please select alumni/s for whom you want to completed baseline survey questionnaire.</p>
	<strong style="color:red">Red:Not graduated but completed survey</strong> <br/>
	<strong style="color:green">Green:Graduated and completed survey.</strong>
	<table>
		<tr>
			<th style="width:10%"><?php echo $this->Form->input('select_all', 
			array('type' => 'checkbox', 'id' => 'select-all', 'div' => false,
			 'label' => false)); ?>Select All</th>
			<th style="width:25%">Student Name</th>
			<th style="width:25%">Sex</th>
			<th style="width:50%">ID</th>
		</tr>
		<?php
		$st_count = 0;
		foreach($alumni as $key => $student) {
			$st_count++;
			$class = null;
			debug($student);
			if (empty($student['Student']['SenateList'])) {
				$class = ' class="rejected"';
			} else if(!empty($student['Student']['SenateList'])) {
			$class = ' class="accepted"';
			
			}
			?>
			<tr <?php echo $class;?>>
				<td><?php 
					echo $this->Form->input('Alumnus.'.$st_count.'.gp', array('type' => 'checkbox', 'label' => false, 'id' => 'AlumnusSelection'.$st_count,'class'=>'checkbox1'));
					echo $this->Form->input('Alumnus.'.$st_count.'.student_id', array('type' => 'hidden', 'value' => $student['Student']['id']));
				?></td>
				<td><?php echo $student['Alumnus']['full_name']; ?></td>
				<td><?php echo $student['Alumnus']['sex']; ?></td>
				<td><?php echo $student['Student']['studentnumber']; ?></td>
			</tr>
			<?php
		}
		?>
	</table>
	
	<?php echo $this->Form->submit(__('Get  Alumni Questionnaire'), array('name' => 'getAlumniQuestionnaireInExcel', 
	'div' => false,'class'=>'tiny radius button bg-blue'));
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo $this->Form->submit(__('Delete Alumni Questionnaire Not Graduated'), array('name' => 'deleteAlumniQuestionnaireInExcel', 
	'div' => false,'class'=>'tiny radius button bg-blue'));
	 ?>
	
	<?php
	
}
?>
<?php echo $this->Form->end(); ?>
</div>
		</div>
       </div>
     </div>
</div>
