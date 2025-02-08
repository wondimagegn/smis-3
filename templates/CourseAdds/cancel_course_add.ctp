<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= ('Course Add Cancellation Interface'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div style="margin-top: -30px;"><hr></div>

				<?= $this->Form->create('CourseAdd'/* , array('onSubmit' => 'return checkForm(this);') */); ?>

				<blockquote>
					<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
					<p style="text-align:justify;">
						<span class="fs14 text-gray" style="font-weight: bold;">
							This tool will help you to cancel student course add. If the selected students has course add for selected academic year and semester, it will display those course. <strong class="text-red"><br><br>WARNING!! Deleting course adds which got exam grade will also delete the associated grades and assesment data of the student!, Before proceeding, please check that the student doesn't got any grade for a given course via student academic profile!!</strong>
						</span>
					</p>
				</blockquote>
				<hr>

				<div onclick="toggleViewFullId('ListSection')">
					<?php
					if (!empty($publishedCourses)) {
						echo $this->Html->image('plus2.gif', array('id' => 'ListSectionImg')); ?>
						<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Display Filter</span>
						<?php
					} else {
						echo $this->Html->image('minus2.gif', array('id' => 'ListSectionImg')); ?>
						<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Hide Filter</span>
						<?php
					} ?>
				</div>

				<div id="ListSection" style="display:<?= (!empty($sections) ? 'none' : 'display'); ?>">
					<fieldset style="padding-bottom: 5px;padding-top: 15px;">
						<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
						<div class="row">
							<div class="large-3 columns">
								<?= $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => 'Academic Year: ', 'style' => 'width:90%', 'options' => $acyear_list, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('semester', array('id' => 'Semester', 'label' => 'Semester: ', 'style' => 'width:90%', 'options' => Configure::read('semesters'))); ?>
								<?= (isset($semester_selected) ? $this->Form->input('semester_selected', array('id' => 'SemesterSelected', 'type' => 'hidden', 'value' => $semester_selected)) : ''); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('studentnumber', array('id' => 'StudentNumber', 'class' => 'fs14', 'label' => 'Student ID: ', 'style' => 'width:90%', 'type' => 'text', 'placeholder' => 'Type Student ID...', 'required', 'maxlength' => 25)); ?>
							</div>
							<div class="large-3 columns">
								<?=  $this->Form->input('password', array('id' => 'Password', 'style' => 'width:90%', 'type' => 'password', 'label' => 'Password: ', 'placeholder' => 'Your Password here..', 'required')); ?>
							</div>
						</div>
						<hr>
						<?= $this->Form->submit(__('Get Courses', true), array('name' => 'listAddedCourses', 'id' => 'listAddedCourses', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
					</fieldset>
					<?= $this->Form->end(); ?>
				</div>
				<hr>

				<!-- AJAX STUDENT PROFILE LOADING -->
				<div id="dialog-modal" title="Academic Profile ">

				</div>
				<!-- END AJAX STUDENT PROFILE LOADING -->

				<div id="manage_main_data">
					<?= (!empty($student_academic_profile) ? '<h6 class="fs14 text-gray">' . $student_academic_profile['BasicInfo']['Student']['first_name'] . ' ' . $student_academic_profile['BasicInfo']['Student']['middle_name'] . ' ' . $student_academic_profile['BasicInfo']['Student']['last_name'] . ' (' . $student_academic_profile['BasicInfo']['Department']['name'] . ')' . '</h6>' : ''); ?> 
					<?= (!empty($student_academic_profile) ? $this->Html->link('[ Open Student Academic Profile ]', '#', array('class' => 'jsview', 'data-animation' => "fade", 'data-reveal-id' => 'myModal', 'data-reveal-ajax' => "/students/get_modal_box/" . $student_academic_profile['BasicInfo']['Student']['id'])) : ''); ?>
					<?php
					if (empty($publishedCourses) && isset($this->request->data['listAddedCourses'])) { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no course add found for <?= (!empty($student_academic_profile) ? $student_academic_profile['BasicInfo']['Student']['first_name'] . ' ' . $student_academic_profile['BasicInfo']['Student']['middle_name'] . ' ' . $student_academic_profile['BasicInfo']['Student']['last_name'] . ' ('. $student_academic_profile['BasicInfo']['Student']['studentnumber'] .') ' : $this->request->data['CourseAdd']['studentnumber']) . (isset($this->request->data['CourseAdd']['acadamic_year'])  ? ' in ' . $this->request->data['CourseAdd']['acadamic_year'] . ' academic year ' . (isset($this->request->data['CourseAdd']['semester']) ? ', semester: ' . $this->request->data['CourseAdd']['semester'] : '')  : ' in the given criteria.'); ?></div>
						<?php
					} else if (isset($publishedCourses) && !empty($publishedCourses)) { ?>
						<?= $this->Form->create('CourseAdd'); ?>
						<!-- <div onclick="toggleViewFullId('Profile')">
							<?php
							/* if (!empty($student_academic_profile)) {
								echo $this->Html->image('plus2.gif', array('id' => 'ProfileImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ProfileTxt">Display Student Academic Profile</span>
								<?php
							} else {
								echo $this->Html->image('minus2.gif', array('id' => 'ProfileImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ProfileTxt">Hide Student Academic Profile</span>
								<?php
							} */ ?>
						</div>
						<hr> -->

						<!-- <div id="Profile" style="display:<?php //echo (!empty($student_academic_profile) ? 'none' : 'display'); ?>">
							<?php //echo (isset($student_academic_profile) && !empty($student_academic_profile) ? $this->element('student_academic_profile') : ' '); ?>
						</div> -->

						<hr>

						<h6 class="fs13 text-gray">Please Select Course(s)</h6>

						<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
						<br>

						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<td class="center" style="width:5%;"><?= $this->Form->input('select_all', array('type' => 'checkbox', 'name' => 'select-all', 'id' => 'select-all', 'div' => false, 'label' => false)); ?></td>
										<td class="vcenter" style="width:40%;">Course Title</td>
										<td class="center" style="width:15%;">Course Code</td>
										<td class="center" style="width:10%;"><?= (count(explode('ECTS', $student_academic_profile['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit'); ?></td>
										<td class="center" style="width:15%;">ACY/SEM</td>
										<td class="center" style="width:15%;">Have Grade?</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$st_count = 0;
									$checkBoxCount = 0;
									foreach ($publishedCourses as $key => $course) { ?>
										<tr>
											<td class="center">
												<div style="margin-left: 10%;"><?= $this->Form->input('CourseAdd.' . $st_count . '.gp', array('type' => 'checkbox', 'class' => 'checkbox1', 'label' => false, 'id' => 'StudentSelection' . $st_count)); ?></div>
												<?= $this->Form->input('CourseAdd.' . $st_count . '.student_id', array('type' => 'hidden', 'value' => $student_academic_profile['BasicInfo']['Student']['id'])); ?>
												<?= $this->Form->hidden('CourseAdd.' . $st_count . '.id', array('label' => false, 'value' => $course['CourseAdd']['id'])); ?>
												<?= $this->Form->hidden('CourseAdd.' . $st_count . '.published_course_id', array('label' => false, 'value' => $course['CourseAdd']['published_course_id'])); ?>
											</td>
											<td class="vcenter"><?= $course['PublishedCourse']['Course']['course_title']; ?></td>
											<td class="center"><?= $course['PublishedCourse']['Course']['course_code']; ?></td>
											<td class="center"><?= $course['PublishedCourse']['Course']['credit']; ?></td>
											<td class="center"><?= $course['PublishedCourse']['academic_year'] . ' / ' . $course['PublishedCourse']['semester']; ?></td>
											<td class="center"><?= (isset($course['ExamGrade'][0]) && !empty($course['ExamGrade'][0]) ? 'Yes (' . $course['ExamGrade'][0]['grade'] . (isset($course['ExamGrade'][0]['ExamGradeChange'][0]) && !empty( $course['ExamGrade'][0]['ExamGradeChange'][0]['grade']) ? ' => ' . $course['ExamGrade'][0]['ExamGradeChange'][0]['grade'] : '') . ')' : 'No'); ?></td>
										</tr>
										<?php
										$st_count++; 
									} ?>
								</tbody>
							</table>
						</div>
						<hr>
						<?= $this->Form->submit(__('Cancel Add & Delete Grade', true), array('name' => 'deleteGrade', 'id' => 'cancelNGandDeleteGrade', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
						<?= $this->Form->end(); ?>
						<?php
					} ?>
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
			$('#' + id + 'Img').attr("src", '/img/plus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append('Hide Filter');
		} else {
			$('#' + id + 'Img').attr("src", '/img/minus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append('Display Filter');
		}
		$('#' + id).toggle("slow");
	}

	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	var form_being_submitted = false;

	$(document).ready(function() {

		$("#manage_main_data").show();

        $('#cancelNGandDeleteGrade').click(function() {

			var checkboxes = document.querySelectorAll('input[type="checkbox"]');
			var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

			var isValid = true;

			if (!checkedOne) {
				alert('At least one Course Add must be selected to cancel!');
				validationMessageNonSelected.innerHTML = 'At least one Course Add must be selected to cancel!';
				isValid = false;
				return false;
			}

			if (form_being_submitted) {
				alert('Canceling Add and Deleting Grade. please wait a moment...');
				$('#cancelNGandDeleteGrade').attr('disabled', true);
				isValid = false;
				return false;
			}

			var confirmm = confirm('Are you sure you want to cancel the selected course adds? Canceling the selected course adds will permanently delete exixting course grades and assesment if any, and it is not recoverable! Are you sure you want to proceed?');

			if (!form_being_submitted && isValid && confirmm) {
				$('#cancelNGandDeleteGrade').val('Canceling Add and Deleting Grade...');
				form_being_submitted = true;
				isValid = true
				return true;
			} else {
				return false;
			}
        });

		$('#listAddedCourses').click(function() {
			$("#manage_main_data").hide();
			$("#listAddedCourses").val('Fetching Courses...');
		});

    });

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>