<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-note"
				style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;">
				<?= __('Student Examination Grade Report'); ?>
			</span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div class="examGrades student_grade_view">
					<div style="margin-top: -30px;"><hr></div>
					<?= $this->Form->create('ExamGrade'); ?>
					<?php
					if (!empty($acadamic_years)) { ?>
						<h6 class="fs13 text-gray">Please select academic year and semester for which you want to get grade report.</h6>
						<fieldset style="padding-top: 5px;">
							<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('academic_year', array('id' => 'AcadamicYear', 'label' => 'Acadamic Year: ', 'class' => 'fs14', 'style' => 'width:90%;', 'type' => 'select', 'options' => $acadamic_years, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:90%;', 'label' => 'Semester: ', 'options' => Configure::read('semesters'), 'default' => (isset($semester_selected) ? $semester_selected : false))); ?>
								</div>
								<div class="large-3 columns">
									<br>
									<?= $this->Form->submit(__('Get My Grade Report'), array('name' => 'myGradeReport', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
								</div>
								<div class="large-3 columns">
									&nbsp;
								</div>
							</div>
						</fieldset>
						<?php
						if (isset($student_copy)) {
							if (!empty($student_copy['courses'])) { ?>
								<div style="overflow-x:auto;">
									<table cellpadding="0" cellspacing="0" class="table fs14">
										<tr>
											<td><b class="text-gray">Fullname: </b>&nbsp; <?= $student_copy['Student']['full_name']; ?></td>
											<td><b class="text-gray">Student ID: </b>&nbsp; <?= $student_copy['Student']['studentnumber']; ?></td>
										</tr>
										<tr>
											<td><b class="text-gray">Program:</b> &nbsp; <?= $student_copy['Student']['Program']['name']; ?></td>
											<td><b class="text-gray">Program Type:</b> &nbsp; <?= $student_copy['Student']['ProgramType']['name']; ?></td>
										</tr>
										<tr>
											<td>
												<b class="text-gray"><?= (isset($student_copy['Student']['College']['type']) && !empty($student_copy['Student']['College']['type']) ? $student_copy['Student']['College']['type'] . ':' : (isset($student_copy['College']['type']) && !empty($student_copy['College']['type']) ? $student_copy['College']['type'] . ':' : "College:")); ?></b>
												<?= (isset($student_copy['Student']['College']['name']) ? $student_copy['Student']['College']['name'] : $student_copy['College']['name']); ?>
											</td>
											<td>
												<b class="text-gray"><?= (is_null($student_copy['Section']['department_id']) || $student_copy['Section']['college_id'] != $student_copy['Department']['college_id'] ? 'Department: ' : (isset($student_copy['Student']['Department']['type']) && !empty($student_copy['Student']['Department']['type']) ? $student_copy['Student']['Department']['type'] . ': ' : (isset($student_copy['Department']['type']) && !empty($student_copy['Department']['type']) ? $student_copy['Department']['type'] . ': ' : 'Department: '))); ?></b>
												<?= (isset($student_copy['Student']['Department']['name']) && !empty($student_copy['Student']['Department']['name']) ? $student_copy['Student']['Department']['name'] : (isset($student_copy['Department']['name']) && !empty($student_copy['Department']['name']) ? $student_copy['Department']['name'] : 'Pre/Freshman')); ?>
											</td>
										</tr>
									</table>
								</div>

								<?php
								$credit_type = '';

								if (isset($student_copy['Curriculum']['type_credit']) && !empty($student_copy['Curriculum']['type_credit'])) {
									if (count(explode('ECTS', $student_copy['Curriculum']['type_credit'])) >= 2) {
										$credit_type = 'ECTS';
									} else {
										$credit_type = 'Credit';
									}
								}


								if (isset($student_copy['Section']['Curriculum']) && !empty($student_copy['Section']['Curriculum']['name']) && !is_null($student_copy['Section']['department_id'])) {
									if (isset($student_copy['Section']['Curriculum']['specialization_english_degree_nomenclature']) && !empty($student_copy['Section']['Curriculum']['specialization_english_degree_nomenclature'])) {
										$stream = explode(' in ', $student_copy['Section']['Curriculum']['specialization_english_degree_nomenclature']);
										if (count($stream) == 1) {
											$stream2 = explode(' of ', $student_copy['Section']['Curriculum']['specialization_english_degree_nomenclature']);
											if (count($stream2) == 2 && count($stream) == 1) {
												$stream[1] = $stream2[1];
											} else if (count($stream2) > 2 && count($stream) == 1) {
												$stream[1] = $stream2[2];
											} else {
												$stream[1] = $student_copy['Section']['Curriculum']['specialization_english_degree_nomenclature'];
											}
										}
									} else {
										$stream = explode(' in ', $student_copy['Section']['Curriculum']['english_degree_nomenclature']);
										if (count($stream) == 1) {
											$stream = explode(' of ', $student_copy['Section']['Curriculum']['english_degree_nomenclature']);
										}
									}

								} else if (isset($student_copy['Curriculum']) && !empty($student_copy['Curriculum']['name'])) {
									if (isset($student_copy['Curriculum']['specialization_english_degree_nomenclature']) && !empty($student_copy['Curriculum']['specialization_english_degree_nomenclature'])) {
										$stream = explode(' in ', $student_copy['Curriculum']['specialization_english_degree_nomenclature']);
										if (count($stream) == 1) {
											$stream2 = explode(' of ', $student_copy['Curriculum']['specialization_english_degree_nomenclature']);
											if (count($stream2) == 2 && count($stream) == 1) {
												$stream[1] = $stream2[1];
											} else if (count($stream2) > 2 && count($stream) == 1) {
												$stream[1] = $stream2[2];
											} else {
												$stream[1] = $student_copy['Curriculum']['specialization_english_degree_nomenclature'];
											}
										}
									} else {
										$stream = explode(' in ', $student_copy['Curriculum']['english_degree_nomenclature']);
										if (count($stream) == 1) {
											$stream = explode(' of ', $student_copy['Curriculum']['english_degree_nomenclature']);
										}
									}
								}

								if (isset($stream) && !empty($stream[1])) {
									$searchBracket = explode('(', $stream[1]);
									if (count($searchBracket) != 1) {
										$searchBracket = explode(')', $searchBracket[1]);
										$stream[1] = trim($searchBracket[0]);
									}
								}

								//debug($student_copy);
								debug($student_copy['Curriculum']['english_degree_nomenclature']);
								debug($student_copy['Section']['Curriculum']['type_credit']);
								?>

								<div style="overflow-x:auto;">
									<fieldset style="padding-top: 10px; padding-bottom: 10px;">
										<legend class="text-black"> &nbsp; &nbsp; <?= (is_null($student_copy['Section']['department_id']) ? 'Pre/1st' : $student_copy['YearLevel']['name'] . ' Year, ') . ' ' . ($student_copy['semester'] == 'I' ? '1st' : ($student_copy['semester'] == 'II' ? '2nd' : '3rd')) . ' Semester (' . $student_copy['academic_year'] . ')'; ?> &nbsp; &nbsp; </legend>
										<table cellpadding="0" cellspacing="0" class="table">
											<tr>
												<td>
													<div class="row">
														<div class="large-6 columns" style="padding: 0.2rem;">
															<?php
															if (is_null($student_copy['Section']['department_id']) || $student_copy['Section']['college_id'] != $student_copy['Department']['college_id']) {
																//$freshmanCollege = ClassRegistry::init('College')->find('first', array('conditions' => array('College.id' => $student_copy['Section']['college_id']), 'fields' => array('College.name', 'College.type'), 'recursive' => -1));
																$freshmanCollege['College'] = $student_copy['Section']['College'];
															} ?>
															<b class="text-gray">&nbsp;&nbsp;<?= (isset($freshmanCollege['College']['type']) && !empty($freshmanCollege['College']['type']) ? $freshmanCollege['College']['type'] . ':' : (isset($student_copy['College']['type']) && !empty($student_copy['College']['type']) ? $student_copy['College']['type'] . ':' : "College:")); ?></b>
															<?= (is_null($student_copy['Section']['department_id']) || $student_copy['Section']['college_id'] != $student_copy['Department']['college_id'] ? $freshmanCollege['College']['name'] : $student_copy['College']['name']); ?>
														</div>
														<div class="large-6 columns" style="padding: 0.2rem;">
															<b class="text-gray">&nbsp;&nbsp;<?= (is_null($student_copy['Section']['department_id']) || $student_copy['Section']['college_id'] != $student_copy['Department']['college_id'] ? 'Department: ' : (isset($student_copy['Department']['type']) && !empty($student_copy['Department']['type']) ? $student_copy['Department']['type'] . ': ' : 'Department: ')); ?></b>
															<?= (is_null($student_copy['Section']['department_id']) ? 'Pre/Freshman' : $student_copy['Department']['name']); ?>
														</div>
													</div>
													<div class="row">
														<div class="large-6 columns" style="padding: 0.2rem;">
															<b class="text-gray">&nbsp;&nbsp;Program:</b> <?= (isset($student_copy['Section']['Program']['name']) && !empty($student_copy['Section']['Program']['name']) ? $student_copy['Section']['Program']['name'] : $student_copy['Program']['name']); ?>
														</div>
														<div class="large-6 columns" style="padding: 0.2rem;">
															<b class="text-gray">&nbsp;&nbsp;Stream:</b> <?= (is_null($student_copy['Section']['department_id']) || !isset($stream) ? '---' : $stream[1]); ?>
														</div>
													</div>
													<div class="row">
														<div class="large-6 columns" style="padding: 0.2rem;">
															<b class="text-gray">&nbsp;&nbsp;Program Type:</b> <?= (isset($student_copy['Section']['ProgramType']['name']) && !empty($student_copy['Section']['ProgramType']['name']) ? $student_copy['Section']['ProgramType']['name'] : $student_copy['ProgramType']['name']); ?>
														</div>
														<div class="large-6 columns" style="padding: 0.2rem;">
															<b class="text-gray">&nbsp;&nbsp;Section:</b> <?= $student_copy['Section']['name']; ?>
														</div>
													</div>
													<!-- <div class="row">
														<div class="large-6 columns" style="padding: 0.2rem;">
															<b class="text-gray">&nbsp;&nbsp;ACY:</b> <?php //echo $student_copy['academic_year']; ?>
															<b class="text-gray">&nbsp;&nbsp;Semester:</b> <?php //echo $student_copy['semester']; ?>
														</div>
														<div class="large-6 columns" style="padding: 0.2rem;">
															<b class="text-gray">&nbsp;&nbsp;Year Level:</b> <?php //echo (is_null($student_copy['Section']['department_id']) ? 'Pre/1st' : $student_copy['YearLevel']['name']); ?>
														</div>
													</div> -->
												</td>
											</tr>
										</table>
										<br>

										<?php 
										//debug($student_copy['courses'][0]); 
										//debug($student_copy);
										
										if (isset($student_copy['Section']['Curriculum']) && !empty($student_copy['Section']['Curriculum']['type_credit'])) {
											debug($student_copy['Section']['Curriculum']['type_credit']);
											if (count(explode('ECTS', $student_copy['Section']['Curriculum']['type_credit'])) >= 2) {
												$credit_type = 'ECTS';
											} else {
												$credit_type = 'Credit';
											}
										} 
										debug($credit_type);
										?>

										<table cellpadding="0" cellspacing="0" class="table">
											<thead>
												<tr>
													<td style="width:5%;" class="center">&nbsp;</td>
													<td style="width:3%;" class="center">#</td>
													<td style="width:15%" class="vcenter">Course Code</td>
													<td style="width:42%" class="vcenter">Course Title</td>
													<td style="width:10%;" class="center"><?= (!empty($credit_type) ? $credit_type : 'Credit'); ?></td>
													<td style="width:10%;" class="center">Grade</td>
													<td style="width:15%;" class="center">Grade Point</td>
												</tr>
											</thead>
											<tbody>
												<?php
												$count = 1;
												$c_count = 0;
												$credit_hour_sum = 0;
												$grade_point_sum = 0;
											
												foreach ($student_copy['courses'] as $key => $course_reg_add) {
													$c_count++;
													if (isset($course_reg_add['Grade']['grade'])) {
														if (isset($course_reg_add['Grade']['used_in_gpa']) && $course_reg_add['Grade']['used_in_gpa'] == 1) {
															$credit_hour_sum += $course_reg_add['Course']['credit'];
															$grade_point_sum += ($course_reg_add['Grade']['point_value'] * $course_reg_add['Course']['credit']);
														} else if (strcasecmp($course_reg_add['Grade']['grade'], 'I') == 0) {
															$credit_hour_sum += $course_reg_add['Course']['credit'];
														}
													} else {
														$credit_hour_sum += $course_reg_add['Course']['credit'];
													} ?>
													<tr <?= (isset($course_reg_add['Grade']['grade']) ? (!$course_reg_add['hasEquivalentMap'] && isset($student_copy['Student']['department_id']) ? ' class="rejected"' : (isset($course_reg_add['Grade']['grade']) && ((!$course_reg_add['Grade']['pass_grade'] && !$course_reg_add['firstTime']) || ($course_reg_add['Grade']['pass_grade'] && !$course_reg_add['firstTime'] && isset($course_reg_add['RepeatitionLabel']['repeated_new']) && $course_reg_add['RepeatitionLabel']['repeated_new'])) ? ' class="accepted"' : (isset($course_reg_add['Grade']['grade']) && !$course_reg_add['Grade']['pass_grade'] ? ' class="rejected"' : ''))) : ''); ?>>
														<td class="center" onclick="toggleView(this)" id="<?= $count; ?>"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $count, 'div' => false, 'align' => 'center')); ?></td>
														<td class="center"><?= $c_count; ?></td>
														<td class="vcenter"><?= $course_reg_add['Course']['course_code']; ?></td>
														<td class="vcenter"><?= $course_reg_add['Course']['course_title']; ?></td>
														<td class="center"><?= $course_reg_add['Course']['credit']; ?></td>
														<td class="center"><?= (isset($course_reg_add['Grade']['grade']) ? $course_reg_add['Grade']['grade'] : '---'); ?></td>
														<td class="center"><?= (isset($course_reg_add['Grade']['grade']) && isset($course_reg_add['Grade']['used_in_gpa']) && $course_reg_add['Grade']['used_in_gpa'] == 1 ? ($course_reg_add['Grade']['point_value'] * $course_reg_add['Course']['credit']) : '---'); ?></td>
													</tr>
													<?php
													//if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) { ?>
													<tr id="c<?= $count++; ?>" style="display:none">
														<td colspan="2" style="background-color: white;"> </td>
														<td colspan="5" style="background-color: white;">
															<table cellpadding="0" cellspacing="0" class="table">
																<tbody>
																	<tr>
																		<td style="background-color: white;" class="vcenter">From:<?= $course_reg_add['PublishedCourse']['add'] ? 'Mass Add (Date Mass Added: ' . (isset($course_reg_add['CourseAdd']) ? $this->Time->format("M j, Y h:i A", $course_reg_add['CourseAdd']['created'], NULL, NULL) : $this->Time->format("M j, Y h:i A", $course_reg_add['CourseRegistration']['created'], NULL, NULL)) . ' )' : ($course_reg_add['regAdd'] == 10 ? 'Course Registration (Date Registered: ' . ($this->Time->format("M j, Y h:i A", $course_reg_add['CourseRegistration']['created'], NULL, NULL)) . ' )' : 'Course Add (Date Added: ' . ($this->Time->format("M j, Y h:i A", $course_reg_add['CourseAdd']['created'], NULL, NULL)) . ' )') . (!$course_reg_add['firstTime'] ? '<span class="accepted" style="padding-left:20px;"> (Repeated Course) </span>' : ''); ?></td>
																	</tr>
																	<?= (isset($course_reg_add['PublishedCourse']['CourseInstructorAssignment']) && !empty($course_reg_add['PublishedCourse']['CourseInstructorAssignment']) ? '<tr><td style="background-color: white;" class="vcenter">Instructor: &nbsp;' . (isset($course_reg_add['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Title']['title']) ? $course_reg_add['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. ' : '') . $course_reg_add['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['full_name'] . (isset($course_reg_add['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Position']['position']) ? ' (' . $course_reg_add['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Position']['position'] . ')' : '') . '</td></tr>' : ''); ?>
														
																	<?php
																	if (isset($course_reg_add['Grade']) && !empty($course_reg_add['Grade'])) { ?>
																		<tr><td style="background-color: white;" class="vcenter">Grade: &nbsp; <?= (isset($course_reg_add['Grade']['grade']) ? $course_reg_add['Grade']['grade'] : '') . (isset($course_reg_add['Grade']['gradeChangeRequested']) && !empty($course_reg_add['Grade']['gradeChangeRequested']) ? '<span class="rejected" style="padding-left: 20px;"> (Grade Change)</span>' : ''); ?></td></tr>
																		<tr>
																			<td style="background-color: white;" class="vcenter">Pass Grade: &nbsp;
																				<?= (isset($course_reg_add['Grade']['pass_grade']) && $course_reg_add['Grade']['pass_grade'] ? '<Span class="accepted">Yes</Span>' : '<Span class="rejected">No</Span>'); ?>
																				<?= (isset($course_reg_add['Grade']['repeatable']) && $course_reg_add['Grade']['repeatable'] == 1 && $course_reg_add['firstTime'] == 1 && $course_reg_add['Grade']['pass_grade'] == 1 ? '<span class="accepted" style="padding-left:20px;">(This course can be repeated in case of result deficiency for graduation.)</span>' : ''); ?>
																			</td>
																		</tr>
																		<tr><td style="background-color: white;" class="vcenter">Grade Type: &nbsp; <?= (isset($course_reg_add['Grade']['grade_type']) ? $course_reg_add['Grade']['grade_type'] : ''); ?></td></tr>
																		<tr><td style="background-color: white;" class="vcenter">Grade Submitted: &nbsp; <?= (isset($course_reg_add['Grade']['submitted']) ? $this->Time->format("M j, Y h:i A", $course_reg_add['Grade']['submitted'], NULL, NULL) : '---'); ?></td></tr>
																		<tr><td style="background-color: white;" class="vcenter">Grade Approved: &nbsp; <?= (isset($course_reg_add['Grade']['approved']) ? $this->Time->format("M j, Y h:i A", $course_reg_add['Grade']['approved'], NULL, NULL) : '---'); ?></td></tr>
																		<?php
																	} ?>

																	<?php
																	if (isset($course_reg_add['ExamType']) && !empty($course_reg_add['ExamType']) /* && isset($course_reg_add['Grade']) && !empty($course_reg_add['Grade']['grade']) */) { ?>
																		<tr>
																			<td style="background-color: white;" class="vcenter"><b>Assessment Detail: </b>&nbsp;
																				<?php
																				if (!empty($course_reg_add['ExamType'])) {
																					$assessment_sum = 0;
																					$percent = 0;
																					$errorOverPercent = '';

																					foreach ($course_reg_add['ExamType'] as $k => $result) {
																						$percent += $result['ExamType']['percent'];
																						if ($percent <= 100) {
																							echo $result['ExamType']['exam_name'] . '(' . $result['ExamType']['percent'] . ')' . '= <b>' . $result['ExamResult']['result'] . '</b> &nbsp; ';
																							if (is_numeric($result['ExamResult']['result'])) {
																								$assessment_sum += $result['ExamResult']['result'];
																							}
																						} else if ($percent > 100) {
																							$errorOverPercent .= '  Exam Setup Error: > 100% (' . $result['ExamType']['exam_name'] . '(' . $result['ExamType']['percent'] . ')' . ' = <b>' . $result['ExamResult']['result'] . '</b> &nbsp; <br>';
																						}
																					}

																					if ($percent == 100) {
																						echo '&nbsp;<b>Total(' . $percent . '%)= ' . $assessment_sum . '</b>';
																					} else {
																						echo '&nbsp;<b>Total(' . $percent . '%)= ' . $assessment_sum . '</b>';
																						echo $errorOverPercent;
																					}

																				} else {
																					echo '&nbsp;<b>Assesment Data not available</b>';
																				} ?>
																			</td>
																		</tr>
																		<?php
																	} else if (!isset($course_reg_add['ExamType']) && isset($course_reg_add['Grade']['grade'])) { ?>
																		<tr><td style="background-color: white;" class="vcenter rejected">Course grade is submitted via Backdated Grade entry interface.</td></tr>
																		<?php
																	}

																	if (isset($course_reg_add['Grade']['gradeChangeRequested']) && !empty($course_reg_add['Grade']['gradeChangeRequested'])) { ?>
																		<?= (isset($course_reg_add['Grade']['gradeChangeReason']) && !empty($course_reg_add['Grade']['gradeChangeReason']) ? '<tr><td style="background-color: white;" class="vcenter">Grade Change Reason: &nbsp;' . $course_reg_add['Grade']['gradeChangeReason'] . '</td></tr>' : ''); ?>
																		<?= (isset($course_reg_add['Grade']['gradeChangeResult']) && !empty($course_reg_add['Grade']['gradeChangeResult']) ? '<tr><td style="background-color: white;" class="vcenter">Result from Grade Change: &nbsp;' . $course_reg_add['Grade']['gradeChangeResult'] . '</td></tr>' : ''); ?>
																		<?= (isset($course_reg_add['Grade']['makeupExamResult']) && !empty($course_reg_add['Grade']['makeupExamResult']) ? '<tr><td style="background-color: white;" class="vcenter">Makeup Exam Result: &nbsp;' . $course_reg_add['Grade']['makeupExamResult'] . '</td></tr>' : ''); ?>
																		<?= (isset($course_reg_add['Grade']['manualNGConversion']) && $course_reg_add['Grade']['manualNGConversion'] ? '<tr><td style="background-color: white;" class="vcenter">Manual NG Conversion By: &nbsp;' . (isset($course_reg_add['Grade']['manualNGConvertedBy']) ? $course_reg_add['Grade']['manualNGConvertedBy'] : '') . '</td></tr>' : ''); ?>
																		<?= (isset($course_reg_add['Grade']['autoNGConversion']) && $course_reg_add['Grade']['autoNGConversion'] ? '<tr><td style="background-color: white;" class="vcenter">Auto NG Conversion</td></tr>' : ''); ?>
																		<tr><td style="background-color: white;" class="vcenter">Grade Change Requested: &nbsp; <?= (isset($course_reg_add['Grade']['gradeChangeRequested']) ? $this->Time->format("M j, Y h:i A", $course_reg_add['Grade']['gradeChangeRequested'], NULL, NULL) : ''); ?></td></tr>
																		<tr><td style="background-color: white;" class="vcenter">Grade Change Approved: &nbsp; <?= (isset($course_reg_add['Grade']['gradeChangeApproved']) ? $this->Time->format("M j, Y h:i A", $course_reg_add['Grade']['gradeChangeApproved'], NULL, NULL) : ''); ?></td></tr>
																		<?php
																	}

																	if (!$course_reg_add['hasEquivalentMap'] && isset($student_copy['Curriculum']['name'])) { ?>
																		<tr><td style="background-color: white;" class="vcenter rejected">Course Equivalency is not set!! Currently, the Student is attached to "<?= $student_copy['Curriculum']['name'] ?>" but, this course is taken from " <?= $course_reg_add['Course']['Curriculum']['name']; ?>" curriculum. Thus, this course should be mapped to the equivalent course from student attached curriculum.</td></tr>
																		<?php
																	} ?>

																</tbody>
															</table>
														</td>
													</tr>
													<?php
												} ?>
												<tr>
													<td colspan="4" style="text-align:right; font-weight:bold">TOTAL</td>
													<td style="text-align:center; font-weight:bold"><?= ($credit_hour_sum != 0 ? $credit_hour_sum : '---'); ?></td>
													<td>&nbsp;</td>
													<td style="text-align:center; font-weight:bold"><?= ($grade_point_sum != 0 ? $grade_point_sum : '---'); ?></td>
												</tr>
											</tbody>
										</table>
										<br>

										<div class="row">
											<div class="large-4 columns" style="padding: 0.5rem;">
												<table cellpadding="0" cellspacing="0" class="table">
													<tr>
														<td colspan="2" style="font-weight:bold">Previous</td>
													</tr>
													<tr>
														<td style="width:50%"><?= (!empty($credit_type) ? $credit_type : 'Credit'); ?> Taken: </td>
														<td style="width:50%"><?= (isset($student_copy['PreviousStudentExamStatus']['previous_credit_hour_sum']) ? $student_copy['PreviousStudentExamStatus']['previous_credit_hour_sum'] : '---'); ?></td>
													</tr>
													<tr>
														<td>GP Earned: </td>
														<td><?= (isset($student_copy['PreviousStudentExamStatus']['previous_grade_point_sum']) ? $student_copy['PreviousStudentExamStatus']['previous_grade_point_sum'] : '---'); ?></td>
													</tr>
													<tr>
														<td>SGPA: </td>
														<td><?= (isset($student_copy['PreviousStudentExamStatus']['sgpa']) ? $student_copy['PreviousStudentExamStatus']['sgpa'] : '---'); ?></td>
													</tr>
													<tr>
														<td>CGPA:</td>
														<td><b><?= (isset($student_copy['PreviousStudentExamStatus']['cgpa']) ? $student_copy['PreviousStudentExamStatus']['cgpa'] : '---'); ?></b></td>
													</tr>
													<tr>
														<td>Status:</td>
														<td <?= (isset($student_copy['PreviousStudentExamStatus']['academic_status_id']) ? ($student_copy['PreviousStudentExamStatus']['academic_status_id'] == 4 ? ' class="rejected"' : ($student_copy['PreviousStudentExamStatus']['academic_status_id'] == 3 ? ' class="on-process"' : ' class="accepted"')) : ''); ?>>
															<?php echo (isset($student_copy['PreviousAcademicStatus']['name']) ? $student_copy['PreviousAcademicStatus']['name'] : '---'); ?>
														</td>
													</tr>
												</table>
											</div>

											<div class="large-4 columns" style="padding: 0.5rem;">
												<table cellpadding="0" cellspacing="0" class="table">
													<tr>
														<td colspan="2" style="font-weight:bold">This Semester</td>
													</tr>
													<tr>
														<td style="width:50%"><?= (!empty($credit_type) ? $credit_type : 'Credit'); ?> Taken: </td>
														<td style="width:50%"><?= ($credit_hour_sum != 0 ? $credit_hour_sum : '---'); ?></td>
													</tr>
													<tr>
														<td>GP Earned: </td>
														<td><?= ($grade_point_sum != 0 ? $grade_point_sum : '---'); ?></td>
													</tr>
													<tr>
														<td>SGPA: </td>
														<td><?= (isset($student_copy['StudentExamStatus']['sgpa']) ? $student_copy['StudentExamStatus']['sgpa'] : '---'); ?></td>
													</tr>
													<tr>
														<td>CGPA:</td>
														<td><b><?= (isset($student_copy['StudentExamStatus']['cgpa']) ? $student_copy['StudentExamStatus']['cgpa'] : '---'); ?></b></td>
													</tr>
													<tr>
														<td>Status:</td>
														<td <?= (isset($student_copy['StudentExamStatus']['academic_status_id']) ? ($student_copy['StudentExamStatus']['academic_status_id'] == 4 ? ' class="rejected"' : ($student_copy['StudentExamStatus']['academic_status_id'] == 3 ? ' class="on-process"' : ' class="accepted"')) : ''); ?>>
															<?php echo (isset($student_copy['AcademicStatus']['name']) ? $student_copy['AcademicStatus']['name'] : '---'); ?>
														</td>
													</tr>
												</table>
											</div>

											<div class="large-4 columns" style="padding: 0.5rem;">
												<table cellpadding="0" cellspacing="0" class="table">
													<tr>
														<td colspan="2" style="font-weight:bold">Cumulative Academic Status</td>
													</tr>
													<tr>
														<td style="width:60%">Total<?= (!empty($credit_type) ? $credit_type : 'Credit'); ?> Taken: </td>
														<td style="width:40%">
															<?php
															if ($credit_hour_sum != 0 && isset($student_copy['PreviousStudentExamStatus']['previous_credit_hour_sum'])) {
																echo $student_copy['PreviousStudentExamStatus']['previous_credit_hour_sum'] + $credit_hour_sum;
															} else if ($credit_hour_sum != 0) {
																echo $credit_hour_sum;
															} else {
																echo '---';
															} ?>
														</td>
													</tr>
													<tr>
														<td>Total GP Earned: </td>
														<td>
															<?php
															if ($grade_point_sum != 0 && isset($student_copy['PreviousStudentExamStatus']['previous_grade_point_sum'])) {
																echo $student_copy['PreviousStudentExamStatus']['previous_grade_point_sum'] + $grade_point_sum;
															} else if ($grade_point_sum != 0) {
																echo $grade_point_sum;
															} else {
																echo '---';
															} ?>
														</td>
													</tr>
												</table>
											</div>
										</div>
									</fieldset>
								</div>
								<?php
							} else { ?>
								<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is neither course registration nor course add for the selected academic year and semester. Please select another academic year and/or semester.</div>
								<?php
							}
						}
					} else { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>You do not have any course registration. Please use this link after course registration to view your exam result and grade.</div>
						<?php
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
</script>