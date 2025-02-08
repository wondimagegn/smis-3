<?php echo $this->Html->script('jquery.observe'); ?>
<?php echo $this->Form->create('CourseDrop');?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<?php if (!isset($hide_search)) { ?>

<table cellpadding="0" cellspacing="0"><tbody>
	
	<tr><td>
	
	
	<?php 
			echo "<table><tr>";
			echo "<td colspan=2>".$this->Form->input('Student.academic_year',array('label'=>'Academic Year','type'=>'select','options'=>$acyear_array_data,'selected'=>isset($defaultacademicyear)?$defaultacademicyear:''))."</td></tr>";
			 echo "<tr><td>".$this->Form->input('Student.semester',array('options'=>array('I'=>'I','II'=>'II',
            'III'=>'III'),'empty'=>'--select semester--'))."</td>";   
			echo '<td>'.$this->Form->input('Student.department_id',array('label'=>'Department','empty'=>'--select departemnt--')).'</td></tr>'; 
			echo '<tr><td>'.$this->Form->input('Student.program_id',array('label'=>'Program','empty'=>'--select program--')).'</td>'; 
			echo '<td>'.$this->Form->input('Student.program_type_id',array('label'=>'Program Type','empty'=>'--select program type--')).'</td>'; 
			echo "</tr></table>";
			?>
	</td></tr>
	<tr><td><?php echo $this->Form->Submit('Search',array('div'=>false,'class'=>'tiny radius button bg-blue','name'=>'continue')); ?> </td>	
</tr>
</tbody>
</table>
<?php } ?>
<?php 
if (isset($publishedCourses) && !empty($publishedCourses)) {
//debug($publishedCourses);
   echo '<div class="smallheading"> List of courses, select courses you want to drop for the list of students
   below.</div>';
   echo "<table id='fieldsForm'><tbody>";
            
            ?>
         
            
            <?php 
            
            echo "<tr><th style='padding:0'> S.No </th>";
             echo "<th style='padding:0'> Select </th>";
            echo "<th style='padding:0'> Course Title </th>";
            echo "<th style='padding:0'> Course Code </th>";
            echo "<th style='padding:0'> Lecture hour </th>";
            echo "<th style='padding:0'> Tutorial hour </th>"; 
            echo "<th style='padding:0'> Credit </th></tr>";
            $count=0;
            
            foreach ($publishedCourses as $pk=>$pv) {
             
                  echo $this->Form->hidden('CourseDrop.'.$count.'.semester',
                 array('value'=>$pv['PublishedCourse']['semester']));
                  echo $this->Form->hidden('CourseDrop.'.$count.'.academic_year',
                 array('value'=>$pv['PublishedCourse']['academic_year']));
                  echo $this->Form->hidden('CourseDrop.'.$count.'.year_level_id',
                 array('value'=>$pv['PublishedCourse']['year_level_id']));
                 
                 //echo "<tr><td>".++$count."</td><td>".$this->Form->checkbox('CourseDrop.drop.' . $pv['Course']['id'])."</td><td>".$pv['Course']['course_title']."</td>";
                 echo "<tr><td>".++$count."</td><td>".$this->Form->input('CourseDrop.drop.' . $pv['Course']['id'],array('type'=>'checkbox','class'=>'course_checkbox','label'=>false,'id'=>'Drop_'.$pv['Course']['id']))."</td><td>".$pv['Course']['course_title']."</td>";
                 
                echo $this->Js->get("#Drop_".$pv["Course"]["id"])->event('change', 
                $this->Js->request(array('action' => 'list_students',$pv["Course"]["id"]), array( 
                'update' => '#element', 'async' => true, 'dataExpression' => true, 'method' => 'post', 
                 'data' => $this->Js->serializeForm(array('isForm' => false, 'inline' => true)) 
                ))); 
                 echo "<td>".$pv['Course']['course_code']."</td>";
                 echo "<td>".$pv['Course']['lecture_hours']."</td>";
                 echo "<td>".$pv['Course']['tutorial_hours']."</td>";
                 echo "<td>".$pv['Course']['credit']."</td></tr>";
                
            }
          //}
         
             //echo $this->Js->event('click',$this->Js->alert('hey you!'));
            echo  "</table>";
            echo "<div id='element'>";
            echo "</div>";
}


 if (isset($list_of_students_registered) && !isset($no_display)) {

  ?>
  <div class="smallheading"> List of students who are registered for the above courses.</div>
  <?php 
  $i = 0;
  $count=0;
  $cc=0;
  foreach ($list_of_students_registered_for_courses as $title=>$list_of_students) { ?>
  <div class="smallheading"><?php echo $title;?></div>
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
	
	//debug($list_of_students_registered);
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
		echo $this->Form->hidden('CourseDrop.'.$cc.'.student_id',array('value'=>$student['Student']['id']));
		echo $this->Form->hidden('CourseDrop.'.$cc.'.course_id',array('value'=>$student['Course']['id']));
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
	echo "<table>";
	echo "<tr><td>".$this->Form->input('minute_number')."</td></tr>";
    echo "<tr><td style='padding-left:300px;'>".$this->Form->input('forced')."</td></tr>";
    echo "</table>";
    
    
    ?>
	<?php echo $this->Form->Submit('Drop Selected Courses',array('div'=>false,'name'=>'dropselecctedcourses')); ?> 
<?php }

echo $this->Form->end();
?>

<?php
 echo $this->Js->writeBuffer();
?>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
