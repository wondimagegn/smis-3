<?php
use Cake\I18n\I18n;

$this->set('title', __('Detach Students from a Curriculum'));
$this->Html->script(['jquery-1.6.2.min'], ['block' => 'script']);
?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#select-all").click(function() {
            $(".checkbox1").prop('checked', $(this).prop('checked'));
        });

        $("#detach-curriculum").click(function() {
            var isValid = true;
            var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="data[AcceptedStudent][approve]"]');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

            if (!checkedOne) {
                alert('<?= __('At least one student must be selected to detach from attached curriculum.') ?>');
                $("#validation-message-non-selected").text('<?= __('At least one student must be selected to detach from attached curriculum.') ?>');
                isValid = false;
                return false;
            }

            if (form_being_submitted) {
                alert('<?= __('Detaching selected students from attached curriculum. Please wait a moment...') ?>');
                $("#detach-curriculum").prop('disabled', true);
                isValid = false;
                return false;
            }

            if (!form_being_submitted && isValid) {
                $("#detach-curriculum").val('<?= __('Detaching Selected Students...') ?>');
                form_being_submitted = true;
                return true;
            }

            return false;
        });

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    });

    function toggleViewFullId(id) {
        var display = $("#" + id).css("display") === 'none';
        $("#" + id + "-img").attr("src", display ? '<?= $this->Url->image('minus2.gif') ?>' : '<?= $this->Url->image('plus2.gif') ?>');
        $("#" + id + "-txt").text(display ? '<?= __('Hide Filter') ?>' : '<?= __('Display Filter') ?>');
        $("#" + id).toggle("slow");
    }
