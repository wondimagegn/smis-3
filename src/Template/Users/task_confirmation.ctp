<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Task Confirmation'); ?></span>
        </div>
    </div>
	<div class="box-body">
		<div class="row">
			<?= $this->Form->create('Permission'); ?>
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>
				<?php
				if (!empty($task_confirmation_request_status)) { ?>
					<h6 class="fs14 text-gray"> List of tasks requested by you within the past 7 days and their status</h6>
					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<td style="width:3%" class="center">#</td>
									<td style="width:15%" class="vcenter">Task</td>
									<td style="width:13%" class="center">Date Requested</td>
									<td style="width:15%" class="center">Applicable To</td>
									<td style="width:10%" class="center">Confirmation</td>
									<td style="width:15%" class="center">Date Confirmed</td>
									<td style="width:16%;" class="center">Confirmed By</td>
									<td style="width:8%;" class="center"> Action </td>
								</tr>
							</thead>
							<tbody>
								<?php
								$count = 0;
								foreach ($task_confirmation_request_status as $value) {
									$valid_date_from = date("Y-m-d H:i:s", mktime(date("H") - 72, date("i"), date("s"), date("n"), date("j"), date("Y"))); ?>
									<tr>
										<td class="center"><?=  ++$count; ?></td>
										<td class="vcenter">
											<?php 
											echo $value['Vote']['task'];
											$office = "";
											if (strcasecmp($value['Vote']['task'], 'Administrator Assignment') == 0 || strcasecmp($value['Vote']['task'], 'Administrator Cancellation') == 0) {
												if ($value['ApplicableOn']['role_id'] == ROLE_MEAL) {
													$office = "Meal service";
												}
												if ($value['ApplicableOn']['role_id'] == ROLE_ACCOMODATION) {
													$office = "Accommodation service";
												}
												if ($value['ApplicableOn']['role_id'] == ROLE_HEALTH) {
													$office = "Health service";
												}
												if ($value['ApplicableOn']['role_id'] == ROLE_REGISTRAR) {
													$office = "Registrar service";
												}
												if ($value['ApplicableOn']['role_id'] == ROLE_DEPARTMENT) {
													$office = $value['ApplicableOn']['Staff'][0]['Department']['name'];
												}
												if ($value['ApplicableOn']['role_id'] == ROLE_COLLEGE) {
													$office = $value['ApplicableOn']['Staff'][0]['College']['name'];
												}
												echo ' to ' . $office;
												//  echo "dd=".$value['Vote']['applicable_on_user_id'];
											} else if (strcasecmp($value['Vote']['task'], 'Role Change') == 0) {
												$to = "";
												if ($value['Vote']['data'] == ROLE_ACCOMODATION) {
													$to = "Accommodation";
												} else if ($value['Vote']['data'] == ROLE_HEALTH) {
													$to = "Health Service";
												} else if ($value['Vote']['data'] == ROLE_MEAL) {
													$to = "Meal Service";
												} else if ($value['Vote']['data'] == ROLE_DEPARTMENT) {
													$to = "Department";
												} else if ($value['Vote']['data'] == ROLE_COLLEGE) {
													$to = "College";
												} else if ($value['Vote']['data'] == ROLE_SYSADMIN) {
													$to = "System Administrator";
												} else if ($value['Vote']['data'] == ROLE_REGISTRAR) {
													$to = "Registrar";
												} else if ($value['Vote']['data'] == ROLE_INSTRUCTOR) {
													$to = "Instructor";
												} else if ($value['Vote']['data'] == ROLE_GENERAL) {
													$to = "General";
												} else if ($value['Vote']['data'] == ROLE_CLEARANCE) {
													$to = "Clearance";
												}
												echo ' to <u>' . $to . '</u>';
											}
											?>
										</td>
										<td class="center"><?= $this->Time->format("M j, Y g:i:s A", $value['Vote']['created'], NULL, NULL); ?></td>
										<td class="center"><?= $value['ApplicableOn']['first_name'] . ' ' . $value['ApplicableOn']['middle_name'] . ' ' . $value['ApplicableOn']['last_name'] . ' (' . $value['ApplicableOn']['username'] . ')'; ?></td>
										<td class="center">
											<?php
											if ($value['Vote']['created'] >= $valid_date_from) {
												echo ($value['Vote']['confirmation'] == 0 ? '<span class="on-process">Waiting</span>' : ($value['Vote']['confirmation'] == 1 ? '<span class="accepted">Accepted</span>' : '<span class="rejected">Rejected</span>'));
											} else {
												if($value['Vote']['confirmation'] == 1){
													echo '<span class="accepted">Accepted</span>';
												} else {
													echo '<span class="rejected">Expired</span>';
												}
											}
											?>
										</td>
										<td class="center"><?= ($value['Vote']['confirmation_date'] != '0000-00-00 00:00:00' && $value['Vote']['confirmation_date'] != null ? $this->Time->format("M j, Y g:i:s A", $value['Vote']['confirmation_date'], NULL, NULL) : '---'); ?></td>
										<td class="center"><?= ($value['ConfirmedBy']['first_name'] == null ? '---' : $value['ConfirmedBy']['first_name'] . ' ' . $value['ConfirmedBy']['middle_name'] . ' ' . $value['ConfirmedBy']['last_name'] . ' (' . $value['ConfirmedBy']['username'] . ')'); ?></td>
										<td class="center">
											<?php
											if ($value['Vote']['confirmation'] == 0 && $value['Vote']['created'] >= $valid_date_from) {
												echo $this->Html->link(__('Cancel'), array('action' => 'cancel_task_confirmation', $value['Vote']['id']), null, sprintf(__('Are you sure you want to cancel "%s" request for "' . $value['ApplicableOn']['first_name'] . ' ' . $value['ApplicableOn']['middle_name'] . ' ' . $value['ApplicableOn']['last_name'] . ' (' . $value['ApplicableOn']['username'] . ')"?'), $value['Vote']['task']));
											} else
												echo '---';
											?>
										</td>
									</tr>
									<?php
								} ?>
							</tbody>
						</table>
					</div>
					<?php
				} else {
					echo '<h6 class="fs14 text-gray"> *** There is no task which is created by you within the past 7 days. *** </h6>';
				} 

				if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) {
					if (!empty($tasks_for_confirmation)) { ?>
						<hr>
						<h6 class="fs14 text-gray"> List of confirmation requests for some critical tasks. The request will be automatically expire after 72 hours if it is not accepted.</h6>
						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<td style="width:3%" class="center">#</td>
										<td style="width:20%" class="vcenter">Task</td>
										<td style="width:20%" class="center">Date Requested</td>
										<td style="width:15%" class="center">Applicable To</td>
										<td style="width:22%" class="center">Requested By</td>
										<td style="width:20%;" class="center">Action</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 0;
									foreach ($tasks_for_confirmation as $value) { ?>
										<tr>
											<td class="center"> <?=  ++$count; ?> </td>
											<td class="vcenter">
												<?php 
												echo $value['Vote']['task'];
												$office = "";
												if (strcasecmp($value['Vote']['task'], 'Administrator Assignment') == 0 || strcasecmp($value['Vote']['task'], 'Administrator Cancellation') == 0) {
													if ($value['ApplicableOn']['role_id'] == ROLE_MEAL) {
														$office = "Meal service";
													}
													if ($value['ApplicableOn']['role_id'] == ROLE_ACCOMODATION) {
														$office = "Accommodation service";
													}
													if ($value['ApplicableOn']['role_id'] == ROLE_HEALTH) {
														$office = "Health service";
													}
													if ($value['ApplicableOn']['role_id'] == ROLE_REGISTRAR) {
														$office = "Registrar service";
													}
													if ($value['ApplicableOn']['role_id'] == ROLE_DEPARTMENT) {
														$office = $value['ApplicableOn']['Staff'][0]['Department']['name'];
													}
													if ($value['ApplicableOn']['role_id'] == ROLE_COLLEGE) {
														$office = $value['ApplicableOn']['Staff'][0]['College']['name'];
													}

													echo ' to ' . $office;
												} else if (strcasecmp($value['Vote']['task'], 'Role Change') == 0) {
													$to = "";
													if ($value['Vote']['data'] == ROLE_ACCOMODATION) {
														$to = "Accommodation";
													} else if ($value['Vote']['data'] == ROLE_HEALTH) {
														$to = "Health Service";
													} else if ($value['Vote']['data'] == ROLE_MEAL) {
														$to = "Meal Service";
													} else if ($value['Vote']['data'] == ROLE_DEPARTMENT) {
														$to = "Department";
													} else if ($value['Vote']['data'] == ROLE_COLLEGE) {
														$to = "College";
													} else if ($value['Vote']['data'] == ROLE_SYSADMIN) {
														$to = "System Administrator";
													} else if ($value['Vote']['data'] == ROLE_REGISTRAR) {
														$to = "Registrar";
													} else if ($value['Vote']['data'] == ROLE_INSTRUCTOR) {
														$to = "Instructor";
													} else if ($value['Vote']['data'] == ROLE_GENERAL) {
														$to = "General";
													} else if ($value['Vote']['data'] == ROLE_CLEARANCE) {
														$to = "Clearance";
													}
													$from = "";
													if ($value['ApplicableOn']['role_id'] == ROLE_ACCOMODATION) {
														$from = "Accommodation";
													} else if ($value['ApplicableOn']['role_id'] == ROLE_HEALTH) {
														$from = "Health Service";
													} else if ($value['ApplicableOn']['role_id'] == ROLE_MEAL) {
														$from = "Meal Service";
													} else if ($value['ApplicableOn']['role_id'] == ROLE_DEPARTMENT) {
														$from = "Department";
													} else if ($value['ApplicableOn']['role_id'] == ROLE_COLLEGE) {
														$from = "College";
													} else if ($value['ApplicableOn']['role_id'] == ROLE_SYSADMIN) {
														$from = "System Administrator";
													} else if ($value['ApplicableOn']['role_id'] == ROLE_REGISTRAR) {
														$from = "Registrar";
													} else if ($value['ApplicableOn']['role_id'] == ROLE_INSTRUCTOR) {
														$from = "Instructor";
													} else if ($value['ApplicableOn']['role_id'] == ROLE_GENERAL) {
														$from = "General";
													} else if ($value['ApplicableOn']['role_id'] == ROLE_CLEARANCE) {
														$from = "Clearance";
													}
													echo ' from <u>' . $from . '</u> to <u>' . $to . '</u>';
												} ?>
											</td>
											<td class="center"><?= $this->Time->format("M j, Y g:i:s A", $value['Vote']['created'], NULL, NULL); ?></td>
											<td class="center"><?= $value['ApplicableOn']['first_name'] . ' ' . $value['ApplicableOn']['middle_name'] . ' ' . $value['ApplicableOn']['last_name'] . ' (' . $value['ApplicableOn']['username'] . ')'; ?></td>
											<td class="center"><?= ($value['Requester']['first_name'] == null ? '---' : $value['Requester']['first_name'] . ' ' . $value['Requester']['middle_name'] . ' ' . $value['Requester']['last_name'] . ' (' . $value['Requester']['username'] . ')'); ?></td>
											<td class="center">
												<?php
												$valid_date_from = date("Y-m-d H:i:s", mktime(date("H") - 72, date("i"), date("s"), date("n"), date("j"), date("Y")));
												if ($value['Vote']['confirmation'] == 0 && $value['Vote']['created'] >= $valid_date_from) {
													echo $this->Html->link(__('Accept'), array('action' => 'confirm_task', $value['Vote']['id']), null, sprintf(__('Are you sure you want to accept "%s" request for "' . $value['ApplicableOn']['first_name'] . ' ' . $value['ApplicableOn']['middle_name'] . ' ' . $value['ApplicableOn']['last_name'] . ' (' . $value['ApplicableOn']['username'] . ')"?'), $value['Vote']['task']));
												} else {
													echo '---';
												}
												?>
											</td>
										</tr>
										<?php
									} ?>
								</tbody>
							</table>
						</div>
						<?php
					} else {
						echo '<hr><h6 class="fs14 text-gray"> *** There is no task confirmation request which is placed by departments, colleges or other system administrators. *** </h6>';
					}
				} 
				
				if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) {
					if (!empty($confirmed_tasks)) { ?>
						<hr>
						<h6 class="fs14 text-gray"> List of tasks which are confirmed by you within the past 7 days.</h6>
						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<td style="width:3%" class="center">#</td>
										<td style="width:20%" class="vcenter">Task</td>
										<td style="width:20%" class="center">Date Requested</td>
										<td style="width:27%" class="center">Applicable To</td>
										<td style="width:30%" class="center">Requested By</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 0;
									foreach ($confirmed_tasks as $value) { ?>
										<tr>
											<td class="center"><?=  ++$count; ?></td>
											<td class="vcenter">
												<?php 
												echo $value['Vote']['task'];
												$office = "";
												if (strcasecmp($value['Vote']['task'], 'Administrator Assignment') == 0 || strcasecmp($value['Vote']['task'], 'Administrator Cancellation') == 0) {
													if ($value['ApplicableOn']['role_id'] == ROLE_MEAL) {
														$office = "Meal service";
													}
													if ($value['ApplicableOn']['role_id'] == ROLE_ACCOMODATION) {
														$office = "Accommodation service";
													}
													if ($value['ApplicableOn']['role_id'] == ROLE_HEALTH) {
														$office = "Health service";
													}
													if ($value['ApplicableOn']['role_id'] == ROLE_REGISTRAR) {
														$office = "Health service";
													}
													if ($value['ApplicableOn']['role_id'] == ROLE_DEPARTMENT) {
														$office = $value['ApplicableOn']['Staff'][0]['Department']['name'];
													}
													if ($value['ApplicableOn']['role_id'] == ROLE_COLLEGE) {
														$office = $value['ApplicableOn']['Staff'][0]['College']['name'];
													}
													echo ' to ' . $office;
												} else if (strcasecmp($value['Vote']['task'], 'Role Change') == 0) {
													$to = "";
													if ($value['Vote']['data'] == ROLE_ACCOMODATION) {
														$to = "Accommodation";
													} else if ($value['Vote']['data'] == ROLE_HEALTH) {
														$to = "Health Service";
													} else if ($value['Vote']['data'] == ROLE_MEAL) {
														$to = "Meal Service";
													} else if ($value['Vote']['data'] == ROLE_DEPARTMENT) {
														$to = "Department";
													} else if ($value['Vote']['data'] == ROLE_COLLEGE) {
														$to = "College";
													} else if ($value['Vote']['data'] == ROLE_SYSADMIN) {
														$to = "System Administrator";
													} else if ($value['Vote']['data'] == ROLE_REGISTRAR) {
														$to = "Registrar";
													} else if ($value['Vote']['data'] == ROLE_INSTRUCTOR) {
														$to = "Instructor";
													} else if ($value['Vote']['data'] == ROLE_GENERAL) {
														$to = "General";
													} else if ($value['Vote']['data'] == ROLE_CLEARANCE) {
														$to = "Clearance";
													}
													echo ' to <u>' . $to . '</u>';
												} ?>
											</td>
											<td class="center"><?= $this->Time->format("M j, Y g:i:s A", $value['Vote']['created'], NULL, NULL); ?></td>
											<td class="center"><?= $value['ApplicableOn']['first_name'] . ' ' . $value['ApplicableOn']['middle_name'] . ' ' . $value['ApplicableOn']['last_name'] . ' (' . $value['ApplicableOn']['username'] . ')'; ?></td>
											<td class="center"><?=  ($value['Requester']['first_name'] == null ? '---' : $value['Requester']['first_name'] . ' ' . $value['Requester']['middle_name'] . ' ' . $value['Requester']['last_name'] . ' (' . $value['Requester']['username'] . ')'); ?></td>
										</tr>
										<?php
									} ?>
								</tbody>
							</table>
						</div>
						<?php
					} else {
						echo '<hr><h6 class="fs14 text-gray"> *** There is no task which is confirmed within the past 7 days. *** </h6>';
					}
				}

				if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) {
					if (!empty($other_admin_tasks)) { ?>
						<hr>
						<h6 class="fs14 text-gray"> List of tasks which are asked and confirmed by other system administrators within the past 30 days.</h6>
						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<td style="width:5%" class="center">#</td>
										<td style="width:20%" class="vcenter">Task</td>
										<td style="width:20%" class="center">Date Requested</td>
										<td style="width:15%" class="center">Applicable To</td>
										<td style="width:20%" class="center">Requested By</td>
										<td style="width:20%" class="center">Confirmed By</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 0;
									foreach ($other_admin_tasks as $value) { ?>
										<tr>
											<td class="center"><?=  ++$count; ?></td>
											<td class="vcenter">
												<?php
												echo $value['Vote']['task'];
												$office = "";
												if (strcasecmp($value['Vote']['task'], 'Administrator Assignment') == 0 || strcasecmp($value['Vote']['task'], 'Administrator Cancellation') == 0) {
													if ($value['ApplicableOn']['role_id'] == ROLE_MEAL) {
														$office = "Meal service";
													}
													if ($value['ApplicableOn']['role_id'] == ROLE_ACCOMODATION) {
														$office = "Accommodation service";
													}
													if ($value['ApplicableOn']['role_id'] == ROLE_HEALTH) {
														$office = "Health service";
													}
													if ($value['ApplicableOn']['role_id'] == ROLE_REGISTRAR) {
														$office = "Health service";
													}
													if ($value['ApplicableOn']['role_id'] == ROLE_DEPARTMENT) {
														$office = $value['ApplicableOn']['Staff'][0]['Department']['name'];
													}
													if ($value['ApplicableOn']['role_id'] == ROLE_COLLEGE) {
														$office = $value['ApplicableOn']['Staff'][0]['College']['name'];
													}
													echo ' to ' . $office;
												} else if (strcasecmp($value['Vote']['task'], 'Role Change') == 0) {
													$to = "";
													if ($value['Vote']['data'] == ROLE_ACCOMODATION) {
														$to = "Accommodation";
													} else if ($value['Vote']['data'] == ROLE_HEALTH) {
														$to = "Health Service";
													} else if ($value['Vote']['data'] == ROLE_MEAL) {
														$to = "Meal Service";
													} else if ($value['Vote']['data'] == ROLE_DEPARTMENT) {
														$to = "Department";
													} else if ($value['Vote']['data'] == ROLE_COLLEGE) {
														$to = "College";
													} else if ($value['Vote']['data'] == ROLE_SYSADMIN) {
														$to = "System Administrator";
													} else if ($value['Vote']['data'] == ROLE_REGISTRAR) {
														$to = "Registrar";
													} else if ($value['Vote']['data'] == ROLE_INSTRUCTOR) {
														$to = "Instructor";
													} else if ($value['Vote']['data'] == ROLE_GENERAL) {
														$to = "General";
													} else if ($value['Vote']['data'] == ROLE_CLEARANCE) {
														$to = "Clearance";
													}
													echo ' to <u>' . $to . '</u>';
												}
												?>
											</td>
											<td class="center"><?= $this->Time->format("M j, Y g:i:s A", $value['Vote']['created'], NULL, NULL); ?></td>
											<td class="center"><?= $value['ApplicableOn']['first_name'] . ' ' . $value['ApplicableOn']['middle_name'] . ' ' . $value['ApplicableOn']['last_name'] . ' (' . $value['ApplicableOn']['username'] . ')'; ?></td>
											<td class="center"><?= ($value['Requester']['first_name'] == null ? '---' : $value['Requester']['first_name'] . ' ' . $value['Requester']['middle_name'] . ' ' . $value['Requester']['last_name'] . ' (' . $value['Requester']['username'] . ')'); ?></td>
											<td class="center"><?= ($value['ConfirmedBy']['first_name'] == null ? '---' : $value['ConfirmedBy']['first_name'] . ' ' . $value['ConfirmedBy']['middle_name'] . ' ' . $value['ConfirmedBy']['last_name'] . ' (' . $value['ConfirmedBy']['username'] . ')'); ?></td>
										</tr>
										<?php
									} ?>
								</tbody>
							</table>
						</div>
						<?php
					} else {
						echo '<hr><h6 class="fs14 text-gray"> *** There is no task which is created and confirmed by other system administrators within the past 30 days. *** </h6>';
					}
				} ?>
				<hr>
			</div>
		</div>
	</div>
</div>