<?php
if (isset($studentEnrolmentHEMIS) && !empty($studentEnrolmentHEMIS)) { 
	foreach ($studentEnrolmentHEMIS as $programD => $list) {
		$headerExplode = explode('~', $programD); ?>
		<div style="overflow-x:auto;">
			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th>#</th>
						<th>Graduated</th>
						<th>Department</th>
						<th>Section</th>
						<th>Full Name</th>
						<th>Sex</th>
						<th>Region</th>
						<th>student_institution_id</td>
						<th>institution_code</td>
						<th>student_national_id</th>
						<th>academic_year</th>
						<th>academic_period</th>
						<th>academic_term</th>
						<th>campus_code</th>
						<th>program</th>
						<th>program_modality</th>
						<th>target_qualification</th>
						<th>year_level</th>
						<th>enrollment_type</th>
						<th>foreign_program</th>
						<th>economically_supported</th>
						<th>required_academic_periods</th>
						<th>required_credits</th>
						<th>current_registred_credits</th>
						<th>cumulative_registred_credits</th>
						<th>cumulative_completed_credits</th>
						<th>cumulative_gpa</th>
						<th>outgoing_exchange</th>
						<th>incoming_exchange</th>
						<th>exchange_country</th>
						<th>exchange_institution</th>
						<th>exchange_institution_lng</th>
						<th>sponsorship</th>
						<th>student_economical_status</th>
						<th>student_disability</th>
						<th>specially_gifted</th>
						<th>food_service_type</th>
						<th>dormitory_service_type</th>
						<th>cost_sharing_loan</th>
						<th>current_cost_sharing</th>
						<th>accumulated_cost_sharing</th>
						<th>settelment_type</th>
						<th>settelment_date</th>
						<!-- <th>total_academic_periods </td> -->
					</tr>
				</thead>
				<tbody>
					<?php
					$count = 0;
					//debug($list[0]); 
					foreach ($list as $ko => $val) {
						$studentTakenCreditsSemesters = ClassRegistry::init('StudentExamStatus')->getStudentTotalAccumulatedCreditsAndSemesterCount($val['id'], $this->request->data['Report']['acadamic_year'], $this->request->data['Report']['semester']); ?>
						<tr class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?php echo $val['id']; ?>">
							<td class="center"><?= ++$count; ?></td>
							<td class="center"><?= ($val['graduated'] == 1 ? 'Yes' : 'No'); ?></td>
							<td class="vcenter"><?= (isset($val['Department']) && isset($val['Department']['name']) ? $val['Department']['name'] :( isset($val['College']) && isset($val['College']['name']) ? $val['College']['name']: '---')); ?></td>
							<td class="vcenter"><?= (isset($val['Section']) ? $val['Section'] : '---'); ?></td>
							<td class="vcenter"><?= $val['first_name'] . ' ' . $val['middle_name'] . ' ' . $val['last_name']; ?></td>
							<td class="center"><?= ((strcasecmp(trim($val['gender']), 'male') == 0) ? 'M' : ((strcasecmp(trim($val['gender']), 'female') == 0) ? 'F' : trim($val['gender']))); ?></td>
							<td class="center"><?= (isset($val['Region']) && !empty($val['Region']) ? $val['Region'] : '---'); ?></td>
							<td class="center"><?= $val['studentnumber']; ?></td>
							<td class="center"><?= (isset($val['Department']) && isset($val['Department']['institution_code']) ? $val['Department']['institution_code'] :( isset($val['College']) && isset($val['College']['institution_code']) ? $val['College']['institution_code']: '---')); ?></td>
							<td class="center"><?= (isset($val['student_national_id']) ? $val['student_national_id'] : '---'); ?></td>
							<td class="center"><?= (str_replace('/', '', $val['academic_year'])); ?></td>
							<td class="center"><?= ((strcasecmp($val['semester'], 'I') == 0) ? 'S1':(strcasecmp($val['semester'], 'II') == 0 ? 'S2' : 'SS')); ?></td>
							<td class="center"><?= ((strcasecmp($val['semester'], 'III') == 0) ? 'T1':(strcasecmp($val['semester'], 'I') == 0 ? 'T3' : 'T4')); ?></td>
							<td class="center"><?= (isset($val['College']['Campus']) && isset($val['College']['Campus']['campus_code']) ? $val['College']['Campus']['campus_code']: '---'); ?></td>
							<td class="center"><!-- program --><?= (isset($val['StudyProgram']) ? $val['StudyProgram'] : ''); ?></td>
							<td class="center"><?= (isset($val['ProgramModality']) ? $val['ProgramModality'] : '---'); ?></td>
							<td class="center"><?= (isset($val['TargetQualification']) ? $val['TargetQualification'] : '---'); ?></td>
							<td class="center"><?= (isset($val['YearLevel']) ? $val['YearLevel'] : '---'); ?></td>
							<td class="center"><?= (isset($val['EnrollmentType']) ? $val['EnrollmentType'] : '---'); ?></td>
							<td class="center"><?= (isset($val['ForeignProgram']) ? $val['ForeignProgram'] : ''); ?></td>
							<td class="center"><!-- economically_supported -->N</td>
							<td class="center"><?= (isset($val['RequiredAcademicPeriods']) ? $val['RequiredAcademicPeriods'] : '---'); ?></td></td>
							<td class="center"><?= (isset($val['RequiredCredit']) ? $val['RequiredCredit'] : '<span style="color:red;"><b>N/A</b></span>'); ?></td>
							<td class="center"><?= (isset($val['CurrentRegistredCredit']) ? $val['CurrentRegistredCredit'] : '0'); ?></td>
							<td class="center"><?= (isset($val['CumulativeRegistredCredit']) ? $val['CumulativeRegistredCredit'] : '0'); ?></td>
							<td class="center"><?= ((isset($studentTakenCreditsSemesters[0][0]['totalAccumulatedCredits'])) ? (int) $studentTakenCreditsSemesters[0][0]['totalAccumulatedCredits'] : '---'); ?></td>
							<td class="center"><?= (isset($val['CumulativeGPA']) ? $val['CumulativeGPA'] : '0'); ?></td>
							<td class="center"><!-- outgoing_exchange -->N</td>
							<td class="center"><!-- incoming_exchange -->N</td>
							<td class="center"><!-- exchange_country --></td>
							<td class="center"><!-- exchange_institution --></td>
							<td class="center"><!-- exchange_institution_lng --></td>
							<td class="center"><!-- sponsorship --><?= (isset($val['Sponsorship']) ? $val['Sponsorship'] : ''); ?></td>
							<td class="center"><!-- student_economical_status --></td>
							<td class="center"><!-- student_disability --></td>
							<td class="center"><!-- specially_gifted -->N</td>
							<td class="center"><!-- food_service_type --><?= (isset($val['FoodServiceType']) ? $val['FoodServiceType'] : ''); ?></td>
							<td class="center"><!-- dormitory_service_type --><?= (isset($val['DormitoryServiceType']) ? $val['DormitoryServiceType'] : ''); ?></td>
							<td class="center"><!-- cost_sharing_loan --><?= (isset($val['CostSharingLoan']) ? $val['CostSharingLoan'] : ''); ?></td>
							<td class="center"><!-- current_cost_sharing --><?= (isset($val['CurrentCostSharing']) && $val['CostSharingLoan'] == 'Y' ? $val['CurrentCostSharing'] : ''); ?></td>
							<td class="center"><!-- accumulated_cost_sharing --><?= (isset($val['AccumulatedCostSharing']) && $val['CostSharingLoan'] == 'Y' ? $val['AccumulatedCostSharing'] : ''); ?></td>
							<td class="center"><!-- settelment_type --></td>
							<td class="center"><!-- settelment_date --></td>
							<!-- <td class="center"><?// ((isset($studentTakenCreditsSemesters[0][0]['totalSemesters'])) ? $studentTakenCreditsSemesters[0][0]['totalSemesters'] : '---'); ?></td> -->
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