</script>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-paperclip" style="font-size: larger; font-weight: bold;"></i>
                <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Detach Students from a Curriculum') ?></span>
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'detachCurriculum'], 'class' => 'form-horizontal']) ?>
                    <div style="margin-top: -20px;">
                        <?php if (empty($autoApprove)): ?>
                            <hr>
                            <blockquote>
                                <h6><i class="fas fa-info-circle"></i> <?= __('Important Note:') ?></h6>
                                <span class="text-dark fs-5" style="text-align: justify;">
                                    <?= __('This tool will help you to detach students from curriculums in your department.') ?><br>
                                    <i class="text-warning"><?= __('You are advised to detach any student from an attached curriculum if the student has no other options to continue their study using the current attached curriculum due to various reasons or to pursue a specialization or if the attached curriculum is not correct.') ?></i>
                                </span>
                            </blockquote>
                            <hr>
                            <div onclick="toggleViewFullId('list-published-course')">
                                <?php if (!empty($autoPlacedStudents)): ?>
                                    <?= $this->Html->image('plus2.gif', ['id' => 'list-published-course-img']) ?>
                                    <span style="font-size: 10px; vertical-align: top; font-weight: bold;" id="list-published-course-txt"><?= __('Display Filter') ?></span>
                                <?php else: ?>
                                    <?= $this->Html->image('minus2.gif', ['id' => 'list-published-course-img']) ?>
                                    <span style="font-size: 10px; vertical-align: top; font-weight: bold;" id="list-published-course-txt"><?= __('Hide Filter') ?></span>
                                <?php endif; ?>
                            </div>
                            <div id="list-published-course" style="display: <?= empty($autoApprove) ? 'block' : 'none' ?>;">
                                <fieldset style="padding-bottom: 0; padding-top: 15px;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <?= $this->Form->control('AcceptedStudent.academic_year', [
                                                'label' => ['text' => __('Admission Year'), 'class' => 'control-label'],
                                                'style' => 'width: 90%',
                                                'options' => $academicYearList,
                                                'default' => $selectedAcademicYear ?? $defaultAcademicYear,
                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?= $this->Form->control('AcceptedStudent.program_id', [
                                                'label' => ['text' => __('Program'), 'class' => 'control-label'],
                                                'style' => 'width: 90%',
                                                'options' => $programs,
                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?= $this->Form->control('AcceptedStudent.program_type_id', [
                                                'label' => ['text' => __('Program Type'), 'class' => 'control-label'],
                                                'style' => 'width: 90%',
                                                'options' => $programTypes,
                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <?= $this->Form->control('AcceptedStudent.name', [
                                                'label' => ['text' => __('Student Name or ID'), 'class' => 'control-label'],
                                                'placeholder' => __('Student Name or ID...'),
                                                'default' => '',
                                                'style' => 'width: 90%',
                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?= $this->Form->control('AcceptedStudent.limit', [
                                                'id' => 'limit',
                                                'type' => 'number',
                                                'min' => 100,
                                                'max' => 1000,
                                                'value' => $limit,
                                                'step' => 100,
                                                'label' => ['text' => __('Limit'), 'class' => 'control-label'],
                                                'style' => 'width: 90%',
                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <?= $this->Form->button(__('Search'), ['type' => 'submit', 'name' => 'searchbutton', 'class' => 'btn btn-primary']) ?>
                                    </div>
                                </fieldset>
                            </div>
                            <hr>
                        <?php endif; ?>
                        <?php if (!empty($autoPlacedStudents)): ?>
                            <hr>
                            <blockquote>
                                <h6><i class="fas fa-info-circle"></i> <?= __('Important Note:') ?></h6>
                                <span class="text-dark fs-5" style="text-align: justify; font-weight: bold;">
                                    <?= __('Detaching a given student from a curriculum is only necessary if:') ?>
                                    <ol style="padding-left: 20px;">
                                        <li><?= __('The student is attached to another curriculum previously by mistake.') ?></li>
                                        <li><?= __('The student is transferred to another department.') ?></li>
                                        <li><?= __('The student has not taken any course from the attached curriculum.') ?></li>
                                        <li><?= __('The student is moved to a specialization that requires a different curriculum.') ?></li>
                                        <li><?= __('All taken courses of the student need to be substituted from the old curriculum to a new one.') ?></li>
                                    </ol>
                                    <b class="text-danger"><i><?= __('Changing curriculum without considering these may lead to complications in graduation.') ?></i></b>
                                </span>
                            </blockquote>
                            <hr>
                            <h6 class="text-muted fs-5"><?= __('List of students admitted in {0} academic year and placed to {1} which are attached to a curriculum.', h($selectedAcademicYear), h($departmentName)) ?></h6>
                            <hr>
                            <h6 id="validation-message-non-selected" class="text-danger fs-5"></h6>
                            <br>
                            <div style="overflow-x: auto;">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <td class="text-center"><?= $this->Form->checkbox('SelectAll', ['id' => 'select-all']) ?></td>
                                        <td class="text-center"><?= __('#') ?></td>
                                        <td class="text-center"><?= __('Full Name') ?></td>
                                        <td class="text-center"><?= __('Sex') ?></td>
                                        <td class="text-center"><?= __('Student ID') ?></td>
                                        <td class="text-center"><?= __('EHEECE') ?></td>
                                        <td class="text-center"><?= __('Department') ?></td>
                                        <td class="text-center"><?= __('Admission Year') ?></td>
                                        <td class="text-center"><?= __('Department Approval') ?></td>
                                        <td class="text-center"><?= __('Placement Type') ?></td>
                                        <td class="text-center"><?= __('Curriculum') ?></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $count = 0; $serialNumber = 1; ?>
                                    <?php foreach ($autoPlacedStudents as $acceptedStudent): ?>
                                        <tr>
                                            <td class="text-center">
                                                <?= $this->Form->hidden("AcceptedStudent.{$count}.id", ['value' => $acceptedStudent->id]) ?>
                                                <div style="margin-left: 15%;">
                                                    <?= $this->Form->checkbox("AcceptedStudent.approve.{$acceptedStudent->id}", ['class' => 'checkbox1']) ?>
                                                </div>
                                            </td>
                                            <td class="text-center"><?= $serialNumber++ ?></td>
                                            <td class="align-middle"><?= h($acceptedStudent->full_name) ?></td>
                                            <td class="text-center"><?= h(strcasecmp(trim($acceptedStudent->sex), 'male') == 0 ? 'M' : (strcasecmp(trim($acceptedStudent->sex), 'female') == 0 ? 'F' : trim($acceptedStudent->sex))) ?></td>
                                            <td class="text-center"><?= h($acceptedStudent->studentnumber) ?></td>
                                            <td class="text-center"><?= h($acceptedStudent->EHEECE_total_results ?? '') ?></td>
                                            <td class="text-center"><?= h($acceptedStudent->Department->name) ?></td>
                                            <td class="text-center"><?= h($acceptedStudent->academic_year) ?></td>
                                            <td class="text-center">
                                                <?= $acceptedStudent->placement_approved_by_department == 1 ? '<span class="text-success">' . __('Yes') . '</span>' : '<span class="text-warning">' . __('No') . '</span>' ?>
                                            </td>
                                            <td class="text-center"><?= h(ucwords(strtolower($acceptedStudent->placementtype ?? ''))) ?></td>
                                            <td class="text-center"><?= h($this->Text->truncate($acceptedStudent->Curriculum->curriculum_detail, 25, ['ellipsis' => '...', 'exact' => true, 'html' => true])) ?></td>
                                        </tr>
                                        <?php $count++; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <hr>
                            <div class="form-group">
                                <?= $this->Form->button(__('Detach Selected'), ['id' => 'detach-curriculum', 'name' => 'deaattach', 'class' => 'btn btn-primary']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
