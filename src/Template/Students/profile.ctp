<?php

use Cake\Core\Configure;
use Cake\Utility\Hash;

$this->Html->script('amharictyping', ['block' => true]);
$role_id = $this->request->getSession()->read('Auth.User.role_id');

?>
<?php if (isset($studentDetail) && !empty($studentDetail->toArray())) { ?>
    <div class="box">
        <div class="box-header bg-transparent">
            <div class="box-title" style="margin-top: 10px;">
                <i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
                <span style="font-size: medium; font-weight: bold; margin-top: 20px;">

                <?= sprintf(__('Update Student Details: %s %s'),
                    $studentDetail->full_name, $studentDetail->studentnumber) ?>

            </span>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="large-12 columns">
                    <div style="margin-top: -40px;"><hr></div>
                    <?php
                    $this->assign('title_details', sprintf(
                        '%s%s - %s (%s)',
                        !empty($this->request->getParam('controller')) ? ' ' . \Cake\Utility\Inflector::humanize(\Cake\Utility\Inflector::underscore($this->request->getParam('controller'))) : '',
                        !empty($this->request->getParam('action')) && $this->request->getParam('action') !== 'index' ? ' | ' . ucwords(str_replace('_', ' ', $this->request->getParam('action'))) : '',
                        $studentDetail->full_name,
                        $studentDetail->studentnumber
                    ));

                    $formOptions = ['id' => 'StudentProfileForm', 'data-abide' => '',
                        'novalidate' => 'novalidate'];
                    if (ALLOW_STUDENTS_TO_UPLOAD_PROFILE_PICTURE) {
                        $formOptions['type'] = 'file';
                    }
                    echo $this->Form->create($studentDetail, $formOptions);
                    ?>
                    <ul class="tabs" data-tab>
                        <li class="tab-title active"><a href="#basic_data"><?= __('Basic Student Information') ?></a></li>
                        <li class="tab-title"><a href="#add_address"><?= __('Address & Primary Contact') ?></a></li>
                        <li class="tab-title"><a href="#education_background"><?= __('Educational Background') ?></a></li>
                    </ul>
                    <div class="tabs-content edumix-tab-horz">
                        <div class="content active" id="basic_data" style="padding-left: 0px; padding-right: 0px;">
                            <div class="row">
                                <div class="large-12 columns">
                                    <hr style="margin-top: -10px;">
                                    <?php
                                    echo $this->Form->hidden('id', ['value' => $studentDetail->id]);
                                    if (!empty($studentDetail->contacts[0]->id)) {
                                        echo $this->Form->hidden('contacts.0.id',
                                            ['value' => $studentDetail->contacts[0]->id]);
                                    }
                                    echo $this->Form->hidden('contacts.0.student_id', ['value' => $studentDetail->id]);


                                    $errors = $studentDetail->getErrors();
                                    if (!empty($errors) && !empty($this->request->getData('Student'))) {
                                        $flatErrors = Hash::flatten($errors);
                                        ?>
                                        <div class="errorSummary">
                                            <ul>
                                                <?php foreach ($flatErrors as $key => $value): ?>
                                                    <li class="rejected"><?= h($value) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <?php
                                    }

                                    $ethiopianStudent = !empty($studentDetail->country_id) && $studentDetail->country_id == COUNTRY_ID_OF_ETHIOPIA;
                                    $ugProgram = !empty($studentDetail->program_id) && $studentDetail->program_id
                                        == PROGRAM_UNDERGRADUATE;
                                    $faidaMandatory = (Configure::read('App.forceAllStudentsToFillFaidaFin') || (!empty($isGraduatingClassStudent) && $isGraduatingClassStudent));

                                    if (!empty($studentMobilePhoneNumberError)) {
                                        ?>
                                        <div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                            <span style='margin-right: 15px;'></span><?= h($studentMobilePhoneNumberError) ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="large-6 columns">
                                    <table cellspacing="0" cellpadding="0" class="table">
                                        <tbody>
                                        <tr>
                                            <td><strong><?= __('Demographic Information') ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: white;">
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('first_name', [
                                                        'label' => __('First Name (English):'),
                                                        'readonly' => true
                                                    ]) ?>
                                                    <?= $this->Form->hidden('first_name', [
                                                        'value' => $studentDetail->first_name ??
                                                            ($studentDetail->accepted_student->first_name ?? null)
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('middle_name', [
                                                        'label' => __('Middle Name (English):'),
                                                        'readonly' => true
                                                    ]) ?>
                                                    <?= $this->Form->hidden('middle_name', [
                                                        'value' => $studentDetail->middle_name ??
                                                            ($studentDetail->accepted_student->middle_name ?? null)
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('last_name', [
                                                        'label' => __('Last Name (English):'),
                                                        'readonly' => true
                                                    ]) ?>
                                                    <?= $this->Form->hidden('last_name', [
                                                        'value' => $studentDetail->last_name
                                                            ?? ($studentDetail->accepted_student->last_name ?? null)
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <label>
                                                        <?= __('First Name (Amharic):') . ($ethiopianStudent ? '&nbsp;<span class="rejected">*</span>' : '') ?>
                                                        <?= $this->Form->control('amharic_first_name', [
                                                            'label' => false,
                                                            'required' => $ethiopianStudent,
                                                            'id' => 'AmharicText',
                                                            'onkeypress' => 'return AmharicPhoneticKeyPress(event, this);'
                                                        ]) ?>
                                                    </label>
                                                </div>
                                                <div class="large-12 columns">
                                                    <label>
                                                        <?= __('Middle Name (Amharic):') . ($ethiopianStudent ? '&nbsp;<span class="rejected">*</span>' : '') ?>
                                                        <?= $this->Form->control('amharic_middle_name', [
                                                            'label' => false,
                                                            'required' => $ethiopianStudent,
                                                            'id' => 'AmharicTextMiddleName',
                                                            'onkeypress' => 'return AmharicPhoneticKeyPress(event, this);'

                                                        ]) ?>
                                                    </label>
                                                </div>
                                                <div class="large-12 columns">
                                                    <label>
                                                        <?= __('Last Name (Amharic):') . ($ethiopianStudent ? '&nbsp;<span class="rejected">*</span>' : '') ?>
                                                        <?= $this->Form->control('amharic_last_name', [
                                                            'label' => false,
                                                            'required' => $ethiopianStudent,
                                                            'id' => 'AmharicTextLastName',
                                                            'onkeypress' => 'return AmharicPhoneticKeyPress(event, this);'
                                                        ]) ?>
                                                    </label>
                                                </div>
                                                <?php if ($ethiopianStudent) { ?>
                                                    <div class="large-12 columns">
                                                        <hr><br>
                                                        <?php
                                                        if (empty($studentDetail->faida_alias_number)) {
                                                            echo  $this->Form->control('faida_alias_number', [
                                                                'id' => 'faidaFan',
                                                                'required' => Configure::read('App.forceAllStudentsToFillFaidaFin'),
                                                                'type' => 'text',
                                                                'label' => __('Fayda FAN Number (16 digit): &nbsp;<span class="rejected">* (Fill out this very carefully!)</span>'),
                                                                'escape' => false, // important to render HTML
                                                                'style' => 'width:100%;',
                                                                'placeholder' => __('Check the FRONT SIDE of your Fayda ID for FAN.'),
                                                                'onBlur' => 'checkFaidaFan(this)'
                                                            ]);
                                                        } else {
                                                           echo  $this->Form->control('faida_alias_number', [
                                                                'id' => 'faidaFan',
                                                                'readonly' => true,
                                                                'required' => Configure::read('App.forceAllStudentsToFillFaidaFin'),
                                                                'type' => 'text',
                                                                'label' => __('Fayda FAN Number (16 digit): &nbsp;<span class="rejected">*</span>'),
                                                                'escape' => false, // allow HTML rendering in label
                                                                'style' => 'width:100%;',
                                                                'placeholder' => __('Check the FRONT SIDE of your Fayda ID for FAN.')
                                                            ]);
                                                            echo $this->Form->hidden('faida_alias_number', [
                                                                'value' => $studentDetail->faida_alias_number ??
                                                                    ($this->request->getData('faida_alias_number') ?? '')
                                                            ]);
                                                        }
                                                        ?>
                                                        <br>
                                                    </div>
                                                    <div class="large-12 columns">
                                                        <?php
                                                        if (empty($studentDetail->faida_identification_number)) {
                                                            echo $this->Form->control('faida_identification_number', [
                                                                'id' => 'faidaFin',
                                                                'required' => Configure::read('App.forceAllStudentsToFillFaidaFin'),
                                                                'type' => 'text',
                                                                'label' => __('Fayda FIN Number (12 digit): &nbsp;<span class="rejected">* (Fill out this very carefully!)</span>'),
                                                                'escape' => false, // allow HTML in label
                                                                'style' => 'width:100%;',
                                                                'placeholder' => __('Check the BACK SIDE of your Fayda ID for FIN.'),
                                                                'onBlur' => 'checkFaidaFin(this)'
                                                            ]);
                                                        } else {
                                                           echo $this->Form->control('faida_identification_number', [
                                                               'id' => 'faidaFin',
                                                               'readonly' => true,
                                                               'required' => Configure::read('App.forceAllStudentsToFillFaidaFin'),
                                                               'type' => 'text',
                                                               'label' => __('Fayda FIN Number (12 digit): &nbsp;<span class="rejected">*</span>'),
                                                               'escape' => false, // enable HTML rendering in label
                                                               'style' => 'width:100%;',
                                                               'placeholder' => __('Check the BACK SIDE of your Fayda ID for FIN.')
                                                           ]);
                                                            echo $this->Form->hidden('faida_identification_number', [
                                                                'value' => $studentDetail->faida_identification_number ?? ($this->request->getData('faida_identification_number') ?? '')
                                                            ]);
                                                        }
                                                        ?>
                                                        <br><hr>
                                                    </div>
                                                <?php } ?>
                                                <div class="large-12 columns">
                                                    <label>
                                                        <?= __('Estimated Graduation Date: (G.C)') ?>
                                                        <?= $this->Form->control('estimated_grad_date', [
                                                            'label' => false,
                                                            'type' => 'date',
                                                            'minYear' => $studentAdmissionYear ?? date('Y'),
                                                            'maxYear' => $maximumEstimatedGraduationYearLimit ?? (date('Y') + \Cake\Core\Configure::read('Calendar.expectedGraduationInFuture')),
                                                            'orderYear' => 'desc',
                                                            'style' => 'width: 25%;'
                                                        ]) ?>
                                                    </label>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('gender', [
                                                        'label' => __('Sex:'),
                                                        'type' => 'select',
                                                        'style' => 'width:30%;',
                                                        'options' => ['Female' => 'Female', 'Male' => 'Male']
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('lanaguage', [
                                                        'label' => __('Primary Language:')
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('email', [
                                                        'type' => 'email',
                                                        'id' => 'email',
                                                        'required' => true,
                                                        'label' => __('Email: &nbsp;<span class="rejected">*</span>'),
                                                        'escape' => false, // enable HTML in label
                                                    ]); ?>

                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('email_alternative', [
                                                        'type' => 'email',
                                                        'id' => 'alternativeEmail',
                                                        'label' => __('Alternative Email:')
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('phone_home', [
                                                        'type' => 'tel',
                                                        'id' => 'phoneoffice',
                                                        'label' => __('Phone (Home):')
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('phone_mobile', [
                                                        'type' => 'tel',
                                                        'id' => 'etPhone',
                                                        'required' => true,
                                                        'label' => __('Phone (Mobile): &nbsp;
<span class="rejected">*</span>'),
                                                        'escape' => false, // enable HTML in label

                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('birthdate', [
                                                        'label' => __('Birth Date: (G.C) &nbsp;<span class="rejected">* (set this carefully!)</span>'),
                                                        'type' => 'date',
                                                        'minYear' => date('Y') - Configure::read('Calendar.birthdayInPast'),
                                                        'maxYear' => date('Y') - 17,
                                                        'orderYear' => 'desc',
                                                        'style' => 'width: 25%;',
                                                        'escape' => false, // enable HTML in label

                                                    ]) ?>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <br><br>
                                </div>
                                <div class="large-6 columns">
                                    <table cellpadding="0" cellspacing="0" class="table">
                                        <tbody>
                                        <tr><td colspan=2><strong><?= __('Profile Picture') ?></strong></td></tr>

                                        <?php
                                        if (!empty($studentDetail->attachments[0])){
                                            ?>
                                            <tr>
                                                <td>
                                            <?php
                                            if($studentDetail->attachments[0]->isLegacy()) {
                                                $legacyURl = $studentDetail->attachments[0]->getLegacyUrlForCake2();
                                                echo $this->Html->image(
                                                    $legacyURl,
                                                    [
                                                        'alt' => h($studentDetail->attachments[0]->file_name ??
                                                            $studentDetail->attachments[0]->basename ?? 'Attachment'),
                                                        'style' => 'max-width: 100px; max-height: 100px;',
                                                        'class' => 'attachment-thumbnail'
                                                    ]
                                                );

                                            } else {

                                                echo $this->Html->image(
                                                    $studentDetail->attachments[0]->getUrl(),
                                                    [

                                                        'alt' => h($studentDetail->attachments[0]->file_name ??
                                                            $studentDetail->attachments[0]->basename ?? 'Attachment'),
                                                        'style' => 'max-width: 100px; max-height: 100px;',
                                                        'class' => 'attachment-thumbnail',
                                                        'escape' => false  // optional, in case the URL contains any HTML
                                                    ]
                                                );

                                                $canBeDeleted = date("Y-m-d H:i:s", strtotime("-" .
                                                DAYS_ALLOWED_TO_DELETE_PROFILE_PICTURE_FROM_LAST_UPLOAD . " days"));
                                            if ($canBeDeleted < $studentDetail->attachments[0]->modified
                                                && $role_id == ROLE_STUDENT) {
                                                $actionControllerId = 'edit~students~' .
                                                    $studentDetail->attachments[0]->foreign_key;

                                               ?>
                                                <br/>
                                                <a
                                                        onclick="if(confirm('Delete profile picture uploaded on <?= h($studentDetail->attachments[0]->modified) ?>?')) {
                                                            fetch('<?= $this->Url->build([
                                                            'controller' => 'Attachments',
                                                            'action' => 'delete',
                                                            $studentDetail->attachments[0]->id,
                                                            $actionControllerId
                                                        ]) ?>', {
                                                            method: 'POST',
                                                            headers: {
                                                            'X-CSRF-Token': '<?= $this->request->getParam('_csrfToken') ?>',
                                                            'X-Requested-With': 'XMLHttpRequest', // 🔑 tell Cake it's AJAX
                                                            'Accept': 'application/json'
                                                            }
                                                            })
                                                            .then(res => res.json())
                                                            .then(data => {
                                                            if (data.success) {
                                                            alert(data.message);
                                                            location.reload();
                                                            } else {
                                                            alert(data.message);
                                                            }
                                                            });
                                                            }">
                                                    <?= __('Delete Profile Picture') ?>
                                                </a>


                                                <?php


                                            }
                                            }
                                        ?>
                                                </td>
                                            </tr>
                                        <?php
                                        } else {
                                            ?>
                                            <tr><td valign="top"><img src="/img/noimage.jpg" width="144"
                                                                      class="profile-picture"></td></tr>
                                            <tr><td>
                                                    <?php
                                                    if (ALLOW_STUDENTS_TO_UPLOAD_PROFILE_PICTURE)
                                                    {
                                                    echo $this->Form->control('attachments.0.upload', ['type' => 'file',
                                                    'label' => __('Upload Profile Picture'),
                                                    'required' => Configure::read('App.requireStudentsToUploadProfilePictureWhenUpdatingProfile'),
                                                    'accept' => '.jpg,.jpeg,.png'
                                                    ]);

                                                        $this->Form->hidden('attachments.0.model', [
                                                            'value' => 'Student'
                                                        ]);
                                                    }
                                                    ?>
                                                </td></tr>

                                            <?php
                                        }
                                        ?>

                                        <tr><td colspan=2><strong><?= __('Access Information') ?></strong></td></tr>
                                        <tr><td style="padding-left:30px;">
                                                <?= sprintf(__('Username: %s'),$studentDetail->user->username ) ?>

                                            </td></tr>
                                        <tr><td style="padding-left:30px;">
                                                <?= __('Last Login:') ?>
                                                <?php
                                                if (empty($studentDetail->user->last_login) || $studentDetail->user->last_login == '0000-00-00 00:00:00' || is_null($studentDetail->user->last_login)) {
                                                    echo '<span class="rejected">' . __('Never logged in') . '</span>';
                                                } else {
                                                    echo $this->Time->timeAgoInWords($studentDetail->user->last_login, ['format' => 'MMM d, Y', 'end' => '1 year', 'accuracy' => ['month' => 'month']]);
                                                }
                                                ?>
                                            </td></tr>
                                        <tr><td style="padding-left:30px;">
                                                <?= __('Last Password Change:') ?>
                                                <?php
                                                if (empty($studentDetail->user->last_password_change_date) || $studentDetail->user->last_password_change_date == '0000-00-00 00:00:00' || is_null($studentDetail->user->last_password_change_date)) {
                                                    echo '<span class="rejected">' . __('Never Changed') . '</span>';
                                                } else {
                                                    echo $this->Time->timeAgoInWords($studentDetail->user->last_password_change_date, ['format' => 'MMM d, Y', 'end' => '1 year', 'accuracy' => ['month' => 'month']]);
                                                }
                                                ?>
                                            </td></tr>
                                        <tr><td style="padding-left:30px;">
                                               <?= sprintf(__('Failed Logins: %s'),$studentDetail->user->failed_login ?? '---' ) ?>

                                            </td></tr>
                                        <tr><td style="padding-left:30px;">

                                                <?= sprintf(__('Ecardnumber: %s'),$studentDetail->ecardnumber ?? '---' ) ?>

                                            </td></tr>
                                        <?php
                                        $preEngineeringColleges = Configure::read('preengineering_college_ids');
                                        if ($studentDetail->program_id == PROGRAM_REMEDIAL) {
                                            $stream = 'Remedial Program';
                                        } elseif (!empty($studentDetail->college->stream) && $studentDetail->college->stream == STREAM_NATURAL && in_array($studentDetail->college_id, $preEngineeringColleges)) {
                                            $stream = 'Freshman - Pre Engineering';
                                        } elseif (!empty($studentDetail->college->stream) && $studentDetail->college->stream == STREAM_NATURAL) {
                                            $stream = 'Freshman - Natural Stream';
                                        } elseif (!empty($studentDetail->college->stream) && $studentDetail->college->stream == STREAM_SOCIAL) {
                                            $stream = 'Freshman - Social Stream';
                                        } else {
                                            $stream = '---';
                                        }
                                        ?>
                                        <tr><td colspan=2><strong><?= __('Classification of Admission') ?></strong></td></tr>
                                        <tr><td style="padding-left:30px;">

                                                <?= sprintf(__('Program: %s'),$programs[$studentDetail->program_id] ?? '---' ) ?>

                                            </td></tr>
                                        <tr><td style="padding-left:30px;">
                                                <?= sprintf(__('Program Type: %s'),$programTypes[$studentDetail->program_type_id] ?? '---' ) ?>


                                            </td></tr>
                                        <tr><td style="padding-left:30px;">
                                                <?= (!empty($studentDetail->college->type) ? $studentDetail->college->type : 'College') . ': ' . ($colleges[$studentDetail->college_id] ?? '') ?>
                                            </td></tr>
                                        <tr><td style="padding-left:30px;">
                                                <?php
                                                $departmentLabel = !empty($studentDetail->department->type) ? $studentDetail->department->type : 'Department';
                                                $departmentName = $studentDetail->department->name ?? ($departments[$studentDetail->department_id] ?? $stream);

                                                echo sprintf(
                                                    __('%s: %s'),
                                                    $departmentLabel,$departmentName

                                                )

                                                ?>
                                            </td></tr>
                                        <tr><td style="padding-left:30px;">
                                                <?= sprintf(__('Admission Year: %s'),$studentDetail->academicyear ?? '---' ) ?>

                                            </td></tr>
                                        <tr><td style="padding-left:30px;">

                                                <?= sprintf(__('Admission Date: %s'),
                                                    $this->Time->format($studentDetail->admissionyear, 'MMM d, Y')) ?>

                                            </td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="content" id="add_address" style="padding-left: 0px; padding-right: 0px;">
                            <div class="row">
                                <div class="large-12 columns">
                                    <hr style="margin-top: -10px;">
                                </div>
                                <div class="large-6 columns">
                                    <table cellspacing="0" cellpadding="0" class="table">
                                        <tbody>
                                        <tr>
                                            <td><strong><?= __('Your Home Address') ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: white;">
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('country_id', [
                                                        'id' => 'country_id_2',
                                                        'label' => __('Country:'),
                                                        'empty' => false,
                                                        'style' => 'width:70%;',
                                                        'default' => COUNTRY_ID_OF_ETHIOPIA
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('region_id', [
                                                        'id' => 'region_id_2',
                                                        'label' => __('Region:'),
                                                        'style' => 'width:70%;'
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?php if ($studentDetail->graduated == 1) { ?>
                                                        <?= $this->Form->control('zone_subcity', ['label' => __('Zone/Subcity:')]) ?>
                                                    <?php } else { ?>
                                                        <?= $this->Form->control('zone_id', [
                                                            'id' => 'zone_id_2',
                                                            'label' => __('Zone:'),
                                                            'empty' => '[ Select Zone ]',
                                                            'style' => 'width:70%;'
                                                        ]) ?>
                                                    <?php } ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?php if ($studentDetail->graduated == 1) { ?>
                                                        <?= $this->Form->control('woreda', ['label' => __('Woreda:')]) ?>
                                                    <?php } else { ?>
                                                        <?= $this->Form->control('woreda_id', [
                                                            'id' => 'woreda_id_2',
                                                            'label' => __('Woreda:'),
                                                            'empty' => '[ Select Woreda ]',
                                                            'style' => 'width:70%;'
                                                        ]) ?>
                                                    <?php } ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('city_id', [
                                                        'label' => __('City:'),
                                                        'id' => 'city_id_2',
                                                        'style' => 'width:70%;',
                                                        'empty' => '[ Select City or Leave, if not listed ]'
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('kebele', ['label' => __('Kebele:')]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('house_number', ['label' => __('House Number:')]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('address1', ['label' => __('Address:')]) ?>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <br><br>
                                </div>
                                <div class="large-6 columns">
                                    <table cellspacing="0" cellpadding="0" class="table">
                                        <tbody>
                                        <tr>
                                            <td><strong><?= __('Your Primary Emergency Contact') ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: white;">
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('contacts.0.first_name', [
                                                        'label' => __('First Name:'),
                                                        'type' => 'text',
                                                        'required' => true,
                                                        'onBlur' => 'checkIsAlpha(this)'
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('contacts.0.middle_name', [
                                                        'label' => __('Middle Name:'),
                                                        'type' => 'text',
                                                        'required' => true,
                                                        'onBlur' => 'checkIsAlpha(this)'
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('contacts.0.last_name', [
                                                        'label' => __('Last Name:'),
                                                        'type' => 'text',
                                                        'required' => true,
                                                        'onBlur' => 'checkIsAlpha(this)'
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('contacts.0.country_id', [
                                                        'label' => __('Country:'),
                                                        'id' => 'country_id_1',
                                                        'default' => COUNTRY_ID_OF_ETHIOPIA,
                                                        'style' => 'width:70%;',
                                                        'onchange' => 'updateRegionCity(1)'
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('contacts.0.region_id', [
                                                        'label' => __('Region:'),
                                                        'options' => $regionsAll,
                                                        'id' => 'region_id_1',
                                                        'empty' => '[ Select Region ]',
                                                        'style' => 'width:70%;'
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('contacts.0.zone_id', [
                                                        'label' => __('Zone:'),
                                                        'options' => $zonesAll,
                                                        'id' => 'zone_id_1',
                                                        'empty' => '[ Select Zone ]',
                                                        'style' => 'width:70%;'
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('contacts.0.woreda_id', [
                                                        'label' => __('Woreda:'),
                                                        'options' => $woredasAll,
                                                        'id' => 'woreda_id_1',
                                                        'empty' => '[ Select Woreda ]',
                                                        'style' => 'width:70%;'
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('contacts.0.city_id', [
                                                        'label' => __('City:'),
                                                        'options' => $citiesAll,
                                                        'id' => 'city_id_1',
                                                        'style' => 'width:70%;',
                                                        'empty' => '[ Select City or Leave, if not listed ]'
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('contacts.0.email', [
                                                        'type' => 'email',
                                                        'label' => __('Email:')
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('contacts.0.alternative_email', [
                                                        'type' => 'email',
                                                        'label' => __('Alternative Email:')
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('contacts.0.phone_home', [
                                                        'type' => 'tel',
                                                        'id' => 'intPhone1',
                                                        'label' => __('Phone (Home):')
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('contacts.0.phone_office', [
                                                        'type' => 'tel',
                                                        'id' => 'intPhone2',
                                                        'label' => __('Phone (Office):')
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('contacts.0.phone_mobile', [
                                                        'type' => 'tel',
                                                        'id' => 'phonemobile',
                                                        'label' => __('Phone (Mobile):')
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <?= $this->Form->control('contacts.0.address1', [
                                                        'label' => __('Address:')
                                                    ]) ?>
                                                </div>
                                                <div class="large-12 columns">
                                                    <hr>
                                                    <?= $this->Form->control('contacts.0.primary_contact', [
                                                        'label' => __('Primary Contact?'),
                                                        'checked' => true
                                                    ]) ?>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="content" id="education_background" style="padding-left: 0px; padding-right: 0px;">
                            <?php
                            if (($role_id== ROLE_STUDENT && $studentDetail->program_id == PROGRAM_UNDERGRADUATE)
                                || !empty($this->request->getData('high_school_education_backgrounds'))) {
                                ?>
                                <hr style="margin-top: -10px;">
                                <blockquote>
                                    <h6><i class="fa fa-info"></i> &nbsp; <?= __('Important Note:') ?></h6>
                                    <span style="text-align:justify;" class="fs15 text-black">
                <?= __('Information you provide on this page should be properly formatted and error-free as <b><i class="rejected">it affects official transcript or student copy address contents</i></b>. <br> Please also make sure that the school name does not exceed 30 characters and replace special characters like - , ( , ) with a space if found in the school name. <br> If you want to add more than one record for the required information, you can use the \'Add Additional School\' or \'Add Additional Subject\' buttons and make sure that the information you are entering is chronologically ordered from the most recent to the oldest for high school background information.') ?>
            </span>
                                </blockquote>
                                <hr>
                                <?php
                                $highSchoolFields = [
                                    'school_level' => '1',
                                    'name' => '2',
                                    'national_exam_taken' => '3',
                                    'region_id' => '4',
                                    'zone' => '5',
                                    'town' => '6'
                                ];
                                $highSchoolAllFields = implode(',', array_keys($highSchoolFields));
                                ?>
                                <div class="row">
                                    <div class="large-12 columns">
                                        <div style="overflow-x:auto;">
                                            <table cellpadding="0" cellspacing="0" class="table">
                                                <thead>
                                                <tr>
                                                    <td colspan="7" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                        <h6 class="fs18 text-black"><?= __('Senior Secondary/Preparatory School Attended') ?></h6>
                                                    </td>
                                                </tr>
                                                </thead>
                                            </table>
                                            <table id="high_school_education" cellpadding="0" cellspacing="0" class="table">
                                                <thead>
                                                <tr>
                                                    <th style="width: 3%;" class="center">#</th>
                                                    <th style="width: 16%;" class="center"><?= __('School Level') ?></th>
                                                    <th style="width: 21%;" class="center"><?= __('Name') ?></th>
                                                    <th style="width: 15%;" class="center"><?= __('National Exam Taken') ?></th>
                                                    <th style="width: 15%;" class="center"><?= __('Region') ?></th>
                                                    <th style="width: 15%;" class="center"><?= __('Zone') ?></th>
                                                    <th style="width: 15%;" class="center"><?= __('Town') ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $highSchoolData = $this->request->getData('high_school_education_backgrounds') ?: ($studentDetail->high_school_education_backgrounds ?? []);
                                                if (!empty($highSchoolData)) {
                                                    $count = 1;
                                                    foreach ($highSchoolData as $bk => $bv) {
                                                        echo $this->Form->hidden("high_school_education_backgrounds.$bk.student_id", ['value' => $studentDetail->id]);
                                                        if (!empty($bv['id'])) {
                                                            echo $this->Form->hidden("high_school_education_backgrounds.$bk.id");
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td class="center"><?= $count ?></td>
                                                            <td class="center">
                                                                <div style="margin-top: 10px;">
                                                                    <?= $this->Form->control("high_school_education_backgrounds.$bk.school_level", [
                                                                        'class' => 'otherRequiredText-input',
                                                                        'label' => false,
                                                                        'style' => 'width:100%;',
                                                                        'placeholder' => __('preparatory, highschool etc..'),
                                                                        'onBlur' => 'checkIsAlpha(this)',
                                                                        'required' => true
                                                                    ]) ?>
                                                                </div>
                                                            </td>
                                                            <td class="center">
                                                                <div style="margin-top: 10px;">
                                                                    <?= $this->Form->control("high_school_education_backgrounds.$bk.name", [
                                                                        'class' => 'otherRequiredText-input',
                                                                        'label' => false,
                                                                        'style' => 'width:100%;',
                                                                        'onBlur' => 'checkIsAlpha(this)',
                                                                        'required' => true,
                                                                        'id' => "high_school_education_backgrounds_{$bk}_name"
                                                                    ]) ?>
                                                                </div>
                                                            </td>
                                                            <td class="center">
                                                                <div style="margin-top: 10px;">
                                                                    <?= $this->Form->control("high_school_education_backgrounds.$bk.national_exam_taken", [
                                                                        'label' => false,
                                                                        'style' => 'width:100%;'
                                                                    ]) ?>
                                                                </div>
                                                            </td>
                                                            <td class="center">
                                                                <div style="margin-top: 10px;">
                                                                    <?= $this->Form->control("high_school_education_backgrounds.$bk.region_id", [
                                                                        'options' => $regionsAll,
                                                                        'style' => 'width:100%;',
                                                                        'type' => 'select',
                                                                        'label' => false,
                                                                        'required' => true,
                                                                        'empty' => '[ Select Region ]'
                                                                    ]) ?>
                                                                </div>
                                                            </td>
                                                            <td class="center">
                                                                <div style="margin-top: 10px;">
                                                                    <?= $this->Form->control("high_school_education_backgrounds.$bk.zone", [
                                                                        'class' => 'otherRequiredText-input',
                                                                        'label' => false,
                                                                        'type' => 'text',
                                                                        'style' => 'width:100%;',
                                                                        'onBlur' => 'checkIsAlpha(this)',
                                                                        'required' => true,
                                                                        'value' => !empty($bv['zone']) ? $bv['zone'] : ($studentDetail->accepted_student->zone_id && !empty($zones[$studentDetail->accepted_student->zone_id]) ? $zones[$studentDetail->accepted_student->zone_id] : ($studentDetail->zone_id && !empty($zones[$studentDetail->zone_id]) ? $zones[$studentDetail->zone_id] : ''))
                                                                    ]) ?>
                                                                </div>
                                                            </td>
                                                            <td class="center">
                                                                <div style="margin-top: 10px;">
                                                                    <?= $this->Form->control("high_school_education_backgrounds.$bk.town", [
                                                                        'class' => 'otherRequiredText-input',
                                                                        'label' => false,
                                                                        'style' => 'width:100%;',
                                                                        'onBlur' => 'checkIsAlpha(this)',
                                                                        'required' => true
                                                                    ]) ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                        $count++;
                                                    }
                                                } else {
                                                    ?>
                                                    <tr>
                                                        <td class="center">1</td>
                                                        <td class="center">
                                                            <div style="margin-top: 10px;">
                                                                <?= $this->Form->control('high_school_education_backgrounds.0.school_level', [
                                                                    'class' => 'otherRequiredText-input',
                                                                    'label' => false,
                                                                    'style' => 'width:100%;',
                                                                    'placeholder' => __('preparatory, highschool etc..'),
                                                                    'onBlur' => 'checkIsAlpha(this)',
                                                                    'required' => true,
                                                                    'value' => !empty($this->request->getData('high_school_education_backgrounds.0.school_level')) ? $this->request->getData('high_school_education_backgrounds.0.school_level') : ($studentDetail->accepted_student->high_school ? 'Preparatory' : '')
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                        <td class="center">
                                                            <div style="margin-top: 10px;">
                                                                <?= $this->Form->control('high_school_education_backgrounds.0.name', [
                                                                    'class' => 'otherRequiredText-input',
                                                                    'label' => false,
                                                                    'style' => 'width:100%;',
                                                                    'onBlur' => 'checkIsAlpha(this)',
                                                                    'required' => true,
                                                                    'id' => 'high_school_education_backgrounds_0_name',
                                                                    'value' => !empty($this->request->getData('high_school_education_backgrounds.0.name')) ? $this->request->getData('high_school_education_backgrounds.0.name') : ($studentDetail->accepted_student->high_school ? ucwords(strtolower(trim($studentDetail->accepted_student->high_school))) : '')
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                        <td class="center">
                                                            <div style="margin-top: 10px;">
                                                                <?= $this->Form->control('high_school_education_backgrounds.0.national_exam_taken', [
                                                                    'label' => false,
                                                                    'style' => 'width:100%;',
                                                                    'checked' => !empty($studentDetail->accepted_student->high_school)
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                        <td class="center">
                                                            <div style="margin-top: 10px;">
                                                                <?= $this->Form->control('high_school_education_backgrounds.0.region_id', [
                                                                    'options' => $regionsAll,
                                                                    'style' => 'width:100%;',
                                                                    'type' => 'select',
                                                                    'label' => false,
                                                                    'required' => true,
                                                                    'empty' => '[ Select Region ]',
                                                                    'default' => !empty($this->request->getData('high_school_education_backgrounds.0.region_id')) ? $this->request->getData('high_school_education_backgrounds.0.region_id') : ($studentDetail->accepted_student->region_id ?? ($studentDetail->region_id ?? ''))
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                        <td class="center">
                                                            <div style="margin-top: 10px;">
                                                                <?= $this->Form->control('high_school_education_backgrounds.0.zone', [
                                                                    'class' => 'otherRequiredText-input',
                                                                    'label' => false,
                                                                    'type' => 'text',
                                                                    'style' => 'width:100%;',
                                                                    'onBlur' => 'checkIsAlpha(this)',
                                                                    'required' => true,
                                                                    'value' => !empty($this->request->getData('high_school_education_backgrounds.0.zone')) ? $this->request->getData('high_school_education_backgrounds.0.zone') : ($studentDetail->accepted_student->zone_id && !empty($zones[$studentDetail->accepted_student->zone_id]) ? $zones[$studentDetail->accepted_student->zone_id] : ($studentDetail->zone_id && !empty($zones[$studentDetail->zone_id]) ? $zones[$studentDetail->zone_id] : ''))
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                        <td class="center">
                                                            <div style="margin-top: 10px;">
                                                                <?= $this->Form->control('high_school_education_backgrounds.0.town', [
                                                                    'class' => 'otherRequiredText-input',
                                                                    'label' => false,
                                                                    'style' => 'width:100%;',
                                                                    'onBlur' => 'checkIsAlpha(this)',
                                                                    'required' => true
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    echo $this->Form->hidden('high_school_education_backgrounds.0.student_id', ['value' => $studentDetail->id]);
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <table cellpadding="0" cellspacing="0" class="table">
                                                <tr>
                                                    <td colspan="7">
                                                        <div style="padding-top: 10px; padding-bottom: 10px;">
                                                            <button type="button" class="button" onclick="addRow('high_school_education', 'high_school_education_backgrounds', 6, '<?= $highSchoolAllFields ?>', '')"><?= __('Add Additional School') ?></button>
                                                            &nbsp; &nbsp; &nbsp;
                                                            <button type="button" class="button alert" onclick="deleteRow('high_school_education')"><?= __('Delete Last School') ?></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <br>
                                    </div>
                                </div>
                                <?php
                            }
                            if (($role_id == ROLE_STUDENT && ($studentDetail->program_id == PROGRAM_POST_GRADUATE
                                        || $studentDetail->program_id == PROGRAM_PhD)) ||
                                !empty($this->request->getData('higher_education_backgrounds'))) {
                                ?>
                                <hr style="margin-top: -10px;">
                                <blockquote>
                                    <h6><i class="fa fa-info"></i> &nbsp; <?= __('Important Note:') ?></h6>
                                    <span style="text-align:justify;" class="fs15 text-black">
                <?= __('Information you provide on this page should be properly formatted and error-free as <b><i class="rejected">it affects official transcript or student copy address contents</i></b>. <br> If you want to add more than one record for the required information, you can use the \'Add Additional Row\' button and make sure that the information you are entering is chronologically ordered from the most recent to the oldest for higher education you attended.') ?>
            </span>
                                </blockquote>
                                <hr>
                                <?php
                                $higherFields = [
                                    'name' => '1',
                                    'field_of_study' => '2',
                                    'diploma_awarded' => '3',
                                    'date_graduated' => '4',
                                    'cgpa_at_graduation' => '5',
                                    'city' => '6'
                                ];
                                $higherAllFields = implode(',', array_keys($higherFields));
                                ?>
                                <div class="row">
                                    <div class="large-12 columns">
                                        <div style="overflow-x:auto;">
                                            <table cellpadding="0" cellspacing="0" class="table">
                                                <thead>
                                                <tr>
                                                    <td colspan="7" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                        <h6 class="fs18 text-black"><?= __('Higher Education Attended') ?></h6>
                                                    </td>
                                                </tr>
                                                </thead>
                                            </table>
                                            <table id="higher_education_background" cellpadding="0" cellspacing="0" class="table">
                                                <thead>
                                                <tr>
                                                    <th style="width: 3%;" class="center">#</th>
                                                    <th style="width: 18%;" class="center"><?= __('Institution/College') ?></th>
                                                    <th style="width: 15%;" class="center"><?= __('Field of Study') ?></th>
                                                    <th style="width: 15%;" class="center"><?= __('Diploma Awarded') ?></th>
                                                    <th style="width: 26%;" class="center"><?= __('Date Graduated (G.C)') ?></th>
                                                    <th style="width: 8%;" class="center"><?= __('CGPA') ?></th>
                                                    <th style="width: 15%;" class="center"><?= __('City') ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $higherEdData = $this->request->getData('higher_education_backgrounds') ?: ($studentDetail->higher_education_backgrounds ?? []);
                                                if (!empty($higherEdData)) {
                                                    $count = 1;
                                                    foreach ($higherEdData as $bk => $bv) {
                                                        echo $this->Form->hidden("higher_education_backgrounds.$bk.id");
                                                        echo $this->Form->hidden("higher_education_backgrounds.$bk.student_id", ['value' => $studentDetail->id]);
                                                        ?>
                                                        <tr>
                                                            <td class="center"><?= $count ?></td>
                                                            <td class="center">
                                                                <div style="margin-top: 10px;">
                                                                    <?= $this->Form->control("higher_education_backgrounds.$bk.name", [
                                                                        'class' => 'otherRequiredText-input',
                                                                        'required' => true,
                                                                        'onBlur' => 'checkIsAlpha(this)',
                                                                        'label' => false,
                                                                        'style' => 'width:100%;'
                                                                    ]) ?>
                                                                </div>
                                                            </td>
                                                            <td class="center">
                                                                <div style="margin-top: 10px;">
                                                                    <?= $this->Form->control("higher_education_backgrounds.$bk.field_of_study", [
                                                                        'class' => 'otherRequiredText-input',
                                                                        'required' => true,
                                                                        'onBlur' => 'checkIsAlpha(this)',
                                                                        'label' => false,
                                                                        'style' => 'width:100%;'
                                                                    ]) ?>
                                                                </div>
                                                            </td>
                                                            <td class="center">
                                                                <div style="margin-top: 10px;">
                                                                    <?= $this->Form->control("higher_education_backgrounds.$bk.diploma_awarded", [
                                                                        'class' => 'otherRequiredText-input',
                                                                        'required' => true,
                                                                        'onBlur' => 'checkIsAlpha(this)',
                                                                        'label' => false,
                                                                        'style' => 'width:100%;'
                                                                    ]) ?>
                                                                </div>
                                                            </td>
                                                            <td class="center">
                                                                <div style="margin-top: 10px;">
                                                                    <?= $this->Form->control("higher_education_backgrounds.$bk.date_graduated", [
                                                                        'required' => true,
                                                                        'label' => false,
                                                                        'style' => 'width:30%;',
                                                                        'type' => 'date',
                                                                        'minYear' => !empty($studentAdmissionYear) ? $studentAdmissionYear - 20 : date('Y') - 30,
                                                                        'maxYear' => !empty($studentAdmissionYear) ? $studentAdmissionYear : date('Y')
                                                                    ]) ?>
                                                                </div>
                                                            </td>
                                                            <td class="center">
                                                                <div style="margin-top: 10px;">
                                                                    <?= $this->Form->control("higher_education_backgrounds.$bk.cgpa_at_graduation", [
                                                                        'class' => 'cgpa-input',
                                                                        'required' => true,
                                                                        'label' => false,
                                                                        'placeholder' => __('CGPA'),
                                                                        'type' => 'text',
                                                                        'onBlur' => 'checkCGPA(this)'
                                                                    ]) ?>
                                                                </div>
                                                            </td>
                                                            <td class="center">
                                                                <div style="margin-top: 10px;">
                                                                    <?= $this->Form->control("higher_education_backgrounds.$bk.city", [
                                                                        'class' => 'otherRequiredText-input',
                                                                        'required' => true,
                                                                        'onBlur' => 'checkIsAlpha(this)',
                                                                        'style' => 'width:100%;',
                                                                        'label' => false,
                                                                        'type' => 'text'
                                                                    ]) ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                        $count++;
                                                    }
                                                } else {
                                                    ?>
                                                    <tr>
                                                        <td class="center">1</td>
                                                        <td class="center">
                                                            <div style="margin-top: 10px;">
                                                                <?= $this->Form->control('higher_education_backgrounds.0.name', [
                                                                    'class' => 'otherRequiredText-input',
                                                                    'required' => true,
                                                                    'onBlur' => 'checkIsAlpha(this)',
                                                                    'label' => false,
                                                                    'placeholder' => __('Name of the Institution..')
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                        <td class="center">
                                                            <div style="margin-top: 10px;">
                                                                <?= $this->Form->control('higher_education_backgrounds.0.field_of_study', [
                                                                    'class' => 'otherRequiredText-input',
                                                                    'required' => true,
                                                                    'onBlur' => 'checkIsAlpha(this)',
                                                                    'label' => false,
                                                                    'placeholder' => __('Field of Study..')
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                        <td class="center">
                                                            <div style="margin-top: 10px;">
                                                                <?= $this->Form->control('higher_education_backgrounds.0.diploma_awarded', [
                                                                    'class' => 'otherRequiredText-input',
                                                                    'required' => true,
                                                                    'onBlur' => 'checkIsAlpha(this)',
                                                                    'label' => false,
                                                                    'placeholder' => __('BSc, MSc, BA, MA..')
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                        <td class="center">
                                                            <div style="margin-top: 10px;">
                                                                <?= $this->Form->control('higher_education_backgrounds.0.date_graduated', [
                                                                    'required' => true,
                                                                    'label' => false,
                                                                    'style' => 'width:30%;',
                                                                    'type' => 'date',
                                                                    'minYear' => !empty($studentAdmissionYear) ? $studentAdmissionYear - 20 : date('Y') - 30,
                                                                    'maxYear' => !empty($studentAdmissionYear) ? $studentAdmissionYear : date('Y')
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                        <td class="center">
                                                            <div style="margin-top: 10px;">
                                                                <?= $this->Form->control('higher_education_backgrounds.0.cgpa_at_graduation', [
                                                                    'class' => 'cgpa-input',
                                                                    'required' => true,
                                                                    'label' => false,
                                                                    'placeholder' => __('CGPA'),
                                                                    'type' => 'text',
                                                                    'onBlur' => 'checkCGPA(this)'
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                        <td class="center">
                                                            <div style="margin-top: 10px;">
                                                                <?= $this->Form->control('higher_education_backgrounds.0.city', [
                                                                    'class' => 'otherRequiredText-input',
                                                                    'required' => true,
                                                                    'onBlur' => 'checkIsAlpha(this)',
                                                                    'style' => 'width:100%;',
                                                                    'label' => false,
                                                                    'type' => 'text',
                                                                    'placeholder' => __('City..')
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    echo $this->Form->hidden('higher_education_backgrounds.0.student_id', ['value' => $studentDetail->id]);
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <table cellpadding="0" cellspacing="0" class="table">
                                                <tr>
                                                    <td colspan="7">
                                                        <div style="padding-top: 10px; padding-bottom: 10px;">
                                                            <button type="button" class="button" onclick="addRow('higher_education_background', 'higher_education_backgrounds', 6, '<?= $higherAllFields ?>', '')"><?= __('Add Additional Row') ?></button>
                                                            &nbsp; &nbsp; &nbsp;
                                                            <button type="button" class="button alert" onclick="deleteRow('higher_education_background')"><?= __('Delete Last Row') ?></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <br>
                                    </div>
                                </div>
                                <?php
                            }
                            $from = date('Y') - 30;
                            $to = date('Y') - 1;
                            $yearOptions = [];
                            for ($j = $to; $j >= $from; $j--) {
                                $yearOptions[$j] = $j;
                            }
                            ?>
                            <div class="row">
                                <?php
                                if (($role_id == ROLE_STUDENT && $studentDetail->program_id == PROGRAM_UNDERGRADUATE
                                    && ALLOW_ESLCE_RESULTS_TO_BE_FILLED_FOR_UNDER_GRADUATE_STUDENTS == 1)
                                || !empty($this->request->getData('eslce_results'))) {
                                $eslceFields = ['subject' => '1', 'grade' => '2', 'exam_year' => '3'];
                                $eslceAllFields = implode(',', array_keys($eslceFields));
                                ?>
                                <div class="large-6 columns">
                                    <div style="overflow-x:auto;">
                                        <table cellpadding="0" cellspacing="0" class="table">
                                            <thead>
                                            <tr>
                                                <td colspan="4" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                    <h6 class="fs18 text-black"><?= __('ESLCE Results (10th Grade)') ?></h6>
                                                </td>
                                            </tr>
                                            </thead>
                                        </table>
                                        <table id="eslce_result" cellpadding="0" cellspacing="0" class="table">
                                            <thead>
                                            <tr>
                                                <th style="width: 5%;" class="center">#</th>
                                                <th style="width: 45%;" class="center"><?= __('Subject') ?></th>
                                                <th style="width: 20%;" class="center"><?= __('Grade') ?></th>
                                                <th style="width: 30%;" class="center"><?= __('Exam Year (G.C)') ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $eslceData = $this->request->getData('eslce_results') ?: ($studentDetail->eslce_results ?? []);
                                            if (!empty($eslceData)) {
                                                $count = 0;
                                                foreach ($eslceData as $bk => $bv) {
                                                    echo $this->Form->hidden("eslce_results.$bk.id");
                                                    echo $this->Form->hidden("eslce_results.$bk.student_id", ['value' => $studentDetail->id]);
                                                    ?>
                                                    <tr>
                                                        <td class="center"><?= ++$count ?></td>
                                                        <td class="center">
                                                            <div style="margin-top: 10px;">
                                                                <?= $this->Form->control("eslce_results.$bk.subject", [
                                                                    'required' => true,
                                                                    'class' => 'subject-input',
                                                                    'onBlur' => 'checkIsAlpha(this)',
                                                                    'label' => false,
                                                                    'style' => 'width:100%;'
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                        <td class="center">
                                                            <div style="margin-top: 10px;">
                                                                <?= $this->Form->control("eslce_results.$bk.grade", [
                                                                    'required' => true,
                                                                    'class' => 'otherRequiredText-input',
                                                                    'onBlur' => 'checkIsAlpha(this)',
                                                                    'label' => false,
                                                                    'style' => 'width:100%;'
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                        <td class="center">
                                                            <div style="margin-top: 10px;">
                                                                <?= $this->Form->control("eslce_results.$bk.exam_year", [
                                                                    'required' => true,
                                                                    'label' => false,
                                                                    'style' => 'width:100%;',
                                                                    'type' => 'select',
                                                                    'options' => $yearOptions,
                                                                    'empty' => '[ Select Year ]'
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td class="center">1</td>
                                                    <td class="center">
                                                        <div style="margin-top: 10px;">
                                                            <?= $this->Form->control('eslce_results.0.subject', [
                                                                'required' => true,
                                                                'class' => 'subject-input',
                                                                'onBlur' => 'checkIsAlpha(this)',
                                                                'label' => false,
                                                                'style' => 'width:100%;'
                                                            ]) ?>
                                                        </div>
                                                    </td>
                                                    <td class="center">
                                                        <div style="margin-top: 10px;">
                                                            <?= $this->Form->control('eslce_results.0.grade', [
                                                                'required' => true,
                                                                'class' => 'otherRequiredText-input',
                                                                'onBlur' => 'checkIsAlpha(this)',
                                                                'label' => false,
                                                                'style' => 'width:100%;'
                                                            ]) ?>
                                                        </div>
                                                    </td>
                                                    <td class="center">
                                                        <div style="margin-top: 10px;">
                                                            <?= $this->Form->control('eslce_results.0.exam_year', [
                                                                'required' => true,
                                                                'label' => false,
                                                                'style' => 'width:100%;',
                                                                'type' => 'select',
                                                                'options' => $yearOptions,
                                                                'empty' => '[ Select Year ]'
                                                            ]) ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                                echo $this->Form->hidden('eslce_results.0.student_id', ['value' => $studentDetail->id]);
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                        <table cellpadding="0" cellspacing="0" class="table">
                                            <tr>
                                                <td colspan="4">
                                                    <div style="padding-top: 10px; padding-bottom: 10px;">
                                                        <button type="button" class="button" onclick="addRow('eslce_result', 'eslce_results', 3, '<?= $eslceAllFields ?>', '<?= $from ?>')"><?= __('Add Additional Subject') ?></button>
                                                        &nbsp; &nbsp; &nbsp;
                                                        <button type="button" class="button alert" onclick="deleteRow('eslce_result')"><?= __('Delete Last Subject') ?></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <br>
                                </div>
                            </div>
                        <?php
                        }
                        if (($role_id == ROLE_STUDENT && $studentDetail->program_id == PROGRAM_UNDERGRADUATE)
                            || !empty($this->request->getData('eheece_results.0.subject'))) {
                            $eheeceFields = ['subject' => '1', 'mark' => '2'];
                            $eheeceAllFields = implode(',', array_keys($eheeceFields));
                            ?>
                            <div class="large-6 columns">
                                <div style="overflow-x:auto;">
                                    <table cellpadding="0" cellspacing="0" class="table">
                                        <thead>
                                        <tr>
                                            <td colspan="4" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                <h6 class="fs18 text-black"><?= __('EHEECE Results (12th Grade)') ?></h6>
                                                <hr>
                                                <?= $this->Form->control('eheece_results.0.exam_year', [
                                                    'value' => !empty($this->request->getData('eheece_results.0.exam_year')) ? $this->request->getData('eheece_results.0.exam_year') : '',
                                                    'label' => __('Exam Taken Date: (G.C) &nbsp;'),
                                                    'style' => 'width:25%;',
                                                    'type' => 'date',
                                                    'minYear' => date('Y') - 10,
                                                    'maxYear' => !empty($studentAdmissionYear) ? $studentAdmissionYear : date('Y')
                                                ]) ?>
                                            </td>
                                        </tr>
                                        </thead>
                                    </table>
                                    <table id="eheece_result" cellpadding="0" cellspacing="0" class="table">
                                        <thead>
                                        <tr>
                                            <th style="width: 5%;" class="center">#</th>
                                            <th style="width: 45%;" class="center"><?= __('Subject') ?></th>
                                            <th style="width: 20%;" class="center"><?= __('Mark') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $eheeceData = $this->request->getData('eheece_results') ?: ($studentDetail->eheece_results ?? []);
                                        if (!empty($eheeceData)) {
                                            $count = 0;
                                            foreach ($eheeceData as $bk => $bv) {
                                                echo $this->Form->hidden("eheece_results.$bk.id");
                                                echo $this->Form->hidden("eheece_results.$bk.student_id", ['value' => $studentDetail->id]);
                                                ?>
                                                <tr>
                                                    <td class="center"><?= ++$count ?></td>
                                                    <td class="center">
                                                        <div style="margin-top: 10px;">
                                                            <?= $this->Form->control("eheece_results.$bk.subject", [
                                                                'required' => true,
                                                                'class' => 'subject-input',
                                                                'onBlur' => 'checkIsAlpha(this)',
                                                                'label' => false,
                                                                'style' => 'width:100%;',
                                                                'placeholder' => __('Subject %s', $count)
                                                            ]) ?>
                                                        </div>
                                                    </td>
                                                    <td class="center">
                                                        <div style="margin-top: 10px;">
                                                            <?= $this->Form->control("eheece_results.$bk.mark", [
                                                                'class' => 'subjectMark-input',
                                                                'required' => true,
                                                                'onBlur' => 'checkValidMarkInput(this)',
                                                                'label' => false,
                                                                'style' => 'width:100%;',
                                                                'placeholder' => __('Mark %s', $count),
                                                                'type' => 'number',
                                                                'min' => 0,
                                                                'max' => 100,
                                                                'step' => 'any'
                                                            ]) ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td class="center">1</td>
                                                <td class="center">
                                                    <div style="margin-top: 10px;">
                                                        <?= $this->Form->control('eheece_results.0.subject', [
                                                            'required' => true,
                                                            'class' => 'subject-input',
                                                            'onBlur' => 'checkIsAlpha(this)',
                                                            'label' => false,
                                                            'style' => 'width:100%;',
                                                            'placeholder' => __('Subject 1')
                                                        ]) ?>
                                                    </div>
                                                </td>
                                                <td class="center">
                                                    <div style="margin-top: 10px;">
                                                        <?= $this->Form->control('eheece_results.0.mark', [
                                                            'class' => 'subjectMark-input',
                                                            'required' => true,
                                                            'onBlur' => 'checkValidMarkInput(this)',
                                                            'label' => false,
                                                            'style' => 'width:100%;',
                                                            'placeholder' => __('Mark 1'),
                                                            'type' => 'number',
                                                            'min' => 0,
                                                            'max' => 100,
                                                            'step' => 'any'
                                                        ]) ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="center">2</td>
                                                <td class="center">
                                                    <div style="margin-top: 10px;">
                                                        <?= $this->Form->control('eheece_results.1.subject', [
                                                            'required' => true,
                                                            'class' => 'subject-input',
                                                            'onBlur' => 'checkIsAlpha(this)',
                                                            'label' => false,
                                                            'style' => 'width:100%;',
                                                            'placeholder' => __('Subject 2')
                                                        ]) ?>
                                                    </div>
                                                </td>
                                                <td class="center">
                                                    <div style="margin-top: 10px;">
                                                        <?= $this->Form->control('eheece_results.1.mark', [
                                                            'class' => 'subjectMark-input',
                                                            'required' => true,
                                                            'onBlur' => 'checkValidMarkInput(this)',
                                                            'label' => false,
                                                            'style' => 'width:100%;',
                                                            'placeholder' => __('Mark 2'),
                                                            'type' => 'number',
                                                            'min' => 0,
                                                            'max' => 100,
                                                            'step' => 'any'
                                                        ]) ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            echo $this->Form->hidden('eheece_results.0.student_id', ['value' => $studentDetail->id]);
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                    <table cellpadding="0" cellspacing="0" class="table">
                                        <tr>
                                            <td colspan="4">
                                                <div style="padding-top: 10px; padding-bottom: 10px;">
                                                    <button type="button" class="button" onclick="addRow('eheece_result', 'eheece_results', 2, '<?= $eheeceAllFields ?>', '<?= $from ?>')"><?= __('Add Additional Subject') ?></button>
                                                    &nbsp; &nbsp; &nbsp;
                                                    <button type="button" class="button alert" onclick="deleteRow('eheece_result')"><?= __('Delete Last Subject') ?></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                            </div>
                            <?php
                        }
                        ?>
                        </div>
                    </div>
                    <hr>
                    <h6 class="fs13 warning-box" style="font-weight: normal;">
                        <?= __('Inputs/fields marked <b class="rejected">*</b> are required and you have to select or provide the required information, not marked fields are optional. Please check all tabs before updating your profile.') ?>
                    </h6>
                    <h6 class="fs13 info-box" style="font-weight: normal;">
                        <?= __('By submitting this form, you certify that all the information provided in this form is accurate and truthful to the best of your knowledge or supporting documents. Any false, misleading, or inaccurate information may be subject to further actions as permitted by the university\'s legislation or applicable law.') ?>
                    </h6>
                    <hr>
                    <?= $this->Form->button(__('Update Student Detail'), [
                        'name' => 'updateStudentDetail',
                        'id' => 'updateStudentDetail',
                        'value'=>'updateStudentDetail',
                        'class' => 'tiny radius button bg-blue'
                    ]) ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<script type="text/javascript">
    var region = Array();
    var months = Array();
    var minGraduationYear = <?= (isset($student_admission_year) && !empty($student_admission_year) ? ($student_admission_year - 20) : date('Y') - 30); ?>;
    var maxGraduationYear = <?= (isset($student_admission_year) && !empty($student_admission_year) ?  $student_admission_year : (date('Y'))); ?>;

    <?php
    for ($i = 1; $i <= 12; $i++) { ?>
    months[<?= $i - 1; ?>] = new Array();
    months[<?= $i - 1; ?>][0] = "<?= date('m', mktime(0, 0, 0, $i, 1, 2011)); ?>";
    months[<?= $i - 1; ?>][1] = "<?= date('F', mktime(0, 0, 0, $i, 1, 2011)); ?>";
    <?php
    }

    if (!empty($regionsAll)) {
    foreach ($regionsAll as $region_id => $region_name) { ?>
    region["<?= $region_id; ?>"] = "<?= $region_name; ?>";
    <?php
    }
    } ?>

    function addRow(tableID, model, no_of_fields, all_fields, other) {

        var elementArray = all_fields.split(',');
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        var row = table.insertRow(rowCount);
        var cell0 = row.insertCell(0);
        cell0.classList.add("center");

        cell0.innerHTML = rowCount;

        for (var i = 1; i <= no_of_fields; i++) {

            var cell = row.insertCell(i);
            var div = document.createElement("div");
            div.style.marginTop = "10px";

            if (elementArray[i - 1] == "region_id") {
                var element = document.createElement("select");
                var string = '<option value="">[ Select Region ]</option>';

                for (var f = 1; f < region.length; f++) {
                    if (!(typeof region[f] === 'undefined')) {
                        string += '<option value="' + f + '">' + region[f] + '</option>';
                    }
                }

                element.style = "width:100%;";
                element.required = "required";
                element.innerHTML = string;

            } else if (elementArray[i - 1] == "exam_year") {
                var element = document.createElement("select");
                var d = new Date();
                var full_year = d.getFullYear();
                var string = '<option value="">[ Select Year ]</option>';

                var selectElement = document.getElementById('EslceResult0ExamYear');

                // Get the selected index
                var selectedIndex = selectElement.selectedIndex;
                var selectedValue = selectElement.options[selectedIndex].value;
                for (var j = full_year - 1; j > other; j--) {
                    if (selectedValue != '' && selectedValue == j) {
                        string += '<option value="' + j + '" selected="selected">' + j + '</option>';
                    } else {
                        string += '<option value="' + j + '">' + j + '</option>';
                    }
                }

                element.innerHTML = string;
                //element.style = "width:70%;";
                element.style = "width:100%;";
                element.required = "required";

            } else if (elementArray[i - 1] == 'grade') {
                var element = document.createElement("input");
                element.type = "text";
                element.style = "width:100%;";
                element.placeholder = "A";
                element.required = "required";

                element.classList.add("otherRequiredText-input");

                element.onblur = function() {
                    checkIsAlpha(this);
                };

            } else if (elementArray[i - 1] == 'mark') {
                var element = document.createElement("input");
                element.type = "number";
                element.max = "100";
                element.min = "0";
                element.step = "any";
                element.style = "width:100%;";
                element.placeholder = "Mark " + rowCount;
                element.required = "required";

                element.classList.add("subjectMark-input");

                element.onblur = function() {
                    checkValidMarkInput(this);
                };

            } else if (elementArray[i - 1] == 'national_exam_taken') {
                var element = document.createElement("input");
                element.type = "checkbox";
                element.style = "width:100%;";
            } else if (elementArray[i - 1] == 'cgpa_at_graduation') {
                var element = document.createElement("input");
                element.type = "text";
                element.classList.add("cgpa-input");
                element.required = "required";

                element.onblur = function() {
                    checkCGPA(this);
                };

            } else if (elementArray[i - 1] == 'date_graduated') {


                var divDateGraduated = document.createElement("div");
                var textNode = document.createTextNode("-");
                var textNode1 = document.createTextNode("-");

                var currentYear = new Date().getFullYear();
                currentYear = currentYear - 1;

                var currentMonth = ("0" + (new Date().getMonth() + 1)).slice(-2); // Months are 0-based
                var currentDay = ("0" + new Date().getDate()).slice(-2);

                var monthSelect = document.createElement("select");
                monthSelect.name = "higher_education_backgrounds[" + rowCount + "][date_graduated][month]";
                monthSelect.style = "width:30%;";
                monthSelect.required = "required";

                var monthOptions = [
                    { value: "01", text: "January" },
                    { value: "02", text: "February" },
                    { value: "03", text: "March" },
                    { value: "04", text: "April" },
                    { value: "05", text: "May" },
                    { value: "06", text: "June" },
                    { value: "07", text: "July" },
                    { value: "08", text: "August" },
                    { value: "09", text: "September" },
                    { value: "10", text: "October" },
                    { value: "11", text: "November" },
                    { value: "12", text: "December" }
                ];

                monthOptions.forEach(function(option) {
                    var opt = document.createElement("option");
                    opt.value = option.value;
                    opt.textContent = option.text;
                    if (option.value === currentMonth) {
                        opt.selected = true;
                    }
                    monthSelect.appendChild(opt);
                });

                var daySelect = document.createElement("select");
                daySelect.name = "higher_education_backgrounds[" + rowCount + "][date_graduated][day]";
                daySelect.style = "width:30%;";
                daySelect.required = "required";

                for (var day = 1; day <= 31; day++) {
                    var opt = document.createElement("option");
                    var dayValue = ("0" + day).slice(-2);
                    opt.value = dayValue;
                    opt.textContent = day;
                    if (dayValue === currentDay) {
                        opt.selected = true;
                    }
                    daySelect.appendChild(opt);
                }

                var yearSelect = document.createElement("select");
                yearSelect.name = "higher_education_backgrounds[" + rowCount + "][date_graduated][year]";
                yearSelect.style = "width:30%;";
                yearSelect.required = "required";

                if (maxGraduationYear != '' && minGraduationYear != '') {
                    for (var year = maxGraduationYear; year >= minGraduationYear; year--) {
                        var opt = document.createElement("option");
                        opt.value = year;
                        opt.textContent = year;
                        if (year === currentYear) {
                            opt.selected = true;
                        }
                        yearSelect.appendChild(opt);
                    }
                } else {
                    for (var year = currentYear; year >= currentYear - 30; year--) {
                        var opt = document.createElement("option");
                        opt.value = year;
                        opt.textContent = year;
                        if (year === currentYear) {
                            opt.selected = true;
                        }
                        yearSelect.appendChild(opt);
                    }
                }

                divDateGraduated.appendChild(monthSelect);
                divDateGraduated.appendChild(textNode);
                divDateGraduated.appendChild(daySelect);
                divDateGraduated.appendChild(textNode1);
                divDateGraduated.appendChild(yearSelect);

            } else if (elementArray[i - 1] == 'subject') {
                var element = document.createElement("input");
                element.type = "text";
                element.style = "width:100%;";
                element.placeholder = "Subject " + rowCount;
                element.required = "required";
                element.classList.add("subject-input");
                element.onblur = function() {
                    checkIsAlpha(this);
                };

            } else {
                var element = document.createElement("input");
                element.type = "text";
                //element.size = "13";
                element.style = "width:100%;";
                element.required = "required";

                element.classList.add("otherRequiredText-input");

                element.onblur = function() {
                    checkIsAlpha(this);
                };
            }


            if (elementArray[i - 1] != 'date_graduated') {
                element.name = model + "[" + rowCount + "][" + elementArray[i - 1] + "]";
                div.appendChild(element);
            } else if (elementArray[i - 1] == 'date_graduated') {
                div.appendChild(divDateGraduated);
            }

            cell.appendChild(div);

            cell.classList.add("center");
        }

        updateSequence(tableID);

    }

    function deleteRow(tableID) {
        try {
            var table = document.getElementById(tableID);
            var rowCount = table.rows.length;
            if (rowCount > 2) {
                table.deleteRow(rowCount - 1);
                updateSequence(tableID);
            } else {
                alert('No more rows to delete');
            }
        } catch (e) {
            alert(e);
        }
    }

    function updateSequence(tableID) {
        var s_count = 1;
        for (i = 1; i < document.getElementById(tableID).rows.length; i++) {
            document.getElementById(tableID).rows[i].cells[0].childNodes[0].data = s_count++;
        }
    }

    function updateRegionCity(id) {
        //serialize form data
        var formData = $("#country_id_" + id).val();

        $("#region_id_" + id).empty();
        $("#region_id_" + id).attr('disabled', true);
        $("#city_id_" + id).attr('disabled', true);

        //get form action
        var formUrl = '/students/getRegions/' + formData;

        $.ajax({
            type: 'get',
            url: formUrl,
            data: formData,
            success: function(data, textStatus, xhr) {
                $("#region_id_" + id).attr('disabled', false);
                $("#region_id_" + id).empty();
                $("#region_id_" + id).append(data);
                var subCat = $("#region_id_" + id).val();
                $("#city_id_" + id).empty();
                var formUrl = '/students/getCities/' + subCat;
                $.ajax({
                    type: 'get',
                    url: formUrl,
                    data: subCat,
                    success: function(data, textStatus, xhr) {
                        $("#city_id_" + id).attr('disabled', false);
                        $("#city_id_" + id).empty();
                        $("#city_id_" + id).append(data);
                    },
                    error: function(xhr, textStatus, error) {
                        alert(textStatus);
                    }
                });
            },
            error: function(xhr, textStatus, error) {
                alert(textStatus);
            }
        });

        return false;
    }

    //Update city given region
    function updateCity(id) {
        //serialize form data
        var subCat = $("#region_id_" + id).val();
        $("#city_id_" + id).attr('disabled', true);
        $("#city_id_" + id).empty();

        //get form action
        var formUrl = '/students/getCities/' + subCat;

        $.ajax({
            type: 'get',
            url: formUrl,
            data: subCat,
            success: function(data, textStatus, xhr) {
                $("#city_id_" + id).attr('disabled', false);
                $("#city_id_" + id).empty();
                $("#city_id_" + id).append(data);
            },
            error: function(xhr, textStatus, error) {
                alert(textStatus);
            }
        });

        return false;
    }
</script>


<script type="text/javascript">

    function toggleSubmitButtonActive() {
        if ($("#email").val != 0 && $("#email").val != '') {
            $("#SubmitID").attr('disabled', false);
        }
    }

    function isValidPhonenumber(value) {
        return (/^\d{7,}$/).test(value.replace(/[\s()+\-\.]|ext/gi, ''));
    }

    function isValidEmail(value) {
        return (/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/).test(value.trim());
    }

    function isAlpha(value) {
        return (/^[a-zA-Z]+$/).test(value.trim());
    }

    var faidaMandatory = <?= json_encode($faidaMandatory); ?>;

    function checkFaidaFin(obj) {

        let message = document.getElementById("customMessageFaidaFin");

        obj.value = obj.value.replace(/\s+/g, '').replace(/[^0-9]/g, '');

        if ((obj.value !== '' && !isNaN(obj.value) && obj.value.length !== 12) || (faidaMandatory && obj.value == '')) {
            obj.style.border = '2px solid red';

            if (!message) {
                message = document.createElement("div");
                message.id = "customMessageFaidaFin";
                document.body.appendChild(message);
            }

            message.innerText = 'Please check the backside of your Fayda ID for a valid FIN, which 12 digits long.';
            message.style.position = 'absolute';
            message.style.backgroundColor = '#f8d7da';
            message.style.color = '#721c24';
            message.style.border = '1px solid #f5c6cb';
            message.style.padding = '5px';
            message.style.zIndex = '1000';

            const rect = obj.getBoundingClientRect();
            message.style.top = `${rect.top + window.scrollY + obj.offsetHeight + 5}px`;
            message.style.left = `${rect.left + window.scrollX}px`;

            obj.focus();

            setTimeout(() => {
                message.remove();
            }, 6000);

            return false;
        } else {
            obj.style.border = '2px solid #ccc';

            if (obj.value.length === 12) {
                // Format the input for redisplay as 1234-5674-8901
                obj.value = obj.value.replace(/(\d{4})(\d{4})(\d{4})/, '$1-$2-$3');
            }

            if (message) {
                message.remove();
            }

            return true;
        }
    }

    function checkFaidaFan(obj) {

        let message = document.getElementById("customMessageFaidaFan");

        obj.value = obj.value.replace(/\s+/g, '').replace(/[^0-9]/g, '');

        if ((obj.value !== '' && !isNaN(obj.value) && obj.value.length !== 16) || (faidaMandatory && obj.value == '')) {
            obj.style.border = '2px solid red';

            if (!message) {
                message = document.createElement("div");
                message.id = "customMessageFaidaFan";
                document.body.appendChild(message);
            }

            message.innerText = 'Please check the front side of your Fayda ID for a valid FAN, which 16 digits long.';
            message.style.position = 'absolute';
            message.style.backgroundColor = '#f8d7da';
            message.style.color = '#721c24';
            message.style.border = '1px solid #f5c6cb';
            message.style.padding = '5px';
            message.style.zIndex = '1000';

            const rect = obj.getBoundingClientRect();
            message.style.top = `${rect.top + window.scrollY + obj.offsetHeight + 5}px`;
            message.style.left = `${rect.left + window.scrollX}px`;

            obj.focus();

            // Remove the message after a few seconds
            setTimeout(() => {
                message.remove();
            }, 6000);

            return false;
        } else {
            obj.style.border = '2px solid #ccc';

            if (obj.value.length === 16) {
                // Format the input for redisplay as 1234-5674-8901-37754
                obj.value = obj.value.replace(/(\d{4})(\d{4})(\d{4})(\d{4})/, '$1-$2-$3-$4');
            }

            if (message) {
                message.remove();
            }

            return true;
        }
    }

    function checkCGPA(obj) {

        let message = document.getElementById("customMessageCGPA");

        if (isNaN(obj.value) || obj.value == '' || obj.value < 2.00 || obj.value > 4.00) {
            obj.style.border = '2px solid red';

            if (!message) {
                message = document.createElement("div");
                message.id = "customMessageCGPA";
                document.body.appendChild(message);
            }

            message.innerText = 'Please enter a valid CGPA between 2.00 and 4.00';
            message.style.position = 'absolute';
            message.style.backgroundColor = '#f8d7da';
            message.style.color = '#721c24';
            message.style.border = '1px solid #f5c6cb';
            message.style.padding = '5px';
            message.style.zIndex = '1000';

            const rect = obj.getBoundingClientRect();
            message.style.top = `${rect.top + window.scrollY + obj.offsetHeight + 5}px`;
            message.style.left = `${rect.left + window.scrollX}px`;

            obj.focus();

            // Remove the message after a few seconds
            setTimeout(() => {
                message.remove();
            }, 3000);

            return false;
        } else {
            obj.style.border = '2px solid #ccc';

            if (message) {
                message.remove();
            }

            return true;
        }
    }

    function checkValidMarkInput(obj) {

        let message = document.getElementById("customMessageMark");

        if (isNaN(obj.value) || obj.value == '' || obj.value < 1 || obj.value > 100) {
            obj.style.border = '2px solid red';

            if (!message) {
                message = document.createElement("div");
                message.id = "customMessageMark";
                document.body.appendChild(message);
            }

            message.innerText = 'Please enter a valid Mark between 1 and 100';
            message.style.position = 'absolute';
            message.style.backgroundColor = '#f8d7da';
            message.style.color = '#721c24';
            message.style.border = '1px solid #f5c6cb';
            message.style.padding = '5px';
            message.style.zIndex = '1000';

            const rect = obj.getBoundingClientRect();
            message.style.top = `${rect.top + window.scrollY + obj.offsetHeight + 5}px`;
            message.style.left = `${rect.left + window.scrollX}px`;

            obj.focus();

            // Remove the message after a few seconds
            setTimeout(() => {
                message.remove();
            }, 3000);

            return false;
        } else {
            obj.style.border = '2px solid #ccc';

            if (message) {
                message.remove();
            }

            return true;
        }
    }

    function capitalizeFirstLetterOfEachWord(str) {
        return str.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ');
    }

    function capitalizeWordsExcludePrepositions(str) {

        const prepositions = [
            'and', 'or', 'of', 'in', 'on', 'at', 'with', 'from', 'by', 'about', 'as', 'into', 'like', 'through', 'after', 'over', 'between', 'out', 'against', 'during', 'without', 'before', 'under', 'around', 'among',
            'an', 'a', 'the', 'this', 'that', 'these', 'those', 'but', 'nor', 'for', 'so', 'yet', 'is', 'was', 'be', 'been', 'being', 'am', 'are', 'were',
        ];

        // Replace multiple spaces with a single space
        str = str.replace(/\s+/g, ' ');

        return str.split(' ').map(word => {
            if (prepositions.includes(word)) {
                return word.toLowerCase();
            } else {
                // Check if the word is a Roman numeral
                if (/^[IVXLCDM]+$/.test(word)) {
                    return word;
                }
                return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
            }
        }).join(' ');

    }

    function checkIsAlpha(obj) {
        const pattern = /^[a-zA-Z\s]+$/; // support space, string allowed
        let message = document.getElementById("customMessage");
        obj.value = capitalizeWordsExcludePrepositions(obj.value.trim());

        if (!pattern.test(obj.value)) {
            obj.style.border = '2px solid red';

            if (!message) {
                message = document.createElement("div");
                message.id = "customMessage";
                document.body.appendChild(message);
            }

            message.innerText = 'Please use only alphabets, avoid adding special charachters like / ( ) & etc';
            message.style.position = 'absolute';
            message.style.backgroundColor = '#f8d7da';
            message.style.color = '#721c24';
            message.style.border = '1px solid #f5c6cb';
            message.style.padding = '5px';
            message.style.zIndex = '1000';

            const rect = obj.getBoundingClientRect();
            message.style.top = `${rect.top + window.scrollY + obj.offsetHeight + 5}px`;
            message.style.left = `${rect.left + window.scrollX}px`;
            obj.focus();
            setTimeout(() => {
                message.remove();
            }, 3000);

            return false;
        } else {
            obj.style.border = '2px solid #ccc';

            if (message) {
                message.remove();
            }

            return true;
        }
    }

    var form_being_submitted = false;
    var ethiopianStudent = <?= json_encode($ethiopianStudent); ?>;
    var ugProgram = <?= json_encode($ugProgram); ?>;

    $('#updateStudentDetail').click(function(event) {

        var isValid = true;
        var faidaFinValue = '';
        var faidaFanValue = '';

        if (ethiopianStudent) {
            if ($('#AmharicText').val() == '') {
                alert('Please provide amharic first name.');
                $('#AmharicText').focus();
                return false;
            }

            if ($('#AmharicTextMiddleName').val() == '') {
                alert('Please provide amharic middle name.');
                $('#AmharicTextMiddleName').focus();
                return false;
            }

            if ($('#AmharicTextLastName').val() == '') {
                alert('Please provide amharic last name.');
                $('#AmharicTextLastName').focus();
                return false;
            }

            if (faidaMandatory && $('#faidaFan').val() == '') {
                alert('Please enter your 16-digit Fayda Alias Number (FAN), located on the front of your Fayda ID.');
                $('#faidaFan').focus();
                return false;
            }

            if (faidaMandatory && $('#faidaFin').val() == '') {
                alert('Please enter your 12-digit Fayda Identification Number (FIN), located on the back of your Fayda ID.');
                $('#faidaFin').focus();
                return false;
            }

            if ($('#faidaFan').val() != '') {

                faidaFanValue = $('#faidaFan').val();

                if ($('#faidaFan').attr('readonly') === 'readonly') {
                    faidaFanValue = '';
                }

                var fanLength = $('#faidaFan').val().replace(/\s+/g, '').replace(/[^0-9]/g, '');
                //alert(finLength.length);
                if (fanLength.length !== 16) {
                    alert('Please check the FRONT SIDE of your Fayda ID for a valid FAN, which 16 digits long.');
                    $('#faidaFan').focus();
                    return false;
                }
            }

            if ($('#faidaFin').val() != '') {

                faidaFinValue = $('#faidaFin').val();

                if ($('#faidaFin').attr('readonly') === 'readonly') {
                    faidaFinValue = '';
                }

                var finLength = $('#faidaFin').val().replace(/\s+/g, '').replace(/[^0-9]/g, '');
                //alert(finLength.length);
                if (finLength.length !== 12) {
                    alert('Please check the BACK SIDE of your Fayda ID for a valid FIN, which 12 digits long.');
                    $('#faidaFin').focus();
                    return false;
                }
            }
        }

        if ($('#email').val() == '') {
            alert('Please provide your primary personal email address.');
            $('#email').focus();
            return false;
        } else if ($('#email').val() != '' && !isValidEmail($('#email').val())) {
            alert('Please provide valid email address. Invalif email address.');
            $('#email').focus();
            return false;
        }

        if ($('#etPhone').val() == '') {
            alert('Please provide mobile phone number without a leading 0.');
            $('#etPhone').focus();
            return false;
        } else if ($('#etPhone').val() != '' && $('#etPhone').val().length != 13) {
            alert('Mobile phone number format is invalid. Please check mobile number length is 13 including +251.');
            $('#etPhone').focus();
            return false;
        }

        if ($('#zone_id_2').val() == '') {
            alert('Please select Zone from Address & Primary Contact tab.');
            $('#zone_id_2').focus();
            return false;
        }

        if ($('#woreda_id_2').val() == '') {
            alert('Please select woreda from Address & Primary Contact tab.');
            $('#woreda_id_2').focus();
            return false;
        }
        document.querySelectorAll('#StudentProfileForm input[required]').forEach(function(input) {
            if (!input.value && input.getAttribute("type") === "select") {
                isValid = false;
                input.focus();
                return false;
            }
        });


        document.querySelectorAll('#StudentProfileForm input[required]').forEach(function(input) {
            if (!input.value) {
                isValid = false;
                //input.style.border = '2px solid red';
                if (input.getAttribute("type") === "select") {
                    input.focus();
                    return false;
                } else if (input.getAttribute("type") !== "email" && input.getAttribute("type") !== "tel") {
                    input.style.border = '2px solid red';
                    if (!input.hasAttribute('highlighted')) {
                        input.setAttribute('highlighted', 'true');
                        input.focus();
                        return false; // Stop further iterations to focus on the first empty input
                    }
                }
            } else {
                input.style.border = ''; // Remove red border if the input is filled
                input.removeAttribute('highlighted');
            }
        });

        if ($('#region_id_1').val() == '') {
            alert('Please select your primary emergency contatct person Region from Address & Primary Contact tab.');
            $('#region_id_1').focus();
            return false;
        }

        if ($('#zone_id_1').val() == '') {
            alert('Please select your primary emergency contatct person Zone from Address & Primary Contact tab.');
            $('#zone_id_1').focus();
            return false;
        }

        if ($('#woreda_id_1').val() == '') {
            alert('Please select your primary emergency contatct person Woreda from Address & Primary Contact tab.');
            $('#woreda_id_1').focus();
            return false;
        }


        if ($('#phonemobile').val() == '') {
            alert('Please provide your primary emergency contact mobile number in Address & Primary Contact tab.');
            $('#phonemobile').focus();
            return false;
        } else if ($('#phonemobile').val() != '' && $('#phonemobile').val().length != 13) {
            alert('Please provide your a valid primary emergency contact mobile number in Address & Primary Contact tab.');
            $('#phonemobile').focus();
            return false;
        }

        if (!isValidPhonenumber($('#phonemobile').val())) {
            alert('Please provide your a valid primary emergency contact mobile number in Address & Primary Contact tab.');
            $('#phonemobile').focus();
            return false;
        }

        document.querySelectorAll('.otherRequiredText-input').forEach(function(inputField) {
            if (!checkIsAlpha(inputField)) {
                inputField.focus();
                isValid = false;
                return false;
            }
        });

        if (!ugProgram) {
            document.querySelectorAll('.cgpa-input').forEach(function(inputField) {
                if (!checkCGPA(inputField)) {
                    inputField.focus();
                    isValid = false;
                    return false;
                }
            });
        }

        document.querySelectorAll('.subject-input').forEach(function(inputField) {
            if (!checkIsAlpha(inputField)) {
                inputField.focus();
                isValid = false;
                return false;
            }
        });

        document.querySelectorAll('.subjectMark-input').forEach(function(inputField) {
            if (!checkValidMarkInput(inputField)) {
                inputField.focus();
                isValid = false;
                return false;
            }
        });


        if (!isValid) {
            alert("Please fill out all required fields in all tabs including Educational Background tab and ensure that the required fieds are not empty or selected.");
            return false;
        }

        if (ugProgram && $('#HighSchoolEducationBackground0Name').val().length) {
            const highSchoolNameLength = $('#HighSchoolEducationBackground0Name').val().length;
            const minLength = 5;
            const maxLength = 30;

            if (highSchoolNameLength < minLength || highSchoolNameLength > maxLength) {
                alert(`High School Name Length must be between ${minLength} and ${maxLength} characters long. please make an appropraite adjustment by shortening shool name.`);
                $('#HighSchoolEducationBackground0Name').focus();
                return false;
            }
        }

        if (form_being_submitted) {
            alert("Updating Student Profile, please wait a moment or refresh your browser.");
            $('#updateStudentDetail').attr('disabled', true);
            return false;
        }

        var confirmm = true;

        if (faidaFinValue != '' && faidaFanValue != '') {
            confirmm = confirm('You have provided FAN: ' + faidaFanValue +  ' and FIN: ' + faidaFinValue +  '  for your Fayda ID. Please confirm that these numbers are correct, as this is your final opportunity to make any corrections before they are permanently updated to your profile. Are you sure you want to proceed?');
        } else if (faidaFinValue != '') {
            confirmm = confirm('You have provided FIN: ' + faidaFinValue +  ' as your Fayda FIN number. Please confirm that the provided Fayda Identification Number (FIN) is correct, as this is your final opportunity to make any corrections before it is permanently updated to your profile. Are you sure you want to proceed?');
        } else if (faidaFanValue != '') {
            confirmm = confirm('You have provided FAN: ' + faidaFanValue +  ' as your Fayda FAN number. Please confirm that the provided Fayda Alias Number (FAN) is correct, as this is your final opportunity to make any corrections before it is permanently updated to your profile. Are you sure you want to proceed?');
        }

        if (!form_being_submitted && isValid && confirmm) {
            $('#updateStudentDetail').val('Updating Student Profile...');
            form_being_submitted = true;
            return true;
        } else {
            return false;
        }
    });

    $('#country_id_2').change(function() {

        var countryId = $(this).val();

        $('#region_id_2').attr('disabled', true);
        $('#zone_id_2').attr('disabled', true);
        $('#woreda_id_2').attr('disabled', true);
        $('#city_id_2').attr('disabled', true);

        if (countryId) {
            $.ajax({
                url: '/students/get_regions/' + countryId,
                type: 'get',
                data: countryId,
                success: function(data, textStatus, xhr) {
                    $('#region_id_2').attr('disabled', false);
                    $('#region_id_2').empty();
                    $('#region_id_2').append(data);

                    $('#zone_id_2').empty().append('<option value="">[ Select Zone ]</option>');
                    $('#woreda_id_2').empty().append('<option value="">[ Select Woreda ]</option>');
                    $('#city_id_2').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });

            return false;

        } else {
            $('#region_id_2').empty().append('<option value="">[ Select Region ]</option>');
            $('#zone_id_2').empty().append('<option value="">[ Select Zone ]</option>');
            $('#woreda_id_2').empty().append('<option value="">[ Select Woreda ]</option>');
            $('#city_id_2').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
        }
    });

    // Load zone options based on selected region
    $('#region_id_2').change(function() {

        var regionId = $(this).val();

        $('#zone_id_2').attr('disabled', true);
        $('#woreda_id_2').attr('disabled', true);
        $('#city_id_2').attr('disabled', true);

        if (regionId) {
            $.ajax({
                url: '/students/getZones/'+ regionId,
                type: 'get',
                data: regionId,
                success: function(data, textStatus, xhr) {
                    $('#zone_id_2').attr('disabled', false);
                    $('#zone_id_2').empty();
                    $('#zone_id_2').append(data);

                    $('#woreda_id_2').empty().append('<option value="">[ Select Woreda ]</option>');
                    $('#city_id_2').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });

            return false;

        } else {
            $('#zone_id_2').empty().append('<option value="">[ Select Zone ]</option>');
            $('#woreda_id_2').empty().append('<option value="">[ Select Woreda ]</option>');
            $('#city_id_2').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
        }
    });

    // Load woreda options based on selected zone
    $('#zone_id_2').change(function() {

        var zoneId = $(this).val();

        $('#woreda_id_2').attr('disabled', true);
        $("#city_id_2").attr('disabled', true);

        if (zoneId) {
            $.ajax({
                url: '/students/getWoredas/'+ zoneId,
                type: 'get',
                data: zoneId,
                success: function(data, textStatus, xhr) {
                    $('#woreda_id_2').attr('disabled', false);
                    $('#woreda_id_2').empty();
                    $('#woreda_id_2').append(data);
                    var regionId = $("#region_id_2").val();
                    $("#city_id_2").empty();

                    $.ajax({
                        type: 'get',
                        url: '/students/getCities/' + regionId,
                        data: regionId,
                        success: function(data, textStatus, xhr) {
                            $("#city_id_2").attr('disabled', false);
                            $("#city_id_2").empty();
                            $("#city_id_2").append(data);
                        },
                        error: function(xhr, textStatus, error) {
                            alert(textStatus);
                        }
                    });

                    // end of sub category
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });

            return false;

        } else {
            $('#woreda_id_2').empty().append('<option value="">[ Select Woreda ]</option>');
            $('#city_id_2').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
        }
    });

    $('#country_id_1').change(function() {

        var countryId = $(this).val();

        $('#region_id_1').attr('disabled', true);
        $('#zone_id_1').attr('disabled', true);
        $('#woreda_id_1').attr('disabled', true);
        $('#city_id_1').attr('disabled', true);

        if (countryId) {
            $.ajax({
                url: '/students/getRegions/' + countryId,
                type: 'get',
                data: countryId,
                success: function(data, textStatus, xhr) {
                    $('#region_id_1').attr('disabled', false);
                    $('#region_id_1').empty();
                    $('#region_id_1').append(data);

                    $('#zone_id_1').empty().append('<option value="">[ Select Zone ]</option>');
                    $('#woreda_id_1').empty().append('<option value="">[ Select Woreda ]</option>');
                    $('#city_id_1').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });

            return false;

        } else {
            $('#region_id_1').empty().append('<option value="">[ Select Region ]</option>');
            $('#zone_id_1').empty().append('<option value="">[ Select Zone ]</option>');
            $('#woreda_id_1').empty().append('<option value="">[ Select Woreda ]</option>');
            $('#city_id_1').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
        }
    });

    // Load zone options based on selected region
    $('#region_id_1').change(function() {
        var regionId = $(this).val();
        $('#zone_id_1').attr('disabled', true);
        $('#woreda_id_1').attr('disabled', true);
        $('#city_id_1').attr('disabled', true);

        if (regionId) {
            $.ajax({
                url: '/students/getZones/'+ regionId,
                type: 'get',
                data: regionId,
                success: function(data, textStatus, xhr) {
                    $('#zone_id_1').attr('disabled', false);
                    $('#zone_id_1').empty();
                    $('#zone_id_1').append(data);

                    $('#woreda_id_1').empty().append('<option value="">[ Select Woreda ]</option>');
                    $('#city_id_1').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });

            return false;

        } else {
            $('#zone_id_1').empty().append('<option value="">[ Select Zone ]</option>');
            $('#woreda_id_1').empty().append('<option value="">[ Select Woreda ]</option>');
            $('#city_id_1').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
        }
    });

    // Load woreda options based on selected zone
    $('#zone_id_1').change(function() {

        var zoneId = $(this).val();

        $('#woreda_id_1').attr('disabled', true);
        $("#city_id_1").attr('disabled', true);

        if (zoneId) {
            $.ajax({
                url: '/students/getWoredas/'+ zoneId,
                type: 'get',
                data: zoneId,
                success: function(data, textStatus, xhr) {
                    $('#woreda_id_1').attr('disabled', false);
                    $('#woreda_id_1').empty();
                    $('#woreda_id_1').append(data);

                    // sub category
                    var regionId = $("#region_id_1").val();
                    $("#city_id_1").empty();

                    $.ajax({
                        type: 'get',
                        url: '/students/getCities/' + regionId,
                        data: regionId,
                        success: function(data, textStatus, xhr) {
                            $("#city_id_1").attr('disabled', false);
                            $("#city_id_1").empty();
                            $("#city_id_1").append(data);
                        },
                        error: function(xhr, textStatus, error) {
                            alert(textStatus);
                        }
                    });

                    // end of sub category
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });

            return false;

        } else {
            $('#woreda_id_1').empty().append('<option value="">[ Select Woreda ]</option>');
            $('#city_id_1').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
        }
    });
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

