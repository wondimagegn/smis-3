<?php ?>

<script language="javascript">
	var totalRow = <?php if (!empty($this->request->data)) echo (count($this->request->data['PlacementAdditionalPoint']));
									else if (!empty($exam_types)) echo (count($exam_types));
									else echo 2; ?>;

	function updateSequence(tableID) {
		var s_count = 1;
		for (i = 1; i < document.getElementById(tableID).rows.length; i++) {
			document.getElementById(tableID).rows[i].cells[0].childNodes[0].data = s_count++;
		}
	}

	function addRow(tableID, model, no_of_fields, all_fields) {

		var elementArray = all_fields.split(',');
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		totalRow++;
		row.id = model + '_' + totalRow;
		var cell0 = row.insertCell(0);
		cell0.innerHTML = rowCount;

		//construct the other cells
		for (var j = 1; j <= no_of_fields; j++) {

			var cell = row.insertCell(j);

			if (elementArray[j - 1] == 'point') {
				var element = document.createElement("input");
				//element.size = "4";
				element.type = "number";
				element.required = "required";
				element.min = "1";
				element.max = "10";
				element.style = "width:70px";


			} else if (elementArray[j - 1] == "type") {
				var element = document.createElement("select");
				string = "";

				string += '<option value="female"> Female Point</option>';
				string += '<option value="disability"> Disability Point</option>';
				string += '<option value="developing_region"> Developing Region Point</option>';
				
				
				element.id = "PlacementType_" + rowCount;
				element.innerHTML = string;
				element.required = "required";

			} else if (elementArray[j - 1] == "edit") {
				var element = document.createElement("a");
				element.innerText = "Delete";
				element.textContent = "Delete";
				element.setAttribute('href', 'javascript:deleteSpecificRow(\'' + model + '_' + totalRow + '\')');
				element.required = "required";
			}

			element.name = "data[" + model + "][" + rowCount + "][" + elementArray[j - 1] + "]";
			cell.appendChild(element);
		}

		updateSequence(tableID);
	}

	function deleteRow(tableID) {
		try {
			var table = document.getElementById(tableID);
			var rowCount = table.rows.length;
			if (rowCount > 2) {
				table.deleteRow(rowCount - 1);
				updateSequence(tableID);
			} else {
				alert('No more rows to delete');
			}
		} catch (e) {
			alert(e);
		}
	}

	function deleteSpecificRow(id) {
		try {
			var row = document.getElementById(id);
			//var table = row.parentElement;
			var table = row.parentNode;
			if (table.rows.length > 2) {
				row.parentNode.removeChild(row);
				updateSequence('PlacementRoundType');
				//row.parentElement.removeChild(row);
			} else {
				alert('There must be at least one participant type.');
			}
		} catch (e) {
			alert(e);
		}
	}
