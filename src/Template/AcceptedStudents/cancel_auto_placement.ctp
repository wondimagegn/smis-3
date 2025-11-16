<?php
use Cake\I18n\I18n;

$this->set('title', __('Auto Student Placement to Department Cancellation'));
?>

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="accepted-students-index">
                        <h2><?= __('Auto Student Placement to Department Cancellation') ?></h2>
                        <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'cancelAutoPlacement'], 'class' => 'form-horizontal']) ?>
                        <table class="table">
                            <tr>
                                <td style="width: 15%;"><?= __('Academic Year') ?>:</td>
                                <td style="width: 20%;">
                                    <?= $this->Form->control('Search.academic_year', [
                                        'options' => $academicYearList,
                                        'empty' => __('--select academic year--'),
                                        'label' => false,
                                        'class' => 'form-control'
                                    ]) ?>
                                </td>
                                <td style="width: 15%;"><?= __('Limit') ?>:</td>
                                <td style="width: 20%;">
                                    <?= $this->Form->control('Search.limit', [
                                        'label' => false,
                                        'class' => 'form-control'
                                    ]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <div class="form-group">
                                        <?= $this->Form->button(__('Search'), ['name' => 'search', 'id' => 'search', 'class' => 'btn btn-primary']) ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <?php if (isset($selectedAcademicYear)): ?>
                            <?php if (!empty($acceptedStudents)): ?>
                                <div class="accepted-students-index">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <?php if (empty($hideButton)): ?>
                                                <td colspan="8">
                                                    <div class="form-group">
                                                        <?= $this->Form->button(__('Cancel Auto Placement'), ['name' => 'cancelplacement', 'class' => 'btn btn-primary']) ?>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                        <tr>
                                            <th><?= $this->Paginator->sort('full_name', __('Full Name')) ?></th>
                                            <th><?= $this->Paginator->sort('sex', __('Sex')) ?></th>
                                            <th><?= $this->Paginator->sort('studentnumber', __('Student Number')) ?></th>
                                            <th><?= $this->Paginator->sort('EHEECE_total_results', __('EHEECE Total Results')) ?></th>
                                            <th><?= $this->Paginator->sort('department_id', __('Department')) ?></th>
                                            <th><?= $this->Paginator->sort('program_type_id', __('Program Type')) ?></th>
                                            <th><?= $this->Paginator->sort('academic_year', __('Academic Year')) ?></th>
                                            <th><?= $this->Paginator->sort('placementtype', __('Placement Type')) ?></th>
                                        </tr>
                                        <?php foreach ($acceptedStudents as $index => $acceptedStudent): ?>
                                            <tr class="<?= $index % 2 == 0 ? 'altrow' : '' ?>">
                                                <?= $this->Form->hidden("AcceptedStudent.{$acceptedStudent->id}.id", ['value' => $acceptedStudent->id]) ?>
                                                <td><?= h($acceptedStudent->full_name) ?></td>
                                                <td><?= h($acceptedStudent->sex) ?></td>
                                                <td><?= h($acceptedStudent->studentnumber) ?></td>
                                                <td><?= h($acceptedStudent->EHEECE_total_results) ?></td>
                                                <td>
                                                    <?= $this->Html->link(
                                                        h($acceptedStudent->Department->name),
                                                        ['controller' => 'Departments', 'action' => 'view', $acceptedStudent->Department->id]
                                                    ) ?>
                                                </td>
                                                <td>
                                                    <?= $this->Html->link(
                                                        h($acceptedStudent->ProgramType->name),
                                                        ['controller' => 'ProgramTypes', 'action' => 'view', $acceptedStudent->ProgramType->id]
                                                    ) ?>
                                                </td>
                                                <td>
                                                    <?= $this->Form->hidden("AcceptedStudent.{$acceptedStudent->id}.academic_year", ['value' => $acceptedStudent->academic_year]) ?>
                                                    <?= h($acceptedStudent->academic_year) ?>
                                                </td>
                                                <td><?= h($acceptedStudent->placementtype) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                    <p>
                                        <?= $this->Paginator->counter([
                                            'format' => __('Page {{page}} of {{pages}}, showing {{current}} records out of {{count}} total, starting on record {{start}}, ending on {{end}}')
                                        ]) ?>
                                    </p>
                                    <div class="pagination">
                                        <?= $this->Paginator->prev('<< ' . __('previous')) ?>
                                        | <?= $this->Paginator->numbers() ?> |
                                        <?= $this->Paginator->next(__('next') . ' >>') ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <span></span><?= __('There is no student that needs auto placement cancellation or the auto placement has been approved by the department, in which case you cannot cancel the auto placement.') ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
