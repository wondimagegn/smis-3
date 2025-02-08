<?php
if (isset($distributionStatistics['distributionStatsTeachersByAcademicRank']) && !empty($distributionStatistics['distributionStatsTeachersByAcademicRank'])) { ?>
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
					<th class="center" colspan="<?= count($positions); ?>">Position</th>
				</tr>
				<tr>
					<th class="center">&nbsp;</th>
					<th class="center">&nbsp;</th>
					<th class="center">&nbsp;</th>
					<?php
					if (!empty($positions) && count($positions)) {
						foreach ($positions as $sk => $svalue) { ?>
							<th class="center"><?= $svalue; ?></th>
							<?php
						}
					} ?>
				</tr>
			</thead>
			<tbody>
				<?php
				$count = 0;
				foreach ($distributionStatistics['distributionStatsTeachersByAcademicRank'] as $departmentNamee => $genderWithRank) { ?>
					<tr>
						<td class="center"><?= ++$count; ?></td>
						<td class="vcenter"><?= $departmentNamee; ?></td>
						<td class="vcenter">Male</td>
						<?php
						foreach ($genderWithRank['male'] as $sk => $svalue) { ?>
							<td class="center"><?= (!empty($svalue) ? $svalue : ''); ?></td>
							<?php
						} ?>
					</tr>
					<tr>
						<td class="center">&nbsp;</td>
						<td class="center">&nbsp;</td>
						<td class="vcenter">Female</td>
						<?php
						foreach ($genderWithRank['female'] as $sk => $svalue) { ?>
							<td class="center"><?= (!empty($svalue) ? $svalue : ''); ?></td>
							<?php
						} ?>
					</tr>
					<?php
				} ?>
			</tbody>
		</table>
	</div>
	<br>
	<?php
} ?>