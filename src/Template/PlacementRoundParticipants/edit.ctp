<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Edit Placement Round Participants'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				
				<?php echo $this->Form->create('PlacementRoundParticipant', array('onSubmit' => 'return checkForm(this);')); ?>
				<div style="margin-top: -30px;">
					<hr>
					<?php
					if (isset($isThereAnyPreferenceFilledByStudents) && $isThereAnyPreferenceFilledByStudents != 0) { ?>
						<div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There are placement preferences recorded by students using these placement round participants, you can not edit placement round participants at this time.</div>
						<?php
					} ?>
					<fieldset style="padding-bottom: 5px;">
						<legend>&nbsp;&nbsp; Placement Participant College/Department &nbsp;&nbsp;</legend>
						<div class="row">
							<div class="large-3 columns">
								<?php echo $this->Form->input('PlacementRoundParticipant.1.academic_year', array('id' => 'AcademicYear', 'label' => 'Academic Year: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $acyear_array_data, 'default' => isset($this->request->data['PlacementRoundParticipant'][1]['academic_year']) ? $this->request->data['PlacementRoundParticipant'][1]['academic_year'] : (isset($defaultacademicyear) ? $defaultacademicyear : ''))); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('PlacementRoundParticipant.1.placement_round', array('class' => 'PlacementRound', 'id' => 'PlacementRound', 'label' => 'Placement Round: ', 'style' => 'width:80%;', 'type' => 'select', 'options' => Configure::read('placement_rounds'))); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('PlacementRoundParticipant.1.program_id', array('id' => 'ProgramId', 'label' => 'Program: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programs)); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('PlacementRoundParticipant.1.program_type_id', array('id' => 'ProgramTypeId', 'label' => 'Program Type: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programTypes)); ?>
							</div>
						</div>
						<div class="row">
							<div class="large-6 columns">
								<?= $this->Form->input('PlacementRoundParticipant.1.applied_for', array('options' => $allUnits, 'id' => 'AppliedFor', 'type' => 'select', 'label' => 'Applied for those students currently in:  ', 'required', 'empty' => '[ Select to be Applied Unit ]', 'style' => 'width:90%;')); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('PlacementRoundParticipant.1.semester', array('id' => 'Semester', 'label' => 'CGPA Semester: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => Configure::read('semesters'))); ?>
							</div>
							<div class="large-3 columns">
								<div style="margin-top: 15px;">
									<br >
									<?= $this->Form->input('PlacementRoundParticipant.1.require_all_selected', array('id' => 'requireAllSelected', 'label' => 'Require All Options Selected', 'type' => 'checkbox', 'checked' => isset($this->request->data['PlacementRoundParticipant'][1]['require_all_selected']) ? $this->request->data['PlacementRoundParticipant'][1]['require_all_selected'] : 'checked')); ?>
								</div>
							</div>
						</div>
					</fieldset>
				</div>

				<div class="examTypes form">
					<div style="overflow-x:auto;">
						<table cellspacing="0" cellpadding="0" id="participant_setup" class="table">
							<thead>
								<tr>
									<td style="width:5%;" class="center">#</th>
									<td style="width:20%;" class="center">Participant Type</td>
									<td style="width:35%;" class="center">Participant Unit</td>
									<td style="width:30%;" class="center">Display Name for Choice</td>
									<td style="width:10%;" class="center">&nbsp;</td>
								</tr>
							</thead>
							<tbody>
								<?php
								if (!empty($this->request->data)) {
									$count = 1;
									foreach ($this->request->data['PlacementRoundParticipant'] as $key => $placementType) { 
										if (isset($isThereAnyPreferenceFilledByStudents) && $isThereAnyPreferenceFilledByStudents == 0 && 0) { ?>
											<tr id="PlacementRoundType_<?= $count; ?>">
												<td class="center"><?= ($count); ?></td>
												<td class="center">
													<?= (isset($placementType['id']) ? $this->Form->input('PlacementRoundParticipant.' . $key . '.id', array('type' => 'hidden')) : ''); ?>
													<?= (isset($placementType['group_identifier']) ? $this->Form->input('PlacementRoundParticipant.' . $key . '.group_identifier', array('type' => 'hidden')) : ''); ?>
													<?= $this->Form->input('PlacementRoundParticipant.' . $key . '.type', array('label' => false, 'options' => $types, 'style' => 'width:90%')); ?>
												</td>
												<td class="center">
													<?php
													if ($placementType['type'] == "College") {
														echo $this->Form->input('PlacementRoundParticipant.' . $key . '.foreign_key', array('label' => false, 'options' => $colleges, 'style' => 'width:90%'));
													} else if ($placementType['type'] == "Department") {
														echo $this->Form->input('PlacementRoundParticipant.' . $key . '.foreign_key', array('label' => false, 'options' => $departments, 'style' => 'width:90%'));
													} ?>
												</td>
												<td class="center"><?= $this->Form->input('PlacementRoundParticipant.' . $key . '.name', array('label' => false, 'id' => 'name_' . $key)); ?></td>
												<td style="padding-top:15px;" class="center">
													<?php
													if (isset($isThereAnyPreferenceFilledByStudents) && $isThereAnyPreferenceFilledByStudents == 0) { ?>
														<a href="javascript:deleteSpecificRow('PlacementRoundType_<?= $count++; ?>')">Delete</a>
														<?php
													} else {
														echo '&nbsp;';
													} ?>
												</td>
											</tr>
											<?php
										} else { ?>
											<tr id="PlacementRoundType_<?= $count; ?>">
                                                <td class="center"><?= ($count); ?></td>
                                                <td class="center">
													<?= (isset($placementType['id']) ? $this->Form->input('PlacementRoundParticipant.' . $key . '.id', array('type' => 'hidden')) : ''); ?>
													<?= (isset($placementType['group_identifier']) ? $this->Form->input('PlacementRoundParticipant.' . $key . '.group_identifier', array('type' => 'hidden')) : ''); ?>
													<?= $types[$placementType['type']]; ?>
												</td>
                                                <td class="vcenter">
													<?php
													if ($placementType['type'] == "College") {
														echo $colleges[$placementType['foreign_key']];
													} else if ($placementType['type'] == "Department") {
														echo $departments[$placementType['foreign_key']];
													} ?>
												</td>
                                                <td class="vcenter"><?= $placementType['name']; ?></td>
                                                <td>&nbsp;</td>
                                            </tr>
											<?php
											$count++;
										}
									}
								} ?>
							</tbody>
						</table>
					</div>
					<br>

                    <?php
                    if (isset($isThereAnyPreferenceFilledByStudents) && $isThereAnyPreferenceFilledByStudents == 0 && 0) { ?>
                        <div class="row">
                            <div class="large-2 columns">
                                <input type="button" value="Add Row" onclick="addRow('participant_setup', 'PlacementRoundParticipant',4, '<?= $fieldSetups; ?>')" />
                            </div>
                            <div class="large-2 columns">
                                <input type="button" value="Delete Row" onclick="deleteRow('participant_setup')" />
                            </div>
                            <div class="large-8 columns">
                                &nbsp;
                            </div>
                        </div>
                        <hr>
                        <?= $this->Form->submit(__('Submit'), array('name' => 'saveIt', 'id' => 'saveIt', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
                        <?php
                    }  ?>
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function updateForeignKey(PlacementType, PlacementForeign) {
		//serialize form data
		var formData = $("#" + PlacementType).val();
		var appliedStr = $("#AppliedFor").val();
		var appliedFor = appliedStr.split("~");
		var formUrl = '/PlacementRoundParticipants/get_participant_unit/' + formData + "/" + appliedFor[0] + "/" + appliedFor[1];
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data, textStatus, xhr) {
				$("#" + PlacementForeign).attr('disabled', false);
				$("#" + PlacementForeign).empty();
				$("#" + PlacementForeign).append(data);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}

	var departments = Array();
	var departments_combo = '';
	var dept_index = 0;

	<?php
	if (isset($departments) && !empty($departments)) {
		foreach ($departments as $unit_id => $unit_name) { ?>
			dept_index = departments.length;
			departments[dept_index] = new Array();
			departments[dept_index][0] = "<?= $unit_id; ?>";
			departments[dept_index][1] = "<?= $unit_name; ?>";
			departments_combo += "<option value='<?= $unit_id; ?>'><?= $unit_name; ?></option>";
			<?php
		} 
	} ?>

	var colleges = Array();
	var colleges_combo = '';
	var clg_index = 0;

	<?php
	if (isset($colleges) && !empty($colleges)) {
		foreach ($colleges as $unit_id => $unit_name) { ?>
			clg_index = colleges.length;
			colleges[clg_index] = new Array();
			colleges[clg_index][0] = "<?= $unit_id; ?>";
			colleges[clg_index][1] = "<?= $unit_name; ?>";
			colleges_combo += "<option value='<?= $unit_id; ?>'><?= $unit_name; ?></option>";
			<?php
		}
	} ?>

	var totalRow = <?php if (!empty($this->request->data)) echo (count($this->request->data['PlacementRoundParticipant'])); else if (!empty($exam_types)) echo (count($exam_types)); else echo 2; ?>;

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
		cell0.classList.add("center");

		//construct the other cells
		for (var j = 1; j <= no_of_fields; j++) {
			var cell = row.insertCell(j);
			cell.classList.add("center");
			if (elementArray[j - 1] == 'name') {
				var element = document.createElement("input");
				//element.size = "4";
				element.type = "text";
				element.required = "required";
				element.id = "name_" + rowCount;
			} else if (elementArray[j - 1] == "type") {
				var element = document.createElement("select");
				string = "";
				string += '<option value="College"> College</option>';
				string += '<option value="Department"> Department</option>';
				string += '<option value="Specialization"> Specialization</option>';
				element.id = "PlacementType_" + rowCount;
				element.innerHTML = string;
				element.required = "required";
				element.style = "width:90%";
				element.onchange = function() {
					updateForeignKey("PlacementType_" + rowCount, "PlacementForeign_" + rowCount);
				};
			} else if (elementArray[j - 1] == "foreign_key") {
				var valueType = document.getElementById("PlacementType_" + rowCount).value;
				var element = document.createElement("select");
				string = "";
				if (valueType == "College") {
					for (var f = 0; f < colleges.length; f++) {
						string += '<option value="' + colleges[f][0] + '"> ' + colleges[f][1] + '</option>';
					}
					element.id = "PlacementForeign_" + rowCount;
					element.innerHTML = string;
					element.required = "required";
				} else if (valueType == "Department") {
					for (var f = 0; f < departments.length; f++) {
						string += '<option value="' + departments[f][0] + '"> ' + departments[f][1] + '</option>';
					}
					element.id = "PlacementForeign_" + rowCount;
					element.innerHTML = string;
					element.required = "required";
				}
				element.style = "width:90%";
				element.onchange = function() {
					displayUnit(this);
				};
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
			var table = row.parentNode;
			if (table.rows.length > 2) {
				row.parentNode.removeChild(row);
				updateSequence('participant_setup');
			} else {
				alert('There must be at least one participant type.');
			}
		} catch (e) {
			alert(e);
		}
	}

	function displayUnit(selectObject) {
		//populate unit
		var selectMyStr = selectObject.id;
		var text = selectObject.options[selectObject.selectedIndex].innerText;

		if (typeof text != 'undefined') {
			var selectIds = selectMyStr.split("_");
			document.getElementById('name_' + selectIds[1]).value = text;
		}
	}


    var form_being_submitted = false; /* global variable */

    var checkForm = function(form) {
        if (form_being_submitted) {
            alert("Your request is being processed, please wait a moment...");
            form.saveIt.disabled = true;
            return false;
        }
        form.saveIt.value = 'Submitting...';
        form_being_submitted = true;
        return true; /* submit form */
    };
    // prevent possible form resubmission of a form 
    // and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>