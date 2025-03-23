<?php
$enableDisplayExport = 0;

if (isset($this->data['Report']['report_type'])) {
    if (isset($currentlyActiveStudentStatistics) && !empty($currentlyActiveStudentStatistics)) {
        $enableDisplayExport = 1;
	} else if (isset($studentConstituencyByAgeGroup) && !empty($studentConstituencyByAgeGroup)) {
        $enableDisplayExport = 1;
    } else if (isset($getStaffCompletedHDPStatistics) && !empty($getStaffCompletedHDPStatistics)) {
        $enableDisplayExport = 1;
    } else if (isset($getActiveTeacherByDegree['teachersStatisticsByDegree']) && !empty($getActiveTeacherByDegree['teachersStatisticsByDegree'])) {
        $enableDisplayExport = 1;
    } else if (isset($getActiveTeacherByAcademicRank['teachersStatisticsByAcademicRank']) && !empty($getActiveTeacherByAcademicRank['teachersStatisticsByAcademicRank'])) {
        $enableDisplayExport = 1;
    } else if (isset($getTeachersOnStudyLeave['teachersOnStudyLeave']) && !empty($getTeachersOnStudyLeave['teachersOnStudyLeave'])) {
        $enableDisplayExport = 1;
    } 
}
?>

<div class="box" ng-app="generalReport">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-chart-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= 'Stakeholder Reports'; ?></span>
		</div>
	</div>
	<div class="box" ng-app="generalReport">
		<div class="box-body" ng-controller="reportCntrl">
			<div class="row">
				<div class="large-12 columns">

					<div class="form">

						<?php
						if (isset($headerLabel) && !empty($headerLabel)) {
							$this->assign('title_details', (!empty($this->request->params['controller']) ? ' ' . Inflector::humanize(Inflector::underscore($this->request->params['controller'])) . (!empty($this->request->params['action']) && $this->request->params['action'] != 'index' ? ' | ' . ucwords(str_replace('_', ' ', $this->request->params['action'])) : '') : '') . ' - ' . $headerLabel);
						} ?>
						
						<?= $this->Form->create('Report'); ?>
						
						<div style="margin-top: -30px;"><hr></div>

						<blockquote>
							<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
							<span style="text-align:justify;" class="fs16 text-black">This tool will help you to get some predefined reports by providing some search criteria.</span> 
						</blockquote>
						<hr>

						<div onclick="toggleViewFullId('ListPublishedCourse')">
							<?php
							if (!empty($currentlyActiveStudentStatistics) || !empty($studentConstituencyByAgeGroup)) {
								echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Display Filter</span>
								<?php
							} else {
								echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Hide Filter</span>
								<?php
							} ?>
						</div>

						<div id="ListPublishedCourse" style="display:<?= (!empty($currentlyActiveStudentStatistics) || !empty($studentConstituencyByAgeGroup) || !empty($getStaffCompletedHDPStatistics) || !empty($getActiveTeacherByDegree['teachersStatisticsByDegree']) ? 'none' : 'display'); ?>">
							<fieldset style="margin-bottom: 0px;">
								<legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>

								<div class="row align-items-center">
									<div class="large-6 columns">
										<?= $this->Form->input('report_type', array('label' => 'Report Type: ', 'type' => 'select', 'style' => 'width:90%;', 'div' => false, 'empty' => 'Select Report Type', 'onchange' => 'toggleFields("reportType")', 'options' => $report_type_options, 'id' => 'reportType', 'required' => 'required')); ?>
									</div>
									<div class="large-6 columns">
										<?= $this->Form->input('department_id', array('class' => 'fs14', 'style' => 'width:95%;', 'label' => 'College / Department: ', 'type' => 'select', 'options' => $departments/* , 'default' => $default_department_id */)); ?>
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
								<div class="row align-items-center">
									<div class="large-3 columns">
										<?= $this->Form->input('region_id', array('label' => 'Region: ', 'type' => 'select', 'class' => 'fs14', 'style' => 'width:90%;', 'default' => $default_region_id, 'options' => $regions)); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('gender', array('id' => 'Gender', 'label' => 'Sex: ', 'type' => 'select', 'class' => 'fs14', 'style' => 'width:50%;', 'options' => array('all' => 'All', 'male' => 'Male', 'female' => 'Female'))); ?>
									</div>
									<div class="large-3 columns">
										&nbsp;
									</div>
									<div class="large-3 columns">
										&nbsp;
									</div>
								</div>
								<hr>
								<?= $this->Form->submit(__('Get Report', true), array('name' => 'getReport', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
							</fieldset>
						</div>
					</div>
					<hr>


					<?php
					if ($enableDisplayExport == 1 ) {
						echo $this->Form->submit(__('Export Report to Excel', true), array('name' => 'getReportExcel', 'div' => false, 'class' => 'tiny radius button bg-blue', 'onclick' => '')). '<hr>';
					} 

					if (!empty($headerLabel)) {
						echo '<h6 class"fs10 text-gray">'. $headerLabel . '</h6><hr>';
					}

					if (isset($this->data['Report']['report_type'])) {
						if (isset($currentlyActiveStudentStatistics) && !empty($currentlyActiveStudentStatistics)) {
							echo $this->element('reports/stakeholders/active_student_stat');
						} else if (isset($studentConstituencyByAgeGroup) && !empty($studentConstituencyByAgeGroup)) {
							echo $this->element('reports/stakeholders/agegroup_student_stat');
						} else if (isset($getStaffCompletedHDPStatistics) && !empty($getStaffCompletedHDPStatistics)) {
							echo $this->element('reports/stakeholders/hdp_training_stat');
						} else if (isset($getActiveTeacherByDegree['teachersStatisticsByDegree']) && !empty($getActiveTeacherByDegree['teachersStatisticsByDegree'])) {
							echo $this->element('reports/stakeholders/active_teacher_degree_stat');
						} else if (isset($getActiveTeacherByAcademicRank['teachersStatisticsByAcademicRank']) && !empty($getActiveTeacherByAcademicRank['teachersStatisticsByAcademicRank'])) {
							echo $this->element('reports/stakeholders/active_teacher_academicrank_stat');
						} else if (isset($getTeachersOnStudyLeave['teachersOnStudyLeave']) && !empty($getTeachersOnStudyLeave['teachersOnStudyLeave'])) {
							echo $this->element('reports/stakeholders/teacher_on_study_leave_stat');
						} else { ?>
							<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no report with the selected search criteria.</div>
							<?php
						} 
					}
					
					if ($enableDisplayExport == 1 ) {
						echo '<hr>'. $this->Form->submit(__('Export Report to Excel', true), array('name' => 'getReportExcel', 'div' => false, 'class' => 'tiny radius button bg-blue', 'onclick' => '')). '<hr>';
					} ?>

					<?= $this->Form->end(); ?>
				</div>
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

	//this toggles the visibility of our parent permission fields depending on the current selected value of the underAge field
	function toggleFields(id) {
		if ($("#" + id).val() == 'attrition_rate') {
			$(".notVisibleOnAttritionRate ").hide();
		} else if (($('#' + id).val() == 'distributionStatsGender' || $('#' + id).val() == 'distributionStatsGenderAndRegion' || $('#' + id).val() == 'distributionStatsStatus' || $('#' + id).val() == 'distributionStatsGraduate')) {
			$('.visibleOnDistribution').show();
			$('.academicStatus').hide();
			$(".academicStatus").hide();
		} else if ($("#" + id).val() == 'top_students') {
			$(".notVisibleOnAttritionRate ").show();
			$(".academicStatus").hide();
		} else if ($("#" + id).val() == 'grade_change_statistics') {
			$(".gradeChangeState").hide();
			$(".academicStatus").hide();
		} else if ($("#" + id).val() == 'academic_status_range') {
			$(".academicStatus").show();
		} else {
			$(".academicStatus").hide();
			$(".notVisibleOnAttritionRate ").hide();
			$('.visibleOnDistribution').hide();
		}
	}

</script>