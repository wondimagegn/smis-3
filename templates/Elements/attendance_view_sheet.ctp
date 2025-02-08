<br />
<style>
.low_padding tr td{
padding:3px
}
</style>
<?php
if(isset($student_course_attendance_details)) {
	if(!empty($course_detail['lecture_attendance_requirement'])) {
		?>
		<p class="fs13">Lecture minimum attendance requirement: <?php echo $course_detail['lecture_attendance_requirement']; ?>%</p>
	<?php
	}
	if(!empty($student_course_attendance_details['register'])) {
		$attendace_for_header = $student_course_attendance_details['register'][0]['Attendance'];
		$columens = count($attendace_for_header)+4;
	}
	else {
		$attendace_for_header = $student_course_attendance_details['add'][0]['Attendance'];
		$columens = count($attendace_for_header)+4;
	}
	if(!empty($student_course_attendance_details['register'])) {
		?>
		<p class="fs13">Students who are registered for <u><?php echo $course_detail['course_title'].' ('.$course_detail['course_code'].')'; ?></u> course.</p>
	<table class="low_padding" style="width:<?php echo ((42+(count($attendace_for_header)*7)) > 100 ? (42+(count($attendace_for_header)*10)): 100); ?>%">
		<tr>
			<th style="width:2%">N<u>o</u></th>
			<th style="width:20%">Student Name</th>
			<th style="width:10%">ID</th>
			<?php
			foreach($attendace_for_header as $key => $header) {
				$formatted_date = date('M d, y', mktime (0, 0, 0, 
					substr($header['Attendance']['attendance_date'],5 ,2), 
					substr($header['Attendance']['attendance_date'],8 ,2), 
					substr($header['Attendance']['attendance_date'],0 ,4))
				);
				?>
				<th style="width:7%"><?php echo $formatted_date; ?></th>
				<?php
			}
				?>
				<th style="width:<?php echo ((42+(count($attendace_for_header)*7)) >= 100 ? 10 : (100 - (32+(count($attendace_for_header)*7))) )?>%">Present %</th>
		</tr>
	<?php
	$st_count = 0;
	foreach($student_course_attendance_details['register'] as $key => $attendnce) {
		$st_count++;
		?>
		<tr>
			<td><?php echo $st_count; ?></td>
			<td><?php echo $attendnce['Student']['first_name'].' '.$attendnce['Student']['middle_name'].' '.$attendnce['Student']['last_name']; ?></td>
			<td><?php echo $attendnce['Student']['studentnumber']; ?></td>
			<?php
			$present_count = 0;
			foreach($attendnce['Attendance'] as $key => $value) {
				if($value['Attendance']['attendance'] == 1)
					$present_count++;
				?>
				<td class="<?php echo ($value['Attendance']['attendance'] == 1 ? 'accepted' : 'rejected'); ?>"><?php 
				if(!empty($value['Attendance']['remark'])) {
					echo '<span style="cursor:help" title="View Remark" onclick="alert(\'Remark: '.addslashes($value['Attendance']['remark']).'\')">';
				}
				echo ($value['Attendance']['attendance'] == 1 ? 'Yes' : 'No'); 
				if(!empty($value['Attendance']['remark'])) {
					echo ' ?</span>';
				}
				?></td>
				<?php
			}
			?>
			<td><?php 
			$present_percent = ($present_count/count($attendnce['Attendance']))*100;
			echo number_format($present_percent, 0).'%'; 
			?></td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php
	}
//COURSE ADD
	if(!empty($student_course_attendance_details['add'])) {
?>
<br />
<p class="fs13">Students who are add <u><?php echo $course_detail['course_title'].' ('.$course_detail['course_code'].')'; ?></u> course.</p>
	<table class="low_padding" style="width:<?php echo ((42+(count($attendace_for_header)*7)) > 100 ? (42+(count($attendace_for_header)*10)): 100); ?>%">
		<tr>
			<th style="width:2%">N<u>o</u></th>
			<th style="width:20%">Student Name</th>
			<th style="width:10%">ID</th>
			<?php
			foreach($attendace_for_header as $key => $header) {
				$formatted_date = date('M d, y', mktime (0, 0, 0, 
					substr($header['Attendance']['attendance_date'],5 ,2), 
					substr($header['Attendance']['attendance_date'],8 ,2), 
					substr($header['Attendance']['attendance_date'],0 ,4))
				);
				?>
				<th style="width:7%"><?php echo $formatted_date; ?></th>
				<?php
			}
				?>
				<th style="width:<?php echo ((42+(count($attendace_for_header)*7)) >= 100 ? 10 : (100 - (32+(count($attendace_for_header)*7))) )?>%">Present %</th>
		</tr>
	<?php
	$st_count = 0;
	foreach($student_course_attendance_details['add'] as $key => $attendnce) {
		$st_count++;
		?>
		<tr>
			<td><?php echo $st_count; ?></td>
			<td><?php echo $attendnce['Student']['first_name'].' '.$attendnce['Student']['middle_name'].' '.$attendnce['Student']['last_name']; ?></td>
			<td><?php echo $attendnce['Student']['studentnumber']; ?></td>
			<?php
			$present_count = 0;
			foreach($attendnce['Attendance'] as $key => $value) {
				if($value['Attendance']['attendance'] == 1)
					$present_count++;
				?>
				<td class="<?php echo ($value['Attendance']['attendance'] == 1 ? 'accepted' : 'rejected'); ?>"><?php
				if(!empty($value['Attendance']['remark'])) {
					echo '<span style="cursor:help" title="View Remark" onclick="alert(\'Remark: '.addslashes($value['Attendance']['remark']).'\')">';
				}
				echo ($value['Attendance']['attendance'] == 1 ? 'Yes' : 'No'); 
				if(!empty($value['Attendance']['remark'])) {
					echo ' ?</span>';
				}
				?></td>
				<?php
			}
			?>
			<td><?php 
			$present_percent = ($present_count/count($attendnce['Attendance']))*100;
			echo number_format($present_percent, 0).'%'; 
			?></td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php
	}
}
?>
