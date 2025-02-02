<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="courseDrops form">
<?php echo $this->Form->create('CourseDrop');?>

<?php if (!isset($hide_search)) { ?>
<table cellpadding="0" cellspacing="0"><tbody>
	
	<tr><td>
	
	
	<?php 
			echo $this->Form->input('Student.studentnumber',array('label'=>'Student Number')); 
			echo $this->Form->input('Student.department_id',array('label'=>'Department','empty'=>'--select departemnt--')); 
			
			?>
	</td></tr>
	<tr><td><?php echo $this->Form->Submit('Search',array('div'=>false,'class'=>'tiny radius button bg-blue','name'=>'continue')); ?> </td>	
</tr>
</tbody>
</table>
<?php } ?>

<?php
 if (isset($student_section) && !empty($student_section)) {
   
    ?>
    <table>
    <tr><td><table><tr><td class="font">Name:&nbsp;&nbsp;&nbsp;<?php echo $student_section['Student']['full_name']?></td></tr>
    <tr><td class="font">Student Number:&nbsp;&nbsp;&nbsp;<?php echo $student_section['Student']['studentnumber']?></td></tr>
   <tr><td class="font">Year Level:&nbsp;&nbsp;&nbsp;<?php echo $year_level_id;?></td></tr>
   </table></td><td>
   <?php 
      // echo $this->Form->hidden('CourseRegistration.student_id',array('value'=>$student_section['Student']['id']));
       if (!empty($student_section['StudentExamStatus'])) {
            echo "<table>";
            foreach ($student_section['StudentExamStatus'] as $k=>$v) {
            ?>
           <tr><td class="font">Semester:&nbsp;&nbsp;&nbsp;<?php echo $v['semester']?></td></tr>
            <tr><td class="font">Academic Year:&nbsp;&nbsp;&nbsp;<?php echo $v['academic_year']?></td></tr>
            <tr><td class="font">Year Level:&nbsp;&nbsp;&nbsp;<?php echo $year_level_id;?></td></tr>
             <tr><td class="font">SGPA:&nbsp;&nbsp;&nbsp;<?php echo $v['sgpa']?></td></tr>
             <?php 
                if (!empty($v['sgpa'])) {
                ?>
                <tr><td class="font">CGPA:&nbsp;&nbsp;&nbsp;<?php echo $v['cgpa']?></td></tr>
                <?php 
                
                }
                if (!empty($v['AcademicStatus'])) {
                     echo '<tr><td class="font">Academic Status:&nbsp;&nbsp;&nbsp'.$v['AcademicStatus']['name'].'</td></tr>';
                }
             ?>
          
            <?php 
        }
        echo "</table>";
    }
   ?>
   </td></tr>
    </table>
    
    <?php 
     //debug($student_section['CourseRegistration']);
       if (isset($coursesDrop) && !empty($coursesDrop)) {
           //debug($published_courses);
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
           
            foreach ($coursesDrop as $pk=>$pv) {
                echo $this->Form->hidden('CourseDrop.'.$count.'.course_registration_id',array('value'=>$pv['CourseRegistration']['id'])); 
               // echo $this->Form->hidden('CourseRegistration.'.$count.'.course_id',array('value'=>$pv['course_id']));
                 echo $this->Form->hidden('CourseDrop.'.$count.'.academic_year',array('value'=>$pv['CourseRegistration']['academic_year']));
                // echo $this->Form->hidden('CourseRegistration.'.$count.'.section_id',array('value'=>$pv['section_id']));
                 echo $this->Form->hidden('CourseDrop.'.$count.'.semester',
                 array('value'=>$pv['CourseRegistration']['semester']));
                 echo $this->Form->hidden('CourseDrop.'.$count.'.student_id',array('value'=>$pv['CourseRegistration']['student_id']));
                  echo $this->Form->hidden('CourseDrop.'.$count.'.year_level_id',array('value'=>$pv['CourseRegistration']['year_level_id']));
                // echo "<td>".$this->Form->checkbox('CourseRegistration.drop.' . $pv['id'])."</td>"; 
                 if(in_array($pv['CourseRegistration']['id'],$already_dropped)){
                 echo "<tr class='linethough'><td>".++$count."</td><td>".$this->Form->checkbox('CourseRegistration.drop.' . $pv['CourseRegistration']['id'],array('disabled'=>in_array($pv['CourseRegistration']['id'],$already_dropped)?true:false))."</td><td>".$pv['PublishedCourse']['Course']['course_title']."</td>";
                 } else {
                   echo "<tr><td>".++$count."</td><td>".$this->Form->checkbox('CourseRegistration.drop.' . $pv['CourseRegistration']['id'])."</td><td>".$pv['PublishedCourse']['Course']['course_title']."</td>";
                 }
                 echo "<td>".$pv['PublishedCourse']['Course']['course_code']."</td>";
                 echo "<td>".$pv['PublishedCourse']['Course']['lecture_hours']."</td>";
                 echo "<td>".$pv['PublishedCourse']['Course']['tutorial_hours']."</td>";
                 echo "<td>".$pv['PublishedCourse']['Course']['credit']."</td></tr>";
                
            }
          //}
            if($count != count($already_dropped)) {
            echo "<tr><td colspan=7>".$this->Form->Submit('Drop Selected',array('div'=>false,'name'=>'drop'))."</td></tr>";
            }
            if (isset($already_dropped) && !empty($already_dropped)) {
                echo "<tr><td colspan=7 class='smallheading'>Note: The uderline courses has already dropped.</td></tr>";
            }
            echo  "</table>";
            
  }

 }

 //debug($student_lists);

 if (isset($student_lists) && !isset($no_display) && !empty($student_lists)) {
 // debug($students);
 
  ?>
  <div class="smallheading"> List of students who have registered for <?php echo 
  $current_academic_year ?> academic year  of semester <?php echo $semester ?> </div>
  <table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo ('No.');?></th>
			<th><?php echo ('Student Number');?></th>
			
			<th><?php echo ('Full Name');?></th>
			<th><?php echo ('Department');?></th>
			<th><?php echo ('Program');?></th>
			<th><?php echo ('Program Type');?></th>
		
			
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$count=1;
	
	foreach ($student_lists as $student):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $count++; ?>&nbsp;</td>
		<td><?php echo $student['Student']['studentnumber']; ?>&nbsp;</td>
		<td><?php echo $student['Student']['full_name']; ?>&nbsp;</td>
		
		<td>
			<?php echo $this->Html->link($student['Department']['name'], array('action' => 'view', $student['Department']['id'])); ?>
		</td> 
		<td>
			<?php echo $this->Html->link($student['Program']['name'], array('action' => 'view', $student['Program']['id'])); ?>
		</td> 
		
		<td>
			<?php echo $this->Html->link($student['ProgramType']['name'], array('controller' => 'program_types', 'action' => 'view', $student['ProgramType']['id'])); ?>
		</td>
	
		<td class="actions">
			<?php echo $this->Html->link(__('Drop Course'), array('action' => 'add', $student['Student']['id'])); ?>
			
		</td>
	</tr>
<?php endforeach; ?>
	</table>
<?php 
}

echo $this->Form->end();
?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
