<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Add Your Placement Preferences'. (isset($roundlabel) ? ' for ' . $academic_year . ', ' .$roundlabel . ' Round' : '')); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('PlacementPreference', array('onSubmit' => 'return checkForm(this);')); ?>
				<div class="preferences form">
					<div style="margin-top: -30px;"><hr></div>
					<?php 
					if (isset($departmentcount) && $departmentcount > 0) { ?>

						<fieldset style="margin-top: 5px; margin-bottom: 15px;">
							<legend>&nbsp;&nbsp; Student and Placement Round Details &nbsp;&nbsp;</legend>
							<div class="large-12 columns">
								<strong class="fs14 text-black">
									Student Name: &nbsp;<?= $studentname; ?> <br>
									Student ID: &nbsp;<?= $studentnumber; ?> <br>
									<hr>
									Current Unit: &nbsp;<?= $unitFor['College']['name'] ?> <br>
									Placement ACY: &nbsp;<?= $acyear; ?> <br>
									Placement Round: &nbsp;<?= $roundlabel; ?> <br>
								</strong>
							</div>
						</fieldset>

						<h6 class="text-gray">Register your placement preferences</h6><br/>

						<?php
						if (isset($departmentcount)) {
							for ($i = 1; $i <= $departmentcount; $i++) {
								if (isset($this->request->data['PlacementPreference'][$i]['id']) && !empty($this->request->data['PlacementPreference'][$i]['id'])) {
									echo $this->Form->hidden('PlacementPreference.' . $i . '.id', array('value' => $this->request->data['PlacementPreference'][$i]['id']));
								}
								echo $this->Form->hidden('PlacementPreference.' . $i . '.accepted_student_id', array('value' => $accepted_student_id));
								echo $this->Form->hidden('PlacementPreference.' . $i . '.academic_year', array('value' => $acyear));
								echo $this->Form->hidden('PlacementPreference.' . $i . '.user_id', array('value' => $user_id));
								echo $this->Form->hidden('PlacementPreference.' . $i . '.accepted_student_id', array('value' => $accepted_student_id));
								echo $this->Form->hidden('PlacementPreference.' . $i . '.student_id', array('value' => $student_id));
								echo $this->Form->hidden('PlacementPreference.' . $i . '.round', array('value' => $placementRound)); 
								echo $this->Form->hidden('PlacementPreference.' . $i . '.preference_order', array('value' => $i)); ?>
								<div class="row">
									<div class="large-6 columns">
										<?= $this->Form->input('PlacementPreference.' . $i . '.placement_round_participant_id', array('style' => 'width:90%;', 'label' => 'Preference ' . $i, 'options' => $departments, 'required' => (isset($require_all_selected_switch) && $require_all_selected_switch ? 'required' : false), 'empty' => '[ Select your number ' . $i . ' preference ]', 'value' => (!empty($this->request->data) ? $this->request->data['PlacementPreference'][$i]['placement_round_participant_id'] : ''))); ?>
									</div>
								</div>
								<?php
							}
						} ?>
						<hr>
						<?= $this->Form->submit(__('Submit Preference'), array('name' => 'fillPreference', 'value' => 1, 'id' => 'saveForm', 'div' => false, 'class' => 'tiny radius button bg-blue')) ?> 
						<?php
					} ?>
				</div>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script>
	var form_being_submitted = false;
	var checkForm = function(form) {
		if (form_being_submitted) {
			alert("Your preferences are being submitted, please wait a moment...");
			form.saveForm.disabled = true;
			return false;
		}
		form.saveForm.value = 'Submitting Preferences...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>