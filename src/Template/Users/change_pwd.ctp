<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-key-outline" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Password Change: ' . ($this->request->getSession()->read('Auth.User.full_name') ?
                        $this->request->getSession()->read('Auth.User.full_name') .
                        ' (' . $this->request->getSession()->read('Auth.User.username') . ')' : '')); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;"><hr></div>
                <?php if ($force_password_change == 0 && !$password_duration_expired) { ?>
                    <h6 style="text-align:center" class="box-title"> <?= __('Change Password'); ?> </h6>
                    <p class="fs14">Hello <?= $this->request->getSession()->read('Auth.User.first_name') . ' ' . $this->request->getSession()->read('Auth.User.middle_name'); ?>,</p>
                <?php } ?>
                <?php if ($force_password_change != 0 || $password_duration_expired) { ?>
                    <?php if ($force_password_change && $first_time_login == 1) { ?>
                        <h6 style="text-align:center" class="box-title"> <?= __('Welcome to your SMiS Account'); ?> </h6>
                        <p class="fs14">Hello <?= $this->request->getSession()->read('Auth.User.first_name') . ' ' . $this->request->getSession()->read('Auth.User.middle_name'); ?>,</p>
                    <?php } else if ($force_password_change == 2 && $first_time_login == 0) { ?>
                        <h6 style="text-align:center" class="box-title"> <?= __('Your Account Password Is Reset'); ?> </h6>
                        <p class="fs14">Hello <?= $this->request->getSession()->read('Auth.User.first_name') . ' ' . $this->request->getSession()->read('Auth.User.middle_name'); ?>,</p>
                    <?php } else if ($password_duration_expired) { ?>
                        <h6 style="text-align:center" class="box-title"> <?= __('Time To Change Your Password'); ?> </h6>
                        <p class="fs14">Hello <?= $this->request->getSession()->read('Auth.User.first_name') . ' ' . $this->request->getSession()->read('Auth.User.middle_name'); ?>,</p>
                    <?php } else { ?>
                        <p class="fs14">Hello <?= $this->request->getSession()->read('Auth.User.first_name') . ' ' . $this->request->getSession()->read('Auth.User.middle_name'); ?>,</p>
                    <?php } ?>
                <?php } ?>
                <p style="text-align:center" class="rejected fs15">PLEASE READ THE FOLLOWING MESSAGE CAREFULLY</p>
                <?php if ($force_password_change && $first_time_login == 1) { ?>
                    <p class="fs14">You are logedin into this account for the first time. From this time onward, you will be responsible for any action/task performed using this account. As a result, you are required to change the password that you are given from your system administrator to your own. </p>
                <?php } else if ($force_password_change && $first_time_login == 0) { ?>
                    <p class="fs14"><strong>Based on your request</strong>, your account password was reset and you are login into this account for the first time after password reset is done. As a result, you are required to change the password that you are given from your <?= ($this->request->getSession()->read('Auth.User.role_id') == ROLE_STUDENT ? 'department' : 'system administrator'); ?> to your own.</p>
                    <p class="fs14">If you are getting this message without any request to reset your account password. Then it means your account password is changed and something has been done with your account illegally. If this is the case, please click on the "Log Out" button which is found on the upper right corner WITHOUT CHANGING THE GIVEN PASSWORD and contact your help desk so that your account will be investigated for any abuse which is done in the name of you illegally.<u> If you change the given password, it means you acknowledge the password reset and you can not make a complain after you make a change.</u></p>
                <?php } else if ($password_duration_expired) { ?>
                    <p class="fs14">According to the SMiS Password Policy, you are required to change your password every <?= $password_duration; ?> days. The last date your password changed was on <?= $this->Format->humanize_date($last_password_change_date); ?>.</p>
                <?php } ?>
                <p class="fs14"> Inorder to make your account secure, you are required to follow the following password policy:
                <ol class="fs14">
                    <li>Your password length should be a minimum of <?= $securitysetting->minimum_password_length; ?> and a maximum of <?= $securitysetting->maximum_password_length; ?> characters. The longer the password is, the harder to crack it using brute force attack.</li>
                    <?php if ($securitysetting->password_strength == 1) { ?>
                        <li>Your password should contain Uppercase Letters, Lowercase Letters, Numbers and symbols (Allowed: !@#$%^&*~<>{}()+-`'"?/|=_.:,:;)</li>
                    <?php } else { ?>
                        <li>Your password should contain Uppercase Letters, Lowercase Letters, Numbers and Symbols (Allowed: !@#$%^&*~<>{}()+-`'"?/|=_.:,:;)</li>
                    <?php } ?>
                    <li>Always use different password for this account from other access passwords including email, LDAP, Active Directory, etc.</li>
                    <li>Do not hint at the format of a password (e.g., "my family name")</li>
                </ol>
                </p>
                <p class="fs14">
                    <b>In addition to the above password creation guidline, you should note the following points: </b>
                <ol style="text-align:justify;">
                    <li>NEVER tell/share your password to any body even to your close friend, system administrator, administrative assistants, secretaries or boss.</li>
                    <li>If someone demands a password, direct them to the help desk.</li>
                    <li>Passwords should NEVER be written down or stored on-line without encryption.</li>
                    <li>Do not reveal a password in email, chat, or other electronic communication.</li>
                    <li>Do not speak about a password in front of others.</li>
                    <li>Do not reveal a password on questionnaires or security forms.</li>
                    <li>When you are asked to save your password by your browser, select NEVER REMEMBER option. You should also NEVER save your password on your browser or computer as it can be known at any time by the person who has access to your computer for any reason including to temporarily use your computer, to maintain your computer or for some other reason.</li>
                    <li>Make sure that the computer that you are using to access this application has good and updated anti virus to protect your computer against malware and other pesky attacks. If there is no antivirus installed on your computer or if you get "out of date" or similar warning from your anti virus software, please contact your help desk to get good anti virus and/or updates.</li>
                    <li>If an account or password compromise is suspected, please report the incident to your help desk as soon as possible.</li>
                </ol>
                </p>
                <?php if ($force_password_change == 1) { ?>
                    <p class="fs14" style="font-weight:bold">Please use the following form to change the given password to your own.</p>
                <?php } else { ?>
                    <p class="fs14" style="font-weight:bold">Please use the following form to change your password.</p>
                <?php } ?>
            </div>
        </div>
        <?= $this->Form->create($user, ['data-abide', 'url' => ['action' => 'changePwd'],
            'onsubmit' => 'return checkForm(this);']); ?>
        <?= $this->Form->hidden('id'); ?>
        <div class="row">
            <div class="large-5 columns">
                <div class="password-container">
                    <?= $this->Form->control('oldpassword', ['type' => "password",
                        'id' => 'UserOldpassword', 'value' => '', 'style' => 'width:90%;',
                        'placeholder' => 'Your old password',
                        'onchange' => 'toggleSubmitButtonActive()', 'required' => true,
                        'label' => ['text' => 'Your Old Password <small></small>', 'escape' => false],
                        'error' => 'Please provide your old password, the one you were
                        using before this password change.']); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="large-5 columns">
                <div class="password-field">
                    <div class="password-container">
                        <?= $this->Form->control('passwd', ['type' => 'password', 'id' => 'passwd',
                            'value' => '', 'style' => 'width:90%;', 'placeholder' => 'Your new password',
                            'pattern' => 'strong_password', 'required' => true,
                            'onkeyup' => 'passwordStrength(this.value)',
                            'label' => ['text' => 'New Password <small></small>',
                                'escape' => false],
                            'error' => 'New Password is required and must match the requirements .
                             Your new password should contain atleast one small letter, one capital letter,
                             one number and one special character(Allowed: !@#$%^&*~<>{}()+-`\'"?/|=_.:,:;)
                             with minimum length of 8 characters total. Like: Abe&3292']); ?>
                        <!-- <i class="fa-solid fa-eye" id="eye"></i> Click the eye icon to show or hide password -->
                        <i class="fa-solid fa-eye" id="eye">
                            <label for="eye" style="font-family: 'Times New Roman', Times,
                             serif; font-weight: normal; display: inline-block;" class="fs14"> &nbsp;
                                Click here to show or hide password</label></i>
                        <p> </p>
                    </div>
                </div>
            </div>
            <div class="large-7 columns">
                <label for="passwordStrength">Enter your new password until you get "<strong>Strong</strong>" or "<strong>Strongest</strong>" result.</label>
                <div id="passwordDescription">Password not entered</div>
                <div id="passwordStrength" class="strength0"></div>
                <br>
            </div>
        </div>
        <div class="row">
            <div class="large-5 columns">
                <div class="password-confirmation-field">
                    <div class="password-container">
                        <?= $this->Form->control('password2', ['type' => 'password', 'id' => 'password2',
                            'value' => '', 'style' => 'width:90%;',
                            'placeholder' => 'Repeat your new password here',
                            'required' => true, 'data-equalto' => 'passwd',
                            'label' => ['text' => 'Confirm Password <small></small>',
                                'escape' => false],
                            'error' => 'The New Password and Confirm Password values did not match or empty.
                             You can cross-check your provided New password by clicking the eye icon above.']); ?>
                    </div>
                </div>
            </div>
            <div class="large-12 columns">
                <hr>
                <?= $this->Form->button('Change Password', ['id' => 'SubmitID', 'disabled' => true,
                    'class' => 'tiny radius button bg-blue']); ?>
            </div>
        </div>
        <?= $this->Form->end(); ?>
    </div>
</div>

<?php

echo $this->Html->css('password_strength');
echo $this->Html->script('password_strength');

?>
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
        if ($("#UserOldpassword").val() != '') {
            $("#SubmitID").attr('disabled', false);
        }
    }

    var form_being_submitted = false;
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
            alert("Changing Password, Please wait a moment...");
            form.SubmitID.disabled = true;
            return false;
        }
        form.SubmitID.value = 'Changing Password...';
        form_being_submitted = true;
        return true; /* submit form */
    };

    // prevent possible form resubmission of a form
    // and disable default JS form resubmit warning dialog caused by pressing browser back button or reload or refresh button
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

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
