<?php
$this->assign('title', __('Assign Students to Section'));
$role_id=$this->request->getSession()->read('Auth.User.role_id')
?>

<div class="box">
    <div class="box-header bg-transparent">
        <h3 class="box-title" style="margin-top: 10px;">
            <i class="fa fa-plus"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Assign Students to Section') ?>
            </span>
        </h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div style="margin-top: -30px;"><hr></div>
                <?= $this->Form->create('Section', ['id' => 'AssignmentForm', 'url' => ['controller' => 'Sections', 'action' => 'assign']]) ?>
                <blockquote>
                    <h6><i class="fa fa-info"></i> &nbsp; <?= __('Important Note:') ?></h6>
                    <p style="text-align: justify;">
                        <span style="font-size: 16px;">
                            <?= __('Students can be involved in section management if and only if:') ?>
                        </span>
                    <ol class="text-muted" style="font-weight: bold; font-size: 14px;">
                        <li><?= __('They have student ID/Number') ?></li>
                        <li><?= __('They are admitted') ?></li>
                        <?php if ($role_id != ROLE_COLLEGE): ?>
                            <li><?= __('They are attached to a curriculum') ?></li>
                        <?php endif; ?>
                    </ol>
                    </p>
                </blockquote>
                <hr>
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th style="border-bottom: none;">
                                <span class="text-muted" style="font-size: 13px;">
                                    <br style="line-height: 0.5;">
                                    <?= __('College:') ?> &nbsp;<?= h($collegename) ?>
                                    <br style="line-height: 0.5;">
                                    <?php if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE): ?>
                                        <?= __('Department:') ?> &nbsp;<?= h($departmentname) ?>
                                    <?php endif; ?>
                                </span>
                                <hr>
                            </th>
                        </tr>
                        <tr>
                            <td style="background-color: white;">
                                <fieldset style="border: none; padding-top: 0px; padding-bottom: 0px;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?= $this->Form->control('academicyearSearch', [
                                                'id' => 'academicyearSearch',
                                                'label' => __('Academic Year: '),
                                                'type' => 'select',
                                                'options' => $acyear_array_data,
                                                'empty' => '[ Select Admission Year ]',
                                                'default' => isset($academicyear) ? $academicyear : '',
                                                'class' => 'form-control',
                                                'style' => 'width: 90%;'
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?= $this->Form->control('program_id', [
                                                'label' => __('Program: '),
                                                'empty' => '[ Select Program ]',
                                                'class' => 'form-control',
                                                'style' => 'width: 90%;'
                                            ]) ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?= $this->Form->control('program_type_id', [
                                                'label' => __('Program Type: '),
                                                'empty' => '[ Select Program Type ]',
                                                'class' => 'form-control',
                                                'style' => 'width: 90%;'
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <?php if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE): ?>
                                            <div class="col-md-6">
                                                <?= $this->Form->control('year_level_id', [
                                                    'disabled' => true,
                                                    'id' => 'SectionYearLevelIdDisabled',
                                                    'label' => __('Year Level: '),
                                                    'class' => 'form-control',
                                                    'style' => 'width: 90%;'
                                                ]) ?>
                                                <?= $this->Form->hidden('year_level_id', [
                                                    'value' => isset($yearLevels) ? array_keys($yearLevels)[0] : 0
                                                ]) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="col-md-6">
                                            <?= $this->Form->control('assignment_type', [
                                                'id' => 'assignmenttype',
                                                'type' => 'select',
                                                'options' => $assignment_type_array,
                                                'label' => __('Assignment Type: '),
                                                'empty' => '[ Select Assignment Type ]',
                                                'class' => 'form-control',
                                                'style' => 'width: 90%;'
                                            ]) ?>
                                        </div>
                                        <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE): ?>
                                            <div class="col-md-6"></div>
                                        <?php endif; ?>
                                    </div>
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                    <hr>
                    <?= $this->Form->button(__('Continue'), [
                        'type' => 'submit',
                        'name' => 'search',
                        'valeu' => 'search',
                        'id' => 'continueAssignment',
                        'class' => 'btn btn-primary btn-sm'
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <div style="overflow-x:auto;">
                        <table id="sectionNotAssignClass" class="table table-bordered">
                            <thead>
                            <tr>
                                <td style="border-bottom: 2px solid #555;" colspan="<?= count($programs) + 1 ?>">
                                        <span class="text-muted">
                                            <br style="line-height: 0.5;">
                                            <?= __(
                                                'Table: Summary of students%s by Program and Program Type',
                                                isset($sselectedAcademicYear) && !empty($sselectedAcademicYear) && $sselectedAcademicYear != '/undefined'
                                                    ? sprintf(__(' admitted for %s'), h($sselectedAcademicYear))
                                                    : (isset($current_academicyear) ? sprintf(__(' admitted for %s'), h($current_academicyear)) : '')
                                            ) ?>
                                        </span>
                                </td>
                            </tr>
                            <tr>
                                <th><?= __('ProgramType/Program') ?></th>
                                <?php foreach ($programs as $kp => $vp): ?>
                                    <th class="text-center"><?= h($vp) ?></th>
                                <?php endforeach; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($programTypes as $i => $programType): ?>
                                <tr>
                                    <td class="text-center"><?= h($programType) ?></td>
                                    <?php foreach ($programs as $j => $program): ?>
                                        <td class="text-center">
                                            <?= isset($summary_data[$program][$programType]) && !empty($summary_data[$program][$programType]) ? h($summary_data[$program][$programType]) : '--' ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <?php if (isset($curriculum_unattached_student_count) && $curriculum_unattached_student_count > 0): ?>
                        <p>
                            <?= __('%s students did not attach to the department curriculum, so these students did not participate in any section assignment.', h($curriculum_unattached_student_count)) ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="sections form" id="assignmentDiv">
                    <div class="col-md-12">
                        <?php if ($section_less_total_students > 0): ?>
                        <?php if (isset($sectionlessStudentCurriculum)): ?>
                            <div class="alert alert-info">
                                <span style="margin-right: 15px;"></span>
                                <?= __('The system notes that there is more than 1 curriculum taken by section unassigned students, so please select curriculum and click on continue button.') ?>
                            </div>
                            <table class="table table-bordered">
                                <tr>
                                    <td>
                                        <?= $this->Form->control('Curriculum', [
                                            'type' => 'select',
                                            'options' => $sectionlessStudentCurriculumArray,
                                            'empty' => '[ Select Curriculum ]',
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= $this->Form->button(__('Continue'), [
                                            'type' => 'submit',
                                            'name' => 'continue',
                                            'class' => 'btn btn-primary btn-sm'
                                        ]) ?>
                                    </td>
                                </tr>
                            </table>
                        <?php endif; ?>

                        <?php if (!empty($sections)): ?>
                            <hr>
                            <fieldset>
                                <legend>&nbsp; &nbsp; &nbsp; <?= __('Assign students to the given section') ?> &nbsp; &nbsp; &nbsp;</legend>
                                <table class="table table-bordered">
                                    <tr>
                                        <td>
                                            <?= $this->Form->control('academicyear', [
                                                'id' => 'academicyear',
                                                'value' => $academicyear,
                                                'readonly' => true,
                                                'class' => 'form-control',
                                                'style' => 'width: 40%;'
                                            ]) ?>
                                            <h6 class="text-muted" style="font-size: 16px;">
                                                <?= h($collegename) ?>
                                            </h6>
                                            <?php if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE): ?>
                                                <h6 class="text-muted" style="font-size: 16px;">
                                                    <?= h($departmentname) ?> <?= __('department') ?>
                                                </h6>
                                            <?php endif; ?>
                                            <div class="font-weight-bold">
                                                <?= __('Total number of %s students who are not assigned to any section: %s', h($selected_program_name), h($section_less_total_students)) ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                    $section_list_name = [];
                                    $sectionsNewDistribution = [];
                                    $default_max_section_size = 50;
                                    if (defined('DEFAULT_MAXIMUM_STUDENTS_PER_SECTION')
                                        && is_numeric(DEFAULT_MAXIMUM_STUDENTS_PER_SECTION)
                                        && DEFAULT_MAXIMUM_STUDENTS_PER_SECTION > 0) {
                                        $default_max_section_size = DEFAULT_MAXIMUM_STUDENTS_PER_SECTION;
                                    }
                                    if ($assignmenttype != 'result') {
                                        $totalStudents = (int) $section_less_total_students;
                                        $sectionsNewDistribution = array_fill(0, count($sections), 0);
                                        $baseStudentsPerSection = (int) floor($totalStudents / count($sections));
                                        $remainder = $totalStudents % count($sections);
                                        foreach ($sectionsNewDistribution as $index => &$studentCount) {
                                            $studentCount = $baseStudentsPerSection;
                                        }
                                        for ($i = 0; $i < $remainder; $i++) {
                                            $sectionsNewDistribution[$i]++;
                                        }
                                    }
                                    ?>
                                    <?php foreach ($sections as $key => $value): ?>
                                        <?= $this->Form->hidden("Section.{$key}.id", ['value' => $value['Section']['id']]) ?>
                                        <?php if ($assignmenttype == 'result'): ?>
                                            <?php
                                            $section_list_name[] = sprintf(
                                                '%s (Currently hosted students: %s%s)',
                                                h($value['Section']['name']),
                                                h($current_sections_occupation[$key]),
                                                isset($sections_curriculum_name[$key]) && !empty($sections_curriculum_name[$key]) ? sprintf(', %s: %s', __('Section curriculum'), h($sections_curriculum_name[$key])) : ''
                                            );
                                            ?>
                                        <?php else: ?>
                                            <tr>
                                                <td class="text-center">
                                                    <?= h($value['Section']['name']) ?>
                                                    (<?= __('Current hosted students: %s%s',
                                                        h($current_sections_occupation[$key]),
                                                        isset($sections_curriculum_name[$key]) && !empty($sections_curriculum_name[$key]) ? sprintf(', %s: %s', __('Section curriculum'), h($sections_curriculum_name[$key])) : ''
                                                    ) ?>)
                                                    <br><br>
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <?= $this->Form->control("Section.{$key}.number", [
                                                                'label' => __('# of students to assign now: '),
                                                                'type' => 'number',
                                                                'value' => isset($sectionsNewDistribution[$key]) ? $sectionsNewDistribution[$key] : 0,
                                                                'min' => 0,
                                                                'max' => $default_max_section_size - $current_sections_occupation[$key],
                                                                'step' => 1,
                                                                'class' => 'form-control',
                                                                'style' => 'width: 40%;'
                                                            ]) ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td class="auto-width">
                                            <?php if ($assignmenttype == 'result'): ?>
                                                <?= $this->Form->control('Sections', [
                                                    'type' => 'select',
                                                    'multiple' => 'checkbox',
                                                    'options' => $section_list_name,
                                                    'class' => 'form-control'
                                                ]) ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                            <hr>
                            <?= $this->Form->button(__('Assign to Section'), [
                                'type' => 'submit',
                                'name' => 'assign',
                                'value' => 'assign',
                                'class' => 'btn btn-primary btn-sm'
                            ]) ?>
                            <?= $this->Form->end() ?>
                        <?php elseif (empty($sections) && !$isbeforesearch): ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                        <span style="margin-right: 15px;"></span>
                                        <?= __('No section is found with these search criteria.') ?>
                                    </div>
                                </div>
                            </div>
                        <?php elseif ($section_less_total_students <= 0 && !$isbeforesearch): ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                        <span style="margin-right: 15px;"></span>
                                        <?= __('There is no student who is not assigned to a section in the search criteria.') ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#academicyearSearch").change(function() {
            var summery = $(this).val();
            var exploded = summery.split('/');
            var academicYear = exploded[0] + '-' + exploded[1];
            $(this).prop('disabled', true);
            $("#sectionNotAssignClass").empty().html('<img src="<?= $this->Url->build('/img/busy.gif') ?>" class="d-block mx-auto" alt="Loading">');
            var formUrl = '<?= $this->Url->build(['controller' => 'Sections', 'action' => 'unAssignedSummaries']) ?>/' + encodeURIComponent(academicYear);
            $.ajax({
                type: 'GET',
                url: formUrl,
                data: { summery: summery },
                success: function(data) {
                    $("#academicyearSearch").prop('disabled', false);
                    $("#sectionNotAssignClass").empty().append(data);
                },
                error: function(xhr, textStatus) {
                    alert(textStatus);
                }
            });
            return false;
        });

        $("#AssignmentForm").submit(function(e) {
            var assignmentType = $("#assignmenttype").val();
            if (!assignmentType) {
                alert('Please select an assignment type.');
                $("#assignmenttype").focus();
                return false;
            }
            if (!$("#academicyearSearch").val()) {
                alert('Please select an academic year.');
                $("#academicyearSearch").focus();
                return false;
            }
            return true;
        });
    });

    window.location.hash = '#assignmentDiv';
</script>
