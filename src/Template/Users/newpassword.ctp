<style>
	.hidden {
		display: none;
	}
	.password-container {
		/* width: 385px; */
		position: relative;
	}
	.password-container input[type="password"],
	.password-container input[type="text"] {
		width: 100%;
		padding: 12px 36px 12px 12px;
		box-sizing: border-box;
	}
	.fa-eye {
		/* position: absolute; */
		top: 28%;
		right: 4%;
		cursor: pointer;
		/* color: lightgray; */
	}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<link rel="stylesheet" type="text/css" href="/css/password_strength.css" media="screen" />

<?= $this->Html->script('password_strength'); ?>

<script type="text/javascript">
	
</script>

<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-key-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Reset your Login Password'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<?= $this->Form->create('User', array('data-abide', 'action' => 'newpassword', 'onSubmit' => 'return checkForm(this);')); ?>
		<div class="row">
			<div class="large-4 columns">
				<b>Full Name: </b>
				<?php //echo $this->request->data['User']['first_name'] . " " . $this->request->data['User']['middle_name'] . " " . $this->request->data['User']['last_name']; ?>
				<?= $this->request->data['User']['full_name']; ?>
				<?= $this->Form->hidden('User.first_name'); ?>
				<?= $this->Form->hidden('User.middle_name'); ?>
				<?= $this->Form->hidden('User.last_name'); ?>
			</div>
		</div>

		<div class="row">
			<div class="large-4 columns">
				<b>Username: </b><?= $this->request->data['User']['username']; ?>
				<?= $this->Form->hidden('User.username'); ?>
				<p> </p>
			</div>
		</div>

		<div class="row">
			<div class="large-4 columns">
				<div class="password-field">
					<div class="password-container">
						<?= $this->Form->input('User.passwd', array('type' => 'password', 'placeholder' => 'New Password', 'id' => 'passwd', 'value' => '', 'style' => 'width:90%;', 'pattern' => 'strong_password', 'required', 'onkeyup' => 'passwordStrength(this.value)', 'label' => 'New Password <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> New Password is required and must match the requirements . Your new password should contain atleast one small letter, one capital letter, one number and one special character(Allowed: !@#$%^&*~<>{}()+-`\'"?/|=_.:,:;) with minimum length of 8 characters total. Like: Abe&3292</small>')); ?>
						<!-- <i class="fa-solid fa-eye" id="eye"></i> Click the eye icon to show or hide password -->
						<i class="fa-solid fa-eye" id="eye"><label for="eye" style="font-family: 'Times New Roman', Times, serif; font-weight: normal; display: inline-block;" class="fs14"> &nbsp; Click here to show or hide password</label></i>
						<p> </p>
						<?= $this->Form->error('User.passwd', 'Please enter the Password.'); ?>
					</div>
				</div>
			</div>
			<div class="large-8 columns">
				<label for="passwordStrength">Enter your new password until you get "<strong>Strong</strong>" or "<strong>Strongest</strong>" result.</label>
				<div id="passwordDescription">Password not entered</div>
				<div id="passwordStrength" class="strength0"></div>
				<br>
			</div>
		</div>

		<div class="row">
			<div class="large-4 columns">
				<div class="password-confirmation-field">
					<div class="password-container">
						<?= $this->Form->input('User.confirmpassword', array('type' => 'password', 'id' => 'confirmpassword', 'placeholder' => 'Confirm Password', 'value' => '', 'style' => 'width:90%;', 'required', 'data-equalto' => 'passwd', 'onchange' => 'toggleSubmitButtonActive()', 'label' => 'Confirm Password <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> The New Password and Confirm Password values did not match or empty. You can cross-check your provided New password by clicking the eye icon above.</small>')); ?>
						<?= $this->Form->error('User.confirmpassword', 'Please enter the Password Again.'); ?>
						<?= $this->Form->error('User.checkpassword', 'Please Be Sure Passwords Match.'); ?>
					</div>
				</div>
				<div class="row">
					<div class="large-12 columns">
						<?= $this->Form->hidden('User.id') ?>
						<?= $this->Form->Submit('Reset Password', array('id' => 'SubmitID', /* 'disabled', */ 'class' => 'tiny radius button bg-blue')); ?>
						<?= $this->Form->end(); ?>
					</div>
				</div>
				<div class="row">
					<div class="large-4 columns">
						<?= $this->Html->link('Return Home', '/') ?>
						<p> </p>
					</div>
				</div>
			</div>
			<div class="large-8 columns">
				<blockquote>
					<h6><i class="fa fa-info"></i> &nbsp; Password Policy:</h6>
					<p style="text-align: justify;"> <span class="fs14 text-black"> Inorder to make your account secure, you are required to follow the following password policy: </span>
						<ol class="fs14">
							<li>Your password length should be a minimum of <?= $securitysetting['Securitysetting']['minimum_password_length']; ?> and a maximum of <?= $securitysetting['Securitysetting']['maximum_password_length']; ?> characters. The longer the password is, the harder to crack it using brute force attack.</li>
							<?php
							if ($securitysetting['Securitysetting']['password_strength'] == 1) { ?>
								<li>Your password should contain Uppercase Letters, Lowercase Letters, Numbers and symbols. <br> (Allowed: !@#$%^&*~<>{}()+-`'"?/|=_.:,:;)</li>
							<?php
							} else { ?>
								<li>Your password should contain Uppercase Letters, Lowercase Letters, Numbers and Symbols. <br> (Allowed: !@#$%^&*~<>{}()+-`'"?/|=_.:,:;)</li>
							<?php
							} ?>
							<li>Always use different password for this account from other access passwords including email, LDAP, Active Directory, etc.</li>
							<li>Do not hint at the format of a password. </li>
						</ol>
					</p>
				</blockquote>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	const passwordInput = document.querySelector("#passwd");
	const password2Input = document.querySelector("#confirmpassword");
	const eye = document.querySelector("#eye");

	eye.addEventListener("click", function() {
		this.classList.toggle("fa-eye-slash");
		const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
		passwordInput.setAttribute("type", type);
		password2Input.setAttribute("type", type);
	});

	function toggleSubmitButtonActive() {
		if ($("#confirmpassword").val != '' && $("#passwd").val != '') {
			$("#SubmitID").attr('disabled', false);
		}
	}

	var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {

		if (form.passwd.value == '') { 
			form.passwd.focus();
			return false;
		}

		if (form.confirmpassword.value == '') { 
			form.confirmpassword.focus();
			return false;
		}

		if (form.passwd.value !== form.confirmpassword.value) { 
			alert('Passwords did not match. Please try again.');
			form.passwd.focus();
			passwordInput.setAttribute("type", 'text');
            password2Input.setAttribute("type", 'text');
			return false;
		}
		
		if (form_being_submitted) {
			alert("Resetting Password, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Resetting Password...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>
