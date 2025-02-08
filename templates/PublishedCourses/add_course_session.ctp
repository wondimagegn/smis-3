<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="courseNumberOfSessions form">
<?php echo $this->Form->create('PublishedCourse');?>
<div class="smallheading"><?php echo __('Add Course Number of Session'); ?></div>

<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('academicyear',array('label' => false,'type'=>'select',
			'options'=>$acyear_array_data,'selected'=>isset($selected_academicyear)?$selected_academicyear:"",'empty'=>"--Select Academic Year--",'style'=>'width:150PX')).'</td>';
        echo '<td class="font"> Program</td>';
        echo '<td>'. $this->Form->input('program_id',array('label'=>false,'selected'=>isset($selected_program)?$selected_program:"",'empty'=>"--Select Program--",'style'=>'width:150PX')).'</td>'; 
        echo '<td class="font"> Program Type</td>';
        echo '<td>'. $this->Form->input('program_type_id',array('label'=>false,'selected'=>isset($selected_program_type)?$selected_program_type:"",'empty'=>"--Select Program Type--", 'style'=>'width:150PX')).'</td></tr>'; 
		if($role_id == ROLE_COLLEGE) { 
			echo '<tr><td class="font"> Department</td>'; 
			echo '<td>'. $this->Form->input('department_id',array('label'=>false,'id'=> 'ajax_department_published_course','selected'=>isset($selected_department)?$selected_department:"",'empty'=>'Pre/(Unassign Freshman)','style'=>'width:150PX')).'</td>';
            echo '<td class="font"> Year Level</td>';
            echo '<td id="ajax_year_level_published_course">'. $this->Form->input('year_level_id',array('label'=>false,'id'=>'ajax_year_level_published','selected'=>isset($selected_year_level)?$selected_year_level:"",'empty'=>'All','style'=>'width:150PX')).'</td>';  
			echo '<td class="font"> Semester</td>';
			echo '<td>'.$this->Form->input('semester',array('label'=>false,'options'=>array('I'=>'I','II'=>'II', 'III'=>'III'),'selected'=>isset($selected_semester)?$selected_semester:"",'empty'=>'--select semester--','style'=>'width:150PX')).'</td></tr>';

        } else {
        	echo '<tr><td class="font"> Year Level</td>';
			echo '<td>'. $this->Form->input('year_level_id',array('label'=>false,'selected'=>isset($selected_year_level)?$selected_year_level:"",'empty'=>'All','style'=>'width:150PX')).'</td>';
			echo '<td class="font"> Semester</td>';
			echo '<td>'.$this->Form->input('semester',array('label'=>false,'options'=>array('I'=>'I','II'=>'II','III'=>'III'),'selected'=>isset($selected_semester)?$selected_semester:"", 'empty'=>'--select semester--','style'=>'width:150PX')).'</td></tr>';
		} 
        echo '<tr><td colspan="6">'. $this->Form->Submit('Search',array('name'=>'search',
'class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>'; 
		
	?> 
</table>
<?php 
	if (isset($sections_array)) { 
		$dropdown_data_array= array();
		foreach($sections_array as $sak=>$sav){
			$count = 1;
			foreach($sav as $sk=>$sv){
				$dropdown_data_array[$sak][$sv['published_course_id']]= ($sv['course_title'].' ('.$sv['course_code'].
				' - Cr.'.$sv['credit'].' (L T L - '.$sv['credit_detail'].'))');
			}
		}

		echo '<table cellpadding="0" cellspacing="0">';
		echo '<tr><td class="font">'.$this->Form->input('courses',array('id'=>'ajax_course','type'=>'select',
			'empty'=>'---Please Select Course---','options'=>$dropdown_data_array)).'</td>';
		echo '<tr><td id="ajax_course_type_session"></td></tr>';
		echo '<tr><td colspan="2">'.$this->Form->Submit('Submit',array('div'=>false,'class'=>'tiny radius button bg-blue','name'=>'submit')).'</td></tr>';
		echo '</table>';
	
		if(isset($PublishedCorseHistory_formatted_array)) {
			echo '<div class="smallheading">Already Recorded Course Number of Session</div>';
			foreach($PublishedCorseHistory_formatted_array as $pchfk=>$pchfv){
				echo "<table style='border: #CCC solid 1px'>";
				echo "<tr><td colspan='8'; style='border-right: #CCC solid 1px'>".$pchfk."</td></tr>";
				echo "<tr><th style='border-right: #CCC solid 1px'>No.</th><th style='border-right: #CCC solid 1px'>Published Course

					</th><th style='border-right: #CCC solid 1px'>Course Code</th><th style='border-right: #CCC solid 1px'>

					Credit</th><th style='border-right: #CCC solid 1px'>L T L</th><th style='border-right: #CCC solid 1px'>

					lecture Number of Session</th><th style='border-right: #CCC solid 1px'>Tutorial Number of Session</th>

					<th style='border-right: #CCC solid 1px'>Lab Number of Session</th></tr>";
				$count = 1;
				foreach($pchfv as $publishedcoursedata){

					echo "<tr><td style='border-right: #CCC solid 1px'>".$count++."</td><td style='border-right: #CCC solid 1px'>".
						$this->Html->link($publishedcoursedata['course_title'], array('controller' => 
						'published_courses', 'action' => 'view', $publishedcoursedata['course_id'])).
						"</td><td style='border-right: #CCC solid 1px'>".
						$publishedcoursedata['course_code']."</td><td style='border-right: #CCC solid 1px'>".
						$publishedcoursedata['credit']."</td><td style='border-right: #CCC solid 1px'>".
						$publishedcoursedata['credit_detail']."</td><td style='border-right: #CCC solid 1px'>".
						$publishedcoursedata['lecture_number_of_session']."</td><td style='border-right: #CCC solid 1px'>".
						$publishedcoursedata['tutorial_number_of_session']."</td><td style='border-right: #CCC solid 1px'>".
						$publishedcoursedata['lab_number_of_session']."</td></tr>";
				}
				echo "</table>";
			}
		}
	}
echo $this->Form->end();
?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
