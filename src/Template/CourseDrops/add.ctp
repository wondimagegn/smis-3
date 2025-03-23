<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= (isset($is_forced_drop) &&  $is_forced_drop ? 'Forced Course Drop from Registerd Student on Hold Biases' : 'Course Drop on behalf of Student') ; ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div class="courseDrops form">
					
					<?= $this->Form->create('CourseDrop'); ?>

					<div style="margin-top: -30px;"><hr></div>
					<blockquote>
						<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<span style="text-align:justify;" class="fs15 text-gray">
							This tool will help you to perform course drop on behalf of students for selected academic year and semester. <br>
							<b class="rejected">You are advised to check Student Academic Profile by by clicking "Open Profile" link below before dropping the courses.</b>
						</span>
					</blockquote>
					<hr>

					<div onclick="toggleViewFullId('ListPublishedCourse')">
						<?php
						if (!empty($turn_off_search) || !empty($no_display)) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Display Filter</span><?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Hide Filter</span>
							<?php
						} ?>
					</div>
					<hr>

					<div id="ListPublishedCourse" style="display:<?= (isset($hide_search) || isset($no_display) ? 'none' : 'display'); ?>">
						<fieldset style="padding-bottom: 0px;padding-top: 15px;">
							<legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('Student.academicyear', array('label' => 'Academic Year: ', 'style' => 'width:90%;', 'empty' => '[ All Applicable ACY ]', 'required',  'default' =>  (isset($this->request->data['Student']['academicyear']) ? $this->request->data['Student']['academicyear'] : (isset($defaultacademicyear) ? $defaultacademicyear : '')) , 'options' => $acyear_array_data)); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Student.semester', array('label' => 'Semester: ', 'style' => 'width:90%;', 'empty' => '[ All Semesters ]', 'options' => Configure::read('semesters'))); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Student.program_id', array('label' => 'Program: ', 'style' => 'width:90%;',  'id' => 'program_id_1', 'empty' => '[ All Programs ]', 'required', 'options' => $programs)); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Student.program_type_id', array('label' => 'Program Type: ', 'style' => 'width:90%;', 'empty' => '[ All Program Types ]', 'required',  'options' => $programTypes)); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-6 columns">
									<?php
									if (isset($departments) && !empty($departments)) {
										echo $this->Form->input('Student.department_id', array('label' => 'Department: ', 'style' => 'width:90%;', 'empty' => '[ All Applicable Departments ]', 'required', 'id' => 'department_id_1', 'default' => (isset($default_department_id) && !empty($default_department_id) ? $default_department_id : '')));
									} else if (isset($colleges) && !empty($colleges)) {
										echo $this->Form->input('Student.college_id', array('label' => 'College: ', 'style' => 'width:90%;', 'empty' => '[ All Applicable Colleges ]', 'required',  /* 'onchange' => 'getDepartment(1)', */ 'id' => 'college_id', 'default' => (isset($default_college_id) && !empty($default_college_id) ? $default_college_id : '')));
									} ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Student.studentnumber', array('label' => 'Student ID:', 'id' => 'studentNumber', 'placeholder' => 'Student ID to filter ..', 'required' => false, 'default' => $studentnumber, 'style' => 'width:90%;')); ?>
								</div>
								<div class="large-3 columns">
									&nbsp;
								</div>
							</div>
							<hr>
							<?= $this->Form->submit(__('Search'), array('name' => 'continue', 'id' => 'continue',  'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
						</fieldset>
					</div>
					<hr>

					<?php
					if (isset($student_section_exam_status)) {
						echo $this->element('student_basic');
					}

					if (isset($coursesDrop)) {
						echo $this->element('course_drop_template');
					}

					if (isset($student_lists) && !isset($no_display) && !empty($student_lists)) {
						if ($is_forced_drop) {  ?>
							<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>List of students who have registered on hold bases and system found out the student(s) is/are not qualified to proceed with his/thier course. You need to drop the courses the student(s) registered.</div>
							<br>
							<?php
						} ?>
						
						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<td class="center">#</th>
										<td class="vcenter">Full Name</th>
										<td class="center">Sex</th>
										<td class="center">Student ID</th>
										<td class="center">Program</th>
										<td class="center">Program Type</th>
										<td class="vcenter">Department</th>
										<td class="center">&nbsp;</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 1;
									foreach ($student_lists as $student) { ?>
										<tr>
											<td class="vcenter"><?= $count++; ?></td>
											<td class="vcenter"><?= $student['Student']['full_name']; ?></td>
											<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : $student['Student']['gender'])); ?></td>
											<td class="center"><?= $student['Student']['studentnumber']; ?></td>
											<td class="center"><?= $student['Student']['Program']['name']; ?></td>
											<td class="center"><?= $student['Student']['ProgramType']['name']; ?></td>
											<td class="vcenter"><?= (!empty($student['Student']['Department']['name']) ? $student['Student']['Department']['name'] : ($student['Student']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Pre/Freshman')); ?></td>
											<td class="actions">
												<?php //eco $this->Html->link(__('Drop Course'), array('action' => 'add', $student['Student']['id']));  */ ?>
												<?= $this->Html->link(__('Drop Course'), array('action' => 'add', 0, $student['CourseRegistration']['id'])); ?>
											</td>
										</tr>
										<?php 
									} ?>
								</tbody>
							</table>
						</div>
						<br>
						<?php
					} ?>

					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type='text/javascript'>
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

	
	$('#continue').click(function() {

		var student_number = $('#studentNumber').val();
		var course_drop_set = <?= (isset($coursesDrop) && !empty($coursesDrop) ? 1 : 0); ?>;
		//alert (course_drop_set);

		if (course_drop_set) {
			//var currentUrl = window.location.href;
			//var modifiedUrl = modifyUrl(currentUrl);
			//alert('Original URL: ' + currentUrl + '\nModified URL: ' + modifiedUrl);
			//window.location.replace(modifiedUrl); // Navigate to the new URL and clear history
			//window.location.href = modifiedUrl;
		}

		if (student_number != '') {
			
			$('#CourseDropAddForm [required]').each(function() {
				$(this).removeAttr('required');
			});

			/* $('input').not('[name*="data[CourseDrop]"]').each(function() {
				$(this).val(''); // Set their values to empty
			}); */

			/* $('input').filter(function() {
				return this.name.includes('data[CourseDrop]');
			}).each(function() {
				$(this).val(''); // Set their values to empty
			}); */

			$('input[name*="data[CourseDrop]"], select[name*="data[CourseDrop]"]').each(function() {
				$(this).val(''); // Set their values to empty
			});

			$('#CourseDropAddForm').submit();
		}
	});

	function modifyUrl(url) {
		// Regular expression to match numbers after the last / 
		// var regex = /\/\d+($|\/)/g;
		// return url.replace(regex, '/');

		// Regular expression to match numbers after the last / 
		var regex = /\/\d+($|\/)/;

		while (regex.test(url)) {
			url = url.replace(regex, '/');
		}
		return url;
	}
</script>