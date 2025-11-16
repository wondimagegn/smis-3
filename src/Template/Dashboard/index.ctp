<?php
use Cake\Core\Configure;
use Cake\Utility\Inflector;
$role_id=$this->getRequest()->getSession()->read('Auth')['User']['role_id'];
?>
<div class="row">
    <div class="large-12 columns">
        <?php if (isset($comingAcademicCalendarsDeadlines) && !empty($comingAcademicCalendarsDeadlines)) : ?>
            <p class="rejected">
                <?php
                $str = '';
                foreach ($comingAcademicCalendarsDeadlines as $k => $v) {
                    // Note: Adjust condition as needed; here it checks if deadline is greater than today and equals "0000-00-00" (which might be a bug)
                    if ($v['GradeSubmissionDeadline'] > date('Y-m-d') && $v['GradeSubmissionDeadline'] == "0000-00-00") {
                        $str .= 'Grade submission deadline ' . $v['GradeSubmissionDeadline'] . ' ';
                    }
                }
                echo $str;
                ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<div class="row" ng-app="dashboardApp">
    <div class="large-4 columns">
        <div class="box">
            <div class="box-header bg-transparent">
                <div class="pull-right box-tools">
                    <span class="box-btn" data-widget="collapse">
                        <i class="icon-minus"></i>
                    </span>
                    </div>
                <h3 class="box-title">
                    <i class="fontello-chat-alt"></i><span>Messages</span>
                </h3>
            </div>

            <div ng-init="getAll()" ng-controller="messageController"
                 class="box-body" style="display: block; margin-top: -15px;" id="AutoMessageDashBoard">
                <loading-ajax></loading-ajax>
                <table cellpadding="0" cellspacing="0" style="width:100%; border:0px;"
                       class="condence table" id="AutoMessage">
                    <tbody>
                    <tr ng-repeat="message in auto_messages | limitTo: paginationLimit()">
                        <td style="font-size:10px; font-weight:bold; background-color: white;">
                            <div>
                                {{ message.created | dateToISO | date:'medium' }}
                                (<span style="color:red; cursor:url('../img/error.ico'), default" ng-click="markAsUnread(message.id)">close</span>)
                            </div>
                            <div style="text-align:justify; font-size:11px; font-weight:bold; background-color: white;"
                                 ng-bind-html="$sce.trustAsHtml(message.message)">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: white;">
                            <div class="pagination pagination-centered">
                                <button class="tiny radius button bg-blue" ng-show="hasMoreItemsToShow()" ng-click="showMoreItems()">Show more</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($role_id == ROLE_STUDENT) : ?>
        <div class="large-4 columns">
            <div class="box" ng-controller="studentRankController">
                <div class="box-header bg-transparent">
                    <div class="pull-right box-tools">
                        <span class="box-btn" data-widget="collapse">
                            <i class="icon-minus"></i>
                        </span>
                        </div>
                    <h3 class="box-title">
                        <i class="fontello-graduation-cap"></i><span>Rank</span>
                    </h3>
                </div>
                <div class="box-body" style="display: block; margin-top: -15px;" id="StudentRankDashBoard" ng-init="getAll()">
                    <loading-ajax></loading-ajax>
                    <table cellpadding="0" cellspacing="0" class="table">
                        <tbody ng-if="rank !== null">
                        <tr ng-repeat="rnk in rank">
                            <td style="font-size:10px; font-weight:bold">
                                <h6 class="text-black" style="text-align:center"> By CGPA </h6>
                                <div>
                                    <span>Academic Year/Semester</span>
                                    <span>{{ rnk.cgpa.StudentRank.academicyear }}</span>/<span>{{ rnk.cgpa.StudentRank.semester }}</span>
                                </div>
                                <div class="semesterStand">
                                    <div>
                                        <span>From Section:</span>
                                        <span>{{ rnk.cgpa.StudentRank.section_rank }}</span>
                                    </div>
                                    <div>
                                        <span>From Batch:</span>
                                        <span>{{ rnk.cgpa.StudentRank.batch_rank }}</span>
                                    </div>
                                    <div>
                                        <span>From College:</span>
                                        <span>{{ rnk.cgpa.StudentRank.college_rank }}</span>
                                    </div>
                                </div>

                                <h6 class="text-black" style="text-align:center"> By SGPA </h6>
                                <div>
                                    <span>Academic Year/Semester</span>
                                    <span>{{ rnk.sgpa.StudentRank.academicyear }}</span>/<span>{{ rnk.sgpa.StudentRank.semester }}</span>
                                </div>
                                <div class="semesterStand">
                                    <div>
                                        <span>From Section:</span>
                                        <span>{{ rnk.sgpa.StudentRank.section_rank }}</span>
                                    </div>
                                    <div>
                                        <span>From Batch:</span>
                                        <span>{{ rnk.sgpa.StudentRank.batch_rank }}</span>
                                    </div>
                                    <div>
                                        <span>From College:</span>
                                        <span>{{ rnk.sgpa.StudentRank.college_rank }}</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="large-4 columns">
            <div class="box" ng-controller="studentDormDashBoardController">
                <div class="box-header bg-transparent">
                    <div class="pull-right box-tools">
                        <span class="box-btn" data-widget="collapse">
                            <i class="icon-minus"></i>
                        </span>
                        <!-- <span class="box-btn" data-widget="remove"><i class="icon-cross"></i></span> -->
                    </div>
                    <h3 class="box-title">
                        <i class="fontello-home-outline"></i><span>Dorm</span>
                    </h3>
                </div>
                <div class="box-body" id="StudentDormDashBoard" style="display: block; margin-top: -15px;" ng-if="dormAssignedStudent.Dormitory !== null" ng-init="getAll()">
                    <loading-ajax></loading-ajax>
                    <div class="row summary-border-top" style="margin:0;">
                        <div class="large-12 columns">
                            <div class="school-timetable">
                                <h6>
                                    <i class="fontello-home-outline"></i> Block <span class="bg-blue">{{ dormAssignedStudent.Dormitory.DormitoryBlock.block_name }}</span>
                                </h6>
                                <h6>
                                    <i class="fontello-home-outline"></i> Floor <span class="bg-blue">{{ dormAssignedStudent.Dormitory.floor }}</span>
                                </h6>
                                <h6>
                                    <i class="fontello-home-outline"></i> Room <span class="bg-green">{{ dormAssignedStudent.Dormitory.dorm_number }}</span>
                                </h6>
                                <h6>
                                    <i class="fontello-home-outline"></i> Capacity <span class="bg-blue">{{ dormAssignedStudent.Dormitory.capacity }}</span>
                                </h6>
                                <a href="#" data-animation="fade" data-reveal-id="myModalUpgrade" data-reveal-ajax="/dormitoryAssignments/getAssignedStudent/{{ dormAssignedStudent.Dormitory.id }}">
                                    Room mates
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($role_id == ROLE_COLLEGE || $role_id == ROLE_DEPARTMENT
        || $role_id == ROLE_REGISTRAR) : ?>
        <div class="large-4 columns">
            <div class="box" ng-controller="gradeChangeController">
                <div class="box-header bg-transparent">
                    <div class="pull-right box-tools">
                        <span class="box-btn" data-widget="collapse">
                            <i class="icon-minus"></i>
                        </span>
                        <!-- <span class="box-btn" data-widget="remove"><i class="icon-cross"></i></span> -->
                    </div>
                    <h3 class="box-title">
                        <i class="fontello-check"></i><span>Grade Change Approval</span>
                    </h3>
                </div>
                <div ng-init="getAll()" class="box-body" id="GradeChangeApproval"
                     style="display: block; margin-top: -15px;">
                    <loading-ajax></loading-ajax>
                    <table cellpadding="0" cellspacing="0" class="table">
                        <tbody >
                        <tr ng-if="isNotZeroOrUndefined(exam_grade_change_requests)">
                            <td>
                                <a ng-href="/examGradeChanges/manageDepartmentGradeChange">
                                    You have {{ exam_grade_change_requests }} grade change requests
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="isNotZeroOrUndefined(exam_grade_changes_for_college_approval)">
                            <td>
                                <a ng-href="/examGradeChanges/manageCollegeGradeChange">
                                    You have {{ exam_grade_changes_for_college_approval }} grade change requests
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="isNotZeroOrUndefined(makeup_exam_grades)">
                            <td>
                                <a ng-href="/examGradeChanges/manageDepartmentGradeChange">
                                    You have {{ makeup_exam_grades }} makeup exam approval requests.
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="isNotZeroOrUndefined(rejected_makeup_exams)">
                            <td class="rejected">
                                <a ng-href="/examGradeChanges/manageDepartmentGradeChange">
                                    You have {{ rejected_makeup_exams }} rejected makeup exam grades.
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="isNotZeroOrUndefined(rejected_supplementary_exams)">
                            <td class="rejected">
                                <a ng-href="/examGradeChanges/manageDepartmentGradeChange">
                                    You have rejected supplementary exam grades.
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="isNotZeroOrUndefined(fm_exam_grade_change_requests)">
                            <td>
                                <a ng-href="/examGradeChanges/manageFreshmanGradeChange">
                                    You have {{ fm_exam_grade_change_requests }} freshman grade change requests
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="isNotZeroOrUndefined(fm_makeup_exam_grades)">
                            <td>
                                <a ng-href="/examGradeChanges/manageFreshmanGradeChange">
                                    You have {{ fm_makeup_exam_grades }} freshman makeup grade change requests
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="isNotZeroOrUndefined(fm_rejected_makeup_exams)">
                            <td class="rejected">
                                <a ng-href="/examGradeChanges/manageFreshmanGradeChange">
                                    You have rejected freshman makeup grade change requests
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="isNotZeroOrUndefined(fm_rejected_supplementary_exams)">
                            <td class="rejected">
                                <a ng-href="/examGradeChanges/manageFreshmanGradeChange">
                                    You have rejected supplementary grade change requests
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="isNotZeroOrUndefined(reg_exam_grade_change_requests)">
                            <td>
                                <a ng-href="/examGradeChanges/manageRegistrarGradeChange">
                                    You have {{ reg_exam_grade_change_requests }} grade change requests
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="isNotZeroOrUndefined(reg_makeup_exam_grades)">
                            <td>
                                <a ng-href="/examGradeChanges/manageRegistrarGradeChange">
                                    You have {{ reg_makeup_exam_grades }} makeup grade change requests
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="isNotZeroOrUndefined(reg_supplementary_exam_grades)">
                            <td>
                                <a ng-href="/examGradeChanges/manageRegistrarGradeChange">
                                    You have {{ reg_supplementary_exam_grades }} supplementary grade change requests
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    if ($role_id == ROLE_COLLEGE || $role_id == ROLE_DEPARTMENT || $role_id == ROLE_REGISTRAR) { ?>
        <div class="large-4 columns">
            <div class="box" id="gradeBox">
                <div class="box-header bg-transparent">
                    <div class="pull-right box-tools">
                        <span class="box-btn" data-widget="collapse"><i class="icon-minus"></i></span>
                      </div>
                    <h3 class="box-title"><i class="fontello-check"></i><span>Grade Approval/Confirmation</span></h3>
                </div>
                <loading-ajax> </loading-ajax>

                <div class="box-body" style="display: block; margin-top: -15px;" id="GradeConfiramationApproval"
                     ng-controller="gradeApprovalConfirmation">
                    <table cellpadding="0" cellspacing="0" class="table">
                        <tr ng-repeat="grade in courses_for_dpt_approvals | startFrom:currentPage*pageSize | limitTo:pageSize">
                            <td>
                                <a ng-href="/examGrades/approveNonFreshmanGradeSubmission/{{grade.id}}">
                                    Instructor: {{grade.course_instructor_assignments[0].staff.Title.title}}. {{grade.course_instructor_assignments[0].staff.first_name}} {{grade.course_instructor_assignments[0].staff.middle_name}} {{grade.course_instructor_assignments[0].staff.last_name}} <br />
                                    Course: {{grade.course.course_title}} ({{grade.course.course_code}}) <br />
                                    Department: {{grade.department.name}} <br />
                                    Section: {{grade.section.name}} <br />
                                    Year Level: {{grade.year_level.name ? grade.year_level.name : 'Pre/Freshman'}} <br />
                                    Program: {{grade.program.name}} <br />
                                    Program Type: {{grade.program_type.name}} <br />
                                    Academic Year: {{grade.academic_year}} <br />
                                    Semester: {{grade.semester}} <br />
                                </a>
                            </td>
                        </tr>
                    </table>

                    <?php
                    if ($role_id == ROLE_DEPARTMENT ) {?>
                        <table ng-show="courses_for_dpt_approvals.length == 0 " cellpadding="0" cellspacing="0" class="table">
                            <tr>
                                <td>
                                    <a href="/examGrades/approveNonFreshmanGradeSubmission"> Check grade submissions prior to <?= $acy_ranges_by_coma_quoted_for_display?></a>
                                </td>
                            </tr>
                        </table>
                        <?php
                    } ?>

                    <?php
                    if ($role_id == ROLE_REGISTRAR ) {?>
                        <table ng-show="courses_for_registrar_approval.length == 0" cellpadding="0" cellspacing="0" class="table">
                            <tr>
                                <td>
                                    <a href="/examGrades/confirmGradeSubmission"> Check grade submissions prior to <?= $acyRangesByComaQuotedForDisplay?></a>
                                </td>
                            </tr>
                        </table>
                        <?php
                    } ?>

                    <div class="pagination-centered" ng-show="courses_for_dpt_approvals.length > 0">
                        <br>
                        <ul class="pagination">
                            <li class="arrow">
                                <button type="button" class="tiny radius button bg-blue" ng-disabled="currentPage == 0" ng-click="currentPage=currentPage-1"> &lt; PREV</button>
                            </li>
                            <li>
                                <span>{{currentPage+1}} of {{ numberOfPages()}}</span>
                            </li>
                            <li class="arrow">
                                <button class="tiny radius button bg-blue" ng-disabled="currentPage >= courses_for_dpt_approvals.length/pageSize-1" ng-click="currentPage=currentPage+1">NEXT &gt;</button>
                            </li>
                        </ul>
                    </div>

                    <table ng-show="courses_for_registrar_approval.length > 0" cellpadding="0" cellspacing="0" class="table">
                        <tr ng-repeat="grade in courses_for_registrar_approval | startFrom:currentPage*pageSize | limitTo:pageSize">
                            <td>
                                <a ng-href="/examGrades/confirmGradeSubmission/{{grade.id}}">
                                    Instructor: {{grade.course_instructor_assignments[0].staff.Title.title}}. {{grade.course_instructor_assignments[0].staff.full_name}} <br />
                                    Course: {{grade.course.course_title}} ({{ grade.course.course_code }}) <br />
                                    Department: {{grade.department.name}} {{grade.college.name}} <br />
                                    Section: {{grade.section.name}} <br />
                                    <!-- Year Level: {{grade.YearLevel.name }} <br /> -->
                                    Year Level: {{(grade.year_level.name ? grade.year_level.name : 'Pre/Freshman') }} <br />
                                    Academic Year: {{grade.academic_year}} <br />
                                    Semester: {{grade.semester}} <br />
                                </a>
                            </td>
                        </tr>
                    </table>

                    <div class="pagination-centered" ng-show="courses_for_registrar_approval.length > 0">
                        <br>
                        <ul class="pagination">
                            <li class="arrow">
                                <button type="button" class="tiny radius button bg-blue" ng-disabled="currentPage == 0" ng-click="currentPage=currentPage-1"> &lt; PREV</button>
                            </li>
                            <li>
                                <span>{{currentPage+1}} of {{ numberOfPages()}}</span>
                            </li>
                            <li class="arrow">
                                <button class="tiny radius button bg-blue" ng-disabled="currentPage >= courses_for_registrar_approval.length/pageSize - 1 " ng-click="currentPage=currentPage+1">NEXT &gt;</button>
                            </li>
                        </ul>
                    </div>

                    <table ng-show="courses_for_freshman_approvals.length > 0" cellpadding="0" cellspacing="0" class="table">
                        <tr ng-repeat="grade in courses_for_freshman_approvals | startFrom:currentPage*pageSize | limitTo:pageSize  ">
                            <td>
                                <a ng-href="/examGrades/approveFreshmanGradeSubmission/{{grade.id}}">
                                    Instructor: {{grade.course_instructor_assignments[0].staff.Title.title}} {{grade.course_instructor_assignments[0].staff.full_name}} <br />
                                    Course: {{grade.course.course_title}}. ({{ grade.course.course_code }}) <br />
                                    Department: {{grade.department.name}} {{grade.college.name}} <br />
                                    Section: {{grade.section.name}} <br />
                                    <!-- Year Level: Pre/1st <br /> -->
                                    Year Level: {{(grade.year_level.name ? grade.year_level.name : 'Pre/Freshman') }} <br />
                                    Program: {{grade.program.name }} <br />
                                    ProgramType: {{grade.program_type.name }} <br />
                                    Academic Year: {{grade.academic_year}} <br />
                                    Semester: {{grade.semester}} <br />
                                </a>
                            </td>
                        </tr>
                    </table>

                    <div class="pagination-centered" ng-show="courses_for_freshman_approvals.length > 0 ">
                        <br>
                        <ul class="pagination">
                            <li class="arrow">
                                <button type="button" class="tiny radius button bg-blue" ng-disabled="currentPage == 0" ng-click="currentPage=currentPage-1"> &lt;  PREV</button>
                            </li>
                            <li>
                                <span>{{currentPage+1}} of {{ numberOfPages()}}</span>
                            </li>
                            <li class="arrow">
                                <button class="tiny radius button bg-blue" ng-disabled="currentPage >=courses_for_freshman_approvals.length/pageSize - 1 " ng-click="currentPage=currentPage+1">NEXT &gt;</button>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
        <?php
    } ?>

    <?php if ($role_id == ROLE_DEPARTMENT) : ?>
        <div class="large-4 columns">
            <div class="box">
                <div class="box-header bg-transparent">
                    <div class="pull-right box-tools">
                        <span class="box-btn" data-widget="collapse">
                            <i class="icon-minus"></i>
                        </span>
                    </div>
                    <h3 class="box-title">
                        <i class="fontello-check"></i><span>Dispatched Courses</span>
                    </h3>
                </div>
                <div class="box-body" id="DispatchedAndAssignedCourseID" style="display: block; margin-top: -15px;" ng-controller="dispatchedNotYetAssignedCourseController">
                    <loading-ajax></loading-ajax>
                    <span ng-if="dispatched_course_list.length > 0">
                        <strong class="fs-12 text-gray">Courses without Instructor assignment</strong><br>
                    </span><br>
                    <table cellpadding="0" cellspacing="0" class="table">
                        <tbody>
                        <tr ng-repeat="course in dispatched_course_list | startFrom:currentPage*pageSize | limitTo:pageSize">
                            <td>
                                <a ng-href="/courseInstructorAssignments/assign_course_instructor/{{ course.PublishedCourse.id }}">
                                    <strong>Dispatched to: </strong> {{ course.given_by_department.name }} <br>
                                    <strong>From: </strong> {{ course.department.name }} {{ course.college.name }} <br>
                                    <strong>Course: </strong> {{ course.course.course_title }} ({{ course.Course.course_code }}) <br>
                                    <strong>Section: </strong> {{ course.section.name }} <br>
                                    <strong>Program: </strong> {{ course.program.name }} - {{ course.program_type.name }} <br>
                                    <strong>ACY/Semester: </strong> {{ course.academic_year }} - {{ course.semester }} <br>
                                </a>
                            </td>
                        </tr>
                        <tr ng-repeat="course in dispatched_course_not_assigned | startFrom:currentPage*pageSize | limitTo:pageSize">
                            <td>
                                <strong>Dispatched to: </strong> {{ course.given_by_department.name }} <br>
                                <strong>Course: </strong> {{ course.course.course_title }} ({{ course.course.course_code }}) <br>
                                <strong>Section: </strong> {{ course.section.name }} <br>
                                <strong>Program: </strong> {{ course.program.name }} - {{ course.program_type.name }} <br>
                                <strong>ACY/Semester: </strong> {{ course.academic_year }} - {{ course.semester }} <br>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <br>
                    <div class="pagination-centered" ng-show="dispatched_course_list.length > 0 || dispatched_course_not_assigned.length > 0">
                        <ul class="pagination">
                            <li class="arrow">
                                <button type="button" class="tiny radius button bg-blue" ng-disabled="currentPage == 0" ng-click="currentPage=currentPage - 1"> &lt; PREV</button>
                            </li>
                            <li>
                                <span>{{ currentPage + 1 }} of {{ numberOfPages() }}</span>
                            </li>
                            <li class="arrow">
                                <button class="tiny radius button bg-blue" ng-click="currentPage=currentPage + 1"
                                        ng-disabled="currentPage >= (dispatched_course_list.length || dispatched_course_not_assigned.length)/pageSize - 1">
                                    NEXT &gt;
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_REGISTRAR || $role_id == ROLE_COLLEGE) : ?>
        <div class="large-4 columns">
            <div class="box" ng-controller="addDropRequestController">
                <div class="box-header bg-transparent">
                    <div class="pull-right box-tools">
                        <span class="box-btn" data-widget="collapse">
                            <i class="icon-minus"></i>
                        </span>
                    </div>
                    <h3 class="box-title">
                        <i class="fontello-check"></i><span>Course Add/Drop Requests</span>
                    </h3>
                </div>
                <div class="box-body" id="AddDropRequest" style="display: block; margin-top: -15px;">
                    <loading-ajax></loading-ajax>
                    <table cellpadding="0" cellspacing="0" class="table">
                        <tbody>
                        <tr>
                            <?php if ($role_id == ROLE_REGISTRAR) : ?>
                                <td ng-show="add_request">
                                    <a ng-show="add_request > 0" ng-href="/courseAdds/approveAdds">
                                        You have {{ add_request }} Course Add requests which are approved by department and waiting your confirmation.
                                    </a>
                                </td>
                                <td ng-if="add_request == 0">
                                    There is no Course Add request that needs your approval for now.
                                </td>
                            <?php else : ?>
                                <td ng-show="add_request_dpt">
                                    <a ng-show="add_request_dpt > 0" ng-href="/courseAdds/approveAdds">
                                        You have {{ add_request_dpt }} Course Add requests from your students waiting your approval.
                                    </a>
                                </td>
                                <td ng-if="add_request_dpt == 0">
                                    There is no Course Add request that needs your approval for now.
                                </td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <?php if ($role_id == ROLE_REGISTRAR) : ?>
                                <td ng-show="drop_request">
                                    <a ng-href="/courseDrops/approveDrops">
                                        You have {{ drop_request }} course drop request approved by department and waiting your confirmation.
                                    </a>
                                </td>
                                <td ng-if="drop_request == 0">
                                    There is no drop request that needs your approval for now.
                                </td>
                            <?php else : ?>
                                <td ng-show="drop_request_dpt">
                                    <a ng-show="drop_request_dpt > 0" ng-href="/courseDrops/approveDrops">
                                        You have {{ drop_request_dpt }} course drop request from your students waiting for approval.
                                    </a>
                                </td>
                                <td ng-if="drop_request_dpt == 0">
                                    There is no drop request that needs your approval for now.
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php if ($role_id == ROLE_REGISTRAR) : ?>
                            <tr>
                                <td ng-show="forced_drops">
                                    <a ng-href="/courseDrops/forcedDrop">You have students that need forced drop. </a>
                                </td>
                                <td ng-if="forced_drops == 0">
                                    You don't have any student that need forced drop for now.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($role_id == ROLE_REGISTRAR) : ?>
        <div class="large-4 columns">
            <div class="box" ng-controller="clearnceWithdrawSubController">
                <div class="box-header bg-transparent">
                    <div class="pull-right box-tools">
                        <span class="box-btn" data-widget="collapse">
                            <i class="icon-minus"></i>
                        </span>
                    </div>
                    <h3 class="box-title">
                        <i class="fontello-check"></i><span>Clearnce/Withdraw Requests</span>
                    </h3>
                </div>
                <div class="box-body" id="ClearnceAndWithdraw" style="display: block; margin-top: -15px;">
                    <loading-ajax></loading-ajax>
                    <table cellpadding="0" cellspacing="0" class="table">
                        <tbody>
                        <?php if ($role_id == ROLE_REGISTRAR) : ?>
                            <tr ng-if="clearance_request > 0 && isNotZeroOrUndefined(clearance_request)">
                                <td>
                                    <a ng-href="/clearances/approveClearance">
                                        You have {{ clearance_request }} clearance/withdraw requests that needs your approval
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr ng-if="exemption_request > 0 && isNotZeroOrUndefined(exemption_request)">
                            <td style="background-color: white;">
                                <a ng-href="/courseExemptions/listExemptionRequest">
                                    You have {{ exemption_request }} exemption requests that needs your approval
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="substitution_request > 0 && isNotZeroOrUndefined(substitution_request)">
                            <td style="background-color: white;">
                                <a ng-href="/CourseSubstitutionRequests/approveSubstitution">
                                    You have {{ substitution_request }} course substitution requests that needs your approval
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($role_id == ROLE_SYSADMIN) : ?>
        <div class="large-4 columns">
            <div class="box" ng-controller="backupController">
                <div class="box-header bg-transparent">
                    <div class="pull-right box-tools">
                        <span class="box-btn" data-widget="collapse">
                            <i class="icon-minus"></i>
                        </span>
                    </div>
                    <h3 class="box-title">
                        <i class="fontello-check"></i><span>Backup/Voting Request</span>
                    </h3>
                </div>
                <div class="box-body" id="BackupAccountRequest" style="display: block; margin-top: -15px;">
                    <loading-ajax></loading-ajax>
                    <table cellpadding="0" cellspacing="0" class="table">
                        <tbody>
                        <tr ng-repeat="backup in latest_backups | startFrom:currentPage*pageSize | limitTo:pageSize">
                            <td>{{ backup.Backup.created | dateToISO | date:'medium' }}</td>
                            <td>
                                <a ng-if="backup.Backup.file_exists" ng-href="/backups/index/{{ backup.Backup.id }}"> Download </a>
                                <div ng-if="!backup.Backup.file_exists"> Not Available </div>
                            </td>
                        </tr>
                        <tr ng-if="password_reset_confirmation_request">
                            <td colspan="2">
                                <a ng-href="/users/task_confirmation/">
                                    You have {{ password_reset_confirmation_request }} password reset confirmation requests.
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="admin_cancelation_confirmation_request">
                            <td colspan="2">
                                <a ng-href="/users/task_confirmation/">
                                    You have {{ admin_cancelation_confirmation_request }} administrator cancellation confirmation requests.
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="admin_assignment_confirmation_request">
                            <td colspan="2">
                                <a ng-href="/users/task_confirmation/">
                                    You have {{ admin_assignment_confirmation_request }} administrator assignment confirmation requests.
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="confirmed_taskss">
                            <td colspan="2">
                                <a ng-href="/users/task_confirmation/">
                                    There are {{ confirmed_taskss }} confirmed tasks by other system administrators.
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="role_change_confirmation_request">
                            <td colspan="2">
                                <a ng-href="/users/task_confirmation/">
                                    You have {{ role_change_confirmation_request }} role change requests.
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="deactivation_confirmation_request">
                            <td colspan="2">
                                <a ng-href="/users/task_confirmation/">
                                    You have {{ deactivation_confirmation_request }} user account deactivation requests.
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="activation_confirmation_request">
                            <td colspan="2">
                                <a ng-href="/users/task_confirmation/">
                                    You have {{ activation_confirmation_request }} user account activation requests.
                                </a>
                            </td>
                        </tr>
                        <tr ng-if="latest_backups">
                            <td colspan="2" class="utils">
                                <a ng-href="/backups/index"> View More </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($role_id == ROLE_REGISTRAR) : ?>
        <div class="large-4 columns">
            <div class="box" ng-controller="profileNotCompleteController">
                <div class="box-header bg-transparent">
                    <div class="pull-right box-tools">
                        <span class="box-btn" data-widget="collapse">
                            <i class="icon-minus"></i>
                        </span>
                    </div>
                    <h3 class="box-title">
                        <i class="fontello-contacts"></i><span>Student Profile(Not Completeee)</span>
                    </h3>
                </div>
                <div class="box-body" id="ProfileNotComplete" style="display: block; margin-top: -15px;">
                    <loading-ajax></loading-ajax>
                    <table cellpadding="0" cellspacing="0" class="table">
                        <tbody>
                        <tr ng-if="profile_not_buildc">
                            <td>
                                <a ng-href="/students/profileNotBuildList">
                                    {{ profile_not_buildc }} students profile is not complete. Please complete their profile.
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="large-4 columns">
        <div class="box">
            <div class="box-header bg-transparent">
                <div class="pull-right box-tools">
                    <span class="box-btn" data-widget="collapse">
                        <i class="icon-view-list"></i>
                    </span>
                </div>
                <h3 class="box-title">
                    <i class="icon-view-list"></i><span>Events</span>
                </h3>
            </div>
            <div class="box-body" id="AcademicCalender" style="display: block; margin-top: -15px;">
                <!-- Events content goes here -->
            </div>
        </div>
    </div>
</div>



<?php if ($role_id == ROLE_STUDENT || $role_id == ROLE_INSTRUCTOR) : ?>
    <div class="row">
        <div class="large-12 columns">
            <div class="box">
                <div class="box-header bg-transparent">
                    <div class="pull-right box-tools">
                        <span class="box-btn" data-widget="collapse">
                            <i class="icon-minus"></i>
                        </span>
                    </div>
                    <h3 class="box-title">
                        <i class="fontello-calendar-1"></i><span>Schedule</span>
                    </h3>
                </div>
                <div class="box-body" id="CourseSchedule" style="display: block;">
                    <!-- Schedule content goes here -->
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="large-12 columns">
        <div id="myModalUpgrade" class="reveal-modal" data-reveal>
            <!-- Modal content goes here -->
        </div>
    </div>
</div>
