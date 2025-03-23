<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Confirm Mass Drops'); ?></span>
		</div>
	</div>
    <div class="box-body">
       <div class="row">
	  		<div class="large-12 columns">
			  	<div style="margin-top: -30px;"><hr></div>

				<div class="courseDrops form">

					<?= $this->Html->script('jquery.observe'); ?>
					<?= $this->Form->create('CourseDrop'); ?> 

					<?php 
					if (!isset($hide_search)) { ?>
						<fieldset style="padding-bottom: 0px;padding-top: 15px;">
							<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('Student.academic_year', array('label' => 'Academic Year: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $acyear_array_data, 'default' => isset($this->request->data['Student']['academic_year']) ? $this->request->data['Student']['academic_year'] : (isset($defaultacademicyear) ? $defaultacademicyear : ''))) ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Student.semester', array('label' => 'Semester: ', 'style' => 'width:90%;', 'empty' => '[ Select Semesters ]', 'required', 'options' => Configure::read('semesters'))); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Student.program_id', array('label' => 'Program: ', 'style' => 'width:90%;', 'empty' => '[ Select Program ]', 'required', 'options' => $programs)); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Student.program_type_id', array('label' => 'Program Type: ', 'style' => 'width:90%;', 'empty' => '[ Select Program Type ]', 'required',  'options' => $programTypes)); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-6 columns">
									<?php
									if (isset($departments) && !empty($departments)) {
										echo $this->Form->input('Student.department_id', array('label' => 'Department: ', 'style' => 'width:95%;', 'empty' => '[ Select Department ]', 'required', 'default' => (isset($default_department_id) && !empty($default_department_id) ? $default_department_id : '')));
									}/*  else if (isset($colleges) && !empty($colleges)) {
										echo $this->Form->input('Student.college_id', array('label' => 'College: ', 'style' => 'width:95%;', 'empty' => '[ Select Colleges ]', 'required', 'default' => (isset($default_college_id) && !empty($default_college_id) ? $default_college_id : '')));
									}  */ ?>
								</div>		
								<div class="large-3 columns">
									<?= $this->Form->input('Student.year_level_id',array('label'=> 'Year Level: ', 'style' => 'width:90%;', 'empty' => '[ Select Year Level ]', 'required')); ?>
								</div>
								<div class="large-3 columns">
									
								</div>
							</div>
							<hr>
							<?= $this->Form->submit(__('Search'), array('name' => 'continue', 'id' => 'continue',  'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
						</fieldset>
						<hr>
						<?php 
					}
					

					if (isset($list_of_students_registered_organized_by_section) && !empty($list_of_students_registered_organized_by_section) && !isset($no_display)) { ?>
						<h6 class="fs16 text-gray">List of students who are registered for courses that have been published by the department as mass drop.</h6>
						<br>
						<?php
						$count = 0;
						$cc = 0;
						$last_course = '';
						foreach ($list_of_students_registered_organized_by_section as $section_id => $list_of_students_registered_for_courses) { ?>
							<!-- <div class="smallheading"><?php //echo "Section: " . $sections[$section_id]; ?></div> -->
							<?php 
							foreach ($list_of_students_registered_for_courses as $title => $list_of_students) { ?>
								<!-- <div class="smallheading"><?php //echo 'Course: '. $title; ?></div> -->
								<div style="overflow-x:auto;">
									<table cellpadding="0" cellspacing="0" class="table">
										<thead>
											<tr>
												<td colspan=7 style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
													<span style="font-size:16px;font-weight:bold; padding-top: 25px;"> 
														<?= $title; ?>
													</span>
													<br style="line-height: 0.35;">
													<span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
														<?= $sections[$section_id]['Section']['name'] . ' (' . (isset($sections[$section_id]['YearLevel']['name']) ? $sections[$section_id]['YearLevel']['name'] : ($sections[$section_id]['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $sections[$section_id]['Section']['academicyear'] . ')'; ?> &nbsp; | &nbsp; 
														<?= ($sections[$section_id]['Program']['name']); ?> &nbsp; | &nbsp; <?= ($sections[$section_id]['ProgramType']['name']); ?> &nbsp; | &nbsp; 
														<?= (isset($sections[$section_id]['Department']) && !empty($sections[$section_id]['Department']['name']) ? $sections[$section_id]['Department']['name'] : $sections[$section_id]['College']['name'] . ' - Pre/Freshman'); ?><br>
													</span>
													<span class="text-gray" style="padding-top: 14px; font-size: 13px; font-weight: bold">
														<i><?= (isset($sections[$section_id]['Curriculum']['name']) ? /* ucwords(strtolower($sections[$section_id]['Curriculum']['name'])) */ $sections[$section_id]['Curriculum']['name'] . ' - ' . $sections[$section_id]['Curriculum']['year_introduced'] . ' (' .  (count(explode('ECTS', $sections[$section_id]['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit') . ') <br style="line-height: 0.35;">' : ''); ?></i>
													</span>
												</td>
											</tr>
											<tr>
												<th class="center">#</th>
												<th class="vcenter">Full Name</th>
												<th class="center">Sex</th>
												<th class="center">Student ID</th>
												<th class="center">Department</th>
												<th class="center">Program</th>
												<th class="center">Program Type</th>
											</tr>
										</thead>
										<tbody>
											<?php
											//debug($list_of_students);
											$last_course = $title;
											foreach ($list_of_students as $student) { 
												if (isset($student['Student']['id']) && !empty($student['Student']['id']) && $student['Student']['graduated'] == 0 && empty($student['ExamGrade'])) { ?>
													<tr>
														<td class="center"><?= ++$count; ?></td>
														<?php 
														//debug($student['Student']['id']);
														echo $this->Form->hidden('CourseDrop.' . $cc . '.student_id', array('value' => $student['Student']['id']));
														echo $this->Form->hidden('CourseDrop.' . $cc . '.course_registration_id', array('value' => $student['CourseRegistration']['id']));
														echo $this->Form->hidden('CourseDrop.' . $cc . '.semester', array('value' => $student['CourseRegistration']['semester']));
														echo $this->Form->hidden('CourseDrop.' . $cc . '.academic_year', array('value' => $student['CourseRegistration']['academic_year']));
														echo $this->Form->hidden('CourseDrop.' . $cc . '.year_level_id', array('value' => $student['CourseRegistration']['year_level_id']));
														$cc++; ?>
														
														<td class="vcenter"><?= $student['Student']['full_name']; ?></td>
														<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : '')); ?></td>
														<td class="center"><?= $student['Student']['studentnumber']; ?></td>
														<td class="center"><?= $student['Student']['Department']['name']; ?></td> 
														<td class="center"><?= $student['Student']['Program']['name']; ?></td> 
														<td class="center"><?= $student['Student']['ProgramType']['name']; ?></td>
													</tr>
													<?php 
												}
											} ?>
										</tbody>
									</table>
								</div>
								<br>
								<?php
							}
						}  
						
						if ($cc != 0) { ?>
							<hr>
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('CourseDrop.minute_number', array('label' => 'Minute Number: ', 'style' => 'width:95%;', 'id' => 'minuteNumber', 'required')); ?>
									<?php //echo $this->Form->input('CourseDrop.0.forced'); ?>
								</div>
							</div>
							<hr>
							<?= $this->Form->Submit('Confirm Mass Drop', array('div' => false, 'class' => 'tiny radius button bg-blue', 'name' => 'massdrop', 'id' => 'massDrop')); ?>
							<?php 
						} else { ?>
							<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Grade is already submitted for all registered students for <?= (!empty($last_course) ? $last_course . ' course or no registerd, not graduated students found' : 'the course'); ?>.</div>
							<?php
						}
					} ?>
					<?= $this->Form->end(); ?>
				</div>
	  		</div>
		</div>
    </div>
</div>
<?= $this->Js->writeBuffer(); ?>

<script type="text/javascript">
	var form_being_submitted = false;
	$('#massDrop').click(function() {
		var isValid = true;
		var minuteNumber = $('#massDrop').val();

		if (minuteNumber == '') {
			$('#massDrop').focus();
			isValid = false;
			return false;
		}

		if (form_being_submitted) {
			alert("Dropping selected course registrations, please wait a moment...");
			$("#massDrop").attr('disabled', true);
			isValid = false;
			return false;
		}

		if (!form_being_submitted && isValid) {
			$('#massDrop').val('Confirming Mass Drop...');
			form_being_submitted = true;
			return true;
		} else {
			isValid = false;
			return false;
		}
	});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>
