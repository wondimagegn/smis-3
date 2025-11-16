<?php
use Cake\I18n\I18n;

$this->set('title', __('Export/Print Student Campus Assignments'));
?>

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="accepted-students-index">
                        <h2><?= __('Export/Print Student Campus Assignments') ?></h2>
                        <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'viewCampusAssignment'], 'class' => 'form-horizontal']) ?>
                        <table class="table">
                            <tr>
                                <td colspan="2">
                                    <?= $this->Form->control('AcceptedStudent.academic_year', [
                                        'id' => 'academic-year',
                                        'label' => ['text' => __('Academic Year'), 'class' => 'control-label'],
                                        'type' => 'select',
                                        'options' => $academicYearList,
                                        'empty' => __('--Select Academic Year--'),
                                        'value' => $selectedAcademicYear ?? '',
                                        'class' => 'form-control'
                                    ]) ?>
                                </td>
                                <td>
                                    <?= $this->Form->control('AcceptedStudent.sex', [
                                        'label' => ['text' => __('Gender'), 'class' => 'control-label'],
                                        'required' => false,
                                        'type' => 'select',
                                        'empty' => __('All'),
                                        'options' => ['female' => __('Female'), 'male' => __('Male')],
                                        'class' => 'form-control'
                                    ]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?= $this->Form->control('AcceptedStudent.college_id', [
                                        'class' => 'form-control'
                                    ]) ?>
                                </td>
                                <td>
                                    <?= $this->Form->control('AcceptedStudent.campus_id', [
                                        'class' => 'form-control'
                                    ]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?= $this->Form->control('AcceptedStudent.program_id', [
                                        'empty' => __('--Select Program--'),
                                        'class' => 'form-control'
                                    ]) ?>
                                </td>
                                <td>
                                    <?= $this->Form->control('AcceptedStudent.program_type_id', [
                                        'empty' => __('--Select Program Type--'),
                                        'class' => 'form-control'
                                    ]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <?= $this->Form->control('Search.limit', [
                                        'label' => ['text' => __('Limit'), 'class' => 'control-label'],
                                        'class' => 'form-control'
                                    ]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="form-group">
                                        <?= $this->Form->button(__('Search'), ['name' => 'search', 'class' => 'btn btn-primary']) ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <?php if (!empty($acceptedStudents)): ?>
                            <table class="table">
                                <tr>
                                    <td class="h2" colspan="2"><?= __('College: {0}', h($selectedCollegeName)) ?></td>
                                </tr>
                                <tr>
                                    <td class="h2" colspan="2"><?= __('Department: {0}', h($selectedCampusName)) ?></td>
                                </tr>
                                <tr>
                                    <td class="h2" colspan="2"><?= __('Program: {0}', h($selectedProgramName)) ?></td>
                                </tr>
                                <tr>
                                    <td class="h2" colspan="2"><?= __('Program Type: {0}', h($selectedProgramTypeName)) ?></td>
                                </tr>
                                <tr>
                                    <td class="h2"><?= __('Academic Year: {0}', h($selectedAcademicYear)) ?></td>
                                    <td style="text-align: right;">
                                        <?= $this->Html->link(
                                            $this->Html->image('pdf_icon.gif', ['alt' => __('Print To PDF')]) . ' ' . __('Print'),
                                            ['action' => 'printStudentsNumberPdf'],
                                            ['escape' => false]
                                        ) ?>
                                        <?= $this->Html->link(
                                            $this->Html->image('xls-icon.gif', ['alt' => __('Export To Excel')]) . ' ' . __('Export'),
                                            ['action' => 'exportStudentsNumberXls'],
                                            ['escape' => false]
                                        ) ?>
                                    </td>
                                </tr>
                            </table>
                            <table class="table table-bordered table-striped" style="width: 60%;">
                                <tbody>
                                <tr>
                                    <th><?= __('No') ?></th>
                                    <th><?= $this->Paginator->sort('full_name', __('Full Name')) ?></th>
                                    <th><?= $this->Paginator->sort('sex', __('Sex')) ?></th>
                                    <th><?= $this->Paginator->sort('studentnumber', __('Student ID')) ?></th>
                                    <th><?= $this->Paginator->sort('region_id', __('Region')) ?></th>
                                </tr>
                                <?php $count = 1; ?>
                                <?php foreach ($acceptedStudents as $index => $acceptedStudent): ?>
                                    <tr class="<?= $index % 2 == 0 ? 'altrow' : '' ?>">
                                        <td><?= $count++ ?></td>
                                        <td><?= h($acceptedStudent->full_name) ?></td>
                                        <td><?= h($acceptedStudent->sex) ?></td>
                                        <td><?= h($acceptedStudent->studentnumber) ?></td>
                                        <td><?= h($acceptedStudent->Region->name) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
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
                        <?php elseif (empty($acceptedStudents) && !$isBeforeSearch): ?>
                            <div class="alert alert-info">
                                <span></span>
                                <?= __('No Accepted students have student identification in the selected criteria. If you have students in these criteria, please go to the Generate Student ID page and generate student IDs before exporting/printing.') ?>
                            </div>
                        <?php endif; ?>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
