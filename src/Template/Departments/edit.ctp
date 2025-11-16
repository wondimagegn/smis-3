<?php

use Cake\Core\Configure;

$this->assign('title', __('Edit Department: ' . ($this->request->getData('Department.name', '') . ($this->request->getData('Department.shortname', '') ? ' (' . $this->request->getData('Department.shortname') . ')' : ''))));

echo $this->Html->script('amharictyping', ['block' => 'script']);
?>

<div class="box">
        <div class="box-header bg-transparent">
            <div class="box-title" style="margin-top: 10px;">
                <i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
                <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Edit Department: ' . ($this->request->getData('Department.name', '') . ($this->request->getData('Department.shortname', '') ? ' (' . $this->request->getData('Department.shortname') . ')' : ''))) ?>
            </span>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <?= $this->Form->create($department, ['data-abide' => true, 'onsubmit' => 'return checkForm(this);']) ?>
                <div class="large-12 columns" style="margin-top: -30px;">
                    <hr>
                </div>
                <div class="large-12 columns">
                    <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_SYSADMIN) { ?>
                        <div class="large-6 columns">
                            <?= $this->Form->control('id', ['type' => 'hidden']) ?>
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
                                    'error' => '<small class="error" style="background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
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
                            <?= $this->Form->control('active', ['type' => 'checkbox']) ?>
                            <br>
                            <?= $this->Form->control('allow_year_based_curriculums', ['type' => 'checkbox']) ?>
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
                            <?= $this->Form->control('description', ['style' => 'width:90%']) ?>
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
                                'value' => $this->request->getData('Department.type_amharic', DEPARTMENT_TYPE_AMHARIC_DEPARTMENT)
                            ]) ?>
                            <?= $this->Form->control('moodle_category_id', [
                                'id' => 'moodleCategoryId',
                                'type' => 'number',
                                'min' => 1,
                                'max' => 1000,
                                'step' => 1,
                                'class' => 'fs13',
                                'label' => 'Moodle Category ID: ',
                                'style' => 'width:25%'
                            ]) ?>
                        </div>
                    <?php } elseif (in_array($this->request->getSession()->read('Auth.User.role_id'), [ROLE_COLLEGE, ROLE_REGISTRAR, ROLE_DEPARTMENT]) && $this->request->getSession()->read('Auth.User.is_admin') == 1) { ?>
                        <div class="large-6 columns">
                            <?= $this->Form->control('id', ['type' => 'hidden']) ?>
                            <?= $this->Form->control('college_id', ['style' => 'width:90%', 'disabled' => true]) ?>
                            <?= $this->Form->control('college_id', ['type' => 'hidden', 'value' => $this->request->getData('Department.college_id')]) ?>
                            <?= $this->Form->control('name', [
                                'style' => 'width:90%',
                                'placeholder' => 'Like: Computer Science',
                                'required' => true,
                                'disabled' => true,
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
                            <?= $this->Form->control('name', ['type' => 'hidden', 'value' => $this->request->getData('Department.name')]) ?>
                            <?= $this->Form->control('shortname', [
                                'style' => 'width:90%',
                                'placeholder' => 'Like: COMP',
                                'disabled' => true,
                                'pattern' => 'alpha',
                                'label' => 'Short Name',
                                'error' => [
                                    'pattern' => 'Department short name must be a single word.'
                                ],
                                'templates' => [
                                    'error' => '<small class="error" style="background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                                ]
                            ]) ?>
                            <?= $this->Form->control('shortname', ['type' => 'hidden', 'value' => $this->request->getData('Department.shortname')]) ?>
                            <?= $this->Form->control('phone', [
                                'style' => 'width:90%',
                                'type' => 'tel',
                                'label' => 'Phone Office',
                                'id' => 'etPhone'
                            ]) ?>
                            <?= $this->Form->control('institution_code', [
                                'style' => 'width:90%',
                                'disabled' => true,
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
                            <?= $this->Form->control('institution_code', ['type' => 'hidden', 'value' => $this->request->getData('Department.institution_code')]) ?>
                            <br>
                            <?= $this->Form->control('active', ['type' => 'checkbox', 'disabled' => true]) ?>
                            <br>
                            <?= $this->Form->control('allow_year_based_curriculums', ['type' => 'checkbox', 'disabled' => true]) ?>
                            <br>
                            <?= $this->Form->control('active', ['type' => 'hidden', 'value' => $this->request->getData('Department.active')]) ?>
                            <?= $this->Form->control('allow_year_based_curriculums', ['type' => 'hidden', 'value' => $this->request->getData('Department.allow_year_based_curriculums')]) ?>
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
                            <?= $this->Form->control('description', ['style' => 'width:90%']) ?>
                            <?= $this->Form->control('type', [
                                'style' => 'width:90%',
                                'id' => 'departmentType',
                                'disabled' => true,
                                'onchange' => 'updateDepartmentType()',
                                'options' => Configure::read('department_types'),
                                'default' => 'Department'
                            ]) ?>
                            <?= $this->Form->control('type', ['type' => 'hidden', 'value' => $this->request->getData('Department.type')]) ?>
                            <?= $this->Form->control('type_amharic', [
                                'type' => 'hidden',
                                'id' => 'departmentTypeAmharic',
                                'value' => $this->request->getData('Department.type_amharic', DEPARTMENT_TYPE_AMHARIC_DEPARTMENT)
                            ]) ?>
                            <?= $this->Form->control('moodle_category_id', [
                                'id' => 'moodleCategoryId',
                                'disabled' => true,
                                'type' => 'number',
                                'min' => 1,
                                'max' => 1000,
                                'step' => 1,
                                'class' => 'fs13',
                                'label' => 'Moodle Category ID: ',
                                'style' => 'width:25%'
                            ]) ?>
                            <?= $this->Form->control('moodle_category_id', ['type' => 'hidden', 'value' => $this->request->getData('Department.moodle_category_id')]) ?>
                        </div>
                    <?php } ?>
                </div>
                <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_SYSADMIN ||
                    (in_array($this->request->getSession()->read('Auth.User.role_id'), [ROLE_COLLEGE, ROLE_REGISTRAR, ROLE_DEPARTMENT]) &&
                        $this->request->getSession()->read('Auth.User.is_admin') == 1)) { ?>
                    <div class="large-12 columns">
                        <hr>
                        <?= $this->Form->button('Save Changes', [
                            'id' => 'SubmitID',
                            'class' => 'tiny radius button bg-blue'
                        ]) ?>
                        <?= $this->Form->end() ?>
                    </div>
                <?php } ?>
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
                alert("Saving Changes, please wait a moment...");
                form.SubmitID.disabled = true;
                return false;
            }
            form.SubmitID.value = 'Saving Changes...';
            form_being_submitted = true;
            return true;
        }

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
</script>
<?php $this->end(); ?>
