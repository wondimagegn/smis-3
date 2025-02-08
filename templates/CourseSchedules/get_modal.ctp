<?php 
	 if(isset($formatted_published_course_detail)){
	 	echo '<div> Course Code: '.$formatted_published_course_detail['course_code'].'</div>';
	 	echo '<div> Course Name: '.$formatted_published_course_detail['course_name'].'</div>';
	 	?>
	 	<table style="border: #CCC solid 1px">
	 	<tr><td colspan="2" class="centeralign_smallheading"> <?php echo 'Assigned Instructors'?></td></tr>
	 	<tr>
	 	<?php if(isset($formatted_published_course_detail['lecture'])) {?>
	 		<th style="border-right: #CCC solid 1px"><?php echo 'Lecture';?></th>
	 	<?php }
	 	 if(isset($formatted_published_course_detail['tutorial'])) {?>
	 		<th style="border-right: #CCC solid 1px"><?php echo 'Tutorial';?></th>
	 	<?php }
	 	 if(isset($formatted_published_course_detail['lab'])) { ?>
	 		<th style="border-right: #CCC solid 1px"><?php echo 'Laboratory';?></th>
	 	<?php } ?>
	 	</tr>
	 	<tr>
	 	<?php if(isset($formatted_published_course_detail['lecture'])) {?>
	 		<td style="border-right: #CCC solid 1px"><?php echo $formatted_published_course_detail['lecture'];?></td>
	 	<?php }
	 	 if(isset($formatted_published_course_detail['tutorial'])) {?>
	 		<td style="border-right: #CCC solid 1px"><?php echo $formatted_published_course_detail['tutorial'];?></td>
	 	<?php }
	 	 if(isset($formatted_published_course_detail['lab'])) { ?>
	 		<td style="border-right: #CCC solid 1px"><?php echo $formatted_published_course_detail['lab'];?></td>
	 	<?php } ?>
	 	</tr>	
<?php
	} 
	?>
</table>
