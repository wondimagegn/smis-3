<?php
use Cake\I18n\I18n;

$this->set('title', __('Direct/Manual Student Placement to Department'));
$this->Html->script(['jquery-1.6.2.min'], ['block' => 'script']);
?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#select-all").click(function() {
            $(".checkbox1").prop('checked', $(this).prop('checked'));
        });
    });
</script>

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h2><?= __('Direct/Manual Student Placement to Department') ?></h2>
                    <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'directPlacement'], 'class' => 'form-horizontal']) ?>
                    <table class="table">
                        <tbody>
                        <tr>
                            <td>
                                <?= $this->Form->control('AcceptedStudent.academic_year', [
                                    'id' => 'academic-year',
                                    'label' => ['text' => __('Academic Year'), 'class' => 'control-label'],
                                    'type' => 'select',
                                    'options' => $academicYearList,
                                    'empty' => __('--Select Academic Year--'),
                                    'value' => $defaultAcademicYear ?? '',
                                    'class' => 'form-control'
                                ]) ?>
                            </td>
                            <td>
                                <?= $this->Form->control('AcceptedStudent.program_type_id', [
                                    'id' => 'program-type',
                                    'label' => ['text' => __('Program Type'), 'class' => 'control-label'],
                                    'class' => 'form-control'
                                ]) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= $this->Form->control('AcceptedStudent.name', [
                                    'id' => 'name',
                                    'label' => ['text' => __('Name'), 'class' => 'control-label'],
                                    'class' => 'form-control'
                                ]) ?>
                            </td>
                            <td>
                                <?= $this->Form->control('AcceptedStudent.limit', [
                                    'id' => 'limit',
                                    'label' => ['text' => __('Limit'), 'class' => 'control-label'],
                                    'class' => 'form-control'
                                ]) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <?= $this->Form->button(__('Search'), ['name' => 'search', 'class' => 'btn btn-primary']) ?>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <?php if (!empty($acceptedStudents)): ?>
                        <div class="accepted-students-index">
                            <h3><?= __('Select Department') ?></h3>
                            <?= $this->Form->create(null, ['id' => 'direct-placement-form', 'url' => ['action' => 'directPlacement'], 'class' => 'form-horizontal']) ?>
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td>
                                        <?= $this->Form->control('AcceptedStudent.department_id', [
                                            'id' => 'department-id',
                                            'type' => 'select',
                                            'options' => $departments,
                                            'empty' => __('--Select Department--'),
                                            'value' => $selectedDepartment ?? '',
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th>
                                        <?= __('Select/Unselect All') ?><br/>
                                        <?= $this->Form->checkbox('selectall', ['id' => 'select-all']) ?>
                                    </th>
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
                                        <td>
                                            <?= $this->Form->checkbox("AcceptedStudent.directplacement.{$acceptedStudent->id}", [
                                                'disabled' => $acceptedStudent->placement_approved_by_department == 1,
                                                'class' => 'checkbox1'
                                            ]) ?>
                                        </td>
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
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <?= $this->Form->button(__('Assign To Selected Department'), ['name' => 'assigndirectly', 'class' => 'btn btn-primary']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <?= $this->Form->button(__('Transfer To Selected Department'), ['name' => 'transfertodepartment', 'class' => 'btn btn-primary']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <?= $this->Form->button(__('Cancel Selected Student Placement'), ['name' => 'cancelplacement', 'class' => 'btn btn-primary']) ?>
                                        </div>
                                    </td>
                                </tr>
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
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <span></span><?= __('No Accepted students in the selected academic year') ?>
                        </div>
                    <?php endif; ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
