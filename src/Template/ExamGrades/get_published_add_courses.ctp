<?php 
if (!empty($otherAdds)) {
   //debug($otherAdds); 
    echo "<div class='smallheading'> Select courses you want to add.</div>";
            echo "<table id='fieldsForm'><tbody>";
            echo "<tr><th style='padding:0'> S.No </th>";
             echo "<th style='padding:0'> Select </th>";
            echo "<th style='padding:0'> Course Title </th>";
            echo "<th style='padding:0'> Course Code </th>";
           
            echo "<th style='padding:0'> ECTS Credit </th>";
		      echo "<th style='padding:0'> Grade </th></tr>";
            $count=1;
            $button_visible=0;
            foreach ($otherAdds as $pk=>$pv) {
                 if ($pv['already_added'] == 0) {
                     echo "<tr><td>".$count."</td><td>".$this->Form->input('CourseAdd.'.$count.'.gp', array('type' => 'checkbox','class'=>'checkbox1', 'label' => false, 'id' => 'StudentSelection'.$count))."</td><td>".$pv['Course']['course_title']."</td>";
                   $button_visible++;
                 } else {
                       if (isset($pv['prerequiste_failed']) && $pv['prerequiste_failed']==1 ) {
                          echo "<tr style='color:red'><td>".++$count."</td><td></td><td>".$pv['Course']['course_title']."</td>";
                       
                 		} else {
                 		   echo "<tr><td>".$count."</td><td>***</td><td>".$pv['Course']['course_title']."</td>";
                 
                 		}
                  
                 }
                
                 
                 echo "<td>".$pv['Course']['course_code']."</td>";
                
                 echo "<td>".$pv['Course']['credit']."</td><td>";

		        if ($pv['already_added'] == 0) {
							
						$gradeList = array();
						if(isset($pv['Course']['GradeType']['Grade']) && !empty($pv['Course']['GradeType']['Grade'])) {
						   foreach($pv['Course']['GradeType']['Grade'] as $key=>$value) {
							$gradeList[$value['grade']]=$value['grade'];
						   } 
						}
 
                        echo $this->Form->input('CourseAdd.'.$count.'.student_id', 
array('type' => 'hidden', 'value' =>$addParamaterss['student_id']));
					    echo $this->Form->input('CourseAdd.'.$count.'.semester', 
array('type' => 'hidden', 'value' =>$addParamaterss['semester']));
						echo $this->Form->input('CourseAdd.'.$count.'.academic_year', 
array('type' => 'hidden', 'value' =>$pv['PublishedCourse']['academic_year']));
						echo $this->Form->input('CourseAdd.'.$count.'.section_id', 
array('type' => 'hidden', 'value' =>$pv['PublishedCourse']['section_id']));
					 echo $this->Form->input('CourseAdd.'.$count.'.published_course_id', 
array('type' => 'hidden', 'value' =>$pv['PublishedCourse']['id']));
echo $this->Form->input('CourseAdd.'.$count.'.year_level_id', 
array('type' => 'hidden', 'value' =>$pv['PublishedCourse']['year_level_id']));
					  echo $this->Form->hidden('CourseAdd.'.$count.'.grade_scale_id', array('value'=>$pv['PublishedCourse']['grade_scale_id']));
					  echo $this->Form->hidden('CourseAdd.'.$count.'.department_approval', 
array('value'=>1));
					  echo $this->Form->hidden('CourseAdd.'.$count.'.registrar_confirmation', 
array('value'=>1));
						
						echo $this->Form->input('CourseAdd.'.$count.'.grade', array('label' => false,'type'=>'select','options'=>$gradeList,'empty'=>'select'));
						
				 }
                 $count++;
				echo '</td></tr>';
            }

			

            echo '<tr><td colspan=6>
             	Note: <ol>
                    <li>*** Courses you have already registred or taken, and got pass grade,
                    not allowed to add it.
                    
                    </li>
                     <li> Red marked courses failed to fullfill prerequiste.
                    
                    </li>
               
                </ol>
                </tr>';
            echo  "</table>";
           if ($button_visible>0) {
                //echo $this->Form->end('Add Selected');
				
echo $this->Form->submit('Add Grade',array('id'=>'add_button_disable','class'=>'tiny radius button bg-blue','div'=>false,'name'=>'addCoursesGrade'));
           }
           
 } else {
		echo 'There is no published course by the selected criteria.';
}
 ?>
