<?php

use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

$role_id=$this->getRequest()->getSession()->read('Auth')['User']['role_id'];

?>

<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('List Course Registrations'); ?></span>
        </div>
    </div>
    <div class="box-body" style="display: block;">
        <?= $this->Form->create('CourseRegistration'); ?>
        <?php if ($role_id != ROLE_STUDENT) { ?>
            <div style="margin-top: -30px;">
                <hr>
                <fieldset style="padding-bottom: 0px; padding-top: 15px;">
                    <div class="row">
                        <div class="large-3 columns">
                            <?= $this->Form->control('Search.academic_year', [
                                'options' => $acyearArrayData,
                                'style' => 'width: 90%;',
                                'required' => true,
                                'id' => 'AcademicYear',
                                'onchange' => 'updateCourseListOnChangeofOtherField()'
                            ]); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->control('Search.semester', [
                                'options' => Configure::read('semesters'),
                                'style' => 'width: 90%;',
                                'required' => true,
                                'id' => 'Semester',
                                'onchange' => 'updateCourseListOnChangeofOtherField()'
                            ]); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->control('Search.program_id', [
                                'required' => true,
                                'id' => 'ProgramId',
                                'style' => 'width: 90%;',
                                'onchange' => 'updateCourseListOnChangeofOtherField()'
                            ]); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->control('Search.program_type_id', [
                                'required' => true,
                                'id' => 'ProgramTypeId',
                                'style' => 'width: 90%;',
                                'onchange' => 'updateCourseListOnChangeofOtherField()'
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="large-6 columns">
                            <?php if (isset($departments) && !empty($departments)) { ?>
                                <?= $this->Form->control('Search.department_id', [
                                    'label' => 'Department',
                                    'id' => 'DepartmentId',
                                    'style' => 'width: 95%;',
                                    'onchange' => 'updateCourseListOnChangeofOtherField()'
                                ]); ?>
                            <?php } elseif (isset($colleges) && !empty($colleges)) { ?>
                                <?= $this->Form->control('Search.college_id', [
                                    'label' => 'College/Institute/School: ',
                                    'id' => 'CollegeId',
                                    'style' => 'width: 95%;',
                                    'onchange' => 'updateCourseListOnChangeofOtherField()'
                                ]); ?>
                            <?php } ?>
                        </div>
                        <div class="large-6 columns">
                            <?= $this->Form->control('Search.section_id', [
                                'id' => 'SectionId',
                                'required' => true,
                                'style' => 'width: 95%;'
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="large-4 columns">
                            <?= $this->Form->control('Search.studentnumber', [
                                'label' => 'Student ID',
                                'id' => 'studentNumber',
                                'style' => 'width: 90%;'
                            ]); ?>
                        </div>
                    </div>
                    <hr>
                    <?= $this->Form->button('Search', [
                        'type' => 'submit',
                        'class' => 'tiny radius button bg-blue',
                        'name' => 'search',
                        'id' => 'Search'
                    ]); ?>
                </fieldset>
            </div>
        <?php } elseif ($role_id == ROLE_STUDENT) { ?>
            <div style="margin-top: -30px;">
                <fieldset style="padding-bottom: 0px; padding-top: 15px;">
                    <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
                    <div class="row">
                        <div class="large-3 columns">
                            <?= $this->Form->control('Search.academic_year', [
                                'style' => 'width: 90%;',
                                'options' => $acadamicYears,
                                'default' => isset($defaultacademicyear) && !empty($defaultacademicyear) ? $defaultacademicyear : false
                            ]); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->control('Search.semester', [
                                'style' => 'width: 90%;',
                                'options' => Configure::read('semesters')
                            ]); ?>
                        </div>
                        <div class="large-6 columns">
                            &nbsp;
                        </div>
                    </div>
                    <hr>
                    <?= $this->Form->button('Search', [
                        'type' => 'submit',
                        'class' => 'tiny radius button bg-blue',
                        'name' => 'search',
                        'value'=>'search',
                        'id' => 'Search'
                    ]); ?>
                </fieldset>
            </div>
        <?php } ?>
        <?= $this->Form->end(); ?>
        <?php

        if ( !empty($courseRegistrations)) { ?>
            <div class="box">
                <div class="box-header bg-transparent">
                    <h3 class="box-title">
                        <i class="fontello-search-outline" style="font-size: larger; font-weight: bold;"></i>
                        <span>SEARCH RESULTS</span>
                    </h3>
                </div>
                <div class="box-body" style="display: block;">
                    <?php if ($role_id != ROLE_STUDENT) { ?>
                        <div class="row" style="margin-bottom:10px;">
                            <div class="large-12 columns" style="margin-top: -10px;">
                                <?php if ($role_id == ROLE_REGISTRAR) { ?>
                                    <?= $this->Form->button('Generate Registration Slip', [
                                        'type' => 'submit',
                                        'class' => 'tiny radius button bg-blue',
                                        'name' => 'generateSlip'
                                    ]); ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <?= $this->Form->button('Get Grade Report', [
                                        'type' => 'submit',
                                        'class' => 'tiny radius button bg-blue',
                                        'name' => 'getGradeReport'
                                    ]); ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php } ?>
                                <?php if (empty($this->request->getData('Search.studentnumber'))) { ?>
                                    <?= $this->Form->button('Generate Registered List', [
                                        'type' => 'submit',
                                        'class' => 'tiny radius button bg-blue',
                                        'name' => 'generateRegisteredList'
                                    ]); ?>
                                <?php } ?>
                                <br><br>
                            </div>
                            <div class="large-4 columns">
                                <input class="form-control" id="filter" placeholder="Filter Results..." type="text" />
                            </div>
                            <div class="large-8 columns">
                                <a href="#clear" style="margin-left:10px;" class="clear-filter tiny radius button bg-orange" title="Clear Filter">Clear Filter</a>
                            </div>
                        </div>
                    <?php } elseif ($role_id == ROLE_STUDENT && (ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS != 0 || ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS != '0')) {
                        $enable_slip_pdf_download = 0;
                        $enable_grade_report_pdf_download = 0;
                        if (!empty($courseRegistrations)) {
                            $studentID = $courseRegistrations[0]->student->id;
                            $programID =  $courseRegistrations[0]->student->program_id;
                            $programTypeID =  $courseRegistrations[0]->student->program_type_id;
                            $studentNumber = $courseRegistrations[0]->student->studentnumber;
                            if (ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS == 1
                                || ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS == '1') {
                                $enable_slip_pdf_download = 1;
                            } elseif (ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS == 'AUTO') {
                                $generalSettings = TableRegistry::getTableLocator()->get('GeneralSettings')
                                    ->getAllGeneralSettingsByStudentByProgramIdOrBySectionID($studentID, $programID, $programTypeID, null);
                                if (!empty($generalSettings['GeneralSetting'])) {
                                    $enable_slip_pdf_download = $generalSettings['GeneralSetting']['allowRegistrationSlipPdfDownloadToStudents'];
                                }
                            }
                            if (ALLOW_GRADE_REPORT_PDF_DOWNLOAD_TO_STUDENTS == 1 || ALLOW_GRADE_REPORT_PDF_DOWNLOAD_TO_STUDENTS == '1') {
                                $enable_grade_report_pdf_download = 1;
                            } elseif (ALLOW_GRADE_REPORT_PDF_DOWNLOAD_TO_STUDENTS == 'AUTO') {
                                if (!isset($generalSettings)) {
                                    $generalSettings = TableRegistry::getTableLocator()->get('GeneralSettings')
                                        ->getAllGeneralSettingsByStudentByProgramIdOrBySectionID(
                                            $studentID, $programID, $programTypeID, null);
                                }
                                if (!empty($generalSettings['GeneralSetting'])) {
                                    $enable_grade_report_pdf_download = $generalSettings['GeneralSetting']['allowGradeReportPdfDownloadToStudents'];
                                }
                            }
                        }
                        if ($enable_slip_pdf_download || $enable_grade_report_pdf_download) { ?>
                            <div class="row" style="margin-bottom:10px;">
                                <div class="large-12 columns" style="margin-top: -10px;">
                                    <?= $this->Form->hidden('Search.studentnumber', [
                                        'id' => 'studentNumber',
                                        'value' => $studentNumber
                                    ]); ?>
                                    <?php if ($enable_slip_pdf_download) { ?>
                                        <?= $this->Form->button('Get Registration Slip', [
                                            'type' => 'submit',
                                            'class' => 'tiny radius button bg-blue',
                                            'name' => 'generateSlip'
                                        ]); ?>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <?php } ?>
                                    <?php if ($enable_grade_report_pdf_download
                                        && !empty($status_generated_acy_semester) &&
                                        in_array($this->request->getData('Search.academic_year'),
                                            $status_generated_acy_semester) &&
                                        in_array($this->request->getData('Search.semester'),
                                            $status_generated_acy_semester)) { ?>
                                        <?= $this->Form->button('Get Grade Report', [
                                            'type' => 'submit',
                                            'class' => 'tiny radius button bg-blue',
                                            'name' => 'getGradeReport'
                                        ]); ?>
                                    <?php } else { ?>
                                        <?= $this->Form->button('Get Grade Report', [
                                            'type' => 'submit',
                                            'class' => 'tiny radius button bg-blue',
                                            'name' => 'getGradeReport222',
                                            'disabled' => true
                                        ]); ?>
                                    <?php } ?>
                                    <br><br>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <table id="footable-res2" class="demo table" data-filter="#filter"
                           data-page-size="20" data-filter-text-only="true">
                        <thead>
                        <tr>
                            <th data-toggle="true" style="text-align: center;
                            vertical-align: middle;">Student ID</th>
                            <th data-hide="phone" style="text-align: center;
                             vertical-align: middle;">Full Name</th>
                            <th data-hide="phone,tablet" style="text-align: center;
                            vertical-align: middle;">Department</th>
                            <th data-hide="phone,tablet" style="text-align: center;
                            vertical-align: middle;">Program</th>
                            <th data-hide="phone,tablet" style="text-align: center;
                             vertical-align: middle;">Program Type</th>
                            <th style="text-align: center; vertical-align: middle;">Year</th>
                            <th style="text-align: center; vertical-align: middle;">ACY</th>
                            <th style="text-align: center; vertical-align: middle;">SEM</th>
                            <th data-hide="phone,tablet" style="text-align: center; vertical-align: middle;">Course</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $start = 1; ?>
                        <?php foreach ($courseRegistrations as $courseRegistration) { ?>
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?= $this->Html->link($courseRegistration->student->studentnumber,
                                        '#', [
                                        'class' => 'jsview',
                                        'data-animation' => 'fade',
                                        'data-reveal-id' => 'myModal',
                                        'data-reveal-ajax' =>  $this->Url->build(
                                            ['controller' => 'Students', 'action' => 'getModalBox', $courseRegistration->student->id]
                                        )
                                    ]); ?>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?= h($courseRegistration->student->full_name); ?></td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?= !empty($courseRegistration->published_course->department->name)
                                        ? h($courseRegistration->published_course->department->name)
                                        : ($courseRegistration->student->program->name == PROGRAM_REMEDIAL
                                            ? h($courseRegistration->published_course->college->shortname . ' - Remedial')
                                            : h($courseRegistration->published_course->college->shortname . ' - Pre/Freshman')); ?>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?= h($courseRegistration->student->program->name); ?></td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?= h($courseRegistration->student->program_type->name); ?></td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?= !empty($courseRegistration->year_level->name)
                                        ? h($courseRegistration->year_level->name)
                                        : ($courseRegistration->student->program->name == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st'); ?>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?= h($courseRegistration->academic_year); ?></td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?= h($courseRegistration->semester); ?></td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?= $this->Html->link(
                                        h($courseRegistration->published_course->course->course_code_title),
                                        ['controller' => 'courses', 'action' => 'view',
                                            $courseRegistration->published_course->course->id]
                                    ); ?>
                                    <?= (isset($courseRegistration->course_drops[0])
                                        && $courseRegistration->course_drops[0]->department_approval == 1
                                        && count($courseRegistration->course_drops) > 0
                                        && $courseRegistration->course_drops[0]->registrar_confirmation == 1)
                                        ? "<b style='color:red'> - Dropped </b>"
                                        : ''; ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="9">
                                <div class="pagination pagination-centered"></div>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?php } ?>
        <script>
            function updateCourseListOnChangeofOtherField() {
                //serialize form data
                $("#studentNumber").val('');
                var formData = '';
                var department_id = $("#DepartmentId").val();
                var college_id = $("#CollegeId").val();
                var academic_year = $("#AcademicYear").val().replace("/", "-");
                var program_id = $("#ProgramId").val();
                var program_type_id = $("#ProgramTypeId").val();
                var section_id = $("#SectionId").val();
                if (typeof department_id != "undefined" && typeof academic_year != "undefined" && typeof program_id != "undefined" && program_type_id != "undefined" && typeof section_id != "undefined" && section_id != '') {
                    formData = department_id + '~' + academic_year + '~' + program_id + '~' + program_type_id + '~' + 'd';
                } else if (typeof college_id != "undefined" && typeof academic_year != "undefined" && typeof program_id != "undefined" && program_type_id != "undefined" && typeof section_id != "undefined" && section_id != '') {
                    formData = college_id + '~' + academic_year + '~' + program_id + '~' + program_type_id + '~' + 'c';
                } else {
                    return false;
                }
                $("#SectionId").attr('disabled', true);
                $("#Search").attr('disabled', true);
                //get form action
                var formUrl = '/courseRegistrations/getSectionComboForView/' + formData;
                $.ajax({
                    type: 'get',
                    url: formUrl,
                    data: formData,
                    success: function(data, textStatus, xhr) {
                        $("#AcademicYear").attr('disabled', false);
                        $("#Semester").attr('disabled', false);
                        $("#ProgramId").attr('disabled', false);
                        $("#ProgramTypeId").attr('disabled', false);
                        $("#DepartmentId").attr('disabled', false);
                        $("#CollegeId").attr('disabled', false);
                        $("#SectionId").attr('disabled', false);
                        $("#SectionId").empty();
                        $("#SectionId").append(data);
                    },
                    error: function(xhr, textStatus, error) {
                    }
                });
                $("#Search").attr('disabled', false);
                return false;
            }
        </script>
    </div>
</div>
