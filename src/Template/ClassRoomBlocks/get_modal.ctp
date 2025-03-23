<div class="row">
<div class="large-12 columns">
<?php 
	 if(isset($programProgramTypes)){
	 	echo '<div> Class Room '.$programProgramTypes[0]['ClassRoom']['room_code'].' is assign to</div>';
	 	?>
	 	<table style="border: #CCC solid 1px">
	 	<tr><th style="border-right: #CCC solid 1px"><?php echo 'No.';?></th><th style="border-right: #CCC solid 1px"><?php echo 'Program';?></th><th style="border-right: #CCC solid 1px"><?php echo 'Program Type';?></th></tr>
	 	<?php 
	 	
	 	$count = 1;
		foreach ($programProgramTypes as $pptk=>$pptv){
			echo '<tr><td style="border-right: #CCC solid 1px">'.$count++.'</td>';
			echo '<td style="border-right: #CCC solid 1px">'.$pptv['Program']['name'].'</td>';
			echo '<td style="border-right: #CCC solid 1px">'.$pptv['ProgramType']['name'].'</td></tr>';
		 }
 			echo '<tr><td colspan="3"> *** To Add or Edit Class Room Program type assignment. 
			Please go to <a href="/classRoomBlocks/assign_program_program_type">Add/Edit Class Rooms</a> Program Type on Class Room Blocks</td></tr>';
	} else if(isset($classroomname)){
		echo '<div> Class Room '.$classroomname.' is yet not assign to any program type</div>';
		echo '<div> *** To Add Class Room Program type assignment. 
					Please go to Add/Edit Class Rooms Program Type on Class Room Blocks</div>';
	}
	?>
</table>
</div>
</div>
<a class="close-reveal-modal">&#215;</a>
