<?php
if(!empty($organized_classRooms_blocks_data)){
	foreach($organized_classRooms_blocks_data as $ocrbdk =>$ocrbdv){
		echo '<table>';
		foreach($ocrbdv as $crbk =>$crbdv){
			echo '<tr><td class="font" width="300PX">'. $ocrbdk .' - Block '.$crbk.' - Class Rooms: </td>';
			//echo '<tr><td><table><tr>';
			foreach($crbdv as $crk=>$crv){
			 echo '<td>'.$this->Form->input('ClassRoomBlock.Selected.'.$crk,array('label'=>$crv,'type'=>'checkbox', 'value'=>$crk)).'</td>';
			}
			echo '</tr>';
			
		}
	}
	echo '</table>';
		echo '<div>'.$this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue')).'</div>';
		
	}
	if(isset($already_assign_class_rooms)) {
		?><div class="smallheading">Already Assign Class Rooms For This Program Type</div>
		<table style='border: #CCC solid 1px'>
		<tr><th style='border-right: #CCC solid 1px'>No.</th><th style='border-right: #CCC solid 1px'>Room
			</th><th style='border-right: #CCC solid 1px'>Block</th><th style='border-right: #CCC solid 1px'>Campus</th><th style='border-right: #CCC solid 1px'>Action</th></tr>
		<?php
		$count = 1;
		foreach($already_assign_class_rooms as $aacrk=>$aacrv){
			echo "<tr><td style='border-right: #CCC solid 1px'>".$count++. "</td><td style='border-right: #CCC solid 1px'>".
				$aacrv['ClassRoom']['room_code']."</td><td style='border-right: #CCC solid 1px'>".
				$aacrv['ClassRoom']['ClassRoomBlock']['block_code']."</td><td style='border-right: #CCC solid 1px'>".
				$aacrv['ClassRoom']['ClassRoomBlock']['Campus']['name'].
			"</td><td style='border-right: #CCC solid 1px'>".
			$this->Html->link(__('Delete'), array('action' => 'delete_assign_program_program_type', $aacrv['ProgramProgramTypeClassRoom']['id']),null, sprintf(__('Are you sure you want to delete %s?'), $aacrv['ClassRoom']['room_code'])).
			"</td></tr>";
		}
		?></table><?php
	}
//}
?>
