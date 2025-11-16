<?php
use Cake\Core\Configure;
?>

<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-user-add-outline" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Create User Account for System Access') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;"><hr></div>

                <?= $this->Form->create($user, ['id' => 'UserAddForm', 'data-abide', 'type' => 'file']) ?>

                <div class="row">
                    <div class="large-3 columns">
                        <?= $this->Form->control('Staff.id', ['type' => 'hidden']) ?>
                        <?= $this->Form->control('Staff.title_id', [
                            'style' => 'width:50%',
                            'id' => 'StaffTitle',
                            'label' => ['text' => __('Title'), 'class' => 'control-label'],
                            'empty' => __('[ Select Title ]'),
                            'options' => $titles
                        ]) ?>
                    </div>
                    <div class="large-3 columns">
                        <?= $this->Form->control('Staff.education_id', [
                            'style' => 'width:100%',
                            'id' => 'Education',
                            'label' => ['text' => __('Education'), 'class' => 'control-label'],
                            'required' => true,
                            'empty' => __('[ Select Education Level ]'),
                            'options' => $educations
                        ]) ?>
                    </div>
                    <div class="large-3 columns">
                        <?= $this->Form->control('Staff.position_id', [
                            'style' => 'width:100%',
                            'id' => 'Position',
                            'label' => ['text' => __('Position'), 'class' => 'control-label'],
                            'required' => true,
                            'empty' => __('[ Select Position ]'),
                            'options' => $positions
                        ]) ?>
                    </div>
                    <div class="large-3 columns">
                        <?= $this->Form->control('Staff.service_wing_id', [
                            'style' => 'width:100%',
                            'id' => 'serviceWing',
                            'label' => ['text' => __('Service Wing'), 'class' => 'control-label'],
                            'required' => true,
                            'empty' => __('[ Select Service Wing ]'),
                            'options' => $servicewings
                        ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->control('Staff.first_name', [
                            'style' => 'width:100%',
                            'label' => ['text' => __('First Name'), 'class' => 'control-label'],
                            'required' => true
                        ]) ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->control('Staff.middle_name', [
                            'style' => 'width:100%',
                            'label' => ['text' => __('Middle Name'), 'class' => 'control-label'],
                            'required' => true
                        ]) ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->control('Staff.last_name', [
                            'style' => 'width:100%',
                            'label' => ['text' => __('Last Name'), 'class' => 'control-label'],
                            'required' => true
                        ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="large-4 columns">
                        <div style="padding-left: 3%;">
                            <h6 class="fs13 text-gray"><?= __('Sex') ?></h6>
                            <?= $this->Form->control('Staff.gender', [
                                'type' => 'radio',
                                'options' => ['male' => __('Male'), 'female' => __('Female')],
                                'id' => 'gender',
                                'name' => 'Staff[gender]',
                                'label' => false,
                                'separator' => ' '
                            ]) ?>
                        </div>
                    </div>
                    <div class="large-4 columns">
                        <?php
                        $from = date('Y') - Configure::read('Calendar.birthdayInPast', 20);
                        $to = (date('Y') - 20) + Configure::read('Calendar.birthdayAhead', 0);
                        $format = Configure::read('Calendar.dateFormat', 'DMY');
                        ?>
                        <?= $this->Form->control('Staff.birthdate', [
                            'style' => 'width:30%',
                            'label' => ['text' => __('Birth Date'), 'class' => 'control-label'],
                            'type' => 'date',
                            'dateFormat' => $format,
                            'minYear' => $from,
                            'maxYear' => $to
                        ]) ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->control('Staff.staffid', [
                            'style' => 'width:100%',
                            'type' => 'text',
                            'id' => 'staffid',
                            'label' => ['text' => __('Staff ID'), 'class' => 'control-label'],
                            'readonly' => $this->request->getSession()->read('Auth.User.role_id') != ROLE_SYSADMIN
                        ]) ?>
                    </div>
                </div>
                <?php if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_REGISTRAR): ?>
                    <div class="row">
                        <div class="large-6 columns">
                            <?= $this->Form->control('Staff.college_id', [
                                'style' => 'width:100%',
                                'label' => ['text' => __('College'), 'class' => 'control-label'],
                                'id' => 'college_id_1',
                                'empty' => __('[ Select College ]'),
                                'options' => $colleges,
                                'onchange' => 'getDepartmentList(1)',
                                'disabled' => $this->request->getSession()->read('Auth.User.role_id') != ROLE_SYSADMIN
                            ]) ?>
                            <?= $this->request->getSession()->read('Auth.User.role_id') != ROLE_SYSADMIN ? $this->Form->control('Staff.0.college_id', ['type' => 'hidden']) : '' ?>
                        </div>
                        <div class="large-6 columns">
                            <?= $this->Form->control('Staff.department_id', [
                                'style' => 'width:100%',
                                'label' => ['text' => __('Department'), 'class' => 'control-label'],
                                'id' => 'department_id_1',
                                'empty' => __('[ Select Department ]'),
                                'options' => $departments,
                                'disabled' => $this->request->getSession()->read('Auth.User.role_id') != ROLE_SYSADMIN
                            ]) ?>
                            <?= $this->request->getSession()->read('Auth.User.role_id') != ROLE_SYSADMIN ? $this->Form->control('Staff.0.department_id', ['type' => 'hidden']) : '' ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->control('User.role_id', [
                            'style' => 'width:100%',
                            'label' => ['text' => __('Role'), 'class' => 'control-label'],
                            'id' => 'RoleID',
                            'empty' => __('[ Select Role ]'),
                            'options' => $roles,
                            'required' => true
                        ]) ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->control('Staff.user_id', ['type' => 'hidden']) ?>
                        <?= $this->Form->control('User.id', ['type' => 'hidden']) ?>
                        <?= $this->Form->control('User.username', [
                            'style' => 'width:100%',
                            'label' => ['text' => __('Username'), 'class' => 'control-label'],
                            'id' => 'username',
                            'onchange' => 'toggleSubmitButtonActive()',
                            'required' => true
                        ]) ?>
                    </div>
                    <div class="large-4 columns">
                        <div class="email-field">
                            <?= $this->Form->control('Staff.email', [
                                'style' => 'width:100%',
                                'type' => 'email',
                                'required' => true,
                                'label' => ['text' => __('Email'), 'class' => 'control-label'],
                                'templates' => [
                                    'error' => '<small class="error" style="width:100%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                                ],
                                'error' => __('Email address is required and it must be a valid one.')
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="large-4 columns">
                        <div class="phone-field">
                            <?= $this->Form->control('Staff.phone_mobile', [
                                'style' => 'width:100%',
                                'type' => 'tel',
                                'id' => 'phonemobile',
                                'required' => true,
                                'label' => ['text' => __('Mobile Phone'), 'class' => 'control-label'],
                                'templates' => [
                                    'error' => '<small class="error" style="width:100%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                                ],
                                'error' => __('Mobile Phone number is required.')
                            ]) ?>
                        </div>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->control('Staff.phone_office', [
                            'style' => 'width:100%',
                            'type' => 'tel',
                            'label' => ['text' => __('Phone Office'), 'class' => 'control-label'],
                            'id' => 'phoneoffice'
                        ]) ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->control('Staff.address', [
                            'style' => 'width:100%',
                            'label' => ['text' => __('Address'), 'class' => 'control-label'],
                            'maxlength' => 20
                        ]) ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->control('Staff.attachments.0.upload', [
                            'type' => 'file',
                            'label' => ['text' => __('Profile Photo or Document (Optional)'), 'class' => 'control-label'],
                            'accept' => 'image/jpeg,image/png,application/pdf,application/msword',
                            'templates' => [
                                'error' => '<small class="error" style="width:100%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                            ]
                        ]) ?>

                    </div>
                </div>
                <hr>
                <?= $this->Form->button(__('Create User'), [
                    'id' => 'SubmitID',
                    'class' => 'tiny radius button bg-blue',
                    'disabled' => true
                ]) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<script type='text/javascript'>
    function getDepartmentList(id) {
        var cid = $("#college_id_" + id).val();
        if (cid != '' && cid != 0) {
            $("#department_id_" + id).attr('disabled', true);
            $("#department_id_" + id).empty();
            var formUrl = '/departments/get_department_combo/' + cid + '/0/1';
            $.ajax({
                type: 'get',
                url: formUrl,
                data: cid,
                success: function(data, textStatus, xhr) {
                    $("#department_id_" + id).attr('disabled', false);
                    $("#department_id_" + id).empty();
                    $("#department_id_" + id).append(data);
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });
            return false;
        } else {
            $("#department_id_" + id).empty().append('<option value="">[ Select College First ]</option>');
        }
    }

    function toggleSubmitButtonActive() {
        if ($("#username").val() != 0 && $("#username").val() != '') {
            $("#SubmitID").attr('disabled', false);
        }
    }

    var form_being_submitted = false;

    $('#SubmitID').click(function() {
        var isValid = true;
        var username = $('#username').val();
        var roleID = $('#RoleID').val();
        var genderRadios = document.getElementsByName('Staff[gender]');
        var isGenderSelected = false;

        var inputs = document.querySelectorAll('#UserAddForm input[required], #UserAddForm select[required]');

        for (var i = 0; i < inputs.length; i++) {
            if (!inputs[i].value) {
                isValid = false;
                inputs[i].focus();
                return false;
            }
        }

        for (var i = 0; i < genderRadios.length; i++) {
            if (genderRadios[i].checked) {
                isGenderSelected = true;
                break;
            }
        }

        if (!isGenderSelected) {
            alert('Please select sex.');
            isValid = false;
            return false;
        }

        if (username == '') {
            $('#username').focus();
            isValid = false;
            return false;
        }

        if (roleID == 0 || roleID == '') {
            $('#RoleID').focus();
            isValid = false;
            return false;
        }

        if (form_being_submitted) {
            alert("Adding User Account, please wait a moment...");
            $('#SubmitID').attr('disabled', true);
            isValid = false;
            return false;
        }

        if (!form_being_submitted && isValid) {
            $('#SubmitID').val('Creating User Account...');
            $('#SubmitID').attr('disabled', false);
            form_being_submitted = true;
            isValid = true;
            return true;
        } else {
            isValid = false;
            return false;
        }
    });

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<style>
    .error {
        background: #fff;
        color: red;
        border: solid thin red;
        border-radius: 5px;
        width: 100%;
    }
    .fs13 {
        font-size: 13px;
    }
    .text-gray {
        color: #666;
    }
</style>
