<?php
use Cake\I18n\I18n;

$this->set('title', __('Export Student IDs'));
$this->Html->script(['jquery-1.6.2.min'], ['block' => 'script']);
?>

<script type="text/javascript">
    $(document).ready(function() {
        function toggleViewFullId(id) {
            var display = $("#" + id).css("display") === 'none';
            $("#" + id + "-img").attr("src", display ? '<?= $this->Url->image('minus2.gif') ?>' : '<?= $this->Url->image('plus2.gif') ?>');
            $("#" + id + "-txt").text(display ? '<?= __('Hide Filter') ?>' : '<?= __('Display Filter') ?>');
            $("#" + id).toggle("slow");
        }

        $("[onclick^='toggleViewFullId']").click(function() {
            toggleViewFullId('list-published-course');
        });
    });
</script>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-id-card" style="font-size: larger; font-weight: bold;"></i>
                <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Export Student IDs') ?></span>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'exportPrintStudentsNumber'], 'class' => 'form-horizontal']) ?>
                    <div style="margin-top: -20px;">
                        <?php if (empty($acceptedStudents) || $this->request->getSession()->check('search_data')): ?>
                            <hr>
                            <div onclick="toggleViewFullId('list-published-course')">
                                <?php if (!empty($acceptedStudents) || $this->request->getSession()->check('search_data')): ?>
                                    <?= $this->Html->image('plus2.gif', ['id' => 'list-published-course-img']) ?>
                                    <span style="font-size: 10px; vertical-align: top; font-weight: bold;" id="list-published-course-txt"><?= __('Display Filter') ?></span>
                                <?php else: ?>
                                    <?= $this->Html->image('minus2.gif', ['id' => 'list-published-course-img']) ?>
                                    <span style="font-size: 10px; vertical-align: top; font-weight: bold;" id="list-published-course-txt"><?= __('Hide Filter') ?></span>
                                <?php endif; ?>
                            </div>
                            <div id="list-published-course" style="display: <?= (!empty($acceptedStudents) || $this->request->getSession()->check('search_data')) ? 'none' : 'block' ?>;">
                                <fieldset style="padding-bottom: 5px; padding-top: 15px;">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <?= $this->Form->control('AcceptedStudent.academic_year', [
                                                'id' => 'academic-year',
                                                'style' => 'width: 90%;',
                                                'label' => ['text' => __('Admission Year'), 'class' => 'control-label'],
                                                'type' => 'select',
                                                'options' => $academicYearList,
                                                'empty' => __('[ Select Academic Year ]'),
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
                                            <?= $this->Form->control('AcceptedStudent.region_id', [
                                                'style' => 'width: 90%;',
                                                'label' => ['text' => __('Region'), 'class' => 'control-label'],
                                                'empty' => __('[ All Regions ]'),
                                                'required' => false,
                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php if (!empty($colleges)): ?>
                                                <?= $this->Form->control('AcceptedStudent.college_id', [
                                                    'style' => 'width: 90%;',
                                                    'label' => ['text' => __('College'), 'class' => 'control-label'],
                                                    'empty' => __('[ Select College ]'),
                                                    'class' => 'form-control'
                                                ]) ?>
                                            <?php elseif (!empty($departments)): ?>
                                                <?= $this->Form->control('AcceptedStudent.department_id', [
                                                    'style' => 'width: 90%;',
                                                    'label' => ['text' => __('Department'), 'class' => 'control-label'],
                                                    'empty' => __('[ Select Department ]'),
                                                    'class' => 'form-control'
                                                ]) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-3">
                                            <?= $this->Form->control('AcceptedStudent.admitted', [
                                                'label' => ['text' => __('Admitted'), 'class' => 'control-label'],
                                                'style' => 'width: 90%',
                                                'options' => ['0' => __('All'), '1' => __('No'), '2' => __('Yes')],
                                                'value' => '2',
                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                        <div class="col-md-3">
                                            <?= $this->Form->control('AcceptedStudent.limit', [
                                                'style' => 'width: 90%;',
                                                'label' => ['text' => __('Limit'), 'class' => 'control-label'],
                                                'type' => 'number',
                                                'min' => 100,
                                                'max' => 10000,
                                                'value' => $limit,
                                                'step' => 100,
                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <?= $this->Form->button(__('Search'), ['name' => 'search', 'class' => 'btn btn-primary']) ?>
                                    </div>
                                </fieldset>
                            </div>
                            <hr>
                        <?php endif; ?>
                        <?php if (!empty($acceptedStudents)): ?>
                            <hr>
                            <div style="overflow-x: auto;">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <td colspan="4" style="vertical-align: middle; border-bottom: 2px solid #555; line-height: 1.5;">
                                            <span style="font-size: 16px; font-weight: bold;"><?= h($selectedCollegeName) ?></span>
                                            <br style="line-height: 0.35;">
                                            <span class="text-muted" style="padding-top: 15px; font-size: 13px; font-weight: normal">
                                                    <?= __('Campus: {0}', h($selectedCampusName)) ?><br>
                                                    <?= __('Admission Year: {0}', h($selectedAcademicYear)) ?><br>
                                                    <?= __('Program: {0} / {1}', h($selectedProgramName), h($selectedProgramTypeName)) ?><br>
                                                </span>
                                        </td>
                                        <td colspan="3" style="text-align: right; vertical-align: middle; border-bottom: 2px solid #555;">
                                            <?= $this->Html->link(
                                                $this->Html->image('pdf_icon.gif', ['alt' => __('Print To PDF')]) . ' ' . __('PDF'),
                                                ['action' => 'printStudentsNumberPdf'],
                                                ['escape' => false]
                                            ) ?>
                                            &nbsp;&nbsp;
                                            <?= $this->Html->link(
                                                $this->Html->image('xls-icon.gif', ['alt' => __('Export To Excel')]) . ' ' . __('Excel'),
                                                ['action' => 'exportStudentsNumberXls'],
                                                ['escape' => false]
                                            ) ?>
                                            &nbsp;&nbsp;
                                            <?= $this->Html->link(
                                                $this->Html->image('csv_icon.png', ['alt' => __('Export To CSV')]) . ' ' . __('CSV'),
                                                ['action' => 'downloadCsv'],
                                                ['escape' => false]
                                            ) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><?= __('#') ?></td>
                                        <td class="align-middle"><?= $this->Paginator->sort('full_name', __('Full Name')) ?></td>
                                        <td class="text-center"><?= $this->Paginator->sort('sex', __('Sex')) ?></td>
                                        <td class="text-center"><?= $this->Paginator->sort('studentnumber', __('Student ID')) ?></td>
                                        <td class="text-center"><?= $this->Paginator->sort('department_id', __('Department')) ?></td>
                                        <td class="text-center"><?= $this->Paginator->sort('region_id', __('Region')) ?></td>
                                        <td class="text-center"><?= __('National ID') ?></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $count = $this->Paginator->counter(['format' => '{{start}}']); ?>
                                    <?php foreach ($acceptedStudents as $acceptedStudent): ?>
                                        <tr>
                                            <td class="text-center"><?= $count++ ?></td>
                                            <td class="align-middle"><?= h($acceptedStudent->full_name) ?></td>
                                            <td class="text-center"><?= h(strcasecmp(trim($acceptedStudent->sex), 'male') == 0 ? 'M' : (strcasecmp(trim($acceptedStudent->sex), 'female') == 0 ? 'F' : '')) ?></td>
                                            <td class="text-center"><?= h($acceptedStudent->studentnumber) ?></td>
                                            <td class="text-center"><?= h($acceptedStudent->Department->name ?? 'Pre/Freshman') ?></td>
                                            <td class="text-center"><?= h($acceptedStudent->Region->name) ?></td>
                                            <td class="text-center"><?= h($acceptedStudent->Student->student_national_id ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
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
                                <?= __('No accepted students found that are {0} and have student identification with the given criteria. If you have students in these criteria which are {1}, change Admitted Field to "All" or Generate Student IDs.',
                                    $this->request->getData('AcceptedStudent.admitted') == 2 ? __('admitted') : ($this->request->getData('AcceptedStudent.admitted') == 1 ? __('not admitted') : __('admitted or not admitted')),
                                    $this->request->getData('AcceptedStudent.admitted') == 2 ? __('admitted') : ($this->request->getData('AcceptedStudent.admitted') == 1 ? __('not admitted, Admit the students first or') : __('admitted or not admitted'))
                                ) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
