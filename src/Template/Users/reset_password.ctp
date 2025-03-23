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

<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-lock-open-filled"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Reset Login Account Password'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>
				<blockquote>
					<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
					<p>
						<span class="fs16 text-black" style="text-align: justify;">This tool will enable you to reset main account, instructors, and system administrators account password using voting system.</span>
						<ol class="fs14" style="text-align: justify;">
							<li> Password reset is available for active user accounts regardless of their associated staff profile activeness. </li>
							<li> After you make password reset, it has to be confirmed by one of the other system administrator within 72 hours to be effective. </li>
						</ol>
					</p>
				</blockquote>
				<hr>
			</div>
		</div>
		<?= $this->Form->create('User', array('data-abide', 'onSubmit' => 'return checkForm(this);')); ?>
		<div class="row">
			<div class="large-6 columns">
				<?= $this->Form->input('role_id', array('label' => false, 'style' => 'width:90%;', 'onchange' => 'getUsersBasedOnRole(this)', 'id' => 'RoleID', 'type' => 'select', 'default' => $role_id, 'options' => $roles, 'style' => 'width:200px')); ?>
			</div>
			<div class="large-6 columns">
				<?= $this->Form->input('user_id', array('label' => false, 'style' => 'width:90%;', 'id' => 'UserID', 'class' => 'custom-select', 'options' => $users, 'onchange' => 'toggleSubmitButtonActive()', 'style' => 'width:300px')); ?>
			</div>
		</div>
		<div class="row">
			<div class="large-6 columns">
				<div class="password-field">
					<div class="password-container">
						<?= $this->Form->input('passwd', array('type' => 'password', 'id' => 'passwd', 'placeholder' => 'New Password', 'pattern' => 'strong_password', 'required', 'style' => 'width:90%;', 'label' => 'New Password <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> New password should contain atleast one small letter, one capital letter, one number and one special character(!@#$%^&*~<>{}()+-`\'"?/|=_.:,:; etc) with minimum length of 8 characters total. Like: Abe&3292 .</small>')); ?>
						<!-- <i class="fa-solid fa-eye" id="eye"></i> Click the eye icon to show or hide password -->
						<i class="fa-solid fa-eye" id="eye"><label for="eye" style="font-family: 'Times New Roman', Times, serif; font-weight: normal; display: inline-block;" class="fs14"> &nbsp; Click here to show or hide password</label></i>
						<p> </p>
					</div>
				</div>
			</div>
			<div class="large-6 columns">
				<div class="password-confirmation-field">
					<div class="password-container">
						<?= $this->Form->input('password2', array('type' => 'password', 'id' => 'password2', 'placeholder' => 'Repeat the New Password', 'required', 'data-equalto' => 'passwd', 'style' => 'width:90%;', 'label' => 'Confirm Password <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> The New Password and Confirm Password values did not match or empty. You can cross-check your provided New password by clicking the eye icon above.</small>')); ?>
					</div>
				</div>
			</div>
			<div class="large-12 columns">
				<hr>
				<?= $this->Form->Submit('Reset Password', array('id' => 'SubmitID', /* 'disabled', */ 'class' => 'tiny radius button bg-blue')); ?>
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
		window.location.replace("/users/reset_password/" + obj.value);
	}

	function toggleSubmitButtonActive() {
		if ($("#UserID").val != 0 || $("#UserID").val != '') {
			$("#SubmitID").attr('disabled', false);
		}
	}

	const passwordInput = document.querySelector("#passwd");
	const password2Input = document.querySelector("#password2");
	const eye = document.querySelector("#eye");

	eye.addEventListener("click", function() {
		this.classList.toggle("fa-eye-slash");
		const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
		passwordInput.setAttribute("type", type);
		password2Input.setAttribute("type", type);
	});

	var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {
		if (form.passwd.value == '') { 
			form.passwd.focus();
			return false;
		}

        if (form.password2.value == '') { 
			form.password2.focus();
			return false;
		}

		if (form.passwd.value !== form.password2.value) { 
			alert('Passwords did not match. Please try again.');
			form.passwd.focus();
            passwordInput.setAttribute("type", 'text');
            password2Input.setAttribute("type", 'text');
			return false;
		}
		
		if (form_being_submitted) {
			alert("Resetting Password, Please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Resetting Password...';
		form_being_submitted = true;
		return true;
	};


	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>