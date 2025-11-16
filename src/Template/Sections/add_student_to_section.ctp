<?php
$this->assign('title', __('Add Student to Section: %s', h($student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')')));
?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#year_level_id").change(function() {
            var formData = $(this).val();
            var acYrStart = $("#acYrStart").val();
            var studentId = "<?= h($student_detail['Student']['id']) ?>";
            if (formData !== '') {
                $("#SectionList").empty();
                $("#year_level_id").prop('disabled', true);
                $("#SectionAssignedSection").prop('disabled', true);
                $("#Add_To_Section_Button").prop('disabled', true);
                var formUrl = '<?= $this->Url->build(['controller' => 'Sections', 'action' => 'getSectionsByYearLevel']) ?>/' + encodeURIComponent(formData) + '/' + encodeURIComponent(studentId) + '/' + encodeURIComponent(acYrStart);
                $.ajax({
                    type: 'GET',
                    url: formUrl,
                    data: { year_level_id: formData },
                    success: function(data) {
                        $("#year_level_id").prop('disabled', false);
                        $("#SectionAssignedSection").prop('disabled', false);
                        $("#Add_To_Section_Button").prop('disabled', false);
                        $("#SectionList").empty().append(data);
                    },
                    error: function(xhr, textStatus) {
                        alert(textStatus);
                    }
                });
            } else {
                if ($('#Add_To_Section_Button').length) {
                    $("#year_level_id").prop('disabled', true);
                    $("#SectionAssignedSection").prop('disabled', true);
                    $("#Add_To_Section_Button").prop('disabled', true);
                }
            }
            return false;
        });
    });
</script>

