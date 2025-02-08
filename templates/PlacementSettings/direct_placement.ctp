<?php
echo $this->Form->Create('PlacementSetting');
?>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<h3>Direct/Manual Student Placement to Department</h3>
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
																		array('options' => array('all' => 'All', 'AUTO PLACED' => 'AUTO PLACED', 'DIRECT PLACED' => 'DIRECT PLACED'), 'label' => false)
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
				if (!empty($acceptedStudents)) {
				?>
					<div class="acceptedStudents index">
						<h2><?php echo __('Select department'); ?></h2>

						<?php
						echo $this->Form->create('PlacementSetting', array('id' => 'directplacementform'));
						?>
						<table cellpadding="0" cellspacing="0">
							<tbody>
								<tr>
									<td> <?php
												echo $this->Form->input('PlacementDirectly.placement_round_participant_id', array(
													'id' => 'department_id', 'type' => 'select',
													'options' => $units, 'empty' => '--Select Assignment Unit--', 'selected' => isset($selecteddepartment) ? $selecteddepartment : ''
												));

												//echo $this->Form->input('department_id');

												?>

									</td>
								</tr>
							</tbody>
						</table>
						<table cellpadding="0" cellspacing="0">
							<tr>
								<th>
									<?php echo 'Select/Unselect All <br/>' . $this->Form->checkbox('selectall', array('id' => 'select-all')); ?> </th>

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
							//debug($acceptedStudents);
							unset($acceptedStudents['auto_summery']);
							//Building every student exam result entry
							$st_count = 1;
							$visibleTheButton = false;
							foreach ($acceptedStudents as
								$key => $student) {
								//debug($student);
							?>
								<tr>


									<td><?php echo $st_count; ?></td>
									<td>
										<?php
										//echo $this->Form->checkbox('AcceptedStudent.directplacement.' . $acceptedStudent['AcceptedStudent']['id'],array('disabled'=>$acceptedStudent['AcceptedStudent']['Placement_Approved_By_Department']==1?true:false,'class'=>'checkbox1')); 
										?>
										<?php

										echo $this->Form->checkbox('PlacementDirectly.approve.' . $student['PlacementParticipatingStudent']['id'], array('class' => 'checkbox1'));
/*
										echo $this->Form->hidden('PlacementParticipatingStudent.' . $st_count . '.id', array('value' =>
										$student['PlacementParticipatingStudent']['id']));
*/
										?>
									</td>


									<td>

										<?php
										echo $student['Student']['full_name']; ?>

									</td>
									<td><?php echo $student['Student']['studentnumber']; ?></td>
									<td><?php echo $student['AcceptedStudent']['sex']; ?></td>


								
					<td><?php echo $student['PlacementParticipatingStudent']['total_placement_weight']; ?>&nbsp;</td>

										<td><?php
												if (!empty($student['AcceptedStudent']['PlacementPreference'])) {
													foreach ($student['AcceptedStudent']['PlacementPreference'] as $key => $value) {
														if ($value['placement_round_participant_id'] == $student['PlacementRoundParticipant']['id']) {
															echo $value['preference_order'];
															break;
														}
													}
												}
												?>&nbsp;</td>

										<td><?php echo $student['PlacementRoundParticipant']['name']; ?>&nbsp;</td>
										<td><?php echo $student['PlacementParticipatingStudent']['academic_year'];  ?>&nbsp;</td>

										<td><?php echo isset($student['PlacementParticipatingStudent']['status']) ? 'Yes' : 'No'; ?>&nbsp;</td>

										<td><?php echo $student['PlacementParticipatingStudent']['placementtype']; ?>&nbsp;</td>
										<td><?php echo $student['PlacementParticipatingStudent']['placement_based'] == 'C' ? 'Competitive' : 'Quota'; ?>&nbsp;</td>

									<?php

									$st_count++;

									?>

								</tr>

							<?php } ?>
							<tr>
								<td colspan="5">

									<?php echo $this->Form->Submit('Assign To Selected Department', array(
										'div' => false,
										'name' => 'assigndirectly', 'class' => 'tiny radius button bg-blue'
									));
									?>
								</td>
							</tr>
						</table>

					</div>
				<?php
				} else {
					echo "<div class='info-box info-message'><span></span>No  students in the selected academic year</div>";
				}
				?>
			</div> <!-- end of columns 12 -->
		</div> <!-- end of row -->
	</div> <!-- end of box-body -->
</div><!-- end of box -->


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
