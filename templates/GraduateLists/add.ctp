<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Add Student to Graduate List'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div style="margin-top: -40px;"><hr></div>

				<div class="graduateLists form">

					<?= $this->Form->create('GraduateList'); ?>

					<div onclick="toggleViewFullId('ListPublishedCourse')">
						<?php
						if (!empty($students_for_graduate_list)) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Display Filter</span>
							<?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Hide Filter</span>
							<?php
						} ?>
					</div>

					<div id="ListPublishedCourse" style="display:<?= (!empty($students_for_graduate_list) ? 'none' : 'display'); ?>">
						<fieldset style="padding-bottom: 0px;padding-top: 20px;">
							<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
							<div class="row">
								<div class="large-12 columns">
									<div class="large-3 columns">
										<?= $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => 'Program:', 'type' => 'select', 'options' => $programs, 'default' => (!empty($default_program_id) ? $default_program_id : ''), 'style' => 'width:100%;')); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => 'Program Type:', 'type' => 'select', 'options' => $program_types, 'default' => (!empty($default_program_type_id) ? $default_program_type_id : ''), 'style' => 'width:100%;')); ?>
									</div>
									<div class="large-6 columns">	
										<?= $this->Form->input('department_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => 'Department:', 'type' => 'select', 'options' => $departments, 'default' => (!empty($default_department_id) ? $default_department_id : ''), 'style' => 'width:100%;')); ?>
									</div>
									<hr>
									<?= $this->Form->submit(__('List Eligible Students'), array('name' => 'listStudentsForGraduateList', 'id' => 'listStudentsForGraduateList', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
								</div>
							</div>
						</fieldset>
						<?= $this->Form->end(); ?>
					</div>
					<hr>

					<div id="student_list_for_senate">
						<?php
						if (!empty($students_for_graduate_list)) {

							echo $this->Form->create('GraduateList', array('data-abide', 'onSubmit' => 'return checkForm(this);'));

							// Keep search data values for redisplay
							echo $this->Form->input('department_id', array('type' => 'hidden', 'value' => $default_department_id)); 
							echo $this->Form->input('program_id', array('type' => 'hidden', 'value' => $default_program_id)); 
							echo $this->Form->input('program_type_id', array('type' => 'hidden', 'value' => $default_program_type_id)); 

							$currentMonth = date('F');

							if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) {
								$yFrom = date('Y') - (Configure::read('Calendar.graduateApprovalInPast') + 8);
							} else {
								$yFrom = date('Y') - Configure::read('Calendar.graduateApprovalInPast');
							}
							$yTo = date('Y') + Configure::read('Calendar.graduateApprovalAhead');  ?>

							<hr/>
							<blockquote>
								<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
								<p style="text-align:justify;"><span class="fs16"> Make your entry and selection carefully. You will have <span class="text-red"> limited time to apply changes (<?= Configure::read('Calendar.daysAvaiableForGraduateDeletion'); ?> days) </span> once you add students to graduated students list.</span></p> 
							</blockquote>
							<hr>

							<p style="margin-bottom:0px"> <span class="fs14">Please specify graduation date, provide minute number and select students from the list below to include students in graduated students list.</span></p>
							<fieldset>
								<div class="row">
									<div class="large-2 columns">
										&nbsp;
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('minute_number', array('id' => 'minuteNumber', 'style' => 'width:90%;', 'required', ' placeholder' => 'Format: ' . (!empty($college_shortname) ? $college_shortname : 'AMiT') . '/'.$currentMonth.'/'. $yTo.'', 'title' => 'Format: ' . (!empty($college_shortname) ? $college_shortname : 'AMiT') . '/'.$currentMonth.'/'. $yTo.'', 'pattern' => 'minute_number', 'label' => 'Minute Number: <small></small></label><small class="error" style="background: #fff; width: 90%; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Format: ' . (!empty($college_shortname) ? $college_shortname : 'AMiT') . '/'.$currentMonth.'/'. $yTo.'</small>')); ?>
									</div>
									<div class="large-5 columns">
										<?= $this->Form->input('graduate_date', array('label' => 'Graduation Date: ', 'minYear' => $yFrom, 'maxYear' => $yTo, 'style' => 'width:30%;')); ?>
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
							$count = 1;
							foreach ($students_for_graduate_list as $c_id => $students) { ?>

								<table cellpadding="0" cellspacing="0" class="table">
									<tr><td class="vcenter">Degree Designation: &nbsp;&nbsp; <?= $students[0]['Curriculum']['english_degree_nomenclature']; ?></td></tr>
									<?php
									if (!empty($students[0]['Curriculum']['specialization_english_degree_nomenclature'])) { ?>
										<tr><td class="vcenter">Specialization: &nbsp;&nbsp; <?= $students[0]['Curriculum']['specialization_english_degree_nomenclature']; ?></td></tr>
										<?php
									} ?>
									<tr><td class="vcenter">Degree Designation (Amharic): &nbsp;&nbsp <?= $students[0]['Curriculum']['amharic_degree_nomenclature']; ?></td></tr>
									<?php
									if (!empty($students[0]['Curriculum']['specialization_amharic_degree_nomenclature'])) { ?>
										<tr><td class="vcenter">Specialization (Amharic): &nbsp;&nbsp; <?= $students[0]['Curriculum']['specialization_amharic_degree_nomenclature']; ?></td></tr>
										<?php
									} ?>
									<tr><td class="vcenter">Curriculum Name: &nbsp;&nbsp;&nbsp;<?= $students[0]['Curriculum']['name']; ?> &nbsp; &nbsp; &nbsp; &nbsp; <a href="/curriculums/view/<?=$students[0]['Curriculum']['id']; ?>" target="_blank">(Open Curriculum Details)</a></td></tr>
									<tr><td class="vcenter">Department: &nbsp;&nbsp; <?= $students[0]['Department']['name']; ?></td></tr>
									<tr><td class="vcenter">Program: &nbsp;&nbsp; <?= $students[0]['Program']['name']; ?></td></tr>
									<tr><td class="vcenter">Required <?= (!isset($students[0]['Curriculum']['id']) ? 'Credit' : (count(explode('ECTS', $students[0]['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit')); ?> for Graduation: &nbsp;&nbsp; <?= $students[0]['Curriculum']['minimum_credit_points']; ?></td></tr>
								</table>
								<br>

								<div style="overflow-x:auto;">
									<table cellpadding="0" cellspacing="0" class="table student_list">
										<thead>
											<tr>
												<td style="width:3%" class="center"></td>
												<td style="width:2%" class="center">#</td>
												<td style="width:30%" class="vcenter">Student Name</td>
												<td style="width:5%;" class="center">Sex</td>
												<td style="width:15%" class="center">Student ID</td>
												<td style="width:15%" class="center">Program Type</td>
												<td style="width:15%;" class="center"><?= (!isset($students[0]['Curriculum']['id']) ? 'Credit' : (count(explode('ECTS', $students[0]['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit')); ?> Taken</td>
												<td style="width:10%;" class="center">CGPA</td>
												<td style="width:10%;" class="center">MCGPA</td>
											</tr>
										</thead>
										<tbody>
											<?php
											$s_count = 1;
											foreach ($students as $key => $student) {
												if ($key == 0) {
													continue;
												}
												//TODO: Remove the following code
												//if($key > 5)
												//$student['disqualification'] = null; ?>
												<tr>
													<?php
													if (!empty($student['disqualification'])) { ?>
														<td  class="center" style="background-color:white" onclick="toggleView(this)" id="<?= $count; ?>"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $count, 'div' => false, 'align' => 'left')); ?>?</td>
														<?php
													} else { ?>
														<td class="center" style="background-color:white">
															<div style="margin-left: 25%;"><?= $this->Form->input('Student.' . $count . '.id', array('type' => 'hidden', 'value' => $student['Student']['id'])); ?></div>
															<?= $this->Form->input('Student.' . $count . '.include_graduate', array('type' => 'checkbox', 'label' => false)); ?>
														</td>
														<?php
													} ?>
													<td style="background-color:white" class="center"><?= $s_count++; ?></td>
													<td style="background-color:white" class="vcenter"><?= $this->Html->link($student['Student']['full_name'],'#',  array('class' => 'jsview', 'data-animation' => "fade", 'data-reveal-id' => 'myModal', 'data-reveal-ajax' => "/students/get_modal_box/" . $student['Student']['id'])); ?></td>
													<td style="background-color:white" class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' :(strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : trim($student['Student']['gender']))); ?></td>
													<td style="background-color:white;" class="center"><?= $student['Student']['studentnumber']; ?></td>
													<td style="background-color:white;" class="center"><?= ($student['Student']['program_type_id'] != PROGRAM_TYPE_REGULAR ? '<span style="font-weight: bold">' . $student['ProgramType']['name'] .'</span>' : $student['ProgramType']['name']); ?></td>
													<td style="background-color:white;" class="center"><?= $student['credit_taken']; ?></td>
													<td style="background-color:white;" class="center"><b><?= $student['cgpa']; ?></b></td>
													<td style="background-color:white;" class="center"><?= $student['mcgpa']; ?></td>
												</tr>
												<?php
												if (!empty($student['disqualification'])) { ?>
													<tr id="c<?= $count; ?>" style="display:none">
														<td  class="vcenter" style="background-color:#f0f0f0">&nbsp;</td>
														<td  class="vcenter" colspan="8" style="background-color:#f0f0f0">
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
								<hr/>
								<?php
							} //End of each curriculum students
							echo $this->Form->submit(__('Add Selected to Graduate List'), array('name' => 'addStudentToGraduateList', 'id' => 'addStudentToGraduateList', 'class' => 'tiny radius button bg-blue', 'div' => false));
							echo $this->Form->end();
						} else if (isset($this->request->data['listStudentsForGraduateList']) && empty($students_for_graduate_list)) { ?>
							<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>The system unable to find list of students who are in the senate list but not in the graduate list based on your search criteria.</div>
							<?php
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

	$('#listStudentsForGraduateList').click(function(event) {
		
		var isValid = true;

		$('#student_list_for_senate').hide();

		if ($("#minuteNumber").length) {
			$('#minuteNumber').val('');
			$('#addStudentToGraduateList').attr('disabled', true);
		}

		if (looking_for_students_for_the_senate_list) {
			alert('Looking for eligible students for the graduate list, please wait a moment...');
			$('#listStudentsForGraduateList').attr('disabled', true);
			isValid = false;
			return false;
		}

		if (!looking_for_students_for_the_senate_list && isValid) {
			$('#listStudentsForGraduateList').val('Looking for Eligible Students...');
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
			alert('At least one student must be selected to add to the graduate list.');
			validationMessageNonSelected.innerHTML = 'At least one student must be selected to add to the graduate list.';
			return false;
		}

		if (form_being_submitted) {
			alert("Adding Selected Students Graduate List, please wait a moment...");
			$('#addStudentToGraduateList').attr('disabled', true);
			$('#listStudentsForGraduateList').attr('disabled', true);
			return false;
		}

		if (!form_being_submitted) {
			$('#listStudentsForGraduateList').attr('disabled', true);
			$('#addStudentToGraduateList').val('Adding Selected Students Senate List...');
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