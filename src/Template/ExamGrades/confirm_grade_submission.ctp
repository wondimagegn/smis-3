<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Confirm Grade Submission'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('ExamGrade', array('onSubmit' => 'return checkForm(this);')); ?>
				<?php 
				if (!isset($get_list_of_students_with_grade)) { ?>
					<div style="margin-top: -30px;">
						<hr>
						<?php
						if (empty($turn_off_search)) { ?>
							<blockquote>
								<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
								<span style="text-align:justify;" class="fs14 text-gray">This tool will help you to approve grade which was approved by department <b style="text-decoration: underline;"><i>for your final confirmation</i></b>. Only those Grades which was not confirmed previously will get confirmed. You can also leave Program and Program Type filters unchecked or empty to get all grade submissions for the selected academic year</span>
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
							<fieldset style="padding-bottom: 0px;padding-top: 20px;">
								<!-- <legend>&nbsp;&nbsp; Search Filter &nbsp;&nbsp;</legend> -->
								<div class="row">
									<div class="large-3 columns">
										<?= $this->Form->input('Search.academicyear', array('label' => 'Academic Year: ', 'type' => 'select', 'style' => 'width: 80%', 'options' => $acyear_array_data, /*  'empty' => "[ Select Academic Year ]", */ 'required', 'default' => isset($this->request->data['Search']['academicyear']) ? $this->request->data['Search']['academicyear'] : $defaultacademicyear)); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search.semester', array('options' => Configure::read('semesters'), 'style' => 'width: 80%', 'label' => 'Semester: ', 'empty' => 'Any Semester')); ?>
									</div>
									<div class="large-3 columns">
										<h6 class='fs13 text-gray'>Program: </h6>
										<?= $this->Form->input('Search.program_id', array('id' => 'program_id', 'label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'div' => false)); ?>
									</div>
									<div class="large-3 columns">
										<h6 class='fs13 text-gray'>Program Type: </h6>
										<?= $this->Form->input('Search.program_type_id', array('id' => 'program_type_id', 'label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'div' => false)); ?>
									</div>
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
					//if(!isset($hide_approve_list)){   ?>
					<?php
					if (isset($grade_submitted_courses_organized_by_published_course) && !empty($grade_submitted_courses_organized_by_published_course)) { ?>
						<hr>
						<h6 class="fs14 text-gray">List of Exam Grades submitted for <?= $this->data['Search']['academicyear']; ?> academic year <?= (empty($this->request->data['Search']['semester']) ? '' : ('' . ($this->request->data['Search']['semester'] == 'I' ? '1st semester' : ($this->request->data['Search']['semester'] == 'II' ? '2nd semester' : ($this->request->data['Search']['semester'] == 'III' ? '3rd semester' : $this->request->data['Search']['semester'] . ' semester'))))); ?>, which are approved by the department and awaiting your confirmation.</h6>
						<hr>
						<?php
						foreach ($grade_submitted_courses_organized_by_published_course as $dep => $depvalue) {
							//echo "<span class='fs14'><strong class='text-gray'>Department: </strong><b>" . ($dep == 0 ? 'Freshman Program' : $departments[$dep]) . "</b></span><br>";
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
														<div style="overflow-x:auto;">
															<table cellpadding="0" cellspacing="0" class="table">
																<thead>
																	<tr>
																		<td colspan="7" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
																			<span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?= $section_name . ' ' . (isset($yk)  ?  ' (' . $yk . ')' : ' (Pre/1st)'); ?></span>
																				<br>
																				<span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
																					<?= (is_numeric($dep) && $dep > 0 ? $departmentsss[$dep] : (count(explode('c~', $dep)) > 1 ? $collegesss[explode('c~', $dep)[1]] . ' - Pre/1st' : '' )). ' &nbsp; | &nbsp; ' .  $pk . ' &nbsp; | &nbsp; ' . $ptk  ?><br>
																					<?= $this->request->data['Search']['academicyear'] . (empty($this->request->data['Search']['semester']) ? '' : (' &nbsp; | &nbsp; ' . ($this->request->data['Search']['semester'] == 'I' ? '1st Semester' : ($this->request->data['Search']['semester'] == 'II' ? '2nd Semester' : ($this->request->data['Search']['semester'] == 'III' ? '3rd Semester' : $this->request->data['Search']['semester'] . ' Semester'))))); ?>
																					<br>
																				</span>
																			</span>
																			<span class="text-black" style="padding-top: 14px; font-size: 14px; font-weight: bold">
																				
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
																		<th class="center">Action</th>
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
																				<td class="center"><?= ((isset($publishedCourse['CourseInstructorAssignment']) && count($publishedCourse['CourseInstructorAssignment']) > 0) ?  $publishedCourse['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. ' . $publishedCourse['CourseInstructorAssignment'][0]['Staff']['full_name'] : ''); ?></td>
																				<td class="center"><?= $this->Html->link(__('Review & Confirm'), array('action' => 'confirm_grade_submission', $publishedCourse['PublishedCourse']['id'])); ?></td>
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
					// }  //hide list of grade approval list

					if (isset($get_list_of_students_with_grade) && !empty($get_list_of_students_with_grade)) {

						//$this->Form->create('ExamGrade', array('action' => 'confirm_grade_submission', 'onSubmit' => 'return checkForm(this);'));

						if (count($get_list_of_students_with_grade) > 0) {
							//debug($get_list_of_students_with_grade);
							// $this->set(compact('get_list_of_students_with_grade', 'hide_approve_list','search_published_course','gradeScaleDetail','instructorDetail', 'publishedCourseDetail'));

							if (isset($publishedCourseDetail['Department']['name'])) {
								$freshman_program = false;
								$approver = 'department';
								$approver_c = 'Department';
							} else {
								$freshman_program = true;
								$approver = 'freshman program';
								$approver_c = 'Freshman Program';
							} ?>

							<div class="fs14" style="text-align: justify;">
								This grade is submitted by <u><b class="text-black"><?= ((isset($instructorDetail['Staff']) && !empty($instructorDetail['Staff'])) ? $instructorDetail['Staff']['Title']['title']  . '. '. $instructorDetail['Staff']['first_name'] . ' ' . $instructorDetail['Staff']['middle_name'] . ' ' . $instructorDetail['Staff']['last_name'] . ' ('. $instructorDetail['Staff']['Position']['position']. ')' : (isset($publishedCourseDetail['Department']['name']) ? 'the department' : 'the freshman program (college)')); ?></b></u> for <u><b class="text-black"><?= $publishedCourseDetail['Course']['course_title'] . ' (' . $publishedCourseDetail['Course']['course_code'] . ')'; ?></b></u> course and waiting for your confirmation. 
								Please make sure that the submitted course exam grade is correct as <strong>your decision is final</strong>. If you reject the grade, then it will be returned back to the <?= $approver; ?> for re-consideration. <b style="text-decoration: underline;"><i> If you accept the the grade, it will become permanent grade and it can only be changed either through grade change process or makeup exam.</i></b>
							</div>
							<hr>
							
							<div style="overflow-x:auto;">
								<table cellpadding="0" cellspacing="0" class="table">
									<tr>
										<td style="width:50%;"><strong class="text-gray"><?= (isset($publishedCourseDetail['Department']['type']) && !empty($publishedCourseDetail['Department']['type']) ? $publishedCourseDetail['Department']['type'].': ' : 'Department: '); ?></strong> <?= (isset($publishedCourseDetail['Department']['name']) ? $publishedCourseDetail['Department']['name'] : 'Pre/Freshman'); ?></td>
										<td style="width:50%;"><strong class="text-gray"> Section:</strong> <?= $publishedCourseDetail['Section']['name']; ?></td>
									</tr>
									<tr>
										<td><strong class="text-gray">Course:</strong> <?= $publishedCourseDetail['Course']['course_title'] . ' (' .$publishedCourseDetail['Course']['course_code'] .')' ; ?></td>
										<td><strong class="text-gray"><?= (count(explode('ECTS', $publishedCourseDetail['Course']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit'); ?>:</strong> <?= $publishedCourseDetail['Course']['credit']; ?></td>
									</tr>
									<tr>
										<td><strong class="text-gray">Program:</strong> <?= $publishedCourseDetail['Program']['name'] . ' / ' . $publishedCourseDetail['ProgramType']['name']; ?></td>
										<td><strong class="text-gray"> Year Level:</strong> <?= (isset($publishedCourseDetail['YearLevel']['name']) ? $publishedCourseDetail['YearLevel']['name'] : '1st'); ?></td>
									</tr>
									<tr>
										<td><strong class="text-gray">Academic Year:</strong> <?= $publishedCourseDetail['PublishedCourse']['academic_year']; ?></td>
										<td><strong class="text-gray">Semester:</strong> <?= $publishedCourseDetail['PublishedCourse']['semester']; ?></td>
									</tr>
								</table>
							</div>
							<br>

							<div style="overflow-x:auto;">
								<table cellpadding="0" cellspacing="0" class="table">
									<thead>
										<tr>
											<td style="width:5%" class="center">&nbsp;</td>
											<td style="width:25%" class="vcenter">Student Name</td>
											<td style="width:15%" class="center">Student ID</td>
											<td style="width:10%" class="center">Grade</td>
											<td style="width:50%" class="center">Status</td>
										</tr>
									</thead>
									<tbody>
										<?php
										$count = 1;
										$frequency_count = array();
										$st_count = 0;
										$enable_approve_button = 0;
										$registrar_regected_grades = 0;
										$department_rejected_grades_back_to_instructor = 0;
										$department_rejected_grades_back_to_registrar = 0;

										$consequetive_rejected_grades_by_the_registrar = 0;

										if (isset($get_list_of_students_with_grade['register']) && !empty($get_list_of_students_with_grade['register'])) {
											foreach ($get_list_of_students_with_grade['register'] as $key => $student) {
												$st_count++; ?>
												<tr <?php if (isset($student['ExamGrade']) && !empty($student['ExamGrade']) && $student['ExamGrade'][0]['registrar_approval'] == null) echo ' style="font-weight:bold"'; ?>>
													<td class="center" onclick="toggleView(this)" id="<?= $st_count; ?>"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $st_count)); ?></td>
													<td class="vcenter"><?= $student['Student']['first_name'] . ' ' . $student['Student']['middle_name'] . ' ' . $student['Student']['last_name']; ?></td>
													<td class="center"><?= $student['Student']['studentnumber']; ?></td>
													<td class="center">
														<?php
														if (isset($student['ExamGrade']) && !empty($student['ExamGrade'])) {
															$frequency_count[] = $student['ExamGrade'][0]['grade'];
															echo $student['ExamGrade'][0]['grade'];
															if ($student['ExamGrade'][0]['registrar_approval'] == null) {
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
													<td style="background-color: white;">&nbsp;</td>
													<td style="background-color: white;" colspan="4">
														<?php
														if (isset($student['MakeupExam']) && isset($student['ExamGradeChange']) && count($student['ExamGradeChange']) > 0) { ?>
															<table cellpadding="0" cellspacing="0" class="table">
																<tr>
																	<td style="width:28%; font-weight:bold; background-color: white;">Makeup Exam Minute Number:</td>
																	<td style="width:72%; background-color: white;"><?= $student['ExamGradeChange'][0]['minute_number']; ?></td>
																</tr>
															</table>
															<br>
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
																<!-- <td style="vertical-align:top; width:10%; background-color: white;">&nbsp;</td> -->
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

										// Course ADDDD

										if (isset($get_list_of_students_with_grade['add']) && !empty($get_list_of_students_with_grade['add'])) {
											foreach ($get_list_of_students_with_grade['add'] as $key => $student) {
												$st_count++; ?>
												<tr <?php if (isset($student['ExamGrade']) && !empty($student['ExamGrade']) && $student['ExamGrade'][0]['registrar_approval'] == null) echo ' style="font-weight:bold"'; ?>>
													<td class="center" onclick="toggleView(this)" id="<?= $st_count; ?>"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $st_count)); ?></td>
													<td class="vcenter"><?= $student['Student']['first_name'] . ' ' . $student['Student']['middle_name'] . ' ' . $student['Student']['last_name']; ?></td>
													<td class="center"><?= $student['Student']['studentnumber']; ?></td>
													<td class="center">
														<?php
														if (isset($student['ExamGrade']) && !empty($student['ExamGrade'])) {
															$frequency_count[] = $student['ExamGrade'][0]['grade'];
															echo $student['ExamGrade'][0]['grade'];
															if ($student['ExamGrade'][0]['registrar_approval'] == null) {
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
													<td style="background-color: white;">&nbsp;</td>
													<td style="background-color: white;" colspan="4">
														<?php
														if (isset($student['MakeupExam']) && isset($student['ExamGradeChange']) && count($student['ExamGradeChange']) > 0) { ?>
															<table cellpadding="0" cellspacing="0" class="table">
																<tr>
																	<td style="width:28%; font-weight:bold; background-color: white;">Makeup Exam Minute Number:</td>
																	<td style="width:72%; background-color: white;"><?= $student['ExamGradeChange'][0]['minute_number']; ?></td>
																</tr>
															</table>
															<br>
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
																<!-- <td style="vertical-align:top; width:40%; background-color: white;">&nbsp;</td> -->
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
												//End of detail view
												$count++;
											}
										} ?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="5">Legend: ( ** ) <span style="font-weight: normal;">Course in progress</span>; &nbsp;&nbsp;&nbsp; ( Bold ) <span style="font-weight: normal;">Waiting your decision</span></td>
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
							<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;"><span style='margin-right: 15px;'></span>Currently, <?= $gradeScaleDetail['Course']['course_code_title']; ?> course is set as a <?= $gradeScaleDetail['Course']['Curriculum']['program_id'] == PROGRAM_POST_GRADUATE ? 'Thesis/Projecct' : 'Dissertation'; ?> course and associated to "<?= $gradeScaleDetail['GradeScale']['name']; ?>" from "<?= $gradeScaleDetail['GradeType']['type']; ?>" grading type which uses point values of the awarded grades in CGPA calculations. Please communicate <?= $gradeScaleDetail['Course']['Curriculum']['Department']['name']; ?> department and check the correctness of the grade type specified on <?= $gradeScaleDetail['Course']['Curriculum']['curriculum_detail']; ?> curriculum before confirming the grades.</div>
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
									$options = array('1' => ' Accept (Make the grades permanent)', '-1' => ' Reject (Send back to department)');
									$attributes = array('legend' => false, 'id' => 'registrarApproval', 'separator' => "<br/>", 'default' => 1); ?>

									<table cellpadding="0" cellspacing="0" class="table">
										<tr>
											<th>Your Decision</th>
										</tr>
										<tr>
											<td style="background-color: white; padding-left: 10%;">
												<br><?= $this->Form->radio('ExamGrade.registrar_approval', $options, $attributes); ?>
											</td>
										</tr>
										<tr>
											<td> Remark <br /><?= $this->Form->input('ExamGrade.registrar_reason', array('label' => false, 'cols' => 60)); ?></td>
										</tr>
									</table>
									<hr>
									<?= $this->Form->Submit('Confirm/Reject Grade Submission', array('div' => false, 'id' => 'confirmGrade', 'name' => 'confirmgradesubmission', 'class' => 'tiny radius button bg-blue')); ?>
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
		if ($('#c' + obj.id).css("display") == 'none')
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		else
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		$('#c' + obj.id).toggle("slow");
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

	var form_being_submitted = false;

	var checkForm = function(form) {

		if (form_being_submitted) {
			alert("Confirming/Rejecting Grade Submission, please wait a moment...");
			form.confirmGrade.disabled = true;
			return false;
		}

		form.confirmGrade.value = 'Confirming/Rejecting Grade Submission...';
		form_being_submitted = true;
		return true;
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}

</script>