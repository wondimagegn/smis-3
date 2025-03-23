<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-vcard" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Add Student to Section: ' . $student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')'; ?></span>
		</div>

		<a class="close-reveal-modal">&#215;</a>
	</div>
	<div class="row">
		<div class="large-12 columns">
			<div style="margin-top: -10px;"><hr></div>
			<?php
			if ($studentMustHaveCurriculum) { 
				if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) { ?>
					<div class='error-box error-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span><?= $student_detail['Student']['full_name']. ' (' . $student_detail['Student']['studentnumber'] . ')'; ?>  is is not attached to a curricullum. Please attach the student to a curriculum in Placement > Accepted Students Attach Curriculum using <?= $student_detail['Student']['academicyear']; ?> Admission year and <?= $student_detail['Program']['name']; ?> as Program and <?= $student_detail['ProgramType']['name']; ?> as Program Type filter.</div>
					<?php
				} else {?>
					<div class='error-box error-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span><?= $student_detail['Student']['full_name']. ' (' . $student_detail['Student']['studentnumber'] . ')'; ?>  is is not attached to a curricullum.  Communicate his/her department to attach a curriculum to the student before trying to add him/her to a section.</div>
					<?php
				}
			} else if ($is_student_dismissed && !$is_student_readmitted) { ?>
				<div class='error-box error-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span><?= $student_detail['Student']['full_name']. ' (' . $student_detail['Student']['studentnumber'] . ')'; ?>  is dismissed in the <?= ($last_student_status['StudentExamStatus']['semester'] == 'I' ? '1st' : ($last_student_status['StudentExamStatus']['semester'] == 'II' ? '2nd' : ($last_student_status['StudentExamStatus']['semester'] == 'III' ? '3rd' : $last_student_status['StudentExamStatus']['semester']))) . ' semester of ' . $last_student_status['StudentExamStatus']['academic_year']; ?> and no readmission data is recorded after his/her dismissal.</div>
				<?php
			} else if (!empty($msg)) { ?>
				<div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Section add aborted. Fix the following errors before attempting to add the student to any section. <br><hr> <?= $msg; ?></div>
				<?php
			} else if (!$statusGeneratedForLastRegistration && ($student_detail['Student']['program_type_id'] == PROGRAM_TYPE_REGULAR)) { ?>
				<div class='error-box error-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span><?= $student_detail['Student']['full_name']. ' (' . $student_detail['Student']['studentnumber'] . ')'; ?>  have registration in the <?= ($student_detail['CourseRegistration'][0]['semester'] == 'I' ? '1st' : ($student_detail['CourseRegistration'][0]['semester'] == 'II' ? '2nd' : ($student_detail['CourseRegistration'][0]['semester'] == 'III' ? '3rd' : $student_detail['CourseRegistration'][0]['semester']))) . ' semester of ' . $student_detail['CourseRegistration'][0]['academic_year']; ?>, but student academic status is not generated.</div>
				<?php
			} else if ($student_have_invalid_grade) { ?>
				<div class='error-box error-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span><?= $student_detail['Student']['full_name']. ' (' . $student_detail['Student']['studentnumber'] . ')'; ?>  have invalid grade in one of  <?= $student_detail['CourseRegistration'][0]['academic_year'] . ' semesters.'?>.</div>
				<?php
			} else if (!empty($possibleAcademicYears) && !empty($sectionOrganized) && $studentNeedsSectionAssignment) { ?>
				<?= $this->Form->create('Section', array('action' => 'add_student_prev_section', "method" => "POST")); ?>
				<?= $this->Form->hidden('Selected_student_id', array('value' => $student_detail['Student']['id'])); ?>
				<?= $this->Form->hidden('acYrStart', array('id' => 'acYrStart', 'value' => !empty($lastReadmittedAcademicYear) ? str_replace('/', '-', $lastReadmittedAcademicYear) : (!empty($lastRegisteredAcademicYear) ? str_replace('/', '-', $lastRegisteredAcademicYear) : str_replace('/', '-', $student_detail['Student']['academicyear'])))); ?>
				<table cellpadding="0" cellspacing="0" class="table">
					<tr>
						<td style="width: 2%;">&nbsp;</td>
						<td>
							<div class="row">
								<div class="large-6 columns" style="margin-top: 10px;">
									<?= $this->Form->input('Section.year_level_id', array('label' => 'Select Year Level: ', 'style' => 'width:90%',  'empty' => '[ Select Year Level of Section ]', 'id' => 'year_level_id',  'options' => $yearLevelsProfile, /*  (empty($student_detail['Student']['department_id']) ? $yearLevelsProfile : $yearLevels), */  'onchange' => 'updateSection("' . $student_detail['Student']['id'] . '")')); ?>
								</div>
							</div>
						</td>
					</tr>
				</table>
				<br>

				<!-- Section List DIV AJAX -->
				<div id="SectionList"> 

				</div>
				<!-- END Section List DIV AJAX -->

				<?php
			} else if ($isLastSemesterInCurriculum) { ?>
				<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Student's attached curricullum, <?=  (isset($student_attached_curriculum_name) && !empty($student_attached_curriculum_name) ?  $student_attached_curriculum_name : ' attached curriculum says'); ?> states that the student (<?= $student_detail['Student']['full_name']. ' (' . $student_detail['Student']['studentnumber'] . ')'; ?>) is in the last year, last semester of the curricullum. You can move the student to different academic year, same level section instead.</div>
				<?php
			} else if (!empty($possibleAcademicYears) && empty($sectionOrganized) && $studentNeedsSectionAssignment) { 
				if (!empty($lastRegisteredAcademicYear) && !empty($lastRegisteredYearLevelName)) { ?>
					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No active <?= $lastRegisteredYearLevelName . ' year'; ?> section is found from <?= $lastRegisteredAcademicYear . ' to ' . $current_academicyear . ' '. (isset($student_attached_curriculum_name) && !empty($student_attached_curriculum_name) ? ' which uses ' . $student_attached_curriculum_name . ' curriculum' : ''); ?> to add <?= $student_detail['Student']['full_name']. ' (' . $student_detail['Student']['studentnumber'] . ')'; ?>. <?= ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT ? 'Please check <a href="/sections/display_sections" target="_blank">Display Sections</a> if there is at least one active section' .  (isset($student_attached_curriculum_name) && !empty($student_attached_curriculum_name) ? ' which uses ' . $student_detail['Student']['full_name'] . '\'s attached curriculum.' : '') : ''); ?></div>
					<?php
				} else {
					$acy_ranges_by_coma_quoted_for_display =  implode ( ", ", $possibleAcademicYears ); ?>
					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No active <?= isset($currentYearLevelIDName) && !empty($currentYearLevelIDName) ? $currentYearLevelIDName : ''; ?> section is found in <?= $acy_ranges_by_coma_quoted_for_display . (count($possibleAcademicYears) == 1 ? ' acadamic year' : ' academic years') . (isset($student_attached_curriculum_name) && !empty($student_attached_curriculum_name) ? ' which uses ' . $student_attached_curriculum_name . ' curriculum' : ''); ?> to add <?= $student_detail['Student']['full_name']. ' (' . $student_detail['Student']['studentnumber'] . ')'; ?>. <?= ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT ? 'Please check <a href="/sections/display_sections" target="_blank">Display Sections</a> if there is at least one active section' .  (isset($student_attached_curriculum_name) && !empty($student_attached_curriculum_name) ? ' which uses ' . $student_detail['Student']['full_name'] . '\'s attached curriculum.' : '') : ''); ?></div>
					<?php
				}
			} else if (!$studentNeedsSectionAssignment) { ?>
				<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span><?= $student_detail['Student']['full_name']. ' (' . $student_detail['Student']['studentnumber'] . ')'; ?>  doesn't need new section assignment. Check for incomplete grade submissions.</div>
				<?php
			} else { ?>
				<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>You can't add <?= $student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')'; ?>  to section since s/he has already in the section. </div>
				<?php
			} ?>
		</div>
	</div>
