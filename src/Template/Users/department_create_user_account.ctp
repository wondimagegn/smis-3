<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-user-add-outline" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Create User Account for System Access.'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;"><hr></div>
                <?= $this->Form->create('User', array('data-abide', 'onSubmit' => 'return checkForm(this);')); ?>
                <?php
                if (!isset($staff_account_valid)) { ?>
                    <blockquote class="fs16">
                        <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                        Only staffs that does not have account appear here!
                    </blockquote>
                    <hr>
                    <fieldset>
                        <legend> <span style="color:gray;">&nbsp; <?php echo __('Search staff for new account'); ?> &nbsp; </span></legend>
                        <div class="row justify-content-md-center">
                            <div class="large-2 columns col-lg-2">
                                <p>&nbsp;</p>
                            </div>
                            <div class="large-8 columns col-md-auto">
                                <div class="large-12 columns">
                                    <div class="row collapse postfix-round">
                                        <div class="small-9 columns">
                                            <?= $this->Form->input('Staff.name', array('style' => "padding: 12px; 12px;", 'type' => "text", 'label' => false, 'placeholder' => 'Search by name or email')); ?></td>
                                        </div>
                                        <div class="small-3 columns">
                                            <?= $this->Form->Submit('Search', array('div' => false, 'class' => 'button postfix', 'name' => 'search')); ?></td>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="large-2 columns col-lg-2">
                                <p>&nbsp;</p>
                            </div>
                        </div>
                    </fieldset>
                    <?php
                }

                if (!empty($staffs) && !isset($staff_account_valid)) { ?>
                    <div class="staffs index mt-3 mb-3">
                        <table cellpadding="0" cellspacing="0" class="table-borderless">
                            <thead>
                                <tr>
                                    <td style="width:5%"> # </td>
                                    <td style="width:55%"> Full Name </td>
                                    <td style="width:30%"> Email </td>
                                    <td style="width:10%; text-align:center" class="actions"><?php echo __('Actions'); ?></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                foreach ($staffs as $user) {
                                    $check_existed_users_from_email = -1;
                                    if (isset($user['Staff']['email']) && !empty($user['Staff']['email'])) {
                                        $check_existed_users_from_email = ClassRegistry::init('User')->find('count', array('conditions' => array('User.email' => strtolower(trim($user['Staff']['email'])))));
                                    } ?>
                                    <tr>
                                        <td><?= $count++; ?></td>
                                        <td <?= ($check_existed_users_from_email != 0 ? 'style="color:gray; text-decoration: line-through;"' : ''); ?>>
                                            <?= $user['Title']['title'] . '. ' . $user['Staff']['full_name'] . ' (' . $user['Position']['position'] . ')'; ?>&nbsp;
                                        </td>
                                        <td <?= ($check_existed_users_from_email != 0 ? 'style="color:gray; text-decoration: line-through;"' : '');  ?>>
                                            <?= ($user['Staff']['email'] == "" ? '---' : $user['Staff']['email']); ?>&nbsp;
                                        </td>
                                        <td class="actions">
                                            <?php
                                            if ($check_existed_users_from_email != -1) {
                                                echo $this->Html->link(__('Create Account'), array('action' => 'department_create_user_account', $user['Staff']['id']));
                                                if ($check_existed_users_from_email > 0) {
                                                    echo '<br/><span class="rejected"> +' . $check_existed_users_from_email . ' more</span>';
                                                }
                                            } else {
                                                if ($check_existed_users_from_email == -1) {
                                                    echo '<span class="rejected">Email is required</span>';
                                                }
                                            } ?> &nbsp;
                                        </td>
                                    </tr>
                                    <?php
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                }

                if (isset($staff_account_valid)) { ?>
                    <br>
                    <div class="mt- col-md-12">
                        <div class="large-8 columns">
                            <table cellpadding="0" cellspacing="0" class="fs13 table">
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="fs13" style="font-weight:bold">Basic Data</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: white;"><?= $this->element('staff_basic'); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <br>
                                            <blockquote class="fs13">
                                                <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                                                You can use the recommened username generated or edit as needed. Please Make sure usernames are short and easly memorizable for the users.
                                            </blockquote>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="large-4 columns">
                            <?= debug( $staff_basic_data['Staff'][0]['id']); ?>
                            <?= $this->Form->hidden('Staff.0.id', array('value' => $staff_basic_data['Staff'][0]['id'])); ?>
                            <?= $this->Form->hidden('Staff.0.phone_mobile', array('value' => $staff_basic_data['Staff'][0]['phone_mobile'])); ?>
                            <table cellpadding="0" cellspacing="0" class="table">
                                <tbody>
                                    <tr>
                                        <td colspan=2 class="fs13" style="font-weight:bold"> Access Data </td>
                                    </tr>
                                    <tr>
                                        <td colspan=2 style="text-align:left">
                                            <?= $this->Form->input('User.username', array('class' => 'radius tiny tooltipster-growing tooltipstered', 'id' => 'username', 'title' => 'Please don\'t change this uniquely generated username unless you have some reason.', 'placeholder' => 'Something Like:' . $recommeded_username . '', 'value' => (!empty($this->request->data['User']['username']) ? $this->request->data['User']['username'] : $recommeded_username))); ?>
                                        </td>
                                    </tr>
                                    <!-- <tr><td colspan=2 style="text-align:left"><?= $this->Form->input('User.passwd', array('label' => 'Password')); ?> </td></tr> -->
                                    <tr>
                                        <td colspan=2 style="text-align:left"><?= $this->Form->input('User.role_id', array('empty' => '[ Select Role ]', 'id' => 'RoleID', 'onchange' => 'toggleSubmitButtonActive()', 'style' => 'width:150px;', 'value' => !empty($this->request->data['User']['role_id']) ? $this->request->data['User']['role_id'] : '')); ?> </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <div class="row">
                                <div class="large-4 columns" style="margin-top: 50px;">
                                    <?= $this->Form->submit('Create Account', array('name' => 'createAccount', 'disabled', 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue', 'div' => 'false')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function toggleSubmitButtonActive() {
        if ($("#RoleID").val != 0 || $("#RoleID").val != '') {
            $("#SubmitID").attr('disabled', false);
        }
    }

    var form_being_submitted = false; /* global variable */

    var checkForm = function(form) {
        if (form.username.value == 0) {
            form.username.focus();
            return false;
        }
        if (form.RoleID.value == 0) {
            form.RoleID.focus();
            return false;
        }

        if (form_being_submitted) {
            alert("Creating User Account, please wait a moment...");
            form.SubmitID.disabled = true;
            return false;
        }

        form.SubmitID.value = 'Creating User Account...';
        form_being_submitted = true;
        return true; /* submit form */
    };

    // prevent possible form resubmission of a form 
    // and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>