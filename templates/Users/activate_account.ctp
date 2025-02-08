<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-user-add-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('User Account Activation'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<?= $this->Form->create('User', array('data-abide', 'onSubmit' => 'return checkForm(this);')); ?>
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>
				<blockquote>
					<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
					<p style="text-align:justify;"><span class="fs15 text-black">This tool will enable you to activate user account. Note that <b><i>only deactivated accounts are listed here.</i></b></span></p> 
				</blockquote>
				<hr>
				<div class="row">
					<div class="large-6 columns">
						<?= $this->Form->input('role_id', array('label' =>' Role: ', 'style' => 'width:320px', 'onchange' => 'getUsersBasedOnRole(this)', 'id' => 'RoleID', 'type' => 'select', 'default' => $role_id, 'options' => $roles)); ?>
					</div>
				</div>
				<div class="row">
					<div class="large-6 columns">
						<?= $this->Form->input('user_id', array('label' => 'User: ', 'style' => 'width:400px', 'id' => 'UserID', 'onchange' => 'toggleSubmitButtonActive()', 'class' => 'custom-select', 'options' => $users)); ?>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="large-6 columns">
						<?= $this->Form->Submit('Activate Account', array('id' => 'SubmitID', 'disabled', 'class' => 'tiny radius button bg-blue')); ?>
					</div>
				</div>
			</div>
			<?= $this->Form->end(); ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	function getUsersBasedOnRole(obj) {
		$("#RoleID").attr('disabled', true);
		$("#UserID").attr('disabled', true);
		$("#SubmitID").attr('disabled', true);
		window.location.replace("/users/activate_account/" + obj.value);
	}

	$(function() {
		$("#UserID").customselect();
	});

	function toggleSubmitButtonActive() {
		if ($("#UserID").val != 0 || $("#UserID").val != '') {
			$("#SubmitID").attr('disabled', false);
		}
	}

	var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {
		if (form.UserID.value == 0) { 
			form.UserID.focus();
			return false;
		}
		if (form.RoleID.value == 0) { 
			form.RoleID.focus();
			return false;
		}

		if (form_being_submitted) {
			alert("Activating User Account, please wait a moment...");
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