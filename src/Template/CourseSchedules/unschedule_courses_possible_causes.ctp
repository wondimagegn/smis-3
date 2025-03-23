<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<?php
if(isset($unschedule_published_courses)){
	//debug($unschedule_published_courses);
	?>
	<div class="smallheading"><?php echo("Unscheduled Course Details");?></div>
	<div class="font"><?php echo ("Institute/College: ". $college_name);?></div>
	<div class="font"><?php echo ("Program: ". $selected_program_name);?></div>
	<div class="font"><?php echo ("Program Type: ". $selected_program_type_name);?></div>
	<div class="font"><?php echo ("Academic Year: ". $selected_academic_year);?></div>
	<div class="font"><?php echo ("Semester: ". $selected_semester);?></div>
	
	<table style='border: #CCC solid 1px'>
		<tr><th style='border-right: #CCC solid 1px'>No.</th>
			<th style='border-right: #CCC solid 1px'>Course</th>
			<th style='border-right: #CCC solid 1px'>credit (LTL)</th>
			<th style='border-right: #CCC solid 1px'>Period length</th>
			<th style='border-right: #CCC solid 1px'>Period Type</th>
			<th style='border-right: #CCC solid 1px'>Section</th>
			<th style='border-right: #CCC solid 1px'>Split Section</th>
			<th style='border-right: #CCC solid 1px'>Possible Cause</th>
		</tr>
		<?php
		$count = 1;
		foreach($unschedule_published_courses as $unschedule_published_course){
			foreach($unschedule_published_course as $unscheduled_course){

			echo "<tr><td style='border-right: #CCC solid 1px'>".$count++. "</td>
					<td style='border-right: #CCC solid 1px'>".$unscheduled_course['course_title']." (".$unscheduled_course['course_code'].")</td>
					<td style='border-right: #CCC solid 1px'>".$unscheduled_course['credit']." (".$unscheduled_course['lecture_hours']." ".$unscheduled_course['tutorial_hours']." ".$unscheduled_course['laboratory_hours'].")</td>
					<td style='border-right: #CCC solid 1px'>".$unscheduled_course['period_length']." hours</td>
					<td style='border-right: #CCC solid 1px'>".$unscheduled_course['period_type']."</td>
					<td style='border-right: #CCC solid 1px'>".$unscheduled_course['section_name']."</td>";
			if(isset($unscheduled_course['split_section_name'])){
				echo	"<td style='border-right: #CCC solid 1px'>".$unscheduled_course['split_section_name']."</td>";
			} else {
				echo	"<td style='border-right: #CCC solid 1px'> --- </td>";
			}
			echo	"<td style='border-right: #CCC solid 1px'>".$unscheduled_course['possible_reason']."</td></tr>"; 
			}
		}
		?>
	</table>
<?php
}
?>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