</script>
<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div class="examTypes form">
					<?php echo $this->Form->create('PlacementSetting'); ?>
					<div class="smallheading"><?php echo __('Placement Additional Points.'); ?></div>
					<table cellspacing="0" cellpadding="0" class="fs14">
						<tr>
							<td style="width:10%">Academic Year:</td>
							<td style="width:15%"><?php

																		echo $this->Form->input('PlacementAdditionalPoint.1.academic_year', array(
																			'class' => 'AYS',
																			'id' => 'AcademicYear', 'label' => false, 'style' => 'width:100px',
																			'type' => 'select', 'options' => $acyear_array_data,
																			'default' => isset($this->request->data['PlacementAdditionalPoint'][1]['academic_year']) ? $this->request->data['PlacementAdditionalPoint'][1]['academic_year'] : (isset($defaultacademicyear) ? $defaultacademicyear : '')

																		)); ?></td>
							<td style="width:10%">Placement Round:</td>
							<td style="width:15%"><?php echo $this->Form->input('PlacementAdditionalPoint.1.round', array(
																			'class' => 'PlacementRound',
																			'id' => 'PlacementRound', 'label' => false, 'style' => 'width:100px', 'type' => 'select',
																			'options' => array('1' => '1', '2' => '2', '3' => '3')
																		)); ?></td>
							<td style="width:17%">Applied For Those Student In :</td>
							<td style="width:23%">
								<?php
								echo $this->Form->input('PlacementAdditionalPoint.1.applied_for', array('options' => $allUnits, 'id' => 'AppliedFor', 'type' => 'select', 'label' => false, 'empty' => '--Select--', 'style' => 'width:250px'));
								?>
							</td>
						</tr>
						<tr>
							<td style="width:10%">Admission Level:</td>
							<td style="width:15%"><?php echo $this->Form->input('PlacementAdditionalPoint.1.program_id', array(
																			'class' => 'AYS',
																			'id' => 'ProgramId', 'label' => false, 'style' => 'width:100px',
																			'type' => 'select', 'options' => $programs
																		)); ?></td>
							<td style="width:10%">Admission Type:</td>
							<td style="width:15%"><?php echo $this->Form->input('PlacementAdditionalPoint.1.program_type_id', array(
																			'class' => 'AYS', 'id' => 'ProgramTypeId', 'label' => false, 'style' => 'width:100px', 'type' => 'select',
																			'options' => $programTypes
																		)); ?></td>

							<td style="width:17%">&nbsp;</td>
							<td style="width:23%">&nbsp;</td>

						</tr>

					</table>
					<table cellspacing="0" cellpadding="0" id="participant_setup" style="margin-bottom:5px">
						<tr>
							<th style="width:5%">No</th>
							<th style="width:25%">Type</th>
							<th style="width:20%">Point </th>

							<th style="width:20%">&nbsp;</th>
						</tr>
						<?php
						if (empty($this->request->data)) {

						?>
							<tr id="PlacementRoundType_1">
								<td style="vertical-align:middle">1</td>
								<td><?php

										echo $this->Form->input('PlacementAdditionalPoint.1.type', array(
											'label' => false, 'id' => 'PlacementType_1', 'options' => $types
										)); ?></td>
								<td><?php echo $this->Form->input('PlacementAdditionalPoint.1.point', array(
											'label' => false, 'id' => 'PlacementPoint_1', 'type' => "number", 'min' => "1",
											'max' => "10", 'step' => "1",
											'style' => 'width:70px',
										)); ?></td>


								<td><a href="javascript:deleteSpecificRow('PlacementRoundType_1')">Delete</a></td>
							</tr>
							<?php

						} else {
							$count = 1;
							foreach ($this->request->data['PlacementAdditionalPoint'] as $key => $placementType) {
								if (!$lockEditing) {
							?>
									<tr id="PlacementRoundType_<?php echo $count; ?>">
										<td style="vertical-align:middle"><?php echo ($count); ?></td>
										<td><?php if (isset($placementType['id'])) echo $this->Form->input('PlacementAdditionalPoint.' . $key . '.id', array('type' => 'hidden')); ?>
											<?php echo $this->Form->input('PlacementAdditionalPoint.' . $key . '.type', array('label' => false, 'options' => $types)); ?></td>


										<td>
											<?php echo $this->Form->input('PlacementAdditionalPoint.' . $key . '.point', array('label' => false)); ?></td>



										<td><a href="javascript:deleteSpecificRow('PlacementRoundType_<?php echo $count++; ?>')">Delete</a></td>
									</tr>
								<?php
								} else {
								?>
									<tr id="PlacementRoundType_<?php echo $count; ?>">
										<td style="vertical-align:middle"><?php echo ($count); ?></td>
										<td>
											<?php echo $types[$placementType['type']]; ?></td>


										<td>
											<?php echo $placementType['point']; ?></td>



										<td></td>
									</tr>

						<?php
								}
							}
						}
						?>

					</table>
					<p><input type="button" value="Add Row" onclick="addRow('participant_setup', 'PlacementAdditionalPoint',3, '<?php echo $fieldSetups; ?>')" /></p>

					<?php
					echo $this->Form->submit(
						__('Submit'),
						array('name' => 'saveIt', 'class' => 'tiny radius button bg-blue', 'div' => false)
					); ?>

					<?php echo $this->Form->end(); ?>
				</div>
			</div> <!-- end of columns 12 -->
		</div> <!-- end of row --->
	</div> <!-- end of box-body -->
</div><!-- end of box -->
