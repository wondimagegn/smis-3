<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('List Course Registrations'); ?></span>
        </div>
    </div>
    <div class="box-body" style="display: block;">
        <?= $this->Form->Create('CourseRegistration'); ?>
        <?php
        //debug($c_or_d);
        //debug($status_generated_acy_semester);
        if ($role_id != ROLE_STUDENT) {  ?>
            <div style="margin-top: -30px;">
                <hr>
                <fieldset style="padding-bottom: 0px; padding-top: 15px;">
                    <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
                    <div class="row">
                        <div class="large-3 columns">
                            <?= $this->Form->input('Search.program_id', array('required' => true, 'id' => 'ProgramId', 'style' => 'width: 90%;', 'onchange' => 'updateCourseListOnChangeofOtherField()')); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->input('Search.program_type_id',  array('required' => true, 'id' => 'ProgramTypeId', 'style' => 'width: 90%;', 'onchange' => 'updateCourseListOnChangeofOtherField()')); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->input('Search.academic_year', array('options' => $acyear_array_data, /* 'empty' => '[ Select Academic Year ] ', */ 'style' => 'width: 90%;', 'required' => true, 'id' => 'AcademicYear', 'onchange' => 'updateCourseListOnChangeofOtherField()')); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->input('Search.semester', array('options' => Configure::read('semesters'), 'style' => 'width: 90%;', 'required' => true, 'id' => 'Semester', 'onchange' => 'updateCourseListOnChangeofOtherField()')) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="large-6 columns">
                            <?php
                            if (isset($departments) &&  !empty($departments)) { ?>
                                <?= $this->Form->input('Search.department_id', array('label' => 'Department', 'id' => 'DepartmentId', 'style' => 'width: 90%;', 'onchange' => 'updateCourseListOnChangeofOtherField()')); ?>
                                <?php
                            } else if (isset($colleges) && !empty($colleges)) { ?>
                                <?= $this->Form->input('Search.college_id', array('onchange' => 'updateCourseListOnChangeofOtherField()', 'style' => 'width: 90%;', 'id' => 'CollegeId', 'label' => 'College/Institute/School: ')); ?></td>
                                <?php
                            } ?>
                        </div>
                        <div class="large-6 columns">
                            <?= $this->Form->input('Search.section_id', array('id' => 'SectionId', /* 'empty' => '[ No Results, Try Changing Search Filters ]', */ 'required', 'style' => 'width: 90%;')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="large-4 columns">
                            <?= $this->Form->input('Search.studentnumber', array('id' => 'studentNumber', 'style' => 'width: 90%;')); ?>
                        </div>
                    </div>
                    <hr>
                    <?= $this->Form->submit('Search', array('class' => 'tiny radius button bg-blue', 'name' => 'search', 'div' => false, 'id' => 'Search')) . ''; ?>
                </fieldset>
            </div>
            <?php
        } else if ($role_id == ROLE_STUDENT) {  ?>
            <div style="margin-top: -30px;">
                <fieldset style="padding-bottom: 0px; padding-top: 15px;">
                    <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
                    <div class="row">
                        <div class="large-3 columns">
                            <?= $this->Form->input('Search.academic_year', array('style' => 'width: 90%;', 'options' => $acadamic_years, 'default' => isset($defaultacademicyear) && !empty($defaultacademicyear) ? $defaultacademicyear : false)); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->input('Search.semester', array('style' => 'width: 90%;', 'options' => Configure::read('semesters'))); ?>
                        </div>
                        <div class="large-6 columns">
                            &nbsp;
                        </div>
                    </div>
                    <hr>
                    <?= $this->Form->submit('Search', array('class' => 'tiny radius button bg-blue', 'name' => 'search', 'div' => false, 'id' => 'Search')) . ''; ?>
                </fieldset>
            </div>
            <?php
        } ?>
    </div>
</div>

<?php
if (isset($courseRegistrations) && !empty($courseRegistrations)) { ?>
    <div class="box">
        <div class="box-header bg-transparent">
            <!-- <div class="pull-right box-tools">
                <span class="box-btn" data-widget="collapse"><i class="icon-minus"></i></span>
                <span class="box-btn" data-widget="remove"><i class="icon-cross"></i></span>
            </div> -->
            <h3 class="box-title"><i class="fontello-search-outline" style="font-size: larger; font-weight: bold;"></i><span>SEARCH RESULTS</span></h3>
        </div>

        <div class="box-body " style="display: block;">
            <?php
            if ($role_id != ROLE_STUDENT) {  ?>
                <div class="row" style="margin-bottom:10px;">
                    <div class="large-12 columns" style="margin-top: -10px;">
                        <?php
                        if ($role_id == ROLE_REGISTRAR) { ?>
                            <?= $this->Form->submit('Generate Registration Slip', array('class' => 'tiny radius button bg-blue', 'name' => 'generateSlip', 'div' => false)); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?= $this->Form->submit('Get Grade Report', array('class' => 'tiny radius button bg-blue', 'name' => 'getGradeReport', 'div' => false)); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php
                        } 
                        if (empty($this->request->data['Search']['studentnumber']) || empty($this->request->data['Search']['studentnumber'])) { ?>
                            <?= $this->Form->submit('Generate Registered List', array('class' => 'tiny radius button bg-blue', 'name' => 'generateRegisteredList', 'div' => false)); ?>
                            <?php
                        } ?>
                        <br><br>
                    </div>

                    <div class="large-4 columns">
                        <input class="form-control" id="filter" placeholder="Filter Results..." type="text" />
                    </div>
                    <div class="large-8 columns">
                        <a href="#clear" style="margin-left:10px;" class="clear-filter tiny radius button bg-orange" title="Clear Filter">Clear Filter</a>
                    </div>
                </div>
                <?php
            } else if ($role_id == ROLE_STUDENT && (ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS != 0 || ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS != '0')) { 
                
                $enable_slip_pdf_download = 0;
                $enable_grade_report_pdf_download = 0;

                if (!empty($courseRegistrations)) {

                    $studentID = $courseRegistrations[0]['Student']['id'];
                    $programID = $courseRegistrations[0]['Student']['program_id'];
                    $programTypeID = $courseRegistrations[0]['Student']['program_type_id'];
                    $studentNumber = $courseRegistrations[0]['Student']['studentnumber'];

                    if (ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS == 1 || ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS == '1') {
                        $enable_slip_pdf_download = 1;
                    } else if (ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS == 'AUTO') {

                        $generalSettings = ClassRegistry::init('GeneralSetting')->getAllGeneralSettingsByStudentByProgramIdOrBySectionID($studentID, $programID, $programTypeID, null);

                        if (!empty($generalSettings['GeneralSetting'])) {
                            //debug($generalSettings['GeneralSetting']);
                            $enable_slip_pdf_download = $generalSettings['GeneralSetting']['allowRegistrationSlipPdfDownloadToStudents'];
                        }

                    }

                    if (ALLOW_GRADE_REPORT_PDF_DOWNLOAD_TO_STUDENTS == 1 || ALLOW_GRADE_REPORT_PDF_DOWNLOAD_TO_STUDENTS == '1') {
                        $enable_grade_report_pdf_download = 1;
                    } else if (ALLOW_GRADE_REPORT_PDF_DOWNLOAD_TO_STUDENTS == 'AUTO') {

                        if (!isset($generalSettings)) {
                            $generalSettings = ClassRegistry::init('GeneralSetting')->getAllGeneralSettingsByStudentByProgramIdOrBySectionID($studentID, $programID, $programTypeID, null);
                        }

                        if (!empty($generalSettings['GeneralSetting'])) {
                            //debug($generalSettings['GeneralSetting']);
                            $enable_grade_report_pdf_download = $generalSettings['GeneralSetting']['allowGradeReportPdfDownloadToStudents'];
                        }

                    }
                }

                if ($enable_slip_pdf_download || $enable_grade_report_pdf_download) { ?>
                    <div class="row" style="margin-bottom:10px;">
                        <div class="large-12 columns" style="margin-top: -10px;">
                            <?= $this->Form->hidden('Search.studentnumber', array('id' => 'studentNumber', 'value' => $studentNumber)); ?>
                            <?php
                            if ($enable_slip_pdf_download) { ?>
                                <?= $this->Form->submit('Get Registration Slip', array('class' => 'tiny radius button bg-blue', 'name' => 'generateSlip', 'div' => false)); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php
                            } 

                            // debug($this->data['Search']['academic_year']);
                            // debug(array_values($status_generated_acy_semester));

                            // debug(in_array($this->data['Search']['academic_year'], array_values($status_generated_acy_semester)) && in_array($this->data['Search']['semester'], array_values($status_generated_acy_semester)) );

                            if ($enable_grade_report_pdf_download && !empty($status_generated_acy_semester) && in_array($this->data['Search']['academic_year'], $status_generated_acy_semester) && in_array($this->data['Search']['semester'], $status_generated_acy_semester)) { ?>
                                <?= $this->Form->submit('Get Grade Report', array('class' => 'tiny radius button bg-blue', 'name' => 'getGradeReport', 'div' => false)); ?>
                                <?php
                            } else { ?>
                                <?= $this->Form->submit('Get Grade Report', array('class' => 'tiny radius button bg-blue', 'name' => 'getGradeReport222', 'div' => false, 'disabled')); ?>
                                <?php
                            } ?>
                            <br><br>
                        </div>
                    </div>
                    <?php
                }
            } ?>

            <?= $this->Form->end(); ?>

            <table id="footable-res2" class="demo table" data-filter="#filter" data-page-size=20 data-filter-text-only="true">
                <thead>
                    <tr>
                        <!-- <th data-hide="phone">#</th> -->
                        <th data-toggle="true" style="text-align: center; vertical-align: middle;">Student ID</th>
                        <th data-hide="phone" style="text-align: center; vertical-align: middle;">Full Name</th>
                        <th data-hide="phone,tablet" style="text-align: center; vertical-align: middle;">Department</th>
                        <th data-hide="phone,tablet" style="text-align: center; vertical-align: middle;">Program</th>
                        <th data-hide="phone,tablet" style="text-align: center; vertical-align: middle;">Program Type</th>
                        <th style="text-align: center; vertical-align: middle;">Year</th>
                        <th style="text-align: center; vertical-align: middle;">ACY</th>
                        <th style="text-align: center; vertical-align: middle;">SEM</th>
                        <th data-hide="phone,tablet" style="text-align: center; vertical-align: middle;">Course</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $start = 1;
                    foreach ($courseRegistrations as $courseRegistration) { ?>
                        <tr>
                            <!-- <td><?= $start++ ?></td> -->
                            <td style="text-align: center; vertical-align: middle;"><?= $this->Html->link($courseRegistration['Student']['studentnumber'], '#', array('class' => 'jsview', 'data-animation' => "fade", 'data-reveal-id' => 'myModal', 'data-reveal-ajax' => "/students/get_modal_box/" . $courseRegistration['Student']['id'])); ?></td>
                            <td style="text-align: center; vertical-align: middle;"><?= $courseRegistration['Student']['full_name']; ?></td>
                            <td style="text-align: center; vertical-align: middle;"><?= (isset($courseRegistration['PublishedCourse']['Department']['name']) ? $courseRegistration['PublishedCourse']['Department']['name'] : ($courseRegistration['Student']['Program']['name'] == PROGRAM_REMEDIAL ? $courseRegistration['PublishedCourse']['College']['shortname']. ' - Remedial' :  $courseRegistration['PublishedCourse']['College']['shortname'] . ' - Pre/Freshman')); ?></td>
                            <td style="text-align: center; vertical-align: middle;"><?= $courseRegistration['Student']['Program']['name']; ?></td>
                            <td style="text-align: center; vertical-align: middle;"><?= $courseRegistration['Student']['ProgramType']['name']; ?></td>
                            <td style="text-align: center; vertical-align: middle;"><?= (isset($courseRegistration['YearLevel']['name']) ? $courseRegistration['YearLevel']['name'] : ($courseRegistration['Student']['Program']['name'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')); ?></td>
                            <td style="text-align: center; vertical-align: middle;"><?= $courseRegistration['CourseRegistration']['academic_year']; ?></td>
                            <td style="text-align: center; vertical-align: middle;"><?= $courseRegistration['CourseRegistration']['semester']; ?></td>
                            <td style="text-align: center; vertical-align: middle;">
                                <?= $this->Html->link($courseRegistration['PublishedCourse']['Course']['course_code_title'], array('controller' => 'courses', 'action' => 'view', $courseRegistration['PublishedCourse']['Course']['id'])); ?>
                                <?= (isset($courseRegistration['CourseDrop'][0]) && $courseRegistration['CourseDrop'][0]['department_approval'] == 1 && count($courseRegistration['CourseDrop']) > 0 && $courseRegistration['CourseDrop'][0]['registrar_confirmation'] == 1 ? "<b style='color:red'> - Dropped </b>" : ''); ?>
                            </td>
                        </tr>
                        <?php
                    } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9">
                            <div class="pagination pagination-centered"></div>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <?php //echo $this->Form->end(); ?>
        </div>
    </div>
    <?php
} else { ?>


    <?php
} ?>

<script>
    function updateCourseListOnChangeofOtherField() {

        //AcademicYear Semester ProgramTypeId SectionId DepartmentId CollegeId ProgramId
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
        var formUrl = '/courseRegistrations/get_section_combo_for_view/' + formData;
        //alert(formUrl);

        $.ajax({
            type: 'get',
            url: formUrl,
            data: formData,
            success: function(data, textStatus, xhr) {
                $("#AcadamicYear").attr('disabled', false);
                $("#Semester").attr('disabled', false);
                $("#Program").attr('disabled', false);
                $("#ProgramType").attr('disabled', false);
                $("#department_id").attr('disabled', false);
                $("#college_id").attr('disabled', false);
                $("#SectionId").attr('disabled', false);
                $("#SectionId").empty();
                $("#SectionId").append(data);
            },
            error: function(xhr, textStatus, error) {
                //alert(textStatus);
            }
        });

        $("#Search").attr('disabled', false);
        return false;
    }
</script>