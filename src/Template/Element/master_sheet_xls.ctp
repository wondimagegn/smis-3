<?php
header("Expires: " . gmdate("D,d M YH:i:s") . " GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=" . $filename . ".xls");
header("Content-Description: Exported as XLS"); ?>

<style>
	.bordering {
		border-left: 1px #cccccc solid;
		border-right: 1px #cccccc solid;
	}

	.bordering2 {
		border-left: 1px #000000 solid;
		border-right: 1px #000000 solid;
	}

	.courses_table tr td,
	.courses_table tr th {
		padding: 1px
	}
</style>

<?php
if (isset($master_sheet) && !empty($master_sheet)) { ?>

	<table>
		<tr>
			<td style="width:40%">
				<table class="fs13">
					<tr>
						<td style="width:30%"><?= (isset($college_detail['type']) ? $college_detail['type'] : 'College'); ?>:</td>
						<td style="width:70%; font-weight:bold"><?= $college_detail['name']; ?></td>
					</tr>
					<tr>
						<td>Program:</td>
						<td style="font-weight:bold"><?= $program_detail['name']; ?></td>
					</tr>
					<tr>
						<td>Program Type:</td>
						<td style="font-weight:bold"><?= $program_type_detail['name']; ?></td>
					</tr>
					<tr>
						<td><?= (isset($department_detail['type']) ? $department_detail['type'] : 'Department'); ?>:</td>
						<td style="font-weight:bold"><?= (isset($department_detail['name']) && !empty($department_detail['name']) ? $department_detail['name'] : ( $program_detail['id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Freshman Program')); ?></td>
					</tr>
					<tr>
						<td>Section:</td>
						<td style="font-weight:bold"><?= $section_detail['name']; ?></td>
					</tr>
					<tr>
						<td>Acdamic Year:</td>
						<td style="font-weight:bold"><?= $academic_year; ?></td>
					</tr>
					<tr>
						<td>Semester:</td>
						<td style="font-weight:bold"><?= $semester; ?></td>
					</tr>
				</table>
			</td>

			<td style="width:60%">
				<?php
				if (count($master_sheet['registered_courses']) > 0) { ?>
					<div style="font-weight:bold; background-color:#cccccc; padding:0px; font-size:14px">Registered Courses</div>
					<table class="courses_table">
						<tr>
							<th style="width:5%">No</th>
							<th style="width:55%">Course Title</th>
							<th style="width:20%">Course Code</th>
							<th style="width:20%">Cr. Hr.</th>
						</tr>

						<?php
						$registered_and_add_course_count = 0;
						$registered_course_credit_sum = 0;

						foreach ($master_sheet['registered_courses'] as $key => $registered_course) {
							$registered_and_add_course_count++;
							$registered_course_credit_sum += $registered_course['credit']; ?>
							<tr>
								<td><?= $registered_and_add_course_count; ?></td>
								<td><?= $registered_course['course_title']; ?></td>
								<td><?= $registered_course['course_code']; ?></td>
								<td><?= $registered_course['credit']; ?></td>
							</tr>
							<?php
						} ?>
						<tr style="font-weight:bold">
							<td colspan="3" style="text-align:right">Total</td>
							<td><?= $registered_course_credit_sum; ?></td>
						</tr>
					</table>
					<?php
				}

				if (count($master_sheet['added_courses']) > 0) { ?>
					<div style="font-weight:bold; background-color:#cccccc; padding:0px; font-size:14px">Add Courses</div>
					<table class="courses_table">
						<tr>
							<th style="width:5%">No</th>
							<th style="width:55%">Course Title</th>
							<th style="width:20%">Course Code</th>
							<th style="width:20%">Cr. Hr.</th>
						</tr>

						<?php
						$added_course_credit_sum = 0;
						foreach ($master_sheet['added_courses'] as $key => $added_course) {
							$registered_and_add_course_count++;
							$added_course_credit_sum += $added_course['credit']; ?>
							<tr>
								<td><?= $registered_and_add_course_count; ?></td>
								<td><?= $added_course['course_title']; ?></td>
								<td><?= $added_course['course_code']; ?></td>
								<td><?= $added_course['credit']; ?></td>
							</tr>
							<?php
						} ?>

						<tr style="font-weight:bold">
							<td colspan="3" style="text-align:right">Total</td>
							<td><?= $added_course_credit_sum; ?></td>
						</tr>
					</table>
					<?php
				} ?>
			</td>
		</tr>
	</table>

	<?php $table_width = (count($master_sheet['registered_courses']) * 10) + (count($master_sheet['added_courses']) * 10) + 86; ?>
	
	<style>
		table, th, td {
		border: 1px solid black;
		border-collapse: collapse;
		}
	</style>

	<table style="width:<?= ($table_width > 100 ? $table_width : 100); ?>%" cellpadding="1" cellspacing="1">
		<thead>
		<tr>
			<th rowspan="2" style="vertical-align:bottom; width:2%">No</th>
			<th rowspan="2" style="vertical-align:bottom; width:18%">Full Name</th>
			<th rowspan="2" style="vertical-align:bottom; width:8%">ID No</th>
			<th rowspan="2" style="vertical-align:bottom; width:3%">Sex</th>
			
			<?php
			$percent = 10;
			$last_percent = false;
			$total_percent = (count($master_sheet['registered_courses']) * 10) + (count($master_sheet['added_courses']) * 10) + 86;
			
			if ($total_percent > 100) {
				//$percent = (100 - 86) / (count($master_sheet['registered_courses']) + count($master_sheet['added_courses']));
			} else if ($total_percent < 100) {
				$last_percent = 100 - $total_percent;
			}

			$registered_and_add_course_count = 0;
			if (!empty($master_sheet['registered_courses'])) {
				foreach ($master_sheet['registered_courses'] as $key => $registered_course) {
					$registered_and_add_course_count++; ?>
					<th colspan="2" style="width:<?= $percent; ?>%; text-align:center" class="bordering2"><?= $registered_and_add_course_count; ?></th>
					<?php
				}
			}

			if (!empty($master_sheet['added_courses'])) {
				foreach ($master_sheet['added_courses'] as $key => $added_course) {
					$registered_and_add_course_count++; ?>
					<th colspan="2" style="width:<?= $percent; ?>%; text-align:center;"><?= $registered_and_add_course_count; ?></th>
					<?php
				} 
			} ?>

			<th colspan="3" style="text-align:center; width:15%" class="bordering2">Semester</th>
			<th colspan="3" style="text-align:center; width:15%" class="bordering2">Previous</th>
			<th colspan="3" style="text-align:center; width:15%" class="bordering2">Cumulative</th>
			<th rowspan="2" style="text-align:center; vertical-align:bottom; width:10%" class="bordering2">Status</th>
			
			<?php
			if ($last_percent) { ?>
				<th style="width:<?= $last_percent; ?>%;">&nbsp;</th>
				<?php
			} ?>
		</tr>
		<tr>
			<?php
			if (!empty($master_sheet['registered_courses'])) {
				foreach ($master_sheet['registered_courses'] as $key => $registered_course) { ?>
					<th style="width:<?= $percent / 2; ?>%; border-left:1px #000000 solid; border-right:1px #000000 solid">G</th>
					<th style="width:<?= $percent / 2; ?>%; border-left:1px #000000 solid; border-right:1px #000000 solid">GP</th>
					<?php
				}
			}

			if (!empty($master_sheet['added_courses'])) {
				foreach ($master_sheet['added_courses'] as $key => $added_course) { ?>
					<th style="width:<?= $percent / 2; ?>%; border-left:1px #000000 solid; border-right:1px #000000 solid">G</th>
					<th style="width:<?= $percent / 2; ?>%; border-left:1px #000000 solid; border-right:1px #000000 solid">GP</th>
					<?php
				} 
			} ?>

			<th style="width:5%" class="bordering2">CH</th>
			<th style="width:5%" class="bordering2">GP</th>
			<th style="width:5%" class="bordering2">SGPA</th>
			<th style="width:5%" class="bordering2">CH</th>
			<th style="width:5%" class="bordering2">GP</th>
			<th style="width:5%" class="bordering2">CGPA</th>
			<th style="width:5%" class="bordering2">CH</th>
			<th style="width:5%" class="bordering2">GP</th>
			<th style="width:5%" class="bordering2">CGPA</th>

			<?php
			if ($last_percent) {?>
				<th>&nbsp;</th>
				<?php
			} ?>
		</tr>
		</thead>
		<tbody>
			<?php
			$student_count = 0;
			if (!empty($master_sheet['students_and_grades'])) {
				foreach ($master_sheet['students_and_grades'] as $key => $student) {
					$credit_hour_sum = 0;
					$gp_sum = 0;
					$student_count++; ?>
					<tr>
						<td>&nbsp;<?= $student_count; ?>&nbsp;</td>
						<td>&nbsp;<?= $student['full_name']; ?>&nbsp;</td>
						<td>&nbsp;<?= $student['studentnumber']; ?>&nbsp;</td>
						<td><?= (strcasecmp($student['gender'], 'male') == 0 ? 'M' : 'F'); ?></td>
						
						<?php
						if (!empty($master_sheet['registered_courses'])) {
							foreach ($master_sheet['registered_courses'] as $key => $registered_course) {
								if ($student['courses']['r-' . $registered_course['id']]['registered'] == 1) {
									if (isset($student['courses']['r-' . $registered_course['id']]['grade'])) { ?>
										<td class="bordering"><?= $student['courses']['r-' . $registered_course['id']]['grade']; ?></td>
										<td class="bordering">
											<?php
											if (isset($student['courses']['r-' . $registered_course['id']]['point_value'])) {
												echo number_format(($student['courses']['r-' . $registered_course['id']]['credit'] * $student['courses']['r-' . $registered_course['id']]['point_value']), 2, '.', '');
												$gp_sum += ($student['courses']['r-' . $registered_course['id']]['credit'] * $student['courses']['r-' . $registered_course['id']]['point_value']);
											} ?>
										</td>
										<?php
									} else { ?>
										<td class="bordering"><?= ($student['courses']['r-' . $registered_course['id']]['droped'] == 1 ? 'DR' : '**'); ?></td>
										<td class="bordering">&nbsp;</td>
										<?php
									}

									if ($student['courses']['r-' . $registered_course['id']]['droped'] == 0) {
										$credit_hour_sum += $student['courses']['r-' . $registered_course['id']]['credit'];
									}

								} else { ?>
									<td class="bordering">---</td>
									<td class="bordering">&nbsp;</td>
									<!-- the student didn't register and there is nothing to display -->
									<?php
								}
							}
						}

						if (!empty($master_sheet['added_courses'])) {
							foreach ($master_sheet['added_courses'] as $key => $added_course) {
								if ($student['courses']['a-' . $added_course['id']]['added'] == 1) {
									if (isset($student['courses']['a-' . $added_course['id']]['grade'])) { ?>
										<td class="bordering"><?= $student['courses']['a-' . $added_course['id']]['grade']; ?></td>
										<td class="bordering">
											<?php
											if (isset($student['courses']['a-' . $added_course['id']]['point_value'])) {
												echo number_format(($student['courses']['a-' . $added_course['id']]['credit'] * $student['courses']['a-' . $added_course['id']]['point_value']), 2, '.', '');
												$gp_sum += ($student['courses']['a-' . $added_course['id']]['credit'] * $student['courses']['a-' . $added_course['id']]['point_value']);
											} ?>
										</td>
										<?php
									} else { ?>
										<td class="bordering">**</td>
										<td class="bordering">&nbsp;</td>
										<?php
									}
									$credit_hour_sum += $student['courses']['a-' . $added_course['id']]['credit'];
								} else { ?>
									<td class="bordering">---</td>
									<td class="bordering">&nbsp;</td>
									<?php
								}
							}
						} ?>

						<td class="bordering"><?= (isset($student['StudentExamStatus']) && !empty($student['StudentExamStatus']) ? $student['StudentExamStatus']['credit_hour_sum'] : '---'); ?></td>
						<td class="bordering"><?= (isset($student['StudentExamStatus']) && !empty($student['StudentExamStatus']) ? $student['StudentExamStatus']['grade_point_sum'] : '---'); ?></td>
						<td class="bordering"><?= (isset($student['StudentExamStatus']) && !empty($student['StudentExamStatus']) ? $student['StudentExamStatus']['sgpa'] : '---'); ?></td>
						<td class="bordering"><?= (isset($student['PreviousStudentExamStatus']) && !empty($student['PreviousStudentExamStatus']) ? $student['PreviousStudentExamStatus']['previous_credit_hour_sum'] : '---'); ?></td>
						<td class="bordering"><?= (isset($student['PreviousStudentExamStatus']) && !empty($student['PreviousStudentExamStatus']) ? $student['PreviousStudentExamStatus']['previous_grade_point_sum'] : '---'); ?></td>
						<td class="bordering"><?= (isset($student['PreviousStudentExamStatus']) && !empty($student['PreviousStudentExamStatus']) ? $student['PreviousStudentExamStatus']['cgpa'] : '---'); ?></td>
						<td class="bordering">
							<?php
							if (isset($student['StudentExamStatus']) && !empty($student['StudentExamStatus']) && isset($student['PreviousStudentExamStatus']) && !empty($student['PreviousStudentExamStatus'])) {
								echo (($student['StudentExamStatus']['credit_hour_sum'] + $student['PreviousStudentExamStatus']['previous_credit_hour_sum']) - $student['deduct_credit']);
							} else if (isset($student['StudentExamStatus']) && !empty($student['StudentExamStatus'])) {
								echo $student['StudentExamStatus']['credit_hour_sum'];
							} else if (isset($student['PreviousStudentExamStatus']) && !empty($student['PreviousStudentExamStatus'])) {
								echo $student['PreviousStudentExamStatus']['previous_credit_hour_sum'];
							} else {
								echo '---';
							} ?>
						</td>
						<td class="bordering">
							<?php
							if (isset($student['StudentExamStatus']) && !empty($student['StudentExamStatus']) && isset($student['PreviousStudentExamStatus']) && !empty($student['PreviousStudentExamStatus'])) {
								echo (($student['StudentExamStatus']['grade_point_sum'] + $student['PreviousStudentExamStatus']['previous_grade_point_sum']) - $student['deduct_gp']);
							} else if (isset($student['StudentExamStatus']) && !empty($student['StudentExamStatus'])) {
								echo $student['StudentExamStatus']['grade_point_sum'];
							} else if (isset($student['PreviousStudentExamStatus']) && !empty($student['PreviousStudentExamStatus'])) {
								echo $student['PreviousStudentExamStatus']['previous_grade_point_sum'];
							} else {
								echo '---';
							} ?>
						</td>
						<td class="bordering"><?= (isset($student['StudentExamStatus']) && !empty($student['StudentExamStatus']) ? $student['StudentExamStatus']['cgpa'] : '---'); ?></td>
						<td><?= (isset($student['AcademicStatus']) && !empty($student['AcademicStatus']['id']) ? $student['AcademicStatus']['name'] : '---'); ?></td>
						<?php
						if ($last_percent) {?>
							<td>&nbsp;</td>
							<?php
						} ?>
					</tr>
					<?php
				} 
			} ?>
			</tbody>
	</table>
	<?php
} ?>