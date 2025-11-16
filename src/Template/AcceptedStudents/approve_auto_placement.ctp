<?php
use Cake\I18n\I18n;

$this->set('title', __('Approve Auto Placement'));
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
                    <div class="accepted-students-form">
                        <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'approveAutoPlacement'], 'class' => 'form-horizontal']) ?>
                        <table>
                            <tbody>
                            <tr>
                                <td width="100%">
                                    <?php if (empty($autoApprove)): ?>
                                        <h2><?= __('Please select the academic year to accept auto placed students.') ?></h2>
                                        <div class="form-group">
                                            <label class="control-label"><?= __('Academic Year') ?></label>
                                            <?= $this->Form->control('AcceptedStudent.academic_year', [
                                                'id' => 'academic-year',
                                                'type' => 'select',
                                                'options' => $academicYearList,
                                                'empty' => __('--Select Academic Year--'),
                                                'label' => false,
                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                        <div class="form-group">
                                            <?= $this->Form->button(__('Continue'), ['type' => 'submit', 'name' => 'searchbutton', 'class' => 'btn btn-primary']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <?php if (!empty($autoPlacedStudents)): ?>
                                        <?= $this->Form->hidden('AcceptedStudent.academic_year', ['value' => $selectedAcademicYear]) ?>
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th colspan="12">
                                                    <h2><?= __('List of students placed to {0} by the college with minute number {1} and who are not attached to the curriculum by the department.', h($departmentName), h($minuteNumber)) ?></h2>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th><?= __('No.') ?></th>
                                                <th>
                                                    <?= __('Select/ Unselect All') ?><br/>
                                                    <?= $this->Form->checkbox('SelectAll', ['id' => 'select-all']) ?>
                                                </th>
                                                <th><?= __('Full Name') ?></th>
                                                <th><?= __('Sex') ?></th>
                                                <th><?= __('Student Number') ?></th>
                                                <th><?= __('EHEECE Total Result') ?></th>
                                                <th><?= __('Preference Order') ?></th>
                                                <th><?= __('Department') ?></th>
                                                <th><?= __('Academic Year') ?></th>
                                                <th><?= __('Department Approval') ?></th>
                                                <th><?= __('Placement Type') ?></th>
                                                <th><?= __('Placement Based') ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $serialNumber = 1; $count = 0; ?>
                                            <?php foreach ($autoPlacedStudents as $index => $acceptedStudent): ?>
                                                <tr class="<?= $index % 2 == 0 ? 'altrow' : '' ?>">
                                                    <td><?= $serialNumber++ ?></td>
                                                    <td>
                                                        <?= $this->Form->checkbox("AcceptedStudent.approve.{$acceptedStudent->id}", ['class' => 'checkbox1']) ?>
                                                        <?= $this->Form->hidden("AcceptedStudent.{$count}.id", ['value' => $acceptedStudent->id]) ?>
                                                    </td>
                                                    <td><?= h($acceptedStudent->full_name) ?></td>
                                                    <td><?= h($acceptedStudent->sex) ?></td>
                                                    <td><?= h($acceptedStudent->studentnumber) ?></td>
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
                                                    <td><?= $acceptedStudent->placement_approved_by_department == 1 ? __('Approved By Department') : __('Not Approved By Department') ?></td>
                                                    <td><?= h($acceptedStudent->placementtype) ?></td>
                                                    <td><?= $acceptedStudent->placement_based == 'C' ? __('Competitive') : __('Quota') ?></td>
                                                </tr>
                                                <?php $count++; ?>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                        <?php if (empty($turnOffApproveButton)): ?>
                                            <table>
                                                <tr>
                                                    <td colspan="2" class="text-muted fs-5">
                                                        <?= __('Select the curriculum you want to attach the selected students') ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 13%;"><?= __('Curriculum') ?>:</td>
                                                    <td style="width: 37%;">
                                                        <?= $this->Form->control('AcceptedStudent.curriculum_id', ['label' => false, 'class' => 'form-control']) ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <?= $this->Form->button(__('Attach Selected Student'), ['type' => 'submit', 'name' => 'approve', 'class' => 'btn btn-primary']) ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        <?php endif; ?>
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
