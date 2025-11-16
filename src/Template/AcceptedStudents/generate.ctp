<?php
use Cake\I18n\I18n;

$this->set('title', __('Generate Student ID Number'));
$this->Html->script(['jquery-1.6.2.min'], ['block' => 'script']);
?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#select-all").click(function() {
            $(".checkbox1").prop('checked', $(this).prop('checked'));
        });

        $("#generate-student-id").click(function() {
            var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="data[AcceptedStudent][generate]"]');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
            var isValid = true;

            if (!checkedOne) {
                alert('<?= __('At least one student must be selected to generate Student ID.') ?>');
                $("#validation-message-non-selected").text('<?= __('At least one student must be selected to generate Student ID.') ?>');
                isValid = false;
                return false;
            }

            if (form_being_submitted) {
                alert('<?= __('Generating Student IDs, please wait a moment...') ?>');
                $("#generate-student-id").prop('disabled', true);
                isValid = false;
                return false;
            }

            if (!form_being_submitted && isValid) {
                $("#generate-student-id").val('<?= __('Generating Student IDs...') ?>');
                form_being_submitted = true;
                return true;
            }

            return false;
        });

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    });
</script>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-check" style="font-size: larger; font-weight: bold;"></i>
                <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Generate Student ID Number') ?></span>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div style="margin-top: -30px;"><hr></div>
                    <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'generate'], 'class' => 'form-horizontal']) ?>
                    <?php if (empty($showListGenerated)): ?>
                        <h2></h2>
                        <h6 class="text-muted fs-5"><?= __('Table: Summary of students that don’t have an Identification Number (Student ID)') ?></h6>
                        <br>
                        <div style="overflow-x: auto;">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                <tr>
                                    <?php $collegeCount = count($colleges); $programCount = count($programs); $programTypeCount = count($programTypes); ?>
                                    <?php debug($programTypeCount); debug($collegeCount); ?>
                                    <?php for ($i = 1; $i <= $collegeCount; $i++): ?>
                                    <td style="width: 50%;">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th class="align-middle" colspan="<?= $programCount + 1 ?>">
                                                    <h6 class="text-muted fs-5"><?= h($colleges[$i]) ?></h6>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th class="align-middle"><?= __('Program/Type') ?></th>
                                                <?php if (!empty($programs)): ?>
                                                    <?php foreach ($programs as $kp => $vp): ?>
                                                        <th class="text-center"><?= h(strcasecmp(trim($vp), 'Undergraduate') == 0 || strcasecmp(trim($vp), 'Under graduate') == 0 ? 'UG' : (strcasecmp(trim($vp), 'Postgraduate') == 0 || strcasecmp(trim($vp), 'Post graduate') == 0 ? 'PG' : $vp)) ?></th>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php for ($j = 1; $j <= $programTypeCount; $j++): ?>
                                                <?php if (isset($programTypes[$j])): ?>
                                                    <tr>
                                                        <td class="align-middle"><?= h($programTypes[$j]) ?></td>
                                                        <?php for ($k = 1; $k <= $programCount; $k++): ?>
                                                            <?php if (isset($programs[$k])): ?>
                                                                <td class="text-center">
                                                                    <?= ($data[$colleges[$i]][$programs[$k]][$programTypes[$j]] != 0 ? '<b>' . h($data[$colleges[$i]][$programs[$k]][$programTypes[$j]]) . '</b>' : '--') ?>
                                                                </td>
                                                            <?php endif; ?>
                                                        <?php endfor; ?>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                    <?php if (($i % 2) == 0): ?>
                                </tr><tr>
                                    <?php endif; ?>
                                    <?php endfor; ?>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <br>
                    <?php endif; ?>
                    <?php if (empty($showListGenerated)): ?>
                        <div>
                            <fieldset style="padding-bottom: 0; padding-top: 15px;">
                                <div class="row">
                                    <div class="col-md-3">
                                        <?= $this->Form->control('AcceptedStudent.academic_year', [
                                            'id' => 'academic-year',
                                            'style' => 'width: 90%;',
                                            'label' => ['text' => __('Academic Year'), 'class' => 'control-label'],
                                            'type' => 'select',
                                            'options' => $academicYearList,
                                            'value' => $selectedAcademicYear ?? '',
                                            'class' => 'form-control'
                                        ]) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= $this->Form->control('AcceptedStudent.program_id', [
                                            'style' => 'width: 90%;',
                                            'label' => ['text' => __('Program'), 'class' => 'control-label'],
                                            'class' => 'form-control'
                                        ]) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= $this->Form->control('AcceptedStudent.program_type_id', [
                                            'style' => 'width: 90%;',
                                            'label' => ['text' => __('Program Type'), 'class' => 'control-label'],
                                            'class' => 'form-control'
                                        ]) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= $this->Form->control('AcceptedStudent.limit', [
                                            'style' => 'width: 90%;',
                                            'label' => ['text' => __('Limit'), 'class' => 'control-label'],
                                            'type' => 'number',
                                            'min' => 100,
                                            'max' => 2000,
                                            'value' => $limit,
                                            'step' => 100,
                                            'class' => 'form-control'
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $this->Form->control('AcceptedStudent.college_id', [
                                            'style' => 'width: 95%;',
                                            'label' => ['text' => __('College'), 'class' => 'control-label'],
                                            'empty' => __('[ Select College ]'),
                                            'class' => 'form-control'
                                        ]) ?>
                                    </div>
                                    <div class="col-md-6"></div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <?= $this->Form->button(__('Search'), ['name' => 'search', 'class' => 'btn btn-primary']) ?>
                                </div>
                            </fieldset>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($acceptedStudents)): ?>
                        <h6 id="validation-message-non-selected" class="text-danger fs-5"></h6>
                        <br>
                        <div style="overflow-x: auto;">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <td class="text-center"><?= $this->Form->checkbox('select-all', ['id' => 'select-all']) ?></td>
                                    <td class="text-center"><?= __('#') ?></td>
                                    <td class="align-middle"><?= $this->Paginator->sort('full_name', __('Full Name')) ?></td>
                                    <td class="text-center"><?= $this->Paginator->sort('sex', __('Sex')) ?></td>
                                    <td class="text-center"><?= $this->Paginator->sort('studentnumber', __('Student ID')) ?></td>
                                    <td class="text-center"><?= $this->Paginator->sort('EHEECE_total_results', __('EHEECE Result')) ?></td>
                                    <td class="text-center"><?= $this->Paginator->sort('department_id', __('Department')) ?></td>
                                    <td class="text-center"><?= $this->Paginator->sort('program_type_id', __('Program Type')) ?></td>
                                    <td class="text-center"><?= $this->Paginator->sort('academic_year', __('ACY')) ?></td>
                                    <td class="text-center"><?= $this->Paginator->sort('Placement_Approved_By_Department', __('Department Approval')) ?></td>
                                    <td class="text-center"><?= $this->Paginator->sort('placementtype', __('Placement Type')) ?></td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $start = $this->Paginator->counter(['format' => '{{start}}']); ?>
                                <?php foreach ($acceptedStudents as $acceptedStudent): ?>
                                    <tr>
                                        <td class="text-center">
                                            <div style="margin-left: 15%;">
                                                <?= $this->Form->checkbox("AcceptedStudent.generate.{$acceptedStudent->id}", ['class' => 'checkbox1']) ?>
                                            </div>
                                        </td>
                                        <td class="text-center"><?= $start++ ?></td>
                                        <td class="align-middle"><?= h($acceptedStudent->full_name) ?></td>
                                        <td class="text-center"><?= h(strcasecmp(trim($acceptedStudent->sex), 'male') == 0 ? 'M' : (strcasecmp(trim($acceptedStudent->sex), 'female') == 0 ? 'F' : '')) ?></td>
                                        <td class="text-center"><?= h($acceptedStudent->studentnumber) ?></td>
                                        <td class="text-center"><?= h((int)$acceptedStudent->EHEECE_total_results) ?></td>
                                        <td class="text-center">
                                            <?= $this->Html->link(
                                                h($acceptedStudent->Department->name),
                                                ['controller' => 'Departments', 'action' => 'view', $acceptedStudent->Department->id]
                                            ) ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $this->Html->link(
                                                h($acceptedStudent->ProgramType->name),
                                                ['controller' => 'ProgramTypes', 'action' => 'view', $acceptedStudent->ProgramType->id]
                                            ) ?>
                                        </td>
                                        <td class="text-center"><?= h($acceptedStudent->academic_year) ?></td>
                                        <td class="text-center">
                                            <?= $acceptedStudent->placement_approved_by_department == 1 ? '<span class="text-success">' . __('Yes') . '</span>' : '' ?>
                                        </td>
                                        <td class="text-center"><?= h($acceptedStudent->placementtype) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="form-group">
                            <?= $this->Form->button(__('Generate Student IDs'), ['name' => 'generateid', 'id' => 'generate-student-id', 'class' => 'btn btn-primary']) ?>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-5">
                                <?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} records out of {{count}} total')]) ?>
                            </div>
                            <div class="col-md-7">
                                <div class="pagination">
                                    <?= $this->Paginator->prev('<< ' . __('previous')) ?>
                                    | <?= $this->Paginator->numbers() ?> |
                                    <?= $this->Paginator->next(__('next') . ' >>') ?>
                                </div>
                            </div>
                        </div>
                    <?php elseif (empty($acceptedStudents) && !$isBeforeSearch): ?>
                        <div class="alert alert-info">
                            <span style="margin-right: 15px;"></span>
                            <?= __('No accepted students without student identification in these selected criteria') ?>
                        </div>
                    <?php endif; ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
