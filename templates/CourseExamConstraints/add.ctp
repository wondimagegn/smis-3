<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
//Get Class Period from a given week day
function getexamperiodlist() {
            //serialize form data
            var subCat = $("#ajax_course_exam_constraints").val();
$("#course_exam_constraints_details").attr('disabled', true);
$("#course_exam_constraints_details").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/course_exam_constraints/get_course_exam_constraints_details/'+subCat;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: subCat,
                success: function(data,textStatus,xhr){
$("#course_exam_constraints_details").attr('disabled', false);
$("#course_exam_constraints_details").empty();
$("#course_exam_constraints_details").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
		
<div class="courseExamConstraints form">
<?php echo $this->Form->create('CourseExamConstraint');?>
<div class="smallheading"><?php echo __('Add Course Exam Session Constraints'); ?></div>
<div class="font"><?php echo 'Institute/College: '.$college_name?></div>
<div class="info-box info-message"><font color=RED><u>Beaware:</u></font><br/> - All course exam can set in any exam period date and session except in excluded date & session by default.</Br> - But if you set exam period date and session for a given course , other unseted date and session of the exam period considered as unassigned data and session by default for that given course.</div>
<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('academicyear',array('label' => false, 'type'=>'select', 'options'=>$acyear_array_data,'selected'=>isset($selected_academicyear)?$selected_academicyear:"", 'empty'=>"--Select Academic Year--",'style'=>'width:150PX')).'</td>';
		echo '<td class="font"> Program</td>';
        echo '<td>'. $this->Form->input('program_id',array('label' => false, 'selected'=>isset($selected_program)?$selected_program:"",'empty'=>"--Select Program--", 'style'=>'width:150PX')).'</td>'; 
        echo '<td class="font"> Program Type</td>';
        echo '<td>'. $this->Form->input('program_type_id',array('label' => false, 'selected'=>isset($selected_program_type)?$selected_program_type:"",'empty'=>"--Select Program Type--", 'style'=>'width:150PX')).'</td></tr>'; 
		if($role_id == ROLE_COLLEGE) { 
			$departments['10000']='Pre/(Unassign Freshman)'; 
			echo '<tr><td class="font"> Department</td>';
			echo '<td>'. $this->Form->input('department_id',array('label' => false, 'id'=>'ajax_department_course_exam_constraints', 'selected'=>isset($selected_department)?$selected_department:"",'options'=>$departments,'empty'=>'--Select Department--', 'style'=>'width:150PX')).'</td>';
			echo '<td class="font"> Year Level</td>';
            echo '<td id="ajax_year_level_course_exam_constraints">'. $this->Form->input('year_level_id', array('label' => false, 'id'=>'ajax_year_level_cec','selected'=>isset($selected_year_level)?$selected_year_level:"", 'empty'=>'--Select Year Level--', 'style'=>'width:150PX')).'</td>';  

        } else {
        	echo '<tr><td class="font"> Year Level</td>';
			echo '<td>'. $this->Form->input('year_level_id',array('label' => false, 'selected'=>isset($selected_year_level)?$selected_year_level:"",'empty'=>'--Select Year Level--', 'style'=>'width:150PX')).'</td>';
		}
		echo '<td class="font"> Semester</td>';
		echo '<td >'.$this->Form->input('semester',array('label' => false, 'options'=>array('I'=>'I','II'=>'II', 'III'=>'III'),'selected'=>isset($selected_semester)?$selected_semester:"",'empty'=>'--select semester--', 'style'=>'width:150PX')).'</td></tr>'; 
        echo '<tr><td colspan="6">'. $this->Form->Submit('Search',array('name'=>'search','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>'; 
		
	?> 
</table>
<?php 
	if (isset($sections_array)) { 
		$dropdown_data_array= array();
		foreach($sections_array as $sak=>$sav){
			$count = 1;
			foreach($sav as $sk=>$sv){
				$dropdown_data_array[$sak][$sv['published_course_id']]= ($sv['course_title'].' ('.$sv['course_code'].' - Cr.'.$sv['credit'].')');
			}
		}
		echo '<table cellpadding="0" cellspacing="0">';
		echo '<tr><td class="font">'.$this->Form->input('published_course_id',array('id'=>'ajax_course_exam_constraints', 'onchange'=>'getexamperiodlist()','label'=>'Courses', 'type'=>'select','selected'=>isset($selected_published_course)?$selected_published_course:"",'empty'=>'---Please Select Course---', 'options'=>$dropdown_data_array)).'</td>';
		
		echo '</table>';
	}
?>
<div id="course_exam_constraints_details">
	<?php
if(!empty($exam_period_dates_array)){
?>
		<table style='border: #CCC solid 1px'>
		<tr><th style='border-right: #CCC solid 1px'>S.N<u>o</u>.</th>
		<th style='border-right: #CCC solid 1px'>Date</th>
		<th style='border-right: #CCC solid 1px'>1st Session(Morning)</th>
		<th style='border-right: #CCC solid 1px'>2nd Session(Afternoon)</th>
		<th style='border-right: #CCC solid 1px'>3rd Session(Evening)</th>
		<th style='border-right: #CCC solid 1px'>Option</th></tr>
		<?php
		$count = 1;
	foreach($exam_period_dates_array as $epdak=>$epdav){
		$option_select_count = 0;
		echo "<tr><td style='border-right: #CCC solid 1px'>".$count++."</td>
			<td style='border-right: #CCC solid 1px'>".$this->Format->short_date($epdav).' ('.date("l",strtotime($epdav)).')'."</td>";
		if(isset($excluded_session_by_date[$epdav][1])){
			$option_select_count++;
			echo "<td style='border-right: #CCC solid 1px'> Excluded. </td>";
		} else if(isset($already_recorded_course_exam_constraints_by_date[$epdav][1]) ||
		isset($already_recorded_course_exam_constraints_by_date[$epdav][0]) ){
			$option_select_count++;
			$active = null;
			if($already_recorded_course_exam_constraints_by_date[$epdav][1]['active'] == 1){
				$active = "Assign";
			} else {
				$active = "Do Not Assign";
			}
			echo "<td style='border-right: #CCC solid 1px'>".$active.' ('.$this->Html->link(__('Delete'), array('action' => 'delete', $already_recorded_course_exam_constraints_by_date[$epdav][1]['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $already_recorded_course_exam_constraints_by_date[$epdav][1]['id'],"fromadd")).')'."</td>";
		} else {
			echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('CourseExamConstraint.Selected.'.$epdak.'-1',array('type'=>'checkbox','value'=>$epdak.'-1', 'label'=>false))."</td>";
		}
		if(isset($excluded_session_by_date[$epdav][2])){
			$option_select_count++;
			echo "<td style='border-right: #CCC solid 1px'> Excluded. </td>";
		} else if(isset($already_recorded_course_exam_constraints_by_date[$epdav][2])){
			$option_select_count++;
			$active = null;
			if($already_recorded_course_exam_constraints_by_date[$epdav][2]['active'] == 1){
				$active = "Assign";
			} else {
				$active = "Do Not Assign";
			}
			echo "<td style='border-right: #CCC solid 1px'>".$active.' ('.$this->Html->link(__('Delete'), array('action' => 'delete', $already_recorded_course_exam_constraints_by_date[$epdav][2]['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $already_recorded_course_exam_constraints_by_date[$epdav][2]['id'],"fromadd")).')'."</td>";
		} else {
			echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('CourseExamConstraint.Selected.'.$epdak.'-2',array('type'=>'checkbox','value'=>$epdak.'-2', 'label'=>false))."</td>";
		}
		if(isset($excluded_session_by_date[$epdav][3])){
			$option_select_count++;
			echo "<td style='border-right: #CCC solid 1px'> Excluded. </td>";
		} else if(isset($already_recorded_course_exam_constraints_by_date[$epdav][3])){
			$option_select_count++;
			$active = null;
			if($already_recorded_course_exam_constraints_by_date[$epdav][3]['active'] == 1){
				$active = "Assign";
			} else {
				$active = "Do Not Assign";
			}
			echo "<td style='border-right: #CCC solid 1px'>".$active.' ('.$this->Html->link(__('Delete'), array('action' => 'delete', $already_recorded_course_exam_constraints_by_date[$epdav][3]['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $already_recorded_course_exam_constraints_by_date[$epdav][3]['id'],"fromadd")).')'."</td>";
		} else {
			echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('CourseExamConstraint.Selected.'.$epdak.'-3',array('type'=>'checkbox','value'=>$epdak.'-3', 'label'=>false))."</td>";
		}
		if($option_select_count == 3){
			echo "<td style='border-right: #CCC solid 1px'></td></tr>";
		} else {
			echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('CourseExamConstraint.active.'.$epdak,array('label'=>false,'type'=>'select','options'=>array(1=>'Assign',0=>'Do Not Assign')))."</td></tr>";
		}
	}
	?> </table>
	<?php echo $this->Form->Submit('Submit', array('name'=>'submit','class'=>'tiny radius button bg-blue','div'=>false));?>
<?php
}
?>
</div>
<?php echo $this->Form->end();?>
</div>            
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

