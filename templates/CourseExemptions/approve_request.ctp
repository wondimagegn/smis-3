<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="courseExemptions form">
<?php echo $this->Form->create('CourseExemption');?>

<?php 

 if (isset($student_section_exam_status) && !empty($student_section_exam_status)) {   
?>
    <table><tr><td>
     <table>
            <tr><td class="font">Name:&nbsp;&nbsp;&nbsp;
            <?php 
                echo $student_section_exam_status['StudentBasicInfo']['full_name'];
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
   ?>
       
       </td></tr>
       
       </table>
 <?php } ?> 
 
 <table>
    <tr>
 	 <td>
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
	 </td>
	 <td>  <table>
       <tr><td>
       <table>	
        <tr><td class="smallheading">	
        <?php
             if ($role_id == ROLE_DEPARTMENT) {
        ?> 
		   <?php echo __('Course Exemption Request Waiting Decision'); ?>
		    <?php 
		    } else if ($role_id ==ROLE_REGISTRAR) {
		    ?>
		       <?php echo __('Course Exemption Approved by Department waiting registrar confirmation.'); 
		       ?>
		        <?php 
		    }
		?>
		</td></tr>
	<?php
		echo $this->Form->hidden('id');
		echo '<tr><td>Request Date : <strong>'.$this->Format->humanize_date($this->request->data['CourseExemption']['request_date']).'</strong></td></tr>';

		echo $this->Form->hidden('student_id',array('value'=>$student_section_exam_status['StudentBasicInfo']['id']));
		//echo '<tr><td>'.$this->Form->input('course_id',array('label'=>'Course Requested Exemption')).'</td></tr>';
	
		echo '<tr><td colspan=2><strong>Course to be exempted </strong></td></tr>';
		echo '<tr><td>Course Title:<strong>'.$this->request->data['Course']['course_title'].'</strong></td></tr>';
		echo '<tr><td>Course Code:<strong>'.$this->request->data['Course']['course_code'].'</strong></td></tr>';
		echo '<tr><td>Course Credit:<strong>'.$this->request->data['Course']['credit'].'</strong></td></tr>';
		echo '<tr><td colspan=2><strong> By </strong></td></tr>';
		echo '<tr><td>Course Title:<strong>'.$this->request->data['CourseExemption']['taken_course_title'].'</strong></td></tr>';
		echo '<tr><td>Course Code:<strong>'.$this->request->data['CourseExemption']['taken_course_code'].'</strong></td></tr>';
		echo '<tr><td> Course Credit:<strong>'.$this->request->data['CourseExemption']['course_taken_credit'].'</strong></td></tr>';
		  
			if (isset($this->request->data['Attachment']) && count($this->request->data['Attachment'])>0) { 
			    echo '<tr><td>';
			    echo 'PDF file uploaded on: '.$this->Format->humanize_date($this->request->data['Attachment'][0]['created']). '<br/> '; 
			  echo "<a href=".$this->Media->url($this->request->data['Attachment'][0]['dirname'].DS.$this->request->data['Attachment'][0]['basename'],true)." target=_blank'>View Attachment</a>";
		      echo '</td></tr>';
		    }
		    
		/* echo $this->Media->embedAsObject($courseExemption['Attachment'][0]['dirname'].DS.$courseExemption['Attachment'][0]['basename'],array('width'=>860,'height'=>'500'));
		 */
		 
		$options=array('1'=>'Accept','0'=>'Reject');
        $attributes=array('legend'=>false,'separator'=>"<br/>");
        if ($role_id == ROLE_DEPARTMENT) {
		echo '<tr><td style="padding-left:100px"> Accept/Reject Request <br/>'.$this->Form->radio('department_accept_reject',$options,$attributes).'</td></tr>';
		 echo '<tr><td>'.$this->Form->input('department_reason',array('label'=>'Reason')).'</td></tr>';
		} else if ($role_id == ROLE_REGISTRAR) {
			echo '<tr><td> Confirm/Deny Exemption <br/>'.$this->Form->radio('registrar_confirm_deny',$options,$attributes).'</td></tr>';
		 
		    echo '<tr><td>Reason</td></tr>';
		     echo '<tr><td>'.$this->Form->input('registrar_reason',array('label'=>false)).'</td></tr>';
		}
		
		
		
	?>
	</table>
	 </td>

	 </tr>
  </table></td>
	 </tr>
	 
  </table>

  <?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
