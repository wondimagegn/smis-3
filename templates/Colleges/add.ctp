<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Add College'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<?= $this->Html->script('amharictyping'); ?>
			<?= $this->Form->create('College', array('data-abide', 'onSubmit' => 'return checkForm(this);')); ?>
			<div class="large-12 columns" style="margin-top: -30px;">
				<hr>
			</div>
			<div class="large-12 columns">
				<div class="large-6 columns">
					<?php
					echo $this->Form->input('campus_id', array('style' => 'width:90%'));
					echo $this->Form->input('name', array('style' => 'width:90%', 'placeholder' => 'Like: College of Natural Sciences', 'required', 'pattern' => '[a-zA-Z]+', 'label' => 'Name <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">College Name is required and must be a string.</small>'));
					echo $this->Form->input('shortname', array('style' => 'width:90%', 'placeholder' => 'Like: NS or CNS', 'pattern' => 'alpha', 'required', 'label' => 'Short Name <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">College short name is required and must be a single word.</small>'));
					echo $this->Form->input('type', array('style' => 'width:90%', 'placeholder' => 'College, School or Institute', 'pattern' => 'alpha', 'label' => 'Type <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Type name must be a single word.</small>'));
					echo $this->Form->input('phone', array('style' => 'width:90%', 'type'=> 'tel', 'id'=>'etPhone'));
					echo '<br>'.$this->Form->input('active', array('type'=>'checkbox', 'checked' =>'checked'));
					?>
				</div>
				<div class="large-6 columns">
					<?php
					echo $this->Form->input('amharic_name', array('style' => 'width:90%', 'id' => 'AmharicText', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);"));
					echo $this->Form->input('amharic_short_name', array('style' => 'width:90%', 'id' => 'AmharicText', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);"));
					echo $this->Form->input('description', array('style' => 'width:90%'));
					echo $this->Form->input('institution_code', array('style' => 'width:90%', 'placeholder' => 'Like: AMU-CNS', 'pattern' => 'institution_code', 'label' => 'Institution Code <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Institution Code must be a single word, with Hyphen separated; Like AMU-CNS</small>'));
					echo $this->Form->input('moodle_category_id', array('id' => 'moodleCategoryId ', 'type' => 'number', 'min'=>'1', 'max'=>'1000', 'step'=>'1', 'class' => 'fs13', 'label' =>'Moodle Category ID: ', 'style' => 'width:25%'));
					?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="large-12 columns">
			<hr>
				<?= $this->Form->end(array('label' => 'Add College', 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue')); ?>
			</div>
		</div>
	</div>
</div>

<script>

	var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Adding College, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Adding College...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>