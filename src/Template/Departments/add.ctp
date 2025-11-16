<?php

use Cake\Core\Configure;

$this->assign('title', __('Add Department'));

// Load amharictyping.js
echo $this->Html->script('amharictyping');
?>

    <div class="box">
        <div class="box-header bg-transparent">
            <div class="box-title" style="margin-top: 10px;">
                <i class="fontello-plus" style="font-size: larger; font-weight: bold;"></i>
                <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Add Department') ?></span>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <?= $this->Form->create($department, ['data-abide' => true,
                    'onsubmit' => 'return checkForm(this);']) ?>
                <div class="large-12 columns" style="margin-top: -30px;">
                    <hr>
                </div>
                <div class="large-12 columns">
                    <div class="large-6 columns">
                        <?= $this->Form->control('college_id', ['style' => 'width:90%']) ?>
                        <?= $this->Form->control('name', [
                            'style' => 'width:90%',
                            'placeholder' => 'Like: Computer Science',
                            'required' => true,
                            'pattern' => '[a-zA-Z]+',
                            'label' => 'Name',
                            'error' => [
                                'required' => 'Department Name is required and must be a string.',
                                'pattern' => 'Department Name must be a string.'
                            ],
                            'templates' => [
                                'error' => '<small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                            ]
                        ]) ?>
                        <?= $this->Form->control('shortname', [
                            'style' => 'width:90%',
                            'placeholder' => 'Like: COMP',
                            'pattern' => 'alpha',
                            'label' => 'Short Name',
                            'error' => [
                                'pattern' => 'Department short name must be a single word.'
                            ],
                            'templates' => [
                                'error' => '<small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                            ]
                        ]) ?>
                        <?= $this->Form->control('phone', [
                            'style' => 'width:90%',
                            'type' => 'tel',
                            'label' => 'Phone Office',
                            'id' => 'etPhone'
                        ]) ?>
                        <?= $this->Form->control('institution_code', [
                            'style' => 'width:90%',
                            'placeholder' => 'Like: AMU-AMIT-COMP',
                            'pattern' => 'institution_code',
                            'label' => 'Institution Code',
                            'error' => [
                                'pattern' => 'Institution Code must be a single word, with Hyphen separated; Like AMU-AMIT-COMP'
                            ],
                            'templates' => [
                                'error' => '<small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                            ]
                        ]) ?>
                        <br>
                        <?= $this->Form->control('active', [
                            'type' => 'checkbox',
                            'checked' => true
                        ]) ?>
                        <br>
                        <?= $this->Form->control('allow_year_based_curriculums', [
                            'type' => 'checkbox'
                        ]) ?>
                        <br>
                    </div>
                    <div class="large-6 columns">
                        <?= $this->Form->control('amharic_name', [
                            'style' => 'width:90%',
                            'id' => 'AmharicText',
                            'onkeypress' => 'return AmharicPhoneticKeyPress(event, this);'
                        ]) ?>
                        <?= $this->Form->control('amharic_short_name', [
                            'style' => 'width:90%',
                            'id' => 'AmharicTextShort',
                            'onkeypress' => 'return AmharicPhoneticKeyPress(event, this);'
                        ]) ?>
                        <?= $this->Form->control('description', [
                            'style' => 'width:90%'
                        ]) ?>
                        <?= $this->Form->control('type', [
                            'style' => 'width:90%',
                            'id' => 'departmentType',
                            'onchange' => 'updateDepartmentType()',
                            'options' => Configure::read('department_types'),
                            'default' => 'Department'
                        ]) ?>
                        <?= $this->Form->control('type_amharic', [
                            'type' => 'hidden',
                            'id' => 'departmentTypeAmharic',
                            'value' => $this->request->getData('Department.type_amharic',
                                DEPARTMENT_TYPE_AMHARIC_DEPARTMENT)
                        ]) ?>


                        <?= $this->Form->control('moodle_category_id', [
                            'id' => 'moodleCategoryId',
                            'type' => 'number',
                            'min' => 1,
                            'max' => 1000,
                            'step' => 1,
                            'class' => 'fs13',
                            'label' => 'Moodle Category ID: ',
                            'style' => 'width:25%',
                            'required' => true,
                            'error' => [
                                'required' => 'Moodle Category ID is required and cannot be empty.',
                                'integer' => 'Moodle Category ID must be a valid number.',
                                'min' => 'Moodle Category ID must be at least 1.',
                                'max' => 'Moodle Category ID must not exceed 1000.'
                            ],
                            'templates' => [
                                'error' => '<small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                            ]
                        ]) ?>
                    </div>
                </div>
                <div class="large-12 columns">
                    <hr>
                    <?= $this->Form->button('Add Department', [
                        'id' => 'SubmitID',
                        'class' => 'tiny radius button bg-blue'
                    ]) ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
</div>
<script>
        function updateDepartmentType() {
            var dept_amharic = "<?= h(DEPARTMENT_TYPE_AMHARIC_DEPARTMENT) ?>";
            var faculty_amharic = "<?= h(DEPARTMENT_TYPE_AMHARIC_FACULTY) ?>";
            var school_amharic = "<?= h(DEPARTMENT_TYPE_AMHARIC_SCHOOL) ?>";
            var department_type = $("#departmentType").val();
            if (department_type == 'Department') {
                $("#departmentTypeAmharic").val(dept_amharic);
            } else if (department_type == 'Faculty') {
                $("#departmentTypeAmharic").val(faculty_amharic);
            } else if (department_type == 'School') {
                $("#departmentTypeAmharic").val(school_amharic);
            } else {
                $("#departmentTypeAmharic").val('');
            }
        }

        var form_being_submitted = false;

        function checkForm(form) {
            if (form_being_submitted) {
                alert("Adding Department, please wait a moment...");
                form.SubmitID.disabled = true;
                return false;
            }
            form.SubmitID.value = 'Adding Department...';
            form_being_submitted = true;
            return true;
        }

        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
</script>
