<fieldset>
    <legend><?= __('Basic Information') ?></legend>

    <?= $this->Form->control('first_name', ['label' => 'First Name']) ?>
    <?= $this->Form->control('middle_name', ['label' => 'Middle Name']) ?>
    <?= $this->Form->control('last_name', ['label' => 'Last Name']) ?>
    <?= $this->Form->control('gender', ['type' => 'radio', 'options' => ['male' => 'Male', 'female' => 'Female']]) ?>
    <?= $this->Form->control('email') ?>
    <?= $this->Form->control('phone_mobile', ['label' => 'Mobile Number']) ?>

    <?= $this->Form->control('studentnumber', ['readonly' => true]) ?>
    <?= $this->Form->hidden('accepted_student_id') ?>

    <?= $this->Form->control('college_id', ['options' => $colleges, 'empty' => '--']) ?>
    <?= $this->Form->control('department_id', ['options' => $departments, 'empty' => '--']) ?>
    <?= $this->Form->control('program_id', ['options' => $programs, 'empty' => '--']) ?>
    <?= $this->Form->control('program_type_id', ['options' => $programTypes, 'empty' => '--']) ?>

    <?= $this->Form->control('estimated_grad_date', ['label' => 'Estimated Graduation Date']) ?>
    <?= $this->Form->control('admissionyear', ['label' => 'Admission Year']) ?>
</fieldset>
