<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<?php echo $this->Html->script('jquery-department_placement');?>
<div class="excludedPublishedCourseExams form">
<?php echo $this->Form->create('ExcludedPublishedCourseExam');?>

<div class="smallheading"><?php echo __('Excluded Courses From Final Exam Schedule'); ?></div>

<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('academicyear',array('label' =>false, 'type'=>'select', 'options'=>$acyear_array_data,'empty'=>"--Select Academic Year--",'style'=>'width:150PX')).'</td>';
		echo '<td class="font"> Program</td>';
        echo '<td>'. $this->Form->input('program_id',array('label' =>false,'empty'=>"--Select Program--", 'style'=>'width:150PX')).'</td>'; 
        echo '<td class="font"> Program Type</td>';
        echo '<td>'. $this->Form->input('program_type_id',array('label' =>false,'empty'=>"--Select Program Type--", 'style'=>'width:150PX')).'</td></tr>'; 
		if($role_id == ROLE_COLLEGE) {
			echo '<tr><td class="font"> Department</td>';  
			echo '<td>'. $this->Form->input('department_id',array('label' =>false,'id'=>'ajax_department','empty'=>'--Select Department--', 'style'=>'width:150PX')).'</td>';
			echo '<td class="font"> Year Level</td>';
            echo '<td id="ajax_year_level">'. $this->Form->input('year_level_id',array('label' =>false,'id'=>'ajax_year_level_one','empty'=>'All', 'style'=>'width:150PX')).'</td>'; 
			
        } else {
        	echo '<tr><td class="font"> Year Level</td>';
			echo '<td>'. $this->Form->input('year_level_id',array('label' =>false,'empty'=>'All', 'style'=>'width:150PX')).'</td>';
		}
		echo '<td class="font"> Semester</td>';
		echo '<td >'.$this->Form->input('semester',array('label' =>false,'options'=>array('I'=>'I','II'=>'II','III'=>'III'),'empty'=>'--select semester--', 'style'=>'width:150PX')).'</td></tr>'; 
        echo '<tr><td colspan="6">'. $this->Form->Submit('Search',array('name'=>'search','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>'; 
		
	?> 
</table>
<?php 
	if (isset($sections_array)) { 
		$index =0;
		foreach($sections_array as $sak=>$sav){
			$count = 1;
			echo "<div class='smallheading'> Section : ".$sak."</div>";
			
			echo '<table style="border: #CCC double 3px "><tr>';
			echo '<th> No.</th>';
			echo "<th style='padding:0'> Select </th>";
			echo '<th> Course Title </th>';
			echo '<th> Course Code </th>';
			echo '<th> Credit </th>';
			echo '<th> L T L </th></tr>';

			foreach($sav as $sk=>$sv){
					echo '<tr><td>' . $count++ .'</td>';
					echo "<td>".$this->Form->checkbox('ExcludedPublishedCourseExams.selected.'. 
						$sv['published_course_id'])."</td>";
					echo '<td>'. $sv['course_title'] .'</td>';
					echo '<td>'. $sv['course_code'] .'</td>';
					echo '<td>'. $sv['credit'] .'</td>';
					echo '<td>'. $sv['credit_detail'] .'</td></tr>';
			}
			echo '</table>';
		}
		echo $this->Form->Submit('Excluded',array('div'=>false,'class'=>'tiny radius button bg-blue','name'=>'exclude'));
	}
echo $this->Form->end();
?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
