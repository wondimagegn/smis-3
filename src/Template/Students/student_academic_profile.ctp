<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-vcard" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Student Academic Profile'); ?> <?= (isset($student_academic_profile['BasicInfo']['Student']) ? ' - '. $student_academic_profile['BasicInfo']['Student']['full_name'] . ' ('. $student_academic_profile['BasicInfo']['Student']['studentnumber'] .')' : ''); ?> </span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns" style="margin-top: -35px;">
                <hr>
                <?php echo $this->Form->create('Student');
                if ($role_id != ROLE_STUDENT && !isset($student_academic_profile)) { ?>
                    <fieldset style="padding-bottom: 5px;">
                        <legend>&nbsp;&nbsp; Student Number / ID &nbsp;&nbsp;</legend>
                        <div class="row">
                            <div class="large-4 columns">
                                <?= $this->Form->input('studentID', array('label' => false, 'placeholder' => 'Type Student ID...', 'required', 'maxlength' => 25)); ?>
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <?= $this->Form->Submit('Search', array('name' => 'continue', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
                    <?php
                }
                if (!empty($student_academic_profile)) {
                    $this->assign('title_details', (!empty($this->request->params['controller']) ? ' ' . Inflector::humanize(Inflector::underscore($this->request->params['controller'])) . (!empty($this->request->params['action']) && $this->request->params['action'] != 'index' ? ' | ' . ucwords(str_replace('_', ' ', $this->request->params['action'])) : '') : '') . (isset($student_academic_profile['BasicInfo']['Student']) ? ' - '. $student_academic_profile['BasicInfo']['Student']['full_name'] . ' ('. $student_academic_profile['BasicInfo']['Student']['studentnumber'] .')' : ''));
                    //$this->assign('title_details', ' Students | ' . $this->request->action . ' | ' . 'Student Academic Profile '. (isset($student_academic_profile['BasicInfo']['Student']) ? ' - '. $student_academic_profile['BasicInfo']['Student']['full_name'] . ' ('. $student_academic_profile['BasicInfo']['Student']['studentnumber'] .')' : ''));
                    echo $this->element('student_academic_profile');
                } ?>
            </div>
        </div>
    </div>
</div>