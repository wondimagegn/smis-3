<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Approve the auto placement.'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div class="examTypes form">

					<?= $this->Form->create('PlacementSetting', array('onSubmit' => 'return checkForm(this);')); ?>

					<div style="margin-top: -30px;">
						<hr>
						<fieldset style="padding-bottom: 15px; margin-bottom: 15px;">
							<div class="large-3 columns">
								<?= $this->Form->input('PlacementSetting.academic_year', array('id' => 'AcademicYear', 'label' => 'Academic Year: ', 'style' => 'width:150px', 'type' => 'select', 'options' => $availableAcademicYears, 'default' => (isset($this->request->data['PlacementSetting']['academic_year']) ? $this->request->data['PlacementSetting']['academic_year'] : (isset($latestACYRoundAppliedFor['academic_year']) ? $latestACYRoundAppliedFor['academic_year'] : '')))); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('PlacementSetting.round', array('id' => 'PlacementRound', 'label' => 'Placement Round: ', 'style' => 'width:150px', 'type' => 'select', 'options' => Configure::read('placement_rounds'),  'default' => (isset($this->request->data['PlacementSetting']['round']) ? $this->request->data['PlacementSetting']['round'] : (isset($latestACYRoundAppliedFor) ? $latestACYRoundAppliedFor['round'] : '')))); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('PlacementSetting.program_id', array('id' => 'ProgramId', 'label' => 'Program: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programs)); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('PlacementSetting.program_type_id', array( 'id' => 'ProgramTypeId', 'label' => 'Program Type: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programTypes)); ?>
							</div>
							<div class="large-6 columns">
								<?= $this->Form->input('PlacementSetting.applied_for', array('options' => $appliedForList, /* 'options' => $allUnits, */ 'id' => 'AppliedFor', 'type' => 'select', 'label' => 'Applied for Students in: ', 'empty' => '[ Select Applied Unit ]', 'style' => 'width:90%;')); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('PlacementSetting.remark', array('id' => 'Remark', 'type' => 'text', 'label' => 'Remark: ', 'style' => 'width:90%;', 'required' => 'required')); ?>
							</div>
							<div class="large-3 columns">
								&nbsp;
							</div>
						</fieldset>
						<hr>
						<?= $this->Form->Submit('Approve Auto Placement', array('div' => false, 'name' => 'approvePlacement', 'id' => 'approvePlacement', 'class' => 'tiny radius button bg-blue')); ?>
					</div>
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>

	var form_being_submitted = false;

	var checkForm = function(form) {

		if (form.AppliedFor.value == 0 || form.RoleID.value == '') { 
			form.AppliedFor.focus();
			return false;
		}

		if (form.Remark.value == '') { 
			form.Remark.focus();
			return false;
		}
	
		if (form_being_submitted) {
			alert("Approving Auto Placement, please wait a moment...");
			form.approvePlacement.disabled = true;
			return false;
		}

		form.approvePlacement.value = 'Approving Auto Placement...';
		form_being_submitted = true;
		return true; 
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>
