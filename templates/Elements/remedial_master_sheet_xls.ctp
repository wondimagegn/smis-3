<?php
header("Expires: " . gmdate("D,d M YH:i:s") . " GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=" . $filename . ".xls");
header("Content-Description: Exported as XLS"); ?>

<?php
if (isset($master_sheet) && count($master_sheet['students_and_grades'])) { ?>

	<table cellpadding="0" cellspacing="0" class="table-borderless">
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0" class="fs13 table">
					<tr>
						<td></td>
						<td style="width:70%; font-weight:bold">College: &nbsp; <?= $college_detail['name']; ?></td>
					</tr>
					<tr>
						<td></td>
						<td style="font-weight:bold;vertical-align: middle; text-align: left;">Department: &nbsp; <?= (!empty($department_detail['name']) && $department_detail['name'] != "" ? $department_detail['name'] : 'Freshman Program'); ?></td>
					</tr>
					<tr>
						<td></td>
						<td style="font-weight:bold; vertical-align: middle; text-align: left;">Program: &nbsp; <?= $program_detail['name']; ?></td>
					</tr>
					<tr>
						<td></td>
						<td style="font-weight:bold;vertical-align: middle; text-align: left;">Program Type: &nbsp; <?= $program_type_detail['name']; ?></td>
					</tr>
					<tr>
						<td></td>
						<td style="font-weight:bold;vertical-align: middle; text-align: left;">Section: &nbsp; <?= $section_detail['name']; ?></td>
					</tr>
					<tr>
						<td></td>
						<td style="font-weight:bold;vertical-align: middle; text-align: left;">Acdamic Year: &nbsp; <?= $academic_year; ?></td>
					</tr>
					<tr>
						<td></td>
						<td style="font-weight:bold;vertical-align: middle; text-align: left;">Semester: &nbsp; <?= $semester; ?></td>
					</tr>
				</table>
				<?php
				if (count($master_sheet['registered_courses']) > 0) { ?>
					<br>

					<b>Registered Courses</b>
					
					<table cellpadding="0" cellspacing="0" class="fs13 table">
						<thead>
							<tr>
								<th style="width:5%;vertical-align: middle; text-align: center;">#</th>
								<th style="width:55%;vertical-align: middle; text-align: left;">Course Title</th>
								<th style="width:20%;vertical-align: middle; text-align: center;">Course Code</th>
								<th style="width:20%;vertical-align: middle; text-align: center;">Credit</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$registered_and_add_course_count = 0;
							$registered_course_credit_sum = 0;
							foreach($master_sheet['registered_courses'] as $key => $registered_course) {
								$registered_and_add_course_count++;
								$registered_course_credit_sum += $registered_course['credit']; ?>
								<tr>
									<td style="vertical-align: middle; text-align: center;"><?= $registered_and_add_course_count; ?></td>
									<td style="vertical-align: middle; text-align: left;"><?= $registered_course['course_title']; ?></td>
									<td style="vertical-align: middle; text-align: center;"><?= $registered_course['course_code']; ?></td>
									<td style="vertical-align: middle; text-align: center;"><?= $registered_course['credit']; ?></td>
								</tr>
								<?php
							} ?>
						</tbody>
						<tfoot>
							<tr style="font-weight:bold">
								<td colspan="3" style=" vertical-align: middle; text-align:right">Total</td>
								<td style="vertical-align: middle; text-align: center;"><?= $registered_course_credit_sum; ?></td>
							</tr>
						</tfoot>
					</table>
					<?php
				} ?>
			</td>
		</tr>
	</table>
	<br>

	<?php $table_width = (count($master_sheet['registered_courses'])*10) + 85; ?>

	<table style="width:<?= ($table_width > 100 ? $table_width : 100); ?>%" cellpadding="0" cellspacing="0" class="fs13 table">
		<thead>
			<tr>
				<th rowspan="2" style="width:2%;vertical-align: middle;text-align: center; border-top:1px #000000 solid;border-bottom:1px #000000 solid;">#</th>
				<th rowspan="2" style="width:20%;vertical-align: middle; text-align: left; border-top:1px #000000 solid;border-bottom:1px #000000 solid;">Full Name</th>
				<th rowspan="2" style="width:8%;vertical-align: middle; text-align: center; border-top:1px #000000 solid;border-bottom:1px #000000 solid;">Student ID</th>
				<th rowspan="2" style="width:5%; vertical-align: middle; text-align: center;border-right:1px #000000 solid; border-top:1px #000000 solid;border-bottom:1px #000000 solid;">Sex</th>
				<?php
				$percent = 10;
				$last_percent = false;
				$total_percent = (count($master_sheet['registered_courses'])*10) + 86;
				
				if ($total_percent > 100) {
					//$percent = (100 - 86) / (count($master_sheet['registered_courses']) + count($master_sheet['added_courses']));
				} else if ($total_percent < 100) {
					$last_percent = 100 - $total_percent;
				}

				$registered_and_add_course_count = 0;

				if (!empty($master_sheet['registered_courses'])) {
					foreach ($master_sheet['registered_courses'] as $key => $registered_course) {
						$registered_and_add_course_count++; 
						$exmTypes = count($registered_course['exam_type']); ?>
						<th colspan=<?= $exmTypes + 3; ?> style="width:<?= $percent; ?>%; border-right:1px #000000 solid;vertical-align: middle; text-align: center;border-top:1px #000000 solid;border-bottom:1px #000000 solid;"><?= $registered_course['course_code']; //$registered_and_add_course_count; ?></th>
						<?php
					}
				} ?>
				
				<?php
				if ($last_percent) { ?>
					<th style="width:<?= $last_percent; ?>%;vertical-align: middle; text-align: center; border-top:1px #000000 solid;border-bottom:1px #000000 solid;">&nbsp;</th>
					<?php
				} ?>
			</tr>
			<tr>
				<?php
				if (!empty($master_sheet['registered_courses'])) {
					foreach ($master_sheet['registered_courses'] as $key => $registered_course) { 

						$exmTypes = count($registered_course['exam_type']);
						
						if ($exmTypes) {
							foreach ($registered_course['exam_type'] as $key => $exmty) { ?>
								<th style="width:<?= $percent/($exmTypes +3); ?>%;vertical-align: middle; text-align: center;border-bottom:1px #000000 solid;"><?= $exmty['exam_name'] .'('. $exmty['percent'] .')'; ?></th>
								<?php
							}  ?>
							<th style="width:<?= $percent/($exmTypes + 3); ?>%; border-left:1px #000000 solid;vertical-align: middle; text-align: center;border-bottom:1px #000000 solid;">30%</th>
							<th style="width:<?= $percent/($exmTypes + 3); ?>%; border-left:1px #000000 solid;vertical-align: middle; text-align: center;border-bottom:1px #000000 solid;">100%</th>
							<th style="width:<?= $percent/($exmTypes + 3); ?>%; border-left:1px #000000 solid; border-right:1px #000000 solid;vertical-align: middle; text-align: center;border-bottom:1px #000000 solid;">G</th>
							<?php

						} else { ?>
							<th style="width:<?= $percent/($exmTypes + 3); ?>%;vertical-align: middle; text-align: center;border-bottom:1px #000000 solid;">30%</th>
							<th style="width:<?= $percent/($exmTypes + 3); ?>%;vertical-align: middle; text-align: center;border-bottom:1px #000000 solid;">100%</th>
							<th style="width:<?= $percent/($exmTypes + 3); ?>%; border-right:1px #000000 solid;vertical-align: middle; text-align: center;border-bottom:1px #000000 solid;">G</th>
							<?php
						}
					}
				} ?>

				<?php
				if ($last_percent) { ?>
					<th style="vertical-align: middle; text-align: center;">&nbsp;</th>
					<?php
				} ?>
			</tr>
		</thead>
		<tbody>
			<?php
			$student_count = 0;
			foreach($master_sheet['students_and_grades'] as $key => $student) {
				$credit_hour_sum = 0;
				$gp_sum = 0;
				$student_count++; ?>
				<tr>
					<td style="border-left:1px #000000 solid;vertical-align: middle; text-align: center;"><?= $student_count; ?></td>
					<td style="border-left:1px #000000 solid;vertical-align: middle; text-align: left;"><?= $student['full_name']; ?></td>
					<td style="border-left:1px #000000 solid;vertical-align: middle; text-align: center;"><?= $student['studentnumber']; ?></td>
					<td style="border-left:1px #000000 solid; border-right:1px #000000 solid;vertical-align: middle; text-align: center;"><?= (strcasecmp(trim($student['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($student['gender']), 'female') == 0 ?'F' : trim($student['gender']))); ?></td>
					<?php
					if (!empty($master_sheet['registered_courses'])) {
						foreach($master_sheet['registered_courses'] as $key => $registered_course) {
							if ($student['courses']['r-'.$registered_course['id']]['registered'] == 1) {

								$thirtyPercent = 0;
								$hundredPercent = 0;
								$exmTypes2 = $exmTypes;
								$haveAssesment = 0;

								if (!empty($student['courses']['r-'.$registered_course['id']]['Assesment'])) {
									$exmTypes2 = $student['courses']['r-'.$registered_course['id']]['Assesment'];
									foreach ($student['courses']['r-'.$registered_course['id']]['Assesment'] as $asskey => $assvalue) {
										if (isset($assvalue['ExamResult']) && !empty($assvalue['ExamResult']['result'])) {
											$hundredPercent += $assvalue['ExamResult']['result'];
											if ($assvalue['ExamType']['percent'] < 50 && $thirtyPercent <= 30) {
												$thirtyPercent += $assvalue['ExamResult']['result'];
												$haveAssesment++;
											} else if (is_numeric($assvalue['ExamType']['percent']) && isset($assvalue['ExamResult']['result'])) {
												$haveAssesment++;
											}
											echo '<td style="vertical-align: middle; text-align: center;">'.$assvalue['ExamResult']['result'].'</td>';

										} else if (isset($assvalue['ExamType'])) {
											echo '<td style="vertical-align: middle; text-align: center;">--</td>';
										} else {
											echo '<td style="vertical-align: middle; text-align: center;">--</td>';
										} 
									} ?>

									<td style="border-left:1px #000000 solid;vertical-align: middle; text-align: center;"><?= (isset($thirtyPercent) && $haveAssesment > 0 ? $thirtyPercent : '--'); ?></td>
									<td style="border-left:1px #000000 solid;vertical-align: middle; text-align: center;"><?= (isset($hundredPercent) && $haveAssesment > 0 ? $hundredPercent : '--'); ?></td>
									<?php
								} else {
									if (isset($exmTypes2) && $exmTypes2) {

										for ($i=0; $i < $exmTypes2; $i++) { 
											echo '<td style="vertical-align: middle; text-align: center;">--</td>';
										} ?>

										<td style="border-left:1px #000000 solid;vertical-align: middle; text-align: center;">--</td>
										<td style="border-left:1px #000000 solid;vertical-align: middle; text-align: center;">--</td>
										<?php
									} else { ?>
										<td style="border-left:1px #000000 solid;vertical-align: middle; text-align: center;">--</td>
										<td style="border-left:1px #000000 solid;vertical-align: middle; text-align: center;">--</td>
										<?php
									}
								}
								
								if (isset($student['courses']['r-'.$registered_course['id']]['grade'])) {
									echo '<td style="border-left:1px #000000 solid; border-right:1px #000000 solid;vertical-align: middle; text-align: center;">'.$student['courses']['r-'.$registered_course['id']]['grade'].'</td>';
								} else {
									echo '<td style="border-left:1px #000000 solid; border-right:1px #000000 solid;vertical-align: middle; text-align: center;">'.($student['courses']['r-'.$registered_course['id']]['droped'] == 1 ? 'DP' : '**').'</td>';
								}
							} else {

								if (isset($exmTypes) && $exmTypes) {
									for ($i=0; $i < $exmTypes; $i++) { 
										echo '<td style="vertical-align: middle; text-align: center;">--</td>';
									}
								}

								echo '<td style="border-left:1px #000000 solid;vertical-align: middle; text-align: center;">&nbsp;</td>';
								echo '<td style="border-left:1px #000000 solid;vertical-align: middle; text-align: center;">&nbsp;</td>';
								echo '<td style="border-left:1px #000000 solid; border-right:1px #000000 solid;vertical-align: middle; text-align: center;">**</td>';

								//the student didn't register and there is nothing to display
							}
						}
					} 

					if ($last_percent) { ?>
						<td style="vertical-align: middle; text-align: center;">&nbsp;</td>
						<?php
					} ?>
				</tr>
				<?php
			} ?>
		</tbody>
	</table>
	<?php
}  ?>