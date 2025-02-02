<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="courseExamGapConstraints form">
<?php echo $this->Form->create('CourseExamGapConstraint');?>
	<div class="smallheading"><?php echo __('Add Course Exam Gap Constraint'); ?></div>

<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('academicyear',array('label' => false, 'type'=>'select','options'=>$acyear_array_data,'selected'=>isset($selected_academicyear)?$selected_academicyear:"",'empty'=>"--Select Academic Year--", 'style'=>'width:150PX')).'</td>';
		echo '<td class="font"> Program</td>';
        echo '<td>'. $this->Form->input('program_id',array('label' => false, 'selected'=>isset($selected_program)?$selected_program:"",'empty'=>"--Select Program--", 'style'=>'width:150PX')).'</td>';
        echo '<td class="font"> Program Type</td>'; 
        echo '<td>'. $this->Form->input('program_type_id',array('label' => false, 'selected'=>isset($selected_program_type)?$selected_program_type:"",'empty'=>"--Select Program Type--", 'style'=>'width:150PX')).'</td></tr>'; 
		if($role_id == ROLE_COLLEGE) { 
			echo '<tr><td class="font"> Department</td>'; 
			echo '<td>'. $this->Form->input('department_id',array('label' => false, 'id'=>'ajax_department_course_exam_gap_constraints', 'selected'=>isset($selected_department)?$selected_department:"",'empty'=>'Pre/(Unassign Freshman)', 'style'=>'width:150PX')).'</td>';
            echo '<td class="font"> Year Level</td>';
            echo '<td id="ajax_year_level_course_exam_gap_constraints">'. $this->Form->input('year_level_id', array('label' => false, 'id'=>'ajax_year_level_cegc','selected'=>isset($selected_year_level)?$selected_year_level:"",'empty'=>'All', 'style'=>'width:150PX')).'</td>';  
        } else {
        	echo '<tr><td class="font"> Year Level</td>';
			echo '<td>'. $this->Form->input('year_level_id',array('label' => false, 'selected'=>isset($selected_year_level)?$selected_year_level:"",'empty'=>'All', 'style'=>'width:150PX')).'</td>';
		}
		echo '<td class="font"> Semester</td>';
		echo '<td >'.$this->Form->input('semester',array('label' => false, 'options'=>array('I'=>'I','II'=>'II', 'III'=>'III'),'selected'=>isset($selected_semester)?$selected_semester:"",'empty'=>'--select semester--', 'style'=>'width:150PX')).'</td></tr>'; 
        echo '<tr><td colspan="6">'. $this->Form->Submit('Search',array('name'=>'search','div'=>false,'class'=>'tiny radius button bg-blue', 'style'=>'width:150PX')).'</td></tr>'; 
	?> 
</table>
<?php 
	if (isset($sections_array) && !empty($sections_array)) { 
		$dropdown_data_array= array();
		foreach($sections_array as $sak=>$sav){
			foreach($sav as $sk=>$sv){
				$dropdown_data_array[$sak][$sv['published_course_id']]= ($sv['course_title'].' ('.$sv['course_code'].' - Cr.'.$sv['credit'].')');
				}
			}
		echo '<table cellpadding="0" cellspacing="0">';
		echo '<tr><td class="font"> Published Course</td>';
		echo '<td class="font">'.$this->Form->input('published_course_id',array('label'=>false, 'type'=>'select','empty'=>'---Please Select Course---', 'options'=>$dropdown_data_array, 'style'=>'width:350PX')).'</td>';
		echo '<td class="font"> Gap Before Exam</td>';
		echo '<td class="font">'.$this->Form->input('gap_before_exam',array('label'=>false, 'style'=>'width:50PX')).'</td></tr>';
		echo '<tr><td colspan="4">'.$this->Form->Submit('Submit',array('div'=>false,'class'=>'tiny radius button bg-blue','name'=>'submit','class'=>'tiny radius button bg-blue')).'</td></tr>';
		echo '</table>';	
	}
		//echo '</table>';
		if(isset($courseExamGapConstraints) && !empty($courseExamGapConstraints)) {
			echo '<div class="smallheading">Already Recorded Course Exam Gap Constraints</div>';
			echo "<table style='border: #CCC solid 1px'>";
			echo "<tr><th style='border-right: #CCC solid 1px'>No.</th>
				<th style='border-right: #CCC solid 1px'>Course</th>
				<th style='border-right: #CCC solid 1px'>Section</th>
				<th style='border-right: #CCC solid 1px'>Gap Before Exam No. Days</th>
				<th style='border-right: #CCC solid 1px'>Action</th></tr>";
			$count = 1;
			foreach($courseExamGapConstraints as $courseExamGapConstraint){
				echo "<tr><td style='border-right: #CCC solid 1px'>".$count++."</td><td style='border-right: #CCC solid 1px'>".
					$this->Html->link($courseExamGapConstraint['PublishedCourse']['Course']['course_code_title'].'('.$courseExamGapConstraint['PublishedCourse']['Course']['course_code'].' - Chr. '.$courseExamGapConstraint['PublishedCourse']['Course']['credit'].')',array('controller' => 'published_courses','action' =>'view',$courseExamGapConstraint['PublishedCourse']['Course']['id']))."</td>
				<td style='border-right: #CCC solid 1px'>".$courseExamGapConstraint['PublishedCourse']['Section']['name'].
				"</td><td style='border-right: #CCC solid 1px'>".$courseExamGapConstraint['CourseExamGapConstraint']['gap_before_exam'].
				"</td><td style='border-right: #CCC solid 1px'>".
				$this->Html->link(__('Delete'), array('action' => 'delete', $courseExamGapConstraint['CourseExamGapConstraint']['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $courseExamGapConstraint['CourseExamGapConstraint']['id'],"fromadd")).
				"</td></tr>";
			}
			echo "</table>";
		}
	?>
<?php $this->Form->end();?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
