<?php
use Cake\I18n\I18n;

$this->set('title', __('Placement Report View'));
$this->Html->script(['jquery-1.6.2.min'], ['block' => 'script']);
?>

<script type="text/javascript">
    $(document).ready(function() {
        function updateCourseListOnChangeofOtherField() {
            var academicYear = $("#academic-year").val().replace("/", "-");
            if (!academicYear) {
                return false;
            }

            $("#placement-result-criteria").prop('disabled', true);
            $("#department-id").prop('disabled', true);
            $("#search").prop('disabled', true);

            var formUrlDept = '<?= $this->Url->build(['controller' => 'ParticipatingDepartments', 'action' => 'getParticipatingDepartment', '_ext' => 'json']) ?>/' + academicYear;
            $.ajax({
                type: 'GET',
                url: formUrlDept,
                data: { academic_year: academicYear },
                dataType: 'json',
                success: function(data, textStatus, xhr) {
                    $("#academic-year").prop('disabled', false);
                    $("#department-id").prop('disabled', false);
                    $("#search").prop('disabled', false);
                    $("#department-id").empty();
                    $("#department-id").append('<option><?= __('--select dept--') ?></option>');
                    $.each(data, function(key, value) {
                        $("#department-id").append('<option value="' + key + '">' + value + '</option>');
                    });
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });

            var formUrlCriteria = '<?= $this->Url->build(['controller' => 'PlacementsResultsCriterias', 'action' => 'getPlacementResultCriteria', '_ext' => 'json']) ?>/' + academicYear;
            $.ajax({
                type: 'GET',
                url: formUrlCriteria,
                data: { academic_year: academicYear },
                dataType: 'json',
                success: function(data, textStatus, xhr) {
                    $("#academic-year").prop('disabled', false);
                    $("#placement-result-criteria").prop('disabled', false);
                    $("#placement-result-criteria").empty();
                    $.each(data, function(key, value) {
                        $("#placement-result-criteria").append('<option value="' + key + '">' + value + '</option>');
                    });
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });

            return false;
        }

        $("#academic-year").on('change', updateCourseListOnChangeofOtherField);
    });
</script>

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="course-registrations-index">
                        <h2><?= __('Placement Report View') ?></h2>
                        <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'autoReport'], 'class' => 'form-horizontal']) ?>
                        <table class="table">
                            <tr>
                                <td style="width: 15%;"><?= __('Academic Year') ?>:</td>
                                <td style="width: 20%;">
                                    <?= $this->Form->control('Search.academic_year', [
                                        'options' => $academicYearList,
                                        'empty' => __('--select academic year--'),
                                        'required' => true,
                                        'id' => 'academic-year',
                                        'label' => false,
                                        'class' => 'form-control'
                                    ]) ?>
                                </td>
                                <td style="width: 15%;"><?= __('Department') ?>:</td>
                                <td style="width: 50%;">
                                    <?= $this->Form->control('Search.department_id', [
                                        'required' => true,
                                        'empty' => __('--select dept--'),
                                        'id' => 'department-id',
                                        'label' => false,
                                        'class' => 'form-control'
                                    ]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 15%;"><?= __('Result Criteria') ?>:</td>
                                <td style="width: 20%;">
                                    <?= $this->Form->control('Search.result_criteria_id', [
                                        'id' => 'placement-result-criteria',
                                        'label' => false,
                                        'class' => 'form-control'
                                    ]) ?>
                                </td>
                                <td style="width: 15%;"><?= __('Gender') ?>:</td>
                                <td style="width: 20%;">
                                    <?= $this->Form->control('Search.sex', [
                                        'options' => ['all' => __('All'), 'male' => __('Male'), 'female' => __('Female')],
                                        'label' => false,
                                        'class' => 'form-control'
                                    ]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 15%;"><?= __('Placement Based') ?>:</td>
                                <td style="width: 20%;">
                                    <?= $this->Form->control('Search.placement_based', [
                                        'options' => ['all' => __('All'), 'C' => __('Competitive'), 'Q' => __('Quota')],
                                        'label' => false,
                                        'class' => 'form-control'
                                    ]) ?>
                                </td>
                                <td style="width: 15%;"><?= __('Assigned') ?>:</td>
                                <td style="width: 20%;">
                                    <?= $this->Form->control('Search.placementtype', [
                                        'options' => [
                                            'all' => __('All'),
                                            'AUTO PLACED' => __('AUTO PLACED'),
                                            'DIRECT PLACED' => __('DIRECT PLACED'),
                                            'REGISTRAR PLACED' => __('REGISTRAR PLACED'),
                                            'CANCELLED PLACEMENT' => __('CANCELLED PLACEMENT')
                                        ],
                                        'label' => false,
                                        'class' => 'form-control'
                                    ]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <div class="form-group">
                                        <?= $this->Form->button(__('Search'), ['name' => 'search', 'id' => 'search', 'class' => 'btn btn-primary']) ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <?php if (!empty($autoPlacedStudents)): ?>
                            <?php $summary = $autoPlacedStudents['auto_summary']; ?>
                            <?php unset($autoPlacedStudents['auto_summary']); ?>
                            <table>
                                <tbody>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <?= $this->Form->button(__('Generate PDF'), ['name' => 'generatePlacedList', 'id' => 'generate-placed-list', 'class' => 'btn btn-primary']) ?>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered table-striped">
                                <tbody>
                                <tr>
                                    <th colspan="5"><?= __('Summary of Auto Placement') ?></th>
                                </tr>
                                <tr>
                                    <th><?= __('Department') ?></th>
                                    <th><?= __('Competitive') ?></th>
                                    <th><?= __('Privileged Quota') ?></th>
                                    <th><?= __('Female By Quota') ?></th>
                                    <th><?= __('Female By Competition') ?></th>
                                </tr>
                                <?php foreach ($summary as $sk => $sv): ?>
                                    <tr>
                                        <td><?= h($sk) ?></td>
                                        <td><?= h($sv['C']) ?></td>
                                        <td><?= h($sv['Q']) ?></td>
                                        <td><?= h($sv['QF']) ?></td>
                                        <td><?= h($sv['CF']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php foreach ($autoPlacedStudents as $key => $data): ?>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <td colspan="12" class="h3"><?= h($key) ?></td>
                                    </tr>
                                    <tr>
                                        <th><?= __('S.No') ?></th>
                                        <th><?= __('Full Name') ?></th>
                                        <th><?= __('Sex') ?></th>
                                        <th><?= __('Student Number') ?></th>
                                        <th><?= __('EHEECE') ?></th>
                                        <th><?= __('Pre Result') ?></th>
                                        <th><?= __('Preference Order') ?></th>
                                        <th><?= __('Department') ?></th>
                                        <th><?= __('Academic Year') ?></th>
                                        <th><?= __('Department Approval') ?></th>
                                        <th><?= __('Placement Type') ?></th>
                                        <th><?= __('Placement Based') ?></th>
                                    </tr>
                                    <?php $count = 1; $i = 0; ?>
                                    <?php foreach ($data as $acceptedStudent): ?>
                                        <tr class="<?= $i++ % 2 == 0 ? 'altrow' : '' ?>">
                                            <td><?= $count++ ?></td>
                                            <td><?= h($acceptedStudent->full_name) ?></td>
                                            <td><?= h($acceptedStudent->sex) ?></td>
                                            <td><?= h($acceptedStudent->studentnumber) ?></td>
                                            <td><?= h($acceptedStudent->EHEECE_total_results) ?></td>
                                            <td><?= h($acceptedStudent->freshman_result) ?></td>
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
                                            <td><?= isset($acceptedStudent->approval) ? __('Yes') : __('No') ?></td>
                                            <td><?= h($acceptedStudent->placementtype) ?></td>
                                            <td><?= $acceptedStudent->placement_based == 'C' ? __('Competitive') : __('Quota') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
