<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-vcard" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= $studentDetail['Student']['full_name'] . ' (' . $studentDetail['Student']['studentnumber'] . ')'; ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div class="row">
					<div style="margin-top: -40px;"><hr></div>
					<?php
					if (isset($publishedCourses) && !empty($publishedCourses)) {
						echo $this->Form->create('CourseRegistration', array('action' => 'update_missing_registration', "method" => "POST", 'onSubmit' => 'return checkForm(this);')); ?>
						
						<?= $this->Form->input('Student.selected_student_id', array('type' => 'hidden', 'value' => $studentDetail['Student']['id'])); ?>

						
						<?php
						if ($last_semester_status_is_not_generated) { ?>
							<div id='flashMessage' class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>PROCEED WITH CAUTION: Last semester status is not generated for the selected student. Please regenarate student academic status and make sure the student is not dismissed or wait until student registered or added course exam grades are fully submitted.</div>
							<?php
						}  ?>

						<!-- <h6 class="fs14 text-gray">Please select course/s and enter corresponding grade.</h6> -->
						<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
						<br>

						<div style="overflow-x:auto;">
							<table cellspacing="0" cellpadding="0" class="table">
								<thead>
									<tr>
										<td style="width:5%" class="center">#</td>
										<td style="width:5%" class="center">&nbsp;</td>
										<td style="width:35%" class="vcenter">Course Title</td>
										<td style="width:15%" class="center">Course Code</td>
										<td style="width:10%" class="center"><?= (count(explode('ECTS', $studentDetail['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit'); ?></td>
										<td style="width:15%" class="center">ACY/SEM</td>
										<td style="width:10%" class="center">Grade</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$st_count = 0;
									$checkBoxCountNG = 0;
									$checkBoxCountMissing = 0;

									foreach ($publishedCourses as $key => $course) {
										$st_count++; ?>
										<tr>
											<td class="center"><?= $st_count; ?></td>
											<td class="center">
												<?php
												if (isset($course['PublishedCourse']['prerequisiteFailed']) && $course['PublishedCourse']['prerequisiteFailed']) {
													echo "**";
												} else if ($course['PublishedCourse']['mass_added'] || $course['PublishedCourse']['mass_dropped'] || (isset($course['PublishedCourse']['grade']) && $course['PublishedCourse']['grade'] == 'NG' && isset($course['PublishedCourse']['haveAssesmentData']) && $course['PublishedCourse']['haveAssesmentData'])) {
													echo "x";
												} else if ($course['PublishedCourse']['readOnly']) {
													echo "-";
												} else if (empty($course['PublishedCourse']['grade']) && $course['PublishedCourse']['readOnly'] == false && empty($course['PublishedCourse']['course_registration_id'])) {
													$checkBoxCountMissing++;
													echo '<div style="margin-left: 25%;">' . $this->Form->input('CourseRegistration.' . $st_count . '.gp', array('type' => 'checkbox', 'class' => 'checkbox1', 'label' => false, 'id' => 'StudentSelection' . $st_count))  . '</div>';
													echo $this->Form->input('CourseRegistration.' . $st_count . '.student_id', array('type' => 'hidden', 'value' => $studentDetail['Student']['id']));
													echo $this->Form->input('CourseRegistration.' . $st_count . '.published_course_id', array('type' => 'hidden', 'value' => $course['PublishedCourse']['id']));
												} else if (!empty($course['PublishedCourse']['grade']) && $course['PublishedCourse']['grade'] == "NG") {

													if (isset($course['PublishedCourse']['haveAssesmentData']) && $course['PublishedCourse']['haveAssesmentData']) {
														// have assesment data do not allow it;
														echo $this->Form->input('CourseRegistration.' . $st_count . '.ng_grade_with_assesment', array('type' => 'hidden', 'value' =>  1));
													} else {
														echo $this->Form->input('CourseRegistration.' . $st_count . '.ng_grade_with_assesment', array('type' => 'hidden', 'value' =>  0));
														$checkBoxCountNG++;
													}
													
													
													echo '<div style="margin-left: 25%;">' . $this->Form->input('CourseRegistration.' . $st_count . '.gp', array('type' => 'checkbox', 'class' => 'checkbox1', 'label' => false, 'id' => 'StudentSelection' . $st_count)) . '</div>';
													echo $this->Form->input('CourseRegistration.' . $st_count . '.id', array('type' => 'hidden', 'value' => $course['PublishedCourse']['course_registration_id']));
													echo $this->Form->input('CourseRegistration.' . $st_count . '.grade_id', array('type' => 'hidden', 'value' =>  $course['PublishedCourse']['grade_id']));
													echo $this->Form->input('CourseRegistration.' . $st_count . '.grade', array('type' => 'hidden', 'value' => $course['PublishedCourse']['grade']));
													echo $this->Form->input('CourseRegistration.' . $st_count . '.course_registration_id', array('type' => 'hidden', 'value' => $course['PublishedCourse']['course_registration_id']));
													
													if (isset($course['PublishedCourse']['grade_change_id'])) {
														echo $this->Form->input('CourseRegistration.' . $st_count . '.grade_change_id', array('type' => 'hidden', 'value' =>  $course['PublishedCourse']['grade_change_id']));
													}
												} ?>
											</td>
											<td class="vcenter">
												<?= $course['Course']['course_title']; ?>
												<?= ($course['PublishedCourse']['mass_added'] ? '<br><span class="on-process">Mass Added Course</span>' : ($course['PublishedCourse']['mass_dropped'] ? '<br><span class="on-process">Mass Dropped Course</span>' : '')); ?>
												<?= ($course['PublishedCourse']['elective'] ? '<br><span class="accepted">(Published as Elective)</span>' : ''); ?>
												<?= (isset($course['PublishedCourse']['haveAssesmentData']) && $course['PublishedCourse']['haveAssesmentData'] ? '<br><span class="on-process">(Have assesment data)</span>' . (isset($show_manage_ng_link) && $show_manage_ng_link ? ' &nbsp; <a href="/examGrades/manage_ng/' . (isset($course['PublishedCourse']['id']) && !empty($course['PublishedCourse']['id']) ? $course['PublishedCourse']['id'] : '') . '" target="_blank">Manage NG</a>' : '') : ''); ?>
											</td>
											<td class="center"><?= $course['Course']['course_code']; ?></td>
											<td class="center"><?= $course['Course']['credit']; ?></td>
											<td class="center"><?= (!empty($course['PublishedCourse']['academic_year']) ? $course['PublishedCourse']['academic_year'] . '/' . $course['PublishedCourse']['semester'] : $course['PublishedCourse']['academic_year'] . '/' . $course['PublishedCourse']['semester']); ?></td>
											<td class="center"><?= (isset($course['PublishedCourse']['grade']) && !empty($course['PublishedCourse']['grade']) ? '<b>' . $course['PublishedCourse']['grade'] . '</b>' : (isset($course['PublishedCourse']['prerequisiteFailed']) && $course['PublishedCourse']['prerequisiteFailed'] ? '**' : '---')); ?></td>
										</tr>
										<?php
									} ?>
								</tbody>
								<tfoot>
									<tr>
										<td>&nbsp;</td>
										<td class="center">**</td>
										<td colspan="5" class="vcenter">Failed to take Prerequisite Course(s)</td>
									</tr>
								</tfoot>
							</table>
						</div>
						<hr>

						<div class="row">
							<div class="large-3 columns">
								<?= ($checkBoxCountMissing > 0 ? $this->Form->submit(__('Register Missing', true), array('name' => 'registerMissingCourse', 'id' => 'registerMissingCourse', 'class' => 'tiny radius button bg-blue', 'div' => false)) : '<input class="tiny radius button bg-blue" type="submit" value="Register Missing" disabled>'); ?>
							</div>
							<div class="large-3 columns">
								<?= ($checkBoxCountNG > 0 ? $this->Form->submit(__('Cancel NG', true), array('name' => 'cancelNG', 'id' => 'cancelNG', 'class' => 'tiny radius button bg-red', 'div' => false)) : '<input class="tiny radius button bg-red" type="submit" value="Cancel NG" disabled>'); ?>
							</div>
							<div class="large-6 columns">
								&nbsp;
							</div>
						</div>

						<?= $this->Form->end(); ?>
						<?php
					} else {
						if (isset($status) && !empty($status)) { ?>
							<div id='flashMessage' class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span><?= $status; ?></div>
							<?php
						} else if ($last_semester_status_is_not_generated) { ?>
							<div id='flashMessage' class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Last semester status is not generated for the selected student. Please regenarate student academic status or wait until student registered or added course exam grades are fully submitted.</div>
							<?php
						}  else { ?>
							<div id='flashMessage' class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no course registration for the given academic year and semester of the selected student.</div>
							<?php
						}
					} ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	
	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	var delete_all_assment_data = <?= DELETE_ASSESMENT_AND_ASSOCIATED_RECORDS_ON_NG_CANCELATION; ?>;

	$(document).ready(function() {
        $('#cancelNG').click(function() {
			var checkboxes = document.querySelectorAll('input[type="checkbox"]');
			var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

			if (!checkedOne) {
				alert('At least one course must be selected to Cancel NG Grade.');
				validationMessageNonSelected.innerHTML = 'At least one course must be selected to Cancel NG Grade.';
				return false;
			}

			if (delete_all_assment_data == 1) {
            	return confirm('WARNING!! This Server is set to delete all data including registration and assesments while cancelling NG grades. Are you sure you want to cancel the selected student NG grades?, This action is NOT RECOVERABLE AND COMPLETELY DELETES ALL ASSOCIATED ASSESMENT DATA recorded for the selected student?');
			} else {
				return confirm('Are you sure you want to cancel NG Grades of the selected student NG grades? Canceling NG here will delete student regigtration and associated NG grade if assesment data is not available. Are you sure you want proceed?..');
			}
        });
    });

	var form_being_submitted = false;

	var checkForm = function(form) {
		var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

		//alert(checkedOne);
		if (!checkedOne) {
			<?php 
			if($checkBoxCountMissing > 0 && $checkBoxCountNG > 0) { ?>
				alert('At least one course must be selected to manage missing registion or cancel NG Grade.');
				validationMessageNonSelected.innerHTML = 'At least one course must be selected to manage missing registion or cancel NG Grade.';
				return false;
				<?php
			} else if($checkBoxCountMissing > 0) { ?>
				alert('At least one course must selected to manage missing registion.');
				validationMessageNonSelected.innerHTML = 'At least one course must be selected to manage missing registion.';
				return false;
				<?php
			} else if($checkBoxCountNG > 0) { ?>
				alert('At least one course must be selected to Cancel NG Grade.');
				validationMessageNonSelected.innerHTML = 'At least one course must be selected to Cancel NG Grade.';
				return false;
				<?php
			} ?>
		}

		if (form_being_submitted) {
			<?php 
			if ($checkBoxCountMissing > 0 && $checkBoxCountNG > 0) { ?>
				alert("Registering Missing Course or Cancelling NG Grade, please wait a moment...");
				form.registerMissingCourse.disabled = true;
				form.cancelNG.disabled = true;
				return false;
				<?php
			} else if ($checkBoxCountMissing > 0) { ?>
				alert("Registering Missing Course, please wait a moment...");
				form.registerMissingCourse.disabled = true;
				//form.cancelNG.disabled = true;
				return false;
				<?php
			} else if ($checkBoxCountNG > 0) { ?>
				alert("Cancelling NG Grade, please wait a moment...");
				//form.registerMissingCourse.disabled = true;
				form.cancelNG.disabled = true;
				return false;
				<?php
			} ?>
		}

		<?php 
		if ($checkBoxCountMissing > 0 && $checkBoxCountNG > 0) { ?>
			form.registerMissingCourse.value = 'Processing...';
			form.cancelNG.value = 'Processing...';
			<?php
		} else if ($checkBoxCountMissing > 0) { ?>
			form.registerMissingCourse.value = 'Registering Missing Course...';
			<?php
		} else if ($checkBoxCountNG > 0) { ?>
			form.cancelNG.value = 'Cancelling NG Grade...';
			<?php
		} ?>

		form_being_submitted = true;
		return true;
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>