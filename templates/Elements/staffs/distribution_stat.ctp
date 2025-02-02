<?php
if (isset($distributionStatistics['distributionStatsTeachersByGender']) && !empty($distributionStatistics['distributionStatsTeachersByGender'])) { ?>
	<!-- <h6><?php //echo $headerLabel; ?></h6> -->
	<?= $this->element('staffs/graph'); ?>
	<hr>
	<div style="overflow-x:auto;">
		<table cellpadding="0" cellspacing="0" class="table">
			<thead>
				<tr>
					<th class="center" style="width: 5%;">#</th>
					<th class="vcenter" style="width: 30%;">Department</th>
					<th class="vcenter" style="width: 10%;">Gender</th>
					<th class="center" style="width: 10%;">Number</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$count = 0;
				foreach ($distributionStatistics['distributionStatsTeachersByGender'] as $departmentName => $yll) { ?>
					<tr>
						<td class="center"><?= ++$count; ?></td>
						<td class="vcenter"><?= $departmentName; ?></td>
						<td class="vcenter">Male</td>
						<td class="center"><?= (isset($yll['male']) && !empty($yll['male']) ? $yll['male'] : ''); ?></td>
					</tr>
					<tr>
						<td class="center">&nbsp;</td>
						<td class="center">&nbsp;</td>
						<td class="vcenter">Female</td>
						<td class="center"><?= (isset($yll['female']) && !empty($yll['female']) ? $yll['female'] : ''); ?></td>
					</tr>
					<?php
				} ?>
			</tbody>
		</table>
	</div>
	<br>
	<?php
} ?>