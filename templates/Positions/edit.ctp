<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Edit Position'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<?= $this->Form->create('Position', array('data-abide', 'onSubmit' => 'return checkForm(this);')); ?>
			<?= $this->Form->input('Position.id'); ?>
			<div class="large-3 columns">
				<?= $this->Form->input('Position.position', array('style' => 'width: 90%', 'id' => 'positionName', 'placeholder' => 'Position Name here', 'required', 'pattern' => '[a-zA-Z]+', 'label' => 'Position: <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Position Name is required and must be a string.</small>')); ?>
			</div>

			<div class="large-3 columns">
				<?= $this->Form->input('Position.service_wing_id', array('type' => 'select', 'options' => $serviceWings, 'style' => 'width: 90%')); ?>
			</div>

			<div class="large-3 columns">
				<?= $this->Form->input('Position.applicable_educations', array('multiple' => 'checkbox', 'options' => $applicableEducations, 'style' => 'width: 90%')); ?>
			</div>
			
			<div class="large-3 columns">
				<?= $this->Form->input('description', array('label' => 'Description', 'style' => 'width: 90%')); ?>
			</div>
		
			<div class="large-12 columns">
				<hr>
				<?= $this->Form->end(array('label' => __('Submit'), 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue')); ?>
			</div>
		</div>
	</div>
</div>

<script>

	var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {

		if (form.positionName.value == '') {
			alert("Please add Position Name");
			return false;
		}
	
		if (form_being_submitted) {
			alert("Adding Position, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Adding Position...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>