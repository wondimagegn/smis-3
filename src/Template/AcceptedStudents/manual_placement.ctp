<?php
use Cake\I18n\I18n;

$this->set('title', __('Manual Student Placement to Department'));
$this->Html->script(['jquery-1.6.2.min', 'jquery-selectall'], ['block' => 'script']);
?>

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="accepted-students-index">
                        <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'manualPlacement'], 'class' => 'form-horizontal']) ?>
                        <?php if (empty($selectedAcademicYear)): ?>
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td>
                                        <?= $this->Form->control('academic_year', [
                                            'id' => 'admission-year',
                                            'label' => ['text' => __('Academic Year'), 'class' => 'control-label'],
                                            'type' => 'select',
                                            'options' => $academicYearList,
                                            'empty' => __('--Select Academic Year--'),
                                            'value' => $selected ?? '',
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <?= $this->Form->button(__('Submit'), ['name' => 'prepandacademicyear', 'class' => 'btn btn-primary']) ?>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <?php if (!empty($acceptedStudents)): ?>
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <?= $this->Form->button(__('Cancel Auto Placement'), ['name' => 'cancelplacement', 'class' => 'btn btn-primary']) ?>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <table class="table table-bordered table-striped">
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
                                            <td><?= h($acceptedStudent->academic_year) ?></td>
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
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <?= __('No Accepted students in the selected academic year') ?>
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