<div class="box">
    <div class="box-header bg-transparent">
        <h3 class="box-title" style="margin-top: 10px;">
            <i class="fa fa-vcard" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Add Student to Section: %s', h($student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')')) ?>
            </span>
        </h3>
        <a class="close-reveal-modal">&#215;</a>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div style="margin-top: -10px;"><hr></div>
            <?php if ($studentMustHaveCurriculum): ?>
                <div class="alert alert-danger" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                    <span style="margin-right: 15px;"></span>
                    <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT): ?>
                        <?= __('%s is not attached to a curriculum. Please attach the student to a curriculum in Placement > Accepted Students Attach Curriculum using %s Admission year and %s as Program and %s as Program Type filter.',
                            h($student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')'),
                            h($student_detail['Student']['academicyear']),
                            h($student_detail['Program']['name']),
                            h($student_detail['ProgramType']['name'])
                        ) ?>
                    <?php else: ?>
                        <?= __('%s is not attached to a curriculum. Communicate their department to attach a curriculum to the student before trying to add them to a section.',
                            h($student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')')
                        ) ?>
                    <?php endif; ?>
                </div>
            <?php elseif ($is_student_dismissed && !$is_student_readmitted): ?>
                <div class="alert alert-danger" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                    <span style="margin-right: 15px;"></span>
                    <?= __('%s is dismissed in the %s semester of %s and no readmission data is recorded after their dismissal.',
                        h($student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')'),
                        $last_student_status['StudentExamStatus']['semester'] == 'I' ? '1st' : ($last_student_status['StudentExamStatus']['semester'] == 'II' ? '2nd' : ($last_student_status['StudentExamStatus']['semester'] == 'III' ? '3rd' : h($last_student_status['StudentExamStatus']['semester']))),
                        h($last_student_status['StudentExamStatus']['academic_year'])
                    ) ?>
                </div>
            <?php elseif (!empty($msg)): ?>
                <div class="alert alert-warning" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                    <span style="margin-right: 15px;"></span>
                    <?= __('Section add aborted. Fix the following errors before attempting to add the student to any section.') ?>
                    <br><hr>
                    <?= h($msg) ?>
                </div>
            <?php elseif (!$statusGeneratedForLastRegistration && ($student_detail['Student']['program_type_id'] == PROGRAM_TYPE_REGULAR)): ?>
                <div class="alert alert-danger" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                    <span style="margin-right: 15px;"></span>
                    <?= __('%s have registration in the %s semester of %s, but student academic status is not generated.',
                        h($student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')'),
                        $student_detail['CourseRegistration'][0]['semester'] == 'I' ? '1st' : ($student_detail['CourseRegistration'][0]['semester'] == 'II' ? '2nd' : ($student_detail['CourseRegistration'][0]['semester'] == 'III' ? '3rd' : h($student_detail['CourseRegistration'][0]['semester']))),
                        h($student_detail['CourseRegistration'][0]['academic_year'])
                    ) ?>
                </div>
            <?php elseif ($student_have_invalid_grade): ?>
                <div class="alert alert-danger" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                    <span style="margin-right: 15px;"></span>
                    <?= __('%s have invalid grade in one of %s semesters.',
                        h($student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')'),
                        h($student_detail['CourseRegistration'][0]['academic_year'])
                    ) ?>
                </div>
            <?php elseif (!empty($possibleAcademicYears) && !empty($sectionOrganized) && $studentNeedsSectionAssignment): ?>
                <?= $this->Form->create('Section', ['url' => ['controller' => 'Sections', 'action' => 'addStudentPrevSection']]) ?>
                <?= $this->Form->hidden('Selected_student_id', ['value' => $student_detail['Student']['id']]) ?>
                <?= $this->Form->hidden('acYrStart', [
                    'id' => 'acYrStart',
                    'value' => isset($acYrStart) && !empty($acYrStart) ? str_replace('/', '-', $acYrStart) :
                        (!empty($lastReadmittedAcademicYear) ? str_replace('/', '-', $lastReadmittedAcademicYear) :
                            (!empty($lastRegisteredAcademicYear) ? str_replace('/', '-', $lastRegisteredAcademicYear) :
                                str_replace('/', '-', $student_detail['Student']['academicyear'])))
                ]) ?>
                <table class="table table-bordered">
                    <tr>
                        <td style="width: 2%;">&nbsp;</td>
                        <td>
                            <div class="row">
                                <div class="col-md-6" style="margin-top: 10px;">
                                    <?= $this->Form->control('year_level_id', [
                                        'label' => __('Select Year Level: '),
                                        'type' => 'select',
                                        'empty' => '[ Select Year Level of Section ]',
                                        'id' => 'year_level_id',
                                        'options' => $yearLevelsProfile,
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <br>
                <div id="SectionList"></div>
                <?= $this->Form->end() ?>
            <?php elseif ($isLastSemesterInCurriculum): ?>
                <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                    <span style="margin-right: 15px;"></span>
                    <?= __(
                        'Student\'s attached curriculum, %s, states that the student (%s) is in the last year, last semester of the curriculum. You can move the student to a different academic year, same level section instead.',
                        isset($student_attached_curriculum_name) && !empty($student_attached_curriculum_name) ? h($student_attached_curriculum_name) : __('attached curriculum'),
                        h($student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')')
                    ) ?>
                </div>
            <?php elseif (!empty($possibleAcademicYears) && empty($sectionOrganized) && $studentNeedsSectionAssignment): ?>
                <?php
                $acy_ranges_by_coma_quoted_for_display = implode(", ", $possibleAcademicYears);
                ?>
                <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                    <span style="margin-right: 15px;"></span>
                    <?php if (!empty($lastRegisteredAcademicYear) && !empty($lastRegisteredYearLevelName)): ?>
                        <?= __(
                            'No active %s section is found from %s to %s %s to add %s. %s',
                            h($lastRegisteredYearLevelName) . ' year',
                            h($lastRegisteredAcademicYear),
                            h($current_academicyear),
                            isset($student_attached_curriculum_name) && !empty($student_attached_curriculum_name) ? __('which uses %s curriculum', h($student_attached_curriculum_name)) : '',
                            h($student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')'),
                            $this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT ?
                                $this->Html->link(
                                    __('Display Sections'),
                                    ['controller' => 'Sections', 'action' => 'displaySections'],
                                    ['target' => '_blank']
                                ) . (isset($student_attached_curriculum_name) && !empty($student_attached_curriculum_name) ? __(" which uses %s's attached curriculum.", h($student_detail['Student']['full_name'])) : '') : ''
                        ) ?>
                    <?php else: ?>
                        <?= __(
                            'No active %s section is found in %s %s to add %s. %s',
                            isset($currentYearLevelIDName) && !empty($currentYearLevelIDName) ? h($currentYearLevelIDName) : '',
                            h($acy_ranges_by_coma_quoted_for_display) . (count($possibleAcademicYears) == 1 ? __(' academic year') : __(' academic years')),
                            isset($student_attached_curriculum_name) && !empty($student_attached_curriculum_name) ? __('which uses %s curriculum', h($student_attached_curriculum_name)) : '',
                            h($student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')'),
                            $this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT ?
                                $this->Html->link(
                                    __('Display Sections'),
                                    ['controller' => 'Sections', 'action' => 'displaySections'],
                                    ['target' => '_blank']
                                ) . (isset($student_attached_curriculum_name) && !empty($student_attached_curriculum_name) ? __(" which uses %s's attached curriculum.", h($student_detail['Student']['full_name'])) : '') : ''
                        ) ?>
                    <?php endif; ?>
                </div>
            <?php elseif (!$studentNeedsSectionAssignment): ?>
                <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                    <span style="margin-right: 15px;"></span>
                    <?= __('%s doesn\'t need new section assignment. Check for incomplete grade submissions.',
                        h($student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')')
                    ) ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                    <span style="margin-right: 15px;"></span>
                    <?= __('You can\'t add %s to section since they are already in the section.',
                        h($student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')')
                    ) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
