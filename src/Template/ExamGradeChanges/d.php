<?php
/**
 * @var \App\View\AppView $this
 * @var array $examGradeChanges
 * @var array $makeupExamGradeChanges
 * @var array $departmentMakeupExamGradeChanges
 * @var string $years_to_look_list_for_display
 */
use Cake\I18n\Time;

$this->assign('title', __('Exam Grade Change, Makeup & Supplementary Exam Approval'));
?>

    <div class="box">
        <div class="box-header bg-transparent">
            <div class="box-title" style="margin-top: 10px;">
                <i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
                <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Exam Grade Change, Makeup & Supplementary Exam Approval') ?>
                <?= isset($years_to_look_list_for_display) ? ' (' . h($years_to_look_list_for_display) . ')' : '' ?>
            </span>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="large-12 columns">
                    <div style="margin-top: -30px;"><hr></div>

                    <?php $st_count = 1; ?>
                    <?= $this->Form->create(null, ['id' => 'ExamGradeChangeForm', 'onsubmit' => 'return checkForm(this);']) ?>

                    <h6 id="validation-message_non_selected" class="text-red fs14"></h6>

                    <?php if (!empty($examGradeChanges)): ?>
                        <hr>
                        <h6 class="fs16 text-gray"><?= __('Exam Grade Changes which are requested by Instructor and approved by the Department and College') ?></h6>
                        <hr>

                        <?php foreach ($examGradeChanges as $college_name => $college_grade_changes): ?>
                            <?php foreach ($college_grade_changes as $department_name => $department_grade_changes): ?>
                                <?php foreach ($department_grade_changes as $program_name => $program_grade_changes): ?>
                                    <?php foreach ($program_grade_changes as $program_type_name => $program_type_grade_changes): ?>
                                        <div style="overflow-x:auto;">
                                            <table cellpadding="0" cellspacing="0" class="table">
                                                <thead>
                                                <tr>
                                                    <td colspan="9" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                        <span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?= h($department_name) ?></span><br>
                                                        <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
                                                            <?= h($college_name) ?><br>
                                                        </span>
                                                        <span class="text-gray" style="padding-top: 14px; font-size: 13px; font-weight: bold">
                                                            <?= h($program_name . ' | ' . $program_type_name) ?><br>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width:5%;" class="center">
                                                        <div style="padding-left: 10%;">
                                                            <?= $this->Form->control('Mass.ExamGradeChange.select_all', [
                                                                'type' => 'checkbox',
                                                                'id' => 'select-all',
                                                                'label' => false,
                                                                'disabled' => true
                                                            ]) ?>
                                                        </div>
                                                    </th>
                                                    <th style="width:2%;" class="center">#</th>
                                                    <th style="width:4%;" class="center">&nbsp;</th>
                                                    <th style="width:20%;" class="vcenter"><?= __('Student Name') ?></th>
                                                    <th style="width:10%;" class="center"><?= __('Student ID') ?></th>
                                                    <th style="width:27%;" class="center"><?= __('Course') ?></th>
                                                    <th style="width:7%;" class="center"><?= __('Previous') ?></th>
                                                    <th style="width:7%;" class="center"><?= __('New') ?></th>
                                                    <th style="width:18%;" class="center"><?= __('Request Date') ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $counter = 1; ?>
                                                <?php foreach ($program_type_grade_changes as $key => $grade_change): ?>
                                                    <tr>
                                                        <td class="center">
                                                            <div style="padding-left: 15%;">
                                                                <?= $this->Form->control('Mass.ExamGradeChange.' . $st_count . '.gp', [
                                                                    'type' => 'checkbox',
                                                                    'label' => false,
                                                                    'id' => 'ExamGradeChange' . $st_count,
                                                                    'class' => 'checkbox1',
                                                                    'disabled' => true
                                                                ]) ?>
                                                                <?= $this->Form->control('Mass.ExamGradeChange.' . $st_count . '.id', [
                                                                    'type' => 'hidden',
                                                                    'value' => $grade_change['ExamGradeChange']['id']
                                                                ]) ?>
                                                            </div>
                                                        </td>
                                                        <td class="center"><?= $counter++ ?></td>
                                                        <td onclick="toggleView(this)" id="<?= $st_count ?>" class="center">
                                                            <?= $this->Html->image('plus2.gif', ['id' => 'i' . $st_count]) ?>
                                                        </td>
                                                        <td class="vcenter">
                                                            <?= h($grade_change['Student']['first_name'] . ' ' . $grade_change['Student']['middle_name'] . ' ' . $grade_change['Student']['last_name']) ?>
                                                        </td>
                                                        <td class="center"><?= h($grade_change['Student']['studentnumber']) ?></td>
                                                        <td class="center">
                                                            <?= h($grade_change['Course']['course_title'] . ' (' . $grade_change['Course']['course_code'] . ')') ?>
                                                        </td>
                                                        <td class="center"><?= h($grade_change['latest_grade']) ?></td>
                                                        <td class="center"><?= h($grade_change['ExamGradeChange']['grade']) ?></td>
                                                        <td class="center">
                                                            <?= (new Time($grade_change['ExamGradeChange']['created']))->format('M j, Y g:i:s A') ?>
                                                        </td>
                                                    </tr>
                                                    <tr id="c<?= $st_count ?>" style="display:none;">
                                                        <td style="background-color: white;">&nbsp;</td>
                                                        <td colspan="2" style="background-color:white;">&nbsp;</td>
                                                        <td colspan="6" style="background-color:white;">
                                                            <table cellpadding="0" cellspacing="0" class="table">
                                                                <?php if ($grade_change['ExamGradeChange']['initiated_by_department'] == 1): ?>
                                                                    <tr>
                                                                        <td class="fs14" style="font-weight:bold;" class="vcenter">
                                                                            <span class="text-red">
                                                                                <?= __('Important Note: This exam grade change is requested by the department, not by the course instructor!') ?>
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                                <tr>
                                                                    <td style="font-weight:bold; background-color:white;" class="vcenter">
                                                                        <?= __('Section') ?>: &nbsp;
                                                                        <?= h($grade_change['Section']['name'] . ' (' . (isset($grade_change['Section']['YearLevel']['id']) && !empty($grade_change['Section']['YearLevel']['name']) ? $grade_change['Section']['YearLevel']['name'] : ($grade_change['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman')) . ', ' . $grade_change['Section']['academicyear'] . ')') ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-weight:bold; background-color:white;" class="vcenter">
                                                                        <?= __('Instructor') ?>: &nbsp;
                                                                        <?= h($grade_change['Staff']['Title']['title'] . '. ' . $grade_change['Staff']['first_name'] . ' ' . $grade_change['Staff']['middle_name'] . ' ' . $grade_change['Staff']['last_name'] . ' (' . $grade_change['Staff']['Position']['position'] . ')') ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-weight:bold; background-color:white;" class="vcenter">
                                                                        <?= __('Reason for Change') ?>: &nbsp;
                                                                        <?= h($grade_change['ExamGradeChange']['reason']) ?>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <br>
                                                            <table cellpadding="0" cellspacing="0" class="table">
                                                                <tr>
                                                                    <td style="vertical-align:top; width:60%; background-color:white;">
                                                                        <?php
                                                                        $register_or_add = 'gh';
                                                                        $grade_history = $grade_change['ExamGradeHistory'] ?? [];
                                                                        $freshman_program = is_null($grade_change['Section']['department_id']);
                                                                        $this->set(compact('register_or_add', 'grade_history', 'freshman_program'));
                                                                        echo $this->element('registered_or_add_course_grade_history');
                                                                        ?>
                                                                    </td>
                                                                    <td style="vertical-align:top; width:40%; background-color:white;">
                                                                        <?php if (!$grade_change['Student']['graduated']): ?>
                                                                            <table cellpadding="0" cellspacing="0" class="table">
                                                                                <tr>
                                                                                    <td colspan="2">
                                                                                        <div style="font-weight:bold; font-size:14px"><?= __('Grade Change Request Approval') ?></div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td style="width:20%; background-color:white;"><?= __('Decision') ?>:</td>
                                                                                    <td style="width:80%; background-color:white;">
                                                                                        <?= $this->Form->control('ExamGradeChange.' . $st_count . '.id', [
                                                                                            'type' => 'hidden',
                                                                                            'value' => $grade_change['ExamGradeChange']['id']
                                                                                        ]) ?>
                                                                                        <?= $this->Form->control('ExamGradeChange.' . $st_count . '.registrar_approval', [
                                                                                            'type' => 'radio',
                                                                                            'options' => [
                                                                                                1 => __('Accept (Finalize)'),
                                                                                                -1 => __('Reject (Back to Department)')
                                                                                            ],
                                                                                            'default' => 1,
                                                                                            'label' => false,
                                                                                            'templates' => [
                                                                                                'radioWrapper' => '<div class="radio">{{label}}</div>',
                                                                                                'nestingLabel' => '{{hidden}}{{input}}<label{{attrs}}>{{text}}</label><br>'
                                                                                            ]
                                                                                        ]) ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td style="background-color:white;"><?= __('Remark') ?>:</td>
                                                                                    <td style="background-color:white;">
                                                                                        <?= $this->Form->control('ExamGradeChange.' . $st_count . '.registrar_reason', [
                                                                                            'label' => false,
                                                                                            'cols' => 40
                                                                                        ]) ?>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                            <br>
                                                                            <?= $this->Form->button(__('Approve Grade Change Request'), [
                                                                                'class' => 'tiny radius button bg-blue',
                                                                                'name' => 'approveGradeChangeByRegistrar_' . $st_count++
                                                                            ]) ?>
                                                                        <?php else: ?>
                                                                            <div class="warning-box warning-message">
                                                                                <span style="margin-right: 15px;"></span>
                                                                                <?= __('Grade Change is not available for graduated student.') ?>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <br>
                                                            <?php
                                                            $student_exam_grade_change_history = $grade_change['ExamGradeHistory'];
                                                            $student_exam_grade_history = $grade_change['ExamGrade'];
                                                            $this->set(compact('student_exam_grade_history', 'student_exam_grade_change_history'));
                                                            echo $this->element('registered_or_add_course_grade_detail_history');
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <hr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($makeupExamGradeChanges)): ?>
                        <hr>
                        <h6 class="fs14 text-gray"><?= __('Makeup Exam approval which is requested by Instructors and approved by the department.') ?></h6>
                        <hr>
                        <?php foreach ($makeupExamGradeChanges as $college_name => $college_grade_changes): ?>
                            <?php foreach ($college_grade_changes as $department_name => $department_grade_changes): ?>
                                <?php foreach ($department_grade_changes as $program_name => $program_grade_changes): ?>
                                    <?php foreach ($program_grade_changes as $program_type_name => $program_type_grade_changes): ?>
                                        <div style="overflow-x:auto;">
                                            <table cellpadding="0" cellspacing="0" class="table">
                                                <thead>
                                                <tr>
                                                    <td colspan="8" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                        <span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?= h($department_name) ?></span><br>
                                                        <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
                                                            <?= h($college_name) ?><br>
                                                        </span>
                                                        <span class="text-gray" style="padding-top: 14px; font-size: 13px; font-weight: bold">
                                                            <?= h($program_name . ' | ' . $program_type_name) ?><br>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width:3%;" class="center">&nbsp;</th>
                                                    <th style="width:4%;" class="center">#</th>
                                                    <th style="width:15%;" class="vcenter"><?= __('Student Name') ?></th>
                                                    <th style="width:10%;" class="center"><?= __('Student ID') ?></th>
                                                    <th style="width:23%;" class="center"><?= __('Exam Taken for') ?></th>
                                                    <th style="width:23%;" class="center"><?= __('Exam Course') ?></th>
                                                    <th style="width:5%;" class="center"><?= __('Grade') ?></th>
                                                    <th style="width:17%;" class="center"><?= __('Request Date') ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $cntr = 1; ?>
                                                <?php foreach ($program_type_grade_changes as $key => $grade_change): ?>
                                                    <tr>
                                                        <td onclick="toggleView(this)" id="<?= $st_count ?>" class="center">
                                                            <?= $this->Html->image('plus2.gif', ['id' => 'i' . $st_count]) ?>
                                                        </td>
                                                        <td class="center"><?= $cntr++ ?></td>
                                                        <td class="center">
                                                            <?= h($grade_change['Student']['first_name'] . ' ' . $grade_change['Student']['middle_name'] . ' ' . $grade_change['Student']['last_name']) ?>
                                                        </td>
                                                        <td class="center"><?= h($grade_change['Student']['studentnumber']) ?></td>
                                                        <td class="center">
                                                            <?= h($grade_change['Course']['course_title'] . ' (' . $grade_change['Course']['course_code'] . ')') ?>
                                                        </td>
                                                        <td class="center">
                                                            <?= h($grade_change['ExamCourse']['course_title'] . ' (' . $grade_change['ExamCourse']['course_code'] . ')') ?>
                                                        </td>
                                                        <td class="center"><?= h($grade_change['ExamGradeChange']['grade']) ?></td>
                                                        <td class="center">
                                                            <?= (new Time($grade_change['ExamGradeChange']['created']))->format('M j, Y h:i:s A') ?>
                                                        </td>
                                                    </tr>
                                                    <tr id="c<?= $st_count ?>" style="display:none">
                                                        <td style="background-color: white;">&nbsp;</td>
                                                        <td style="background-color: white;">&nbsp;</td>
                                                        <td colspan="6" style="background-color: white;">
                                                            <?php if (!isset($grade_change['MakeupExam'])): ?>
                                                                <table cellpadding="0" cellspacing="0" class="table">
                                                                    <tr>
                                                                        <td style="font-weight:bold; background-color: white;" class="vcenter">
                                                                            <?= __('Section') ?>: &nbsp;
                                                                            <?= h($grade_change['Section']['name'] . ' (' . (isset($grade_change['Section']['YearLevel']['id']) && !empty($grade_change['Section']['YearLevel']['name']) ? $grade_change['Section']['YearLevel']['name'] : ($grade_change['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman')) . ', ' . $grade_change['Section']['academicyear'] . ')') ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="font-weight:bold; background-color: white;" class="vcenter">
                                                                            <?= __('Instructor') ?>: &nbsp;
                                                                            <?= h($grade_change['Staff']['first_name'] . ' ' . $grade_change['Staff']['middle_name'] . ' ' . $grade_change['Staff']['last_name']) ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="font-weight:bold; background-color: white;" class="vcenter">
                                                                            <?= __('Reason for Change') ?>: &nbsp;
                                                                            <?= h($grade_change['ExamGradeChange']['reason']) ?>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                                            <?php else: ?>
                                                                <table cellpadding="0" cellspacing="0" class="table">
                                                                    <tr>
                                                                        <td style="font-weight:bold; background-color: white;" class="vcenter">
                                                                            <?= __('Minute Number') ?>: &nbsp;
                                                                            <?= h($grade_change['MakeupExam']['minute_number']) ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="font-weight:bold; background-color: white;" class="vcenter">
                                                                            <?= __('Exam Section') ?>: &nbsp;
                                                                            <?= h($grade_change['ExamSection']['name']) ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="font-weight:bold; background-color: white;" class="vcenter">
                                                                            <?= __('Exam Given By') ?>: &nbsp;
                                                                            <?= h($grade_change['Staff']['first_name'] . ' ' . $grade_change['Staff']['middle_name'] . ' ' . $grade_change['Staff']['last_name']) ?>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                                            <?php endif; ?>
                                                            <table cellpadding="0" cellspacing="0" class="table">
                                                                <tr>
                                                                    <td style="vertical-align:top; width:60%; background-color: white;">
                                                                        <?php
                                                                        $register_or_add = 'gh';
                                                                        $grade_history = $grade_change['ExamGradeHistory'] ?? [];
                                                                        $freshman_program = is_null($grade_change['Section']['department_id']);
                                                                        $this->set(compact('register_or_add', 'grade_history', 'freshman_program'));
                                                                        echo $this->element('registered_or_add_course_grade_history');
                                                                        ?>
                                                                        <br>
                                                                    </td>
                                                                    <td style="vertical-align:top; width:40%; background-color: white;">
                                                                        <table cellpadding="0" cellspacing="0" class="table">
                                                                            <tr>
                                                                                <td colspan="2">
                                                                                    <div style="font-weight:bold; font-size:14px"><?= __('Grade Change Request Approval') ?></div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="width:20%; background-color: white;"><?= __('Decision') ?>:</td>
                                                                                <td style="width:80%; background-color: white;">
                                                                                    <?= $this->Form->control('ExamGradeChange.' . $st_count . '.id', [
                                                                                        'type' => 'hidden',
                                                                                        'value' => $grade_change['ExamGradeChange']['id']
                                                                                    ]) ?>
                                                                                    <?= $this->Form->control('ExamGradeChange.' . $st_count . '.registrar_approval', [
                                                                                        'type' => 'radio',
                                                                                        'options' => [
                                                                                            1 => __('Accept (Finalize)'),
                                                                                            -1 => __('Reject (Back to Department)')
                                                                                        ],
                                                                                        'default' => 1,
                                                                                        'label' => false,
                                                                                        'templates' => [
                                                                                            'radioWrapper' => '<div class="radio">{{label}}</div>',
                                                                                            'nestingLabel' => '{{hidden}}{{input}}<label{{attrs}}>{{text}}</label><br>'
                                                                                        ]
                                                                                    ]) ?>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="background-color: white;"><?= __('Remark') ?>:</td>
                                                                                <td style="background-color: white;">
                                                                                    <?= $this->Form->control('ExamGradeChange.' . $st_count . '.registrar_reason', [
                                                                                        'label' => false,
                                                                                        'cols' => 40
                                                                                    ]) ?>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                        <br>
                                                                        <?= $this->Form->button(__('Approve Grade Change Request'), [
                                                                            'class' => 'tiny radius button bg-blue',
                                                                            'name' => 'approveGradeChangeByRegistrar_' . $st_count++
                                                                        ]) ?>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <br>
                                                            <?php
                                                            $student_exam_grade_change_history = $grade_change['ExamGradeHistory'];
                                                            $student_exam_grade_history = $grade_change['ExamGrade'];
                                                            $this->set(compact('student_exam_grade_history', 'student_exam_grade_change_history'));
                                                            echo $this->element('registered_or_add_course_grade_detail_history');
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <hr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($departmentMakeupExamGradeChanges)): ?>
                        <hr>
                        <h6 class="fs16 text-gray"><?= __('Exam Grade Change through Supplementary Exam') ?></h6>
                        <hr>
                        <?php foreach ($departmentMakeupExamGradeChanges as $college_name => $college_grade_changes): ?>
                            <?php foreach ($college_grade_changes as $department_name => $department_grade_changes): ?>
                                <?php foreach ($department_grade_changes as $program_name => $program_grade_changes): ?>
                                    <?php foreach ($program_type_grade_changes as $program_type_name => $program_type_grade_changes): ?>
                                        <div style="overflow-x:auto;">
                                            <table cellpadding="0" cellspacing="0" class="table">
                                                <thead>
                                                <tr>
                                                    <td colspan="8" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                        <span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?= h($department_name) ?></span><br>
                                                        <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
                                                            <?= h($college_name) ?><br>
                                                        </span>
                                                        <span class="text-gray" style="padding-top: 14px; font-size: 13px; font-weight: bold">
                                                            <?= h($program_name . ' | ' . $program_type_name) ?><br>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width:4%;" class="center">&nbsp;</th>
                                                    <th style="width:3%;" class="center">#</th>
                                                    <th style="width:18%;" class="vcenter"><?= __('Student Name') ?></th>
                                                    <th style="width:10%;" class="center"><?= __('Student ID') ?></th>
                                                    <th style="width:30%;" class="center"><?= __('Course') ?></th>
                                                    <th style="width:8%;" class="center"><?= __('Previous') ?></th>
                                                    <th style="width:8%;" class="center"><?= __('New') ?></th>
                                                    <th style="width:19%;" class="center"><?= __('Request Date') ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $freshman_program = is_null($program_type_grade_changes[0]['Section']['department_id']);
                                                $cntrr = 1;
                                                ?>
                                                <?php foreach ($program_type_grade_changes as $key => $grade_change): ?>
                                                    <tr>
                                                        <td onclick="toggleView(this)" id="<?= $st_count ?>" class="center">
                                                            <?= $this->Html->image('plus2.gif', ['id' => 'i' . $st_count]) ?>
                                                        </td>
                                                        <td class="center"><?= $cntrr++ ?></td>
                                                        <td class="vcenter">
                                                            <?= h($grade_change['Student']['first_name'] . ' ' . $grade_change['Student']['middle_name'] . ' ' . $grade_change['Student']['last_name']) ?>
                                                        </td>
                                                        <td class="center"><?= h($grade_change['Student']['studentnumber']) ?></td>
                                                        <td class="center">
                                                            <?= h($grade_change['Course']['course_title'] . ' (' . $grade_change['Course']['course_code'] . ')') ?>
                                                        </td>
                                                        <td class="center"><?= h($grade_change['latest_grade']) ?></td>
                                                        <td class="center"><?= h($grade_change['ExamGradeChange']['grade']) ?></td>
                                                        <td class="center">
                                                            <?= (new Time($grade_change['ExamGradeChange']['created']))->format('M j, Y h:i:s A') ?>
                                                        </td>
                                                    </tr>
                                                    <tr id="c<?= $st_count ?>" style="display:none">
                                                        <td style="background-color: white;">&nbsp;</td>
                                                        <td style="background-color: white;">&nbsp;</td>
                                                        <td colspan="6" style="background-color: white;">
                                                            <table cellpadding="0" cellspacing="0" class="table">
                                                                <tr>
                                                                    <td class="vcenter" style="background-color: white;">
                                                                        <b><?= __('Section') ?>:</b> &nbsp;
                                                                        <?= h($grade_change['Section']['name'] . ' (' .
                                                                            (isset($grade_change['Section']['YearLevel']['id']) &&
                                                                            !empty($grade_change['Section']['YearLevel']['name']) ?
                                                                                $grade_change['Section']['YearLevel']['name'] :
                                                                                ($grade_change['Section']['program_id'] == PROGRAM_REMEDIAL ?
                                                                                    'Remedial' : 'Pre/Freshman')) . ', ' .
                                                                            $grade_change['Section']['academicyear'] . ')') ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="vcenter" style="background-color: white;">
                                                                        <b><?= __('Course Instructor') ?>:</b> &nbsp;
                                                                        <?= isset($grade_change['Staff']) && !empty($grade_change['Staff'])
                                                                            ? h($grade_change['Staff']['first_name'] . ' ' . $grade_change['Staff']['middle_name'] . ' ' . $grade_change['Staff']['last_name'])
                                                                            : __('Instructor not assigned by the department') ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="vcenter" style="background-color: white;">
                                                                        <b><?= __('Makeup Exam Remark By') ?> <?= $freshman_program ? 'freshman program' : 'department' ?>:</b> &nbsp;
                                                                        <?= h($grade_change['ExamGradeChange']['reason']) ?>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <br>
                                                            <table cellpadding="0" cellspacing="0" class="table">
                                                                <tr>
                                                                    <td style="vertical-align:top; width:60%; background-color: white;">
                                                                        <?php
                                                                        $register_or_add = 'gh';
                                                                        $grade_history = $grade_change['ExamGradeHistory'] ?? [];
                                                                        $this->set(compact('register_or_add', 'grade_history', 'freshman_program'));
                                                                        echo $this->element('registered_or_add_course_grade_history');
                                                                        ?>
                                                                        <br>
                                                                    </td>
                                                                    <td style="vertical-align:top; width:40%; background-color: white;">
                                                                        <table cellpadding="0" cellspacing="0" class="table">
                                                                            <tr>
                                                                                <td colspan="2">
                                                                                    <div style="font-weight:bold; font-size:14px"><?= __('Grade Change Request Approval') ?></div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="width:18%; background-color: white;"><?= __('Decision') ?>:</td>
                                                                                <td style="width:82%; background-color: white;">
                                                                                    <?= $this->Form->control('ExamGradeChange.' . $st_count . '.id', [
                                                                                        'type' => 'hidden',
                                                                                        'value' => $grade_change['ExamGradeChange']['id']
                                                                                    ]) ?>
                                                                                    <?= $this->Form->control('ExamGradeChange.' . $st_count . '.registrar_approval', [
                                                                                        'type' => 'radio',
                                                                                        'options' => [
                                                                                            1 => __('Accept (Finalize)'),
                                                                                            -1 => __('Reject (Back to Department)')
                                                                                        ],
                                                                                        'default' => 1,
                                                                                        'label' => false,
                                                                                        'templates' => [
                                                                                            'radioWrapper' => '<div class="radio">{{label}}</div>',
                                                                                            'nestingLabel' => '{{hidden}}{{input}}<label{{attrs}}>{{text}}</label><br>'
                                                                                        ]
                                                                                    ]) ?>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="background-color: white;"><b><?= __('Remark') ?>:</b></td>
                                                                                <td style="background-color: white;">
                                                                                    <?= $this->Form->control('ExamGradeChange.' . $st_count . '.registrar_reason', [
                                                                                        'label' => false,
                                                                                        'cols' => 40
                                                                                    ]) ?>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                        <br>
                                                                        <?= $this->Form->button(__('Approve Grade Change Request'), [
                                                                            'class' => 'tiny radius button bg-blue',
                                                                            'name' => 'approveGradeChangeByRegistrar_' . $st_count++
                                                                        ]) ?>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <br>
                                                            <?php
                                                            $student_exam_grade_change_history = $grade_change['ExamGradeHistory'];
                                                            $student_exam_grade_history = $grade_change['ExamGrade'];
                                                            $this->set(compact('student_exam_grade_history', 'student_exam_grade_change_history'));
                                                            echo $this->element('registered_or_add_course_grade_detail_history');
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <hr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (count($examGradeChanges) > 1): ?>
                        <!-- <?= $this->Form->button(__('Accept All Grade Change Request'), [
                            'id' => 'approveRejectGradeChange',
                            'class' => 'tiny radius button bg-blue',
                            'name' => 'ApproveAllGradeChangeByRegistrar'
                        ]) ?> -->
                        <!-- Commented out as per original code -->
                    <?php endif; ?>

                    <?= $this->Form->control('grade_change_count', [
                        'type' => 'hidden',
                        'value' => ($st_count - 1)
                    ]) ?>

                    <?php if (empty($makeupExamGradeChanges) && empty($examGradeChanges) && empty($departmentMakeupExamGradeChanges)): ?>
                        <div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                            <span style="margin-right: 15px;"></span>
                            <?= __('There is no Exam Grade Change or Makeup Exam Grade submission request to confirm') ?>
                            <?= !empty($years_to_look_list_for_display) ? h($years_to_look_list_for_display) : '' ?>.
                            <?= __('Exam grade changes and makeup exams are required to be submitted by instructor and approved by department & college (for grade change) in order to appear here. You can use the "View Grade Change" tool to see the status of any grade change from assigned and department.') ?>
                        </div>
                    <?php endif; ?>

                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>

<?php $this->append('script'); ?>
    <script>
        function toggleView(obj) {
            if ($('#c' + obj.id).css("display") === 'none') {
                $('#i' + obj.id).attr("src", '/img/minus2.gif');
            } else {
                $('#i' + obj.id).attr("src", '/img/plus2.gif');
            }
            $('#c' + obj.id).toggle("slow");
        }

        let form_being_submitted = false;
        const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

        function checkForm(form) {
            const radios = document.querySelectorAll('input[type="radio"]');
            const checkedOneRadio = Array.from(radios).some(x => x.checked);

            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            const checkedOneCheckbox = Array.from(checkboxes).some(x => x.checked);

            if (!checkedOneRadio) {
                alert('At least one Grade Change Must be Accepted or Rejected!');
                validationMessageNonSelected.innerHTML = 'At least one Grade Change Must be Accepted or Rejected!';
                return false;
            }

            if (form_being_submitted) {
                alert("Approving/Rejecting Grade Change, please wait a moment...");
                return false;
            }

            form_being_submitted = true;
            return true;
        }

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
<?php $this->end(); ?>
