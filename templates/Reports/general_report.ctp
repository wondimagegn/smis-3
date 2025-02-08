<?php
$enableDisplayExport = 0;

if (isset($this->data['Report']['report_type'])) {
    if (isset($attrationRate) && !empty($attrationRate)) {
        $enableDisplayExport = 1;
    } else if (isset($top) && !empty($top)) {
        $enableDisplayExport = 1;
    } else if (isset($dismissedList) && !empty($dismissedList)) {
        $enableDisplayExport = 1;
    } else if (isset($admittedMoreThanOneProgram) && !empty($admittedMoreThanOneProgram)) {
        $enableDisplayExport = 1;
    } else if (isset($resultBy) && !empty($resultBy)) {
        $enableDisplayExport = 1;
    } else if (isset($gradeChangeLists) && !empty($gradeChangeLists)) {
        $enableDisplayExport = 1;
    } else if (isset($gradeSubmissionDelay) && !empty($gradeSubmissionDelay)) {
        $enableDisplayExport = 1;
    } else if (isset($studentList) && !empty($studentList)) {
        $enableDisplayExport = 1;
    } else if (isset($delayedGradeSubmissionReportList) && !empty($delayedGradeSubmissionReportList)) {
        $enableDisplayExport = 1;
    } else if (isset($distributionStatistics) && !empty($distributionStatistics)) {
        $enableDisplayExport = 1;
    } else if (isset($notAssignedCourseeList) && !empty($notAssignedCourseeList)) {
        $enableDisplayExport = 1;
    } else if (isset($distributionStatisticsStatus['distributionByStatusYearLevel']) && !empty($distributionStatisticsStatus['distributionByStatusYearLevel'])) {
        $enableDisplayExport = 1;
    } else if (isset($distributionStatsLetterGrade['distributionLetterGrade']) && !empty($distributionStatsLetterGrade['distributionLetterGrade'])) {
        $enableDisplayExport = 1;
    } else if (isset($activeList) && !empty($activeList)) {
        $enableDisplayExport = 1;
    } else if (isset($notRegisteredList) && !empty($notRegisteredList)) {
        $enableDisplayExport = 1;
    } else if (isset($registeredList) && !empty($registeredList)) {
        $enableDisplayExport = 1;
    } else if (isset($graduateRateToEntry['distributionGraduateEntry']) && !empty($graduateRateToEntry['distributionGraduateEntry'])) {
        $enableDisplayExport = 1;
    } else if (isset($studentResultsHEMIS) && !empty($studentResultsHEMIS)) {
        $enableDisplayExport = 1;
    } else if (isset($studentGraduateHEMIS) && !empty($studentGraduateHEMIS)) {
        $enableDisplayExport = 1;
    } else if (isset($studentListForExitExam) && !empty($studentListForExitExam)) {
        $enableDisplayExport = 1;
    } else if (isset($studentListForOffice) && !empty($studentListForOffice)) {
        $enableDisplayExport = 1;
    } else if (isset($studentListForElearning) && !empty($studentListForElearning)) {
        $enableDisplayExport = 1;
    } else if (isset($studentEnrolmentHEMIS) && !empty($studentEnrolmentHEMIS)) {
        $enableDisplayExport = 1;
    }

} ?>

