<?php
use Cake\I18n\I18n;

$this->set('title', __('Auto Student Placement to Departments'));
?>

<script type="text/javascript">
    $(document).ready(function() {
        var image = new Image();
        image.src = '<?= $this->Url->image('busy.gif') ?>';

        function getPlacementSummary() {
            var summary = $("#academic-year").val();
            $("#academic-year").prop('disabled', true);
            $("#run-auto-placement-button").prop('disabled', true);
            $("#summary-student-result-category").html('<img src="<?= $this->Url->image('busy.gif') ?>" class="img-fluid mx-auto d-block" />');

            var formUrl = '<?= $this->Url->build(['controller' => 'ReservedPlaces', 'action' => 'getSummaries', '_ext' => 'json']) ?>/' + summary;

            $.ajax({
                type: 'GET',
                url: formUrl,
                data: { summary: summary },
                dataType: 'json',
                success: function(data, textStatus, xhr) {
                    $("#academic-year").prop('disabled', false);
                    $("#run-auto-placement-button").prop('disabled', false);
                    $("#summary-student-result-category").empty();
                    $("#summary-student-result-category").append(data);
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });
            return false;
        }

        $("#academic-year").on('change', getPlacementSummary);
    });
</script>

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="accepted-students-form">
                        <h2><?= __('Auto Student Placement to Departments') ?></h2>
                        <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'autoPlacement'], 'class' => 'form-horizontal']) ?>
                        <table>
                            <tbody>
                            <tr>
                                <td width="10%">
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <?php if (empty($autoAlreadyRun)): ?>
                                                    <div class="text-muted"><?= __('Academic Year') ?></div>
                                                    <?= $this->Form->control('AcceptedStudent.academic_year', [
                                                        'id' => 'academic-year',
                                                        'label' => false,
                                                        'type' => 'select',
                                                        'options' => $academicYearList,
                                                        'empty' => __('--Select Academic Year--'),
                                                        'class' => 'form-control'
                                                    ]) ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td width="90%" id="summary-student-result-category"></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <?php if (empty($autoAlreadyRun)): ?>
                                        <div class="form-group">
                                            <?= $this->Form->button(__('Run Auto Placement'), [
                                                'type' => 'submit',
                                                'name' => 'runautoplacement',
                                                'id' => 'run-auto-placement-button',
                                                'class' => 'btn btn-primary'
                                            ]) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <?php if (!empty($autoPlacedStudents)): ?>
                                        <?php $summary = $autoPlacedStudents['auto_summary']; ?>
                                        <table>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <?= $this->Html->link(
                                                        $this->Html->image('pdf_icon.gif', ['alt' => __('Print to PDF')]),
                                                        ['action' => 'printAutoPlacedPdf'],
                                                        ['escape' => false]
                                                    ) ?> <?= __('Print') ?>
                                                </td>
                                                <td>
                                                    <?= $this->Html->link(
                                                        $this->Html->image('xls-icon.gif', ['alt' => __('Export to XLS')]),
                                                        ['action' => 'exportAutoPlacedXls'],
                                                        ['escape' => false]
                                                    ) ?> <?= __('Export') ?>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
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
                                        <?php unset($autoPlacedStudents['auto_summary']); ?>
                                        <?php foreach ($autoPlacedStudents as $key => $data): ?>
                                            <table class="table table-bordered table-striped">
                                                <tr>
                                                    <td colspan="11" class="h2"><?= h($key) ?></td>
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
