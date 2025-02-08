<?php
echo $this->Form->Create('PlacementSetting');
?>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div class="courseRegistrations index">

					<div class="smallheading"><?php echo __('Placement Report View '); ?></div>

					<table cellspacing="0" cellpadding="0" class="fs14">
						<tr>
							<td style="width:10%">Academic Year:</td>
							<td style="width:15%"><?php

																		echo $this->Form->input('PlacementSetting.academic_year', array(
																			'class' => 'AYS',
																			'id' => 'AcademicYear', 'label' => false, 'style' => 'width:100px', 'onchange' => 'appliedFor()',
																			'type' => 'select', 'options' => $acyear_array_data,
																			'default' => isset($this->request->data['PlacementSetting']['academic_year']) ? $this->request->data['PlacementSetting']['academic_year'] : (isset($defaultacademicyear) ? $defaultacademicyear : '')

																		)); ?></td>
							<td style="width:10%">Placement Round:</td>
							<td style="width:15%"><?php echo $this->Form->input('PlacementSetting.round', array(
																			'class' => 'PlacementRound',
																			'onchange' => 'appliedFor()',														'id' => 'PlacementRound', 'label' => false, 'style' => 'width:100px', 'type' => 'select',
																			'options' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4')
																		)); ?></td>
							<td style="width:17%">Applied For Those Student In :</td>
							<td style="width:23%">
								<?php
								echo $this->Form->input('PlacementSetting.applied_for', array(
									'options' => $allUnits, 'id' => 'AppliedFor', 'type' => 'select', 'label' => false, 'empty' => '--Select--', 'style' => 'width:250px',
									'onchange' => 'appliedFor()',
								));
								?>
							</td>
						</tr>
						<tr>
							<td style="width:10%">Admission Level:</td>
							<td style="width:15%"><?php echo $this->Form->input('PlacementSetting.program_id', array(
																			'onchange' => 'appliedFor()',
																			'class' => 'AYS',
																			'id' => 'ProgramId', 'label' => false, 'style' => 'width:100px',
																			'type' => 'select', 'options' => $programs
																		)); ?></td>
							<td style="width:10%">Admission Type:</td>
							<td style="width:15%"><?php echo $this->Form->input('PlacementSetting.program_type_id', array(

																			'class' => 'AYS', 'id' => 'ProgramTypeId', 'onchange' => 'appliedFor()', 'label' => false, 'style' => 'width:100px', 'type' => 'select',
																			'options' => $programTypes
																		)); ?></td>
							<td style="width:20%">Assigned To:</td>
							<td style="width:20%"><?php echo $this->Form->input(
																			'assigned_to',
																			array('id' => 'AssignedTo', 'type' => 'select', 'label' => false)
																		); ?></td>

						</tr>
						<tr>
							<td style="width:15%">Placement Based:</td>
							<td style="width:20%"><?php echo $this->Form->input(
																			'placement_based',
																			array('options' => array('all' => 'All', 'C' => 'Competitive', 'Q' => 'Quota'), 'label' => false)
																		);  ?></td>
							<td style="width:15%">Assignment Type:</td>
							<td style="width:20%"><?php echo $this->Form->input(
																			'placementtype',
																			array('options' => array('all' => 'All', 'AUTO PLACED' => 'AUTO PLACED', 'DIRECT PLACED' => 'DIRECT PLACED', 'REGISTRAR PLACED' => 'REGISTRAR PLACED', 'CANCELLED PLACEMENT' => 'CANCELLED PLACEMENT'), 'label' => false)
																		); ?></td>

						</tr>


						<tr>

							<td colspan="6">
								<?php echo $this->Form->Submit('Search', array(
									'div' => false,
									'name' => 'Search', 'class' => 'tiny radius button bg-blue'
								));
								?>
							</td>
						</tr>
					</table>


					<?php
					if (isset($autoplacedstudents) && !empty($autoplacedstudents)) {

						$summery = $autoplacedstudents['auto_summery'];
						unset($autoplacedstudents['auto_summery']);


						echo "<table><tbody><tr><td>" . $this->Form->submit('Generate PDF', array('class' => 'tiny radius button bg-blue', 'name' => 'generatePlacedList', 'div' => false, 'id' => 'generatePlacedList')) . "</td></tr></tbody></table>";
						echo "<table><tbody>";
						echo "<tr><th colspan=5> Summery of Auto Placement.</th></tr>";
						echo "<tr><th>Department</th><th>Competitive</th><th> Privilaged Quota</th><th>Female By Quota</th><th>Female By Competition</th>";
						foreach ($summery as $sk => $sv) {
							echo "<tr><td>" . $sk . "</td><td>" . $sv['C'] . "</td><td>" . $sv['Q'] . '</td>
<td>' . $sv['QF'] . '</td><td>' . $sv['CF'] . '</td>';
						}
						echo "</tbody></table>";

						foreach ($autoplacedstudents as $key => $data) {
					?>
							<table>
								<tr>
									<td colspan=12 class="headerfont"><?php echo $key ?></td>
								</tr>
								<tr>
									<th><?php echo ('S.No'); ?></th>
									<th><?php echo ('Full Name'); ?></th>
									<th><?php echo ('Sex'); ?></th>
									<th><?php echo ('Student Number'); ?></th>
									<th><?php echo ('Total Placement Weight'); ?></th>

									<th><?php echo ('Preference Order'); ?></th>
									<th><?php echo ('Assigned To'); ?></th>
									<th><?php echo ('Academic Year'); ?></th>
									<th><?php echo ('Approval'); ?></th>
									<th><?php echo ('Placement Type '); ?></th>
									<th><?php echo ('Placement Based'); ?></th>

								</tr>
								<?php
								$i = 0;
								$count = 1;
								foreach ($data as $acceptedStudent) :
									$class = null;
									if ($i++ % 2 == 0) {
										$class = ' class="altrow"';
									}
								?>
									<tr<?php echo $class; ?>>
										<td><?php echo $count++; ?>&nbsp;</td>
										<td><?php echo $acceptedStudent['AcceptedStudent']['full_name']; ?>&nbsp;</td>
										<td><?php echo $acceptedStudent['AcceptedStudent']['sex']; ?>&nbsp;</td>
										<td><?php echo $acceptedStudent['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
										<td><?php echo $acceptedStudent['PlacementParticipatingStudent']['total_placement_weight']; ?>&nbsp;</td>

										<td><?php
												if (!empty($acceptedStudent['AcceptedStudent']['PlacementPreference'])) {
													foreach ($acceptedStudent['AcceptedStudent']['PlacementPreference'] as $key => $value) {
														if ($value['placement_round_participant_id'] == $acceptedStudent['PlacementRoundParticipant']['id']) {
															echo $value['preference_order'];
															break;
														}
													}
												}
												?>&nbsp;</td>

										<td><?php echo $acceptedStudent['PlacementRoundParticipant']['name']; ?>&nbsp;</td>
										<td><?php echo $acceptedStudent['PlacementParticipatingStudent']['academic_year'];  ?>&nbsp;</td>

										<td><?php echo isset($acceptedStudent['PlacementParticipatingStudent']['status']) ? 'Yes' : 'No'; ?>&nbsp;</td>

										<td><?php echo $acceptedStudent['PlacementParticipatingStudent']['placementtype']; ?>&nbsp;</td>
										<td><?php echo $acceptedStudent['PlacementParticipatingStudent']['placement_based'] == 'C' ? 'Competitive' : 'Quota'; ?>&nbsp;</td>
										</tr>
									<?php endforeach; ?>
							</table>

					<?php
						}
					}

					?>

				</div>
			</div> <!-- end of columns 12 -->
		</div> <!-- end of row -->
	</div> <!-- end of box-body -->
</div><!-- end of box -->


<?php

echo $this->Form->end();
?>

<script type="text/javascript">
	function appliedFor() {
		$("#AssignedTo").empty();

		//get form action
		var formUrl = '/PlacementRoundParticipants/get_selected_participant_unit/PlacementSetting';
		$.ajax({
			type: 'POST',
			url: formUrl,
			data: $('form').serialize(),
			success: function(data, textStatus, xhr) {

				$("#AssignedTo").empty();
				$("#AssignedTo").append(data);

				$("#AppliedFor").attr('disabled', false);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});

		return false;
	}

	function refreshDiv(chkPassport) {
		appliedFor();
	}
	$(document).ready(function() {
		refreshDiv();
	});
</script>