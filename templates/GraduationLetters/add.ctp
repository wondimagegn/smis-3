<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="graduationLetters form">
<?php echo $this->Form->create('GraduationLetter');?>
<div class="smallheading"><?php __('Add Graduation Letter Template'); ?></div>
<table>
	<tr>
		<td>Type of Letter:</td>
		<td><?php echo $this->Form->input('type', array('label' => false, 'type' => 'select', 'options' => array('Language Proficiency' => 'Language Proficiency', 'To Whom It May Concern' => 'To Whom It May Concern'))); ?></td>
	</tr>
	<tr>
		<td>Program:</td>
		<td><?php echo $this->Form->input('program_id', array('label' => false)); ?></td>
	</tr>
	<tr>
		<td>Program Type:</td>
		<td><?php echo $this->Form->input('program_type_id', array('label' => false)); ?></td>
	</tr>

        <tr>
		<td>Department:</td>
		<td colspan="3"><?php echo $this->Form->input('department', array('id' => 'Department', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $departments, 'default' => $default_department_id)); ?></td>
	</tr>


	<tr>
		<td>Title:</td>
		<td><?php echo $this->Form->input('title', array('label' => false)); ?></td>
	</tr>
	<tr>
		<td>Title Font Size:</td>
		<td><?php echo $this->Form->input('title_font_size', array('label' => false, 'type' => 'select', 'options' => array(12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16, 17=> 17, 18 => 18, 19 => 19, 20 => 20, 21 => 21, 22 => 22, 23 => 23, 24 => 24, 25 => 25))); ?></td>
	</tr>
	<tr>
		<td>Letter</td>
		<td><?php echo $this->Form->input('content', array('label' => false, 'cols' => 90, 'rows' => 10)); ?></td>
	</tr>
	<tr>
		<td>Content Key Words</td>
		<td>
		STUDENT_NAME => Name of the student<br />
		STUDENT_DEPARTMENT => Student Department</br />
		DEGREE_NOMENCLATURE => Degree Nomenclature<br />
		GRADUATION_DATE => Graduation Date<br />
		STUDENT_CGPA => Student CGPA<br />
		STUDENT_MCGPA => Student Major CGPA
		</td>
	</tr>
	<tr>
		<td>Content Font Size:</td>
		<td><?php echo $this->Form->input('content_font_size', array('label' => false, 'type' => 'select', 'options' => array(12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16, 17=> 17, 18 => 18, 19 => 19))); ?></td>
	</tr>
	<tr>
		<td>Academic Year</td>
		<td><?php 
			echo $this->Form->input('academic_year', array('type' => 'select', 'options' => $acs, 'label' => false, 'div' => false, 'style' => 'width:100px', 'class' => 'fs14'));
			//echo $this->Form->year('academic_year', Configure::read('Calendar.universityEstablishement'), date('Y')+1, date('Y'), array('empty' => false, 'label' => false, 'div' => false, 'style' => 'width:100px', 'class' => 'fs14')); ?></td>
	</tr>
	<tr>
		<td>Applicable for Current Student</td>
		<td><?php echo $this->Form->input('applicable_for_current_student', array('label' => false)); ?></td>
	</tr>
</table>
<?php echo $this->Form->end(array('label'=>__('Submit', true),'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
