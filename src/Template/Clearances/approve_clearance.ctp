<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Approve Clearance/Withdrawal Applicantions'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-12">
                <?= $this->Form->create(null, ['id' => 'ClearanceForm']) ?>
                <div style="margin-top: -30px;">
                    <hr>
                    <fieldset style="padding-bottom: 5px;">
                        <div class="row">
                            <div class="col-md-4">
                                <?= $this->Form->control('Search.academic_year', [
                                    'id' => 'AcademicYear',
                                    'label' => 'Academic Year: ',
                                    'type' => 'select',
                                    'options' => $acYearArrayData,
                                    'default' => !empty($academicYearSelected) ? $academicYearSelected : $defaultAcademicYear,
                                    'class' => 'form-control',
                                    'style' => 'width: 90%;'
                                ]) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $this->Form->control('Search.program_id', [
                                    'label' => 'Program: ',
                                    'empty' => 'All Programs',
                                    'options' => $programs,
                                    'class' => 'form-control',
                                    'style' => 'width: 90%;'
                                ]) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $this->Form->control('Search.program_type_id', [
                                    'label' => 'Program Type: ',
                                    'empty' => 'All Program Types',
                                    'options' => $programTypes,
                                    'class' => 'form-control',
                                    'style' => 'width: 80%;'
                                ]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php if (!empty($departments)): ?>
                                    <?= $this->Form->control('Search.department_id', [
                                        'label' => 'Department: ',
                                        'options' => $departments,
                                        'class' => 'form-control',
                                        'style' => 'width: 80%;'
                                    ]) ?>
                                <?php elseif (!empty($colleges)): ?>
                                    <?= $this->Form->control('Search.college_id', [
                                        'label' => 'College: ',
                                        'options' => $colleges,
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Type:</strong><br><br>
                                <?= $this->Form->control('Search.clear', [
                                    'type' => 'checkbox',
                                    'label' => 'Clearance',
                                    'checked' => (!empty($this->request->getData('Search.clear')) && ($this->request->getData('Search.clear') == 1 || $this->request->getData('Search.clear') == 'on'))
                                ]) ?>
                                <?= $this->Form->control('Search.withdrawal', [
                                    'type' => 'checkbox',
                                    'label' => 'Withdrawal',
                                    'checked' => (!empty($this->request->getData('Search.withdrawal')) && ($this->request->getData('Search.withdrawal') == 1 || $this->request->getData('Search.withdrawal') == 'on'))
                                ]) ?>
                            </div>
                        </div>
                        <hr>
                        <?= $this->Form->button(__('Filter Application'), [
                            'name' => 'filterClearance',
                            'class' => 'btn btn-primary btn-sm'
                        ]) ?>
                    </fieldset>
                </div>
                <?php if (!empty($clearances)): ?>
                    <?php
                    $options = [
                        '1' => 'Clear',
                        '-1' => 'Not Clear'
                    ];
                    $attributes = ['legend' => false, 'separator' => '<br>'];
                    ?>
                    <br>
                    <div class="small-heading fs-5"><?= __('List of clearance/withdrawal applicants processed by the system and not taken properties from the concerned bodies and waiting your decision.') ?></div>
                    <br>
                    <h6 id="validation-message_non_selected" class="text-danger fs-6"></h6>
                    <?php $start = 0; ?>
                    <?php foreach ($clearances as $deptName => $program): ?>
                        <?php foreach ($program as $programName => $programType): ?>
                            <?php foreach ($programType as $programTypeName => $clearancesList): ?>
                                <br>
                                <div style="overflow-x: auto;">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <td colspan="8" style="vertical-align: middle; border-bottom: 2px solid #555; line-height: 1.5;">
                                                <span style="font-size: 16px; font-weight: bold; margin-top: 25px;"><?= h($deptName) ?></span><br>
                                                <span class="text-muted" style="padding-top: 13px; font-size: 13px; font-weight: bold;">
                                                        <?= h($programName) ?> &nbsp; | &nbsp; <?= h($programTypeName) ?><br>
                                                    </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-center" style="width: 3%;">#</th>
                                            <th class="align-middle" style="width: 20%;">Full Name</th>
                                            <th class="text-center">Student ID</th>
                                            <th class="text-center">Sex</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Reason</th>
                                            <th class="text-center">Request Date</th>
                                            <th class="text-center" style="width: 15%;">Clearance</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($clearancesList as $clearance): ?>
                                            <tr>
                                                <td class="text-center"><?= ++$start ?></td>
                                                <td class="align-middle"><?= h($clearance['student']['full_name']) ?></td>
                                                <td class="text-center">
                                                    <?= $this->Html->link(
                                                        h($clearance['student']['studentnumber']),
                                                        ['controller' => 'Students',
                                                            'action' => 'studentAcademicProfile', $clearance['student']['id']]
                                                    ) ?>
                                                </td>
                                                <td class="text-center"><?= strcasecmp(trim($clearance['student']['gender']),
                                                        'male') === 0 ? 'M' : 'F' ?></td>
                                                <td class="text-center">
                                                    <?= isset($clearance['type']) ? ucfirst(h($clearance['type'])) : '' ?>
                                                    <?php if (!empty($clearance['attachments'])): ?>
                                                        <br>
                                                        <a href="<?php echo $clearance['attachments'][0]->getUrl()?>">
                                                            Download Attachment ('<?php echo   $clearance['attachments'][0]->file_type . ', ' .
                                                                $clearance['attachments'][0];?>' bytes)</a>

                                                    <?php endif; ?>
                                                    <?= $this->Form->hidden("Clearance.$start.id", ['value' => $clearance['id']]) ?>
                                                    <?= $this->Form->hidden("Clearance.$start.student_id", ['value' =>
                                                        $clearance['student_id']]) ?>
                                                </td>
                                                <td class="text-center"><?= isset($clearance['reason']) ? h($clearance['reason']) : '' ?></td>
                                                <td class="text-center"><?= $this->Time->format($clearance['request_date'], 'MMM d, YYYY') ?></td>
                                                <td class="align-middle" style="padding-left: 5%; padding-top: 2%;">
                                                    <?= $this->Form->radio("Clearance.$start.confirmed", $options, $attributes) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $this->Form->button('Process Selected', [
                                'name' => 'saveIt',
                                'id' => 'saveIt',
                                'class' => 'btn btn-primary btn-sm'
                            ]) ?>
                        </div>
                        <div class="col-md-8">
                            <?= $this->Form->button('Reset Form', [
                                'type' => 'reset',
                                'id' => 'resetForm',
                                'class' => 'btn btn-danger btn-sm'
                            ]) ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let formBeingSubmitted = false;
        const validationMessageNonSelected = document.getElementById('validation-message_non_selected');
        const saveItButton = document.getElementById('saveIt');
        const resetFormButton = document.getElementById('resetForm');

        saveItButton.addEventListener('click', function (event) {
            const radios = document.querySelectorAll('input[type="radio"]');
            const checkedOne = Array.from(radios).some(radio => radio.checked);

            if (!checkedOne) {
                window.alert('At least one request must be selected as clear or not clear.');
                validationMessageNonSelected.textContent = 'At least one request must be selected as clear or not clear.';
                event.preventDefault();
                return false;
            }

            if (formBeingSubmitted) {
                window.alert('Processing selected requests, please wait a moment...');
                saveItButton.disabled = true;
                event.preventDefault();
                return false;
            }

            const confirmMessage = 'Are you sure you want to process the selected clearance/withdrawal requests?';
            if (window.confirm(confirmMessage)) {
                saveItButton.value = 'Processing Selected ...';
                formBeingSubmitted = true;
                return true;
            } else {
                event.preventDefault();
                return false;
            }
        });

        resetFormButton.addEventListener('click', function (event) {
            const confirmMessage = 'Resetting the form will discard any selected clearance/withdrawal requests. Are you sure you want to reset the form?';
            if (!window.confirm(confirmMessage)) {
                event.preventDefault();
                return false;
            }
            return true;
        });
    });
</script>
