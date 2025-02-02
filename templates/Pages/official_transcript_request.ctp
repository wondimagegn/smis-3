<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class=" icon-window" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Official Transcript Request Form'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div class="row">
					<div class="large-12 columns">
					<?= $this->Form->create('Page', array('controller' => 'pages', 'action' => 'official_transcript_request', 'method' => 'post', 'data-abide', 'onSubmit' => 'return checkForm(this);')); ?>
						<div class="row">
							<div class="large-4 columns">
								<?= $this->Form->input('OfficialTranscriptRequest.first_name', array('placeholder' => 'First Name', 'required', 'pattern'=>'alpha', 'label' => 'First Name: <small></small></label><small class="error" style="background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">First Name is required and must be a string.</small>')); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('OfficialTranscriptRequest.father_name', array('placeholder' => 'Father\'s Name', 'required', 'pattern'=>'alpha', 'label' => 'Father\'s Name: <small></small></label><small class="error" style="background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Father Name is required and must be a string.</small>')); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('OfficialTranscriptRequest.grand_father', array('placeholder' => 'Grand Father\'s Name', 'required', 'pattern'=>'alpha', 'label' => 'Grand Father\'s Name: <small></small></label><small class="error" style="background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Grand Father Name is required and must be a string.</small>')); ?>
							</div>
						</div>
						<div class="row">
							<div class="large-4 columns">
								<?= $this->Form->input('OfficialTranscriptRequest.studentnumber', array('placeholder' => 'ID Number', 'required', 'pattern'=>'^[a-zA-Z0-9\/]+$', 'label' => 'ID Number: <small></small></label><small class="error" style="background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">ID Number is required and must be alpha-numeric with / LIKE "RAMIT/123/12".</small>' )); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('OfficialTranscriptRequest.admissiontype', array('label' => 'Admission Type: ', 'required' => 'required')); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('OfficialTranscriptRequest.degreetype', array('label' => 'Degree Type: ', 'required' => 'required')); ?>
							</div>
						</div>
						<div class="row">
							<div class="large-4 columns">
								<?= $this->Form->input('OfficialTranscriptRequest.email', array('placeholder' => 'Email', 'type' => 'email', 'required', 'pattern'=>'email', 'label' => 'Email: <small></small></label><small class="error" style="background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> Email address is required and it must be a valid one.</small>')); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('OfficialTranscriptRequest.mobile_phone', array('type' => 'tel', 'required', 'id'=>'intPhoneHyphenFormatted', 'label' => 'Mobile Phone: <small></small></label><small class="error" style="background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> Mobile Phone number is required.</small>')); ?>
							</div>
							<div class="large-4 columns">
								&nbsp;
							</div>
						</div>
						<div class="row">
							<div class="large-4 columns">
								<?= $this->Form->input('OfficialTranscriptRequest.institution_name', array('placeholder' => 'Institution Name', 'required' , 'pattern' => '[a-zA-Z]+', 'label' => 'Receiver Institution Name: <small></small></label><small class="error" style="background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> Receiver Institution Name is required.</small>')); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('OfficialTranscriptRequest.institution_address', array('placeholder' => 'Institution Postal Address', 'required', 'pattern' => '[a-zA-Z]+', 'label' =>'Receiver Institution Postal Address: <small></small></label><small class="error" style="background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> Receiver Institution Postal address is required.</small>')); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('OfficialTranscriptRequest.recipent_country', array('placeholder' => 'Institution Country', 'required', 'pattern' => '[a-zA-Z]+', 'label' => 'Country of Receiver Institution: <small></small></label><small class="error" style="background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> Country of Receiver Institution is required.</small>')); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('OfficialTranscriptRequest.institution_email', array('placeholder' => 'Institution Email', 'type' => 'email', 'required', 'pattern'=>'email', 'label' => 'Receiver Institution Email: <small></small></label><small class="error" style="background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> Receiver Institution Email is required and it must be a valid one.</small>')); ?>
							</div>
						</div>
						<hr>
						<?= $this->Form->end(array('label' => __('Submit', true),  'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {

		/* if (form.UserID.value == 0) { 
			form.UserID.focus();
			return false;
		} */
	
		if (form_being_submitted) {
			alert("Submitting your Official Transcript Request, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Activating User Account...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>
		