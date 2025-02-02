<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= 'View Instructor Load '; ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('CourseInstructorAssignment'); ?>
				<div style="margin-top: -30px;">
					<hr>
                    <fieldset style="padding-bottom: 5px; padding-bottom: 0px;">
                        <!-- <legend>&nbsp;&nbsp; Search &nbsp;&nbsp;</legend> -->
                        <div class="row">
                            <div class="large-2 columns">
								<?= $this->Form->input('Search.academic_year', array('id' => 'AcadamicYear', 'label' => 'Acadamic Year: ', 'class' => 'fs14', 'style' => 'width: 90%;', 'type' => 'select', 'options' => $acyear_array_data, 'onchange' => 'updateInstructor(1)', 'default' => $current_acy_and_semester['academic_year'])); ?>
                            </div>
                            <div class="large-2 columns">
								<?= $this->Form->input('Search.semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width: 90%;', 'label' => 'Semester: ', 'options' => Configure::read('semesters'), 'onchange' => 'updateInstructor(1)', 'default' => $current_acy_and_semester['semester'])); ?>
                            </div>
                            <div class="large-4 columns">
								<?= $this->Form->input('Search.college_id', array('label' => 'College: ', 'class' => 'fs14', 'options' => $colleges, 'default' => $selected_college_id,  'onchange' => 'updateDepartment(1)', 'id' => 'college_id_1', 'style' => 'width: 95%;')); ?>
                            </div>
                            <div class="large-4 columns">
								<?= $this->Form->input('Search.department_id', array('class' => 'fs14', 'style' => 'width: 95%;', 'label' => 'Department:', 'id' => 'department_id_1', 'onchange' => 'updateInstructor(1)', 'options' => $departments, 'default' => $selected_department_id)); ?>
                            </div>
                        </div>
						<div class="row">
                            <div class="large-4 columns">
								<?= $this->Form->input('Search.staff_id', array('label' => 'Instructor: ', 'id' => 'staff_id_1', 'class' => 'fs14', 'options' => $staffs, 'required', 'style' => 'width: 95%;')); ?>
							</div>
						</div>
						<hr>
						<?= $this->Form->submit(__('View Load'), array('name' => 'viewInstructorLoad', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
                    </fieldset>
                </div>
				
				<?php
				if (isset($instructor_loads) && !empty($instructor_loads)) {
					debug($instructor_loads);
					foreach ($instructor_loads as $acadamic_year => $semester) {
						foreach ($semester as $sem => $course_instructor) { ?>
							<table cellpadding="0" cellspacing="0" class="table fs13">
								<tr>
									<td style="font-weight:bold"><span class="text-gray" style="font-weight: bold;">Instructor Name: </span> &nbsp;&nbsp;
										<?php
										if (isset($staff_details) && !empty($staff_details)) {
											$staff_title_position = null;
											if (isset($staff_details['Title'])) {
												$staff_title_position .= $staff_details['Title']['title'];
											}

											if (isset($staff_details['Staff'])) {
												$staff_title_position .= ' ' . $staff_details['Staff']['full_name'];
											}

											if (isset($staff_details['Position'])) {
												$staff_title_position .= '<strong> (' . $staff_details['Position']['position'] . ')</strong>';
											}
											echo  $staff_title_position;
										} ?>
									</td>
								</tr>
								<tr>
									<td style="font-weight:bold; background-color: white;"><span class="text-gray" style="font-weight: bold;">Acadamic Year: </span> &nbsp;&nbsp; <?= $acadamic_year; ?> &nbsp; &nbsp; &nbsp; <span class="text-gray" style="font-weight: bold;">Semester: </span> &nbsp;&nbsp; <?= $sem; ?></td>
								</tr>
							</table>
							<br>
							
							<div style="overflow-x:auto;">
								<table cellpadding="0" cellspacing="0" class="table fs13">
									<thead>
										<tr>
											<th style="width:3%" class="center">#</th>
											<th style="width:27%" class="vcenter">Course Title</th>
											<th style="width:10%" class="center">Course Code</th>
											<th style="width:20%" class="center">Assigned Section</th>
											<th style="width:18%" class="center">Assignment Type</th>
											<th style="width:6%" class="center">Credit</th>
											<th style="width:10%" class="center">L T L</th>
											<th style="width:6%" class="center">Load</th>
										</tr>
									</thead>
									<tbody>

										<?php
										$c_count = 1;
										$loads = 0;
										//L T L
										//2 + 3* 2/3 0x2/3 = LOAD
										// Load = LecHr*(registeredStudents/50) + ((LabHrs+TutHrs)*0.67)*(registeredStudent/40))
										foreach ($course_instructor as $index => $value) { ?>
											<tr>
												<td style="text-align: center;"><?= $c_count++; ?></td>
												<td><?= $value['PublishedCourse']['Course']['course_title']; ?></td>
												<td class="center"><?= $value['PublishedCourse']['Course']['course_code']; ?></td>
												<td class="center"><?= $value['Section']['name']; ?></td>
												<td class="center"><?= ucwords($value['CourseInstructorAssignment']['type']); ?></td>
												<td class="center"><?= $value['PublishedCourse']['Course']['credit']; ?> </td>
												<td class="center"><?= $value['PublishedCourse']['Course']['course_detail_hours']; ?> </td>
												<td class="center">
													<?php

													$credit_type_check = explode('ECTS',$value['PublishedCourse']['Course']['Curriculum']['type_credit']);

													if (count($credit_type_check) >= 2){
														//debug($credit_type_check);
														$credit_type = 'ECTS_CREDIT_TYPE';
														debug($credit_type);
													} else {
														//debug($credit_type_check);
														$credit_type = 'CREDIT_POINT';
														debug($credit_type);
														debug(round(($value['PublishedCourse']['Course']['credit'] * CREDIT_TO_ECTS),0) .' ECTS');
														debug(($value['PublishedCourse']['Course']['credit'] * CREDIT_TO_ECTS) .' ECTS');
													}

													if (strcasecmp($value['CourseInstructorAssignment']['type'], 'Lecture') === 0) {
														echo number_format($value['PublishedCourse']['Course']['lecture_hours'] * (count($value['PublishedCourse']['CourseRegistration']) / 50), 2, '.', ',');
														$loads += ($value['PublishedCourse']['Course']['lecture_hours'] * (count($value['PublishedCourse']['CourseRegistration']) / 50));
													} else if (strcasecmp($value['CourseInstructorAssignment']['type'], 'Lecture+Tutorial') === 0) {
														/* echo number_format($value['PublishedCourse']['Course']['lecture_hours']+ $value['PublishedCourse']['Course']['tutorial_hours']*(2/3),2,'.',',');
														$loads +=($value['PublishedCourse']['Course']['lecture_hours']+ $value['PublishedCourse']['Course']['tutorial_hours']*(2/3)); */
														// Load = LecHr*(registeredStudents/50) + ((LabHrs+TutHrs)*0.67)*(registeredStudent/40))
														echo number_format($value['PublishedCourse']['Course']['lecture_hours'] * (count($value['PublishedCourse']['CourseRegistration']) / 50) + (($value['PublishedCourse']['Course']['tutorial_hours'] + 0) * 0.67) * (count($value['PublishedCourse']['CourseRegistration']) / 40), 2, '.', '2');
														$loads += ($value['PublishedCourse']['Course']['lecture_hours'] * (count($value['PublishedCourse']['CourseRegistration']) / 50) + (($value['PublishedCourse']['Course']['tutorial_hours'] + 0) * 0.67) * (count($value['PublishedCourse']['CourseRegistration']) / 40));
													} else if (strcasecmp($value['CourseInstructorAssignment']['type'], 'tutorial') === 0) {

														if (isset($value['PublishedCourse']['Course']['tutorial_hours'])) {
															/* echo number_format(
															$value['PublishedCourse']['Course']['tutorial_hours']*(2/3),2,'.',','); 
															$loads += ($value['PublishedCourse']['Course']['tutorial_hours']*(2/3)); */
															// Load = LecHr*(registeredStudents/50) + ((LabHrs+TutHrs)*0.67)*(registeredStudent/40))

															echo number_format(0 * (count($value['PublishedCourse']['CourseRegistration']) / 50) + (($value['PublishedCourse']['Course']['tutorial_hours'] + 0) * 0.67) * (count($value['PublishedCourse']['CourseRegistration']) / 40), 2, '.', ',');
															$loads += (0 * (count($value['PublishedCourse']['CourseRegistration']) / 50) + (($value['PublishedCourse']['Course']['tutorial_hours'] + 0) * 0.67) * (count($value['PublishedCourse']['CourseRegistration']) / 40));
														}
													} else if (strcasecmp($value['CourseInstructorAssignment']['type'], 'lab') === 0) {
														if (isset($value['PublishedCourse']['Course']['laboratory_hours'])) {
															/* echo number_format($value['PublishedCourse']['Course']['laboratory_hours']*(2/3),2,'.',','); 
															$loads += ($value['PublishedCourse']['Course']['laboratory_hours']*(2/3)); */
															// Load = LecHr*(registeredStudents/50) + ((LabHrs+TutHrs)*0.67)*(registeredStudent/40))

															echo number_format(0 * (count($value['PublishedCourse']['CourseRegistration']) / 50) + (($value['PublishedCourse']['Course']['laboratory_hours'] + 0) * 0.67) * (count($value['PublishedCourse']['CourseRegistration']) / 40), 2, '.', ',');
															$loads += (0 * (count($value['PublishedCourse']['CourseRegistration']) / 50) + (($value['PublishedCourse']['Course']['laboratory_hours'] + 0) * 0.67) * (count($value['PublishedCourse']['CourseRegistration']) / 40));
														}
													} else if (strcasecmp($value['CourseInstructorAssignment']['type'], 'Lecture+Lab') === 0) {
														/* echo number_format($value['PublishedCourse']['Course']['lecture_hours']+ $value['PublishedCourse']['Course']['laboratory_hours']*(2/3),2,'.',',');
														$loads +=($value['PublishedCourse']['Course']['lecture_hours']+ $value['PublishedCourse']['Course']['laboratory_hours']*(2/3)); */

														echo number_format($value['PublishedCourse']['Course']['lecture_hours'] * (count($value['PublishedCourse']['CourseRegistration']) / 50) + (($value['PublishedCourse']['Course']['laboratory_hours'] + 0) * 0.67) * (count($value['PublishedCourse']['CourseRegistration']) / 40), 2, '.', ',');
														$loads += ($value['PublishedCourse']['Course']['lecture_hours'] * (count($value['PublishedCourse']['CourseRegistration']) / 50) + (($value['PublishedCourse']['Course']['laboratory_hours'] + 0) * 0.67) * (count($value['PublishedCourse']['CourseRegistration']) / 40));
													} else if (strcasecmp($value['CourseInstructorAssignment']['type'], 'Lecture Tutorial Lab') === 0) {
														echo number_format($value['PublishedCourse']['Course']['lecture_hours'] * (count($value['PublishedCourse']['CourseRegistration']) / 50) + (($value['PublishedCourse']['Course']['laboratory_hours'] + $value['PublishedCourse']['Course']['tutorial_hours']) * 0.67) * (count($value['PublishedCourse']['CourseRegistration']) / 40), 2, '.', ',');
														$loads += ($value['PublishedCourse']['Course']['lecture_hours'] * (count($value['PublishedCourse']['CourseRegistration']) / 50) + (($value['PublishedCourse']['Course']['laboratory_hours'] + $value['PublishedCourse']['Course']['tutorial_hours']) * 0.67) * (count($value['PublishedCourse']['CourseRegistration']) / 40));
													} ?>
												</td>
											</tr>
											<?php
											/* Lecture  tutorial Lecture+Tutorial */
											// end of course iteration 
										} ?>

										<tr>
											<td></td>
											<td colspan=6 style="text-align: right; vertical-align: middle;"><strong>Total Load:</strong></td>
											<td class="center"><strong><?= number_format($loads, 2, '.', ','); ?></strong></td>
										</tr>
									</tbody>
								</table>
							</div>
							<?php
							// end of semester
						}
						// end of year level  
					}
					// end of curriculum  
				}  ?>
			</div>
		</div>
	</div>
</div>

<script type='text/javascript'>
	//Sub Cat Combo 1
	function updateDepartment(id) {
		//serialize form data
		var formData = $("#college_id_" + id).val();
		$("#college_id_" + id).attr('disabled', true);
		$("#department_id_" + id).empty();
		//$("#department_id_" + id).append('<option style="width:100px">loading...</option>');
		$("#department_id_" + id).attr('disabled', true);
		$("#staff_id_" + id).empty();
		//$("#staff_id_" + id).append('<option style="width:100px">loading...</option>');
		$("#staff_id_" + id).attr('disabled', true);
		//get form action
		var formUrl = '/departments/get_department_combo/' + formData + '/' + 1 + '/' + 1;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data, textStatus, xhr) {
				$("#department_id_" + id).attr('disabled', false);
				$("#college_id_" + id).attr('disabled', false);
				$("#department_id_" + id).empty();
				$("#department_id_" + id).append(data);
				//Items list
				var subCat = $("#department_id_" + id).val();
				$("#staff_id_" + id).empty();
				//get form action
				var formUrl = '/course_instructor_assignments/get_instructor_combo/' + subCat;
				$.ajax({
					type: 'get',
					url: formUrl,
					data: subCat,
					success: function(data, textStatus, xhr) {
						$("#staff_id_" + id).attr('disabled', false);
						$("#staff_id_" + id).empty();
						$("#staff_id_" + id).append(data);
					},
					error: function(xhr, textStatus, error) {
						alert(textStatus);
					}
				});
				//End of items list
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});

		return false;
	}

	//Staff List
	function updateInstructor(id) {
		//serialize form data
		var subCat = $("#department_id_" + id).val();
		$("#staff_id_" + id).attr('disabled', true);
		$("#department_id_" + id).attr('disabled', true);
		//$("#staff_id_" + id).append('<option style="width:100px">loading...</option>');
		$("#staff_id_" + id).empty();
		//get form action
		var formUrl = '/course_instructor_assignments/get_instructor_combo/' + subCat;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: subCat,
			success: function(data, textStatus, xhr) {
				$("#staff_id_" + id).attr('disabled', false);
				$("#department_id_" + id).attr('disabled', false);
				$("#staff_id_" + id).empty();
				$("#staff_id_" + id).append(data);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});

		return false;
	}
</script>