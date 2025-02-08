<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Edit College: ' . (isset($this->data['College']['name']) ? $this->data['College']['name'] : '') . (isset($this->data['College']['shortname']) ? '  (' . $this->data['College']['shortname'] . ')' : ''); ?></span>
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
				<?php
				if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) { ?>
					<div class="large-6 columns">
						<?php
						echo $this->Form->hidden('id');
						echo $this->Form->input('campus_id', array('style' => 'width:90%'));
						echo $this->Form->input('name', array('style' => 'width:90%', 'placeholder' => 'Like: College of Natural Sciences', 'required', 'pattern' => '[a-zA-Z]+', 'label' => 'Name <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">College Name is required and must be a string.</small>'));
						echo $this->Form->input('shortname', array('style' => 'width:90%', 'placeholder' => 'Like: NS or CNS', 'pattern' => 'alpha', 'required', 'label' => 'Short Name <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">College short name is required and must be a single word.</small>'));
						echo $this->Form->input('type', array('style' => 'width:90%', 'placeholder' => 'College, School or Institute', 'pattern' => 'alpha', 'label' => 'Type <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Type name must be a single word.</small>'));
						echo $this->Form->input('phone', array('style' => 'width:90%', 'type'=> 'tel', 'id'=>'etPhone'));
						echo '<br>'.$this->Form->input('active', array('type'=>'checkbox'));
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
					<?php 
				} else if (($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE || $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) && $this->Session->read('Auth.User')['is_admin'] == 1) { ?>
					<div class="large-6 columns">
						<?php
						echo $this->Form->hidden('id');
						echo $this->Form->input('campus_id', array('style' => 'width:90%', 'disabled'));
						echo $this->Form->hidden('campus_id', array('value' => $this->data['College']['campus_id']));
						echo $this->Form->input('name', array('style' => 'width:90%', 'placeholder' => 'Like: College of Natural Sciences', 'disabled', 'required', 'pattern' => '[a-zA-Z]+', 'label' => 'Name <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">College Name is required and must be a string.</small>'));
						echo $this->Form->hidden('name', array('value' => $this->data['College']['name']));
						echo $this->Form->input('shortname', array('style' => 'width:90%', 'placeholder' => 'Like: NS or CNS', 'pattern' => 'alpha', 'disabled', 'required', 'label' => 'Short Name <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">College short name is required and must be a single word.</small>'));
						echo $this->Form->hidden('shortname', array('value' => $this->data['College']['shortname']));
						echo $this->Form->input('type', array('style' => 'width:90%', 'placeholder' => 'College, School or Institute', 'pattern' => 'alpha', 'disabled', 'label' => 'Type <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Type name must be a single word.</small>'));
						echo $this->Form->hidden('type', array('value' => $this->data['College']['type']));
						echo $this->Form->input('phone', array('style' => 'width:90%', 'type'=> 'tel', 'id'=>'etPhone'));
						echo '<br>'.$this->Form->input('active', array('type'=>'checkbox', 'disabled'));
						echo $this->Form->hidden('active', array('value' => $this->data['College']['active']));
						?>
					</div>
					<div class="large-6 columns">
						<?php
						echo $this->Form->input('amharic_name', array('style' => 'width:90%', 'id' => 'AmharicText', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);"));
						echo $this->Form->input('amharic_short_name', array('style' => 'width:90%', 'id' => 'AmharicText', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);"));
						echo $this->Form->input('description', array('style' => 'width:90%'));
						echo $this->Form->input('institution_code', array('style' => 'width:90%', 'placeholder' => 'Like: AMU-CNS', 'disabled', 'pattern' => 'institution_code', 'label' => 'Institution Code <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Institution Code must be a single word, with Hyphen separated; Like AMU-CNS</small>'));
						echo $this->Form->hidden('institution_code', array('value' => $this->data['College']['institution_code']));
						echo $this->Form->input('moodle_category_id', array('id' => 'moodleCategoryId ', 'type' => 'number', 'min'=>'1', 'disabled', 'max'=>'1000', 'step'=>'1', 'class' => 'fs13', 'label' =>'Moodle Category ID: ', 'style' => 'width:25%'));
						echo $this->Form->hidden('moodle_category_id', array('value' => $this->data['College']['moodle_category_id']));
						?>
					</div>
					<?php
				} ?>
			</div>
		</div>
		<?php
		if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN || (($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE || $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) && $this->Session->read('Auth.User')['is_admin'] == 1)) { ?>
			<div class="row">
				<div class="large-12 columns">
				<hr>
					<?= $this->Form->end(array('label' => 'Save Changes', 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue')); ?>
				</div>
			</div>
			<?php
		} ?>
	</div>
</div>

<script>

	var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Saving Changes, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Saving Changes...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>