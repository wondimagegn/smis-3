<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
//Get exam period listObjects
function getAlreadyRecorded() {
            //serialize form data
             var academicyear = $("#academicyear").val().split('/');
			var formatted_academicyear = academicyear[0]+'-'+academicyear[1];
            var formData = $("#ajax_instructor_id").val()+'~'+formatted_academicyear+'~'+$("#semester").val()+'~'+$("#department_id").val();
$("#instructor_number_of_exam_constraints_details").attr('disabled', true);
$("#instructor_number_of_exam_constraints_details").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/instructor_number_of_exam_constraints/get_instructor_number_of_exam_constraints_details/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
$("#instructor_number_of_exam_constraints_details").attr('disabled', false);
$("#instructor_number_of_exam_constraints_details").empty();
$("#instructor_number_of_exam_constraints_details").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
</script>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="instructorNumberOfExamConstraints form">
<?php echo $this->Form->create('InstructorNumberOfExamConstraint');?>
<div class="smallheading"><?php echo __('Add/Edit Instructor Number Of Exam Constraint'); ?></div>
<div class="font"><?php echo 'Institute/College: '.$college_name?></div>
	<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('academicyear',array('label' => false, 'id'=>'academicyear', 'type'=>'select','options'=>$acyear_array_data,'selected'=>isset($selected_academicyear)?$selected_academicyear:$defaultacademicyear, 'style'=>'width:200PX')).'</td>';
		echo '<td class="font"> Semester</td>';
		echo '<td >'.$this->Form->input('semester',array('label'=>false, 'id'=>'semester', 'options'=>array('I'=>'I','II'=>'II', 'III'=>'III'),'selected'=>isset($selected_semester)?$selected_semester:"", 'style'=>'width:200PX')).'</td>'; 
		echo '<td class="font"> Department</td>';
		echo '<td colspan=2>'.$this->Form->input('department_id',array('label'=>false, 'type'=>'select', 'id'=>'department_id','options'=>$departments,'selected'=>isset($selected_department)?$selected_department:"",'empty'=>"--Select Department--", 'style'=>'width:200PX')).'</td></tr>';
	    echo '<tr><td colspan="6">'. $this->Form->Submit('Search',array('name'=>'search','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>'; 			   
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
		echo '<tr><td class="font">'.$this->Form->input('staff_id',array('id'=>'ajax_instructor_id','onchange'=>'getAlreadyRecorded()','label'=>'Instructors', 'type'=>'select','selected'=>isset($selected_instructor)?$selected_instructor:"",'empty'=>'---Please Instructor---', 'options'=>$dropdown_data_array)).'</td>';
		
		echo '</table>';
	?>
	<div id="instructor_number_of_exam_constraints_details"> 
<?php
if(isset($selected_instructor_id)){
?>
	<table style='border: #CCC solid 1px'>
	<?php 
		echo "<tr><td clas='font'>".$this->Form->input('InstructorNumberOfExamConstraint.max_number_of_exam')."</td>";
		echo '<td>'. $this->Form->input('InstructorNumberOfExamConstraint.year_level_id',array('id'=>'yearlevel','type'=>'select', 'multiple'=>'checkbox')).'</td></tr>';
		echo "<tr><td colspan='2'>".$this->Form->Submit('Submit', array('name'=>'submit','class'=>'tiny radius button bg-blue', 'div'=>false))."</td></tr>";
		
		?>
	</table>
	<table style='border: #CCC solid 1px'>
		<tr><td colspan="8" class="centeralign_smallheading"><?php echo("Already Recorded Instructor Number of Exam Constraints.")?></td></tr>
		<tr><th style='border-right: #CCC solid 1px'>S.N<u>o<u>.</th>
		<th style='border-right: #CCC solid 1px'>Instructor</th>
		<th style='border-right: #CCC solid 1px'>Position</th>
		<th style='border-right: #CCC solid 1px'>Year Level</th>
		<th style='border-right: #CCC solid 1px'>Academic Year</th>
		<th style='border-right: #CCC solid 1px'>Semester</th>
		<th style='border-right: #CCC solid 1px'>Maximum Number of Exam</th>
		<th style='border-right: #CCC solid 1px'>Actions</th></tr>
		<?php
		$count = 1;
	foreach($instructorNumberOfExamConstraints as $ineck=>$inecv){
			echo "<tr><td style='border-right: #CCC solid 1px'>".$count++."</td>";
			if(isset($inecv['StaffForExam'])){
				echo "<td style='border-right: #CCC solid 1px'>".$inecv['StaffForExam']['Staff']['Title']['title'].' '.$inecv['StaffForExam']['Staff']['full_name']."</td>";

				echo "<td style='border-right: #CCC solid 1px'>".$inecv['StaffForExam']['Staff']['Position']['position']."</td>";
			} else {
				echo "<td style='border-right: #CCC solid 1px'>".$inecv['Staff']['Title']['title'].' '.$inecv['Staff']['full_name']."</td>";

				echo "<td style='border-right: #CCC solid 1px'>".$inecv['Staff']['Position']['position']."</td>";
			}

				echo "<td style='border-right: #CCC solid 1px'>".$inecv['InstructorNumberOfExamConstraint']['year_level_id']."</td>";

				echo "<td style='border-right: #CCC solid 1px'>".$inecv['InstructorNumberOfExamConstraint']['academic_year']."</td>";
				echo "<td style='border-right: #CCC solid 1px'>".$inecv['InstructorNumberOfExamConstraint']['semester']."</td>";
				echo "<td style='border-right: #CCC solid 1px'>".$inecv['InstructorNumberOfExamConstraint']['max_number_of_exam']."</td>";
				echo "<td style='border-right: #CCC solid 1px'>".$this->Html->link(__('Delete'), array('action' => 'delete', $inecv['InstructorNumberOfExamConstraint']['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $inecv['InstructorNumberOfExamConstraint']['id'],"fromadd"))."</td></tr>";

		}
	?> </table>
<?php
}
?>
	</div>
	<?php
	}
?>

<?php echo $this->Form->end();?> 
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
