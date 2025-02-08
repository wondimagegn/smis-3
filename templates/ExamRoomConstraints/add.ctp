<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
        
//Class Room Combo
    function updateclassroomcombo() {
            //serialize form data
            //alert($("#ajax_class_room_block").val());
            var formData = $("#ajax_class_room_block").val();
$("#ajax_class_room").empty();
$("#ajax_exam_period_and_already_recorded_constraints").empty();
$("#ajax_class_room").attr('disabled', true);
//$("#ajax_already_recorded_constraints").attr('disabled', true);
//get form action
            var formUrl = '/exam_room_constraints/get_class_rooms/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
$("#ajax_class_room").attr('disabled', false);
$("#ajax_class_room").empty();
$("#ajax_exam_period_and_already_recorded_constraints").empty();
$("#ajax_class_room").append(data);
//End 
},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
});
return false;
}
//Get Exam Period and already recorded Exam Room Constraint
function updateconstraints(id) {
            //serialize form data
			var academicyear = $("#academicyear").val().split('/');
			var formatted_academicyear = academicyear[0]+'-'+academicyear[1];
            var subCat = $("#ajax_class_room").val()+'~'+formatted_academicyear+'~'+$("#semester").val();
$("#ajax_exam_period_and_already_recorded_constraints").attr('disabled', true);
$("#ajax_exam_period_and_already_recorded_constraints").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/exam_room_constraints/get_exam_period_and_already_recorded_data/'+subCat;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: subCat,
                success: function(data,textStatus,xhr){
$("#ajax_exam_period_and_already_recorded_constraints").attr('disabled', false);
$("#ajax_exam_period_and_already_recorded_constraints").empty();
$("#ajax_exam_period_and_already_recorded_constraints").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
        }

</script> 
<div class="examRoomConstraints form">
<?php echo $this->Form->create('ExamRoomConstraint');?>
<div class="smallheading"><?php echo __('Add/Edit Exam Rooms Session Constraints'); ?></div>
<div class="font"><?php echo 'Institute/College: '.$college_name?></div>
<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('ExamRoomConstraint.academicyear',array('label'=>false, 'id'=>'academicyear','type'=>'select','options'=>$acyear_array_data,'selected'=>isset($selected_academicyear)?$selected_academicyear:$defaultacademicyear,'style'=>'width:200PX')).'</td>';
		echo '<td class="font"> Semester</td>';
		echo '<td >'.$this->Form->input('ExamRoomConstraint.semester',array('label'=> false, 'id'=>'semester', 'options'=>array('I'=>'I','II'=>'II', 'III'=>'III'),'selected'=>isset($selected_semester)?$selected_semester:"",'style'=>'width:200PX')).'</td></tr>'; 
		echo '<tr><td class="font"> Class Room Block</td>';
		echo '<td>'.$this->Form->input('ExamRoomConstraint.class_room_blocks',array('label' => false, 'type'=>'select','id'=>'ajax_class_room_block','onchange'=>'updateclassroomcombo()', 'options'=>$formatted_class_room_blocks,'selected'=>isset($selected_class_room_block)?$selected_class_room_block:"", 'empty'=>"--Select Class Room Block--",'style'=>'width:200PX')).'</td>';
		echo '<td class="font"> Class Room</td>';	
	  	echo '<td>'. $this->Form->input('ExamRoomConstraint.class_room_id', array('label'=>false, 'id'=>'ajax_class_room','onchange'=>'updateconstraints()','type'=>'select','options'=>$classRooms,'selected'=>isset($selected_class_room)?$selected_class_room:"",'empty'=>'--Select Class Room --','style'=>'width:200PX')).'</td>';

        ?>
        <tr><td colspan="4"><div id="ajax_exam_period_and_already_recorded_constraints">
			<?php
if(!empty($date_array)){
?>
		<table style='border: #CCC solid 1px'>
		<tr><td colspan="5" class="centeralign_smallheading"><?php echo("Select the sessions that the selected class room is occupied.")?></td><tr>
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
			if(isset($already_recorded_exam_room_constraints_by_date[$dav][1])){
				$active = null;
				if($already_recorded_exam_room_constraints_by_date[$dav][1]['active'] == 0){
					$active = "Occupied";
				} else {
					$active = "Free";
				}
				echo "<td style='border-right: #CCC solid 1px'>".$active.' ('.$this->Html->link(__('Delete'), array('action' => 'delete', $already_recorded_exam_room_constraints_by_date[$dav][1]['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $already_recorded_exam_room_constraints_by_date[$dav][1]['id'],"fromadd")).')'."</td>";
			} else {
				echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('ExamRoomConstraint.Selected.'.$dak.'-1',array('type'=>'checkbox','value'=>$dak.'-1', 'label'=>false))."</td>";
			}
			if(isset($already_recorded_exam_room_constraints_by_date[$dav][2])){
				$active = null;
				if($already_recorded_exam_room_constraints_by_date[$dav][2]['active'] == 0){
					$active = "Occupied";
				} else {
					$active = "Free";
				}
				echo "<td style='border-right: #CCC solid 1px'>".$active.' ('.$this->Html->link(__('Delete'), array('action' => 'delete', $already_recorded_exam_room_constraints_by_date[$dav][2]['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $already_recorded_exam_room_constraints_by_date[$dav][2]['id'],"fromadd")).')'."</td>";
			} else {
				echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('ExamRoomConstraint.Selected.'.$dak.'-2',array('type'=>'checkbox','value'=>$dak.'-2', 'label'=>false))."</td>";
			}
			if(isset($already_recorded_exam_room_constraints_by_date[$dav][3])){
				$active = null;
				if($already_recorded_exam_room_constraints_by_date[$dav][3]['active'] == 0){
					$active = "Occupied";
				} else {
					$active = "Free";
				}
				echo "<td style='border-right: #CCC solid 1px'>".$active.' ('.$this->Html->link(__('Delete'), array('action' => 'delete', $already_recorded_exam_room_constraints_by_date[$dav][3]['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $already_recorded_exam_room_constraints_by_date[$dav][3]['id'],"fromadd")).')'."</td></tr>";
			} else {
				echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('ExamRoomConstraint.Selected.'.$dak.'-3',array('type'=>'checkbox','value'=>$dak.'-3', 'label'=>false))."</td></tr>";
			}
	}
	?> </table>
	<?php echo $this->Form->Submit('Submit', array('name'=>'submit','class'=>'tiny radius button bg-blue','div'=>false));?>
<?php
}
?>
        </div></td></tr>
</table>
<?php echo $this->Form->end(); ?>
</div>
