<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-vcard" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Student Preference List'); ?></span>
		</div>

		<a class="close-reveal-modal">&#215;</a>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;">
					<hr>
					<?php 
					if (!empty($studentList)) { ?>
						<?php 
						//debug($studentList);
						$disp =  $studentList = array_values($studentList);
						$lastEntry = array_pop($disp);
						//debug($lastEntry);
						?>
						<fieldset>
							<div class="large-6 columns">
								<strong class="fs14">
									Name: &nbsp; <?= $studentList[0]['AcceptedStudent']['full_name']; ?> <br>
									Student ID: &nbsp; <?= $studentList[0]['AcceptedStudent']['studentnumber']; ?> <br>
									Sex: &nbsp; <?= (ucfirst(strtolower(trim($studentList[0]['AcceptedStudent']['sex'])))); ?> <br>
									ACY: &nbsp; <?= $studentList[0]['PlacementSetting']['academic_year']; ?> <br>
									Roud: &nbsp; <?= $studentList[0]['PlacementSetting']['round']; ?> <br>
									Assigned to: &nbsp; <?= (isset($assigned) && !empty($assigned) ? $assigned :'In progress...'); ?> <br> <br>
								</strong>
							</div>
							<div class="large-6 columns">
								<strong class="fs14">
									EHEECE: &nbsp; <?= (isset($studentList[0]['AcceptedStudent']['EHEECE_total_results']) && $studentList[0]['AcceptedStudent']['EHEECE_total_results'] > 100 ? $studentList[0]['AcceptedStudent']['EHEECE_total_results'] : 'N/A'); ?> <br>
									<?= ($prepararoryResultSet == 1 && $preparatoryPercent > 0  ? 'Prepartory: &nbsp;'. $prepartory .'<br>' : ''); ?>
									<?= ($freshmanResultSet == 1 && $freshmanPercent > 0  ? 'Freshman: &nbsp;'. $freshman .'<br>' : ''); ?> 
									<?= ($entranceResultSet == 1 && $entrancePercent > 0  ? 'Entrance: &nbsp;'. $entrance .'<br>' : ''); ?>
									<?php
									$affirmative_point = 0;
									if (strcasecmp(trim($studentList[0]['AcceptedStudent']['sex']), 'female') == 0 || strcasecmp(trim($studentList[0]['AcceptedStudent']['sex']), 'f') == 0) {
										$affirmative_point = DEFAULT_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT; 
									} ?>
									<?= ($affirmative_point > 0  ? 'Affirmative Point: &nbsp;'. $affirmative_point .'<br>' : ''); ?>
									<?= ($entranceResultSet == 1 && $prepararoryResultSet == 1 && $freshmanResultSet == 1 && isset($entrance) && isset($prepartory) && isset($freshman) ? 'Total Weight: &nbsp;' . ($entrance + $prepartory + $freshman + $affirmative_point) : ($prepararoryResultSet == 1 && $freshmanResultSet == 1 && isset($prepartory) && isset($freshman) ? 'Total Weight: &nbsp;' . ($prepartory + $freshman + $affirmative_point) : ($freshmanResultSet == 1 && isset($freshman) ? 'Total Weight: &nbsp;' . ($freshman + $affirmative_point): ''))).'<br>'; ?>
								</strong>
							</div>
							<div class="large-12 columns">
								<hr>
								<fieldset>
									<legend> &nbsp; &nbsp; <strong class="fs14">Placement Settings</strong> &nbsp; &nbsp;</legend>
									<ol>
										<li><h6 class="fs14 text-gray"><?= ($freshmanResultSet == 1 && $freshmanPercent > 0 ? 'Freshman Result is used in this round and taken out of ' . $freshmanPercent . '% with the maximum result out of ' . $freshmanMaxResultDB. '.' : '<span style="color: red;">Freshman Result is not used in this round.</span>'); ?></h6></li>
										<li><h6 class="fs14 text-gray"><?= ($entranceResultSet == 1 && $entrancePercent > 0 ? 'Department Entrance Result is used in this round and taken out of ' . $entrancePercent . '% with the maximum result out of ' . $entranceMaxResultDB. '.' : '<span class="on-process">Department Entrance Result is not used in this round.</span>'); ?></h6></li>
										<li><h6 class="fs14 text-gray"><?= ($prepararoryResultSet == 1 && $preparatoryPercent > 0 ? 'Preparatory (EHEECE Total) Result is used in this round and taken out of ' . $preparatoryPercent . '% with the maximum result out of ' . $prepMaxResultDB. '.' : '<span class="on-process">Preparatory (EHEECE Total) Result is not used in this round.</span>'); ?></h6></li>
									</ol>
								</fieldset>
							</div>
						</fieldset>
						<hr>

						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<th data-hide="phone,tablet" class="center">#</th>
										<th data-toggle="true" class="vcenter">Preference</th>
										<th class="center">Order</th>
										<th class="center">ACY</th>
										<th class="center">Round</th>
										<!-- <th data-hide="phone,tablet" class="center">Freshman</th>
										<th data-hide="phone,tablet" class="center">Entrance</th> -->
										<th data-hide="phone,tablet" class="vcenter">Assigned To</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 1;
									foreach ($studentList as $pk => $pv) { ?>
										<tr>
											<td class="center"><?= $count++; ?></td>
											<td class="vcenter"><?= $pv['PlacementSetting']['preference_name']; ?></td>
											<td class="center"><?= $pv['PlacementSetting']['preference_order']; ?></td>
											<td class="center"><?= $pv['PlacementSetting']['academic_year']; ?></td>
											<td class="center"><?= $pv['PlacementSetting']['round']; ?></td>
											<!-- <td class="center"><?php //echo (isset($pv['PlacementSetting']['freshman']) ? $pv['PlacementSetting']['freshman'] : ''); ?></td>
											<td class="center"><?php //echo (isset($pv['PlacementSetting']['entrance']) ? $pv['PlacementSetting']['entrance'] : ''); ?></td> -->
											<td class="vcenter"><?= (isset($pv['Assigned']) ? $pv['Assigned'] : ''); ?></td>
										</tr>
										<?php
									} ?>
								</tbody>
							</table>
						</div>
						<?php 
					} else { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no department preference filled by <?= (isset($studentBasic['Student']['full_name']) ? $studentBasic['Student']['full_name'] . ' (' . $studentBasic['Student']['studentnumber']. ') ' : 'the selected student'); ?>.</div>
						<?php 
					} ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- <a class="close-reveal-modal">&#215;</a> -->