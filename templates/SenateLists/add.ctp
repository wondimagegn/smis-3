<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Add Students to Senate List'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div style="margin-top: -30px;"><hr></div>

				<div class="senateLists form">

					<?= $this->Form->create('SenateList'); ?>

					<div onclick="toggleViewFullId('ListPublishedCourse')">
						<?php
						if (!empty($students_for_senate_list)) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> &nbsp; Display Filter</span>
							<?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> &nbsp; Hide Filter</span>
							<?php
						} ?>
					</div>

					<div id="ListPublishedCourse" style="display:<?= (!empty($students_for_senate_list) ? 'none' : 'display'); ?>">
						<fieldset style="padding-bottom: 0px; padding-top: 20px;">
							<!-- <legend>&nbsp;&nbsp; Search / Filter &nbsp;&nbsp;</legend> -->
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'style' => 'width:90%', 'label' => 'Program: ', 'type' => 'select', 'options' => $programs, 'default' => (!empty($default_program_id) ? $default_program_id : ''))); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'style' => 'width:90%', 'label' => 'Program Type: ', 'type' => 'select', 'options' => $program_types, 'default' => (!empty($default_program_type_id) ? $default_program_type_id : ''))); ?>
								</div>
								<div class="large-6 columns">
									<?= $this->Form->input('department_id', array('id' => 'Department', 'class' => 'fs14', 'style' => 'width:100%', 'label' => 'Department: ', 'type' => 'select', 'options' => $departments, 'default' => (!empty($default_department_id) ? $default_department_id : ''))); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('percent_completed', array('id' => 'percentCompleted', 'class' => 'fs13', 'type' => 'number', 'min' => '0.45', 'max' => '0.80', 'step' => '0.05', 'style' => 'width:50%;', 'default' => '0.45', 'label' => 'Completed Courses(%): ')); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('academicyear', array('id' => 'AcademicYear', 'class' => 'fs14', 'style' => 'width:90%', 'label' => 'Admission Year: ', 'type' => 'select', 'options' => $admission_years, 'default' => '', 'empty' => 'All')); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('studentnumber', array('id' => 'studentNumber', 'class' => 'fs14', 'style' => 'width:90%', 'placeholder' => 'Type Student ID for one student.. ', 'label' => 'Student ID: ', 'type' => 'text', 'maxLength' => '25')); ?>
								</div>
								<div class="large-3 columns">
									&nbsp;
								</div>
							</div>
							<hr>
							<?= $this->Form->submit(__('List Eligible Students'), array('name' => 'listStudentsForSenateList', 'id' => 'listStudentsForSenateList', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
						</fieldset>
						<?= $this->Form->end(); ?>
					</div>
					<hr>

					<div id="student_list_for_senate">
						<?php
						if (!empty($students_for_senate_list)) {

							echo $this->Form->create('SenateList', array('data-abide', 'onSubmit' => 'return checkForm(this);'));

							$currentMonth = date('F');

							if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) {
								$yFrom = date('Y') - (Configure::read('Calendar.senateApprovalInPast') + 8);
							} else {
								$yFrom = date('Y') - Configure::read('Calendar.senateApprovalInPast');
							}
							
							$yTo = date('Y') + Configure::read('Calendar.senateApprovalAhead'); ?>

							<blockquote>
								<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
								<p style="text-align:justify; line-height: 1.25;">
									<span class="fs16">Below is the list of students who fullfill the minimum credit hour requirement for graduation according to their attached curriculum. Please enter minute number, approval date and select students who should be included in the senate list.</span>
									<ul style="line-height: 1.5;text-align:justify;">
										<li><span class="fs15">Please note that, students who doesn't fullfill the minimum credit for graduation set in their attached curriculum and students who are already in senate list or graduate list will not appear here.</span></li> 
										<li><span class="fs15 on-process">It's a good idea to <a href="/studentStatusPatterns/regenerate_academic_status" target="_blank">Batch Regenerate Student Status</a> of the students or <a href="/studentStatusPatterns/regenerate_individual_academic_status" target="_blank">Regenerate Student's Status(Individually)</a> before adding them to the Senate List to avoid possible CGPA variation on online and printed versions that might occure due to various reasons like: Status determination setting changes, late grade changes, supplemetary exam submissions, calculation errors etc.</span></li>
									</ul>
								</p>
							</blockquote>

							<hr>
							<fieldset>
								<div class="row">
									<div class="large-2 columns">
										&nbsp;
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('minute_number', array('id' => 'minuteNumber', 'style' => 'width:90%;', 'required', ' placeholder' => 'Format: ' . (!empty($college_shortname) ? $college_shortname : 'AMiT') . '/'.$currentMonth.'/'. $yTo.'', 'title' => 'Format: ' . (!empty($college_shortname) ? $college_shortname : 'AMiT') . '/'.$currentMonth.'/'. $yTo.'', 'pattern' => 'minute_number', 'label' => 'Minute Number: <small></small></label><small class="error" style="background: #fff; width: 90%; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Format: ' . (!empty($college_shortname) ? $college_shortname : 'AMiT') . '/'.$currentMonth.'/'. $yTo.'</small>')); ?>
									</div>
									<div class="large-5 columns">
										<?= $this->Form->input('approved_date', array('label' => 'Approved Date: ', 'minYear' => $yFrom, 'maxYear' => $yTo, 'style' => 'width:30%;')); ?>
									</div>
									<div class="large-2 columns">
										&nbsp;
									</div>
								</div>
							</fieldset>
							<hr>

							<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
							<br>
							
							<?php
							$eligible_students_count = 0;
							$count = 1;
							if (isset($students_for_senate_list) && !empty($students_for_senate_list)) {
								foreach ($students_for_senate_list as $c_id => $students) { ?>
									<table cellpadding="0" cellspacing="0" class="table">
										<tbody>
											<tr>
												<td style="width:22%" class="vcenter"><span class="text-gray">Department: </span>&nbsp; <?= $students[0]['Department']['name']; ?></td>
											</tr>
											<tr>
												<td class="vcenter"><span class="text-gray">Program: </span>&nbsp; <?= $students[0]['Program']['name']; ?></td>
											</tr>
											<tr>
												<td class="vcenter"><span class="text-gray">Curriculum: </span>&nbsp; <?= $students[0]['Curriculum']['name']; ?> &nbsp; &nbsp; &nbsp; &nbsp; <a href="/curriculums/view/<?=$students[0]['Curriculum']['id']; ?>" target="_blank">(Open Curriculum Details)</a></td>
											</tr>
											<tr>
												<td class="vcenter"><span class="text-gray">Degree Designation: </span>&nbsp; <?= $students[0]['Curriculum']['english_degree_nomenclature']; ?></td>
											</tr>
											<?php
											if (!empty($students[0]['Curriculum']['specialization_english_degree_nomenclature'])) { ?>
												<tr>
													<td class="vcenter"><span class="text-gray">Specialization: </span>&nbsp; <?= $students[0]['Curriculum']['specialization_english_degree_nomenclature']; ?></td>
												</tr>
												<?php
											} ?>
											<tr>
												<td class="vcenter"><span class="text-gray">Degree Designation (Amharic): </span>&nbsp; <?= $students[0]['Curriculum']['amharic_degree_nomenclature']; ?></td>
											</tr>
											<?php
											if (!empty($students[0]['Curriculum']['specialization_amharic_degree_nomenclature'])) { ?>
												<tr>
													<td class="vcenter"><span class="text-gray">Specialization (Amharic): </span>&nbsp; <?= $students[0]['Curriculum']['specialization_amharic_degree_nomenclature']; ?></td>
												</tr>
												<?php
											} ?>
											<tr>
												<td class="vcenter"><span class="text-gray">Required <?= (!isset($students[0]['Curriculum']['id']) ? 'Credit' : (count(explode('ECTS', $students[0]['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit')); ?> for Graduation: </span>&nbsp; <?= $students[0]['Curriculum']['minimum_credit_points']; ?></td>
											</tr>
										</tbody>
									</table>
									<br>

									<div style="overflow-x:auto;">
										<table cellpadding="0" cellspacing="0" class="table student_list">
											<thead>
												<tr>
													<td style="width:4%" class="center"></td>
													<td style="width:4%" class="center">#</td>
													<td style="width:25%" class="vcenter">Student Name</td>
													<td style="width:5%" class="center">Sex</td>
													<td style="width:13%" class="center">Student ID</td>
													<td style="width:15%" class="center">Program Type</td>
													<td style="width:13%" class="center"><?= (!isset($students[0]['Curriculum']['id']) ? 'Credit' : (count(explode('ECTS', $students[0]['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit')); ?> Taken</td>
													<td style="width:10%" class="center">CGPA</td>
													<td style="width:10%" class="center">MCGPA</td>
												</tr>
											</thead>
											<tbody>
												<?php
												$s_count = 1;
												foreach ($students as $key => $student) {
													if ($key == 0) {
														continue;
													} ?>
													<tr style="color:<?= (empty($student['disqualification']) ? 'green' : 'red'); ?>"  class="center">
														<?php
														if (!empty($student['disqualification'])) { ?>
															<td style="background-color:white" onclick="toggleView(this)" id="<?= $count; ?>" class="center">
																<div style="margin-left: 10%;"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $count, 'div' => false, 'align' => 'left')); ?> ? </div>
															</td>
															<?php
														} else { ?>
															<td style="background-color:white" class="center">
																<?= $this->Form->input('Student.' . $count . '.id', array('type' => 'hidden', 'value' => $student['Student']['id'])); ?>
																<div style="margin-left: 10%;"><?= $this->Form->input('Student.' . $count . '.include_senate', array('type' => 'checkbox', 'label' => false)); ?></div>
															</td>
															<?php
															$eligible_students_count++;
														} ?>
														<td style="background-color:white" class="center"><?= $s_count++; ?></td>
														<td style="background-color:white" class="vcenter"><?= $this->Html->link(__($student['Student']['full_name']), array('controller' => 'students', 'action' => 'student_academic_profile', $student['Student']['id']), array('target' => '_blank', 'style' => 'font-weight:normal; color:' . (empty($student['disqualification']) ? 'green' : 'red'))); ?></td>
														<td style="background-color:white" class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' :(strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : trim($student['Student']['gender']))); ?></td>
														<td style="background-color:white" class="center"><?= $student['Student']['studentnumber']; ?></td>
														<td style="background-color:white;" class="center"><?= ($student['Student']['program_type_id'] != PROGRAM_TYPE_REGULAR ? '<span style="font-weight: bold">' . $student['ProgramType']['name'] .'</span>' : $student['ProgramType']['name']); ?></td>
														<td style="background-color:white" class="center"><?= $student['credit_taken']; ?></td>
														<td style="background-color:white" class="center"><b><?= $student['cgpa']; ?></b></td>
														<td style="background-color:white" class="center"><?= $student['mcgpa']; ?></td>
													</tr>
													<?php
													if (!empty($student['disqualification'])) { ?>
														<tr id="c<?= $count; ?>" style="display:none">
															<td style="background-color:white">&nbsp;</td>
															<td colspan="8" style="background-color:white;">
																<ol>
																	<?php
																	foreach ($student['disqualification'] as $d_key => $disqualification) {
																		echo '<li>' . $disqualification . '</li>';
																	} ?>
																</ol>
															</td>
														</tr>
														<?php
													}
													$count++;
												} ?>
											</tbody>
										</table>
									</div>
									<hr>
									<?php
								}//End of each curriculum students
							}

							echo $this->Form->submit(__('Add Student to Senate List'), array('name' => 'addStudentToSenateList', 'id' => 'addStudentToSenateList', (empty($eligible_students_count) ? 'disabled': ''), 'class' => 'tiny radius button bg-blue'));
							echo $this->Form->end();
						} else if (isset($this->request->data) && empty($students_for_senate_list)) {
							//echo '<div id="flashMessage" class="info-box info-message"><span></span>The system unable to find list of students who are not on the senate list but fully take the minimum credit hour which is set on their curriculum.</div>';
						} ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>

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
	

	var looking_for_students_for_the_senate_list = false;

	$('#listStudentsForSenateList').click(function(event) {
		
		var isValid = true;

		$('#student_list_for_senate').hide();

		if ($("#minuteNumber").length) {
			$('#minuteNumber').val('');
			$('#addStudentToSenateList').attr('disabled', true);
		}

		if (looking_for_students_for_the_senate_list) {
			alert('Looking for eligible students for the senate list, please wait a moment...');
			$('#listStudentsForSenateList').attr('disabled', true);
			isValid = false;
			return false;
		}

		if (!looking_for_students_for_the_senate_list && isValid) {
			$('#listStudentsForSenateList').val('Looking for Eligible Students...');
			looking_for_students_for_the_senate_list = true;
			isValid = true
			return true;
		} else {
			return false;
		}
	});

	var form_being_submitted = false;

	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	var checkForm = function(form) {
		
		var minuteNumber = $('#minuteNumber').val();

		if (minuteNumber == '') { 
			//form.minuteNumber.focus();
			$('#minuteNumber').focus();
			return false;
		}

		var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

		//alert(checkedOne);
		if (!checkedOne) {
			alert('At least one student must be selected to add to the senate list.');
			validationMessageNonSelected.innerHTML = 'At least one student must be selected to add to the senate list.';
			return false;
		}

		if (form_being_submitted) {
			alert("Adding Selected Students Senate List, please wait a moment...");
			$('#addStudentToSenateList').attr('disabled', true);
			$('#listStudentsForSenateList').attr('disabled', true);
			return false;
		}

		if (!form_being_submitted) {
			$('#listStudentsForSenateList').attr('disabled', true);
			$('#addStudentToSenateList').val('Adding Selected Students Senate List...');
			form_being_submitted = true;
			return true;
		} else {
			return false;
		}
	}

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>