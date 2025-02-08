<?php echo $this->Form->create('ExamRoomNumberOfInvigilator');?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
        
//Class Room Combo
 function updateclassroomcombo() {
        var formData = $("#ajax_class_room_block").val();
		$("#ajax_class_room").empty();
		
		$("#ajax_class_room").attr('disabled', true);
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
</script> 
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="examRoomNumberOfInvigilators index">
	<h2><?php echo __('View Exam Room Number Of Invigilators');?></h2>
	<table cellpadding="0" cellspacing="0">
	<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td>'.$this->Form->input('ExamRoomNumberOfInvigilator.academicyear',array('label' => 'Academic Year','id'=>'academicyear','type'=>'select','options'=>$acyear_array_data,'selected'=>isset($selected_academicyear)?$selected_academicyear:$defaultacademicyear)).'</td>';
			
		echo '<td >'.$this->Form->input('ExamRoomNumberOfInvigilator.semester',array('id'=>'semester', 'options'=>array('I'=>'I','II'=>'II', 'III'=>'III'),'selected'=>isset($selected_semester)?$selected_semester:"")).'</td></tr>'; 
			
		echo '<td>'.$this->Form->input('ExamRoomNumberOfInvigilator.class_room_blocks',array('label' => 'Class Room Blocks','type'=>'select','id'=>'ajax_class_room_block','onchange'=>'updateclassroomcombo()', 'options'=>$formatted_class_room_blocks,'selected'=>isset($selected_class_room_block)?$selected_class_room_block:"", 'empty'=>"--Select Class Room Block--")).'</td>';
			
	   echo '<td>'. $this->Form->input('ExamRoomNumberOfInvigilator.class_room_id', array('id'=>'ajax_class_room','type'=>'select','options'=>$classRooms,'selected'=>isset($selected_class_room)?$selected_class_room:"",'empty'=>'--Select Class Room --')).'</td></tr>';

	   echo "<tr><td>".$this->Form->Submit('Submit', array('name'=>'search','class'=>'tiny radius button bg-blue','div'=>false))."</td></tr>";
	   
        ?>
        </table>
	</table>

	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('class_room_id');?></th>
			<th><?php echo $this->Paginator->sort('academic_year');?></th>
			<th><?php echo $this->Paginator->sort('semester');?></th>
			<th><?php echo $this->Paginator->sort('number_of_invigilator');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($examRoomNumberOfInvigilators as $examRoomNumberOfInvigilator):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($examRoomNumberOfInvigilator['ClassRoom']['id'], array('controller' => 'class_rooms', 'action' => 'view', $examRoomNumberOfInvigilator['ClassRoom']['id'])); ?>
		</td>
		<td><?php echo $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['semester']; ?>&nbsp;</td>
		<td><?php echo $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['number_of_invigilator']; ?>&nbsp;</td>
		<td><?php echo $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['created']; ?>&nbsp;</td>
		<td><?php echo $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
