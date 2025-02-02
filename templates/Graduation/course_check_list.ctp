<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-vcard" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Student Course List'); ?> <?= (isset($student_academic_profile['BasicInfo']['Student']) ? ' - '. $student_academic_profile['BasicInfo']['Student']['full_name'] . ' ('. $student_academic_profile['BasicInfo']['Student']['studentnumber'] .')' : ''); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns" style="margin-top: -20px;">
                <?=  $this->Form->create('Student'); ?>
                <?php
                if ($role_id != ROLE_STUDENT && !isset($student_academic_profile)) { ?>
                    <fieldset style="padding-bottom: 5px;">
                        <legend>&nbsp;&nbsp; Student Number / ID &nbsp;&nbsp;</legend>
                        <div class="row">
                            <div class="large-4 columns">
                                <?= $this->Form->input('studentID', array('label' => false, 'placeholder' => 'Type Student ID...', 'required')); ?>
                            </div>
                        </div>
                    </fieldset>
                    <?= $this->Form->Submit('Search', array('name' => 'continue', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
                    <?php
                }
                if (!empty($student_academic_profile)) {
                    echo $this->element('course_check_list');
                }
                ?>
            </div>
        </div>
    </div>
</div>