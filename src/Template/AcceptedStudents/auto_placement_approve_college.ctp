<?php
use Cake\I18n\I18n;

$this->set('title', __('Auto Placement Approval'));
$this->Html->script(['jquery-1.6.2.min', 'jquery-department_placement'], ['block' => 'script']);
?>

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="accepted-students-form">
                        <h2><?= __('Auto Placement Approval') ?></h2>
                        <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'autoPlacementApproveCollege'], 'class' => 'form-horizontal']) ?>
                        <table>
                            <tbody>
                            <tr>
                                <td width="100%">
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <?php if (empty($autoApprove)): ?>
                                                    <div class="text-muted"><?= __('Academic Year') ?></div>
                                                    <?= $this->Form->control('AcceptedStudent.academic_year', [
                                                    'id' => 'academic-year',
                                                    'label' => false,
                                                    'type' => 'select',
                                                    'options' => $academicYearList,
                                                    'empty' => __('--Select Academic Year--'),
                                                    'value' => $selectedAcademicYear ?? '',
                                                    'class' => 'form-control'
                                                ]) ?>
                                                    <div class="form-group">
                                                        <?= $this->Form->button(__('Continue'), ['type' => 'submit', 'class' => 'btn btn-primary']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <?php if (!empty($autoPlacedStudents)): ?>
                                        <?php $summary = $autoPlacedStudents['auto_summary']; ?>
                                        <?php unset($autoPlacedStudents['auto_summary']); ?>
                                        <?= $this->Form->hidden('AcceptedStudent.academic_year', ['value' => $selectedAcademicYear]) ?>
                                        <?php if (empty($minuteNumber)): ?>
                                            <table>
                                                <tbody>
                                                <tr>
                                                    <td style="width: 30%;">
                                                        <?= $this->Form->control('AcceptedStudent.minute_number', [
                                                            'label' => ['text' => __('Minute Number'), 'class' => 'control-label'],
                                                            'class' => 'form-control'
                                                        ]) ?>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <?= $this->Form->button(__('Approve'), ['type' => 'submit', 'name' => 'approve', 'class' => 'btn btn-primary']) ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        <?php else: ?>
                                            <table>
                                                <tbody>
                                                <tr>
                                                    <td style="width: 30%;">
                                                        <h3><?= __('List of auto-placed students approved by minute number {0}', h($minuteNumber)) ?></h3>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        <?php endif; ?>
                                        <table class="table table-bordered table-striped">
                                            <tbody>
                                            <tr>
                                                <th colspan="3"><?= __('Summary of Auto Placement') ?></th>
                                            </tr>
                                            <tr>
                                                <th><?= __('Department') ?></th>
                                                <th><?= __('Competitive Assignment') ?></th>
                                                <th><?= __('Privileged Quota Assignment') ?></th>
                                            </tr>
                                            <?php foreach ($summary as $sk => $sv): ?>
                                                <tr>
                                                    <td><?= h($sk) ?></td>
                                                    <td><?= h($sv['C']) ?></td>
                                                    <td><?= h($sv['Q']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                        <?php $count = 0; ?>
                                        <?php foreach ($autoPlacedStudents as $key => $data): ?>
                                            <table class="table table-bordered table-striped">
                                                <tr>
                                                    <td colspan="11" class="h3"><?= h($key) ?></td>
                                                </tr>
                                                <tr>
                                                    <th><?= __('Full Name') ?></th>
                                                    <th><?= __('Sex') ?></th>
                                                    <th><?= __('Student Number') ?></th>
                                                    <th><?= __('Assignment Type') ?></th>
                                                    <th><?= __('EHEECE Total Result') ?></th>
                                                    <th><?= __('Preference Order') ?></th>
                                                    <th><?= __('Department') ?></th>
                                                    <th><?= __('Academic Year') ?></th>
                                                    <th><?= __('Department Approval') ?></th>
                                                    <th><?= __('Placement Type') ?></th>
                                                    <th><?= __('Placement Based') ?></th>
                                                </tr>
                                                <?php $i = 0; ?>
                                                <?php foreach ($data as $acceptedStudent): ?>
                                                    <tr class="<?= $i++ % 2 == 0 ? 'altrow' : '' ?>">
                                                        <?= $this->Form->hidden("AcceptedStudent.{$count}.id", ['value' => $acceptedStudent->id]) ?>
                                                        <td><?= h($acceptedStudent->full_name) ?></td>
                                                        <td><?= h($acceptedStudent->sex) ?></td>
                                                        <td><?= h($acceptedStudent->studentnumber) ?></td>
                                                        <td><?= h($acceptedStudent->assignment_type) ?></td>
                                                        <td><?= h($acceptedStudent->EHEECE_total_results) ?></td>
                                                        <td>
                                                            <?php if (!empty($acceptedStudent->Preferences)): ?>
                                                                <?php foreach ($acceptedStudent->Preferences as $preference): ?>
                                                                    <?php if ($preference->department_id == $acceptedStudent->Department->id): ?>
                                                                        <?= h($preference->preferences_order) ?>
                                                                        <?php break; ?>
                                                                    <?php endif; ?>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= h($acceptedStudent->Department->name) ?></td>
                                                        <td><?= h($acceptedStudent->academic_year) ?></td>
                                                        <td><?= isset($acceptedStudent->approval) ? __('Approved By Department') : __('Not Approved By Department') ?></td>
                                                        <td><?= h($acceptedStudent->placementtype) ?></td>
                                                        <td><?= $acceptedStudent->placement_based == 'C' ? __('Competitive') : __('Quota') ?></td>
                                                    </tr>
                                                    <?php $count++; ?>
                                                <?php endforeach; ?>
                                            </table>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
