<?php
if (isset($studentGraduateHEMIS) && !empty($studentGraduateHEMIS)) {
	foreach ($studentGraduateHEMIS as $programD => $list) {
		$headerExplode = explode('~', $programD); ?>
		<?php //echo debug($headerExplode); ?>

		<!-- <div style="overflow-x:auto;">
			<table cellspacing="0" cellpading="0" class="table-borderless fs13">
				<tbody>
					<tr>
						<td>
							<span class="text-gray" style="font-weight: bold;">College:</span> &nbsp;&nbsp; <?php //echo $headerExplode[7]; ?>
						</td>
					</tr>
					<tr>
						<td>
							<span class="text-gray" style="font-weight: bold;">Department:</span> &nbsp;&nbsp; <?php //echo $headerExplode[6]; ?>
						</td>
					</tr>
					<tr>
						<td>
							<span class="text-gray" style="font-weight: bold;">Program:</span> &nbsp;&nbsp; <?php //echo $headerExplode[4]; ?>
						</td>
					</tr>
					<tr>
						<td>
							<span class="text-gray" style="font-weight: bold;">Program Type:</span> &nbsp;&nbsp; <?php //echo $headerExplode[5]; ?>
						</td>
					</tr>
					<tr>
						<td>
							<span class="text-gray" style="font-weight: bold;">Academic Year:</span> &nbsp;&nbsp; <?php //echo $headerExplode[0]; ?>
						</td>
					</tr>
					<tr>
						<td>
							<span class="text-gray" style="font-weight: bold;">Semester:</span> &nbsp;&nbsp; <?php //echo $headerExplode[1]; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div> -->
		
		<div style="overflow-x:auto;">
			<table style="width:100%" cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th class="center"> # </th>
						<th class="center">Graduation Date</th>
						<th class="center">minute_number</th>
						<th class="center">College</th>
						<th class="center">Department</th>
						<th class="center">Program</th>
						<th class="center">Program Type</th>
						<th class="venter">Full Name</th>
						<th class="center">Sex</th>
						<th class="center">Region</th>
						<th class="center">student_institution_id</th>
						<th class="center">institution_code</th>
						<th class="center">student_national_id</th>
						<th class="center">academic_year</th>
						<th class="center">academic_period</th>
						<th class="center">total_accumulated_credits</th>
						<th class="center">cgpa</th>
						<th class="center">total_academic_periods</th>
						<th class="center">exit_exam_score</th>
						<th class="center">employability_training</th>
						<th class="center">enterpreunership_training</th>
						<th class="center">graduation_date</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$count = 1;
					foreach ($list as $ko => $val) {
						//debug($val['GraduateList']['AccumulatedCreditsAndSemesterCount']);
						//$studentAccumulatedCreditsSemesters = ClassRegistry::init('StudentExamStatus')->getStudentTotalAccumulatedCreditsAndSemesterCountGraduated($val['Student']['id']);
						//debug($studentAccumulatedCreditsSemesters);
						//$studentTakenCreditsSum = ClassRegistry::init('StudentExamStatus')->getStudentTakenCreditsForHemis($val['Student']['id'], 1); 
						?>
						<tr class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?= $val['Student']['id']; ?>">
							<td class="center"><?= $count++; ?></td>
							<td class="center"><?= ((isset($val['GraduateList']['graduate_date']) && !empty($val['GraduateList']['graduate_date'])) ? $this->Time->format("M j, Y", $val['GraduateList']['graduate_date'], NULL, NULL) : '---'); ?></td>
							<td class="center"><?= ((isset($val['GraduateList']['minute_number']) && !empty($val['GraduateList']['minute_number'])) ? $val['GraduateList']['minute_number'] : '---'); ?></td>
							<td class="center"><?= $val['Student']['College']['shortname'] ?></td>
							<td class="vcenter"><?= $val['Student']['Department']['name'] ?></td>
							<td class="center"><?= $val['Student']['Program']['name'] ?></td>
							<td class="center"><?= $val['Student']['ProgramType']['name'] ?></td>
							<td class="vcenter"><?= $val['Student']['full_name'] ?></td>
							<td class="center"><?= ((strcasecmp(trim($val['Student']['gender']), 'male') == 0) ? 'M' : ((strcasecmp(trim($val['Student']['gender']), 'female') == 0) ? 'F' : trim($val['Student']['gender']))); ?></td>
							<td class="center"><?= (isset($val['Student']['Region']['name']) ? $val['Student']['Region']['name'] : '---'); ?></td>
							<td class="center"><?= $val['Student']['studentnumber']; ?></td>
							<td class="center"><?= $val['Student']['Department']['institution_code']; ?></td>
							<td class="center"><?= (isset($val['Student']['student_national_id']) ? $val['Student']['student_national_id'] : '---'); ?></td>
							<td class="center"><?= (str_replace('/', '', $val['GraduateList']['academic_year'])); ?></td>
							<td class="center"><?= (strcasecmp($val['GraduateList']['semester'], 'I') == 0 ? 'S1' : (strcasecmp($val['GraduateList']['semester'], 'II') == 0 ? 'S2' : 'SS')); ?></td>
							<!-- <td class="center"><?php //echo ((isset($studentTakenCreditsSum['credit_sum']) && !empty($studentTakenCreditsSum['credit_sum'])) ? $studentTakenCreditsSum['credit_sum'] : ((isset($studentAccumulatedCreditsSemesters['totalAccumulatedCredits']) && !empty($studentAccumulatedCreditsSemesters['totalAccumulatedCredits'])) ? $studentAccumulatedCreditsSemesters['totalAccumulatedCredits'] : '---')); ?></td> -->
							<td class="center"><?= ((isset($val['GraduateList']['AccumulatedCreditsAndSemesterCount']['TotalAccumulatedCredits']) && !empty($val['GraduateList']['AccumulatedCreditsAndSemesterCount']['TotalAccumulatedCredits'])) ? $val['GraduateList']['AccumulatedCreditsAndSemesterCount']['TotalAccumulatedCredits'] : '---'/* ((isset($studentAccumulatedCreditsSemesters['totalAccumulatedCredits']) && !empty($studentAccumulatedCreditsSemesters['totalAccumulatedCredits'])) ? $studentAccumulatedCreditsSemesters['totalAccumulatedCredits'] : '---' )*/); ?></td>
							<td class="center"><?= ((isset($val['Student']['StudentExamStatus'][0]['cgpa']) ? $val['Student']['StudentExamStatus'][0]['cgpa'] : '---')); ?></td>
							<!-- <td class="center"><?php //echo ((isset($studentAccumulatedCreditsSemesters['totalSemesters']) && !empty($studentAccumulatedCreditsSemesters['totalSemesters'])) ? $studentAccumulatedCreditsSemesters['totalSemesters'] : '---'); ?></td> -->
							<td class="center"><?= ((isset($val['GraduateList']['AccumulatedCreditsAndSemesterCount']['TotalAcademicPeriods']) && !empty($val['GraduateList']['AccumulatedCreditsAndSemesterCount']['TotalAcademicPeriods'])) ? $val['GraduateList']['AccumulatedCreditsAndSemesterCount']['TotalAcademicPeriods'] : '---'); ?></td>
							<td class="center"><?= ((isset($val['Student']['ExitExam'][0]['result']) && !empty($val['Student']['ExitExam'][0]['result'])) ? $val['Student']['ExitExam'][0]['result'] : ''); ?></td>
							<td class="center"><!-- employability_training --></td>
							<td class="center"><!-- enterpreunership_training --></td>
							<td class="center"><?= ((isset($val['GraduateList']['graduate_date']) && !empty($val['GraduateList']['graduate_date'])) ? $val['GraduateList']['graduate_date'] : '---'); ?></td>
							
						</tr>
						<?php
					} ?>
				</tbody>
			</table>
		</div>
		<br>
		<?php
	}
} ?>