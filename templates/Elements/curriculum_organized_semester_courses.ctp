<?php

$credit_type = '';

if (isset($student_academic_profile['Curriculum']['type_credit']) && !empty($student_academic_profile['Curriculum']['type_credit'])) {
	$crtype = explode('ECTS', $student_academic_profile['Curriculum']['type_credit']);
	if (count($crtype) == 2) {
		$credit_type = 'ECTS';
	} else {
		$credit_type = 'Credit';
	}
}

if (isset($student_academic_profile['Curriculum']['Course']) && !empty($student_academic_profile['Curriculum']['Course'])) {
	
	$curriculums = $student_academic_profile['Curriculum']['Course'];
	
	foreach ($curriculums as $year_level => $semester) {
		foreach ($semester as $sem => $course) { ?>
			<div style="overflow-x:auto;">
				<fieldset style="padding-top: 10px; padding-bottom: 15px;">
					<legend> &nbsp; &nbsp; <?= $year_level . ' Year, ' . ( $sem=='I'? '1st':($sem=='II'? '2nd':'3rd')) . ' Semester' ?> &nbsp; &nbsp; </legend>
					<table cellpadding="0" cellspacing="0" class="table">
						<thead>
							<tr>
								<td style="width:3%; text-align:center" class="center"> # </td>
								<td style="width:15%" class="vcenter">Course Code</td>
								<td style="width:22%" class="vcenter">Course Title</td>
								<td style="width:8%;" class="center"><?= (!empty($credit_type) ? $credit_type : 'Credit'); ?></td>
								<td style="width:20%;" class="center">Course Category</td>
								<td style="width:17%;" class="center">Grade Type</td>
								<td style="width:15%;" class="center">Prerequisite</td>
							</tr>
						</thead>
						<tbody>
							<?php
							$c_count = 1;
							foreach ($course as $index => $value) { ?>
								<tr>
									<td style="background: #fff; text-align:center;" class="center"><?= $c_count++; ?></td>
									<td style="background: #fff;" class="vcenter"><?= $value['course_code']; ?></td>
									<td style="background: #fff;" class="vcenter"><?= $value['course_title']; ?></td>
									<td style="background: #fff; text-align:center;" class="center"><?= $value['credit']; ?></td>
									<td style="background: #fff; text-align:center;" class="center"><?= (isset($value['CourseCategory']['name']) && !empty($value['CourseCategory']['name']) ? $value['CourseCategory']['name'] : 'N/A'); ?></td>
									<td style="background: #fff; text-align:center;" class="center"><?= $value['GradeType']['type']; ?></td>
									<td style="background: #fff;" class="center">
										<?php
										if (!empty($value['Prerequisite'])) {
											//echo '<span style="padding: 4px;">';
											echo '<ul style="text-align:left;">';
											foreach ($value['Prerequisite'] as $p => $pv) {
												echo '<li style="text-align:left;">' . $pv['PrerequisiteCourse']['course_title'] . ' (' . $pv['PrerequisiteCourse']['course_code'] . ')' . '</li>';
											}
											echo '</ul>';
											//echo '</span>';
										} else {
											echo 'None';
										}
										?>
									</td>
								</tr>
								<?php
							} ?> 
						</tbody>
					</table>
				</fieldset>
			</div>
			<?php
		}
	} 
} ?>