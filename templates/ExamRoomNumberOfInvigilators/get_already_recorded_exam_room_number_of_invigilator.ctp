<?php
if(isset($class_room_id)){
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
			echo "</table>";
		}
}
?>
