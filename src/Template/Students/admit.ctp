<?= $this->Form->create($student, ['type' => 'file', 'id' => 'AdmitForm']) ?>

<div class="students form">
    <?= $this->Flash->render() ?>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= __('Admit Student') ?></h3>
        </div>
        <div class="box-body">

            <?= $this->element('students/basic_info', compact('student', 'colleges', 'departments', 'programs', 'programTypes')) ?>

            <?= $this->element('students/education_background', compact('student', 'regions')) ?>

            <?= $this->element('students/address_contact', compact('student', 'regions', 'countries', 'cities', 'zones', 'woredas')) ?>

        </div>
        <div class="box-footer">
            <?= $this->Form->button(__('Save'), ['class' => 'btn btn-primary', 'name' => 'admit']) ?>
        </div>
    </div>
</div>

<?= $this->Form->end() ?>
