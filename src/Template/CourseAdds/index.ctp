<?php

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

$this->assign('title', __('List Course Adds'));
?>

<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('List Course Adds') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create(null, ['url' => ['action' => 'search']]) ?>
                <?php if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_STUDENT) { ?>
                    <div style="margin-top: -30px;">
                        <hr>
                        <div onclick="toggleViewFullId('ListPublishedCourse')">
                            <?php if (!empty($turn_off_search)) { ?>
                                <?= $this->Html->image('plus2.gif', ['id' => 'ListPublishedCourseImg']) ?>
                                <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Display Filter</span>
                            <?php } else { ?>
                                <?= $this->Html->image('minus2.gif', ['id' => 'ListPublishedCourseImg']) ?>
                                <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Hide Filter</span>
                            <?php } ?>
                        </div>
                        <div id="ListPublishedCourse" style="display:<?= (!empty($turn_off_search) ? 'none' : 'block') ?>">
                            <fieldset style="padding-bottom: 0px;padding-top: 15px;">
                                <div class="row">
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.academic_year', [
                                            'label' => 'Academic Year: ',
                                            'style' => 'width:90%;',
                                            'empty' => ' All Applicable ACY ',
                                            'options' => $academicYearArrayData
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.semester', [
                                            'label' => 'Semester: ',
                                            'style' => 'width:90%;',
                                            'empty' => ' All Semesters ',
                                            'options' => Configure::read('semesters')
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.program_id', [
                                            'label' => 'Program: ',
                                            'style' => 'width:90%;',
                                            'id' => 'program_id_1',
                                            'empty' => ' All Programs ',
                                            'options' => $programs
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.program_type_id', [
                                            'label' => 'Program Type: ',
                                            'style' => 'width:90%;',
                                            'empty' => ' All Program Types ',
                                            'options' => $programTypes
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="large-6 columns">
                                        <?php if ( !empty($colleges)) { ?>
                                            <?= $this->Form->control('Search.college_id', [
                                                'label' => 'College: ',
                                                'style' => 'width:90%;',
                                                'empty' => ' All Applicable Colleges ',
                                                'onchange' => 'getDepartment(1)',
                                                'id' => 'college_id',
                                                'value' => (!empty($default_college_id) && !empty($default_college_id) ? $default_college_id : '')
                                            ]) ?>
                                        <?php } elseif ( !empty($departments)) { ?>
                                            <?= $this->Form->control('Search.department_id', [
                                                'label' => 'Department: ',
                                                'style' => 'width:90%;',
                                                'empty' => ' All Applicable Departments ',
                                                'id' => 'department_id_1',
                                                'value' => (!empty($default_department_id) ? $default_department_id : '')
                                            ]) ?>
                                        <?php } ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.graduated', [
                                            'label' => 'Graduated: ',
                                            'style' => 'width:90%;',
                                            'options' => ['0' => 'No', '1' => 'Yes', '2' => 'All'],
                                            'value' => '0'
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.name', [
                                            'label' => 'Student Name or ID: ',
                                            'placeholder' => 'Optional student name or ID...',
                                            'value' => $name,
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
                                        &nbsp;
                                    </div>
                                    <div class="large-3 columns">
                                        <div style="padding-left: 10%;">
                                            <br>
                                            <h6 class='fs13 text-gray'>Status: </h6>
                                            <?php
                                            $options = [
                                                'accepted' => ' Accepted',
                                                'rejected' => ' Rejected',
                                                'notprocessed' => ' Not Processed',
                                                'auto_rejected' => ' Auto Rejected'
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
                                <?php if (isset($departments) && !empty($departments) && $this->request->getSession()->read('Auth.User.role_id') != ROLE_STUDENT && $this->request->getSession()->read('Auth.User.role_id') != ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE && $this->request->getSession()->read('Auth.User.role_id') != ROLE_DEPARTMENT) { ?>
                                    <div class="row">
                                        <div class="large-6 columns">
                                            <?= $this->Form->control('Search.department_id', [
                                                'label' => 'Department: ',
                                                'style' => 'width:90%;',
                                                'empty' => ' All Departments ',
                                                'id' => 'department_id_1',
                                                'value' => $default_department_id
                                            ]) ?>
                                        </div>
                                        <div class="large-6 columns">
                                        </div>
                                    </div>
                                <?php } ?>
                                <hr>
                                <?= $this->Form->button(__('Search'), ['name' => 'search',
                                    'class' => 'tiny radius button bg-blue']) ?>
                            </fieldset>
                            <?= $this->Form->end() ?>
                            <br>
                            <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) { ?>
                                <div style="margin-top: -10px;">
                                    <hr>
                                    <blockquote>
                                        <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                                        <span style="text-align:justify;" class="fs15 text-gray">The student list you will get here depends on your <b><i>assigned College or Department, assigned Program and Program Types, and with your search conditions</i></b>. You can contact the registrar to adjust permissions assigned to you if you miss your students here.</span>
                                        <?php if (empty($this->request->getData('Search.status')) || (!empty($this->request->getData('Search.status')) && $this->request->getData('Search.status') != 'auto_rejected')) { ?>
                                            <br><br>
                                            <span style="text-align:justify;" class="fs15 text-gray">You can view auto rejected student course add requests by adjusting the <b>Status</b> filter to <b>Auto Rejected</b> and check the auto rejection reason by clicking <b>'+'</b> icon for each rejected course add request. <br><br>You can also approve auto rejected course add requests for <b style="text-decoration: underline;">graduating class students</b> if: <ol class="fs15 text-gray" style="padding-top: 10px;"> <li>Grade is not submitted for the course</li> <li>The student must have at least one course registration in the requested academic year and semester</li> </ol> <b style="padding-top: 10px;">and the sum of current load of the student and the requested course add credit is below the allowed maximum Credit/ECTS specified in the senate legislation</b> </span>
                                        <?php } ?>
                                    </blockquote>
                                </div>
                            <?php } else { ?>
                                <?php if (empty($this->request->getData('Search.status')) || (!empty($this->request->getData('Search.status'))
                                        && $this->request->getData('Search.status') != 'auto_rejected')) { ?>
                                    <div style="margin-top: -10px;">
                                        <hr>
                                        <blockquote>
                                            <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                                            <span style="text-align:justify;" class="fs15 text-gray">You can view auto rejected student course add requests by adjusting the <b>Status</b> filter to <b>Auto Rejected</b> and check the auto rejection reason by clicking <b>'+'</b> icon for each rejected course add request. <b><i>You can also approve auto rejected course add requests for graduating class students if the sum of current load of the student and the requested course add credit is below the allowed maximum credit/ECTS specified in the senate legislation</i></b></span>
                                        </blockquote>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                    <hr>
                <?php } else { ?>
                    <div style="margin-top: -30px;"><hr></div>
                <?php } ?>
                <?php if (!empty($courseAdds)) { ?>
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
                                <?= $this->request->getSession()->read('Auth.User.role_id') == ROLE_STUDENT ? '<td class="center"></td>' : '' ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $start = $this->Paginator->counter(['format' => '{{start}}']);
                            $grade_submitted_for_course = 0;
                            foreach ($courseAdds as $courseAdd) {
                                ?>
                                <tr>
                                    <td class="center" onclick="toggleView(this)" id="<?= $count ?>">
                                        <?= $this->Html->image('plus2.gif', ['id' => 'i' . $count]) ?></td>
                                    <td class="center"><?= $start++ ?></td>
                                    <td class="vcenter"><?= $this->Html->link($courseAdd['student']['full_name'],
                                            ['controller' => 'Students', 'action' => 'studentAcademicProfile',
                                                $courseAdd['student']['id']]) ?></td>
                                    <td class="center"><?= (strtolower(trim($courseAdd['student']['gender']))
                                        == 'male' ? 'M' : (strtolower(trim($courseAdd['student']['gender']))
                                        == 'female' ? 'F' : '')) ?></td>
                                    <td class="center"><?= $courseAdd['student']['studentnumber'] ?></td>
                                    <td class="center"><?= !empty($courseAdd['id']) ? $courseAdd['academic_year'] : 'N/A' ?></td>
                                    <td class="center"><?= !empty($courseAdd['id']) ? $courseAdd['semester'] : 'N/A' ?></td>
                                    <td class="center"><?= !empty($courseAdd['year_level']['name']) ? $courseAdd['year_level']['name'] : 'Pre/1st' ?></td>
                                    <td class="center"><?= !empty($courseAdd['published_course']['course'])
                                        && !empty($courseAdd['published_course']['course']['id']) ? $this->Html->link(trim(str_replace(' ',
                                            ' ', $courseAdd['published_course']['course']['course_title'])),
                                            ['controller' => 'Courses', 'action' => 'view', $courseAdd['published_course']['course']['id']]) : 'N/A' ?></td>
                                    <td class="center"><?= !empty($courseAdd['published_course']['course'])
                                        && !empty($courseAdd['published_course']['course']['id']) ?
                                            $courseAdd['published_course']['course']['credit'] : 'N/A' ?></td>
                                    <td class="center">
                                        <?php
                                        if (!empty($this->request->getData('Search.academic_year'))
                                            && !empty($this->request->getData('Search.semester'))
                                            && $this->request->getData('Search.academic_year') == $courseAdd['academic_year']
                                            && $this->request->getData('Search.semester') == $courseAdd['semester']) {
                                            $courseTitleWithCredit = !empty($courseAdd['published_course']['course']['course_title']) ?
                                                $courseAdd['published_course']['course']['course_title'] . ' (' .
                                                $courseAdd['published_course']['course']['course_code'] . ') course with ' .
                                                $courseAdd['published_course']['course']['credit'] . ' ' .
                                                (str_contains($courseAdd['published_course']['course']['curriculum']['type_credit'],
                                                    'ECTS') ? 'ECTS' : 'Credit') : '';
                                            $credit_type = !empty($courseAdd['student']['curriculum']['type_credit']) ?
                                                (str_contains($courseAdd['student']['curriculum']['type_credit'], 'ECTS') ?
                                                    'ECTS' : 'Credit') : 'Credit';
                                            $student_full_name = !empty($courseAdd['student']['full_name']) ?
                                                $courseAdd['student']['full_name'] . ' (' . $courseAdd['student']['studentnumber'] . ')' : '';
                                            $enable_auto_rejection_override = false;
                                        }
                                        if ($courseAdd['department_approval'] == 1) {
                                            if (!$courseAdd['auto_rejected']) {
                                                echo '<span class="accepted">Accepted</span>';
                                            } else {
                                                echo '<span class="rejected">Auto Reject Override (Department)</span>';
                                            }
                                        } else {
                                            if (!empty($courseAdd['student']['graduated']) && $courseAdd['student']['graduated'] == 1) {
                                                echo '<span class="on-process">Graduated Student</span>';
                                            } elseif (!$courseAdd['auto_rejected']) {
                                                if (is_null($courseAdd['department_approval'])) {
                                                    if ((isset($courseAdd['published_course']['section']['archive'])
                                                            && $courseAdd['published_course']['section']['archive'])
                                                        || (isset($allowed_academic_years_for_add_drop) &&
                                                            !empty($allowed_academic_years_for_add_drop) &&
                                                            !in_array($courseAdd['academic_year'],
                                                                $allowed_academic_years_for_add_drop))) {
                                                        echo '<span class="rejected">Expired</span>';
                                                    } else {
                                                        echo '<span class="text-gray"><i>Waiting Decision</i></span>';
                                                    }
                                                } elseif ($courseAdd['CourseAdd']['department_approval'] == 0) {
                                                    echo '<span class="rejected">Rejected</span>';
                                                }
                                            } else {
                                                echo '<span class="rejected">Auto Rejected (System)</span>';
                                                if (!empty($this->request->getData('Search.academic_year'))
                                                    && !empty($this->request->getData('Search.semester'))
                                                    && $this->request->getData('Search.academic_year') == $courseAdd['academic_year']
                                                    && $this->request->getData('Search.semester') == $courseAdd['CourseAdd']['semester']) {
                                                    $confirmMessage = __('Are you sure you want to cancel the
                                                    auto rejected course add of %s for %s? Cancelling this auto rejection will
                                                     auto approve the course add request. Are you sure you want to cancel the
                                                      auto rejection anyway?', $student_full_name, $courseTitleWithCredit);
                                                    if (!empty($current_load) && !empty($courseAdd['student']['id'])
                                                        && !empty($current_load[$courseAdd['student']['id']])) {
                                                        echo '<br>Load: ' . $current_load[$courseAdd['student']['id']] . ' ' . $credit_type;
                                                        if (!empty($graduatingClassStudent) && $graduatingClassStudent[$courseAdd['student']['id']]
                                                            && $credit_type == 'Credit') {
                                                            if (!empty($courseAdd['published_course']['course']['credit'])
                                                                && $courseAdd['published_course']['Course']['credit'] >= 0
                                                                && (($current_load[$courseAdd['student']['id']] +
                                                                        $courseAdd['published_course']['course']['credit'])
                                                                    <= (DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER +
                                                                        ADDITIONAL_CREDIT_ALLOWED_FOR_GRADUATING_STUDENTS))) {
                                                                $enable_auto_rejection_override = true;
                                                            }
                                                        } elseif (!empty($graduatingClassStudent)
                                                            && $graduatingClassStudent[$courseAdd['student']['id']] && $credit_type == 'ECTS') {
                                                            if (!empty($courseAdd['published_course']['course']['credit'])
                                                                && $courseAdd['published_course']['course']['credit'] >= 0
                                                                && (($current_load[$courseAdd['student']['id']] +
                                                                        $courseAdd['published_course']['course']['credit'])
                                                                    <= ((int) ((DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER * CREDIT_TO_ECTS)
                                                                        + ADDITIONAL_ECTS_ALLOWED_FOR_GRADUATING_STUDENTS)))) {
                                                                $enable_auto_rejection_override = true;
                                                            }
                                                        }
                                                    }
                                                    if (!empty($courseAdd['student']['graduated']) && $courseAdd['student']['graduated'] == 0
                                                        && !empty($courseAdd['published_course']['Course']['id'])
                                                        && $this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT
                                                        && !empty($courseAdd['published_course']['id']) &&
                                                        TableRegistry::getTableLocator()->get('ExamGrades')->
                                                        isGradeSubmittedForPublishedCourse($courseAdd['published_course']['id']) == 0) {
                                                        if ($enable_auto_rejection_override &&
                                                            ENABE_AUTO_COURSE_ADD_REJECTION_OVERRIDE_FOR_DEPARTMENTS == 1) {
                                                            echo '<br>' . $this->Html->link(__('[Approve Anyway]'),
                                                                    ['action' => 'approveAutoRejectedCourseAdd',
                                                                        $courseAdd['CourseAdd']['id']], ['escape' => false,
                                                                        'onclick' => 'return confirm(\'' . $confirmMessage . '\');']);
                                                        }
                                                    } elseif (isset($courseAdd['student']['graduated']) &&
                                                        $courseAdd['student']['graduated'] == 0 &&
                                                        TableRegistry::getTableLocator()->get('ExamGrades')->
                                                        isGradeSubmittedForPublishedCourse($courseAdd['published_course']['id'])) {
                                                        echo '<br><span class="on-process">Grade Submitted</span>';
                                                        $grade_submitted_for_course++;
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="center">
                                        <?php
                                        if (!empty($courseAdd['student']['graduated']) && $courseAdd['student']['graduated'] == 1) {
                                            echo '<span class="on-process">Graduated Student</span>';
                                        } elseif (!$courseAdd['auto_rejected']) {
                                            if ($courseAdd['department_approval'] == 1) {
                                                if (is_null($courseAdd['registrar_confirmation'])) {
                                                    if ((!empty($courseAdd['published_course']['section']['archive']) &&
                                                            $courseAdd['published_course']['section']['archive'])
                                                        || (!empty($allowed_academic_years_for_add_drop) &&
                                                            !empty($allowed_academic_years_for_add_drop) &&
                                                            !in_array($courseAdd['academic_year'], $allowed_academic_years_for_add_drop))) {
                                                        echo '<span class="rejected">Expired</span>';
                                                    } else {
                                                        echo '<span class="text-gray"><i>Waiting Decision</i></span>';
                                                    }
                                                } elseif ($courseAdd['registrar_confirmation'] == 1) {
                                                    echo '<span class="accepted">Accepted</span>';
                                                } elseif ($courseAdd['registrar_confirmation'] == 0) {
                                                    echo '<span class="rejected">Rejected</span>';
                                                }
                                            }
                                        } else {
                                            echo '<span class="rejected">Auto Rejected (System)</span>';
                                            if (!empty($this->request->getData('Search.academic_year'))
                                                && !empty($this->request->getData('Search.semester')) &&
                                                $this->request->getData('Search.academic_year') == $courseAdd['academic_year']
                                                && $this->request->getData('Search.semester') == $courseAdd['semester']) {
                                                $confirmMessage = __('Are you sure you want to cancel the
                                                auto rejected course add of %s for %s? Cancelling this auto rejection will
                                                auto confirm the course add request. Are you sure you want to cancel the auto rejection anyway?',
                                                    $student_full_name, $courseTitleWithCredit);
                                                if (!empty($current_load) && !empty($courseAdd['student']['id'])
                                                    && !empty($current_load[$courseAdd['student']['id']])) {
                                                    echo '<br>Load: ' . $current_load[$courseAdd['student']['id']] . ' ' . $credit_type;
                                                    if (!empty($graduatingClassStudent) && $graduatingClassStudent[$courseAdd['student']['id']]
                                                        && $credit_type == 'Credit') {
                                                        if (!empty($courseAdd['published_course']['course']['credit'])
                                                            && $courseAdd['published_course']['course']['credit'] >= 0
                                                            && (($current_load[$courseAdd['student']['id']] +
                                                                    $courseAdd['published_course']['course']['credit'])
                                                                <= (DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER +
                                                                    ADDITIONAL_CREDIT_ALLOWED_FOR_GRADUATING_STUDENTS))) {
                                                            $enable_auto_rejection_override = true;
                                                        }
                                                    } elseif (!empty($graduatingClassStudent)
                                                        && $graduatingClassStudent[$courseAdd['student']['id']] && $credit_type == 'ECTS') {
                                                        if (!empty($courseAdd['published_course']['course']['credit'])
                                                            && $courseAdd['published_course']['course']['credit'] >= 0
                                                            && (($current_load[$courseAdd['student']['id']] +
                                                                    $courseAdd['published_course']['course']['credit'])
                                                                <= ((int) ((DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER * CREDIT_TO_ECTS)
                                                                    + ADDITIONAL_ECTS_ALLOWED_FOR_GRADUATING_STUDENTS)))) {
                                                            $enable_auto_rejection_override = true;
                                                        }
                                                    }
                                                }
                                                if (!empty($courseAdd['published_course']['add']) && !($courseAdd['published_course']['add'])
                                                    && !empty($courseAdd['published_course']['section']['archive'])
                                                    && !($courseAdd['published_course']['section']['archive'])
                                                    && isset($courseAdd['student']['graduated']) && $courseAdd['student']['graduated'] == 0
                                                    && isset($courseAdd['published_course']['Course']['id']) &&
                                                    $this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR &&
                                                    $this->request->getSession()->read('Auth.User.is_admin') != 1 &&
                                                    isset($courseAdd['PublishedCourse']['id']) && !TableRegistry::getTableLocator()->get('ExamGrades')->isGradeSubmittedForPublishedCourse($courseAdd['PublishedCourse']['id'])) {
                                                    if ($enable_auto_rejection_override && ENABE_AUTO_COURSE_ADD_REJECTION_OVERRIDE_FOR_REGISTRAR) {
                                                        echo '<br>' . $this->Html->link(__('[Confirm Anyway]'),
                                                                ['action' => 'approveAutoRejectedCourseAdd', $courseAdd['id']],
                                                                ['escape' => false, 'onclick' => 'return confirm(\'' . $confirmMessage . '\');']);
                                                    }
                                                } elseif (!empty($courseAdd['student']['graduated']) && $courseAdd['student']['graduated'] == 0
                                                    && !empty($courseAdd['published_course']['course']['id'])
                                                    && $this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR
                                                    && $this->request->getSession()->read('Auth.User.is_admin') == 1
                                                    && !empty($courseAdd['published_course']['id'])) {
                                                    echo '<br>' . $this->Html->link(__('[Confirm Anyway]'),
                                                            ['action' => 'approveAutoRejectedCourseAdd', $courseAdd['id']],
                                                            ['escape' => false, 'onclick' => 'return confirm(\'' . $confirmMessage . '\');']);
                                                    if (TableRegistry::getTableLocator()->get('ExamGrades')->isGradeSubmittedForPublishedCourse($courseAdd['published_course']['id'])) {
                                                        echo '<br><span class="on-process">Grade Submitted</span><br>';
                                                        $grade_submitted_for_course++;
                                                    }
                                                } elseif (isset($courseAdd['Student']['graduated']) && $courseAdd['Student']['graduated'] == 0 &&
                                                    TableRegistry::getTableLocator()->get('ExamGrades')->isGradeSubmittedForPublishedCourse($courseAdd['published_course']['id'])) {
                                                    echo '<br><span class="on-process">Grade Submitted</span>';
                                                    $grade_submitted_for_course++;
                                                }
                                            }
                                        }
                                        ?>
                                        <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_STUDENT) { ?>
                                    <td class="center">
                                        <?php
                                        if (isset($courseAdd['published_course']['add']) && !($courseAdd['PublishedCourse']['add'])
                                            && isset($courseAdd['published_course']['section']['archive'])
                                            && !($courseAdd['published_course']['section']['archive']) &&
                                            is_null($courseAdd['department_approval']) &&
                                            is_null($courseAdd['registrar_confirmation']) &&
                                            !($courseAdd['auto_rejected']) && !($courseAdd['cron_job'])
                                            && !TableRegistry::getTableLocator()->get('ExamGrades')->isGradeSubmittedForPublishedCourse($courseAdd['published_course']['id'])) {
                                            $confirmMessage = __('Are you sure you want to cancel course add of %s, you requested previously?
                                            Cancelling this course add will delete your course add request and you can not add this course again
                                            if course add deadline is passed. Are you sure you want cancel this course add anyway?', $courseTitleWithCredit);
                                            echo $this->Html->link(__('Cancel Add'), ['action' => 'delete',
                                                $courseAdd['id']], ['escape' => false, 'onclick' => 'return confirm(\'' . $confirmMessage . '\');']);
                                        }
                                        ?>
                                    </td>
                                    <?php } ?>
                                    </td>
                                </tr>
                                <tr id="c<?= $count++ ?>" style="display:none">
                                    <td colspan="2" style="background-color: white;"></td>
                                    <td colspan="<?= $this->request->getSession()->read('Auth.User.role_id') == ROLE_STUDENT ? '11' : '10' ?>" style="background-color: white;">
                                        <?php if (isset($courseAdd['published_course']['course'])
                                            && !empty($courseAdd['published_course']['course']['id'])) { ?>
                                            <table cellpadding="0" cellspacing="0" class="table">
                                                <tbody>
                                                <tr>
                                                    <td class="vcenter" style="background-color: white;">
                                                        <span class="fs13 text-gray" style="font-weight: bold">Added from Section: </span>
                                                        <?= ($courseAdd['published_course']['section']['name'] . ' (' .
                                                            (isset($courseAdd['published_course']['section']['year_level']['name'])
                                                                ? $courseAdd['published_course']['section']['year_level']['name'] : 'Pre/1st') .
                                                            ', ' . $courseAdd['published_course']['section']['academicyear'] . ')') ?>
                                                        &nbsp;
                                                        <?= ($courseAdd['published_course']['section']['archive'] ?
                                                            '<span class="rejected"> (Archived) </span>' :
                                                            '<span class="accepted"> (Active) </span>') ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter">
                                                        <span class="fs13 text-gray" style="font-weight: bold">Section/Course Curriculum: </span>
                                                        <?= (isset($courseAdd['published_course']['section']['curriculum']['name']) ?
                                                            $courseAdd['published_course']['section']['curriculum']['name'] . ' - ' .
                                                            $courseAdd['published_course']['section']['curriculum']['year_introduced'] :
                                                            (isset($courseAdd['published_course']['course']['curriculum']['name']) ?
                                                                $courseAdd['published_course']['course']['curriculum']['name'] . ' - ' .
                                                                $courseAdd['published_course']['course']['curriculum']['year_introduced'] : '')) ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter" style="background-color: white;">
                                                        <span class="fs13 text-gray" style="font-weight: bold">Section Department/College: </span>
                                                        <?= (isset($courseAdd['published_course']['department']['name']) ?
                                                            $courseAdd['published_course']['department']['name'] . ' (' .
                                                            $courseAdd['published_course']['department']['College']['name'] . ')' :
                                                            (isset($courseAdd['published_course']['college']['name']) ? 'Pre/Freshman (' .
                                                                $courseAdd['published_course']['college']['name'] . ')' : '')) ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter">
                                                        <span class="fs13 text-gray" style="font-weight: bold">Course Given By: </span>
                                                        <?= (isset($courseAdd['published_course']['given_by_department']['name'])
                                                            ? $courseAdd['published_course']['given_by_department']['name'] :
                                                            'Not Assigned Yet') ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter" style="background-color: white;">
                                                        <span class="fs13 text-gray" style="font-weight: bold">Student Attached Curriculum: </span>
                                                        <?= (!empty($courseAdd['student']['curriculum']['name']) ?
                                                            $courseAdd['student']['curriculum']['name'] . ' - ' .
                                                            $courseAdd['Student']['curriculum']['year_introduced'] :
                                                            '<span class="Rejected">No Curriculum Attachment</span>') ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter">
                                                        <span class="fs13 text-gray" style="font-weight: bold">
                                                            Student Graduated: </span>
                                                        <?= ($courseAdd['student']['graduated'] ?
                                                            '<span class="rejected"> Yes </span>' :
                                                            '<span class="accepted"> No </span>') ?>
                                                    </td>
                                                </tr>
                                                <?= (!empty($courseAdd['minute_number']) ? '<tr><td class="vcenter" style="background-color: white;"><span class="fs13 text-gray" style="font-weight: bold">Minute Number: </span>' . $courseAdd['CourseAdd']['minute_number'] . '</td></tr>' : '') ?>
                                                <tr>
                                                    <td class="vcenter" style="background-color: white;">
                                                        <span class="fs13 text-gray" style="font-weight: bold">
                                                            Course Add Requested: </span>
                                                        <?= $this->Time->format($courseAdd['created'],
                                                            'MMMM d, yyyy h:mm:ss a') ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="vcenter">
                                                                <span class="fs13 text-gray" style="font-weight: bold">Course Add
                                                                    <?= (is_null($courseAdd['department_approval']) && is_null($courseAdd['registrar_confirmation']) ?
                                                                        ' Approval: </span> Pending ... ' : (($courseAdd['auto_rejected'] == 0 &&
                                                                        (($courseAdd['department_approval'] == 1 && is_null($courseAdd['registrar_confirmation'])) ||
                                                                            $courseAdd['registrar_confirmation'] == 1) ? ' Approved' .
                                                                            ($courseAdd['registrar_confirmation'] == 1 ?
                                                                                ' By Registrar' : ' By Department') . ': </span> ' .
                                                                            $this->Time->format($courseAdd['modified'], 'MMMM d, yyyy h:mm:ss a') :
                                                                            ((($courseAdd['auto_rejected'] == 1 || $courseAdd['department_approval'] == 0 ||
                                                                                $courseAdd['registrar_confirmation'] == 0) ? '' . ($courseAdd['auto_rejected'] == 1 ?
                                                                                    ' Auto Rejected By System' : ($courseAdd['department_approval'] == 0 ?
                                                                                        'Rejected By Department' : 'Rejected By Registrar')) . ': </span> ' .
                                                                        $this->Time->format($courseAdd['modified'], 'MMMM d, yyyy h:mm:ss a') :
                                                                                ' Approval: </span> Pending ... '))))) ?>
                                                    </td>
                                                </tr>
                                                <?= (!empty($courseAdd['CourseAdd']['reason']) ? '<tr><td class="vcenter" style="background-color: white;">
<span class="fs13 text-gray" style="font-weight: bold">Reason: </span>' . $courseAdd['reason'] . '</td></tr>' : '') ?>
                                                </tbody>
                                            </table>
                                        <?php } else { ?>
                                            <span class="rejected">Error: Published Course not found or deleted. Couldn't load Course details!!.</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <?php if ($grade_submitted_for_course != 0) { ?>
                        <div class='info-box' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">Grade Submitted: one or more grade is submitted for a student or students for the published course section, not possible to process your course add request at this time.</div>
                    <?php } ?>
                    <hr>
                    <div class="row">
                        <div class="large-5 columns">

                            <?=$this->Paginator->counter([ 'format' => 'Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total' ]) ?>


                        </div>
                        <div class="large-7 columns">
                            <div class="pagination-centered">
                                <ul class="pagination">
                                    <?= $this->Paginator->prev('<<', ['tag' => 'li'], null, ['class' => 'arrow disabled']) ?>
                                    <?= $this->Paginator->numbers(['separator' => '', 'tag' => 'li']) ?>
                                    <?= $this->Paginator->next('>>', ['tag' => 'li'], null, ['class' => 'arrow disabled']) ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                <?php } else { ?>
                    <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no Course Add in the system in the given criteria.</div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function toggleViewFullId(id) {
        if ($('#' + id).css("display") == 'none') {
            $('#' + id + 'Img').attr("src", '/img/minus2.gif');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append(' Hide Filter');
        } else {
            $('#' + id + 'Img').attr("src", '/img/plus2.gif');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append(' Display Filter');
        }
        $('#' + id).toggle("slow");
    }

    function getDepartment(id) {
        var formData = $("#college_id").val();
        $("#department_id_" + id).empty();
        $("#department_id_" + id).append('<option style="width:90%;">loading...</option>');
        if (formData) {
            $("#department_id_" + id).attr('disabled', true);
            var formUrl = '/departments/getDepartmentCombo/' + formData + '/0/1';
            $.ajax({
                type: 'get',
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
            $('#i' + obj.id).attr("src", '/img/minus2.gif');
        } else {
            $('#i' + obj.id).attr("src", '/img/plus2.gif');
        }
        $('#c' + obj.id).toggle("slow");
    }
</script>
