<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\ORM\Entity $student
 * @var array $student_academic_profile
 * @var int $role_id
 */

$this->assign('title', __('Student Academic Profile') . (isset($studentAcademicProfile['BasicInfo']['Student'])
        ? ' - ' . h($studentAcademicProfile['BasicInfo']['Student']['full_name']) . ' (' .
        h($studentAcademicProfile['BasicInfo']['Student']['studentnumber']) . ')'
        : ''));
?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-vcard" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Student Academic Profile'); ?>
                <?= (isset($student_academic_profile['BasicInfo']['Student']) ? ' - '.
                    $student_academic_profile['BasicInfo']['Student']['full_name'] . ' ('.
                    $student_academic_profile['BasicInfo']['Student']['studentnumber'] .')' : ''); ?> </span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns" style="margin-top: -35px;">
                <hr>
                <?= $this->Form->create($student, ['url' => ['action' => 'studentAcademicProfile']]) ?>
                <?php
                if ($role_id != ROLE_STUDENT && !isset($studentAcademicProfile)) { ?>
                    <fieldset style="padding-bottom: 5px;">
                        <legend>&nbsp;&nbsp; Student Number / ID &nbsp;&nbsp;</legend>
                        <div class="row">
                            <div class="large-4 columns">
                                <?= $this->Form->control('studentID', [
                                    'label' => false,
                                    'placeholder' => __('Type Student ID...'),
                                    'required' => true,
                                    'maxlength' => 25,
                                    'class' => 'form-control'
                                ]) ?>
                            </div>
                        </div>
                    </fieldset>
                    <hr>

                    <?= $this->Form->hidden('continue', ['value' => 'Search']) ?>
                    <?= $this->Form->button(__('Search'), [
                        'type' => 'submit',
                        'name' => 'continue',
                        'value' => __('Search'),
                        'class' => 'btn btn-primary btn-sm'
                    ]) ?>
                    <?= $this->Form->end() ?>

                   <?php
                }
               ?>

                <?php if (!empty($studentAcademicProfile)): ?>
                    <?= $this->element('student_academic_profile') ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
