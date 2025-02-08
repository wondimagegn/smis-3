<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Update Placement Deadline'); ?></span>
        </div>
    </div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('PlacementDeadline', array('onSubmit' => 'return checkForm(this);')); ?>
				<div style="margin-top: -30px;">
					<hr>
					<fieldset style="padding-bottom: 10px;">
						<!-- <legend>&nbsp;&nbsp; Edit Placement Deadline &nbsp;&nbsp;</legend> -->
						<div class="large-3 columns">
							<?= $this->Form->input('PlacementDeadline.id'); ?>
							<?= $this->Form->input('PlacementDeadline.academic_year', array('class' => 'AYS', 'id' => 'AcademicYear', 'label' => 'Academic Year: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $acyear_array_data, 'default' => isset($this->request->data['PlacementDeadline']['academic_year']) ? $this->request->data['PlacementDeadline']['academic_year'] : (isset($defaultacademicyear) ? $defaultacademicyear : ''))); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('PlacementDeadline.placement_round', array('class' => 'PlacementRound', 'id' => 'PlacementRound', 'label' => 'Placement Round: ', 'style' => 'width:80%', 'type' => 'select', 'options' => Configure::read('placement_rounds'))); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('PlacementDeadline.program_id', array('class' => 'AYS', 'id' => 'ProgramId', 'label' => 'Program: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $programs)); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('PlacementDeadline.program_type_id', array('class' => 'AYS', 'id' => 'ProgramTypeId', 'label' => 'Program Type: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $programTypes)); ?>
						</div>
						<div class="large-6 columns">
							<?= $this->Form->input('PlacementDeadline.applied_for', array('options' => $allUnits, 'id' => 'AppliedFor', 'type' => 'select', 'label' => 'Applied For Those Student In: ', 'empty' => '[ Select Applied Unit ]', 'style' => 'width:90%')); ?>
						</div>
						<div class="large-6 columns">
							<?= $this->Form->input('PlacementDeadline.deadline', array('label' => 'Deadline: ', 'minYear' => date('Y'), 'maxYear' => date('Y'), 'style' => 'width:15%',)); ?>
						</div>
						<div class="large-12 columns">&nbsp;</div>
						<hr>
						<?= $this->Form->end(array('label' => 'Update Deadline', 'name' => 'saveIt', 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue')); ?>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	var form_being_submitted = false; 

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Saving Placement Deadline, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Saving Placement Deadline...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>