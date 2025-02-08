
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
$("#ajax_already_recorded_exam_room_number_of_invigilator").empty();
$("#ajax_class_room").attr('disabled', true);
//$("#ajax_already_recorded_constraints").attr('disabled', true);
//get form action
            var formUrl = '/exam_room_number_of_invigilators/get_class_rooms/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
$("#ajax_class_room").attr('disabled', false);
$("#ajax_class_room").empty();
$("#ajax_already_recorded_exam_room_number_of_invigilator").empty();
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
$("#ajax_already_recorded_exam_room_number_of_invigilator").attr('disabled', true);
$("#ajax_already_recorded_exam_room_number_of_invigilator").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/exam_room_number_of_invigilators/get_already_recorded_exam_room_number_of_invigilator/'+subCat;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: subCat,
                success: function(data,textStatus,xhr){
$("#ajax_already_recorded_exam_room_number_of_invigilator").attr('disabled', false);
$("#ajax_already_recorded_exam_room_number_of_invigilator").empty();
$("#ajax_already_recorded_exam_room_number_of_invigilator").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
        }

</script> <div class="examRoomNumberOfInvigilators form">
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<?php echo $this->Form->create('ExamRoomNumberOfInvigilator');?>
<div class="smallheading"><?php echo __('Add/Edit Exam Room Number Of Invigilator'); ?></div>
<div class="font"><?php echo 'Institute/College: '.$college_name?></div>
<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td>'.$this->Form->input('ExamRoomNumberOfInvigilator.academicyear',array('label' => 'Academic Year','id'=>'academicyear','type'=>'select','options'=>$acyear_array_data,'selected'=>isset($selected_academicyear)?$selected_academicyear:$defaultacademicyear)).'</td>';
			
		echo '<td >'.$this->Form->input('ExamRoomNumberOfInvigilator.semester',array('id'=>'semester', 'options'=>array('I'=>'I','II'=>'II', 'III'=>'III'),'selected'=>isset($selected_semester)?$selected_semester:"")).'</td></tr>'; 
			
		echo '<td>'.$this->Form->input('ExamRoomNumberOfInvigilator.class_room_blocks',array('label' => 'Class Room Blocks','type'=>'select','id'=>'ajax_class_room_block','onchange'=>'updateclassroomcombo()', 'options'=>$formatted_class_room_blocks,'selected'=>isset($selected_class_room_block)?$selected_class_room_block:"", 'empty'=>"--Select Class Room Block--")).'</td>';
			
	   echo '<td>'. $this->Form->input('ExamRoomNumberOfInvigilator.class_room_id', array('id'=>'ajax_class_room','onchange'=>'updateconstraints()','type'=>'select','options'=>$classRooms,'selected'=>isset($selected_class_room)?$selected_class_room:"",'empty'=>'--Select Class Room --')).'</td></tr>';
	   
        ?>
        </table>
<div id="ajax_already_recorded_exam_room_number_of_invigilator">
			<?php
if(isset($selected_class_room)){
	if(empty($already_recorded_exam_room_number_of_invigilators)){
?>
	<table style='border: #CCC solid 1px'>
	<?php 
		echo "<tr><td width='50%' clas='font'>".$this->Form->input('ExamRoomNumberOfInvigilator.number_of_invigilator')."</td>";
		echo "<tr><td>".$this->Form->Submit('Submit', array('name'=>'submit','class'=>'tiny radius button bg-blue','div'=>false))."</td></tr>";
		
		?>
	</table>
	<?php } ?>
		<table>
	<?php 
		if(!empty($already_recorded_exam_room_number_of_invigilators)) {
			echo '<div class="smallheading">Already Recorded Exam Room Number of Invigilators</div>';
			echo "<table style='border: #CCC solid 1px'>";
			echo "<tr><th style='border-right: #CCC solid 1px'>No.</th>
				<th style='border-right: #CCC solid 1px'>Exam Room</th>
				<th style='border-right: #CCC solid 1px'>Block</th>
				<th style='border-right: #CCC solid 1px'>Campus</th>
				<th style='border-right: #CCC solid 1px'>Number of Invigilators</th>
				<th style='border-right: #CCC solid 1px'>Action</th></tr>";
			$count = 1;
			foreach($already_recorded_exam_room_number_of_invigilators as $examRoomNumberOfInvigilator){
				echo "<tr><td style='border-right: #CCC solid 1px'>".$count++.
					"</td><td style='border-right: #CCC solid 1px'>".
					$examRoomNumberOfInvigilator['ClassRoom']['room_code'].
					"</td><td style='border-right: #CCC solid 1px'>".
					$examRoomNumberOfInvigilator['ClassRoom']['ClassRoomBlock']['block_code'].
					"</td><td style='border-right: #CCC solid 1px'>".
					$examRoomNumberOfInvigilator['ClassRoom']['ClassRoomBlock']['Campus']['name'].
					"</td><td style='border-right: #CCC solid 1px'>".$examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['number_of_invigilator'].
				"</td><td style='border-right: #CCC solid 1px'>".
			 	$this->Html->link(__('Edit'), array('action' => 'edit', $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['id']))."&nbsp;&nbsp;&nbsp;".
			 	$this->Html->link(__('Delete'), array('action' => 'delete', $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['id'],'fromadd'), null, sprintf(__('Are you sure you want to delete # %s?'), $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['id'],'fromadd')).
				"</td></tr>";
			}
		}
	echo "</table>";
}
?>
        </div>
<?php echo $this->Form->end(); ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
