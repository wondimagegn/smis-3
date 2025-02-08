<?php
//debug($date_array);
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
