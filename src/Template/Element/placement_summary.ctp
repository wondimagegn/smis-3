<?php
if (!empty($placementSummary) && $placementSummary['placementAlreadyRun']) { ?>
	<div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Auto Placement is already Run, Please view the placed students at Auto Placement Report.</div>
	<?php
} ?>
<hr>
<br>
<table cellpadding="0" cellspacing="0" class="table">
	<tbody>
		<?php 
		if (!empty($placementSummary)) {
			if ($placementSummary['round'] == 1) {
				$round = '1st';
			} elseif ($placementSummary['round'] == 2) {
				$round = '2nd';
			} elseif ($placementSummary['round'] == 3) {
				$round = '3rd';
			} else {
				$round = $placementSummary['round'] . 'th';
			} ?>
			<tr>
				<td colspan="2">
					<h6 class="fs14"><?= $placementSummary['targetStudentInUnit']; ?> has a total of <?= $placementSummary['totalStudentReadyForPlacement']; ?> elegible students for placement for <?= $placementSummary['academic_year']; ?> academic year, <?= $round; ?> round </h6>
				</td>
			</tr>
			<tr>
				<td style="background-color: white;">
					<table cellpadding="0" cellspacing="0" class="table">
						<tbody>
							<?php
							if (isset($placementSummary['PlacementRoundParticipant'])) { ?>
								<tr>
									<td colspan="6">
										<div class="font">Placement quota</div>
									</td>
								</tr>
								<tr>
									<th style="width:50%" class="vcenter">Unit</th>
									<th style="width:10%" class="center">Competitive Capacity</th>
									<th style="width:10%" class="center">Female Quota</th>
									<th style="width:10%" class="center">Regions Quota</th>
									<th style="width:10%" class="center">Disability Quota</th>
									<th style="width:10%" class="center">Total</th>
								</tr>
								<?php
								foreach ($placementSummary['PlacementRoundParticipant'] as $k => $v) { ?>
									<tr>
										<td class="vcenter"><?= $v['PlacementRoundParticipant']['name']; ?></td>
										<td class="center"><?= $v['PlacementRoundParticipant']['intake_capacity']; ?></td>
										<td class="center"><?= $v['PlacementRoundParticipant']['female_quota']; ?></td>
										<td class="center"><?= $v['PlacementRoundParticipant']['region_quota']; ?></td>
										<td class="center"><?= $v['PlacementRoundParticipant']['disability_quota']; ?></td>
										<td class="center"><?= ($v['PlacementRoundParticipant']['intake_capacity'] + $v['PlacementRoundParticipant']['female_quota'] + $v['PlacementRoundParticipant']['region_quota'] + $v['PlacementRoundParticipant']['disability_quota']); ?></td>
									</tr>
									<?php
								}
							} ?>
						</tbody>
					</table>
				</td>
				<td style="background-color: white;">
					<table cellpadding="0" cellspacing="0" class="table">
						<tbody>
							<?php
							if (isset($placementSummary['ResultWeight'])) { ?>
								<tr>
									<td colspan="5">
										<div class="font">Result Setting</div>
									</td>
								</tr>
								<tr>
									<th style="width:25%" class="vcenter">Type</th>
									<th style="width:20%" class="center">Percent</th>
								</tr>
								<?php
								foreach ($placementSummary['ResultWeight'] as $k => $v) {
									$type = '';
									if ($v['PlacementResultSetting']['result_type'] == "EHEECE_total_results") {
										$type = "Highschool/preparatory";
									} elseif ($v['PlacementResultSetting']['result_type'] == "freshman_result") {
										$type = "Freshman";
									} else {
										$type = $v['PlacementResultSetting']['result_type'];
									} ?>
									<tr>
										<td class="vcenter"><?= $type; ?></td>
										<td class="center"><?= $v['PlacementResultSetting']['percent']; ?></td>
									</tr>
									<?php
								}
							} ?>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="background-color: white;">
					<table cellpadding="0" cellspacing="0" class="table">
						<tbody>
							<?php
							if (isset($placementSummary['Preference'])) { ?>
								<tr>
									<td colspan="7">
										<div class="font">Preference Statistics</div>
									</td>
								</tr>
								<tr>
									<th style="width:40%" class="vcenter">Unit</th>
									<th style="width:10%" class="center">Preference Order</th>
									<th style="width:10%" class="center">Female</th>
									<th style="width:10%" class="center">Male</th>
									<th style="width:10%" class="center">Disability</th>
									<th style="width:10%" class="center">Developing Region</th>
									<th style="width:10%" class="center">Total</th>
								</tr>
								<?php
								foreach ($placementSummary['Preference'] as $k => $v) { ?>
									<tr>
										<td class="Vcenter"><?= $v['unit']; ?></td>
										<td class="center"><?= $v['preference_order']; ?></td>
										<td class="center"><?= $v['female']; ?></td>
										<td class="center"><?= $v['male']; ?></td>
										<td class="center"><?= $v['disability']; ?></td>
										<td class="center"><?= $v['developing_region']; ?></td>
										<td class="center"><?= $v['total']; ?></td>
									</tr>
									<?php
								}
							} ?>
						</tbody>
					</table>
				</td>
			</tr>
			<?php
		} else { ?>
			<tr>
				<td colspan="2" style="background-color: white;">
				<div class='error-box error-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no student prepared for Auto Placement for the selected seach critera.</div>
				</td>
			</tr>
			<?php
		} ?>
	</tbody>
</table>
<?php
if (isset($placementSummary) && !empty($placementSummary) && !$placementSummary['placementAlreadyRun']) { ?>
	<hr>
	<?= $this->Form->Submit('Run Placement', array('div' => false, 'name' => 'runAutomPlacement', 'id' => 'runAutomPlacement', 'class' => 'tiny radius button bg-blue')); ?>
	<?php
} ?>
