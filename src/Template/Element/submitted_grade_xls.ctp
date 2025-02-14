<?php
	header("Expires: " . gmdate("D,d M YH:i:s") . " GMT");
	header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=" . $filename . ".xls");
	header("Content-Description: Exported as XLS");
	?>
	<?php
	if (!isset($grade_view_only)) {
		$grade_view_only = false;
	} ?>

	<style>
		table.grade_list tr td {
			padding: 0px;
			vertical-align: middle;
		}
	</style>

	<table class="fs13">
		<tr>
			<td colspan="8" style="text-align:center">
				<?php
				if (isset($university['University']['name'])) {
					echo $university['University']['name'];
				} else {
					echo '----';
				} ?>
			</td>
		</tr>
		<tr><td colspan="8" style="text-align:center">OFFICE OF THE REGISTRAR</td></tr>
		<tr><td colspan="8" style="text-align:center">Mark Sheet</td></tr>
		<tr>
			<td>COLLEGE/INSTITUTE: </td>
			<td>
				<?php
				if (isset($publish_course_detail['Department']['College']['name'])) {
					echo $publish_course_detail['Department']['College']['name'];
				} ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php
				if (isset($publish_course_detail['Department']['name'])) {
					echo $publish_course_detail['Department']['name'];
				} ?>
			</td>
			<td>
				<?php
				if (isset($publish_course_detail['Section']['name'])) {
					echo $publish_course_detail['Section']['name'];
				} ?>
			</td>
			<td>
				<?php
				if (isset($publish_course_detail['Section']['YearLevel']['name'])) {
					echo $publish_course_detail['Section']['YearLevel']['name'];
				} ?>
			</td>
			<td>
				<?php
				if (isset($publish_course_detail['Course']['course_title'])) {
					echo $publish_course_detail['Course']['course_title'];
				} ?>
			</td>
		</tr>
		<tr>
			<td>Department </td>
			<td>Section </td>
			<td>CLASS YEAR </td>
			<td>COURSE TITLE </td>
		</tr>
		<tr>
			<td>
				<?php
				if (isset($publish_course_detail['Course']['course_code'])) {
					echo $publish_course_detail['Course']['course_code'];
				} ?>
			</td>
			<td>
				<?php
				if (isset($publish_course_detail['Course']['credit'])) {
					echo $publish_course_detail['Course']['credit'];
				} ?>
			</td>
			<td>
				<?php
				if (isset($publish_course_detail['PublishedCourse']['semester'])) {
					echo $publish_course_detail['PublishedCourse']['semester'];
				} ?>
			</td>
			<td><?=  $publish_course_detail['PublishedCourse']['semester']; ?></td>
			<td>
				<?php
				if (isset($publish_course_detail['CourseInstructorAssignment'])) {
					echo $publish_course_detail['CourseInstructorAssignment'][0]['Staff']['full_name'];
				} else {
					echo 'Submitted by department';
				} ?>
			</td>
			<td><?=  $publish_course_detail['PublishedCourse']['academic_year']; ?></td>
		</tr>
		<tr>
			<td>Course N<u>o</u></td>
			<td>
				<?php
				if (isset($publish_course_detail['Course']['Curriculum']['type_credit']) && $publish_course_detail['Course']['Curriculum']['type_credit'] == "ECTS Credit Point") {
					echo 'ECTS';
				} else {
					echo 'Credit';
				} ?>
			</td>
			<td>SEMESTER</td>
			<td>INSTRUCTOR</td>
			<td>ACADEMIC YEAR</td>
		</tr>
	</table>

	<table class="grade_list">
		<tr>
			<th style="width:2%">&nbsp;</th>
			<th style="width:10%">Student ID</th>
			<th style="width:16%">Student Name</th>
			<th style="width:3%">Sex</th>
			<?php
			$percent = 10;
			$last_percent = "";
			//If it is makeup exam entry
			if ($grade_view_only) {
				//It is exam grade view only and there is nothing to do for now
				$percent = 10;
				$last_percent = 42;
			} else if ($makeup_exam) { ?>
				<th style="width:<?=  (!($grade_submission_status['grade_submited']  || $display_grade || $view_only) ? 72 : 10); ?>%">Total (100%)</th>
				<?php
				$last_percent = 32;
			} else {
				//If it is not makeup exams (add and registered)
				$grade_width = 0;

				if ($grade_submission_status['grade_submited']) {
					$grade_width = 3;
				} else if ($display_grade || $view_only) {
					$grade_width = 3;
				}

				if (((100 - 28) / ((count($exam_types) + 1) + $grade_width)) > 10) {
					$last_percent = (100 - 28) - ((count($exam_types) + 1 + $grade_width) * 10);
				} else {
					$percent = ((100 - 28) / (count($exam_types) + 1 + $grade_width));
				}

				$count_for_percent = 0;

				foreach ($exam_types as $key => $exam_type) {
					$count_for_percent++; ?>
					<th style="width:<?=  ($count_for_percent == (count($exam_types) + 1) && $last_percent != "" && !($grade_submission_status['grade_submited'] || $display_grade || $view_only) ? $last_percent + $percent : $percent); ?>%">
						<?=  $exam_type['ExamType']['exam_name'] . ' (' . $exam_type['ExamType']['percent'] . '%)'; ?>
					</th>
					<?php
				} ?>
				<th style="width:<?=  (!($grade_submission_status['grade_submited'] || $display_grade || $view_only) ? $last_percent + $percent : $percent); ?>%">Total (100%)</th>
				<?php
			}
			//End of non-makeup exams

			//It it is submited grade or on "grade preview" state
			if ($view_only || $grade_submission_status['grade_submited'] || $display_grade) { ?>
				<th style="width:<?=  $percent; ?>%">Grade</th>
				<th style="width:<?=  $percent; ?>%">In Progress</th>
				<th style="width:<?=  ($last_percent != "" ? $last_percent + $percent : $percent); ?>%">Status</th>
				<?php
			} ?>
		</tr>
	</table>