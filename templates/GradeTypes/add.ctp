<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Add Grade Type'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('GradeType', array('onSubmit' => 'return checkForm(this);')); ?>

				<?php
				$grade_detail = array('grade' => 1, 'point_value' => 2, 'pass_grade' => 3, 'allow_repetition' => 4);
				$all_grade_detail = "";
				$sep = "";

				foreach ($grade_detail as $key => $tag) {
					$all_grade_detail .= $sep . $key;
					$sep = ",";
				} ?>

				<div class="gradeTypes form">
					<table cellpadding="0" cellspacing="0" class="table-borderless fs13">
						<tbody>
							<tr>
								<td style="padding-left:-150px"><?= $this->Form->input('type', array('label' => 'Grade Type Name: ', 'id' => 'GradeTypeType')); ?> </td>
							</tr>
							<tr>
								<td><?= $this->Form->input('used_in_gpa', array('label' => 'Used In GPA', 'checked' => true)); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->input('scale_required', array('label' => 'Required Scale', 'checked' => true, 'type' => 'checkbox')); ?></td>
							</tr>
							<tr>
								<td class="fs15">Add possible grades with its point value for the above grade type you entered.</td>
							</tr>
							<tr>
								<td>
									<table id="grade" cellpadding="0" cellspacing="0" class="table-borderless">
										<thead>
											<tr>
												<td>#</td>
												<td>Grade</td>
												<td>Point Value</td>
												<td>Pass Grade</td>
												<td>Repeatable</td>
											</tr>
										</thead>
										<tbody>
											<?php
											debug($this->request->data['Grade']);
											if (!empty($this->request->data['Grade'])) {
												$count = 1;
												foreach ($this->request->data['Grade'] as $bk => $bv) { ?>
													<tr>
														<td><?= $count++; ?></td>
														<td><?= $this->Form->input('Grade.' . $bk . '.grade', array('type' => 'text', 'value' => (isset($this->request->data['Grade'][$bk]['grade']) ? $this->request->data['Grade'][$bk]['grade'] : ''), 'label' => false, 'div' => false, 'size' => 4 )); ?></td>
														<td><?= $this->Form->input('Grade.' . $bk . '.point_value', array('type' => 'number','value' => (isset($this->request->data['Grade'][$bk]['point_value']) ? $this->request->data['Grade'][$bk]['point_value'] : ''), 'label' => false, 'div' => false, 'size' => 4 )); ?></td>
														<td><?= $this->Form->input('Grade.' . $bk . '.pass_grade', array('type' => 'checkbox', (isset($this->request->data['Grade'][$bk]['pass_grade']) && ($this->request->data['Grade'][$bk]['pass_grade'] == 1 ||  $this->request->data['Grade'][$bk]['pass_grade'] == 'on') ? 'checked': ''), 'label' => false, 'div' => false)); ?></td>
														<td><?= $this->Form->input('Grade.' . $bk . '.allow_repetition', array('type' => 'checkbox', (isset($this->request->data['Grade'][$bk]['allow_repetition']) && ($this->request->data['Grade'][$bk]['allow_repetition'] == 1 ||  $this->request->data['Grade'][$bk]['allow_repetition'] == 'on') ? 'checked': ''), 'label' => false, 'div' => false)); ?></td>
													</tr>
													<?php
												}
											} else { ?>
												<tr>
													<td>1</td>
													<td><?= $this->Form->input('Grade.0.grade', array('type' => 'text', 'label' => false, 'div' => false, 'size' => 4 )); ?></td>
													<td><?= $this->Form->input('Grade.0.point_value', array('type' => 'number', 'label' => false, 'div' => false, 'size' => 4)); ?></td>
													<td><?= $this->Form->input('Grade.0.pass_grade', array('type' => 'checkbox', 'checked', 'label' => false, 'div' => false)); ?></td>
													<td><?= $this->Form->input('Grade.0.allow_repetition', array('type' => 'checkbox', 'label' => false, 'div' => false)); ?></td>
												</tr>
												<?php
											} ?>
										</tbody>
									</table>

									<table cellpadding="0" cellspacing="0" class="table-borderless">
										<tr>
											<td colspan=5>
												<input type="button" value="Add Row" onclick="addRow('grade','Grade',4,'<?= $all_grade_detail; ?>')" />
												<input type="button" value="Delete Row" onclick="deleteRow('grade')" />
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
					<hr>
					<?= $this->Form->Submit('Submit', array('class' => 'tiny radius button bg-blue', 'id' => 'SubmitID', 'div' => false)) ?>
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>


<script language="javascript">
	function addRow(tableID, model, no_of_fields, all_fields) {

		var elementArray = all_fields.split(',');
		var table = document.getElementById(tableID);

		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);

		var cell0 = row.insertCell(0);
		cell0.innerHTML = rowCount;

		//construct the other cells
		for (var j = 1; j <= no_of_fields; j++) {
			var cell = row.insertCell(j);

			if (elementArray[j - 1] == "grade") {
				var element = document.createElement("input");
				element.size = "4";
				element.type = "text";
			} else if (elementArray[j - 1] == 'point_value') {
				var element = document.createElement("input");
				element.size = "4";
				element.type = "number";
			} else if (elementArray[j - 1] == "pass_grade") {
				var element = document.createElement("input");
				element.type = "checkbox";
				element.checked = true;
			} else if (elementArray[j - 1] == "allow_repetition") {
				var element = document.createElement("input");
				element.type = "checkbox";
				element.checked = false;
			}


			element.name = "data[" + model + "][" + rowCount + "][" + elementArray[j - 1] + "]";
			cell.appendChild(element);
		}
	}

	function deleteRow(tableID) {
		try {
			var table = document.getElementById(tableID);
			var rowCount = table.rows.length;
			if (rowCount > 2) {
				table.deleteRow(rowCount - 1);
			} else {
				alert('No more rows to delete');
			}
		} catch (e) {
			alert(e);
		}
	}


	var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {

		if (form.GradeTypeType.value == '') { 
			form.GradeTypeType.focus();
			return false;
		}

		if (form_being_submitted) {
			alert("Adding Grade Type, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Adding Grade Type...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>