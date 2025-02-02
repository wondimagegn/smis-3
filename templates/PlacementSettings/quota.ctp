<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-search" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Participating Unit\'s Quota Setting for Auto Student Placement'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('PlacementSetting'); ?>

				<div style="margin-top: -30px;"><hr></div>

				<div class="quotas form">
					<table cellspacing="0" cellpadding="0" class="table">
						<tr>
							<td style="width:20%">Academic Year:</td>
							<td style="width:30%"><?= $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year']; ?></td>
							<td style="width:20%">Placement Round:</td>
							<td style="width:30%"><?= $firstRowRoundParticipants['PlacementRoundParticipant']['placement_round']; ?></td>
						</tr>
						<tr>
							<td style="width:20%">Program:</td>
							<td style="width:30%"><?= $programs[$firstRowRoundParticipants['PlacementRoundParticipant']['program_id']]; ?></td>
							<td style="width:20%">Program Type :</td>
							<td style="width:30%"><?= $programTypes[$firstRowRoundParticipants['PlacementRoundParticipant']['program_type_id']]; ?></td>
						</tr>
						<tr>
							<td style="width:20%">Involved Students currenty in:</td>
							<td colspan="3">
								<?php
								$name = "";
								$count = 0;
								if (isset($allUnits) && !empty($allUnits)) {
									foreach ($allUnits as $uk => $uv) {
										foreach ($uv as $uuk => $uuv) {
											if ($firstRowRoundParticipants['PlacementRoundParticipant']['applied_for'] == $uuk) {
												$count++;
												$name = $uuv;
												break 2;
											}
										}
									}
								}
								echo $name; ?>
							</td>
						</tr>
					</table>
					<hr>

					<div id="quotaparticiptingdepartment"></div>

					<?php
					if (isset($lockEditing) && $lockEditing > 0) { ?>
						<div class="info-box info-message"><span></span>Participating Quota quota for <?= $firstRowRoundParticipants['PlacementRoundParticipant']['academic_year']; ?> academic year for student auto placement applied for those students in <?= $allUnits[$firstRowRoundParticipants['PlacementRoundParticipant']['applied_for']]; ?>. You have already run the auto placement, you can not add or edit quota now.</div>
						<?php
					}
					
					if (!empty($placementRoundParticipants)) { ?>
						<hr>
						<h6>Total number of students who can be elegible for placement if prepared.</h6>
						<hr>
						<?php
						if (isset($totalStudentsForPlacement)) { ?>
							<table cellpadding="0" cellspacing="0" class="table">
								<tbody>
									<tr>
										<td style='font-weight:bold; width:20%'>Total number of not placed students: <?= number_format($totalStudentsForPlacement, 0, '.', ','); ?></td>
									</tr>
								</tbody>
							</table>
							<?php
						} ?>
						<hr>

						<table width="50%" cellpadding="0" cellspacing="0" class="table">
							<tbody>
								<tr>
									<th colspan=3>Total Number of privileged students</th>
								</tr>
								<tr>
									<th>Region</th>
									<th>Female</th>
									<th>Disability</th>
								</tr>
								<tr>
									<td><?= $quota_sum['region'] ?></td>
									<td><?= $quota_sum['female'] ?></td>
									<td><?= $quota_sum['disable'] ?></td>
								</tr>
							</tbody>
						</table>
						<br>

						<?php
						if ($totalStudentsPreparedForPlacement) { ?>

							<h6>Total number of students who are prepared and selected for placement</h6>
							<hr>

							<?php
							if (isset($totalStudentsPreparedForPlacement)) { ?>
								<table cellpadding="0" cellspacing="0" class="table">
									<tbody>
										<tr>
											<td style='font-weight:bold; width:20%'>Total number of students  not placed but ready for placement: <?= number_format($totalStudentsPreparedForPlacement, 0, '.', ','); ?></td>
										</tr>
									</tbody>
								</table>
								<?php
							} ?>
							<br>

							<table width="50%" cellpadding="0" cellspacing="0" class="table">
								<tbody>
									<tr>
										<th colspan=3>Total Number of privileged students among prepared</th>
									</tr>
									<tr>
										<th>Region</th>
										<th>Female</th>
										<th>Disability</th>
									</tr>
									<tr>
										<td><?= $quota_sum['pregion'] ?></td>
										<td><?= $quota_sum['pfemale'] ?></td>
										<td><?= $quota_sum['pdisable'] ?></td>
									</tr>
								</tbody>
							</table>
							<br>

							<hr>
							<p onclick="toggleView('all_stat')" class="fs15" style="font-weight:bold; padding-bottom:0px; margin-bottom:0px"><?= $this->Html->image('plus2.gif', array('id' => 'iall_stat'));; ?> All student preference stat</p>

							<div id="call_stat" style="display:none">
								<br>
								<div style="overflow-x:auto;">
									<table cellpadding="0" cellspacing="0" class="table">
										<thead>
											<tr>
												<td style="width:25%" class="vcenter">Participating Unit</td>
												<?php
												for ($i = 1; $i <= count($stat['pall']); $i++) {
													echo '<td style="width:' . (75 / count($stat['pall'])) . '%" class="center">' . $i . '</td>';
												} ?>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach ($stat['pall'] as $stat_dep) { ?>
												<tr>
													<td class="vcenter" style="background-color: white;"><?= $stat_dep['department_name']; ?></td>
													<?php
													$preference_sum = 0;
													for ($i = 1; $i <= count($stat['pall']); $i++) { ?>
														<td class="center" style="background-color: white;">
															<table style="width:100%" cellpadding="0" cellspacing="0">
																<?php
																foreach ($stat_dep['count'][$i] as $k => $v) {
																	if (strcasecmp($k, '~total~') != 0) { ?>
																		<tr>
																			<td style="width:50%; background:transparent" class="center"><?= $k; ?>:</td>
																			<td style="width:50%; background:transparent" class="center"><?= $v; ?></td>
																		</tr>
																		<?php
																	}
																} ?>
																<tr>
																	<!-- <td style="background:transparent">Total:</td> -->
																	<td style="background:transparent" colspan="2" class="center"><?= $stat_dep['count'][$i]['~total~']; ?></td>
																</tr>
															</table>
														</td>
														<?php
													} ?>
												</tr>
												<?php
											} ?>
										</tbody>
									</table>
								</div>
							</div>

							<hr>

							<p onclick="toggleView('female_stat')" class="fs15" style="font-weight:bold; padding-bottom:0px; margin-bottom:0px"><?= $this->Html->image('plus2.gif', array('id' => 'ifemale_stat'));; ?> Female students preference stat</p>

							<div id="cfemale_stat" style="display:none">
								<br>
								<div style="overflow-x:auto;">
									<table cellpadding="0" cellspacing="0" class="table">
										<thead>
											<tr>
												<td style="width:25%" class="vcenter">Participating Unit</td>
												<?php
												for ($i = 1; $i <= count($stat['pfemale']); $i++) {
													echo '<td style="width:' . (75 / count($stat['pfemale'])) . '%" class="center">' . $i . '</td>';
												} ?>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach ($stat['pfemale'] as $stat_dep) { ?>
												<tr>
													<td class="vcenter" style="background-color: white;"><?= $stat_dep['department_name']; ?></td>
													<?php
													$preference_sum = 0;
													for ($i = 1; $i <= count($stat['pfemale']); $i++) { ?>
														<td class="center" style="background-color: white;">
															<table style="width:100%" cellpadding="0" cellspacing="0">
																<?php
																foreach ($stat_dep['count'][$i] as $k => $v) {
																	if (strcasecmp($k, '~total~') != 0) { ?>
																		<tr> 
																			<td style="width:50%; background:transparent" class="center"><?= $k; ?>:</td>
																			<td style="width:50%; background:transparent" class="center"><?= $v ;?></td>
																		</tr>
																		<?php
																	}
																} ?>
																<tr>
																	<!-- <td style="background:transparent" class="center">Total:</td> -->
																	<td style="background:transparent" colspan="2" class="center"><?= $stat_dep['count'][$i]['~total~']; ?></td>
																</tr>
															</table>
														</td>
														<?php
													} ?>
												</tr>
												<?php
											} ?>
										</tbody>
									</table>
								</div>
							</div>
							<hr>

							<p onclick="toggleView('region_stat')" class="fs15" style="font-weight:bold; padding-bottom:0px; margin-bottom:0px"><?= $this->Html->image('plus2.gif', array('id' => 'iregion_stat'));; ?> Developing regions preference stat</p>
							
							<div id="cregion_stat" style="display:none">
								<br>
								<div style="overflow-x:auto;">
									<table cellpadding="0" cellspacing="0" class="table">
										<thead>
											<tr>
												<td style="width:25%" class="vcenter">Participating Unit</td>
												<?php
												for ($i = 1; $i <= count($stat['pregion']); $i++) {
													echo '<td style="width:' . (75 / count($stat['pregion'])) . '%" class="center">' . $i . '</td>';
												} ?>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach ($stat['pregion'] as $stat_dep) { ?>
												<tr>
													<td class="vcenter" style="background-color: white;"><?= $stat_dep['department_name']; ?></td>
													<?php
													$preference_sum = 0;
													for ($i = 1; $i <= count($stat['pregion']); $i++) { ?>
														<td class="center" style="background-color: white;">
															<table style="width:100%" cellpadding="0" cellspacing="0">
																<?php
																foreach ($stat_dep['count'][$i] as $k => $v) {
																	if (strcasecmp($k, '~total~') != 0) { ?>
																		<tr>
																			<td style="width:50%; background:transparent" class="center"><?= $k ;?>:</td>
																			<td style="width:50%; background:transparent" class="center"><?= $v ;?></td>
																		</tr>
																		<?php
																	}
																} ?>
																<tr>
																	<!-- <td style="background:transparent" class="center">Total:</td> -->
																	<td style="background:transparent" colspan="2" class="center"><?= $stat_dep['count'][$i]['~total~']; ?></td>
																</tr>
															</table>
														</td>
														<?php
													} ?>
												</tr>
												<?php
											} ?>
										</tbody>
									</table>
								</div>
							</div>
							<hr>

							<p onclick="toggleView('disable_stat')" class="fs15" style="font-weight:bold; padding-bottom:0px; margin-bottom:0px"><?= $this->Html->image('plus2.gif', array('id' => 'idisable_stat'));; ?> Disabled Students Preference Stats</p>
							
							<div id="cdisable_stat" style="display:none">
								<br>
								<div style="overflow-x:auto;">
									<table cellpadding="0" cellspacing="0" class="table">
										<thead>
											<tr>
												<td style="width:25%" class="vcenter">Participating Unit</td>
												<?php
												for ($i = 1; $i <= count($stat['pdisable']); $i++) {
													echo '<td style="width:' . (75 / count($stat['pdisable'])) . '%" class="center">' . $i . '</td>';
												} ?>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach ($stat['pdisable'] as $stat_dep) { ?>
												<tr>
													<td class="vcenter" style="background-color: white;"><?= $stat_dep['department_name']; ?></td>
													<?php
													$preference_sum = 0;
													for ($i = 1; $i <= count($stat['pdisable']); $i++) { ?>
														<td class="center" style="background-color: white;">
															<table style="width:100%" cellpadding="0" cellspacing="0">
																<?php
																foreach ($stat_dep['count'][$i] as $k => $v) {
																	if (strcasecmp($k, '~total~') != 0) { ?>
																		<tr>
																			<td style="width:50%; background:transparent" class="center"><?= $k; ?>:</td>
																			<td style="width:50%; background:transparent" class="center"><?= $v ;?></td>
																		</tr>
																		<?php
																	}
																} ?>
																<tr>
																	<!-- <td style="background:transparent" class="center">Total:</td> -->
																	<td style="background:transparent" colspan="2" class="center"><?= $stat_dep['count'][$i]['~total~']; ?></td>
																</tr>
															</table>
														</td>
														<?php
													} ?>
												</tr>
												<?php
											} ?>
										</tbody>
									</table>
								</div>
							</div>
							<hr>
							<?php
						} ?>

						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<td colspan="6"><h6><?= (isset($already_added_capacity) && $already_added_capacity > 0 ? '<strong>Edit or Re-adjust Departments Quota</strong>' : '<strong>Add  Quota</strong>'); ?></h6></td>
									</tr>
									<tr>
										<td class="vcenter" style="width: 50%;">Participating Unit</td>
										<td class="center">Capacity</td>
										<td class="center">Female Quota(if any)</td>
										<td class="center">Region Quota (if any)</td>
										<td class="center">Disability Quota(if any)</td>
										<td class="center"><!-- Action --></td>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 0;

									// echo $this->Form->hidden('PlacementSetting..academic_year', array('value' => $selectedAcademicYear));
									// echo $this->Form->hidden('PlacementSetting.placement_round', array('value' => $placementround));
									// echo $this->Form->hidden('PlacementSetting.applied_for',array('value' => $applied_for));
									
									foreach ($placementRoundParticipants as $key => $value) {
										if (isset($lockEditing) && $lockEditing > 0) { ?>
											<tr>
												<td class="vcenter"><?= $value['PlacementRoundParticipant']['name']; ?></td>
												<td class="center"><?= $value['PlacementRoundParticipant']['intake_capacity']; ?></td>
												<td class="center"><?= $value['PlacementRoundParticipant']['female_quota']; ?></td>
												<td class="center"><?= $value['PlacementRoundParticipant']['region_quota']; ?></td>
												<td class="center"><?= $value['PlacementRoundParticipant']['disability_quota']; ?></td>
												<td class="center">&nbsp;</td>
											</tr>
											<?php
										} else { ?>
											<?= $this->Form->hidden('PlacementSetting.' . $count . '.id', array('value' => $value['PlacementRoundParticipant']['id'])); ?>
											<?= $this->Form->hidden('PlacementSetting.' . $count . '.group_identifier', array('value' => $value['PlacementRoundParticipant']['group_identifier'])); ?>
											<?= $this->Form->hidden('PlacementSetting.' . $count . '.foreign_key', array('value' => $value['PlacementRoundParticipant']['foreign_key'])); ?>
											<tr>
												<td class="vcenter"><?= $value['PlacementRoundParticipant']['name']; ?></td>
												<td class="center"><?= $this->Form->input('PlacementSetting.' . $count . '.intake_capacity', array('value' => (empty($this->request->data['PlacementSetting'][$count]['intake_capacity']) ? $value['PlacementRoundParticipant']['intake_capacity'] : $this->request->data['PlacementSetting'][$count]['intake_capacity']), 'style' => 'width:150px', 'label' => false, 'type' => 'number', 'min' => 0, 'required' => 'required')); ?></td>
												<td class="center"><?= $this->Form->input('PlacementSetting.' . $count . '.female_quota', array('value' => (empty($this->request->data['PlacementSetting'][$count]['female_quota']) ? $value['PlacementRoundParticipant']['female_quota'] : $this->request->data['PlacementSetting'][$count]['female_quota']), 'style' => 'width:150px', 'label' => false, 'type' => 'number', 'min' => 0 )); ?></td>
												<td class="center"><?= $this->Form->input('PlacementSetting.' . $count . '.region_quota', array('value' => (empty($this->request->data['PlacementSetting'][$count]['region_quota']) ? $value['PlacementRoundParticipant']['region_quota'] : $this->request->data['PlacementSetting'][$count]['region_quota']), 'style' => 'width:150px', 'label' => false, 'type' => 'number', 'min' => 0)); ?></td>
												<td class="center"><?= $this->Form->input('PlacementSetting.' . $count . '.disability_quota', array('value' => (empty($this->request->data['PlacementSetting'][$count]['disability_quota']) ? $value['PlacementRoundParticipant']['disability_quota'] : $this->request->data['PlacementSetting'][$count]['disability_quota']), 'style' => 'width:150px', 'label' => false, 'type' => 'number', 'min' => 0)); ?></td>
												<td class="center">
													<?php
													if (!empty($value['PlacementRoundParticipant']['id'])) {
														$tmp_academic_year = str_replace('/', "-", $value['PlacementRoundParticipant']['academic_year']);
														$action_participating_id = 'add_quota~participating_departments~' . $tmp_academic_year;
													}

													if (!empty($action_participating_id)) {
														//echo $this->Html->link(__('Delete'), array('action' => 'delete', $value['PlacementRoundParticipant']['id'], $action_participating_id), null, sprintf(__('Are you sure you want to delete # %s?'), $value['PlacementRoundParticipant']['name']));
													} ?>
												</td>
											</tr>
											<?php
										}
										$count++;
									} ?>
									<tr>
										<td colspan="6">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="6" class="font"><h6>Please select region/s that will be considered as developing region and entitled to privilaged quota, if there is any.</h6></td>
									</tr>
									<tr>
										<td colspan="6">
											<?php
											$selected_region_ids = array();
											if (isset($selectedDevelopingRegions)) {
												//	$selected_region_ids = explode(',', $selectedDevelopingRegions);
												$selected_region_ids = $selectedDevelopingRegions;
											}
											if (isset($lockEditing) && $lockEditing > 0) {
												echo $this->Form->input('PlacementSetting.0.developing_region', array('label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'div' => 'input select', 'disabled' => true, 'value' => (!empty($this->request->data['PlacementSetting']['developing_region']) ? $this->request->data['PlacementSetting']['developing_region'] : (!empty($selected_region_ids) ? $selected_region_ids : ''))));
											} else {
												echo $this->Form->input('PlacementSetting.0.developing_region', array('label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'div' => 'input select', 'value' => (!empty($this->data['PlacementSetting'][0]['developing_region']) ? $this->data['PlacementSetting'][0]['developing_region'] : (!empty($selected_region_ids) ? $selected_region_ids : ''))));
											} ?>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<hr>

						<?php
						if (isset($lockEditing) && $lockEditing > 0) {
						} else {
							$options = array('label' => 'Save Quota', 'name' => 'quota', 'class' => 'tiny radius button bg-blue', 'div' => false);
							echo $this->Form->Submit('Save Quota', array('div' => false, 'name' => 'quota', 'class' => 'tiny radius button bg-blue')); 
							//echo $this->Form->end($options);
						} ?>
						<?php
					} ?>
					<?= $this->Form->end();  ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function toggleView(id) {
		if ($('#c' + id).css("display") == 'none') {
			$('#i' + id).attr("src", '/img/minus2.gif');
		} else {
			$('#i' + id).attr("src", '/img/plus2.gif');
		}
		$('#c' + id).toggle("slow");
	}
</script>