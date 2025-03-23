<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Student Placement Preferences'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('PlacementPreference', array('name' => 'searchPref')); ?>
				<div class="preferences index">
					<div style="margin-top: -30px;"><hr></div>

					<?php
					if ($role_id != ROLE_STUDENT) { ?>
						<fieldset style="padding-bottom: 5px;">
							<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('PlacementPreference.program_id', array('id' => 'ProgramId', 'class' => 'fs13', 'style' => 'width:85%;', 'label' => 'Program: ', 'onchange' => 'updateForeignKey()')); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('PlacementPreference.program_type_id', array('id' => 'ProgramTypeId', 'class' => 'fs13', 'label' => 'Program Type: ', 'type' => 'select', 'style' => 'width:85%;', 'onchange' => 'updateForeignKey()')); ?>
								</div>
								<div class="large-3 columns">
									<?=  $this->Form->input('PlacementPreference.academic_year', array('id' => 'Academic Year', 'label' => 'Academic Year', 'type' => 'select', 'options' => $acyear_array_data, 'default'=> $selectedAcy, 'style' => 'width:90%;', 'onchange' => 'updateForeignKey()')); ?>
								</div>
								<div class="large-3 columns">
									<?=  $this->Form->input('PlacementPreference.round', array('class' => 'PlacementRound', 'id' => 'PlacementRound', 'label' => 'Round:', 'style' => 'width:90%;', 'default' => $selectedRound, 'type' => 'select', 'options' => Configure::read('placement_rounds'), 'onchange' => 'updateForeignKey()')); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-6 columns">
									<?= $this->Form->input('PlacementPreference.applied_for', array('id' => 'AppliedFor', 'class' => 'fs13', 'label' => 'Applied For: ', 'type' => 'select', /* 'default' => $selectedCurrentUnit, */  'style' => 'width:95%;', 'onchange' => 'updateForeignKey()', 'options' => $appliedForList,  /* 'options' => $currentUnits, */ /* 'empty' => "[ Select Current Unit ]", */)); ?>
								</div>
								<div class="large-4 columns">
									<?= $this->Form->input('PlacementPreference.placement_round_participant_id', array('id' => 'Department', 'class' => 'fs13', 'label' => 'Preferered Unit: ', 'type' => 'select',  'style' => 'width:90%;', 'options' => $preferredUnits, /*  'options' => $allUnits, */ 'empty' => "[ Select Unit ]", 'required')); ?>
								</div>
								<div class="large-2 columns">
									<?= $this->Form->input('PlacementPreference.preference_order', array('options' => $preferenceOrderList, 'label' => 'Preference Order: ',  'style' => 'width:90%;')); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-2 columns">
									<?= $this->Form->input('PlacementPreference.limit', array('id' => 'Limit ', 'type' => 'number', 'min'=>'100',  'max'=>'1000', 'value' => $selectedLimit, 'step'=>'100', 'class' => 'fs13', 'label' =>' Limit: ', 'style' => 'width:90%;')); ?>
									<?= $this->Form->hidden('PlacementPreference.page', array('value' => $page)); ?>
									<?= $this->Form->hidden('PlacementPreference.sort', array('value' => $sort)); ?>
									<?= $this->Form->hidden('PlacementPreference.direction', array('value' => $direction)); ?>
								</div>
								<div class="large-10 columns">
									&nbsp;
								</div>
							</div>
							<hr>
							<?= $this->Form->submit(__('List Students Preference', true), array('name' => 'listStudentsPreference', 'id' => 'Search', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
						</fieldset>
						<?php
					} ?>

					<div id="preference_list">
						<?php
						$dealinePassed = true;

						if (isset($preference_deadline['PlacementDeadline']['deadline'])) {
							$date_now = date('Y-m-d H:i:s');
							if ($date_now < $preference_deadline['PlacementDeadline']['deadline'] && $preference_deadline['PlacementDeadline']['placement_round'] == $placement_preferences[0]['PlacementPreference']['round']) {
								$dealinePassed = false;
							} else if ($date_now < $preference_deadline['PlacementDeadline']['deadline'] && $preference_deadline['PlacementDeadline']['academic_year'] == $placement_preferences[0]['PlacementPreference']['academic_year'] /* && $preference_deadline['PlacementDeadline']['placement_round'] == ($placement_preferences[0]['PlacementPreference']['round'] - 1) */) {
								$dealinePassed = false;
							}
						}

						if ($role_id == ROLE_STUDENT && $freshman) {
							if ($deadLineStatus == 0) { 
								// flash message will be presented if the student is redirected from record_preference page
							} else if (isset($preference_deadline['PlacementDeadline']['deadline']) && $deosTheStudentHaveAnySectionAssignment) {
								if (!$dealinePassed) { ?>
									<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>You can fill or update your placement preference <?= (isset($acYear) && isset($roundLebel) ? ' for ' . $acYear . ' academic year ' . $roundLebel . ' round ' : ''); ?> before the deadline, <?= $this->Time->format("F j, Y g:i:s A", $preference_deadline['PlacementDeadline']['deadline'], NULL, NULL); ?> </div>
									<?php
								} else { ?>
									<div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Deadline to fill or change your placement preference <?= (isset($acYear) && isset($roundLebel) ? ' for ' . $acYear . ' academic year ' . $roundLebel . ' round ' : ''); ?> was on <?= $this->Time->format("F j, Y g:i:s A", $preference_deadline['PlacementDeadline']['deadline'], NULL, NULL); ?> and it is now closed. <br>You can advise the registrar for further information.</div>
									<?php
								}
							} else if (!$deosTheStudentHaveAnySectionAssignment) { ?>
								<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>You don't have section assignment and you can't fill preference at this time.</div>
								<?php
							} else {
								if (!isset($preference_deadline['PlacementDeadline']['deadline'])) { ?>
									<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no placement preference deadline defined <?= (isset($acYear) && isset($roundLebel) ? ' for ' . $acYear . ' academic year ' . $roundLebel . ' round ' : ''); ?>  for now. Advise the registrar for more information or come again after the registrar announcement.</div>
									<?php
								}
							} 
						} ?>
					</div>

					<?php
					//debug($_POST);
					
					if (isset($placement_preferences) && !empty($placement_preferences)) { 

						if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) { 
							debug($preference_deadline);
							
							//debug($placement_preferences); ?>

							<fieldset style="padding-bottom: 5px;">
								<legend>&nbsp;&nbsp; Student and Placement Round Details &nbsp;&nbsp;</legend>
								<div class="large-12 columns">
									<strong class="fs14 text-black">
										Name: &nbsp;<?= $placement_preferences[0]['AcceptedStudent']['full_name']; ?> <br>
										Student ID: &nbsp;<?= $placement_preferences[0]['AcceptedStudent']['studentnumber']; ?> <br>
										Sex: <?= ((strcasecmp(trim($placement_preferences[0]['Student']['gender']), 'male') == 0) ? 'M' : ((strcasecmp(trim($placement_preferences[0]['Student']['gender']), 'female') == 0) ? 'F': trim($placement_preferences[0]['Student']['gender']))); ?> <br>
										Admission Year: &nbsp;<?= $placement_preferences[0]['Student']['academicyear']; ?> <br>
										Current Placement: &nbsp;<?= (!empty($placement_preferences[0]['Student']['department_id']) && $placement_preferences[0]['Student']['department_id'] ? $departmentsList[$placement_preferences[0]['Student']['department_id']] : $collegesList[$placement_preferences[0]['Student']['college_id']] . ($placement_preferences[0]['Student']['program_id'] == PROGRAM_REMEDIAL ? ' Remedial' : ' Pre/Freshman')); ?> <br>
										<hr>
										Placement ACY: &nbsp;<?= $placement_preferences[0]['PlacementPreference']['academic_year']; ?> <br>
										Placement Round: &nbsp;<?= $placement_preferences[0]['PlacementPreference']['round']; ?> <br>
										<?php
										if (isset($preference_deadline['PlacementDeadline']['deadline']) && !$dealinePassed && $preference_deadline['PlacementDeadline']['academic_year'] == $placement_preferences[0]['PlacementPreference']['academic_year']  && $preference_deadline['PlacementDeadline']['placement_round'] == $placement_preferences[0]['PlacementPreference']['round']) {
											echo '<hr>';
											echo $this->Html->link(__('Edit My '. $placement_preferences[0]['PlacementPreference']['academic_year']. ' round ' . $placement_preferences[0]['PlacementPreference']['round'] .' Preferences'), array('action' => 'record_preference', $placement_preferences[0]['PlacementPreference']['id']), array('class' => 'tiny radius button bg-blue')) ;
										} else {
											echo '<br>';
										} ?>
									</strong>
								</div>
							</fieldset>

							<h6 class="text-gray"><?= __('List of your Placement Preferences'); ?></h6><br/>
							
							<div style="overflow-x:auto;">
								<table cellspacing="0" cellpadding="0" class="table">
									<thead>
										<tr>
											<td class="center" style="width: 5%">#</td>
											<td class="center" style="width: 10%;">ACY</td>
											<td class="center" style="width: 10%;">Round</td>
											<td class="center" style="width: 10%;">Order</td>
											<td class="vcenter">Placement Choice</td>
										</tr>
									</thead>
									<tbody>
										<?php
										$start = $this->Paginator->counter('%start%');
										foreach ($placement_preferences as $preference) { ?>
											<tr>
												<td class="center"><?= $start++; ?></td>
												<td class="center"><?= $preference['PlacementPreference']['academic_year']; ?></td>
												<td class="center"><?= $preference['PlacementPreference']['round']; ?></td>
												<td class="center"><?= $preference['PlacementPreference']['preference_order']; ?></td>
												<td class="vcenter"><?= (!is_null($preference['PlacementRoundParticipant']['name']) ? $preference['PlacementRoundParticipant']['name'] : '[ Not Selected ]'); ?> </td>
											</tr>
											<?php 
										} ?>
									</tbody>
								</table>
							</div>
							<?php
						} else { ?>
							<h6 class="text-gray"><?= __('List of Students Placement Preferences'); ?></h6><br/>
							<div style="overflow-x:auto;">
								<table cellspacing="0" cellpadding="0" class="table">
									<thead>
										<tr>
											<td class="center">#</td>
											<td class="vcenter">Full Name</td>
											<td class="center">Sex</td>
											<td class="center">Student ID</td>
											<td class="center"><?= $this->Paginator->sort('academicyear', 'ACY'); ?></td>
											<td class="center"><?= $this->Paginator->sort('round', 'Round'); ?></td>
											<td class="center"><?= $this->Paginator->sort('preferences_order', 'Order'); ?></td>
											<td class="center"><?= $this->Paginator->sort('placement_round_participant_id', 'Placement Choice'); ?></td>
											<td class="center">&nbsp;</td>
										</tr>
									</thead>
									<tbody>
										<?php
										$start = $this->Paginator->counter('%start%');
										foreach ($placement_preferences as $preference) { ?>
											<tr>
												<td class="center"><?= $start++; ?></td>
												<td class="vcenter"><?= $preference['AcceptedStudent']['full_name']; ?> </td>
												<td class="center"><?= ((strcasecmp(trim($preference['Student']['gender']), 'male') == 0) ? 'M' : ((strcasecmp(trim($preference['Student']['gender']), 'female') == 0) ? 'F': trim($preference['Student']['gender']))); ?></td>
												<td class="center"><?= $this->Html->link($preference['AcceptedStudent']['studentnumber'], array('controller' => 'AcceptedStudents', 'action' => 'view', $preference['AcceptedStudent']['id'])); ?> </td>
												<td class="center"><?= $preference['PlacementPreference']['academic_year']; ?></td>
												<td class="center"><?= $preference['PlacementPreference']['round']; ?></td>
												<td class="center"><?= $preference['PlacementPreference']['preference_order']; ?></td>
												<td class="center"><?= $preference['PlacementRoundParticipant']['name']; ?> </td>
												<td  class="center">
													<?php
													if ($role_id == ROLE_STUDENT) {
														if (isset($preference_deadline['PlacementDeadline']['deadline']) && !$dealinePassed) {
															echo $this->Html->link(__('Edit'), array('action' => 'record_preference', $preference['PlacementPreference']['id'])) ;
														}
													} else if ($role_id == ROLE_SYSADMIN || $role_id == ROLE_REGISTRAR) {
														//echo $this->Html->link(__('Delete'), array('action' => 'delete', $preference['PlacementPreference']['id']), null, sprintf(__('Are you sure you want to delete  %s preferences ?'), $preference['AcceptedStudent']['full_name']));
													} ?>
												</td>
											</tr>
											<?php 
										} ?>
									</tbody>
								</table>
							</div>
							<?php
						} ?>

						<hr>
						<div class="row">
							<div class="large-5 columns">
								<?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total'))); ?>
							</div>
							<div class="large-7 columns">
								<div class="pagination-centered">
									<ul class="pagination">
										<?= $this->Paginator->prev('<< ' . __(''), array('tag' => 'li'), null, array('class' => 'arrow unavailable')); ?> <?= $this->Paginator->numbers(array('separator' => '', 'tag' => 'li')); ?> <?= $this->Paginator->next(__('') . ' >>', array('tag' => 'li'), null, array('class' => 'arrow unavailable')); ?>
									</ul>
								</div>
							</div>
						</div>
						<?php 
					}  ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function updateForeignKey() {
		var formUrl = '/PlacementRoundParticipants/get_selected_participant_unit';
		$.ajax({
			type: 'POST',
			url: formUrl,
			data: $('form').serialize(),
			success: function(response) {
				$("#Department").attr('disabled', false);
				$("#Department").empty();
				$("#Department").append(response);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}

	function updateDepartmentListOnChangeofOtherField() {
		var formData = '';
		var academic_year = $("#Academicyear").val().replace("/", "-");
		if (typeof academic_year != "undefined") {
			formData = academic_year;
		} else {
			return false;
		}

		$("#Department").attr('disabled', true);
		$("#Search").attr('disabled', true);
		var formUrl = '/participatingDepartments/getParticipatingDepartment/' + formData;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data, textStatus, xhr) {
				$("#Academicyear").attr('disabled', false);
				$("#Department").attr('disabled', false);
				$("#Search").attr('disabled', false);
				$("#Department").empty();
				$("#Department").append(data);

			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}
</script>