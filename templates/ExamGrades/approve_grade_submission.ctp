<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Exam Grade Approval'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('ExamGrade', array('onSubmit' => 'return checkForm(this);')); ?>

				<?php 
				if (!isset($get_list_of_students_with_grade) && !isset($grade_submitted_courses_rejected_organized_by_published_course)) { ?>
					<div style="margin-top: -30px;">
						<hr>
						<?php
						if (empty($turn_off_search)) { ?>
							<blockquote>
								<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
								<span style="text-align:justify;" class="fs14 text-gray">This tool will help you to approve grade which was submitted by instructors <b style="text-decoration: underline;"><i>Only those Grades which were not approved previously will get approved</i></b>. You can also leave Program, Program Type and Year Level filters unchecked or empty to get all grade submissions for the selected academic year.</span>
							</blockquote>
							<?php
						} ?>

						<hr>

						<div onclick="toggleViewFullId('ListPublishedCourse')">
							<?php
							if (!empty($turn_off_search)) {
								echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
								<?php
							} else {
								echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
								<?php
							} ?>
						</div>

						<div id="ListPublishedCourse" style="display:<?= (!empty($turn_off_search) ? 'none' : 'display'); ?>">
							<fieldset style="padding-bottom: 0px; padding-top: 20px;">
								<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
								<div class="row">
									<div class="large-2 columns">
										<?= $this->Form->input('Search.academicyear', array('label' => 'Acadamic Year: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $acyear_array_data, 'empty' => "[ Select ]", 'default' => (isset($this->request->data['Search']['academicyear']) ? $this->request->data['Search']['academicyear'] : $defaultacademicyear))); ?>
									</div>
									<div class="large-2 columns">
										<?= $this->Form->input('Search.semester', array('options' => Configure::read('semesters'), 'type' => 'select', 'style' => 'width:90%', 'label' => 'Semester: ', 'empty' => 'Any Semester')); ?>
									</div>
									<div class="large-3 columns">
										<h6 class="fs13 text-gray"><b>Program: </b></h6>
										<?= $this->Form->input('Search.program_id', array('id' => 'program_id', 'label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'div' => false)); ?>
									</div>
									<div class="large-3 columns">
										<h6 class="fs13 text-gray"><b>Program Type: </b></h6>
										<?= $this->Form->input('Search.program_type_id', array('id' => 'program_type_id', 'label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'div' => false)); ?>
									</div>
									<?php
									if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) { ?>
										<div class="large-2 columns">
											<h6 class="fs13 text-gray"><b>Year Level: </b></h6>
											<?= $this->Form->input('Search.year_level_id', array('id' => 'year_level_id', 'label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'div' => false)); ?>
										</div>
										<?php
									} else { ?>
										<div class="large-2 columns">
											<h6 class="fs13 text-gray"><b>Year Level: </b></h6>
											<input type="checkbox" name="data[Search][year_level_id][]" value="0" id="year_level_id1st" disabled="disabled">
										</div>
										<?php
									}  ?>
								</div>
								<hr>
								<?= $this->Form->submit(__('Search'), array('name' => 'getCourseNeedsApproval',  'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
							</fieldset>
						</div>
					</div>
					<hr>
					<?php
				} ?>

				<div class="publishedCourses index">
					<?php
					if (!isset($hide_approve_list) && isset($grade_submitted_courses_organized_by_published_course) && !empty($grade_submitted_courses_organized_by_published_course)) { ?>
						<hr>
						<h6 class="fs14 text-gray">List of Exam Grades submitted by instructors for <?= $this->request->data['Search']['academicyear']; ?> academic year <?= (empty($this->request->data['Search']['semester']) ? '' : ('' . ($this->request->data['Search']['semester'] == 'I' ? '1st semester' : ($this->request->data['Search']['semester'] == 'II' ? '2nd semester' : ($this->request->data['Search']['semester'] == 'III' ? '3rd semester' : $this->request->data['Search']['semester'] . ' semester'))))); ?>  and awaiting your approval.</h6>
						<hr>
						<?php
						if (isset($grade_submitted_courses_organized_by_published_course) && !empty($grade_submitted_courses_organized_by_published_course)) {
							foreach ($grade_submitted_courses_organized_by_published_course as $dep => $depvalue) {
								foreach ($depvalue as $pk => $pv) {
									if (!empty($pk)) {
										//echo "<span class='fs14'><strong class='text-gray'>Program: </strong><b>" . $pk . "</b></span><br>";
										foreach ($pv as $ptk => $ptv) {
											if (!empty($ptk)) {
												//echo "<span class='fs14'><strong class='text-gray'>Program Type: </strong><b>" . $ptk . "</b></span><br>";
												foreach ($ptv as $yk => $yv) {
													if (!empty($yv)) {
														//echo "<span class='fs14'><strong class='text-gray'>Year Level: </strong><b>" . $yk . "</b></span><br>";
														foreach ($yv as $section_name => $section_value) {
															//echo "<span class='fs14'><strong class='text-gray'>Section: </strong><b>" . $section_name . "</b></span><br>"; ?>
															<br>
															<?php // debug($section_value); ?>
															<div style="overflow-x:auto;">
																<table cellpadding="0" cellspacing="0" class="table">
																	<thead>
																		<tr>
																			<td colspan="7" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
																				<span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?= $section_name . ' ' . (isset($yk)  ?  ' (' . $yk . ')' : ' (Pre/1st)'); ?></span>
																					<br>
																					<span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
																						<?= (is_numeric($dep) && $dep > 0 ? $departmentsss[$dep] : (count(explode('c~', $dep)) > 1 ? $collegesss[explode('c~', $dep)[1]] . ' - Pre/1st' : '' )) . ' &nbsp; | &nbsp; ' .  $pk . ' &nbsp; | &nbsp; ' . $ptk  ?> <br>
																						<?= $this->request->data['Search']['academicyear'] . (empty($this->request->data['Search']['semester']) ? '' : (' &nbsp; | &nbsp; ' . ($this->request->data['Search']['semester'] == 'I' ? '1st Semester' : ($this->request->data['Search']['semester'] == 'II' ? '2nd Semester' : ($this->request->data['Search']['semester'] == 'III' ? '3rd Semester' : $this->request->data['Search']['semester'] . ' Semester'))))); ?>
																					</span>
																				</span>
																			</td>
																		</tr>
																		<tr>
																			<th class="center">#</th>
																			<th class="vcenter">Course Title</th>
																			<th class="center">Course Code</th>
																			<th class="center"><?= (count(explode('ECTS', array_values($section_value)[0]['Course']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit'); ?></th>
																			<th class="center">L T L</th>
																			<th class="center">Instructor</th>
																			<th class="center">Actions</th>
																		</tr>
																	</thead>
																	<tbody>
																		<?php
																		$sn_count = 1;
																		foreach ($section_value as $pub_id => $publishedCourse) {
																			if (!empty($publishedCourse)) { ?>
																				<tr>
																					<td class="center"><?= $sn_count++; ?></td>
																					<td class="vcenter"><?= $this->Html->link($publishedCourse['Course']['course_title'], array('controller' => 'courses', 'action' => 'view', $publishedCourse['Course']['id'])); ?></td>
																					<td class="center"><?= $publishedCourse['Course']['course_code']; ?></td>
																					<td class="center"><?= $publishedCourse['Course']['credit']; ?></td>
																					<td class="center"><?= $publishedCourse['Course']['course_detail_hours']; ?></td>
																					<td class="center">
																						<?php
																						if (isset($publishedCourse['CourseInstructorAssignment']) && count($publishedCourse['CourseInstructorAssignment']) > 0) {
																							echo $publishedCourse['CourseInstructorAssignment'][0]['Staff']['Title']['title']. '. '. $publishedCourse['CourseInstructorAssignment'][0]['Staff']['full_name'];
																						} ?>
																					</td>
																					<td class="center">
																						<?= $this->Html->link(__('Review & Approve'), array('action' => ($department == 1 ? 'approve_non_freshman_grade_submission' : 'approve_freshman_grade_submission'), $publishedCourse['PublishedCourse']['id'])); ?>
																					</td>
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
												}
											}
										}
									}
								}
							}
						}
					}

					//Rejected grades from registrar_approval
					if (!isset($hide_approve_list) && isset($grade_submitted_courses_rejected_organized_by_published_course) && !empty($grade_submitted_courses_rejected_organized_by_published_course)) { ?>
						<h6 class="fs15 text-gray">List of exam grades which are rejected by registrar for <?= $this->request->data['Search']['academicyear']; ?> ACY <?= (isset($this->request->data['Search']['semeter']) ? ' Semester: '. $this->request->data['Search']['semeter'] : ''); ?>  and awaiting your approval.</h6>
						<br>
						<?php
						//debug($grade_submitted_courses_rejected_organized_by_published_course);
						if (isset($grade_submitted_courses_rejected_organized_by_published_course) && !empty($grade_submitted_courses_rejected_organized_by_published_course)) {
							//debug($grade_submitted_courses_rejected_organized_by_published_course);
							foreach ($grade_submitted_courses_rejected_organized_by_published_course as $pk => $pv) {
								if (!empty($pk)) {
									echo "<span class='fs14'><strong class='text-gray'>Program: </strong><b>" . $pk . "</b></span><br>";
									foreach ($pv as $ptk => $ptv) {
										if (!empty($ptk)) {
											echo "<span class='fs14'><strong class='text-gray'>Program Type: </strong><b>" . $ptk . "</b></span><br>";
											foreach ($ptv as $yk => $yv) {
												if (!empty($yv)) {
													echo "<span class='fs14'><strong class='text-gray'>Year Level: </strong><b>" . $yk . "</b></span><br>";
													foreach ($yv as $section_name => $section_value) {
														echo "<span class='fs14'><strong class='text-gray'>Section: </strong><b>" . $section_name . "</b></span><br>"; ?>
														<br>
														<div style="overflow-x:auto;">
															<table cellpadding="0" cellspacing="0" class="table">
																<thead>
																	<tr>
																		<td class="center">#</td>
																		<td class="vcenter">Course Title</td>
																		<td class="center">Course Code</th>
																		<td class="center"><?= (count(explode('ECTS', array_values($section_value)[0]['Course']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit'); ?></th>
																		<td class="center">L T L</td>
																		<td class="vcenter">Instructor</td>
																		<td class="center"></td>
																	</tr>
																</thead>
																<tbody>
																	<?php
																	$sn_count = 1;
																	foreach ($section_value as $pub_id => $publishedCourse) {
																		if (!empty($publishedCourse)) { ?>
																			<tr>
																				<td class="center"><?= $sn_count++; ?>&nbsp;</td>
																				<td class="center"><?= $this->Html->link($publishedCourse['Course']['course_title'], array('controller' => 'courses', 'action' => 'view', $publishedCourse['Course']['id'])); ?></td>
																				<td class="center"><?= $publishedCourse['Course']['course_code']; ?></td>
																				<td class="center"><?= $publishedCourse['Course']['credit']; ?></td>
																				<td class="center"><?= $publishedCourse['Course']['course_detail_hours']; ?></td>
																				<td class="center">
																					<?php
																					if (isset($publishedCourse['CourseInstructorAssignment']) && count($publishedCourse['CourseInstructorAssignment']) > 0) {
																						echo  $publishedCourse['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. ' . $publishedCourse['CourseInstructorAssignment'][0]['Staff']['full_name'];
																					} ?>
																				</td>
																				<td  class="center">
																					<?= $this->Html->link(__('Review & Approve'), array('action' => ($department == 1 ? 'approve_non_freshman_grade_submission' : 'approve_freshman_grade_submission'), $publishedCourse['PublishedCourse']['id'])); ?>
																				</td>
																			</tr>
																			<?php
																		}
																	} ?>
																</tbody>
															</table>
														</div>
														<?php
													}
												}
											}
										}
									}
								}
							}
						}
					}

					if (isset($get_list_of_students_with_grade) && !empty($get_list_of_students_with_grade)) {
						if (count($get_list_of_students_with_grade) > 0) {
							//debug($get_list_of_students_with_grade);
							// $this->set(compact('get_list_of_students_with_grade', 'hide_approve_list', 'search_published_course', 'gradeScaleDetail', 'instructorDetail', 'publishedCourseDetail'));

							if (isset($publishedCourseDetail['Department']['name'])) {
								$freshman_program = false;
								$approver = 'department';
								$approver_c = 'Department';
							} else {
								$freshman_program = true;
								$approver = 'freshman program';
								$approver_c = 'Freshman Program';
							} ?>

							<div class="fs14">Grade submitted by <u><b class="text-black"><?= (!empty($instructorDetail['Staff']) ? $instructorDetail['Staff']['Title']['title']  . '. '. $instructorDetail['Staff']['first_name'] . ' ' . $instructorDetail['Staff']['middle_name'] . ' ' . $instructorDetail['Staff']['last_name']  . ' ('. $instructorDetail['Staff']['Position']['position']. ')' : (isset($publishedCourseDetail['Department']['name']) ? 'the department' : 'the freshman program (college)')); ?></b></u> for <u><b class="text-black"><?= $publishedCourseDetail['Course']['course_title'] . ' (' . $publishedCourseDetail['Course']['course_code'] . ')'; ?></b></u> course and waiting for your decesion. If the result is accepted, it will be forwarded to the registrar for final confirmation.</div>
							<br />

							<?php //debug($publishedCourseDetail['Course']); ?>

							<div style="overflow-x:auto;">
								<table cellpadding="0" cellspacing="0" class="table">
									<tr>
										<td style="width:50%;"><strong class="text-gray"><?= (isset($publishedCourseDetail['Department']['type']) && !empty($publishedCourseDetail['Department']['type']) ? $publishedCourseDetail['Department']['type'].': ' : 'Department: '); ?></strong> <?= (isset($publishedCourseDetail['Department']['name']) ? $publishedCourseDetail['Department']['name'] : 'Freshman'); ?></td>
										<td style="width:50%;"><strong class="text-gray">Program: </strong> <?= $publishedCourseDetail['Program']['name'] . ' / ' . $publishedCourseDetail['ProgramType']['name']; ?></td>
									</tr>
									<tr>
										<td><strong class="text-gray">Course: </strong> <?= $publishedCourseDetail['Course']['course_title'] . ' (' . $publishedCourseDetail['Course']['course_code'] . ')'; ?></td>
										<td><strong class="text-gray"><?= (count(explode('ECTS', $publishedCourseDetail['Course']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit'); ?>: </strong> <?= $publishedCourseDetail['Course']['credit']; ?></td>
									</tr>
									<tr>
										<td><strong class="text-gray">Academic Year: </strong> <?= $publishedCourseDetail['PublishedCourse']['academic_year']; ?></td>
										<td><strong class="text-gray">Semester: </strong> <?= $publishedCourseDetail['PublishedCourse']['semester']; ?></td>
									</tr>
									<tr>
										<td><strong class="text-gray">Section: </strong> <?= $publishedCourseDetail['Section']['name']; ?></td>
										<td><strong class="text-gray">Instructor: </strong> <?= $instructorDetail['Staff']['Title']['title']  . '. '. $instructorDetail['Staff']['first_name'] . ' ' . $instructorDetail['Staff']['middle_name'] . ' ' . $instructorDetail['Staff']['last_name']; ?></td>
									</tr>
									<tr>
										<td colspan="2"><strong class="text-gray">Year Level: </strong> <?= (isset($publishedCourseDetail['YearLevel']['name']) ? $publishedCourseDetail['YearLevel']['name'] : '1st'); ?></td>
									</tr>
								</table>
							</div>

							<hr>

							<div style="overflow-x:auto;">
								<table class="table" cellpadding="0" cellspacing="0">
									<thead>
										<tr>
											<td style="width:2%" class="center">&nbsp;</th>
											<td style="width:18%" class="vcenter">Student Name</td>
											<td style="width:10%" class="vcenter">Student ID</td>
											<?php

											$percent = 10;
											$last_percent = "";

											if (((100 - 30) / count($exam_types) + 3) > 10) {
												$last_percent = (100 - 30) - ((count($exam_types) + 3) * 10);
											} else {
												$percent = ((100 - 30) / (count($exam_types) + 3));
											}

											$count_for_percent = 0;

											if (!empty($exam_types)) {
												foreach ($exam_types as $key => $exam_type) {
													$count_for_percent++; ?>
													<td class="center" style="width:<?= $percent ?>%">
														<?= $exam_type['ExamType']['exam_name'] . ' (' . $exam_type['ExamType']['percent'] . '%)'; ?>
													</td>
													<?php
												} 
											} ?>

											<td class="center" style="width:<?= $percent; ?>%">Total (100%)</th>
											<td class="center" style="width:<?= $percent; ?>%">Grade</td>
											<td class="center" style="width:<?= $last_percent != "" ? $last_percent + $percent : $percent; ?>%"> Status</td>
										</tr>
									</thead>
									<tbody>
										<?php

										$enable_approve_button = 0;
										$department_rejected_grades = 0;
										$department_rejected_grades_back_to_instructor = 0;
										$department_rejected_grades_back_to_registrar = 0;
										$registrar_regected_grades = 0;

										$consequetive_rejected_grades_by_the_registrar = 0;

										$count = 1;
										$frequency_count = array();
										
										$st_count = 0; 
										
										//Course Registration

										if (isset($get_list_of_students_with_grade['register']) && !empty($get_list_of_students_with_grade['register'])) {
											foreach ($get_list_of_students_with_grade['register'] as $key => $student) {
												$st_count++; ?>
												<tr <?php if (isset($student['ExamGrade']) && !empty($student['ExamGrade']) && ($student['ExamGrade'][0]['department_approval'] == null || $student['ExamGrade'][0]['registrar_approval'] == -1)) echo ' style="font-weight:bold"'; ?>>
													<td class="center" onclick="toggleView(this)" id="<?= $st_count; ?>"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $st_count)); ?></td>
													<td class="vcenter"><?= $student['Student']['first_name'] . ' ' . $student['Student']['middle_name'] . ' ' . $student['Student']['last_name']; ?></td>
													<td class="vcenter"><?= $student['Student']['studentnumber']; ?></td>
													<?php

													$total_100 = "";

													if (!empty($exam_types)) {
														foreach ($exam_types as $key => $exam_type) { ?>
															<td class="center">
																<?php
																$id = "";
																$value = "";

																if (isset($student['ExamResult']) && !empty($student['ExamResult'])) {
																	foreach ($student['ExamResult'] as $key => $examResult) {
																		if ($examResult['exam_type_id'] == $exam_type['ExamType']['id']) {
																			$id = $examResult['id'];
																			$value = $examResult['result'];
																			break;
																		}
																	}
																}

																if ($id != "") {
																	echo $value;
																	$total_100 += $value;
																} else {
																	echo '--';
																} ?>
															</td>
															<?php
														} 
													} ?>

													<td class="center"><?= ($total_100 === "" ? '---' : $total_100); ?></td>
													<td class="center">
														<?php
														if (isset($student['ExamGrade']) && !empty($student['ExamGrade'])) {
															$frequency_count[] = $student['ExamGrade'][0]['grade'];
															echo $student['ExamGrade'][0]['grade'];
															if ($student['ExamGrade'][0]['department_approval'] == null || $student['ExamGrade'][0]['registrar_approval'] == -1) {
																echo $this->Form->hidden('ExamGrade.' . $count . '.id', array('value' => $student['ExamGrade'][0]['id']));
																$enable_approve_button++;
															} else if ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == -1 && is_null($student['ExamGrade'][0]['registrar_approval'])) {
																$department_rejected_grades_back_to_registrar++;
															} else if ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == -1 && $student['ExamGrade'][0]['registrar_approval'] == -1) {
																$consequetive_rejected_grades_by_the_registrar++;
															} else if (($student['ExamGrade'][0]['department_approval'] == -1 && is_null($student['ExamGrade'][0]['registrar_approval'])) || ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == 1) || ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == 1 && $student['ExamGrade'][0]['registrar_approval'] == -1)) {
																$department_rejected_grades_back_to_instructor++;
																if ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == 1 && $student['ExamGrade'][0]['registrar_approval'] == -1) {
																	$registrar_regected_grades++;
																}
															} 
														} else {
															echo '**';
														} ?>
													</td>
													<td class="center">
														<?php
														//Status of grade submision
														if (!isset($student['ExamGrade']) || empty($student['ExamGrade'])) {
															echo '<span class="text-gray" style="font-weight:normal;"><i>Waiting for Grade Submission</i></span>';
														} else if ($student['ExamGrade']['0']['registrar_approval'] == 1) {
															echo '<span class="accepted" style="font-weight:normal;">Accepted</span>';
														} else if ($student['ExamGrade']['0']['department_approval'] == null) {
															echo '<span class="on-process" style="font-weight:normal;">Pending for ' . $approver . ' approval</span>';
														} else if ($student['ExamGrade']['0']['department_approval'] == -1) {
															if ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == -1 && is_null($student['ExamGrade'][0]['registrar_approval'])) {
																echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for registrar\'s response for ' . $approver . '\'s rejection of previously rejected grade by the registrar.</span>', 50 , array('ellipsis' => '...', 'exact' => true, 'html' => true));
															} else if ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == -1 && $student['ExamGrade'][0]['registrar_approval'] == -1) {
																echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for ' . $approver . '\'s response for registrar\'s consequetive rejections(two or more times).</span>', 50 , array('ellipsis' => '...', 'exact' => true, 'html' => true));
															} else if (($student['ExamGrade'][0]['department_approval'] == -1 && is_null($student['ExamGrade'][0]['registrar_approval'])) || ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == 1) || ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == 1 && $student['ExamGrade'][0]['registrar_approval'] == -1)) {
																if ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == 1 && $student['ExamGrade'][0]['registrar_approval'] == -1) {
																	echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for Instructor grade re-submission in response for ' . $approver . '\'s acceptance of previously rejected grade by the registrar.</span>', 50 , array('ellipsis' => '...', 'exact' => true, 'html' => true));
																} else {
																	echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for Instructor grade re-submission in response for '.  $approver . '\'s rejection of previously submitted grade by the instructor.</span>', 50 , array('ellipsis' => '...', 'exact' => true, 'html' => true));
																}
															} else {
																echo '<span class="rejected" style="font-weight:normal;">Grade is rejected by the ' . $approver . '</span>';
															}
														} else {
															if ($student['ExamGrade']['0']['registrar_approval'] == null) {
																echo '<span class="on-process" style="font-weight:normal;">Approved by ' . $approver . ', pending for registrar confirmation</span>';
															} else if ($student['ExamGrade']['0']['registrar_approval'] == 1) {
																echo '<span class="accepted" style="font-weight:normal;">Accepted</span>';
															} else if ($student['ExamGrade']['0']['registrar_approval'] == -1) {
																echo '<span class="rejected" style="font-weight:normal;">Approved by ' . $approver . ', but rejected by registrar</span>';
															} else {
																echo '<span class="accepted" style="font-weight:normal;">Accepted</span>';
															}
														} ?>
													</td>
												</tr>

												<tr id="c<?= $st_count; ?>" style="display:none">
													<td style="background-color: white;"></td>
													<td style="background-color: white;" colspan="<?= (5 + count($exam_types)); ?>">
														
														<?php
														if (isset($student['MakeupExam']) && isset($student['ExamGradeChange']) && count($student['ExamGradeChange']) > 0) { ?>
															<table cellpadding="0" cellspacing="0" class="table">
																<tr>
																	<td style="width:28%; font-weight:bold; background-color: white;">Makeup Exam Minute Number:</td>
																	<td style="width:72%; background-color: white;"><?= $student['ExamGradeChange'][0]['minute_number']; ?></td>
																</tr>
															</table>
															<?php
														}

														$register_or_add = 'gh';

														if (isset($student['ExamGradeHistory'])) {
															$grade_history = $student['ExamGradeHistory'];
														} else {
															$grade_history = array();
														}

														$this->set(compact('register_or_add', 'grade_history', 'freshman_program')); ?>

														<table cellpadding="0" cellspacing="0" class="table">
															<tr>
																<td style="vertical-align:top; background-color: white;"><?= $this->element('registered_or_add_course_grade_history'); ?></td>
																<!-- <td style="vertical-align:top; width:40%">&nbsp;</td> -->
															</tr>
														</table>

														<?php
														$student_exam_grade_change_history = $student['ExamGradeHistory'];
														$student_exam_grade_history = $student['ExamGrade'];
														$this->set(compact('student_exam_grade_history', 'student_exam_grade_change_history', 'freshman_program'));
														echo $this->element('registered_or_add_course_grade_detail_history'); ?>
													</td>
												</tr> 
												<?php
												$count++;
											}
										}

										// Course Add
										if (isset($get_list_of_students_with_grade['add']) && !empty($get_list_of_students_with_grade['add'])) {
											foreach ($get_list_of_students_with_grade['add'] as $key => $student) {
												$st_count++; ?>
												<tr <?php if (isset($student['ExamGrade']) && !empty($student['ExamGrade']) && ($student['ExamGrade'][0]['department_approval'] == null || $student['ExamGrade'][0]['registrar_approval'] == -1)) echo ' style="font-weight:bold"'; ?>>
													<td class="center" onclick="toggleView(this)" id="<?= $st_count; ?>"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $st_count)); ?></td>
													<td class="vcenter"><?= $student['Student']['first_name'] . ' ' . $student['Student']['middle_name'] . ' ' . $student['Student']['last_name']; ?></td>
													<td class="vcenter"><?= $student['Student']['studentnumber']; ?></td>

													<?php
													$total_100 = "";

													if (!empty($exam_types)) {
														foreach ($exam_types as $key => $exam_type) { ?>
															<td class="center">
																<?php
																$id = "";
																$value = "";

																if (isset($student['ExamResult']) && !empty($student['ExamResult'])) {
																	foreach ($student['ExamResult'] as $key => $examResult) {
																		if ($examResult['exam_type_id'] == $exam_type['ExamType']['id']) {
																			$id = $examResult['id'];
																			$value = $examResult['result'];
																			break;
																		}
																	}
																}

																if ($id != "") {
																	echo $value;
																	$total_100 += $value;
																} else {
																	echo '--';
																} ?>
															</td>
															<?php
														}  
													} ?>

													<td class="center"><?= ($total_100 === "" ? '---' : $total_100); ?></td>
													<td class="center">
														<?php
														if (isset($student['ExamGrade']) && !empty($student['ExamGrade'])) {
															$frequency_count[] = $student['ExamGrade'][0]['grade'];
															echo $student['ExamGrade'][0]['grade'];
															if (is_null($student['ExamGrade'][0]['department_approval']) || $student['ExamGrade'][0]['registrar_approval'] == -1) {
																echo $this->Form->hidden('ExamGrade.' . $count . '.id', array('value' => $student['ExamGrade'][0]['id']));
																$enable_approve_button++;
															} else if ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == -1 && is_null($student['ExamGrade'][0]['registrar_approval'])) {
																$department_rejected_grades_back_to_registrar++;
															} else if ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == -1 && $student['ExamGrade'][0]['registrar_approval'] == -1) {
																$consequetive_rejected_grades_by_the_registrar++;
															} else if (($student['ExamGrade'][0]['department_approval'] == -1 && is_null($student['ExamGrade'][0]['registrar_approval'])) || ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == 1) || ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == 1 && $student['ExamGrade'][0]['registrar_approval'] == -1)) {
																$department_rejected_grades_back_to_instructor++;
																if ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == 1 && $student['ExamGrade'][0]['registrar_approval'] == -1) {
																	$registrar_regected_grades++;
																}
															} 
														} else {
															echo '**';
														} ?>
													</td>
													<td class="center">
														<?php
														//Status of grade submision
														if (!isset($student['ExamGrade']) || empty($student['ExamGrade'])) {
															echo '<span class="text-gray" style="font-weight:normal;"><i>Waiting for Grade Submission</i></span>';
														} else if ($student['ExamGrade']['0']['registrar_approval'] == 1) {
															echo '<span class="accepted" style="font-weight:normal;">Accepted</span>';
														} else if ($student['ExamGrade']['0']['department_approval'] == null) {
															echo '<span class="on-process" style="font-weight:normal;">Pending for ' . $approver . ' approval</span>';
														} else if ($student['ExamGrade']['0']['department_approval'] == -1) {
															if ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == -1 && is_null($student['ExamGrade'][0]['registrar_approval'])) {
																echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for registrar\'s response for ' . $approver . '\'s rejection of previously rejected grade by the registrar.</span>', 50 , array('ellipsis' => '...', 'exact' => true, 'html' => true));
															} else if ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == -1 && $student['ExamGrade'][0]['registrar_approval'] == -1) {
																echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for ' . $approver . '\'s response for registrar\'s consequetive rejections(two or more times).</span>', 50 , array('ellipsis' => '...', 'exact' => true, 'html' => true));
															} else if (($student['ExamGrade'][0]['department_approval'] == -1 && is_null($student['ExamGrade'][0]['registrar_approval'])) || ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == 1) || ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == 1 && $student['ExamGrade'][0]['registrar_approval'] == -1)) {
																if ($student['ExamGrade'][0]['department_reply'] == 1 && $student['ExamGrade'][0]['department_approval'] == 1 && $student['ExamGrade'][0]['registrar_approval'] == -1) {
																	echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for Instructor grade re-submission in response for ' . $approver . '\'s acceptance of previously rejected grade by the registrar.</span>', 50 , array('ellipsis' => '...', 'exact' => true, 'html' => true));
																} else {
																	echo $this->Text->truncate('<span class="on-process" style="font-weight:normal;">Waiting for Instructor grade re-submission in response for '.  $approver . '\'s rejection of previously submitted grade by the instructor.</span>', 50 , array('ellipsis' => '...', 'exact' => true, 'html' => true));
																}
															} else {
																echo '<span class="rejected" style="font-weight:normal;">Grade is rejected by the ' . $approver . '</span>';
															}
														} else {
															if ($student['ExamGrade']['0']['registrar_approval'] == null) {
																echo '<span class="on-process" style="font-weight:normal;">Approved by ' . $approver . ', pending for registrar confirmation</span>';
															} else if ($student['ExamGrade']['0']['registrar_approval'] == 1) {
																echo '<span class="accepted" style="font-weight:normal;">Accepted</span>';
															} else if ($student['ExamGrade']['0']['registrar_approval'] == -1) {
																echo '<span class="rejected" style="font-weight:normal;">Approved by ' . $approver . ', but rejected by registrar</span>';
															} else {
																echo '<span class="accepted" style="font-weight:normal;">Accepted</span>';
															}
														} ?>
													</td>
												</tr>

												<tr id="c<?= $st_count; ?>" style="display:none">
													<td style="background-color: white;"></td>
													<td style="background-color: white;" colspan="<?= (5 + count($exam_types)); ?>">
														<?php
														if (isset($student['MakeupExam']) && isset($student['ExamGradeChange']) && count($student['ExamGradeChange']) > 0) { ?>
															<table>
																<tr>
																	<td style="width:28%; font-weight:bold; background-color: white;">Makeup Exam Minute Number:</td>
																	<td style="width:72%; background-color: white;"><?= $student['ExamGradeChange'][0]['minute_number']; ?></td>
																</tr>
															</table>
															<?php
														}

														$register_or_add = 'gh';

														if (isset($student['ExamGradeHistory'])) {
															$grade_history = $student['ExamGradeHistory'];
														} else {
															$grade_history = array();
														}

														$this->set(compact('register_or_add', 'grade_history', 'freshman_program')); ?>

														<table>
															<tr>
																<td style="vertical-align:top; background-color: white; width:40%"><?= $this->element('registered_or_add_course_grade_history'); ?></td>
																<td style="vertical-align:top; background-color: white; width:60%">&nbsp;</td>
															</tr>
														</table>

														<?php
														$student_exam_grade_change_history = $student['ExamGradeHistory'];
														$student_exam_grade_history = $student['ExamGrade'];
														$this->set(compact('student_exam_grade_history', 'student_exam_grade_change_history', 'freshman_program'));
														echo $this->element('registered_or_add_course_grade_detail_history'); ?>
													</td>
												</tr> 
												<?php
												$count++;
											}
										} ?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="<?= (count($exam_types) + 6); ?>">Legend: ( ** ) <span style="font-weight: normal;">Course in progress</span>; &nbsp;&nbsp;&nbsp; ( Bold ) <span style="font-weight: normal;">Waiting your decision</span></td>
										</tr>
									</tfoot>
								</table>
							</div>

							<?php
						}

						$array_count = array_count_values($frequency_count); ?>
						<hr>

						<?php
						if ((isset($gradeScaleDetail) && !empty($gradeScaleDetail['Course']['id']) && $gradeScaleDetail['Course']['thesis'] == 1 && ($gradeScaleDetail['Course']['Curriculum']['program_id'] == PROGRAM_PhD || $gradeScaleDetail['Course']['Curriculum']['program_id'] == PROGRAM_POST_GRADUATE)) && isset($gradeScaleDetail['GradeType']['used_in_gpa']) && $gradeScaleDetail['GradeType']['used_in_gpa'] == 1) { ?>
							<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;"><span style='margin-right: 15px;'></span>Currently, <?= $gradeScaleDetail['Course']['course_code_title']; ?> course is set as a <?= $gradeScaleDetail['Course']['Curriculum']['program_id'] == PROGRAM_POST_GRADUATE ? 'Thesis/Projecct' : 'Dissertation'; ?> course and associated to "<?= $gradeScaleDetail['GradeScale']['name']; ?>" from "<?= $gradeScaleDetail['GradeType']['type']; ?>" grading type which uses point values of the awarded grades in CGPA calculations. Please <?= $publishedCourseDetail['GivenByDepartment']['id'] == $gradeScaleDetail['Course']['Curriculum']['Department']['id'] ? '' : ('communicate ' . $gradeScaleDetail['Course']['Curriculum']['Department']['name'] . ' department and'); ?> check the correctness of the grade type specified on <?= $gradeScaleDetail['Course']['Curriculum']['curriculum_detail']; ?> curriculum before approving the grades.</div>
							<hr>
							<?php
						} ?>

						<input type="button" value="Show Grade Scale" onclick="showHideGradeScale('<?= $publishedCourseDetail['PublishedCourse']['id']; ?>')" id="ShowHideGradeScale" class="tiny radius button bg-blue">
						
						<div class="row">
							<div class="large-6 columns">
								<!-- AJAX GRADE SCALE LOADING -->
								<div style="margin-top:10px" id="GradeScale"></div>
								<!-- END AJAX GRADE SCALE LOADING -->
								<br>
							</div>
							<div class="large-6 columns"><br></div>
						</div>

						<br>
						<div class="row">
							<div class="large-3 columns">
								<table cellpadding="0" cellspacing="0" class="table">
									<thead>
										<tr>
											<td class="center"> Grade </th>
											<td class="center"> Frequency </td>
										</tr>
									</thead>
									<tbody>
										<?php
										$total_students = 0;
										foreach ($array_count as $grade => $freqeuncy) { ?>
											<tr>
												<td class="center"><?= $grade; ?></td>
												<td class="center"><?= $freqeuncy; ?></td>
											</tr>
											<?php
											$total_students += $freqeuncy;
										} ?>
									</tbody>
									<?php
									if (isset($total_students) && $total_students) { ?>
										<tfoot>
											<tr>
												<td class="center">Total</td>
												<td class="center"><?= $total_students; ?></td>
											</tr>
										</tfoot>
										<?php
									} ?>
								</table>
								<br>
							</div>
							<div class="large-6 columns">
								<?php
								if ($enable_approve_button) {

									$options = array('1' => (is_null($student['ExamGrade'][0]['registrar_approval']) ? ' &nbsp;Accept (Forward to Registrar)' : (isset($student['ExamGrade'][0]['registrar_approval']) && $student['ExamGrade'][0]['registrar_approval'] == -1 ? ' &nbsp;Reject (Reject Registrar\'s rejection, Assesment is correct)' : ' &nbsp;Accept (Forward to Registrar)')) , '-1' => (is_null($student['ExamGrade'][0]['registrar_approval']) ? ' &nbsp;Reject (Return to instructor for re-consideration)' : (isset($student['ExamGrade'][0]['registrar_approval']) && $student['ExamGrade'][0]['registrar_approval'] == -1 ? ' &nbsp;Accept Registrar\'s Rejection (Return to instructor)' : ' &nbsp;Reject (Return to instructor for re-consideration)')));
									$attributes = array('legend' => false, 'label' => false, 'separator' => "<br/>", 'default' => 1);
									//echo "<td colspan=2>";
									//echo $this->Form->radio('department_approval',$options,$attributes);
									//echo "</td></tr>";
									?>
									<table cellpadding="0" cellspacing="0" class="table">
										<tr>
											<th>Decision</th>
										</tr>
										<tr>
											<td style="background-color: white; padding-left: 10%;">
												<br>
												<?= $this->Form->radio('ExamGrade.department_approval', $options, $attributes); ?>
											</td>
										</tr>
										<tr>
											<td>
												Remark <br/>
												<?= $this->Form->input('ExamGrade.department_reason', array('label' => false, 'cols' => 60)); ?>
											</td>
										</tr>
									</table>
									<hr>
									<?= $this->Form->Submit('Approve/Reject Grade Submission', array('div' => false, 'id' => 'approveGrade', 'name' => 'approvegradesubmission', 'class' => 'tiny radius button bg-blue')); ?>
									<?php
								} else {
									//debug($department_rejected_grades_back_to_registrar);
									if ($department_rejected_grades_back_to_registrar) { ?>
										<br>
										<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Waiting for Registrar response for <?= $approver; ?>'s rejection of previously rejected grade by the registrar.</div>
										<?php
									} else if ($consequetive_rejected_grades_by_the_registrar) { ?>
										<br>
										<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Waiting for <?= $approver ; ?>'s response for registrar\'s consequetive rejections(two or more times).</div>
										<?php
									} else if ($department_rejected_grades_back_to_instructor) {
										if ($registrar_regected_grades) { ?>
											<br>
											<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Waiting for Instructor grade re-submission in response for <?= $approver; ?>'s acceptance of previously rejected grade by the registrar.</div>
											<?php
										} else { ?>
											<br>
											<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Waiting for Instructor grade re-submission in response for <?= $approver; ?>'s rejection of previously submitted grade by the instructor.</div>
											<?php
										}
									} else { ?>
										<br>
										<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No Grade needs your Approval/Rejection.</div>
										<?php
									}
								} ?>
							</div>
						</div>
						<?php
					} ?>
				</div>
				<?= $this->Form->end(); ?>
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

	function showHideGradeScale(id) {
		if ($("#ShowHideGradeScale").val() == 'Show Grade Scale') {
			var p_course_id = id;
			$("#GradeScale").empty();

			$("#GradeScale").append('Loading ...');

			var formUrl = '/published_courses/get_course_grade_scale/' + p_course_id;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: p_course_id,
				success: function(data, textStatus, xhr) {
					$("#GradeScale").empty();
					$("#GradeScale").append(data);
					$("#ShowHideGradeScale").attr('value', 'Hide Grade Scale');
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});
		} else {
			$("#GradeScale").empty();
			$("#ShowHideGradeScale").attr('value', 'Show Grade Scale');
		}

		return false;
	}

	function toggleViewFullId(id) {
		if ($('#' + id).css("display") == 'none') {
			$('#' + id + 'Img').attr("src", '/img/minus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append('Hide Filter');
		} else {
			$('#' + id + 'Img').attr("src", '/img/plus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append('Display Filter');
		}
		$('#' + id).toggle("slow");
	}

	var form_being_submitted = false;

	var checkForm = function(form) {

		if (form_being_submitted) {
			alert("Approving/Rejecting Grade Submission, please wait a moment...");
			form.approveGrade.disabled = true;
			return false;
		}

		form.approveGrade.value = 'Approving/Rejecting Grade Submission...';
		form_being_submitted = true;
		return true;
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>