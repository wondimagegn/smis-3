<?php 

if (!empty($registred_students)) {
       
        $count=0;
        if (!empty($registred_students['register']) && count($registred_students['register'])>0) {
                echo "<div class='smallheading'>";
                echo "List of students registred for the selected courses.";
                echo "</div>";
                echo "<table>";
                
                echo "<tr><th>S.No</th><th>Full Name</th><th> Student Number</th></tr>";
                foreach ($registred_students['register'] as $index=>$detail) {
                    echo $this->Form->hidden('CourseRegistration.'.$detail['CourseRegistration']['id'].'.id',
                    array('value'=>$detail['CourseRegistration']['id']));
                    echo "<tr>";
                    echo "<td>".++$count."</td>";
                    echo "<td>".$detail['Student']['first_name'].''.$detail['Student']['middle_name'].''.$detail['Student']['last_name']."</td><td>".$detail['Student']['studentnumber']."<td>";
                    echo "</tr>";
                  //  $count++;
                }
                echo "</table>";
        }
} else {
   
}
?>
