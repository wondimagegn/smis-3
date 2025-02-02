<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
//Get exam period listObjects
function getexamperiodlist() {
            //serialize form data
             var academicyear = $("#academicyear").val().split('/');
			var formatted_academicyear = academicyear[0]+'-'+academicyear[1];
            var formData = $("#ajax_instructor_id").val()+'~'+formatted_academicyear+'~'+$("#semester").val();
$("#instructor_exam_exclude_date_constraints_details").attr('disabled', true);
$("#instructor_exam_exclude_date_constraints_details").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/instructor_exam_exclude_date_constraints/get_instructor_exam_exclude_date_constraints_details/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
$("#instructor_exam_exclude_date_constraints_details").attr('disabled', false);
$("#instructor_exam_exclude_date_constraints_details").empty();
$("#instructor_exam_exclude_date_constraints_details").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
</script>
<div class="instructorExamExcludeDateConstraints form">
<?php echo $this->Form->create('InstructorExamExcludeDateConstraint');?>
<div class="smallheading"><?php echo __('Add/Edit Instructor Exam Session Exclude Constraint'); ?></div>
<div class="font"><?php echo 'Institute/College: '.$college_name?></div>
	<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('academicyear',array('label' =>false, 'id'=>'academicyear', 'type'=>'select','options'=>$acyear_array_data,'selected'=>isset($selected_academicyear)?$selected_academicyear:$defaultacademicyear, 'style'=>'width:200PX')).'</td>';
		echo '<td class="font"> Semester</td>';
		echo '<td >'.$this->Form->input('semester',array('label'=>false,'id'=>'semester', 'options'=>array('I'=>'I','II'=>'II', 'III'=>'III'),'selected'=>isset($selected_semester)?$selected_semester:"", 'style'=>'width:200PX')).'</td>'; 
		echo '<td class="font"> Department</td>';
		echo '<td colspan=2>'.$this->Form->input('department_id',array('label'=>false, 'type'=>'select','id'=>'department_id','options'=>$departments,'selected'=>isset($selected_department)?$selected_department:"",'empty'=>"--Select Department--", 'style'=>'width:200PX')).'</td></tr>';
	    echo '<tr><td colspan="6">'. $this->Form->Submit('Search',array('name'=>'search','class'=>'tiny radius button bg-blue', 'div'=>false)).'</td></tr>'; 			   
        ?>
    </table>
<?php 
	if (isset($instructors_list)) { 
		
		$dropdown_data_array= array();
		if(isset($instructors_list[0]['StaffForExam'])){
			foreach($instructors_list as $ilk=>$ilv){
				$count = 1;
				$dropdown_data_array['SFE'.'~'.$ilv['Staff']['id']]= ($ilv['Staff']['Title']['title'].' '.$ilv['Staff']['full_name'].' ( Position: '.$ilv['Staff']['Position']['position'].' - College: .'.$ilv['Staff']['College']['name'].')');
			}
		} else {
			foreach($instructors_list as $ilk=>$ilv){
				$count = 1;
				$dropdown_data_array['S'.'~'.$ilv['Staff']['id']]= ($ilv['Title']['title'].' '.$ilv['Staff']['full_name'].' ( Position: '.$ilv['Position']['position'].')');
			}
		}
		echo '<table cellpadding="0" cellspacing="0">';
		echo '<tr><td class="font">'.$this->Form->input('staff_id',array('id'=>'ajax_instructor_id','onchange'=>'getexamperiodlist()','label'=>'Instructors', 'type'=>'select','selected'=>isset($selected_instructor)?$selected_instructor:"",'empty'=>'---Please Instructor---', 'options'=>$dropdown_data_array)).'</td>';
		
		echo '</table>';
	?>
	<div id="instructor_exam_exclude_date_constraints_details"> 
	<?php

if(!empty($date_array)){
?>
		<table style='border: #CCC solid 1px'>
		<tr><td colspan="5" class="centeralign_smallheading"><?php echo("Select the sessions that the selected instructor is occupied.")?></td><tr>
		<tr><th style='border-right: #CCC solid 1px'>S.N<u>o</u>.</th>
		<th style='border-right: #CCC solid 1px'>Date</th>
		<th style='border-right: #CCC solid 1px'>1st Session(Morning)</th>
		<th style='border-right: #CCC solid 1px'>2nd Session(Afternoon)</th>
		<th style='border-right: #CCC solid 1px'>3rd Session(Evening)</th></tr>
		<!-- <th style='border-right: #CCC solid 1px'>Option</th></tr> -->
		<?php
		$count = 1;
	foreach($date_array as $dak=>$dav){
			echo "<tr><td style='border-right: #CCC solid 1px'>".$count++."</td>
				<td style='border-right: #CCC solid 1px'>".$this->Format->short_date($dav).' ('.date("l",strtotime($dav)).')'."</td>";
			if(isset($already_recorded_instructor_exam_excluded_date_constraints[$dav][1])){

				echo "<td style='border-right: #CCC solid 1px'> Excluded (".$this->Html->link(__('Delete'), array('action' => 'delete', $already_recorded_instructor_exam_excluded_date_constraints[$dav][1]['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $already_recorded_instructor_exam_excluded_date_constraints[$dav][1]['id'],"fromadd")).')'."</td>";
			} else {
				echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('InstructorExamExcludeDateConstraint.Selected.'.$dak.'-1',array('type'=>'checkbox','value'=>$dak.'-1', 'label'=>false))."</td>";
			}
			if(isset($already_recorded_instructor_exam_excluded_date_constraints[$dav][2])){
				echo "<td style='border-right: #CCC solid 1px'> Excluded (".$this->Html->link(__('Delete'), array('action' => 'delete', $already_recorded_instructor_exam_excluded_date_constraints[$dav][2]['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $already_recorded_instructor_exam_excluded_date_constraints[$dav][2]['id'],"fromadd")).')'."</td>";
			} else {
				echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('InstructorExamExcludeDateConstraint.Selected.'.$dak.'-2',array('type'=>'checkbox','value'=>$dak.'-2', 'label'=>false))."</td>";
			}
			if(isset($already_recorded_instructor_exam_excluded_date_constraints[$dav][3])){
				echo "<td style='border-right: #CCC solid 1px'> Excluded (".$this->Html->link(__('Delete'), array('action' => 'delete', $already_recorded_instructor_exam_excluded_date_constraints[$dav][3]['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $already_recorded_instructor_exam_excluded_date_constraints[$dav][3]['id'],"fromadd")).')'."</td></tr>";
			} else {
				echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('InstructorExamExcludeDateConstraint.Selected.'.$dak.'-3',array('type'=>'checkbox','value'=>$dak.'-3', 'label'=>false))."</td></tr>";
			}
	}
	?> </table>
	<?php echo $this->Form->Submit('Submit', array('name'=>'submit','class'=>'tiny radius button bg-blue','div'=>false));?>
<?php
}
?>
	</div>
	<?php
	}
?>

<?php echo $this->Form->end();?> 
</div>
