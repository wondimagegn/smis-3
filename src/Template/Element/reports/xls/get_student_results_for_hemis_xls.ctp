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
if (isset($studentResultsHEMIS) && !empty($studentResultsHEMIS)) { 
	foreach ($studentResultsHEMIS as $programD => $list) {
		//$headerExplode = explode('~', $programD); ?>
		<div style="overflow-x:auto;">
			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th class="center">#</th>
						<th class="center">Graduated</th>
						<th class="center">College</th>
						<th class="center">Department</th>
						<th class="center">Full Name</th>
						<th class="center">Sex</th>
						<th class="center">Region</th>
						<th class="center">Student ID</th>
						<th class="center">institution_code</td>
						<th class="center">student_national_id</th>
						<th class="center">academic_year</th>
						<th class="center">academic_period</th>
						<th class="center">total_accumulated_credits</td>
						<th class="center">cgpa</th>
						<th class="center">total_academic_periods </td>
						<th class="center">result</th>
						<th class="center">transfer</th>
						<th class="center">gpa</th>
						<th class="center">digital_literacy_training</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$count = 0;
					foreach ($list as $ko => $val) {
						//debug($val);
						//debug($studentTakenCreditsSemesters); ?>
						<tr class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?php echo $val['id']; ?>">
							<td class="center"><?= ++$count; ?></td>
							<td class="center"><?= ($val['graduated'] == 1 ? 'Yes' : 'No'); ?></td>
							<td class="center"><?= (isset($val['College']['shortname']) ? $val['College']['shortname'] : '---'); ?></td>
							<td class="vcenter"><?= (isset($val['Department']['name']) ? $val['Department']['name'] : $val['College']['shortname'] . ' Pre/Fresh'); ?><!-- institution_code --></td>
							<td class="vcenter"><?= $val['first_name'] . ' ' . $val['middle_name'] . ' ' . $val['last_name']; ?></td>
							<td class="center"><?= ((strcasecmp(trim($val['gender']), 'male') == 0) ? 'M' : ((strcasecmp(trim($val['gender']), 'female') == 0) ? 'F' : trim($val['gender']))); ?></td>
							<td class="center"><?= (isset($val['Region']) && !empty($val['Region']) ? $val['Region'] : '---'); ?></td>
							<td class="center"><?= $val['studentnumber']; ?></td>
							<td class="center"><?= isset($val['Department']['institution_code']) ? $val['Department']['institution_code'] : $val['College']['institution_code']; ?><!-- institution_code --></td>
							<td class="vcenter"><?= (isset($val['student_national_id']) ? $val['student_national_id'] : '---'); ?></td>
							<td class="center"><?= (str_replace('/', '', $val['academic_year'])); ?></td>
							<td class="center"><?= ((strcasecmp($val['semester'], 'I') == 0) ? 'S1':(strcasecmp($val['semester'], 'II') == 0 ? 'S2' : 'SS')); ?></td>
							<td class="center"><?= ((isset($val['StudentTakenCreditsSemesters'][0][0]['totalAccumulatedCredits'])) ? $val['StudentTakenCreditsSemesters'][0][0]['totalAccumulatedCredits'] : '---'); ?></td>
							<td class="center"><?= $val['cgpa']; ?></td>
							<td class="center"><?= ((isset($val['StudentTakenCreditsSemesters'][0][0]['totalSemesters'])) ? $val['StudentTakenCreditsSemesters'][0][0]['totalSemesters'] : '---'); ?></td>
							<td class="center"><?= (!isset($val['academic_status_id']) ?  '---' : (($val['academic_status_id'] != 4) ? 'P' : 'F')); ?></td>
							<td class="center"><!-- transfer --></td>
							<td class="center"><?= $val['sgpa']; ?></td>
							<td class="center"><!-- digital_literacy_training --></td>
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