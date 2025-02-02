<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="courseExemptions form">
<?php echo $this->Form->create('CourseExemption',array('action' => 'add','type'=>'file'));?>
<?php 
  echo $this->element('student_basic');
  ?> 
       <table>
	        <?php 
	            if (!empty($previous_substitution_accepted)) {
	                echo "<tr><td class='smallheading' colspan=3> Previous course exemption request by you and accepted by the department.</td</tr>";
	                $count=0;
	                foreach ($previous_substitution_accepted as $psk=>$pvv) {
	                  echo "<tr><td><table>";
	                  echo "<tr><th>Course Title</th><th>Course Code</th><th>Credit</th></tr>";
	                  echo "<tr><td>".$pvv['CourseForSubstitued']['course_title']."</td><td>".$pvv['CourseForSubstitued']['course_code']."</td><td>".$pvv['CourseForSubstitued']['course_code']."</td></tr>";
	                   
	                  echo "</table></td><td class='smallheading' style='vertical-align:middle; align:center'>Substituted by => </td>";
	                  echo "<td>";
	                  echo "<table>";
echo "<tr><th>Course Title</th><th>Course Code</th><th>Credit</th></tr>";
echo "<tr><td>".$pvv['CourseBeSubstitued']['course_title']."</td><td>".$pvv['CourseBeSubstitued']['course_code']."</td><td>".$pvv['CourseBeSubstitued']['course_code']."</td></tr>";
echo "</table>";
	                  echo "</td></tr>";
	                }
	                
	            }
	        ?>
	    </table>
		<div class="smallheading"><?php echo __('Request Course Exemption'); ?></div>
	<?php
		//echo $this->Form->hidden('request_date');
		echo "<table>";
	//	echo $this->Form->input('course_id');
		echo "<tr><td width='25px'><table><tr><td style='width:24%'>Exempt Course</td><td style='width:76%'>".$this->Form->input('course_id',array('style'=>'width:250px',
		'label'=>false))."
		</td></tr></table></td><td><table>";
		
		
		echo "<tr><td>".$this->Form->input('taken_course_title')."</td></tr>";
		echo "<tr><td>".$this->Form->input('taken_course_code')."</td></tr>";
		echo "<tr><td>".$this->Form->input('course_taken_credit')."</td></tr>";
		echo "<tr><td>".$this->Form->input('reason').'</td></tr>';
		echo $this->Form->hidden('student_id',array('value'=>$student_section_exam_status['StudentBasicInfo']['id']));
		echo '<tr><td>'.$this->element('attachments', array('plugin' => 'media','label'=>'Upload profile picture')).'</td></tr></table></td></tr>';
		
		echo "</table>";
	
	?>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
