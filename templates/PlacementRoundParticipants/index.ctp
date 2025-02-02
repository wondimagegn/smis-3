<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-search" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Placement Round Participants'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns" style=" margin-top: -30px;">
				<hr>
				<?= $this->Form->create('PlacementRoundParticipant'); ?>
				<fieldset style="padding-bottom: 5px; padding-top: 15px;">
					<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
					<div class="row">
						<div class="large-3 columns">
							<?= $this->Form->input('PlacementRoundParticipant.academic_year', array('id' => 'AcademicYear', 'label' => 'Academic Year: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $acyear_array_data, 'default' => isset($this->request->data['PlacementRoundParticipant']['academic_year']) ? $this->request->data['PlacementRoundParticipant']['academic_year'] : (isset($defaultacademicyear) ? $defaultacademicyear : ''))); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('PlacementRoundParticipant.placement_round', array('id' => 'PlacementRound', 'label' => 'Placement Round: ', 'style' => 'width:80%;', 'type' => 'select', 'options' => Configure::read('placement_rounds'), 'default' => (isset($this->request->data['PlacementRoundParticipant']['placement_round']) ? $this->request->data['PlacementRoundParticipant']['placement_round'] : (isset($latestACYRoundAppliedFor) ? $latestACYRoundAppliedFor['round'] : '')))); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('PlacementRoundParticipant.program_id', array('id' => 'ProgramId', 'label' => 'Program: ',  'style' => 'width:90%;', 'type' => 'select', 'options' => $programs)); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('PlacementRoundParticipant.program_type_id', array('id' => 'ProgramTypeId', 'label' => 'Program Type: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programTypes)); ?>
						</div>
					</div>
					<div class="row">
						<div class="large-6 columns">	
							<?= $this->Form->input('PlacementRoundParticipant.applied_for', array('options' => $appliedForList /*  $allUnits */, 'id' => 'AppliedFor', 'type' => 'select', 'label' => 'Applied for those Students in: ', 'empty' => '[ Select Applied Unit ]', 'style' => 'width:90%;')); ?>
						</div>
					</div>
					<hr>
					<?= $this->Form->submit(__('Search'), array('name' => 'search', 'class' => 'tiny radius button bg-blue', 'div' => false) ); ?>
				</fieldset>

				<?php
				if (!empty($placementRoundParticipants)) { 

					if (!empty($placementRoundParticipants[0]['PlacementRoundParticipant']['group_identifier'])) {

						$participantIDs = ClassRegistry::init('PlacementRoundParticipant')->get_placement_participant_ids_by_group_identifier($placementRoundParticipants[0]['PlacementRoundParticipant']['group_identifier']);
						
						$isThereAnyPreferenceFilledByStudents = ClassRegistry::init('PlacementPreference')->find('count', array(
							'conditions' => array(
								'PlacementPreference.round' => $placementRoundParticipants[0]['PlacementRoundParticipant']['placement_round'],
								'PlacementPreference.academic_year LIKE ' => $placementRoundParticipants[0]['PlacementRoundParticipant']['academic_year'] . '%',
								'PlacementPreference.placement_round_participant_id' => $participantIDs
							)
						));

						//debug($participantIDs);
						//debug($isThereAnyPreferenceFilledByStudents);
						
						?>
						
						<hr>
						<div class="row">
							<?php
							if ($isThereAnyPreferenceFilledByStudents == 0) { ?>
								<div class="large-3 columns">
									<?= $this->Html->link(__('Edit Round Participants'), array('action' => 'edit', $placementRoundParticipants[0]['PlacementRoundParticipant']['group_identifier']), array('class' => 'tiny radius button bg-blue')); ?>
								</div>
								<?php
							} ?>
							<div class="large-3 columns">
								<?= $this->Html->link(__('Quota Settings'), array( 'controller' => 'placement_settings', 'action' => 'quota', $placementRoundParticipants[0]['PlacementRoundParticipant']['group_identifier']), array('class' => 'tiny radius button bg-blue')); ?>
							</div>
							<div class="large-6 columns">
								&nbsp;
							</div>
						</div>
						<?php
					} ?>
					<hr>

					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="fs14 table-borderless">
							<thead>
								<tr>
									<td class="center">#</td>
									<td class="center">Type</td>
									<td class="vcenter">Display Name</td>
									<td class="center">ACY</td>
									<td class="center">Round</td>
									<td class="center">&nbsp;</td>
								</tr>
							</thead>
							<tbody>
								<?php
								$count = 1;
								foreach ($placementRoundParticipants as $placementRoundParticipant) { ?>
									<tr>
										<td class="center"><?= $count++; ?>&nbsp;</td>
										<td class="center"><?= $placementRoundParticipant['PlacementRoundParticipant']['type']; ?></td>
										<td class="vcenter"><?= $placementRoundParticipant['PlacementRoundParticipant']['name']; ?></td>
										<td class="center"><?= $placementRoundParticipant['PlacementRoundParticipant']['academic_year']; ?></td>
										<td class="center"><?= $placementRoundParticipant['PlacementRoundParticipant']['placement_round']; ?></td>
										<td class="center">
											<?php //echo $this->Html->link(__('Edit'), array('controller' => 'placement_round_participants', 'action' => 'edit', $placementRoundParticipant['PlacementRoundParticipant']['group_identifier'])); ?><!--  &nbsp;&nbsp;&nbsp; -->
											<?php //echo $this->Html->link(__('Settings'), array('controller' => 'placement_settings', 'action' => 'quota', $placementRoundParticipant['PlacementRoundParticipant']['group_identifier'])); ?><!--  &nbsp;&nbsp;&nbsp; -->
											<?php
											if ($isThereAnyPreferenceFilledByStudents == 0) {
												$this->Form->postLink(__('Delete'), array('action' => 'delete', $placementRoundParticipant['PlacementRoundParticipant']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $placementRoundParticipant['PlacementRoundParticipant']['id'])));
											} ?>
										</td>
									</tr>
									<?php
								} ?>
							</tbody>
						</table>
					</div>
					<br>
					<?php
				} if (empty($placementRoundParticipants) && isset($this->request->data['search'])) {
					echo '<div class= "info-box info-message"><span></span> &nbsp;&nbsp; No recorded placement round participant found for <i><u>'. ($this->request->data['PlacementRoundParticipant']['placement_round'] == '1' ? '1st' : ($this->request->data['PlacementRoundParticipant']['placement_round'] == '2' ? '2nd' : '3rd')) . ' placement round of ' . (isset($this->request->data['PlacementRoundParticipant']['academic_year']) ? $this->request->data['PlacementRoundParticipant']['academic_year'] : (isset($defaultacademicyear) ? $defaultacademicyear : '')) . '</u></i> ACY for the selected College or Department. </div>';
				} ?>
			</div>
		</div>
	</div>
</div>