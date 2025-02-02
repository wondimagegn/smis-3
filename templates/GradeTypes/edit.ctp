<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Edit Grade Type : ' . (isset($this->request->data['GradeType']['type']) ? $this->request->data['GradeType']['type'] : ''); ?></span>
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
					<?php
					if (isset($check_not_involved_in_grade_computing) && $check_not_involved_in_grade_computing == false) { ?>
						
						<div class="warning-box warning-message"><span style="margin-right: 15px;"></span><?= __('The grade type can not be editted because it is attached to courses.'); ?></div>
						
						<table cellpadding="0" cellspacing="0" class="table-borderless fs13">
							<tr>
								<td><span class="text-gray" style="font-weight: bold;">Grade Type:</span> &nbsp;&nbsp; <?= $this->request->data['GradeType']['type']; ?></td>
							</tr>
							<tr>
								<td><span class="text-gray" style="font-weight: bold;">Used in GPA:</span> &nbsp;&nbsp; <?= $this->request->data['GradeType']['used_in_gpa'] == 1 ? 'Yes' : 'No';  ?></td>
							</tr>
							<tr>
								<td><span class="text-gray" style="font-weight: bold;">Required Scale:</span> &nbsp;&nbsp; <?= $this->request->data['GradeType']['scale_required'] == 1 ? 'Yes' : 'No'; ?></td>
							</tr>
						</table>

						<h6 class="fs15 text-gray">List of grades with their point value for <?= (isset($this->request->data['GradeType']['type']) ? $this->request->data['GradeType']['type'] : ' the above '); ?> grade type.</h6>

						<table id="grade" cellpadding="0" cellspacing="0" class="responsive table-borderless fs13">
							<thead>
								<tr>
									<!-- <td>#</td> -->
									<td style="text-align: center;">Grade</td>
									<td style="text-align: center;">Point Value</td>
									<td style="text-align: center;">Pass Grade</td>
									<td style="text-align: center;">Repeatable</td>
									<td style="text-align: center;">Active</td>
								</tr>
							</thead>
							<tbody>
								<?php
								if (!empty($this->request->data['Grade'])) {
									$count = 1;
									foreach ($this->request->data['Grade'] as $bk => $bv) { ?>
										<tr>
											<!-- <td style="text-align: center;"><?= $count++; ?></td> -->
											<td style="text-align: center;"><?= $bv['grade']; ?></td>
											<td style="text-align: center;"><?= $bv['point_value']; ?></td>
											<td style="text-align: center;"><?= ($bv['pass_grade'] == 1 ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>'); ?></td>
											<td style="text-align: center;"><?= ($bv['allow_repetition'] == 1 ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>'); ?></td>
											<td style="text-align: center;"><?= ($bv['active'] == 1 ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>'); ?></td>
										</tr>
										<?php
									}
								} ?>
							</tbody>
						</table>
						<?php
					} else { ?>

						<!-- <div class="smallheading"><?= __('Edit Grade Type'); ?></div> -->
						<table cellpadding="0" cellspacing="0" class="table-borderless fs13">
							<tr>
								<td style="padding-left:-150px">
									<?= $this->Form->hidden('id'); ?>
									<?= $this->Form->input('type', array('label' => false)); ?>
								</td>
							</tr>
							<tr>
								<td><?= $this->Form->input('used_in_gpa', array('label' => 'Used In GPA')); ?></td>
							</tr>
							<tr>
								<td><?= $this->Form->input('scale_required', array('label' => 'Required Scale')); ?></td>
							</tr>
							<tr>
								<td class="fs15">Add possible grades with its point value for the above grade type you entered.</td>
							</tr>
							<tr>
								<td>
									<table id="grade" cellpadding="0" cellspacing="0" class="table-borderless fs13">
										<thead>
											<tr>
												<td>#</th>
												<td>Grade</td>
												<td>Point Value</td>
												<td>Pass Grade</td>
												<td>Repeatable</td>
												<td>Action</td>
											</tr>
										</thead>
										<tbody>
											<?php
											if (!empty($this->request->data['Grade'])) {
												$count = 1;
												foreach ($this->request->data['Grade'] as $bk => $bv) {
													if (isset($bv['id']) && !empty($bv['id'])) {
														echo $this->Form->hidden('Grade.' . $bk . '.id', array('value' => isset($this->request->data['Grade'][$bk]['id']) && !empty($this->request->data['Grade'][$bk]['id']) ? $this->request->data['Grade'][$bk]['id'] : ''));
														$action_controller_id = 'edit~gradeTypes~' . $bv['grade_type_id'];
													} ?>
													<?= $this->Form->hidden('Grade.' . $bk . '.grade_type_id'); ?>
													<tr>
														<td><?= $count++; ?></td>
														<td><?= $this->Form->input('Grade.' . $bk . '.grade', array('value' => isset($this->request->data['Grade'][$bk]['grade']) ? $this->request->data['Grade'][$bk]['grade'] : '', 'label' => false, 'div' => false, 'size' => 4)); ?></td>
														<td><?= $this->Form->input('Grade.' . $bk . '.point_value', array('value' => isset($this->request->data['Grade'][$bk]['point_value']) ? $this->request->data['Grade'][$bk]['point_value'] : '', 'label' => false, 'div' => false, 'size' => 4)); ?></td>
														<td><?= $this->Form->input('Grade.' . $bk . '.pass_grade', array('type' => 'checkbox', (isset($this->request->data['Grade'][$bk]['pass_grade']) && ($this->request->data['Grade'][$bk]['pass_grade'] == 1 ||  $this->request->data['Grade'][$bk]['pass_grade'] == 'on') ? 'checked': ''), 'label' => false, 'div' => false)); ?></td>
														<td><?= $this->Form->input('Grade.' . $bk . '.allow_repetition', array('type' => 'checkbox', (isset($this->request->data['Grade'][$bk]['allow_repetition']) && ($this->request->data['Grade'][$bk]['allow_repetition'] == 1 ||  $this->request->data['Grade'][$bk]['allow_repetition'] == 'on') ? 'checked': ''), 'label' => false, 'div' => false)); ?></td>
														<td>
															<?php
															if(isset($bv['id'])){
																echo $this->Html->link(__('Delete'), array('controller' => 'grades', 'action' => 'delete', $bv['id'], $action_controller_id), null, sprintf(__('Are you sure you want to delete %s grade?'), $bv['grade'])); 
															}
															?>
															</td>
													</tr>
													<?php
												}
											} else { ?>
												<tr>
													<td>1</td>
													<td><?= $this->Form->input('Grade.0.grade', array('type' => 'text', 'label' => false, 'div' => false, 'size' => 4)); ?></td>
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
											<td>
												<input type="button" value="Add Row" onclick="addRow('grade','Grade',4,'<?= $all_grade_detail; ?>')" />
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<hr>
						<?= $this->Form->Submit('Submit', array('class' => 'tiny radius button bg-blue', 'id' => 'SubmitID', 'div' => false)) ?>
						<?= $this->Form->end(); ?>
						<?php
					} ?>
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
			if (rowCount != 0) {
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
			alert("Submitting Form, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Submitting Form...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>