<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fas fa-check" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Confirm Grade Submission') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create(null, ['url' => ['controller' => 'ExamGrades', 'action' => 'confirm_grade_submission'],
                    'onsubmit' => 'return checkForm(this);']) ?>
                <?php if (!isset($get_list_of_students_with_grade)) {

                    ?>
                    <div style="margin-top: -30px;">
                        <hr>
                        <?php if (empty($turn_off_search)) { ?>
                            <blockquote>
                                <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                                <span style="text-align:justify;" class="fs14 text-gray">This tool will help you to approve grade which was approved by department <b style="text-decoration: underline;"><i>for your final confirmation</i></b>. Only those Grades which was not confirmed previously will get confirmed. You can also leave Program and Program Type filters unchecked or empty to get all grade submissions for the selected academic year</span>
                            </blockquote>
                        <?php } ?>
                        <hr>
                        <div onclick="toggleViewFullId('ListPublishedCourse')">
                            <?php if (!empty($turn_off_search)) {
                                echo $this->Html->image('/img/plus2.gif', ['id' => 'ListPublishedCourseImg']);
                                ?>
                                <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
                            <?php } else {
                                echo $this->Html->image('/img/minus2.gif', ['id' => 'ListPublishedCourseImg']);
                                ?>
                                <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
                            <?php } ?>
                        </div>
                        <div id="ListPublishedCourse" style="display:<?= (!empty($turn_off_search) ? 'none' : 'block') ?>;">
                            <fieldset style="padding-bottom: 0px;padding-top: 20px;">
                                <div class="row">
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.academicyear', [
                                            'label' => 'Academic Year: ',
                                            'type' => 'select',
                                            'style' => 'width: 80%',
                                            'options' => $acyearArrayData,
                                            'required' => true
                                           ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.semester', [
                                            'options' => \Cake\Core\Configure::read('semesters'),
                                            'style' => 'width: 80%',
                                            'label' => 'Semester: ',
                                            'empty' => 'Any Semester'
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <h6 class='fs13 text-gray'>Program: </h6>
                                        <?= $this->Form->control('Search.program_id', [
                                            'id' => 'program_id',
                                            'label' => false,
                                            'type' => 'select',
                                            'multiple' => 'checkbox'
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <h6 class='fs13 text-gray'>Program Type: </h6>
                                        <?= $this->Form->control('Search.program_type_id', [
                                            'id' => 'program_type_id',
                                            'label' => false,
                                            'type' => 'select',
                                            'multiple' => 'checkbox'
                                        ]) ?>
                                    </div>
                                </div>
                                <hr>
                                <?= $this->Form->button(__('Search'), ['name' => 'getCourseNeedsApproval', 'class' => 'tiny radius button bg-blue']) ?>
                            </fieldset>
                        </div>
                    </div>
                    <hr>
                <?php } ?>
                <div class="publishedCourses index">
                    <?php if (isset($grade_submitted_courses_organized_by_published_course)
                    && !empty($grade_submitted_courses_organized_by_published_course))
                    { ?>
                    <hr>
                    <h6 class="fs14 text-gray">List of Exam Grades submitted for
                        <?= h($this->getRequest()->getData('Search.academicyear')) ?>
                        academic year <?= (empty($this->getRequest()->getData('Search.semester')) ? '' : (' ' . ($this->getRequest()->getData('Search.semester') == 'I' ? '1st semester' : ($this->getRequest()->getData('Search.semester') == 'II' ? '2nd semester' : ($this->getRequest()->getData('Search.semester') == 'III' ? '3rd semester' : h($this->getRequest()->getData('Search.semester')) . ' semester'))))) ?>, which are approved by the department and awaiting your confirmation.</h6>
                    <hr>
                    <?php foreach ($grade_submitted_courses_organized_by_published_course as $dep => $depvalue) {
                        foreach ($depvalue as $pk => $pv) {
                            if (!empty($pk)) {
                                foreach ($pv as $ptk => $ptv) {
                                    if (!empty($ptk)) {
                                        foreach ($ptv as $yk => $yv) {
                                            if (!empty($yv)) {
                                                foreach ($yv as $section_name => $section_value) { ?>
                                                    <br>
                                                    <div style="overflow-x:auto;">
                                                        <table cellpadding="0" cellspacing="0" class="table">
                                                            <thead>
                                                            <tr>
                                                                <td colspan="7" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                                    <span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?= h($section_name) . ' ' . (isset($yk) ? ' (' . h($yk) . ')' : ' (Pre/1st)') ?></span>
                                                                    <br>
                                                                    <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
                                                                            <?= (is_numeric($dep) && $dep > 0 ? h($departmentsss[$dep]) : (count(explode('c~', $dep)) > 1 ? h($collegesss[explode('c~', $dep)[1]]) . ' - Pre/1st' : '')) . ' &nbsp; | &nbsp; ' . h($pk) . ' &nbsp; | &nbsp; ' . h($ptk) ?><br>
                                                                            <?= h($this->getRequest()->getData('Search.academicyear')) . (empty($this->getRequest()->getData('Search.semester')) ? '' : (' &nbsp; | &nbsp; ' . ($this->getRequest()->getData('Search.semester') == 'I' ? '1st Semester' : ($this->getRequest()->getData('Search.semester') == 'II' ? '2nd Semester' : ($this->getRequest()->getData('Search.semester') == 'III' ? '3rd Semester' : h($this->getRequest()->getData('Search.semester')) . ' Semester'))))) ?>
                                                                        </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="center">#</th>
                                                                <th class="vcenter">Course Title</th>
                                                                <th class="center">Course Code</th>
                                                                <th class="center"><?= (count(explode('ECTS',
                                                                        array_values($section_value)[0]['course']['curriculum']['type_credit']))
                                                                    >= 2 ? 'ECTS' : 'Credit') ?></th>
                                                                <th class="center">L T L</th>
                                                                <th class="center">Instructor</th>
                                                                <th class="center">Action</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php $sn_count = 1;
                                                            foreach ($section_value as $pub_id => $publishedCourse) {
                                                                if (!empty($publishedCourse)) {
                                                                    ?>
                                                                    <tr>
                                                                        <td class="center"><?= $sn_count++ ?></td>
                                                                        <td class="vcenter"><?= $this->Html->link(h($publishedCourse['course']['course_title']),
                                                                                ['controller' => 'Courses', 'action' => 'view', $publishedCourse['course']['id']]) ?></td>
                                                                        <td class="center"><?= h($publishedCourse['course']['course_code']) ?></td>
                                                                        <td class="center"><?= h($publishedCourse['course']['credit']) ?></td>
                                                                        <td class="center"><?= h($publishedCourse['course']['course_detail_hours']) ?></td>
                                                                        <td class="center"><?= (isset($publishedCourse['course_instructor_assignments'])
                                                                                && count($publishedCourse['course_instructor_assignments']) > 0)
                                                                                ? h($publishedCourse['course_instructor_assignments'][0]['staff']['Title']['title'] . '. ' .
                                                                                    $publishedCourse['course_instructor_assignments'][0]['staff']['full_name']) : '' ?></td>
                                                                        <td class="center"><?= $this->Html->link(__('Review & Confirm'),
                                                                                ['action' => 'confirm_grade_submission', $publishedCourse['id']]) ?></td>
                                                                    </tr>
                                                                <?php }
                                                            } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br>
                                                <?php }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        } ?>
                    <?php } ?>
                    <?php

                    if (isset($get_list_of_students_with_grade) && !empty($get_list_of_students_with_grade)) {
                        if (count($get_list_of_students_with_grade) > 0) {

                            $freshman_program = isset($publishedCourseDetail['department']['name']) ? false : true;
                            $approver = $freshman_program ? 'freshman program' : 'department';
                            $approver_c = $freshman_program ? 'Freshman Program' : 'Department';
                            ?>
                            <div class="fs14" style="text-align: justify;">
                                This grade is submitted by <u><b class="text-black"><?= (isset($instructorDetail['staff'])
                                            && !empty($instructorDetail['staff'])) ?
                                            h($instructorDetail['staff']['title']['title'] . '. ' .
                                                $instructorDetail['staff']['full_name'] . ' (' .
                                                $instructorDetail['staff']['position']['position'] . ')') :
                                            h($freshman_program ? 'the freshman program (college)' :
                                                'the department') ?></b></u> for <u><b class="text-black">
                                        <?= h($publishedCourseDetail['course']['course_title'] . ' (' .
                                            $publishedCourseDetail['course']['course_code'] . ')') ?></b></u>
                                course and waiting for your confirmation. Please make sure that the submitted
                                course exam grade is correct as <strong>your decision is final</strong>.
                                If you reject the grade, then it will be returned back to the <?= h($approver) ?>
                                for re-consideration. <b style="text-decoration: underline;"><i> If you accept the grade, it will become permanent
                                        grade and it can only be changed either through grade change process or makeup exam.</i></b>
                            </div>
                            <hr>
                            <div style="overflow-x:auto;">
                                <table cellpadding="0" cellspacing="0" class="table">
                                    <tr>
                                        <td style="width:50%;"><strong class="text-gray"><?= (isset($publishedCourseDetail['department']['type']) && !empty($publishedCourseDetail['department']['type']) ? h($publishedCourseDetail['department']['type'] . ': ') : 'Department: ') ?></strong> <?= h($publishedCourseDetail['department']['name'] ?? 'Pre/Freshman') ?></td>
                                        <td style="width:50%;"><strong class="text-gray"> Section:</strong> <?= h($publishedCourseDetail['section']['name']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong class="text-gray">Course:</strong> <?= h($publishedCourseDetail['course']['course_title'] . ' (' . $publishedCourseDetail['course']['course_code'] . ')') ?></td>
                                        <td><strong class="text-gray"><?= (count(explode('ECTS', $publishedCourseDetail['course']['curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit') ?>:</strong> <?= h($publishedCourseDetail['course']['credit']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong class="text-gray">Program:</strong> <?= h($publishedCourseDetail['program']['name'] . ' / ' . $publishedCourseDetail['program_type']['name']) ?></td>
                                        <td><strong class="text-gray"> Year Level:</strong> <?= h($publishedCourseDetail['year_level']['name'] ?? '1st') ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong class="text-gray">Academic Year:</strong> <?= h($publishedCourseDetail['academic_year']) ?></td>
                                        <td><strong class="text-gray">Semester:</strong> <?= h($publishedCourseDetail['semester']) ?></td>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <div style="overflow-x:auto;">
                                <table cellpadding="0" cellspacing="0" class="table">
                                    <thead>
                                    <tr>
                                        <td style="width:5%" class="center">&nbsp;</td>
                                        <td style="width:25%" class="vcenter">Student Name</td>
                                        <td style="width:15%" class="center">Student ID</td>
                                        <td style="width:10%" class="center">Grade</td>
                                        <td style="width:50%" class="center">Status</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $count = 1;
                                    $frequency_count = [];
                                    $st_count = 0;
                                    $enable_approve_button = 0;
                                    $registrar_regected_grades = 0;
                                    $department_rejected_grades_back_to_instructor = 0;
                                    $department_rejected_grades_back_to_registrar = 0;
                                    $consequetive_rejected_grades_by_the_registrar = 0;
                                    if (isset($get_list_of_students_with_grade['register'])
                                        && !empty($get_list_of_students_with_grade['register'])) {
                                        foreach ($get_list_of_students_with_grade['register'] as $key => $student) {
                                            $st_count++;
                                            ?>
                                            <tr <?= (isset($student['exam_grades'])
                                            && !empty($student['exam_grades'])
                                            && $student['exam_grades'][0]['registrar_approval'] == null
                                            && !is_null($student['exam_grades'][0]['department_approval'])
                                                ? 'style="font-weight:bold;"' : '') ?>>
                                                <td class="center" onclick="toggleView(this)" id="<?= $st_count ?>"><?= $this->Html->image('/img/plus2.gif', ['id' => 'i' . $st_count]) ?></td>
                                                <td class="vcenter"><?= h($student['student']['first_name'] . ' ' . $student['student']['middle_name'] . ' ' . $student['student']['last_name']) ?></td>
                                                <td class="center"><?= h($student['student']['studentnumber']) ?></td>
                                                <td class="center">
                                                    <?php
                                                    if (isset($student['exam_grades']) && !empty($student['exam_grades'])) {
                                                        $frequency_count[] = $student['exam_grades'][0]['grade'];
                                                        echo h($student['exam_grades'][0]['grade']);
                                                        if ($student['exam_grades'][0]['registrar_approval'] == null
                                                            && !is_null($student['exam_grades'][0]['department_approval'])) {
                                                            echo $this->Form->hidden('ExamGrade.' . $count . '.id', ['value' => $student['exam_grades'][0]['id']]);
                                                            $enable_approve_button++;
                                                        } elseif ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == -1 && is_null($student['exam_grades'][0]['registrar_approval'])) {
                                                            $department_rejected_grades_back_to_registrar++;
                                                        } elseif ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == -1 && $student['exam_grades'][0]['registrar_approval'] == -1) {
                                                            $consequetive_rejected_grades_by_the_registrar++;
                                                        } elseif (($student['exam_grades'][0]['department_approval'] == -1 && is_null($student['exam_grades'][0]['registrar_approval'])) || ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grade'][0]['department_approval'] == 1) || ($student['exam_grade'][0]['department_reply'] == 1 && $student['exam_grade'][0]['department_approval'] == 1 && $student['exam_grade'][0]['registrar_approval'] == -1)) {
                                                            $department_rejected_grades_back_to_instructor++;
                                                            if ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == 1 && $student['exam_grades'][0]['registrar_approval'] == -1) {
                                                                $registrar_regected_grades++;
                                                            }
                                                        }
                                                    } else {
                                                        echo '**';
                                                    }
                                                    ?>
                                                </td>
                                                <td class="center">
                                                    <?php
                                                    if (!isset($student['exam_grades']) || empty($student['exam_grades'])) {
                                                        echo '<span class="text-gray" style="font-weight:normal;"><i>Waiting for Grade Submission</i></span>';
                                                    } elseif ($student['exam_grades'][0]['registrar_approval'] == 1) {
                                                        echo '<span class="accepted" style="font-weight:normal;">Accepted</span>';
                                                    } elseif ($student['exam_grades'][0]['department_approval'] == null) {
                                                        echo '<span class="on-process" style="font-weight:normal;">Pending for ' . h($approver) . ' approval</span>';
                                                    } elseif ($student['exam_grades'][0]['department_approval'] == -1) {
                                                        if ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == -1 && is_null($student['exam_grades'][0]['registrar_approval'])) {
                                                            echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for registrar\'s response for ' . h($approver) . '\'s rejection of previously rejected grade by the registrar.</span>', 50, ['ellipsis' => '...', 'exact' => true, 'html' => true]);
                                                        } elseif ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == -1 && $student['exam_grades'][0]['registrar_approval'] == -1) {
                                                            echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for ' . h($approver) . '\'s response for registrar\'s consequetive rejections(two or more times).</span>', 50, ['ellipsis' => '...', 'exact' => true, 'html' => true]);
                                                        } elseif (($student['exam_grades'][0]['department_approval'] == -1 && is_null($student['exam_grades'][0]['registrar_approval'])) || ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == 1) || ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == 1 && $student['exam_grades'][0]['registrar_approval'] == -1)) {
                                                            if ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == 1 && $student['exam_grades'][0]['registrar_approval'] == -1) {
                                                                echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for Instructor grade re-submission in response for ' . h($approver) . '\'s acceptance of previously rejected grade by the registrar.</span>', 50, ['ellipsis' => '...', 'exact' => true, 'html' => true]);
                                                            } else {
                                                                echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for Instructor grade re-submission in response for ' . h($approver) . '\'s rejection of previously submitted grade by the instructor.</span>', 50, ['ellipsis' => '...', 'exact' => true, 'html' => true]);
                                                            }
                                                        } else {
                                                            echo '<span class="rejected" style="font-weight:normal;">Grade is rejected by the ' . h($approver) . '</span>';
                                                        }
                                                    } else {
                                                        if ($student['exam_grades'][0]['registrar_approval'] == null) {
                                                            echo '<span class="on-process" style="font-weight:normal;">Approved by ' . h($approver) . ', pending for registrar confirmation</span>';
                                                        } elseif ($student['exam_grades'][0]['registrar_approval'] == 1) {
                                                            echo '<span class="accepted" style="font-weight:normal;">Accepted</span>';
                                                        } elseif ($student['exam_grades'][0]['registrar_approval'] == -1) {
                                                            echo '<span class="rejected" style="font-weight:normal;">Approved by ' . h($approver) . ', but rejected by registrar</span>';
                                                        } else {
                                                            echo '<span class="accepted" style="font-weight:normal;">Accepted</span>';
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr id="c<?= $st_count ?>" style="display:none">
                                                <td style="background-color: white;">&nbsp;</td>
                                                <td style="background-color: white;" colspan="4">
                                                    <?php if (isset($student['MakeupExam']) && isset($student['ExamGradeChange']) && count($student['ExamGradeChange']) > 0) { ?>
                                                        <table cellpadding="0" cellspacing="0" class="table">
                                                            <tr>
                                                                <td style="width:28%; font-weight:bold; background-color: white;">Makeup Exam Minute Number:</td>
                                                                <td style="width:72%; background-color: white;"><?= h($student['ExamGradeChange'][0]['minute_number']) ?></td>
                                                            </tr>
                                                        </table>
                                                        <br>
                                                    <?php }
                                                    $register_or_add = 'gh';
                                                    $grade_history = isset($student['ExamGradeHistory']) ? $student['ExamGradeHistory'] : [];
                                                    $this->set(compact('register_or_add', 'grade_history', 'freshman_program'));
                                                    ?>
                                                    <table cellpadding="0" cellspacing="0" class="table">
                                                        <tr>
                                                            <td style="vertical-align:top; background-color: white;"><?= $this->element('registered_or_add_course_grade_history') ?></td>
                                                        </tr>
                                                    </table>
                                                    <?php
                                                    $student_exam_grade_change_history = $student['ExamGradeHistory'];
                                                    $student_exam_grade_history = $student['exam_grade'];
                                                    $this->set(compact('student_exam_grade_change_history', 'student_exam_grade_history', 'freshman_program'));
                                                    echo $this->element('registered_or_add_course_grade_detail_history');
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php $count++;
                                        }
                                    }
                                    if (isset($get_list_of_students_with_grade['add'])
                                        && !empty($get_list_of_students_with_grade['add'])) {
                                        foreach ($get_list_of_students_with_grade['add']
                                                 as $key => $student) {
                                            $st_count++; ?>
                                            <tr <?= (isset($student['exam_grades']) && !empty($student['exam_grades'])
                                            && $student['exam_grades'][0]['registrar_approval'] == null
                                            && !is_null($student['exam_grades'][0]['department_approval'])
                                                ? 'style="font-weight:bold;"' : '') ?>>
                                                <td class="center" onclick="toggleView(this)" id="<?= $st_count ?>">
                                                    <?= $this->Html->image('/img/plus2.gif', ['id' => 'i' . $st_count]) ?></td>
                                                <td class="vcenter"><?= h($student['student']['full_name']) ?></td>
                                                <td class="center"><?= h($student['student']['studentnumber']) ?></td>
                                                <td class="center">
                                                    <?php

                                                    if (isset($student['exam_grades']) && !empty($student['exam_grades'])) {
                                                        $frequency_count[] = $student['exam_grades'][0]['grade'];
                                                        echo h($student['exam_grades'][0]['grade']);
                                                        if ($student['exam_grades'][0]['registrar_approval'] == null &&
                                                            !is_null($student['exam_grades'][0]['department_approval'])) {
                                                            echo $this->Form->hidden('ExamGrade.' . $count . '.id', ['value' => $student['exam_grades'][0]['id']]);
                                                            $enable_approve_button++;
                                                        } elseif ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == -1 && is_null($student['exam_grades'][0]['registrar_approval'])) {
                                                            $department_rejected_grades_back_to_registrar++;
                                                        } elseif ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == -1 && $student['exam_grades'][0]['registrar_approval'] == -1) {
                                                            $consequetive_rejected_grades_by_the_registrar++;
                                                        } elseif (($student['exam_grades'][0]['department_approval'] == -1 && is_null($student['exam_grades'][0]['registrar_approval'])) || ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == 1) || ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == 1 && $student['exam_grades'][0]['registrar_approval'] == -1)) {
                                                            $department_rejected_grades_back_to_instructor++;
                                                            if ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == 1 && $student['exam_grades'][0]['registrar_approval'] == -1) {
                                                                $registrar_regected_grades++;
                                                            }
                                                        }
                                                    } else {
                                                        echo '**';
                                                    }
                                                    ?>
                                                </td>
                                                <td class="center">
                                                    <?php
                                                    if (!isset($student['exam_grades']) || empty($student['ExamGrade'])) {
                                                        echo '<span class="text-gray" style="font-weight:normal;"><i>Waiting for Grade Submission</i></span>';
                                                    } elseif ($student['exam_grades'][0]['registrar_approval'] == 1) {
                                                        echo '<span class="accepted" style="font-weight:normal;">Accepted</span>';
                                                    } elseif ($student['exam_grades'][0]['department_approval'] == null) {
                                                        echo '<span class="on-process" style="font-weight:normal;">Pending for ' . h($approver) . ' approval</span>';
                                                    } elseif ($student['exam_grades'][0]['department_approval'] == -1) {
                                                        if ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == -1 && is_null($student['ExamGrade'][0]['registrar_approval'])) {
                                                            echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for registrar\'s response for ' . h($approver) . '\'s rejection of previously rejected grade by the registrar.</span>', 50, ['ellipsis' => '...', 'exact' => true, 'html' => true]);
                                                        } elseif ($student['exam_grades'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == -1 && $student['ExamGrade'][0]['registrar_approval'] == -1) {
                                                            echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for ' . h($approver) . '\'s response for registrar\'s consequetive rejections(two or more times).</span>', 50, ['ellipsis' => '...', 'exact' => true, 'html' => true]);
                                                        } elseif (($student['exam_grades'][0]['department_approval'] == -1 && is_null($student['exam_grades'][0]['registrar_approval'])) || ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == 1) || ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == 1 && $student['exam_grades'][0]['registrar_approval'] == -1)) {
                                                            if ($student['exam_grades'][0]['department_reply'] == 1 && $student['exam_grades'][0]['department_approval'] == 1 && $student['exam_grades'][0]['registrar_approval'] == -1) {
                                                                echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for Instructor grade re-submission in response for ' . h($approver) . '\'s acceptance of previously rejected grade by the registrar.</span>', 50, ['ellipsis' => '...', 'exact' => true, 'html' => true]);
                                                            } else {
                                                                echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for Instructor grade re-submission in response for ' . h($approver) . '\'s rejection of previously submitted grade by the instructor.</span>', 50, ['ellipsis' => '...', 'exact' => true, 'html' => true]);
                                                            }
                                                        } else {
                                                            echo '<span class="rejected" style="font-weight:normal;">Grade is rejected by the ' . h($approver) . '</span>';
                                                        }
                                                    } else {
                                                        if ($student['exam_grades'][0]['registrar_approval'] == null) {
                                                            echo '<span class="on-process" style="font-weight:normal;">Approved by ' . h($approver) . ', pending for registrar confirmation</span>';
                                                        } elseif ($student['exam_grades'][0]['registrar_approval'] == 1) {
                                                            echo '<span class="accepted" style="font-weight:normal;">Accepted</span>';
                                                        } elseif ($student['exam_grades'][0]['registrar_approval'] == -1) {
                                                            echo '<span class="rejected" style="font-weight:normal;">Approved by ' . h($approver) . ', but rejected by registrar</span>';
                                                        } else {
                                                            echo '<span class="accepted" style="font-weight:normal;">Accepted</span>';
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr id="c<?= $st_count ?>" style="display:none">
                                                <td style="background-color: white;">&nbsp;</td>
                                                <td style="background-color: white;" colspan="4">
                                                    <?php if (isset($student['MakeupExam']) && isset($student['ExamGradeChange']) && count($student['ExamGradeChange']) > 0) { ?>
                                                        <table cellpadding="0" cellspacing="0" class="table">
                                                            <tr>
                                                                <td style="width:28%; font-weight:bold; background-color: white;">Makeup Exam Minute Number:</td>
                                                                <td style="width:72%; background-color: white;"><?= h($student['ExamGradeChange'][0]['minute_number']) ?></td>
                                                            </tr>
                                                        </table>
                                                        <br>
                                                    <?php }
                                                    $register_or_add = 'gh';
                                                    $grade_history = isset($student['ExamGradeHistory']) ? $student['ExamGradeHistory'] : [];
                                                    $this->set(compact('register_or_add', 'grade_history', 'freshman_program'));
                                                    ?>
                                                    <table cellpadding="0" cellspacing="0" class="table">
                                                        <tr>
                                                            <td style="vertical-align:top; background-color: white;"><?= $this->element('registered_or_add_course_grade_history') ?></td>
                                                        </tr>
                                                    </table>
                                                    <?php
                                                    $student_exam_grade_change_history = $student['ExamGradeHistory'];
                                                    $student_exam_grade_history = $student['exam_grades'];
                                                    $this->set(compact('student_exam_grade_change_history', 'student_exam_grade_history', 'freshman_program'));
                                                    echo $this->element('registered_or_add_course_grade_detail_history');
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php $count++;
                                        }
                                    } ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="5">Legend: ( ** ) <span style="font-weight: normal;">Course in progress</span>; &nbsp;&nbsp;&nbsp; ( Bold ) <span style="font-weight: normal;">Waiting your decision</span></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php }
                        $array_count = array_count_values($frequency_count); ?>
                        <hr>
                        <?php if (isset($gradeScaleDetail) && !empty($gradeScaleDetail['Course']['id']) && $gradeScaleDetail['Course']['thesis'] == 1 && ($gradeScaleDetail['Course']['Curriculum']['program_id'] == \Cake\Core\Configure::read('PROGRAM_PhD') || $gradeScaleDetail['Course']['Curriculum']['program_id'] == \Cake\Core\Configure::read('PROGRAM_POST_GRADUATE')) && isset($gradeScaleDetail['GradeType']['used_in_gpa']) && $gradeScaleDetail['GradeType']['used_in_gpa'] == 1) { ?>
                            <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;">
                                <span style='margin-right: 15px;'></span>Currently, <?= h($gradeScaleDetail['Course']['course_code_title']) ?> course is set as a <?= $gradeScaleDetail['Course']['Curriculum']['program_id'] == \Cake\Core\Configure::read('PROGRAM_POST_GRADUATE') ? 'Thesis/Project' : 'Dissertation' ?> course and associated to "<?= h($gradeScaleDetail['GradeScale']['name']) ?>" from "<?= h($gradeScaleDetail['GradeType']['type']) ?>" grading type which uses point values of the awarded grades in CGPA calculations. Please communicate <?= h($gradeScaleDetail['Course']['Curriculum']['Department']['name']) ?> department and check the correctness of the grade type specified on <?= h($gradeScaleDetail['Course']['Curriculum']['curriculum_detail']) ?> curriculum before confirming the grades.
                            </div>
                            <hr>
                        <?php }

                        ?>
                        <input type="button" value="Show Grade Scale" onclick="showHideGradeScale('<?= h($publishedCourseDetail['id']) ?>')" id="ShowHideGradeScale"
                               class="tiny radius button bg-blue">
                        <div class="row">
                            <div class="large-6 columns">
                                <div style="margin-top:10px" id="GradeScale"></div>
                                <br>
                            </div>
                            <div class="large-6 columns"><br></div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="large-3 columns">
                                <table cellpadding="0" cellspacing="0" class="table">
                                    <thead>
                                    <tr>
                                        <th class="center">Grade</th>
                                        <th class="center">Frequency</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $total_students = 0;
                                    foreach ($array_count as $grade => $freqeuncy) { ?>
                                        <tr>
                                            <td class="center"><?= h($grade) ?></td>
                                            <td class="center"><?= h($freqeuncy) ?></td>
                                        </tr>
                                        <?php $total_students += $freqeuncy;
                                    } ?>
                                    </tbody>
                                    <?php if (isset($total_students) && $total_students) { ?>
                                        <tfoot>
                                        <tr>
                                            <td class="center">Total</td>
                                            <td class="center"><?= h($total_students) ?></td>
                                        </tr>
                                        </tfoot>
                                    <?php } ?>
                                </table>
                                <br>
                            </div>
                            <div class="large-6 columns">
                                <?php if ($enable_approve_button) {
                                    $options = [
                                        '1' => ' Accept (Make the grades permanent)',
                                        '-1' => ' Reject (Send back to department)'
                                    ];
                                    $attributes = ['legend' => false, 'id' => 'registrarApproval',
                                        'separator' => '<br/>', 'default' => 1];
                                    ?>
                                    <table cellpadding="0" cellspacing="0" class="table">
                                        <tr>
                                            <th>Your Decision</th>
                                        </tr>
                                        <tr>
                                            <td style="background-color: white; padding-left: 10%;">
                                                <br><?= $this->Form->radio('ExamGrade.registrar_approval', $options,
                                                    $attributes) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Remark <br /><?= $this->Form->control('ExamGrade.registrar_reason',
                                                    ['label' => false, 'cols' => 60]) ?>
                                            </td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <?= $this->Form->button('Confirm/Reject Grade Submission',
                                        ['id' => 'confirmGrade', 'name' => 'confirmgradesubmission',
                                            'class' => 'tiny radius button bg-blue']) ?>
                                <?php } else {
                                    if ($department_rejected_grades_back_to_registrar) { ?>
                                        <br>
                                        <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                            <span style='margin-right: 15px;'></span>Waiting for Registrar response for <?= h($approver) ?>'s rejection of previously rejected grade by the registrar.
                                        </div>
                                    <?php } elseif ($consequetive_rejected_grades_by_the_registrar) { ?>
                                        <br>
                                        <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                            <span style='margin-right: 15px;'></span>Waiting for <?= h($approver) ?>'s response for registrar's consequetive rejections(two or more times).
                                        </div>
                                    <?php } elseif ($department_rejected_grades_back_to_instructor) {
                                        if ($registrar_regected_grades) { ?>
                                            <br>
                                            <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                                <span style='margin-right: 15px;'></span>Waiting for Instructor grade re-submission in response for <?= h($approver) ?>'s acceptance of previously rejected grade by the registrar.
                                            </div>
                                        <?php } else { ?>
                                            <br>
                                            <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                                <span style='margin-right: 15px;'></span>Waiting for Instructor grade re-submission in response for <?= h($approver) ?>'s rejection of previously submitted grade by the instructor.
                                            </div>
                                        <?php }
                                    } else { ?>
                                        <br>
                                        <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                            <span style='margin-right: 15px;'></span>No Grade needs your Approval/Rejection.
                                        </div>
                                    <?php }
                                } ?>
                            </div>
                        </div>
                    <?php
                    }?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function toggleView(obj) {
        if ($('#c' + obj.id).css("display") == 'none') {
            $('#i' + obj.id).attr("src", '<?= $this->Url->build('/img/minus2.gif') ?>');
        } else {
            $('#i' + obj.id).attr("src", '<?= $this->Url->build('/img/plus2.gif') ?>');
        }
        $('#c' + obj.id).toggle("slow");
    }
    function toggleViewFullId(id) {
        if ($('#' + id).css("display") == 'none') {
            $('#' + id + 'Img').attr("src", '<?= $this->Url->build('/img/minus2.gif') ?>');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append('Hide Filter');
        } else {
            $('#' + id + 'Img').attr("src", '<?= $this->Url->build('/img/plus2.gif') ?>');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append('Display Filter');
        }
        $('#' + id).toggle("slow");
    }
    function showHideGradeScale(id) {
        alert(id);
        if ($("#ShowHideGradeScale").val() == 'Show Grade Scale') {
            var p_course_id = id;
            $("#GradeScale").empty();
            $("#GradeScale").append('Loading ...');
            var formUrl = '<?= $this->Url->build(['controller' => 'PublishedCourses',
                'action' => 'getCourseGradeScale']) ?>/' + p_course_id;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: { p_course_id: p_course_id },
                success: function(data, textStatus, xhr) {
                    $("#GradeScale").empty();
                    $("#GradeScale").append(data);
                    $("#ShowHideGradeScale").attr('value', 'Hide Grade Scale');
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });
        } else {
            $("#GradeScale").empty();
            $("#ShowHideGradeScale").attr('value', 'Show Grade Scale');
        }
        return false;
    }
    var form_being_submitted = false;
    var checkForm = function(form) {
        if (form_being_submitted) {
            alert("Confirming/Rejecting Grade Submission, please wait a moment...");
            form.confirmGrade.disabled = true;
            return false;
        }
        form.confirmGrade.value = 'Confirming/Rejecting Grade Submission...';
        form_being_submitted = true;
        return true;
    };
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
