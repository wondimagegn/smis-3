<?php

use Cake\Core\Configure;

?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-check-outline" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Batch Regenerate Student Academic Status') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create('StudentStatusPattern') ?>
                <div style="margin-top: -30px;">
                    <hr>
                    <div class="examGrades <?= $this->request->getParam('action') ?>">
                        <div onclick="toggleViewFullId('ListSection')">
                            <?php if (!empty($sections)): ?>
                                <?= $this->Html->image('plus2.gif', ['id' => 'ListSectionImg']) ?>
                                <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Display Filter</span>
                            <?php else: ?>
                                <?= $this->Html->image('minus2.gif', ['id' => 'ListSectionImg']) ?>
                                <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Hide Filter</span>
                            <?php endif; ?>
                        </div>
                        <div id="ListSection" style="display:<?= (!empty($sections) ? 'none' : 'block') ?>">
                            <fieldset style="padding-bottom: 5px;padding-top: 5px;">
                                <legend>&nbsp;&nbsp; Search / Filter &nbsp;&nbsp;</legend>
                                <div class="row">
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('StudentStatusPatterns.academic_year', [
                                            'id' => 'AcademicYear',
                                            'label' => __('Admission Year:'),
                                            'class' => 'fs14',
                                            'style' => 'width:90%;',
                                            'options' => $acyearArrayData,
                                            'default' => isset($academicYearSelected) ? $academicYearSelected : $defaultAcademicYear
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('StudentStatusPatterns.year_level_id', [
                                            'id' => 'YearLevelId',
                                            'label' => __('Year Level'),
                                            'class' => 'fs14',
                                            'style' => 'width:90%;',
                                            'options' => $yearLevels
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('StudentStatusPatterns.program_id', [
                                            'id' => 'Program',
                                            'class' => 'fs14',
                                            'label' => __('Program:'),
                                            'style' => 'width:90%;',
                                            'options' => $programs,
                                            'default' => isset($programId) ? $programId : false
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('StudentStatusPatterns.program_type_id', [
                                            'id' => 'ProgramType',
                                            'class' => 'fs14',
                                            'label' => __('Program Type:'),
                                            'style' => 'width:90%;',
                                            'options' => $programTypes,
                                            'default' => isset($programTypeId) ? $programTypeId : false
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('StudentStatusPatterns.status_academic_year', [
                                            'id' => 'StatusAcademicYear',
                                            'label' => __('Status Academic Year:'),
                                            'class' => 'fs14',
                                            'style' => 'width:90%;',
                                            'options' => $acyearArrayData,
                                            'default' => isset($academicYearSelected) ? $academicYearSelected : $defaultAcademicYear
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('StudentStatusPatterns.semester', [
                                            'label' => __('Status Semester:'),
                                            'class' => 'fs14',
                                            'style' => 'width:90%;',
                                            'options' => Configure::read('semesters')
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('StudentStatusPatterns.name', [
                                            'id' => 'name',
                                            'class' => 'fs14',
                                            'style' => 'width:90%;',
                                            'label' => __('Name:')
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="row">
                                    <?php if ((!empty($departments[0]) && $departments[0] != 0) || !empty($departmentIds)): ?>
                                        <div class="large-6 columns">
                                            <?= $this->Form->control('StudentStatusPatterns.department_id', [
                                                'class' => 'fs14',
                                                'label' => __('Department:'),
                                                'style' => 'width:90%;',
                                                'options' => $departments,
                                                'default' => isset($departmentId) ? $departmentId : false
                                            ]) ?>
                                        </div>
                                    <?php elseif (!empty($colleges) || !empty($collegeIds)): ?>
                                        <div class="large-6 columns">
                                            <?= $this->Form->control('StudentStatusPatterns.college_id', [
                                                'class' => 'fs14',
                                                'label' => __('College/Institute/School:'),
                                                'style' => 'width:90%;',
                                                'options' => $colleges,
                                                'default' => isset($collegeId) ? $collegeId : false
                                            ]) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <hr>
                                <?= $this->Form->submit(__('List Students'), [
                                    'name' => 'listSections',
                                    'class' => 'tiny radius button bg-blue',
                                    'div' => false,
                                    'id' => 'listSections'
                                ]) ?>
                            </fieldset>
                            <br>
                        </div>
                        <hr>
                        <?php if (!empty($sections)): ?>
                            <table cellspacing="0" cellpadding="0" class="table">
                                <tr>
                                    <td class="center">
                                        <div class="row">
                                            <div class="large-2 columns" style="margin-top: 10px;">
                                                <br>
                                                <span style="padding-left: 25px;">Section:</span>
                                            </div>
                                            <div class="large-8 columns">
                                                <br>
                                                <?= $this->Form->control('section_id', [
                                                    'class' => 'fs14',
                                                    'id' => 'Section',
                                                    'label' => false,
                                                    'style' => 'width:90%;',
                                                    'empty' => __('[ Select Section ]'),
                                                    'options' => $sections,
                                                    'default' => isset($sectionId) && !empty($sectionId) && $sectionId != 0 ? $sectionId : 0
                                                ]) ?>
                                            </div>
                                            <div class="large-2 columns">
                                                &nbsp;
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <hr>
                        <?php endif; ?>
                        <?php if (isset($studentsInSection) && empty($studentsInSection) && !empty($sectionId)): ?>
                            <div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                <span style="margin-right: 15px;"></span>There is no student in the selected section.
                            </div>
                        <?php elseif (isset($studentsInSection) && !empty($studentsInSection)): ?>
                            <h6 class="fs14 text-gray"><?= __('Please select student/s for whom you want to regenerate academic status.') ?></h6>
                            <br>
                            <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                            <div style="overflow-x:auto;">
                                <table cellpadding="0" cellspacing="0" class="table">
                                    <thead>
                                    <tr>
                                        <td style="width: 4%;" class="center"><?= $this->Form->checkbox('select_all', ['id' => 'select-all']) ?></td>
                                        <td style="width: 3%;" class="center">#</td>
                                        <td style="width: 30%;" class="vcenter"><?= __('Student Name') ?></td>
                                        <td style="width: 10%;" class="center"><?= __('Sex') ?></td>
                                        <td style="width: 15%;" class="center"><?= __('Student ID') ?></td>
                                        <td style="width: 38%;" class="center"><?= __('Last Generated') ?></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $stCount = 0;
                                    foreach ($studentsInSection as $key => $student): $stCount++;

                                    ?>
                                        <tr>
                                            <td class="center">
                                                <div style="margin-left: 15%;">
                                                    <?= $this->Form->checkbox("StudentStatusPattern.$stCount.gp", [
                                                        'class' => 'checkbox1',
                                                        'id' => "StudentSelection$stCount"
                                                    ]) ?>
                                                    <?= $this->Form->hidden("StudentStatusPattern.$stCount.student_id",
                                                        ['value' => $student->student->id]) ?>
                                                </div>
                                            </td>
                                            <td class="center"><?= $stCount ?></td>
                                            <td class="vcenter"><?= h($student->student->full_name) ?></td>
                                            <td class="center"><?= (strcasecmp(trim($student->student->gender), 'male') == 0 ? 'M' : (strcasecmp(trim($student->student->gender), 'female') == 0 ? 'F' : $student->student->gender)) ?></td>
                                            <td class="center"><?= h($student->student->studentnumber) ?></td>
                                            <td class="center">
                                                <?php
                                               // debug($student->student->student_exam_statuses);
                                                ?>
                                                <?= (!empty($student->student->student_exam_statuses) && $student->student->student_exam_statuses[0]
                                                && $student->student->student_exam_statuses[0]->modified ?
                                                    h($student->student->student_exam_statuses[0]->modified->format('M j, Y')) .
                                                    ' (' . h($student->student->student_exam_statuses[0]->academic_year) . ' - ' .
                                                    h($student->student->student_exam_statuses[0]->semester) . ')' : '') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <hr>
                            <?= $this->Form->submit(__('Regenerate Student Status'), [
                                'name' => 'regenerateStatus',
                                'id' => 'regenerateStatus',
                                'div' => false,
                                'class' => 'tiny radius button bg-blue'
                            ]) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle view for filter
    function toggleViewFullId(id) {
        if ($('#' + id).css("display") == 'none') {
            $('#' + id + 'Img').attr("src", '/img/minus2.gif');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append('Hide Filter');
        } else {
            $('#' + id + 'Img').attr("src", '/img/plus2.gif');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append('Display Filter');
        }
        $('#' + id).toggle("slow");
    }

    // Section change
    $("#Section").change(function() {
        var s_id = $("#Section").val();
        if (s_id != '') {
            window.location.replace("/studentStatusPatterns/<?= $this->request->getParam('action') ?>/" + s_id);
        }
    });

    var numberOfStudents = <?= (isset($studentsInSection) ? count($studentsInSection) : 0) ?>;
    function checkUncheck(id) {
        var checked = ($('#' + id).attr("checked") == 'checked' ? true : false);
        for (i = 1; i <= numberOfStudents; i++) {
            $('#StudentSelection' + i).attr("checked", checked);
        }
    }

    var formBeingSubmitted = false;
    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');
    $('#regenerateStatus').click(function() {
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
        var checkedCount = Array.prototype.slice.call(checkboxes).filter(x => x.checked).length;

        if (!checkedOne) {
            alert('At least one student must be selected to regenerate status.');
            validationMessageNonSelected.innerHTML = 'At least one student must be selected to regenerate status.';
            return false;
        }

        if (formBeingSubmitted) {
            alert("Regenerating status for the selected (" + checkedCount + ") students , please wait a moment...");
            $('#regenerateStatus').attr('disabled', true);
            return false;
        }

        $('#regenerateStatus').val('Regenerating Student Status...');
        $('#regenerateStatus').attr('disabled', false);
        formBeingSubmitted = true;

        return confirm("Are you sure you want to regenerate status for the selected (" + checkedCount + ") students?");
    });
</script>
