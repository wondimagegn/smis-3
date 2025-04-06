<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Batch Admit Students'); ?>
            </span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create(null); ?>

                <div style="margin-top: -30px;">
                    <hr>
                    <blockquote>
                        <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                        <span class="fs14 text-gray">
                            Admit selected students at once.
                            <b><i>Please don't forget to record and maintain each student's record after batch admission</i></b>.
                        </span>
                    </blockquote>
                    <hr>

                    <fieldset>
                        <legend>Search Filter</legend>
                        <div class="row">
                            <div class="large-4 columns">
                                <?= $this->Form->control('Search.academicyear', [
                                    'label' => 'Academic Year:',
                                    'options' => $acyear_array_data,
                                    'empty' => '[ Select Academic Year ]',
                                    'default' => $defaultacademicyear ?? '',
                                    'style' => 'width:90%;'
                                ]) ?>
                            </div>
                            <div class="large-4 columns">
                                <?= $this->Form->control('Search.program_id', [
                                    'label' => 'Program:',
                                    'options' => $programs,
                                    'empty' => '[ Select Program ]',
                                    'style' => 'width:90%;'
                                ]) ?>
                            </div>
                            <div class="large-4 columns">
                                <?= $this->Form->control('Search.program_type_id', [
                                    'label' => 'Program Type:',
                                    'options' => $programTypes,
                                    'empty' => '[ Select Program Type ]',
                                    'style' => 'width:90%;'
                                ]) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="large-4 columns">
                                <?php if (!empty($college_level)): ?>
                                    <?= $this->Form->control('Search.college_id', [
                                        'label' => 'College:',
                                        'options' => $colleges,
                                        'empty' => '[ Select College ]',
                                        'style' => 'width:90%;',
                                        'required' => true
                                    ]) ?>
                                <?php elseif (!empty($department_level)): ?>
                                    <?= $this->Form->control('Search.department_id', [
                                        'label' => 'Department:',
                                        'options' => $departments,
                                        'empty' => '[ Select Department ]',
                                        'style' => 'width:90%;'
                                    ]) ?>
                                <?php endif; ?>
                            </div>
                            <div class="large-4 columns">
                                <?= $this->Form->control('Search.name', [
                                    'label' => 'Student Name:',
                                    'type' => 'text',
                                    'style' => 'width:90%;'
                                ]) ?>
                            </div>
                            <div class="large-4 columns">
                                <?= $this->Form->control('Search.limit', [
                                    'label' => 'Limit:',
                                    'type' => 'number',
                                    'min' => '0',
                                    'max' => '2000',
                                    'step' => '100',
                                    'style' => 'width:90%;'
                                ]) ?>
                            </div>
                        </div>

                        <hr>
                        <?= $this->Form->submit('Search', [
                            'name' => 'getacceptedstudent',
                            'class' => 'tiny radius button bg-blue'
                        ]) ?>
                    </fieldset>
                </div>

                <?php if (!empty($acceptedStudents)): ?>
                    <hr>
                    <h6 id="validation-message_non_selected" class="text-red fs14"></h6>

                    <div style="overflow-x:auto;">
                        <table class="table">
                            <thead>
                            <tr>
                                <td colspan="11"><h6>Select Students you want to batch admit</h6></td>
                            </tr>
                            <tr>
                                <th><?= $this->Form->checkbox('SelectAll', ['id' => 'select-all']) ?></th>
                                <th>#</th>
                                <th>Full Name</th>
                                <th>Sex</th>
                                <th>Student ID</th>
                                <th>EHEECE</th>
                                <th>Department</th>
                                <th>ACY</th>
                            </tr>
                            <?php if (!empty($curriculums)): ?>
                                <tr>
                                    <td colspan="8">
                                        <?= $this->Form->control('Curriculum.curriculum_id', [
                                            'options' => $curriculums,
                                            'label' => 'Attach Curriculum',
                                            'empty' => '[ Select Curriculum ]',
                                            'required' => true
                                        ]) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </thead>
                            <tbody>
                            <?php $i = 1; foreach ($acceptedStudents as $student): ?>
                                <tr>
                                    <td><?= $this->Form->checkbox('AcceptedStudent.approve.' . $student['AcceptedStudent']['id'], ['class' => 'checkbox1']) ?></td>
                                    <td><?= $i++ ?></td>
                                    <td><?= h($student['AcceptedStudent']['full_name']) ?></td>
                                    <td><?= strtoupper(substr($student['AcceptedStudent']['sex'], 0, 1)) ?></td>
                                    <td><?= h($student['AcceptedStudent']['studentnumber']) ?></td>
                                    <td><?= h($student['AcceptedStudent']['EHEECE_total_results']) ?></td>
                                    <td><?= h($student['Department']['name'] ?? 'N/A') ?></td>
                                    <td><?= h($student['AcceptedStudent']['academicyear']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <hr>
                    <?= $this->Form->submit('Admit Selected Students', [
                        'name' => 'admit',
                        'id' => 'admitAll',
                        'class' => 'tiny radius button bg-blue'
                    ]) ?>
                <?php endif; ?>

                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<?php $this->start('scriptBottom'); ?>
<script>
    let formBeingSubmitted = false;

    document.getElementById('admitAll')?.addEventListener('click', function(e) {
        const checkboxes = document.querySelectorAll('input[type="checkbox"].checkbox1');
        const checkedOne = Array.from(checkboxes).some(x => x.checked);

        if (!checkedOne) {
            e.preventDefault();
            alert('At least one student must be selected to admit!');
            document.getElementById('validation-message_non_selected').innerText = 'At least one student must be selected to admit!';
            return false;
        }

        if (formBeingSubmitted) {
            alert("Admitting Students, please wait...");
            this.disabled = true;
            return false;
        }

        this.value = 'Admitting Students...';
        formBeingSubmitted = true;
    });

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    // Select all logic
    document.getElementById('select-all')?.addEventListener('change', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.checkbox1').forEach(cb => cb.checked = isChecked);
    });
</script>
<?php $this->end(); ?>
