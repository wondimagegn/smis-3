<?php echo $this->Form->create('CourseSubstitutionRequest');?>
<script type='text/javascript'>

 function updateSubCurriculum(id) {
            //serialize form data
            var formData = $("#department_id_"+id).val();
			$("#curriculum_id_"+id).empty();
			$("#curriculum_id_"+id).append('<option style="width:100px">loading...</option>');
			$("#curriculum_id_"+id).attr('disabled', true);
			
			$("#course_id_"+id).attr('disabled', true);
			//get form action
            var formUrl = '/curriculums/get_curriculum_combo/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
						$("#curriculum_id_"+id).attr('disabled', false);
						$("#curriculum_id_"+id).empty();
						$("#curriculum_id_"+id).append(data);
							//Items list
							var subCat = $("#curriculum_id_"+id).val();
							$("#course_id_"+id).empty();
							//get form action
							var formUrl ='/curriculums/get_courses/'+subCat;
							$.ajax({
								type: 'get',
								url: formUrl,
								data: subCat,
								success: function(data,textStatus,xhr){
										$("#course_id_"+id).attr('disabled', false);
										$("#course_id_"+id).empty();
										$("#course_id_"+id).append('<option value=""></option>');
										$("#course_id_"+id).append(data);
								},
								error: function(xhr,textStatus,error){
										alert(textStatus);
								}
							});
							//End of items list
				},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
			
			return false;
}
//update course combo 
function updateCourse(id) {
            //serialize form data
            var subCat = $("#curriculum_id_"+id).val();
			$("#course_id_"+id).empty();
			$("#course_id_"+id).append('<option style="width:100px">loading...</option>');
			$("#course_id_"+id).attr('disabled', true);
			//get form action
            var formUrl = '/curriculums/get_courses/'+subCat;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: subCat,
                success: function(data,textStatus,xhr){
						$("#course_id_"+id).attr('disabled', false);
						$("#course_id_"+id).empty();
						$("#course_id_"+id).append('<option value=""></option>');
						$("#course_id_"+id).append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
			
            return false;
}
</script>
<div class="courseSubstitutionRequests form">

<?php 
echo $this->element('student_basic');
/*
 if (isset($student_section_exam_status) && !empty($student_section_exam_status)) {   
?>
    <table><tr><td>
     <table>
            <tr><td class="font">Name:&nbsp;&nbsp;&nbsp;
            <?php 
              if (isset($student_section_exam_status['StudentBasicInfo']['full_name'])) {
                echo $student_section_exam_status['StudentBasicInfo']['full_name'];
              }
            ?>
            </td></tr>
            <tr><td class="font">Student Number:&nbsp;&nbsp;&nbsp;
            <?php 
                echo $student_section_exam_status['StudentBasicInfo']['studentnumber']; 
            ?>
            </td></tr>
         
            <tr><td class="font">Year Level:&nbsp;&nbsp;&nbsp;
            <?php 
                echo $student_section_exam_status['Section']['YearLevel']['name'];
            
            ?>
            </td></tr>
            <tr><td class="font">Section:&nbsp;&nbsp;&nbsp;
            <?php 
            
            echo $student_section_exam_status['Section']['name'];
            
            ?>
            </td></tr>
           
       </table>
       </td><td>
        <?php 
         if (!empty($student_section_exam_status['StudentExamStatus'])) {
            echo "<table>";
           
            ?>
           <tr><td class="font">Semester:&nbsp;&nbsp;&nbsp;<?php echo $student_section_exam_status['StudentExamStatus']['semester']?></td></tr>
            <tr><td class="font">Academic Year:&nbsp;&nbsp;&nbsp;<?php echo $student_section_exam_status['StudentExamStatus']['academic_year'];?></td></tr>
         
             <tr><td class="font">SGPA:&nbsp;&nbsp;&nbsp;<?php echo $student_section_exam_status['StudentExamStatus']['sgpa'];?></td></tr>
             <?php 
                if (!empty($student_section_exam_status['StudentExamStatus']['sgpa'])) {
                ?>
                <tr><td class="font">CGPA:&nbsp;&nbsp;&nbsp;<?php echo $student_section_exam_status['StudentExamStatus']['cgpa'];?></td></tr>
                <?php 
                
                }
                if (!empty($student_section_exam_status['StudentExamStatus']['AcademicStatus'])) {
                     echo '<tr><td class="font">Academic Status:&nbsp;&nbsp;&nbsp;'.$student_section_exam_status['StudentExamStatus']['AcademicStatus']['name'].'</td></tr>';
                }
             ?>
          
            <?php 

        echo "</table>";
    }
   echo  '</td></tr>';
   echo  '</table>';
   */
?>
       
 <table>
	        <?php 
	            if (!empty($previous_exemption_accepted)) {
	                echo "<tr><td class='smallheading' colspan=3> Previous course exemption request by this student and accepted by the department and confirmed by registrar.</td</tr>";
	                $count=0;
	                foreach ($previous_exemption_accepted as $psk=>$pvv) {
	                  echo "<tr><td><table>";
	                  echo "<tr><th>Course Title</th><th>Course Code</th><th>Credit</th></tr>";
	                  echo "<tr><td>".$pvv['Course']['course_title']."</td><td>".$pvv['Course']['course_code']."</td><td>".$pvv['Course']['course_code']."</td></tr>";
	                   
	                  echo "</table></td><td class='smallheading' style='vertical-align:middle; align:center'>Exempted by => </td>";
	                  echo "<td>";
	                  echo "<table>";
echo "<tr><th>Course Title</th><th>Course Code</th><th>Credit</th></tr>";
echo "<tr><td>".$pvv['CourseExemption']['taken_course_title']."</td><td>".$pvv['CourseExemption']['taken_course_code']."</td><td>".$pvv['CourseExemption']['course_taken_credit']."</td></tr>";
echo "</table>";
	                  echo "</td></tr>";
	                }
	                
	            }
	        ?>
</table>


      
 <?php //} ?> 	
		<div class="smallheading"><?php echo __('Request Course Substitution '); ?></div>
	<?php
		
		echo $this->Form->hidden('EquivalentCourse.student_id',array('value'=>$student_section_exam_status['StudentBasicInfo']['id']));
			 echo "<table>";
			 ?> 
	
	<?php
	?>
	  <tr><th width="30%">Your Department Course</th><th style="align:left"> Other Department Course</th></tr>
	  <tr><td>
	  <table>
	  <?php 
	 
	  
	   echo '<tr><td>Curriculum Attached:'.$curriculums[$student_section_exam_status['StudentBasicInfo']['curriculum_id']].'</td></tr>';
	     echo $this->Form->hidden('curriculum_id',array('value'=>$student_section_exam_status['StudentBasicInfo']['curriculum_id']));
	     echo '<tr><td>'.$this->Form->input('course_for_substitued_id',
	     array('id'=>'course_id_1','style'=>'width:150px','empty'=>'--select course--','label'=>'Course To be equivalent')).'</td></tr>';
	  ?> 
	  </table> </td><td><?php 
	  echo $this->Form->input('department_id',array('onchange'=>'updateSubCurriculum(2)',
	  'empty'=>'--select department--','id'=>"department_id_2",'style'=>'width:250px')); ?>
	  <table>
	  <tr><td><?php 
       echo $this->Form->input('CourseSubstitutionRequest.other_curriculum_id',array('id'=>'curriculum_id_2',
	  'onchange'=>'updateCourse(2)','options'=>$otherCurriculums,'type'=>'select',
	  'style'=>'width:250px','empty'=>'--select curriculum--')); 
	  /*
	  if (isset($this->request->data['curriculum'])) {
	    echo $this->Form->input('other_curriculum_id',array('id'=>'curriculum_id_2',
	  'onchange'=>'updateCourse(2)','options'=>$otherCurriculums,
	  'style'=>'width:250px')); 
	  
	  } else {
	    echo $this->Form->input('other_curriculum_id',array('id'=>'curriculum_id_2',
	  'onchange'=>'updateCourse(2)','empty'=>'--select curriculum--')); 
	  
	  }
	  
	  */
	  
	  
	  ?>
	  
	  
	  </td></tr>
	   <tr><td>
	   <?php 
	   	    echo $this->Form->input('course_be_substitued_id',array('id'=>'course_id_2','empty'=>'--select equivalent course--','style'=>'width:250px'));
	   	    /*
	   	  if (isset($this->request->data['courseBeSubstitueds'])) {
	        echo $this->Form->input('course_be_substitued_id',array('id'=>'course_id_2','style'=>'width:250px'));
	   
	      } else {
	        echo $this->Form->input('course_be_substitued_id',array('id'=>'course_id_2',
	      'empty'=>' '));
	   
	      }
	      */
	      
	    ?>
	    
	    
	    </td></tr>
	  </table>
	   </td></tr>
	  <!-- <tr><td>&nbsp;&nbsp;&nbsp;</td><td id="kk" style="align:left"></td></tr> -->
	  <?php 
	   
	echo "</table>";
	?>
	
<?php echo $this->Form->end(__('Submit'));?>

</div>
