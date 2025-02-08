<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="courseDrops form">
<?php echo $this->Html->script('jquery.observe'); ?>
<?php echo $this->Form->create('CourseDrop');?>

<?php if (!isset($hide_search)) { ?>

<table cellpadding="0" cellspacing="0"><tbody>
	<tr><td class="smallheading"> Course drop for mass students when department publish courses as mass drop.</td></tr>
	<tr><td>
	
	
	<?php 
			//echo $this->Form->input('Student.course_id',array('label'=>'Student Number')); 
			/*$this->Form->input('Course.academicyear',array(
            'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--"))
            */
			echo "<table><tr>";
			echo "<td>".$this->Form->input('Student.academic_year',
			array('label'=>'Academic Year','type'=>'select','options'=>$acyear_array_data,
			'selected'=>isset($this->request->data['Student']['academic_year']) ? $this->request->data['Student']['academic_year'] :
			 (isset($defaultacademicyear)? $defaultacademicyear:''))).
			"</td><td>".$this->Form->input('Student.year_level_id',array('label'=>
			'Year Level','empty'=>"--Select Year Level--"))."</td></tr>";
			// echo '<tr><td>'. $this->Form->input('Student.year_level_id',array('label'=>'Year Level','empty'=>"--Select Year Level--")).'</td>'; 
			 echo "<tr><td>".$this->Form->input('Student.semester',array('options'=>array('I'=>'I','II'=>'II',
            'III'=>'III'),'empty'=>'--select semester--'))."</td>";   
			echo '<td>'.$this->Form->input('Student.department_id',array('label'=>'Department','empty'=>'--select departemnt--')).'</td></tr>'; 
			echo '<tr><td>'.$this->Form->input('Student.program_id',array('label'=>'Program','empty'=>'--select program--')).'</td>'; 
			echo '<td>'.$this->Form->input('Student.program_type_id',array('label'=>'Program Type','empty'=>'--select program type--')).'</td>'; 
			echo "</tr></table>";
			?>
	</td></tr>
	<tr><td><?php echo $this->Form->Submit('Search',array('div'=>false,'name'=>'continue')); ?> </td>	
</tr>
</tbody>
</table>
<?php } ?>
<?php


 if (isset($list_of_students_registered_organized_by_section) && !isset($no_display)) {

  ?>
  <div class="smallheading"> List of students who are registered for courses that has been published by the department as drop.</div>
  <?php 
  $i = 0;
  $count=0;
  $cc=0;
foreach($list_of_students_registered_organized_by_section as $section_id=>$list_of_students_registered_for_courses) {
?>
   <div class="smallheading"><?php echo "Section:".$sections[$section_id]; ?></div>
   <?php 
  foreach ($list_of_students_registered_for_courses as $title=>$list_of_students) { ?>
  <div class="smallheading"><?php echo 'Course:'.$title;?></div>
 
  <table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo ('No.');?></th>
			<th><?php echo ('Student Number');?></th>
			
			<th><?php echo ('Full Name');?></th>
			<th><?php echo ('Department');?></th>
			<th><?php echo ('Program');?></th>
			<th><?php echo ('Program Type');?></th>
	</tr>
	<?php
	
	//debug($list_of_students);
	foreach ($list_of_students as $student):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo ++$count; ?>&nbsp;</td>
		<?php 
		//debug($student['Student']['id']);
		echo $this->Form->hidden('CourseDrop.'.$cc.'.student_id',
		array('value'=>$student['Student']['id']));
		echo $this->Form->hidden('CourseDrop.'.$cc.'.course_registration_id',
		array('value'=>$student['CourseRegistration']['id']));
		echo $this->Form->hidden('CourseDrop.'.$cc.'.semester',
                 array('value'=>$student['CourseRegistration']['semester']));
                  echo $this->Form->hidden('CourseDrop.'.$cc.'.academic_year',
                 array('value'=>$student['CourseRegistration']['academic_year']));
                  echo $this->Form->hidden('CourseDrop.'.$cc.'.year_level_id',
                 array('value'=>$student['CourseRegistration']['year_level_id']));
       $cc++;
            ?>
		<td><?php echo $student['Student']['studentnumber']; ?>&nbsp;</td>
		<td><?php echo $student['Student']['full_name']; ?>&nbsp;</td>
		
		<td>
			<?php echo $this->Html->link($student['Student']['Department']['name'], array('action' => 'view', $student['Student']['Department']['id'])); ?>
		</td> 
		<td>
			<?php echo $this->Html->link($student['Student']['Program']['name'], array('action' => 'view', $student['Student']['Program']['id'])); ?>
		</td> 
		
		<td>
			<?php echo $this->Html->link($student['Student']['ProgramType']['name'], array('controller' => 'program_types', 'action' => 'view', $student['Student']['ProgramType']['id'])); ?>
		</td>
	
	</tr>
<?php endforeach; ?>

	</table>
	<?php 
	
	}
	
   }
echo "<table>";
echo "<tr><td>".$this->Form->input('CourseDrop.minute_number')."</td></tr>";
//echo "<tr><td style='padding-left:300px;'>".$this->Form->input('CourseDrop.0.forced')."</td></tr>";
echo "</table>";
    
    
    ?>
	<?php echo $this->Form->Submit('Mass drop',array('div'=>false,'class'=>'tiny radius button bg-blue','name'=>'massdrop')); ?> 
<?php 
}
echo $this->Form->end();
?>
</div>
<?php
 echo $this->Js->writeBuffer();
?>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