<div class="box" ng-app="generalReport">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-chart-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= 'General Reports'; ?></span>
		</div>
	</div>
    <div class="box-body" ng-controller="reportCntrl">
        <div class="row">
            <div class="large-12 columns">
                <div class="form">
                    <?= $this->Form->create('Report'); ?>

                    <?php
                    if (isset($headerLabel) && !empty($headerLabel)) {
                        $this->assign('title_details', (!empty($this->request->params['controller']) ? ' ' . Inflector::humanize(Inflector::underscore($this->request->params['controller'])) . (!empty($this->request->params['action']) && $this->request->params['action'] != 'index' ? ' | ' . ucwords(str_replace('_', ' ', $this->request->params['action'])) : '') : '') . ' - ' . $headerLabel);
                    } ?>

                    <div style="margin-top: -30px;"><hr></div>

                    <blockquote>
                        <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                        <span style="text-align:justify;" class="fs16 text-black">This tool will help you to get some predefined reports by providing some search criteria.</span> 
                    </blockquote>
                    <hr>

                    <div onclick="toggleViewFullId('ListPublishedCourse')">
                        <?php
                        if ($enableDisplayExport) {
                            echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
                            <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Display Filter</span>
                            <?php
                        } else {
                            echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
                            <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Hide Filter</span>
                            <?php
                        } ?>
                    </div>

                    <div id="ListPublishedCourse" style="display:<?= ($enableDisplayExport ? 'none' : 'display'); ?>">
                        <fieldset style="padding-bottom: 0px; padding-top: 15px;">
                            <!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->

                            <div class="row align-items-center">
                                <div class="large-6 columns">
                                    <?= $this->Form->input('report_type', array('label' => 'Report Type: ', 'type' => 'select', 'style' => 'width:90%;', 'div' => false, 'empty' => '[ Select Report Type ]', 'onchange' => 'toggleFields()', 'options' => $report_type_options, 'id' => 'reportType', 'required' => 'required')); ?>
                                </div>
                                <div class="large-6 columns">
                                    <div class="TopLimit" style="display:none;">
                                        <?= $this->Form->input('top', array('id' => 'Top', 'class' => 'fs13', 'type' => 'number', 'min'=>'10',  'max'=>'20000', 'step'=>'10', 'value' => (isset($this->data['Report']['top']) ? $this->data['Report']['top'] : '10'), 'label' => 'Top: ', 'onkeypress' => 'validate(event)', 'style' => 'width:30%;')); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row align-items-center">
                                <div class="large-6 columns">
                                    <?= $this->Form->input('department_id', array('class' => 'fs14', 'style' => 'width:90%;', 'label' => 'College / Department: ', 'type' => 'select', 'options' => $departments/* , 'default' => $default_department_id */)); ?>
                                    
                                    <div class="Freshman" style="display:none;">
                                        <?= $this->Form->input('freshman', array('id' => 'freshman', 'label' => 'Only Freshman / Remedial', 'type' => 'checkbox')); ?>
                                        <!-- <br style="line-height: 0.1;"> -->
                                    </div>
                                </div>
                                <div class="large-6 columns">
                                    <div class='YearLevel' style='display:none;'>
                                        <?php
                                        if ((isset($freshman) && $freshman) || $only_pre_assigned_user) {
                                            if ($only_pre_assigned_user) {
                                                $default_year_level_id = '';
                                            } else {
                                                $yearLevels = array('' => 'Pre/Fresh/Remedial') + $yearLevels;
                                                $default_year_level_id = '';
                                            } ?>
                                            <?= $this->Form->input('year_level_id', array('id' => 'YearLevel', 'class' => 'fs13', 'label' => 'Year Level: ', 'style' => 'width:40%;', 'type' => 'select', 'options' => $yearLevels, 'default' => $default_year_level_id)); ?>
                                            <?php
                                        } else { ?>
                                            <?= $this->Form->input('year_level_id', array('id' => 'YearLevel', 'class' => 'fs13', 'label' => 'Year Level: ', 'style' => 'width:40%;', 'type' => 'select', 'options' => $yearLevels, 'default' => $default_year_level_id)); ?>
                                            <?php
                                        } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row align-items-center">
                                <div class="large-6 columns">
                                    <div class="ExcludeGraduated" style="display:none;">
                                        <?= $this->Form->input('exclude_graduated', array('id' => 'excludeGraduated', 'label' => 'Exclude Graduated', 'type' => 'checkbox')); ?>
                                        <br style="line-height: 0.25;">
                                    </div>
                                </div>
                                <div class="large-6 columns">
                                    <div class="OnlyWithCompleteData" style="display:none;">
                                        <?= $this->Form->input('only_with_complete_data', array('id' => 'onlyWithCompleteData', 'label' => 'Only With Complete Data', 'type' => 'checkbox')); ?>
                                        <br style="line-height: 0.25;">
                                    </div>
                                    <div class="extendedReportForExitExam" style="display:none;">
                                        <?= $this->Form->input('get_extended_report_for_exit_exam', array('id' => 'extendedReportForExitExam', 'label' => 'Get Extended Report <span class="rejected">(Very slaw, select this for a department only and if it is necessary)</span>', 'type' => 'checkbox')); ?>
                                        <br style="line-height: 0.25;">
                                    </div>
                                </div>
                            </div>

                            <div class="row align-items-center">
                                <div class="large-3 columns">
                                    <?= $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => 'Acadamic Year: ', 'class' => 'fs14', 'style' => 'width:50%;', 'type' => 'select', 'options' => $acyear_array_data, 'default' => $defaultacademicyear)); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:50%;', 'label' => 'Semester: ', 'options' => Configure::read('semesters'), 'default' => $defaultsemester)); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'style' => 'width:90%;', 'label' => 'Program: ', 'type' => 'select', 'options' => $programs, /* 'default' => $default_program_id */)); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'style' => 'width:90%;', 'label' => 'Program Type: ', 'type' => 'select', 'options' => $program_types, /* 'default' => $default_program_type_id */)); ?>
                                </div>
                            </div>

                            <div class="row SexAndRegion" style="display:none;">
                                <div class="large-3 columns">
                                    <div class="Sex" style="display:none;">
                                        <?= $this->Form->input('gender', array('id' => 'Gender', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:40%;', 'label' => 'Sex: ', 'options' => array('all' => 'All', 'female' => 'Female', 'male' => 'Male'))); ?>
                                    </div>
                                </div>
                                <div class="large-3 columns">
                                    <div class="Region" style="display:none;">
                                        <?= $this->Form->input('region_id', array('label' => 'Region: ', 'type' => 'select', 'style' => 'width:90%;', 'div' => false, 'default' => $default_region_id, 'options' => $regions)); ?>
                                    </div>
                                </div>
                                <div class="large-6 columns">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="row academicStatus" style="display:none;">
                                <div class="large-3 columns">
                                    <?= $this->Form->input('academic_status_id', array('label' => 'Academic Status: ', 'style' => 'width:60%;', 'empty' => 'All')); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('gpa', array('label' => 'SGPA/CGPA', 'style' => 'width:60%;', 'options' => array('cgpa' => 'CGPA', 'sgpa' => 'SGPA') , 'default' => 'cgpa')); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('from', array('id' => 'From', 'class' => 'fs13', 'label' => 'From: ', 'style' => 'width:40%;', 'type' => 'number', 'min' => 0, 'max' => 4, 'step' => '0.01', 'value' => (isset($this->data['Report']['from']) ? $this->data['Report']['from'] : '0.00'))); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('to', array('id' => 'To', 'class' => 'fs13', 'type' => 'number', 'style' => 'width:40%;', 'label' => 'To: ', 'min' => 0, 'max' => 4, 'step' => '0.01', 'value' => (isset($this->data['Report']['to']) ? $this->data['Report']['to'] : '4.00'))); ?>
                                </div>
                            </div>

                            <div class="row gpaOnly" style="display:none;">
                                <div class="large-3 columns">
                                    <?= $this->Form->input('gpa', array('label' => 'SGPA/CGPA', 'style' => 'width:60%;', 'options' => array('cgpa' => 'CGPA', 'sgpa' => 'SGPA') , 'default' => 'cgpa')); ?>
                                </div>
                                <div class="large-9 columns">
                                    &nbsp;
                                </div>
                            </div>

                            <div class="row visibleOnDistribution" style="display:none;">
                                <div class="large-3 columns">
                                    <?= $this->Form->input('graph_type', array('label' => 'Graph Type: ','style' => 'width:80%;', 'type' => 'select', 'div' => false, 'options' => $graph_type)); ?>
                                </div>
                                <div class="large-9 columns">
                                    &nbsp;
                                </div>
                            </div>
                            <hr>
                            <?= $this->Form->submit(__('Get Report', true), array('name' => 'getReport', 'id' => 'getReport', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
                        </fieldset>
                    </div>
                    <hr>
                </div>

                <div id="processingMessage" style="display:none;">Processing, please wait...</div>

                <div id="show_report_details">
                    <?php
                    if ($enableDisplayExport == 1 ) {
                        echo $this->Form->submit(__('Export Report to Excel', true), array('name' => 'getReportExcel', 'id' => 'getReportExcel1', 'div' => false, 'class' => 'tiny radius button bg-blue', 'onclick' => '')) . '<hr>';
                    } ?>

                    <?php
                    if (!empty($headerLabel)) {
                        echo '<h6 class"fs10 text-gray">'. $headerLabel . '</h6><hr>';
                    }

                    if (isset($this->data['Report']['report_type'])) {
                        if (isset($attrationRate) && !empty($attrationRate)) {
                            echo $this->element('reports/attration_rate_stats');
                        } else if (isset($top) && !empty($top)) {
                            echo $this->element('reports/top_student');
                        } else if (isset($dismissedList) && !empty($dismissedList)) {
                            echo $this->element('reports/dismissed_list');
                        } else if (isset($admittedMoreThanOneProgram) && !empty($admittedMoreThanOneProgram)) {
                            echo $this->element('reports/duplicate_admission');
                        } else if (isset($resultBy) && !empty($resultBy)) {
                            echo $this->element('reports/result_range_list');
                        } else if (isset($gradeChangeLists) && !empty($gradeChangeLists)) {
                            echo $this->element('reports/grade_change_list');
                        } else if (isset($gradeSubmissionDelay) && !empty($gradeSubmissionDelay)) {
                            echo $this->element('reports/grade_submission_delay_list');
                        } else if (isset($studentList) && !empty($studentList)) {
                            echo $this->element('reports/list_bygrade_student');
                        } else if (isset($delayedGradeSubmissionReportList) && !empty($delayedGradeSubmissionReportList)) {
                            echo $this->element('reports/delayed_grade_submission_report_list');
                        } else if (isset($distributionStatistics) && !empty($distributionStatistics)) {
                            echo $this->element('reports/distribution_stat');
                        } else if (isset($notAssignedCourseeList) && !empty($notAssignedCourseeList)) {
                            echo $this->element('reports/course_not_assigned');
                        } else if (isset($distributionStatisticsStatus['distributionByStatusYearLevel']) && !empty($distributionStatisticsStatus['distributionByStatusYearLevel'])) {
                            echo $this->element('reports/distribution_status_stat');
                        } else if (isset($distributionStatsLetterGrade['distributionLetterGrade']) && !empty($distributionStatsLetterGrade['distributionLetterGrade'])) {
                            echo $this->element('reports/distribution_stats_letter_grade');
                        } else if (isset($activeList) && !empty($activeList)) {
                            echo $this->element('reports/active_list');
                        } else if (isset($notRegisteredList) && !empty($notRegisteredList)) {
                            echo $this->element('reports/not_registered_list');
                        } else if (isset($registeredList) && !empty($registeredList)) {
                            echo $this->element('reports/registered_list');
                        } else if (isset($graduateRateToEntry['distributionGraduateEntry']) && !empty($graduateRateToEntry['distributionGraduateEntry'])) {
                            echo $this->element('reports/distribution_entry_graduate');
                        } else if (isset($studentResultsHEMIS) && !empty($studentResultsHEMIS)) {
                            echo $this->element('reports/get_student_results_for_hemis');
                        } else if (isset($studentGraduateHEMIS) && !empty($studentGraduateHEMIS)) {
                            echo $this->element('reports/get_student_graduate_for_hemis');
                        } else if (isset($studentListForExitExam) && !empty($studentListForExitExam)) {
                            echo $this->element('reports/get_student_list_for_exit_exam');
                        } else if (isset($studentListForOffice) && !empty($studentListForOffice)) {
                            echo $this->element('reports/student_list_for_office');
                        } else if (isset($studentListForElearning) && !empty($studentListForElearning)) {
                            echo $this->element('reports/student_list_for_elearning');
                        } else if (isset($studentEnrolmentHEMIS) && !empty($studentEnrolmentHEMIS)) {
                            echo $this->element('reports/get_student_enrolment_for_hemis');
                        } else { ?>
                            <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no data for the report with the selected search criteria.</div>
                            <?php
                        }

                        if ($enableDisplayExport == 1 ) {
                            echo '<hr>' . $this->Form->submit(__('Export Report to Excel', true), array('name' => 'getReportExcel', 'id' => 'getReportExcel2', 'div' => false, 'class' => 'tiny radius button bg-blue', 'onclick' => ''));
                        }
                    } ?>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function toggleView(obj) {
        if ($('#c' + obj.id).css("display") == 'none') {
            $('#i' + obj.id).attr("src", '/img/minus2.gif');
        } else {
            $('#i' + obj.id).attr("src", '/img/plus2.gif');
        }
        $('#c' + obj.id).toggle("slow");
    }

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

    function toggleFields(id) {
        if ($('#reportType').val() == '') {
            $(".ExcludeGraduated").hide("fast");
            $(".Freshman").hide("fast");
            //$(".YearLevel").show("fast");
            $(".Region").hide("fast");
            $(".SexAndRegion").hide("fast");
            $(".Sex").hide("fast");
            $(".academicStatus").hide("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'attrition_rate') {
            $(".ExcludeGraduated").hide("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".Region").show("fast");
            $(".SexAndRegion").show("fast");
            $(".Sex").show("fast");
            $(".academicStatus").hide("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'distributionStatsGender' ||
            $('#reportType').val() == 'distributionStatsGenderAndRegion' ||
            $('#reportType').val() == 'distributionStatsStatus' ||
            $('#reportType').val() == 'distributionStatsGraduate' ||
            $('#reportType').val() == 'distributionStatsLetterGrade' ||
            $('#reportType').val() == 'graduatedRateCompareToEntry' ||
            $('#reportType').val() == 'enrollStatistics'
            //|| $('#reportType').val() == 'distributionStatsGrade'
        ) {
            $('.visibleOnDistribution').show("fast");
            $('.academicStatus').hide("fast");
            $(".ExcludeGraduated").hide("fast");
            $(".SexAndRegion").show("fast");
            $(".Sex").show("fast");
            $(".Region").show("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'top_students') {
            $(".SexAndRegion").show("fast");
            $(".academicStatus").hide("fast");
            $(".ExcludeGraduated").show("fast");
            $(".Region").hide("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".Sex").show("fast");
            $(".TopLimit").show("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".gpaOnly").show("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'get_student_list_for_exit_exam') {
            $(".academicStatus").hide("fast");
            $('#excludeGraduated').prop('checked', true);
            $(".ExcludeGraduated").show("fast");
            $(".Sex").show("fast");
            $(".Region").show("fast");
            $(".Freshman").hide("fast");
            $("#YearLevel").val('0');
            $(".YearLevel").hide("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".gpaOnly").hide("fast");
            $(".extendedReportForExitExam").show("fast");
            $("#Program").val(1);
        } else if ($('#reportType').val() == 'grade_change_statistics') {
            $(".academicStatus").hide("fast");
            $(".ExcludeGraduated").hide("fast");
            $(".Region").hide("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'academic_status_range') {
            $(".SexAndRegion").show("fast");
            $(".academicStatus").show("fast");
            $(".ExcludeGraduated").show("fast");
            $(".Region").hide("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".visibleOnDistribution").hide("fast");
            $(".Sex").show("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'get_student_results_for_hemis' ||
            $('#reportType').val() == 'get_student_enrolment_for_hemis'
        ) {
            $(".SexAndRegion").hide("fast");
            $(".academicStatus").hide("fast");
            $(".ExcludeGraduated").show("fast");
            $(".Sex").hide("fast");
            $(".Region").hide("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").show("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'get_student_graduate_for_hemis') {
            $(".SexAndRegion").hide("fast");
            $(".academicStatus").hide("fast");
            $(".ExcludeGraduated").hide("fast");
            $(".Sex").hide("fast");
            $(".Region").hide("fast");
            $(".Freshman").hide("fast");
            $(".YearLevel").hide("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").show("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'active_student_list' ||
            $('#reportType').val() == 'notRegisteredList' ||
            $('#reportType').val() == 'registeredList' ||
            $('#reportType').val() == 'listFx' ||
            $('#reportType').val() == 'listF' ||
            $('#reportType').val() == 'listNG' ||
            $('#reportType').val() == 'dismissed_student_list' ||
            $('#reportType').val() == 'studentListForOffice' ||
            $('#reportType').val() == 'studentListForElearning'
        ) {
            $(".SexAndRegion").show("fast");
            $(".academicStatus").hide("fast");
            $(".ExcludeGraduated").show("fast");
            $(".Region").hide("fast");
            $(".Sex").show("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'delayedCountGradeSubmissionList' ||
            $('#reportType').val() == 'gradeSubmittedInstructorList' ||
            $('#reportType').val() == 'lateGradeSubmission' ||
            $('#reportType').val() == 'getGradeChangeList' ||
            $('#reportType').val() == 'notAssignedCourseeList' ||
            $('#reportType').val() == 'admittedMoreThanOneProgram'
        ) {
            $(".SexAndRegion").hide("fast");
            $(".academicStatus").hide("fast");
            $('.visibleOnDistribution').hide("fast");
            $(".ExcludeGraduated").hide("fast");
            $(".Region").hide("fast");
            $(".Sex").hide("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".extendedReportForExitExam").hide("fast");
        }
    }

    var selected_report_type = '';

    if ($("#reportType").val() != '') {
        selected_report_type = $("#reportType option:selected").text();
    }

    //alert(selected_report_type);

    var freshman = <?= $freshman; ?>;
    var excludeGraduated = <?= $exclude_graduated; ?>;
    var extendedReportExitExam = <?= $get_extended_report_for_exit_exam; ?>;

    $(document).ready(function() {

        if ($("#extendedReportForExitExam").is(":checked") || extendedReportExitExam) {
            $("#YearLevel").val('0');
            $(".YearLevel").hide("fast");
            $(".ExcludeGraduated").css("display", "block");
            $("#excludeGraduated").prop('checked', true);
            $('#freshman').prop('checked', false);
            $(".Freshman").hide("fast");
            $(".extendedReportForExitExam").css("display", "block");
            $("#Program").val(1);
            $(".Sex").show("fast");
            $(".Region").show("fast");
            $(".gpaOnly").hide("fast");
        } else if ($("#freshman").is(":checked") || freshman) {
            $(".YearLevel").hide("fast");
            $(".YearLevel").css("display", "none");
            $(".ExcludeGraduated").hide("fast");
            $(".ExcludeGraduated").css("display", "none");
            $(".Freshman").css("display", "block");
            $("#YearLevel").val('');
            $("#freshman").val(1);
        } else if ($("#excludeGraduated").is(":checked") || excludeGraduated) {
            //$(".Freshman").css("display", "none");
            $(".ExcludeGraduated").css("display", "block");
            //$(".Freshman").hide("fast");
            $("#excludeGraduated").val(1);
        }

        $("#freshman").click(function() {
            if ($("#freshman").is(":checked")) {
                $(".YearLevel").hide("fast");
                //$("#Program").val(1);
                //$("#ProgramType").val(1);
                $("#YearLevel").val('');
                
                if ($("#excludeGraduated").is(":checked")) {
                    $('#excludeGraduated').attr('checked', false);
                }
                $(".ExcludeGraduated").hide("fast");
                
            } else {
                $("#YearLevel").val(0);
                $(".YearLevel").show("fast");
                $("#excludeGraduated").val(0);
                $(".ExcludeGraduated").show("fast");
            }
        });

        $("#excludeGraduated").click(function() {
            if ($("#excludeGraduated").is(":checked")) {
                if ($("#freshman").is(":checked")) {
                    $('#freshman').attr('checked', false);
                }
                $(".Freshman").hide("fast");
            } else {
                $(".Freshman").show("fast");
            }
        });

        $("#extendedReportForExitExam").click(function() {
            $("#excludeGraduated").attr('checked', true);
            $(".ExcludeGraduated").show("fast");
            $('#freshman').attr('checked', false);
            $(".Freshman").hide("fast");
            $("#YearLevel").val(0);
            $(".YearLevel").hide("fast");
            $("#Program").val(1);
            $(".Sex").show("fast");
            $(".Region").show("fast");
            $(".gpaOnly").hide("fast");
        });

        if ($('#reportType').val() == '') {
            $(".ExcludeGraduated").hide("fast");
            $(".Freshman").hide("fast");
            $(".YearLevel").show("fast");
            //$(".Region").hide("fast");
            $(".SexAndRegion").hide("fast");
            $(".Sex").hide("fast");
            $(".academicStatus").hide("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'attrition_rate') {
            $(".ExcludeGraduated").hide("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".Region").show("fast");
            $(".SexAndRegion").show("fast");
            $(".Sex").show("fast");
            $(".academicStatus").hide("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'distributionStatsGender' ||
            $('#reportType').val() == 'distributionStatsGenderAndRegion' ||
            $('#reportType').val() == 'distributionStatsStatus' ||
            $('#reportType').val() == 'distributionStatsGraduate' ||
            $('#reportType').val() == 'distributionStatsLetterGrade' ||
            $('#reportType').val() == 'graduatedRateCompareToEntry' ||
            $('#reportType').val() == 'enrollStatistics'
            //|| $('#reportType').val() == 'distributionStatsGrade'
        ) {
            $('.visibleOnDistribution').show("fast");
            $('.academicStatus').hide("fast");
            $(".ExcludeGraduated").hide("fast");
            $(".SexAndRegion").show("fast");
            $(".Sex").show("fast");
            $(".Region").show("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'top_students') {
            $(".SexAndRegion").show("fast");
            $(".academicStatus").hide("fast");
            $(".ExcludeGraduated").show("fast");
            $(".Region").hide("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".Sex").show("fast");
            $(".TopLimit").show("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".gpaOnly").show("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'get_student_list_for_exit_exam') {
            $(".academicStatus").hide("fast");
            $('#excludeGraduated').prop('checked', true);
            $(".ExcludeGraduated").show("fast");
            $(".Sex").show("fast");
            $(".Region").show("fast");
            $(".Freshman").hide("fast");
            $("#YearLevel").val('0');
            $(".YearLevel").hide("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".gpaOnly").show("fast");
            $(".extendedReportForExitExam").show("fast");
            $("#Program").val(1);
            $(".gpaOnly").hide("fast");
        } else if ($('#reportType').val() == 'grade_change_statistics') {
            $(".academicStatus").hide("fast");
            $(".ExcludeGraduated").hide("fast");
            $(".Region").hide("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
           $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'academic_status_range') {
            $(".SexAndRegion").show("fast");
            $(".academicStatus").show("fast");
            $(".ExcludeGraduated").show("fast");
            $(".Region").hide("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".visibleOnDistribution").hide("fast");
            $(".Sex").show("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'get_student_results_for_hemis' ||
            $('#reportType').val() == 'get_student_enrolment_for_hemis'
        ) {
            $(".SexAndRegion").hide("fast");
            $(".academicStatus").hide("fast");
            $(".ExcludeGraduated").show("fast");
            $(".Sex").hide("fast");
            $(".Region").hide("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").show("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'get_student_graduate_for_hemis') {
            $(".SexAndRegion").hide("fast");
            $(".academicStatus").hide("fast");
            $(".ExcludeGraduated").hide("fast");
            $(".Sex").hide("fast");
            $(".Region").hide("fast");
            $(".Freshman").hide("fast");
            $(".YearLevel").hide("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").show("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'active_student_list' ||
            $('#reportType').val() == 'notRegisteredList' ||
            $('#reportType').val() == 'registeredList' ||
            $('#reportType').val() == 'listFx' ||
            $('#reportType').val() == 'listF' ||
            $('#reportType').val() == 'listNG' ||
            $('#reportType').val() == 'dismissed_student_list' ||
            $('#reportType').val() == 'studentListForOffice' ||
            $('#reportType').val() == 'studentListForElearning'
        ) {
            $(".SexAndRegion").show("fast");
            $(".academicStatus").hide("fast");
            $(".ExcludeGraduated").show("fast");
            $(".Region").hide("fast");
            $(".Sex").show("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".extendedReportForExitExam").hide("fast");
        } else if ($('#reportType').val() == 'delayedCountGradeSubmissionList' ||
            $('#reportType').val() == 'gradeSubmittedInstructorList' ||
            $('#reportType').val() == 'lateGradeSubmission' ||
            $('#reportType').val() == 'getGradeChangeList' ||
            $('#reportType').val() == 'notAssignedCourseeList' ||
            $('#reportType').val() == 'admittedMoreThanOneProgram'
        ) {
            $(".SexAndRegion").hide("fast");
            $(".academicStatus").hide("fast");
            $('.visibleOnDistribution').hide("fast");
            $(".ExcludeGraduated").hide("fast");
            $(".Region").hide("fast");
            $(".Sex").hide("fast");
            $(".Freshman").show("fast");
            $(".YearLevel").show("fast");
            $(".TopLimit").hide("fast");
            $(".OnlyWithCompleteData").hide("fast");
            $(".extendedReportForExitExam").hide("fast");
        }
        
        if ($("#reportType").val() != '') {
            selected_report_type = $("#reportType option:selected").text()
            $("#getReportExcel1").val('Export ' + selected_report_type + ' Report to Excel');
            $("#getReportExcel2").val('Export ' + selected_report_type + ' Report to Excel');
        }
    });

    if ($("#extendedReportForExitExam").is(":checked") || extendedReportExitExam) {
        $("#YearLevel").val('0');
        $(".YearLevel").hide("fast");
        $(".ExcludeGraduated").css("display", "block");
        $("#excludeGraduated").prop('checked', true);
        $('#freshman').prop('checked', false);
        $(".Freshman").hide("fast");
        $(".extendedReportForExitExam").css("display", "block");
        $("#Program").val(1);
        $(".Sex").show("fast");
        $(".Region").show("fast");
        $(".gpaOnly").hide("fast");
    } else if ($("#excludeGraduated").is(":checked") || excludeGraduated) {
        $(".ExcludeGraduated").css("display", "block");
        $("#excludeGraduated").val(1);
    } else if ($("#freshman").is(":checked") || freshman) {
        $(".ExcludeGraduated").hide("fast");
        $(".ExcludeGraduated").css("display", "none");
        $(".Freshman").css("display", "block");
        $("#YearLevel").val('');
        $("#freshman").val(1);
    }
    
	var get_report_button_clicked = false;

	$('#getReport').click(function(event) {
		
		var isValid = true;

		$('#show_report_details').hide();
        $('#getReportExcel1').attr('disabled', true);
        $('#getReportExcel2').attr('disabled', true);

		if ($("#reportType").val() == '') {
			$("#reportType").focus();
            isValid = false;
			return false;
		} else {
            selected_report_type = $("#reportType option:selected").text()
        }

		if (get_report_button_clicked) {
			alert('Generating ' + selected_report_type + ', please wait a moment...');
			$('#getReport').attr('disabled', true);
            $("#getReportExcel1").attr('disabled', true);
            $("#getReportExcel2").attr('disabled', true);
			isValid = false;
			return false;
		}

		if (!get_report_button_clicked && isValid) {
			$('#getReport').val('Generating ' + selected_report_type + '...');
			get_report_button_clicked = true;
			isValid = true
			return true;
		} else {
            get_report_button_clicked = false;
			return false;
		}
	});


    var export_report_button_clicked = false;
    var isValid2 = true;

    // get Report button at the begining of the page

    $("#getReportExcel1").click(function(event) {

        // Submit the form, After submitting, disable form elements & Set timeout to 0 to ensure it runs immediately after form submission
        $('#getReport').attr('disabled', false);
        $("#getReportExcel1").attr('disabled', false);
        $("#getReportExcel2").attr('disabled', false);

        if (export_report_button_clicked) {

            $('#getReport').attr('disabled', true);
            $("#getReportExcel1").attr('disabled', true);
            $("#getReportExcel2").attr('disabled', true);

            if (!isValid2) {
                alert('Still Processing ' + selected_report_type + ' for Excel Export...');
                $('#getReportExcel1').val('Still Processing ' + selected_report_type + ' for Excel Export...');
                $('#getReportExcel2').val('Still Processing ' + selected_report_type + ' for Excel Export...');
            }

            setTimeout(function() {
                $("input, select, textarea, button").prop("disabled", false);

                if (isValid2) {
                    $("#getReportExcel1").val('Export ' + selected_report_type + ' Report to Excel');
                    $("#getReportExcel2").val('Export ' + selected_report_type + ' Report to Excel');
                } else {
                    $("#getReportExcel1").val('Refresh Report to Export ' + selected_report_type + ' to Excel again');
                    $("#getReportExcel2").val('Refresh Report to Export ' + selected_report_type + ' to Excel again');
                }
            }, 5000);

			isValid2 = false;
			return false;
		}

        if (!export_report_button_clicked && isValid2) {

            $('#getReport').attr('disabled', true);

            $("form").submit();

            setTimeout(function() {
                $("input, select, textarea, button").prop("disabled", true);
                $('#getReportExcel1').val('Processing ' + selected_report_type + ' for Excel Export...');
                $('#getReportExcel2').val('Processing ' + selected_report_type + ' for Excel Export...');
            }, 0); 
            
            // Assuming the file save dialog appears within a few seconds after form submission, Re-enable form elements after a delay
            setTimeout(function() {
                $("input, select, textarea, button").prop("disabled", false);
                $("#getReportExcel1").val('Export ' + selected_report_type + ' Report to Excel');
                $("#getReportExcel2").val('Export ' + selected_report_type + ' Report to Excel');
            }, 5000); // Adjust the delay as needed (in milliseconds, 5 seconds);

			export_report_button_clicked = true;
			isValid2 = true
			return true;
		} else {
            $('#getReport').attr('disabled', false);
            isValid2 = false;
            $("#getReportExcel1").attr('disabled', true);
            $("#getReportExcel2").attr('disabled', true);
			//return false;
		}
    });

    // get Report button at the bottom of the page

    $("#getReportExcel2").click(function(event) {

        // Submit the form, After submitting, disable form elements & Set timeout to 0 to ensure it runs immediately after form submission
        $('#getReport').attr('disabled', false);
        $("#getReportExcel1").attr('disabled', false);
        $("#getReportExcel2").attr('disabled', false);

        if (export_report_button_clicked) {

            $('#getReport').attr('disabled', true);
            $("#getReportExcel1").attr('disabled', true);
            $("#getReportExcel2").attr('disabled', true);

            if (!isValid2) {
                alert('Still Processing ' + selected_report_type + ' for Excel Export...');
                $('#getReportExcel1').val('Still Processing ' + selected_report_type + ' for Excel Export...');
                $('#getReportExcel2').val('Still Processing ' + selected_report_type + ' for Excel Export...');
            }

            setTimeout(function() {
                $("input, select, textarea, button").prop("disabled", false);
                if (isValid2) {
                    $("#getReportExcel1").val('Export ' + selected_report_type + ' Report to Excel');
                    $("#getReportExcel2").val('Export ' + selected_report_type + ' Report to Excel');
                } else {
                    $("#getReportExcel1").val('Refresh Report to Export ' + selected_report_type + ' to Excel again');
                    $("#getReportExcel2").val('Refresh Report to Export ' + selected_report_type + ' to Excel again');
                }
            }, 5000);

            isValid2 = false;
            return false;
        }

        if (!export_report_button_clicked && isValid2) {

            $('#getReport').attr('disabled', true);

            $("form").submit();

            setTimeout(function() {
                $("input, select, textarea, button").prop("disabled", true);
                $('#getReportExcel1').val('Processing ' + selected_report_type + ' for Excel Export...');
                $('#getReportExcel2').val('Processing ' + selected_report_type + ' for Excel Export...');
            }, 0); 
            
            // Assuming the file save dialog appears within a few seconds after form submission, Re-enable form elements after a delay
            setTimeout(function() {
                $("input, select, textarea, button").prop("disabled", false);
                $("#getReportExcel1").val('Export ' + selected_report_type + ' Report to Excel');
                $("#getReportExcel2").val('Export ' + selected_report_type + ' Report to Excel');
            }, 5000); // Adjust the delay as needed (in milliseconds, 5 seconds);

            export_report_button_clicked = true;
            isValid2 = true
            return true;
        } else {
            $('#getReport').attr('disabled', false);
            isValid2 = false;
            $("#getReportExcel1").attr('disabled', true);
            $("#getReportExcel2").attr('disabled', true);
            //return false;
        }
    });

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>

