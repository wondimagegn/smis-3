<?php
if(!empty($course_detail) && !empty($section_detail))
	echo '<p style="font-size:14px">'.$course_detail['course_title'].' ('.$course_detail['course_code'].') '.' exam result entry for '.$section_detail['name'].' section.</p>';
if(empty($published_course_id)) {
	echo '<div id="flashMessage" class="info-box message-box" style="text-align:center"><span></span>Please select a course for which you want to view exam result.</div>';
}
else if(empty($exam_types)) {
	echo '<div id="flashMessage" class="info-box message-box" style="text-align:center"><span></span>You need to create exam setup before you view exam result.</div>';
}
else if(count($students) <= 0 && count($student_adds) <= 0) {
	echo '<div id="flashMessage" class="info-box message-box" style="text-align:center"><span></span>The system unable to find list of students who are registered for the course you selected. Please contact your department for more information.</div>';
}
else {
	?>
<table>
	<tr>
		<th style="width:18%">Student Name</th>
		<th style="width:10%">Student ID</th>
		<?php
		$percent = 10;
		$last_percent = "";
		if(((100-28)/count($exam_types)) > 10) {
			$last_percent = (100-28) - (count($exam_types)*10);
		}
		else
			$percent = ((100-28)/count($exam_types));
		$count_for_percent = 0;
		foreach($exam_types as $key => $exam_type) {
		$count_for_percent++;
		?>
		<th style="width:<?php echo ($count_for_percent == count($exam_types) && $last_percent != "" ? $last_percent : $percent); ?>%">
		<?php
		echo $exam_type['ExamType']['exam_name'].' ('.$exam_type['ExamType']['percent'].'%)';
		?>
		</th>
		<?php
		}
		?>
	</tr>
	<?php
	foreach($students as $key => $student) {
	?>
	<tr>
		<td><?php echo $student['Student']['first_name'].' '.$student['Student']['middle_name'].' '.$student['Student']['last_name']; ?></td>
		<td><?php echo $student['Student']['studentnumber']; ?></td>
		<?php
		foreach($exam_types as $key => $exam_type) {
		?>
		<td>
		<?php
		$id = "";
		$value ="";
		if(isset($student['ExamResult']) && !empty($student['ExamResult'])) {
			foreach($student['ExamResult'] as $key => $examResult) {
				if($examResult['exam_type_id'] == $exam_type['ExamType']['id']) {
					$id = $examResult['id'];
					$value = $examResult['result'];
					break;
					}
			}
		}
		
		if($id != "") {
			echo $value;
		}
		else {
			echo '--';
		}
		?>
		</td>
		<?php
		}
		?>
	</tr>
	<?php
	}
	?>
</table>
	<?php
}
	if(count($student_adds) > 0) {
		echo '<p style="font-size:14px">Students who add '.$course_detail['course_title'].' ('.$course_detail['course_code'].') course from other section/s.</p>';
		?>
		<table>
		<tr>
			<th style="width:18%">Student Name</th>
			<th style="width:10%">Student ID</th>
			<?php
			$count_for_percent = 0;
			foreach($exam_types as $key => $exam_type) {
			$count_for_percent++;
			?>
			<th style="width:<?php echo ($count_for_percent == count($exam_types) && $last_percent != "" ? $last_percent : $percent); ?>%">
			<?php
			echo $exam_type['ExamType']['exam_name'].' ('.$exam_type['ExamType']['percent'].'%)';
			?>
			</th>
			<?php
			}
			?>
		</tr>
		<?php
		foreach($student_adds as $key => $student_add) {
		?>
		<tr>
			<td><?php echo $student_add['Student']['first_name'].' '.$student_add['Student']['middle_name'].' '.$student_add['Student']['last_name']; ?></td>
			<td><?php echo $student_add['Student']['studentnumber']; ?></td>
			<?php
			foreach($exam_types as $key => $exam_type) {
			?>
			<td>
			<?php
			$id = "";
			$value ="";
			if(isset($student_add['ExamResult']) && !empty($student_add['ExamResult'])) {
				foreach($student_add['ExamResult'] as $key => $examResult) {
					if($examResult['exam_type_id'] == $exam_type['ExamType']['id']) {
						$id = $examResult['id'];
						$value = $examResult['result'];
						break;
						}
				}
			}
		
			if($id != "") {
				echo $value;
			}
			else {
				echo '--';
			}
			?>
			</td>
			<?php
			}
			?>
		</tr>
		<?php
		}
		?>
	</table>
	<?php
	}
?>
