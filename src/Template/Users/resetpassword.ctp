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
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-lock-open-filled" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Reset Login Password for: '  . $this->data['User']['first_name'] . ' ' . $this->data['User']['middle_name']. ' ' . $this->data['User']['last_name']. ' (' .  $this->data['User']['username'] . ')'); ?></span>
        </div>
    </div>
    <div class="box-body">

        <div class="row">

            <?= $this->Form->create('User', array('action' => 'resetpassword', 'data-abide', 'onSubmit' => 'return checkForm(this);')); ?>
            <?= $this->Form->hidden('id'); ?>
            <?= $this->Form->hidden('User.role_id'); ?>

            <div class="large-12 columns">
                <div style="margin-top: -30px;"><hr></div>
                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->input('User.first_name', array('type' => 'text', 'required', 'pattern' => '[a-zA-Z]+', 'style' => 'width:85%;', 'label' => 'First name <small></small></label><small class="error">First Name is required and must be a string.</small>', 'disabled')); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('User.middle_name', array('type' => 'text', 'required', 'pattern' => '[a-zA-Z]+', 'style' => 'width:85%;', 'label' => 'Middle name <small></small></label><small class="error">Middle Name is required and must be a string.</small>', 'disabled')); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('User.last_name', array('type' => 'text', 'required', 'pattern' => '[a-zA-Z]+','style' => 'width:85%;', 'label' => 'First name <small></small></label><small class="error">Last Name is required and must be a string.</small>', 'disabled')); ?>
                    </div>
                </div>
            </div>
            <div class="large-12 columns">
                <div class="row">
                    <div class="large-6 columns">
                        <?= $this->Form->input('User.username', array('type' => 'text',  'required', 'pattern' => '[a-zA-Z]+', 'style' => 'width:90%;', 'label' => 'Usename <small></small></label><small class="error">Username is required and must be a string.</small>', 'disabled')); ?>
                    </div>
                    <div class="large-6 columns">
                        <?= $this->Form->input('Role.name', array('type' => 'text',  'required', 'pattern' => '[a-zA-Z]+', 'style' => 'width:90%;', 'label' => 'Role <small></small></label><small class="error">Role is required and must be a string.</small>', 'disabled')); ?>
                    </div>
                </div>
            </div>

            <div class="large-6 columns">
                <?= $this->Form->input('passwd', array('type' => 'password', 'id' => 'passwd',  'placeholder' => 'New Password', 'pattern' => 'strong_password', 'required', 'style' => 'width:90%;', 'label' => 'New Password <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> New password should contain atleast one small letter, one capital letter, one number and one special character(!@#$%^&*~<>{}()+-`\'"?/|=_.:,:; etc) with minimum length of 8 characters total. Like: Abe&3292 .</small>')); ?>
                <!-- <i class="fa-solid fa-eye" id="eye"></i> Click the eye icon to show or hide password -->
                <i class="fa-solid fa-eye" id="eye"><label for="eye" style="font-family: 'Times New Roman', Times, serif; font-weight: normal; display: inline-block;" class="fs14"> &nbsp; Click here to show or hide password</label></i>
				<p> </p>
            </div>
            <div class="large-6 columns">
                <?= $this->Form->input('password2', array('type' => 'password', 'id' => 'password2', 'placeholder' => 'Repeat the New Password', 'required', 'data-equalto' => 'passwd', 'style' => 'width:90%;', 'onchange' => 'toggleSubmitButtonActive()', 'label' => 'Confirm Password <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> The New Password and Confirm Password values did not match or empty. You can cross-check your provided New password by clicking the eye icon above.</small>')); ?>
            </div>
            <div class="large-12 columns">
                <hr>
                <?= $this->Form->Submit('Reset Password', array('id' => 'SubmitID', /* 'disabled', */ 'class' => 'tiny radius button bg-blue')); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	const passwordInput = document.querySelector("#passwd");
    const password2Input = document.querySelector("#password2");
	const eye = document.querySelector("#eye");

	eye.addEventListener("click", function() {
		this.classList.toggle("fa-eye-slash");
		const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
		passwordInput.setAttribute("type", type);
        password2Input.setAttribute("type", type);
	});

    function toggleSubmitButtonActive() {
		if ($("#passwd").val != '' && $("#password2").val != '') {
			$("#SubmitID").attr('disabled', false);
		}
	}

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
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>