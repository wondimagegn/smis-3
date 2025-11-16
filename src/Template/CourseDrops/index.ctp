<?php

use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\Utility\Text;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
?>

<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('List Course Drops') ?>
            </span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create(null, ['url' => ['action' => 'search']]) ?>
                <?php if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_STUDENT) : ?>
                    <div style="margin-top: -30px;">
                        <hr>
                        <div onclick="toggleViewFullId('ListPublishedCourse')">
                            <?php if (!empty($turn_off_search)) : ?>
                                <?= $this->Html->image('plus2.gif', ['id' => 'ListPublishedCourseImg']) ?>
                                <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">
                                    Display Filter
                                </span>
                            <?php else : ?>
                                <?= $this->Html->image('minus2.gif', ['id' => 'ListPublishedCourseImg']) ?>
                                <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">
                                    Hide Filter
                                </span>
                            <?php endif; ?>
                        </div>
                        <div id="ListPublishedCourse" style="display:<?= !empty($turn_off_search) ? 'none' : 'block' ?>">
                            <fieldset style="padding-bottom: 0px; padding-top: 15px;">
                                <div class="row">
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.academic_year', [
                                            'label' => 'Academic Year: ',
                                            'style' => 'width:90%;',
                                            'empty' => 'All Applicable ACY',
                                            'options' => $acyearArrayData
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.semester', [
                                            'label' => 'Semester: ',
                                            'style' => 'width:90%;',
                                            'empty' => 'All Semesters',
                                            'options' => Configure::read('semesters')
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.program_id', [
                                            'label' => 'Program: ',
                                            'style' => 'width:90%;',
                                            'id' => 'program_id_1',
                                            'empty' => 'All Programs',
                                            'options' => $programs
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.program_type_id', [
                                            'label' => 'Program Type: ',
                                            'style' => 'width:90%;',
                                            'empty' => 'All Program Types',
                                            'options' => $programTypes
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="large-6 columns">
                                        <?php if (isset($colleges) && !empty($colleges)) : ?>
                                            <?= $this->Form->control('Search.college_id', [
                                                'label' => 'College: ',
                                                'style' => 'width:90%;',
                                                'empty' => 'All Applicable Colleges',
                                                'options' => $colleges,
                                                'onchange' => 'getDepartment(1)',
                                                'id' => 'college_id',
                                                'default' => isset($default_college_id) && !empty($default_college_id) ? $default_college_id : ''
                                            ]) ?>
                                        <?php elseif (isset($departments) && !empty($departments)) : ?>
                                            <?= $this->Form->control('Search.department_id', [
                                                'label' => 'Department: ',
                                                'style' => 'width:90%;',
                                                'empty' => 'All Applicable Departments',
                                                'options' => $departments,
                                                'id' => 'department_id_1',
                                                'default' => isset($default_department_id) && !empty($default_department_id) ? $default_department_id : ''
                                            ]) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.graduated', [
                                            'label' => 'Graduated: ',
                                            'style' => 'width:90%;',
                                            'options' => ['0' => 'No', '1' => 'Yes', '2' => 'All'],
                                            'default' => '0'
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.name', [
                                            'label' => 'Student Name or ID: ',
                                            'placeholder' => 'Optional student name or ID...',
                                            'default' => $name,
                                            'style' => 'width:90%;'
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.limit', [
                                            'id' => 'limit',
                                            'type' => 'number',
                                            'min' => '1',
                                            'max' => '1000',
                                            'value' => !empty($this->request->getData('Search.limit')) ? $this->request->getData('Search.limit') : $limit,
                                            'step' => '1',
                                            'label' => 'Limit: ',
                                            'style' => 'width:90%;'
                                        ]) ?>
                                        <?= !empty($this->request->getData('Search.page')) ? $this->Form->hidden('page', ['value' => $this->request->getData('Search.page')]) : '' ?>
                                        <?= !empty($this->request->getData('Search.sort')) ? $this->Form->hidden('sort', ['value' => $this->request->getData('Search.sort')]) : '' ?>
                                        <?= !empty($this->request->getData('Search.direction')) ? $this->Form->hidden('direction', ['value' => $this->request->getData('Search.direction')]) : '' ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <div style="padding-left: 10%;">
                                            <br>
                                            <h6 class='fs13 text-gray'>Status: </h6>
                                            <?php
                                            $options = [
                                                'accepted' => 'Accepted',
                                                'rejected' => 'Rejected',
                                                'notprocessed' => 'Not Processed',
                                                'forced' => 'Forced Drop'
                                            ];
                                            ?>
                                            <?= $this->Form->control('Search.status', [
                                                'type' => 'radio',
                                                'options' => $options,
                                                'label' => false,
                                                'default' => 'notprocessed',
                                                'separator' => '<br>'
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($departments) && !empty($departments) &&
                                    $this->request->getSession()->read('Auth.User.role_id') != ROLE_STUDENT &&
                                    $this->request->getSession()->read('Auth.User.role_id') != ROLE_REGISTRAR &&
                                    $this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE &&
                                    $this->request->getSession()->read('Auth.User.role_id') != ROLE_DEPARTMENT) : ?>
                                    <div class="row">
                                        <div class="large-6 columns">
                                            <?= $this->Form->control('Search.department_id', [
                                                'label' => 'Department: ',
                                                'style' => 'width:90%;',
                                                'empty' => 'All Departments',
                                                'options' => $departments,
                                                'id' => 'department_id_1',
                                                'default' => $default_department_id
                                            ]) ?>
                                        </div>
                                        <div class="large-6 columns"></div>
                                    </div>
                                <?php endif; ?>
                                <hr>
                                <?= $this->Form->button(__('Search'), ['name' => 'search', 'class' => 'tiny radius button bg-blue']) ?>
                            </fieldset>
                            <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) : ?>
                                <br>
                                <div style="margin-top: -10px;">
                                    <hr>
                                    <blockquote>
                                        <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                                        <span style="text-align:justify;" class="fs15 text-gray">
                                            The student list you will get here depends on your
                                            <b style="text-decoration: underline;"><i>assigned College or Department, assigned Program and Program Types, and with your search conditions</i></b>.
                                            You can contact the registrar to adjust permissions assigned to you if you miss your students here.
                                        </span>
                                    </blockquote>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <hr>
                <?php else : ?>
                    <div style="margin-top: -30px;"><hr></div>
                <?php endif; ?>
                <?php if (!empty($courseDrops)) : ?>
                    <?php $count = 1; ?>
                    <br>
                    <div style="overflow-x:auto;">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                            <tr>
                                <td class="center">&nbsp;</td>
                                <td class="center">#</td>
                                <td class="vcenter"><?= $this->Paginator->sort('student_id', 'Student Name') ?></td>
                                <td class="vcenter"><?= $this->Paginator->sort('gender', 'Sex') ?></td>
                                <td class="center"><?= $this->Paginator->sort('studentnumber', 'Student ID') ?></td>
                                <td class="center"><?= $this->Paginator->sort('academic_year', 'ACY') ?></td>
                                <td class="center"><?= $this->Paginator->sort('semester', 'Sem') ?></td>
                                <td class="center"><?= $this->Paginator->sort('year_level_id', 'Year') ?></td>
                                <td class="center"><?= $this->Paginator->sort('course_id', 'Course') ?></td>
                                <td class="center">Cr</td>
                                <td class="center"><?= $this->Paginator->sort('department_approval', 'Department') ?></td>
                                <td class="center"><?= $this->Paginator->sort('registrar_confirmation', 'Registrar') ?></td>
                                <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_STUDENT) : ?>
                                    <td class="center"></td>
                                <?php endif; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $start = $this->Paginator->counter(['format' => '{{start}}']);
                            foreach ($courseDrops as $courseDrop) :
                                $courseTitleWithCredit = isset($courseDrop->course_registration->published_course->course->course_title)
                                    ? $courseDrop->course_registration->published_course->course->course_title . ' (' .
                                    $courseDrop->course_registration->published_course->course->course_code . ') course with ' .
                                    $courseDrop->course_registration->published_course->course->credit . ' ' .
                                    (strpos($courseDrop->course_registration->published_course->course->curriculum->type_credit, 'ECTS') !== false ? 'ECTS' : 'Credit')
                                    : '';
                                $credit_type = isset($courseDrop->student->curriculum->type_credit)
                                    ? (strpos($courseDrop->student->curriculum->type_credit, 'ECTS') !== false ? 'ECTS' : 'Credit')
                                    : 'Credit';
                                $student_full_name = isset($courseDrop->student->full_name)
                                    ? $courseDrop->student->full_name . ' (' . $courseDrop->student->studentnumber . ')'
                                    : '';
                                ?>
                                <tr>
                                    <td class="center" onclick="toggleView(this)" id="<?= $count ?>">
                                        <?= $this->Html->image('plus2.gif', ['id' => 'i' . $count, 'align' => 'center']) ?>
                                    </td>
                                    <td class="center"><?= $start++ ?></td>
                                    <td class="vcenter">
                                        <?= $this->Html->link($courseDrop->student->full_name, [
                                            'controller' => 'Students',
                                            'action' => 'student_academic_profile',
                                            $courseDrop->student->id
                                        ]) ?>
                                    </td>
                                    <td class="center">
                                        <?= strcasecmp(trim($courseDrop->student->gender), 'male') === 0 ? 'M' :
                                            (strcasecmp(trim($courseDrop->student->gender), 'female') === 0 ? 'F' : '') ?>
                                    </td>
                                    <td class="center"><?= $courseDrop->student->studentnumber ?></td>
                                    <td class="center"><?= $courseDrop->academic_year ?></td>
                                    <td class="center"><?= $courseDrop->semester ?></td>
                                    <td class="center">
                                        <?= !empty($courseDrop->year_level->name) ? $courseDrop->year_level->name : 'Pre/1st' ?>
                                    </td>
                                    <td class="center">
                                        <?php if (isset($courseDrop->course_registration->published_course->course) &&
                                            !empty($courseDrop->course_registration->published_course->course)) : ?>
                                            <?= $this->Html->link($courseDrop->course_registration->published_course->course->course_title, [
                                                'controller' => 'Courses',
                                                'action' => 'view',
                                                $courseDrop->course_registration->published_course->course->id
                                            ]) ?>
                                        <?php else : ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td class="center">
                                        <?= isset($courseDrop->course_registration->published_course->course) &&
                                        !empty($courseDrop->course_registration->published_course->course)
                                            ? $courseDrop->course_registration->published_course->course->credit
                                            : 'N/A' ?>
                                    </td>
                                    <td class="center">
                                        <?php if ($courseDrop->department_approval == 1) : ?>
                                            <?php if (!$courseDrop->forced) : ?>
                                                <span class="accepted">Accepted</span>
                                            <?php else : ?>
                                                --
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <?php if (!$courseDrop->forced) : ?>
                                                <?php if (is_null($courseDrop->department_approval)) : ?>
                                                    <?php if (isset($courseDrop->course_registration->exam_results) &&
                                                        !empty($courseDrop->course_registration->exam_results)) : ?>
                                                        <span class="on-process">(Have Exam Result)</span>
                                                    <?php elseif (isset($courseDrop->course_registration->exam_grades) &&
                                                        !empty($courseDrop->course_registration->exam_grades)) : ?>
                                                        <span class="on-process">(Have Exam Grade)</span>
                                                    <?php else : ?>
                                                        <?php if ((isset($courseDrop->course_registration->published_course->section->archive) &&
                                                                $courseDrop->course_registration->published_course->section->archive) ||
                                                            (isset($allowed_academic_years_for_add_drop) &&
                                                                !empty($allowed_academic_years_for_add_drop) &&
                                                                !in_array($courseDrop->academic_year, $allowed_academic_years_for_add_drop))) : ?>
                                                            <span class="rejected">Expired</span>
                                                        <?php else : ?>
                                                            <span class="text-gray"><i>Waiting Decision</i></span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php elseif ($courseDrop->department_approval == 0) : ?>
                                                    <span class="rejected">Rejected</span>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <?= $courseDrop->forced == 1 ? '<span class="rejected">Forced Drop</span>' : '' ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="center">
                                        <?php if (!$courseDrop->forced) : ?>
                                            <?php if ($courseDrop->department_approval == 1) : ?>
                                                <?php if (is_null($courseDrop->registrar_confirmation)) : ?>
                                                    <?php if (isset($courseDrop->course_registration->exam_results) &&
                                                        !empty($courseDrop->course_registration->exam_results)) : ?>
                                                        <span class="on-process">(Have Exam Result)</span>
                                                    <?php elseif (isset($courseDrop->course_registration->exam_grades) &&
                                                        !empty($courseDrop->course_registration->exam_grades)) : ?>
                                                        <span class="on-process">(Have Exam Grade)</span>
                                                    <?php else : ?>
                                                        <?php if ((isset($courseDrop->course_registration->published_course->section->archive) &&
                                                                $courseDrop->course_registration->published_course->section->archive) ||
                                                            (isset($allowed_academic_years_for_add_drop) &&
                                                                !empty($allowed_academic_years_for_add_drop) &&
                                                                !in_array($courseDrop->academic_year, $allowed_academic_years_for_add_drop))) : ?>
                                                            <span class="rejected">Expired</span>
                                                        <?php else : ?>
                                                            <span class="text-gray"><i>Waiting Decision</i></span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php elseif ($courseDrop->registrar_confirmation == 1) : ?>
                                                    <span class="accepted">Accepted</span>
                                                <?php elseif ($courseDrop->registrar_confirmation == 0) : ?>
                                                    <span class="rejected">Rejected</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php elseif ((isset($courseDrop->course_registration->published_course->section->archive) &&
                                                $courseDrop->course_registration->published_course->section->archive) ||
                                            (isset($allowed_academic_years_for_add_drop) &&
                                                !empty($allowed_academic_years_for_add_drop) &&
                                                !in_array($courseDrop->academic_year, $allowed_academic_years_for_add_drop))) : ?>
                                            <span class="rejected">Expired</span>
                                        <?php else : ?>
                                            <?= $courseDrop->forced == 1 ? '<span class="on-process">Forced Drop</span>' : '' ?>
                                            <?php
                                            $examGradeTable = TableRegistry::getTableLocator()->get('ExamGrades');
                                            if (!empty($courseDrop->course_registration->published_course->drop) &&
                                                $courseDrop->course_registration->published_course->drop == 0 &&
                                                !empty($courseDrop->course_registration->published_course->course->id) &&
                                                $this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR &&
                                                $this->request->getSession()->read('Auth.User.is_admin') != 1 &&
                                                !empty($courseDrop->course_registration->published_course->id) &&
                                                !$examGradeTable->isGradeSubmittedForPublishedCourse($courseDrop->course_registration->published_course->id)) :
                                                $confirmMessage = __(
                                                    'Are you sure you want to cancel the course drop of {0} for {1}? Cancelling this course drop will make the student available for course assigned instructor for grade submission. Are you sure you want cancel the course drop anyway?',
                                                    $student_full_name,
                                                    $courseTitleWithCredit
                                                );
                                                ?>
                                                <br>
                                                <?= $this->Html->link(
                                                __('[Cancel Drop]'),
                                                ['action' => 'delete', $courseDrop->id],
                                                ['escape' => false, 'confirm' => $confirmMessage]
                                            ) ?>
                                            <?php endif; ?>
                                            <?php if (!empty($courseDrop->course_registration->published_course->drop) &&
                                                $courseDrop->course_registration->published_course->drop == 0 &&
                                                !empty($courseDrop->registrar_confirmation) &&
                                                $courseDrop->registrar_confirmation == 1 &&
                                                !empty($courseDrop->student->graduated) &&
                                                $courseDrop->student->graduated == 0 &&
                                                !empty($courseDrop->id) &&
                                                $this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR &&
                                                $this->request->getSession()->read('Auth.User.is_admin') == 1) :
                                                $confirmMessage = __(
                                                    'REGISTRAR ADMIN COURSE DROP CANCELATION: Use this if the course is dropped by mistake for {0} for {1}? Cancelling this course drop will make the student available for course assigned instructor for grade submission and make sure the course instructor is available for grade submission and calender is open for {2} semester {3}. Are you sure you want cancel the course drop anyway?',
                                                    $student_full_name,
                                                    $courseTitleWithCredit,
                                                    $courseDrop->academic_year,
                                                    $courseDrop->semester
                                                );
                                                ?>
                                                <br>
                                                <?= $this->Html->link(
                                                __('[Cancel Drop]'),
                                                ['action' => 'delete', $courseDrop->id],
                                                ['escape' => false, 'confirm' => $confirmMessage]
                                            ) ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_STUDENT) : ?>
                                        <td class="center">
                                            <?php
                                            if (!$courseDrop->forced &&
                                                !empty($courseDrop->course_registration->published_course->drop) &&
                                                $courseDrop->course_registration->published_course->drop == 0 &&
                                                !empty($courseDrop->course_registration->published_course->section->archive) &&
                                                $courseDrop->course_registration->published_course->section->archive == 0 &&
                                                is_null($courseDrop->department_approval) &&
                                                is_null($courseDrop->registrar_confirmation) &&
                                                !$examGradeTable->isGradeSubmittedForPublishedCourse($courseDrop->course_registration->published_course->id)) :
                                                $confirmMessage = __(
                                                    'READ THE FOLLOWING NOTIFICATION CAREFULLY BEFORE PROCEEDING!! If you cancel course drop of {0} you requested previously, you will be allowed to continue with the course and you will be available for course assigned instructor for grade submission. But If you did not attended the class and your are cancelling this course add, you will get NG grade. Are you sure you want cancel the course drop anyway?',
                                                    $courseTitleWithCredit
                                                );
                                                ?>
                                                <?= $this->Html->link(
                                                'Cancel Drop',
                                                ['action' => 'delete', $courseDrop->id],
                                                ['escape' => false, 'confirm' => $confirmMessage]
                                            ) ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                                <tr id="c<?= $count++ ?>" style="display:none">
                                    <td colspan="2" style="background-color: white;"></td>
                                    <td colspan="<?= $this->request->getSession()->read('Auth.User.role_id') == ROLE_STUDENT ? '11' : '10' ?>" style="background-color: white;">
                                        <?php if (!empty($courseDrop->course_registration->published_course->course) &&
                                            !empty($courseDrop->course_registration->published_course->course->id)) : ?>
                                            <table cellpadding="0" cellspacing="0" class="table">
                                                <tbody>
                                                <tr>
                                                    <td class="vcenter" style="background-color: white;">
                                                        <span class="fs13 text-gray" style="font-weight: bold">Dropped from Section: </span>
                                                        <?= $courseDrop->course_registration->published_course->section->name . ' (' .
                                                        (!empty($courseDrop->course_registration->published_course->section->year_level->name)
                                                            ? $courseDrop->course_registration->published_course->section->year_level->name
                                                            : 'Pre/1st') . ', ' .
                                                        $courseDrop->course_registration->published_course->section->academicyear . ')' ?>
                                                        &nbsp;
                                                        <?= $courseDrop->course_registration->published_course->section->archive
                                                            ? '<span class="rejected"> (Archieved) </span>'
                                                            : '<span class="accepted"> (Active) </span>' ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter">
                                                        <span class="fs13 text-gray" style="font-weight: bold">Section/Course Curriculum: </span>
                                                        <?= !empty($courseDrop->course_registration->published_course->section->curriculum->name)
                                                            ? $courseDrop->course_registration->published_course->section->curriculum->name . ' - ' .
                                                            $courseDrop->course_registration->published_course->section->curriculum->year_introduced
                                                            : (!empty($courseDrop->course_registration->published_course->course->curriculum->name)
                                                                ? $courseDrop->course_registration->published_course->course->curriculum->name . ' - ' .
                                                                $courseDrop->course_registration->published_course->course->curriculum->year_introduced
                                                                : '') ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter" style="background-color: white;">
                                                        <span class="fs13 text-gray" style="font-weight: bold">Section Department/College: </span>
                                                        <?= !empty($courseDrop->course_registration->published_course->department->name)
                                                            ? $courseDrop->course_registration->published_course->department->name . ' (' .
                                                            $courseDrop->course_registration->published_course->department->college->name . ')'
                                                            : (!empty($courseDrop->course_registration->published_course->college->name)
                                                                ? 'Pre/Freshman (' . $courseDrop->course_registration->published_course->college->name . ')'
                                                                : '') ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter">
                                                        <span class="fs13 text-gray" style="font-weight: bold">Course Given By: </span>
                                                        <?= !empty($courseDrop->course_registration->published_course->given_by_department->name)
                                                            ? $courseDrop->course_registration->published_course->given_by_department->name
                                                            : 'Not Assigned Yet' ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter" style="background-color: white;">
                                                        <span class="fs13 text-gray" style="font-weight: bold">Student Attached Curriculum: </span>
                                                        <?= !empty($courseDrop->student->curriculum->name)
                                                            ? $courseDrop->student->curriculum->name . ' - ' . $courseDrop->student->curriculum->year_introduced
                                                            : '<span class="rejected">No Curriculum Attachment</span>' ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter">
                                                        <span class="fs13 text-gray" style="font-weight: bold">Student Graduated: </span>
                                                        <?= $courseDrop->student->graduated
                                                            ? '<span class="rejected"> Yes </span>'
                                                            : '<span class="accepted"> No </span>' ?>
                                                    </td>
                                                </tr>
                                                <?php if (!empty($courseDrop->reason)) : ?>
                                                    <tr>
                                                        <td class="vcenter" style="background-color: white;">
                                                            <span class="fs13 text-gray" style="font-weight: bold">Course Drop Reason: </span>
                                                            <?= $courseDrop->reason ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                                <?php if (!empty($courseDrop->minute_number)) : ?>
                                                    <tr>
                                                        <td class="vcenter" style="background-color: white;">
                                                            <span class="fs13 text-gray" style="font-weight: bold">Minute Number: </span>
                                                            <?= $courseDrop->minute_number ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                                <tr>
                                                    <td class="vcenter" style="background-color: white;">
                                                        <span class="fs13 text-gray" style="font-weight: bold">Course Drop Requested: </span>
                                                        <?= (new Time($courseDrop->created))->format('F j, Y h:i:s A') ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter">
                                                                <span class="fs13 text-gray" style="font-weight: bold">
                                                                    Course Drop
                                                                    <?= ($courseDrop->forced == 0 &&
                                                                        ($courseDrop->department_approval == 1 ||
                                                                            $courseDrop->registrar_confirmation == 1))
                                                                        ? ' Approved' . ($courseDrop->registrar_confirmation == 1 ? ' By Registrar' : ' By Department') . ': '
                                                                        : (($courseDrop->forced == 1 ||
                                                                            $courseDrop->department_approval == 0 ||
                                                                            $courseDrop->registrar_confirmation == 0)
                                                                            ? ($courseDrop->forced == 1 ? ' Forced Drop'
                                                                                : ($courseDrop->registrar_confirmation == 0 ? ' Rejected By Registrar' : ' Rejected By Department')) . ': '
                                                                            : ' Approval: ')
                                                                    ?>
                                                                </span>
                                                        <?= ($courseDrop->forced == 0 &&
                                                            ($courseDrop->department_approval == 1 ||
                                                                $courseDrop->registrar_confirmation == 1) ||
                                                            $courseDrop->forced == 1 ||
                                                            $courseDrop->department_approval == 0 ||
                                                            $courseDrop->registrar_confirmation == 0)
                                                            ? (new Time($courseDrop->modified))->format('F j, Y h:i:s A')
                                                            : 'Waiting ...' ?>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        <?php else : ?>
                                            <span class="rejected">Error: Published Course not found or deleted. Couldn't load Course details!!.</span>
                                            <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) : ?>
                                                <?php
                                                $confirmMessage = __(
                                                    'Are you sure you want to cancel the course drop of {0} for {1}? Cancelling this course drop will make the student available for course assigned instructor for grade submission. Are you sure you want cancel the course drop anyway?',
                                                    $student_full_name,
                                                    $courseTitleWithCredit
                                                );
                                                ?>
                                                <br>
                                                <?= $this->Html->link(
                                                    __('[Cancel Drop]'),
                                                    ['action' => 'delete', $courseDrop->id],
                                                    ['escape' => false, 'confirm' => $confirmMessage]
                                                ) ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <hr>
                    <div class="row">
                        <div class="large-5 columns">
                            <?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} records out of {{count}} total')]) ?>
                        </div>
                        <div class="large-7 columns">
                            <div class="pagination-centered">
                                <ul class="pagination">
                                    <?= $this->Paginator->prev('<< ' . __('Previous'), ['tag' => 'li'], null, ['class' => 'arrow unavailable']) ?>
                                    <?= $this->Paginator->numbers(['separator' => '', 'tag' => 'li']) ?>
                                    <?= $this->Paginator->next(__('Next') . ' >>', ['tag' => 'li'], null, ['class' => 'arrow unavailable']) ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                        <span style='margin-right: 15px;'></span>
                        There is no Course Drop in the system in the given criteria.
                    </div>
                <?php endif; ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function toggleViewFullId(id) {
        if ($('#' + id).css("display") == 'none') {
            $('#' + id + 'Img').attr("src", '<?= Router::url('/img/minus2.gif') ?>');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append('Hide Filter');
        } else {
            $('#' + id + 'Img').attr("src", '<?= Router::url('/img/plus2.gif') ?>');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append('Display Filter');
        }
        $('#' + id).toggle("slow");
    }

    function getDepartment(id) {
        var formData = $("#college_id").val();
        $("#department_id_" + id).empty();
        $("#department_id_" + id).append('<option style="width:90%;">loading...</option>');
        if (formData) {
            $("#department_id_" + id).attr('disabled', true);
            var formUrl = '<?= Router::url(['controller' => 'Departments', 'action' => 'get_department_combo']) ?>/' + formData + '/0/1';
            $.ajax({
                type: 'GET',
                url: formUrl,
                data: formData,
                success: function(data, textStatus, xhr) {
                    $("#department_id_" + id).attr('disabled', false);
                    $("#department_id_" + id).empty();
                    $("#department_id_" + id).append(data);
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });
            return false;
        } else {
            $("#department_id_" + id).empty().append('<option value="">[ Select College First ]</option>');
        }
    }

    function toggleView(obj) {
        if ($('#c' + obj.id).css("display") == 'none') {
            $('#i' + obj.id).attr("src", '<?= Router::url('/img/minus2.gif') ?>');
        } else {
            $('#i' + obj.id).attr("src", '<?= Router::url('/img/plus2.gif') ?>');
        }
        $('#c' + obj.id).toggle("slow");
    }
</script>
