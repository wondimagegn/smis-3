<?php
$this->assign('title', __('Edit College: ' . ($this->request->getData('College.name', '') . ($this->request->getData('College.shortname', '') ? ' (' . $this->request->getData('College.shortname') . ')' : ''))));

echo $this->Html->script('amharictyping');
?>

    <div class="box">
        <div class="box-header bg-transparent">
            <div class="box-title" style="margin-top: 10px;">
                <i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
                <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Edit College: ' . ($this->request->getData('College.name', '') . ($this->request->getData('College.shortname', '') ? ' (' . $this->request->getData('College.shortname') . ')' : ''))) ?>
            </span>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <?= $this->Form->create($college, ['data-abide' => true, 'onsubmit' => 'return checkForm(this);']) ?>
                <div class="large-12 columns" style="margin-top: -30px;">
                    <hr>
                </div>
                <div class="large-12 columns">
                    <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_SYSADMIN) { ?>
                        <div class="large-6 columns">
                            <?= $this->Form->control('id', ['type' => 'hidden']) ?>
                            <?= $this->Form->control('campus_id', [
                                'style' => 'width:90%',
                                'required' => true,
                                'label' => 'Campus',
                                'error' => [
                                    'required' => 'Campus is required.'
                                ],
                                'templates' => [
                                    'error' => '<small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                                ]
                            ]) ?>
                            <?= $this->Form->control('name', [
                                'style' => 'width:90%',
                                'placeholder' => 'Like: College of Natural Sciences',
                                'required' => true,
                                'pattern' => '[a-zA-Z]+',
                                'label' => 'Name',
                                'error' => [
                                    'required' => 'College Name is required.',
                                    'validFormat' => 'College Name must be a string.',
                                    'uniqueInCampus' => 'The college name should be unique in the campus. The name is already taken. Use another one.'
                                ],
                                'templates' => [
                                    'error' => '<small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                                ]
                            ]) ?>
                            <?= $this->Form->control('shortname', [
                                'style' => 'width:90%',
                                'placeholder' => 'Like: NS or CNS',
                                'pattern' => 'alpha',
                                'required' => true,
                                'label' => 'Short Name',
                                'error' => [
                                    'required' => 'College short name is required.',
                                    'validFormat' => 'College short name must be a single word.'
                                ],
                                'templates' => [
                                    'error' => '<small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                                ]
                            ]) ?>
                            <?= $this->Form->control('type', [
                                'style' => 'width:90%',
                                'placeholder' => 'College, School or Institute',
                                'pattern' => 'alpha',
                                'required' => true,
                                'label' => 'Type',
                                'error' => [
                                    'required' => 'Type is required.',
                                    'validFormat' => 'Type name must be a single word.'
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
                            <br>
                            <?= $this->Form->control('active', ['type' => 'checkbox']) ?>
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
                            <?= $this->Form->control('institution_code', [
                                'style' => 'width:90%',
                                'placeholder' => 'Like: AMU-CNS',
                                'pattern' => 'institution_code',
                                'label' => 'Institution Code',
                                'error' => [
                                    'validFormat' => 'Institution Code must be a single word, with hyphen-separated parts (e.g., AMU-CNS).'
                                ],
                                'templates' => [
                                    'error' => '<small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                                ]
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
                    <?php } elseif (in_array($this->request->getSession()->read('Auth.User.role_id'), [ROLE_COLLEGE, ROLE_REGISTRAR]) &&
                        $this->request->getSession()->read('Auth.User.is_admin') == 1) { ?>
                        <div class="large-6 columns">
                            <?= $this->Form->control('id', ['type' => 'hidden']) ?>
                            <?= $this->Form->control('campus_id', [
                                'style' => 'width:90%',
                                'disabled' => true,
                                'label' => 'Campus',
                                'error' => [
                                    'required' => 'Campus is required.'
                                ],
                                'templates' => [
                                    'error' => '<small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                                ]
                            ]) ?>
                            <?= $this->Form->control('campus_id', ['type' => 'hidden', 'value' => $this->request->getData('College.campus_id')]) ?>
                            <?= $this->Form->control('name', [
                                'style' => 'width:90%',
                                'placeholder' => 'Like: College of Natural Sciences',
                                'required' => true,
                                'disabled' => true,
                                'pattern' => '[a-zA-Z]+',
                                'label' => 'Name',
                                'error' => [
                                    'required' => 'College Name is required.',
                                    'validFormat' => 'College Name must be a string.',
                                    'uniqueInCampus' => 'The college name should be unique in the campus. The name is already taken. Use another one.'
                                ],
                                'templates' => [
                                    'error' => '<small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                                ]
                            ]) ?>
                            <?= $this->Form->control('name', ['type' => 'hidden', 'value' => $this->request->getData('College.name')]) ?>
                            <?= $this->Form->control('shortname', [
                                'style' => 'width:90%',
                                'placeholder' => 'Like: NS or CNS',
                                'pattern' => 'alpha',
                                'required' => true,
                                'disabled' => true,
                                'label' => 'Short Name',
                                'error' => [
                                    'required' => 'College short name is required.',
                                    'validFormat' => 'College short name must be a single word.'
                                ],
                                'templates' => [
                                    'error' => '<small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                                ]
                            ]) ?>
                            <?= $this->Form->control('shortname', ['type' => 'hidden', 'value' => $this->request->getData('College.shortname')]) ?>
                            <?= $this->Form->control('type', [
                                'style' => 'width:90%',
                                'placeholder' => 'College, School or Institute',
                                'pattern' => 'alpha',
                                'disabled' => true,
                                'label' => 'Type',
                                'error' => [
                                    'required' => 'Type is required.',
                                    'validFormat' => 'Type name must be a single word.'
                                ],
                                'templates' => [
                                    'error' => '<small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                                ]
                            ]) ?>
                            <?= $this->Form->control('type', ['type' => 'hidden', 'value' => $this->request->getData('College.type')]) ?>
                            <?= $this->Form->control('phone', [
                                'style' => 'width:90%',
                                'type' => 'tel',
                                'label' => 'Phone Office',
                                'id' => 'etPhone'
                            ]) ?>
                            <br>
                            <?= $this->Form->control('active', ['type' => 'checkbox', 'disabled' => true]) ?>
                            <?= $this->Form->control('active', ['type' => 'hidden', 'value' => $this->request->getData('College.active')]) ?>
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
                            <?= $this->Form->control('institution_code', [
                                'style' => 'width:90%',
                                'disabled' => true,
                                'placeholder' => 'Like: AMU-CNS',
                                'pattern' => 'institution_code',
                                'label' => 'Institution Code',
                                'error' => [
                                    'validFormat' => 'Institution Code must be a single word, with hyphen-separated parts (e.g., AMU-CNS).'
                                ],
                                'templates' => [
                                    'error' => '<small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                                ]
                            ]) ?>
                            <?= $this->Form->control('institution_code', ['type' => 'hidden', 'value' => $this->request->getData('College.institution_code')]) ?>
                            <?= $this->Form->control('moodle_category_id', [
                                'id' => 'moodleCategoryId',
                                'type' => 'number',
                                'min' => 1,
                                'max' => 1000,
                                'step' => 1,
                                'class' => 'fs13',
                                'label' => 'Moodle Category ID: ',
                                'style' => 'width:25%',
                                'disabled' => true,
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
                            <?= $this->Form->control('moodle_category_id', ['type' => 'hidden', 'value' => $this->request->getData('College.moodle_category_id')]) ?>
                        </div>
                    <?php } ?>
                </div>
                <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_SYSADMIN ||
                    (in_array($this->request->getSession()->read('Auth.User.role_id'), [ROLE_COLLEGE, ROLE_REGISTRAR]) &&
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
