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
if (isset($distributionStatistics['getDistributionStatsTeacherToStudents']) && !empty($distributionStatistics['getDistributionStatsTeacherToStudents'])) { ?>
	<h6><?= $headerLabel; ?></h6>
	<?php //echo $this->element('staffs/graph'); ?>
	<!-- <hr> -->

	<div style="overflow-x:auto;">
		<table cellpadding="0" cellspacing="0" class="table">
			<thead>
				<tr>
					<th class="center" style="width: 5%;">#</th>
					<th class="vcenter" style="width: 30%;">Department</th>
					<th class="center" style="width: 15%;">Type</th>
					<th class="center" style="width: 10%;">Number</th>
					<th class="center">Ratio</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$count = 0;
				foreach ($distributionStatistics['getDistributionStatsTeacherToStudents'] as $departmentNamee => $genderWithRank) { ?>
					<tr>
						<td rowspan="2" class="center"><?= ++$count; ?></td>
						<td rowspan="2" class="vcenter"><?= $departmentNamee; ?></td>
						<td class="center">Student</td>
						<td class="center"><?= (isset($genderWithRank['student']) && !empty($genderWithRank['student']) ? $genderWithRank['student'] : ''); ?></td>
						<td rowspan="2" class="center">
							<?php
							if ($genderWithRank['teacher'] > 0 && $genderWithRank['student'] > 0) {
								echo 'One Instructor to ' . (round($genderWithRank['student'] / $genderWithRank['teacher'])) . ' students ';
							} else if ($genderWithRank['teacher'] == 0 && $genderWithRank['student'] == 0) {
								echo 'No Instructor & Student is found';
							} else if ($genderWithRank['student'] == 0) {
								echo 'No Student is found';
							} else {
								echo 'No Instructor is found';
							} ?>
						</td>
					</tr>
					<tr>
						<td class="center">Teacher</td>
						<td class="center"><?= (isset($genderWithRank['teacher']) && !empty($genderWithRank['teacher']) ? $genderWithRank['teacher'] : ''); ?></td>
					</tr>
					<?php
				} ?>
			</tbody>
		</table>
	</div>
	<br>
	<?php
} ?>