</div>


<script>
	function updateSection(studentId) {
		//serialize form data
		$("#SectionList").empty();

		var formData = $("#year_level_id").val();

		var acYrStart = $("#acYrStart").val();

		// $("#year_level_id").attr('disabled', true);
		// $("#Add_To_Section_Button").attr('disabled', true);

		//get form action

		if (formData != '') {

			$("#SectionList").empty();
			$("#year_level_id").attr('disabled', true);
			$("#SectionAssignedSection").attr('disabled', true);
			$("#Add_To_Section_Button").attr('disabled', true);

			var formUrl = '/sections/get_sections_by_year_level/' + formData + '/' + studentId + '/' + acYrStart;

			$.ajax({
				type: 'get',
				url: formUrl,
				data: formData,
				success: function(data, textStatus, xhr) {
					$("#year_level_id").attr('disabled', false);
					$("#SectionAssignedSection").attr('disabled', false);
					$("#Add_To_Section_Button").attr('disabled', false);
					$("#SectionList").empty();
					$("#SectionList").append(data);
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;
			
		} else {
			if ($('#Add_To_Section_Button').length) {
				$("#year_level_id").attr('disabled', true);
				$("#SectionAssignedSection").attr('disabled', true);
				$("#Add_To_Section_Button").attr('disabled', true);
			}
		}
	}
</script>