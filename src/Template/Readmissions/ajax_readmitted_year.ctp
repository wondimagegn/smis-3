<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-vcard" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Maintain Readmission for ' . $student_detail['Student']['first_name'] . ' ' . $student_detail['Student']['middle_name'] . ' ' . $student_detail['Student']['last_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')'; ?></span>
		</div>

		<a class="close-reveal-modal">&#215;</a>
	</div>
	<div class="row">
		<div class="large-12 columns">

			<div style="margin-top: -10px;"><hr></div>

			<?= $this->Form->create('Readmission', array('id' => 'ReadmissionReadmissionDataEntryForm', 'action' => 'readmission_data_entry', "method" => "POST", /* 'onSubmit' => 'return checkForm(this);' */)); ?>
			
			<?php
			if (isset($pAcYear) && !empty($pAcYear)) { ?>
				<div class="large-7 columns" style="padding-top: 25px;">
					<?= $this->element('student_basic'); ?>
				</div>

				<div class="large-5 columns" style="padding-top: 25px;">
					<h6 class="text-gray fs14">Please select the academic year and semester the student has been readmitted:</h6>
					<br>
					<?= $this->Form->input('Search.student_id', array('type' => 'hidden', 'value' => $student_detail['Student']['id'])); ?>

					<h6 id="validation-message_non_selected" class="text-red fs14"></h6>

					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<td class="center" style="width: 5%;"></td>
									<td class="vcenter">ACY</td>
									<td class="center">Semester</td>
									<td class="center"></td>
								</tr>
							</thead>
							<tbody>
								<?php
								$count = 0;
								$enable_delete_button = 0;
								foreach ($pAcYear as $k => $Year) {
									$count++;
									$ek = explode('~', $k); ?>
									<tr>
										<td class="center">
											<?php
											if (!empty($ek[1])) {
												echo $this->Form->input('Readmission.' . $count . '.id', array('type' => 'hidden', 'value' => $ek[1]));
												$enable_delete_button++;
											}
											echo $this->Form->input('Readmission.' . $count . '.gp', array('type' => 'checkbox', 'class' => 'checkbox1', 'label' => false, 'id' => 'StudentSelection' . $count));
											echo $this->Form->input('Readmission.' . $count . '.student_id', array('type' => 'hidden', 'value' => $student_detail['Student']['id']));
											echo $this->Form->input('Readmission.' . $count . '.academic_year', array('type' => 'hidden', 'value' => $Year));
											?>
										</td>
										<td class="vcenter"><?= $Year; ?></td>
										<td class="center">
											<br>
											<?php
											if (!empty($ek[1])) {
												echo $this->Form->input('Readmission.' . $count . '.semester', array('label' => false, 'type' => 'select', 'options' => Configure::read('semesters'), 'style' => 'width:80%;'));
											} else {
												echo $this->Form->input('Readmission.' . $count . '.semester', array('label' => false, 'type' => 'select', 'options' => Configure::read('semesters'), 'empty' => '[ Select ]', 'style' => 'width:80%;'));
											} ?>
										</td>
										<td class="vcenter"><?= (!empty($ek[1]) ? "Readmitted" : ''); ?></td>
									</tr>
									<?php 
								} ?>
							</tbody>
						</table>
					</div>
					<div class="row">
						<hr>
						<div class="large-6 columns">
							<?= $this->Form->submit(__('Save ', true), array('name' => 'saveReadmission', 'id' => 'saveReadmission', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
						</div>
						<div class="large-6 columns">
							<?= ($enable_delete_button ? $this->Form->submit(__('Delete ', true), array('name' => 'deleteReadmission', 'id' => 'deleteReadmission', 'class' => 'tiny radius button bg-red', 'div' => false)) : ''); ?>
						</div>
					</div>
				</div>
				<?php
			} else { ?>
				<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Tnable to load students academic status data or there is no dismissed academic status for the selected student.</div>
				<?php
			} ?>

			<?= $this->Form->end(); ?>
		</div>
	</div>
</div>

<script type="text/javascript">

	var form_being_submitted = false;

	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	
	$('#deleteReadmission').click(function() {
		
		var checkboxes1 = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne1 = Array.prototype.slice.call(checkboxes1).some(x => x.checked);
		var checkedCount1 = Array.prototype.slice.call(checkboxes1).filter(x => x.checked).length;

		if (!checkedOne1) {
			validationMessageNonSelected.innerHTML = 'At least one Academic Year must be selected!';
			//alert("Please select at least one Academic Year.");
			return false;
		}

		if (checkedCount1 > 1) {
			validationMessageNonSelected.innerHTML = 'One Readmission is allowed to delete at a time. Uncheck the others.';
			//alert("Please select at least one Academic Year.");
			return false;
		}

		$('#deleteReadmission').val('Deleting Readmission...');

		return confirm('Are you sure you want to delete the selected readmission?');

	});

	$('#saveReadmission').click(function() {
		
		var checkboxes1 = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne1 = Array.prototype.slice.call(checkboxes1).some(x => x.checked);
		var checkedCount1 = Array.prototype.slice.call(checkboxes1).filter(x => x.checked).length;

		if (!checkedOne1) {
			validationMessageNonSelected.innerHTML = 'At least one Academic Year must be selected!';
			return false;
		}

		if (checkedCount1 > 1) {
			validationMessageNonSelected.innerHTML = 'One Readmission is allowed to add at a time. Uncheck the others.';
			return false;
		}

		$('#saveReadmission').val('Adding Readmission...');

		return confirm('Are you sure you want to add the selected readmission?');

	});

	/* var checkForm = function(form) {
		var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
		var checkedCount = Array.prototype.slice.call(checkboxes).filter(x => x.checked).length;

		if (!checkedOne) {
			validationMessageNonSelected.innerHTML = 'At least one Academic Year must be selected!';
			//alert("Please select at least one Academic Year.");
			return false;
		}

		if (checkedCount > 1) {
			validationMessageNonSelected.innerHTML = 'One Readmission is allowed to add or delete at a time. Uncheck the others.';
			//alert("Please select at least one Academic Year.");
			return false;
		}

		if (form_being_submitted) {
			alert("Saving Readmission data, please wait a moment...");
			form.saveReadmission.disabled = true;
			return false;
		}

		form.saveReadmission.value = 'Processing...';
		form_being_submitted = true;
		return true;
	}; */


	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
	
</script>