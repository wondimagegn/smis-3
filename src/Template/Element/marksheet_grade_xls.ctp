<?php
//Configure::write('debug', '0');
header("Expires: " . gmdate("D,d M YH:i:s") . " GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=" . $filename . ".xls");
header("Content-Description: Exported as XLS");

if (!isset($grade_view_only)) {
	$grade_view_only = false;
} ?>

<table class="fs13">
	<tr><td colspan=<?= ((int) count($exam_types) + 6); ?> style="text-align:center"><b><?= (isset($university['University']['name']) ? strtoupper($university['University']['name']) : '---'); ?></b></td></tr>
	<tr><td colspan=<?= ((int) count($exam_types) + 6); ?> style="text-align:center"><b>OFFICE OF THE REGISTRAR</b></td></tr>
	<tr><td colspan=<?= ((int) count($exam_types) + 6); ?> style="text-align:center"><b>MARK SHEET</b></td></tr>
	<tr><td colspan=<?= ((int) count($exam_types) + 6); ?>>&nbsp;</td></tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan=4 style="text-align:left"><b><?= (isset($publish_course_detail_info['Department']['College']['type']) ? strtoupper($publish_course_detail_info['Department']['College']['type']) : (isset($publish_course_detail_info['College']['type']) ? strtoupper($publish_course_detail_info['College']['type']) : strtoupper('College'))); ?>:</b> &nbsp; <?= (isset($publish_course_detail_info['Department']['College']['name']) ? $publish_course_detail_info['Department']['College']['name'] : $publish_course_detail_info['College']['name']); ?></td>
		<td>&nbsp;</td>
		<td colspan=4 style="text-align:right"><b><?= (isset($publish_course_detail_info['Department']['type']) ? strtoupper($publish_course_detail_info['Department']['type']) : 'DEPARTMENT'); ?>:</b> &nbsp; <?= (isset($publish_course_detail_info['Department']['name']) ? $publish_course_detail_info['Department']['name'] :($publish_course_detail_info['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Pre/Freshman')); ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="text-align:right"><?= (isset($publish_course_detail_info['Section']['YearLevel']['name']) ? $publish_course_detail_info['Section']['YearLevel']['name'] : ($publish_course_detail_info['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial/1st' : 'Pre/1st')); ?></td>
		<td style="text-align:right"><?= (isset($publish_course_detail_info['Section']['name']) ? $publish_course_detail_info['Section']['name'] : ''); ?></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td colspan=5 style="text-align:right"><?= (isset($publish_course_detail_info['Course']['course_title']) ? $publish_course_detail_info['Course']['course_title'] : ''); ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="text-align:right; font-weight: bold;">CLASS YEAR</td>
		<td style="text-align:right; font-weight: bold;">SECTION</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td colspan=5 style="text-align:right; font-weight: bold;">COURSE TITLE</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="text-align:right"><?= (isset($publish_course_detail_info['Course']['course_code']) ? $publish_course_detail_info['Course']['course_code'] : ''); ?></td>
		<td style="text-align:right"><?= (isset($publish_course_detail_info['Course']['credit']) ? $publish_course_detail_info['Course']['credit'] : ''); ?></td>
		<td>&nbsp;</td>
		<td style="text-align:right"><?= $publish_course_detail_info['PublishedCourse']['semester']; ?></td>
		<td>&nbsp;</td>
		<td style="text-align:right"><?= $publish_course_detail_info['PublishedCourse']['academic_year']; ?></td>
		<td>&nbsp;</td>
		<td colspan=2 style="text-align:right"><?= (isset($publish_course_detail_info['CourseInstructorAssignment'][0]['Staff']['full_name']) ? $publish_course_detail_info['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. ' . $publish_course_detail_info['CourseInstructorAssignment'][0]['Staff']['full_name'] : 'Submitted by Department'); ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td style="text-align:right; font-weight: bold;">COURSE CODE</td>
		<td style="text-align:right; font-weight: bold;"><?= ((isset($publish_course_detail_info['Course']['Curriculum']['type_credit']) && (count(explode('ECTS', $publish_course_detail['Course']['Curriculum']['type_credit'])) >= 2)) ? 'ECTS' : 'CREDIT'); ?></td>
		<td>&nbsp;</td>
		<td style="text-align:right; font-weight: bold;">SEMESTER</td>
		<td>&nbsp;</td>
		<td style="text-align:right; font-weight: bold;">ACADEMIC YEAR</td>
		<td>&nbsp;</td>
		<td colspan=2 style="text-align:right; font-weight: bold;">INSTRUCTOR</td>
	</tr>
</table>
<br>

<style>
	table, th, td {
	border: 1px solid black;
	border-collapse: collapse;
	}
</style>

<table cellpadding="1" cellspacing="1">
	<thead>
		<tr>
			<th style="text-align: center; vertical-align: middle;">#</th>
			<th style="text-align: center; vertical-align: middle;">Student ID</th>
			<th style="vertical-align: middle;">Student Name</th>
			<th style="text-align: center; vertical-align: middle;">Sex</th>

			<?php
			$percent = 10;
			$last_percent = "";

			if ($grade_view_only) {
				$percent = 10;
				$last_percent = 42;
			} else if (isset($makeup_exam)) { ?>
				<th style="text-align: center; vertical-align: middle;">Total(100%)</th>
				<?php
				$last_percent = 32;
			} else {
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

				if (!empty($exam_types)) {
					foreach ($exam_types as $key => $exam_type) {
						$count_for_percent++; ?>
						<th style="text-align: center; vertical-align: middle;"><?= $exam_type['ExamType']['exam_name'] . ' (' . $exam_type['ExamType']['percent'] . '%)'; ?></th>
						<?php
					}
				} ?>

				<th style="text-align: center; vertical-align: middle;">Total(100%)</th>
				<?php
			}

			if ($view_only || $grade_submission_status['grade_submited'] || $display_grade) { ?>
				<th style="text-align: center; vertical-align: middle;">Grade</th>
				<?php
			} ?>
		</tr>
	</thead>
	<tbody>
		<?php
		if (!isset($total_student_count)) {
			$total_student_count = count($students);
		}

		if (!empty($students)) {
			$st_count = 0;
			foreach ($students as $key => $student) {

				$grade_history_count = 0;

				if (isset($student['freshman_program']) && $student['freshman_program']) {
					$freshman_program = true;
					$approver = 'freshman program';
					$approver_c = 'Freshman Program';
				} else {
					$freshman_program = false;
					$approver = 'department';
					$approver_c = 'Department';
				}

				$total_100 = "";
				$st_count++; ?>
				<tr>
					<td style="text-align: center; vertical-align: middle;">&nbsp;<?= $st_count; ?>&nbsp;</td>
					<td style="text-align: center; vertical-align: middle;">&nbsp;<?= $student['Student']['studentnumber']; ?>&nbsp;</td>
					<td style="vertical-align: middle;">&nbsp;<?= $student['Student']['first_name'] . ' ' . $student['Student']['middle_name'] . ' ' . $student['Student']['last_name']; ?>&nbsp;</td>
					<td style="text-align: center; vertical-align: middle;"><?= ((strcasecmp(trim($student['Student']['gender']), 'male') == 0) ? 'M' : 'F') ?></td>

					<?php
					if ($grade_view_only) {
						// nothing
					} else if (isset($makeup_exam)) { ?>
						<td style="text-align: center; vertical-align: middle;">
							<?php
							if (!empty($student['ExamGradeChange']) && $student['ExamGradeChange'][0]['department_approval'] != -1) {
								echo ($student['MakeupExam']['result'] != null ? $student['MakeupExam']['result'] : '---');
							} else {
								if ($display_grade || $view_only) {
									echo ($student['MakeupExam']['result'] != null ? $student['MakeupExam']['result'] : '---');
								} else {

								}
							} ?>
						</td>
						<?php
					} else {
						$et_count = 0;
						$count = 0;
						if (!empty($exam_types)) {
							foreach ($exam_types as $key => $exam_type) {
								$et_count++; ?>
								<td style="text-align: center; vertical-align: middle;">
									<?php
									$id = "";
									$value = "";
									if (isset($student['ExamResult']) && !empty($student['ExamResult'])) {
										foreach ($student['ExamResult'] as $key => $examResult) {
											if ($examResult['exam_type_id'] == $exam_type['ExamType']['id']) {
												$id = $examResult['id'];
												$value = $examResult['result'];
												$total_100 += $value;
												break;
											}
										}
									}
									
									$i = (($st_count - 1) * count($exam_types)) + 1;

									if ($display_grade || $view_only || (!empty($student['ExamGrade']) && $student['ExamGrade'][0]['department_approval'] != -1)) {
										echo ($value != "" ? $value : '---');
									} else {
										if ($id != "") {
											echo ($value != "" ? $value : '---');
										}
									} ?>
								</td>
								<?php
								$count++;
							}
						} ?>
						<td style="font-weight: bold; text-align: center; vertical-align: middle;" id="total_100_<?= $st_count; ?>"><?= (!empty($total_100) ? $total_100 : (isset($student['LatestGradeDetail']['ResultEntryAssignment']['result']) ? $student['LatestGradeDetail']['ResultEntryAssignment']['result'] : '---')); ?></td>
						<?php
					}

					if ($view_only || $display_grade || $grade_submission_status['grade_submited']) { ?>
						<td style="font-weight: bold; text-align: center; vertical-align: middle;">
							<?php
							$latest_grade_detail = $student['LatestGradeDetail'];

							if ($display_grade && isset($student['GeneratedExamGrade'])) {
								echo $student['GeneratedExamGrade']['grade'];
							} else if (isset($student['MakeupExam']) && (empty($student['ExamGradeChange']) || $student['ExamGradeChange'][0]['created'] >= $latest_grade_detail['ExamGrade']['created'])) {
								if (isset($student['ExamGradeChange']) && !empty($student['ExamGradeChange'])) {
									if ($student['ExamGradeChange'][0]['department_approval'] == -1) {
										echo '<p class="rejected">';
									}
									echo $student['ExamGradeChange'][0]['grade'];
									if ($student['ExamGradeChange'][0]['department_approval'] == -1) {
										echo '</p>';
									}
								} else {
									echo '**';
								}
							} else if (!empty($latest_grade_detail['ExamGrade'])) {

								if ((!isset($latest_grade_detail['ExamGrade']['auto_ng_conversion']) || $latest_grade_detail['ExamGrade']['auto_ng_conversion'] == 0) && (!isset($latest_grade_detail['ExamGrade']['manual_ng_conversion']) || $latest_grade_detail['ExamGrade']['manual_ng_conversion'] == 0) && $latest_grade_detail['ExamGrade']['department_approval'] == -1) {
									echo '<p class="rejected">';
								}

								echo $latest_grade_detail['ExamGrade']['grade'];

								if ($latest_grade_detail['ExamGrade']['department_approval'] == -1) {
									echo '</p>';
								}

								if (strcasecmp($latest_grade_detail['type'], 'Change') == 0) {
									if ($latest_grade_detail['ExamGrade']['makeup_exam_id'] == null && $latest_grade_detail['ExamGrade']['makeup_exam_result'] != null) {
										echo ' (Supplementary)';
									} else if ($latest_grade_detail['ExamGrade']['makeup_exam_result'] != null) {
										echo ' (Makeup)';
									} else {
										echo ' (Change)';
									}
								}

								if (isset($latest_grade_detail['ResultEntryAssignment']) && !empty($latest_grade_detail['ResultEntryAssignment'])) {
									echo ' (Result Entry Assignment)';
								}

								if ((strpos($latest_grade_detail['ExamGrade']['registrar_reason'], 'backend') !== false) || $latest_grade_detail['ExamGrade']['registrar_reason'] == 'Via backend data entry interface') {
									echo ' (Backend Data Entry)';
								}
							} else {
								echo '**';
							} ?>
						</td>
						<?php
					} ?>
				</tr>
				<?php
			}
		} ?>
	</tbody>
</table>