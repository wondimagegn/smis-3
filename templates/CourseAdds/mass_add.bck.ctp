<div class="courseDrops form">

<?php echo $this->Form->create('CourseAdd');?>

<?php if (!isset($hide_search)) { ?>

<table cellpadding="0" cellspacing="0"><tbody>
	<tr><td class="smallheading"> 
	Course add for mass students when department publish courses as mass add.</td></tr>
	<tr><td>
	
	
	<?php 
			echo "<table><tr>";
			echo "<td>".$this->Form->input('Student.academic_year',array('label'=>'Academic Year','type'=>'select','options'=>$acyear_array_data,'selected'=>isset($defaultacademicyear)?$defaultacademicyear:''))."</td><td>".$this->Form->input('Student.year_level_id',array('label'=>'Year Level ','empty'=>'--select year level--'))."</td></tr>";
			 echo "<tr><td>".$this->Form->input('Student.semester',array('options'=>array('I'=>'I','II'=>'II',
            'III'=>'III'),'empty'=>'--select semester--'))."</td>";   
            if(!empty($departments)) {
			echo '<td>'.$this->Form->input('Student.department_id',array('label'=>'Department','empty'=>'--select department--')).'</td></tr>'; 
			} else if (!empty($colleges)) {
			  echo '<td>'.$this->Form->input('Student.college_id',array('label'=>'College','empty'=>'--select college--')).'</td></tr>';
			
			}
			
			
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
if (isset($section_organized_students) && !empty($section_organized_students)) {
echo '<div class="largeheading"> List of mass add courses published by the department to be added by list of students below.</div>';
   if(!empty($published_courses)) {
        echo "<table>";
        echo "<tr><th style='padding:0'> S.No </th>";
        echo "<th style='padding:0'> Course Title </th>";
        echo "<th style='padding:0'> Course Code </th>";
        echo "<th style='padding:0'> Lecture hour </th>";
        echo "<th style='padding:0'> Tutorial hour </th>"; 
        echo "<th style='padding:0'> Credit </th>";
        echo "<th style='padding:0'> Section </th></tr>";
        $pcount=0;
        foreach ($published_courses as $pk=>$pv) {
                  echo "<tr><td>".++$pcount."</td>";
                  echo "<td>".$pv['Course']['course_title']."</td>";
                  echo "<td>".$pv['Course']['course_code']."</td>";
                  echo "<td>".$pv['Course']['lecture_hours']."</td>";
                  echo "<td>".$pv['Course']['tutorial_hours']."</td>";
                  echo "<td>".$pv['Course']['credit']."</td>";
                  echo "<td>".$sections[$pv['PublishedCourse']['section_id']]."</td></tr>";
        }
        echo "</table>";
   }
   echo "<table>";
    //list of students
          if (!empty($section_organized_students)) {
           $count_form=0;
                    
                     foreach($section_organized_students as $section_id=>$section_organized_student){
                         echo "<tr><td colspan=3 class='smallheading'>Program:".$program."</td></tr>";
                         echo "<tr><td colspan=3 class='smallheading'>Program Type:".$programType."</td></tr>";
                         echo "<tr><td colspan=3 class='smallheading'>Section:".$sections[$section_id]."</td></tr>";
                         echo "<tr><td colspan=3>";
                                echo "<table>";
                                //echo "<tr><td colspan="2"> List of students in this section.</td></tr>";
                               echo "<tr><th>S.N<u>o</u></th><th> Full Name</th> <th> Student Number</th></tr>";
                                $stu_count=1;
                                foreach ($section_organized_student as $ssk=>$ssv) {
                                     echo "<tr><td>".$stu_count++."</td><td>".$ssv['full_name']."</td><td>".$ssv['studentnumber']."</td></tr>";
                                       
                                  foreach ($published_courses as $pck=>$pcv) {
                                      if($pcv['PublishedCourse']['section_id']==$section_id) {
                                          echo $this->Form->hidden('CourseAdd.'.$count_form.'.semester',
                                         array('value'=>$pcv['PublishedCourse']['semester']));
                                           echo $this->Form->hidden('CourseAdd.'.$count_form.'.published_course_id',
                                         array('value'=>$pcv['PublishedCourse']['id']));
                                        
                                           echo $this->Form->hidden(
                                           'CourseAdd.'.$count_form.'.student_id',
                         array('value'=>$ssv['id']));
                          /* echo $this->Form->hidden('CourseAdd.'.$count_form.'.section_id',
                         array('value'=>$sv['Section']['id']));*/
                                          echo $this->Form->hidden('CourseAdd.'.$count_form.'.academic_year',
                                         array('value'=>$pcv['PublishedCourse']['academic_year']));
                                          echo $this->Form->hidden('CourseAdd.'.$count_form.'.year_level_id',
                                         array('value'=>$pcv['PublishedCourse']['year_level_id']));
                                         $count_form++;
                                      }
                                   }
                                }
                                echo "</table>";
                         echo "</td></tr>";
                    }
               }
   echo "</table>";           
        echo '<tr><td>'.$this->Form->Submit('Mass Add',
        array('div'=>false,'name'=>'massadd')).'</td></tr>'; 
        echo  "</table>";
        echo "<div id='element'>";
        echo "</div>";
}

echo $this->Form->end();
?>
</div>
