<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('List Course Drops'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->Create('CourseDrop', array('action' => 'search')); ?>
				<?php
				if ($this->Session->read('Auth.User')['role_id'] != ROLE_STUDENT) { ?>
					<div style="margin-top: -30px;">
						<hr>
						<div onclick="toggleViewFullId('ListPublishedCourse')">
							<?php
							if (!empty($turn_off_search)) {
								echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Display Filter</span>
								<?php
							} else {
								echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Hide Filter</span>
								<?php
							} ?>
						</div>
						<div id="ListPublishedCourse" style="display:<?= (!empty($turn_off_search) ? 'none' : 'display'); ?>">
							<fieldset style="padding-bottom: 0px;padding-top: 15px;">
								<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
								<div class="row">
									<div class="large-3 columns">
										<?= $this->Form->input('Search.academic_year', array('label' => 'Academic Year: ', 'style' => 'width:90%;', 'empty' => ' All Applicable ACY ', 'options' => $acyear_array_data)); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search.semester', array('label' => 'Semester: ', 'style' => 'width:90%;', 'empty' => ' All Semesters ', 'options' => Configure::read('semesters'))); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search.program_id', array('label' => 'Program: ', 'style' => 'width:90%;',  'id' => 'program_id_1', 'empty' => ' All Programs ', 'options' => $programs)); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search.program_type_id', array('label' => 'Program Type: ', 'style' => 'width:90%;', 'empty' => ' All Program Types ', 'options' => $programTypes)); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-6 columns">
										<?php
										if (isset($colleges) && !empty($colleges)) {
											echo $this->Form->input('Search.college_id', array('label' => 'College: ', 'style' => 'width:90%;', 'empty' => ' All Applicable Colleges ', 'onchange' => 'getDepartment(1)', 'id' => 'college_id', 'default' => (isset($default_college_id) && !empty($default_college_id) ? $default_college_id : '')));
										} else if (isset($departments) && !empty($departments)) {
											echo $this->Form->input('Search.department_id', array('label' => 'Department: ', 'style' => 'width:90%;', 'empty' => ' All Applicable Departments ', 'id' => 'department_id_1', 'default' => (isset($default_department_id) && !empty($default_department_id) ? $default_department_id : '')));
										} ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search.graduated', array('label' => 'Graduated: ', 'style' => 'width:90%;', 'options' => array('0' => 'NO', '1' => 'Yes', '2' => 'All'), 'default' => '0')); ?>
									</div>
									
									<!-- <div class="large-3 columns">
										<?php //echo $this->Form->input('Search.studentnumber', array('label' => 'Student ID:', 'placeholder' => 'Student ID to filter ..', 'default' => $studentnumber, 'style' => 'width:90%;')); ?>
									</div> -->
									<div class="large-3 columns">
										<?= $this->Form->input('Search.name', array('label' => 'Student Name or ID: ', 'placeholder' => 'Optional student name or ID...', 'default' => $name, 'style' => 'width:90%;')); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search.limit', array('id' => 'limit ', 'type' => 'number', 'min' => '1',  'max' => '1000', 'value' => (isset($this->data['Search']['limit']) ? $this->data['Search']['limit'] : $limit), 'step' => '1', 'label' => 'Limit: ', 'style' => 'width:90%;')); ?>

										<?= (isset($this->data['Search']['page']) ? $this->Form->hidden('page', array('value' => $this->data['Search']['page'])) : ''); ?>
										<?= (isset($this->data['Search']['sort']) ? $this->Form->hidden('sort', array('value' => $this->data['Search']['sort'])) : ''); ?>
										<?= (isset($this->data['Search']['direction']) ? $this->Form->hidden('direction', array('value' => $this->data['Search']['direction'])) : ''); ?>

									</div>
									<div class="large-3 columns">
										&nbsp;
									</div>
									<div class="large-3 columns">
										<div style="padding-left: 10%;">
											<br>
											<h6 class='fs13 text-gray'>Status: </h6>
											<?php $options = array('accepted' => ' Accepted', 'rejected' => ' Rejected', 'notprocessed' => ' Not Processed', 'forced' => ' Forced Drop', );  ?>
											<?= $this->Form->input('Search.status', array('options' => $options, 'type' => 'radio', 'legend' => false, 'separator' => '<br>', 'label' => false, 'default' => 'notprocessed')); ?>
										</div>
									</div>
								</div>
								<?php
								if (isset($departments) && !empty($departments) && $this->Session->read('Auth.User')['role_id'] != ROLE_STUDENT && $this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR &&  $this->Session->read('Auth.User')['role_id'] != ROLE_COLLEGE &&  $this->Session->read('Auth.User')['role_id'] != ROLE_DEPARTMENT) { ?>
									<div class="row">
										<div class="large-6 columns">
											<?= $this->Form->input('Search.department_id', array('label' => 'Department: ', 'style' => 'width:90%;', 'empty' => ' All Departments ', 'id' => 'department_id_1', 'default' => $default_department_id)); ?>
										</div>
										<div class="large-6 columns">
										</div>
									</div>
									<?php
								} ?>
								<hr>
								<?= $this->Form->submit(__('Search'), array('name' => 'search', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
							</fieldset>
							
							<?php
							if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) { ?>
								<br> 
								
								<div style="margin-top: -10px;">
									<hr>
									<blockquote>
										<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
										<span style="text-align:justify;" class="fs15 text-gray">The student list you will get here depends on your <b style="text-decoration: underline;"><i>assigned College or Department, assigned Program and Program Types, and with your search conditions</i></b>. You can contact the registrar to adjust permissions assigned to you if you miss your students here.</span>
									</blockquote>
								</div>
								<?php
							} ?>
						</div>
					</div>
					<hr>
					<?php
				} else {
					echo '<div style="margin-top: -30px;"><hr></div>';
				} ?>

				<?php
				if (!empty($courseDrops)) { 
					$count = 1; ?>
					<br>
					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<td class="center">&nbsp;</td>
									<td class="center">#</td>
									<td class="vcenter"><?= $this->Paginator->sort('student_id', 'Student Name'); ?></td>
									<td class="vcenter"><?= $this->Paginator->sort('gender', 'Sex'); ?></td>
									<td class="center"><?= $this->Paginator->sort('studentnumber', 'Student ID'); ?></td>
									<td class="center"><?= $this->Paginator->sort('academic_year', 'ACY'); ?></td>
									<td class="center"><?= $this->Paginator->sort('semester', 'Sem'); ?></td>
									<td class="center"><?= $this->Paginator->sort('year_level_id', 'Year'); ?></td>
									<td class="center"><?= $this->Paginator->sort('course_id', 'Course'); ?></td>
									<td class="center">Cr</td>
									<td class="center"><?= $this->Paginator->sort('department_approval', 'Department'); ?></td>
									<td class="center"><?= $this->Paginator->sort('registrar_confirmation', 'Registrar'); ?></td>
									<?php
									if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) { ?>
										<td class="center"></td>
										<?php
									} ?>
								</tr>
							</thead>
							<tbody>
								<?php
								$start = $this->Paginator->counter('%start%');
								//debug($courseDrops[0]);
								foreach ($courseDrops as $courseDrop) { 
									//debug($courseDrop);
									//if (isset($this->data['Search']['academic_year']) && isset($this->data['Search']['semester']) && $this->data['Search']['academic_year'] == $courseDrop['CourseDrop']['academic_year'] && $this->data['Search']['semester'] == $courseDrop['CourseDrop']['semester']) {
										$courseTitleWithCredit = (isset($courseDrop['CourseRegistration']['PublishedCourse']['Course']['course_title']) ? $courseDrop['CourseRegistration']['PublishedCourse']['Course']['course_title'] . ' (' .  $courseDrop['CourseRegistration']['PublishedCourse']['Course']['course_code']. ') course  with ' . $courseDrop['CourseRegistration']['PublishedCourse']['Course']['credit']. ' ' . (count(explode('ECTS', $courseDrop['CourseRegistration']['PublishedCourse']['Course']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : '');
										$credit_type = (isset($courseDrop['Student']['Curriculum']['type_credit']) ? (count(explode('ECTS', $courseDrop['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit');
										$student_full_name = (isset($courseDrop['Student']['full_name']) ? $courseDrop['Student']['full_name'] . ' ('.  $courseDrop['Student']['studentnumber'] . ')' : '');
										
									//}
									?>
									<tr>
										<td class="center" onclick="toggleView(this)" id="<?= $count; ?>"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $count, 'div' => false, 'align' => 'center')); ?></td>
										<td class="center"><?= $start++; ?></td>
										<td class="vcenter"><?= $this->Html->link($courseDrop['Student']['full_name'], array('controller' => 'students', 'action' => 'student_academic_profile', $courseDrop['Student']['id'])); ?></td>
										<td class="center"><?= (strcasecmp(trim($courseDrop['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($courseDrop['Student']['gender']), 'female') == 0 ? 'F' : '')); ?></td>
										<td class="center"><?= $courseDrop['Student']['studentnumber']; ?></td>
										<td class="center"><?= $courseDrop['CourseDrop']['academic_year']; ?></td>
										<td class="center"><?= $courseDrop['CourseDrop']['semester']; ?></td>
										<td class="center"><?= (!empty($courseDrop['YearLevel']['name']) ? $courseDrop['YearLevel']['name'] : 'Pre/1st'); ?></td>
										<td class="center"><?= (isset($courseDrop['CourseRegistration']['PublishedCourse']['Course']) && !empty($courseDrop['CourseRegistration']['PublishedCourse']['Course']) ? $this->Html->link($courseDrop['CourseRegistration']['PublishedCourse']['Course']['course_title'], array('controller' => 'courses', 'action' => 'view', $courseDrop['CourseRegistration']['PublishedCourse']['Course']['id'])) : 'N/A'); ?></td>
										<td class="center"><?= (isset($courseDrop['CourseRegistration']['PublishedCourse']['Course']) && !empty($courseDrop['CourseRegistration']['PublishedCourse']['Course']) ? $courseDrop['CourseRegistration']['PublishedCourse']['Course']['credit'] : 'N/A'); ?></td>
										<td class="center">
											<?php
											if ($courseDrop['CourseDrop']['department_approval'] == 1) {
												if (!$courseDrop['CourseDrop']['forced']) {
													echo '<span class="accepted">Accepted</span>';
												} else {
													//echo '<span class="on-process">Forced Drop</span>';
													echo '--';
												}
											} else {
												if (!$courseDrop['CourseDrop']['forced']) {
													if (is_null($courseDrop['CourseDrop']['department_approval'])) {
														if (isset($courseDrop['CourseRegistration']['ExamResult']) && !empty($courseDrop['CourseRegistration']['ExamResult'])) {
															echo '<span class="on-process">(Have Exam Result)</span>';
														} else if (isset($courseDrop['CourseRegistration']['ExamGrade']) && !empty($courseDrop['CourseRegistration']['ExamGrade'])) {
															echo '<span class="on-process">(Have Exam Grade)</span>';
														} else {
															if ((isset($courseDrop['CourseRegistration']['PublishedCourse']['Section']['archive']) && $courseDrop['CourseRegistration']['PublishedCourse']['Section']['archive']) || (isset($allowed_academic_years_for_add_drop) && !empty($allowed_academic_years_for_add_drop) && !in_array($courseDrop['CourseDrop']['academic_year'], $allowed_academic_years_for_add_drop))) {
																echo '<span class="rejected">Expired</span>';
															} else {
																echo '<span class="text-gray"><i>Waiting Decision</i></span>';
															}
														}
													} else if ($courseDrop['CourseDrop']['department_approval'] == 0) {
														echo '<span class="rejected">Rejected</span>';
													}
												} else {
													echo ($courseDrop['CourseDrop']['forced'] == 1 ? '<span class="rejected">Forced Drop</span>' : '');
												}
											} ?>
										</td>
										<td class="center">
											<?php
											if (!$courseDrop['CourseDrop']['forced']) {
												if ($courseDrop['CourseDrop']['department_approval'] == 1) {
													if (is_null($courseDrop['CourseDrop']['registrar_confirmation'])) {
														if (isset($courseDrop['CourseRegistration']['ExamResult']) && !empty($courseDrop['CourseRegistration']['ExamResult'])) {
															echo '<span class="on-process">(Have Exam Result)</span>';
														} else if (isset($courseDrop['CourseRegistration']['ExamGrade']) && !empty($courseDrop['CourseRegistration']['ExamGrade'])) {
															echo '<span class="on-process">(Have Exam Grade)</span>';
														} else {
															if ((isset($courseDrop['CourseRegistration']['PublishedCourse']['Section']['archive']) && $courseDrop['CourseRegistration']['PublishedCourse']['Section']['archive']) || (isset($allowed_academic_years_for_add_drop) && !empty($allowed_academic_years_for_add_drop) && !in_array($courseDrop['CourseDrop']['academic_year'], $allowed_academic_years_for_add_drop))) {
																echo '<span class="rejected">Expired</span>';
															} else {
																echo '<span class="text-gray"><i>Waiting Decision</i></span>';
															}
														}
													} else if ($courseDrop['CourseDrop']['registrar_confirmation'] == 1) {
														echo '<span class="accepted">Accepted</span>';
													} else if ($courseDrop['CourseDrop']['registrar_confirmation'] == 0) {
														echo '<span class="rejected">Rejected</span>';
													}
												}
											} else if ((isset($courseDrop['CourseRegistration']['PublishedCourse']['Section']['archive']) && $courseDrop['CourseRegistration']['PublishedCourse']['Section']['archive']) || (isset($allowed_academic_years_for_add_drop) && !empty($allowed_academic_years_for_add_drop) && !in_array($courseDrop['CourseDrop']['academic_year'], $allowed_academic_years_for_add_drop))) {
												echo '<span class="rejected">Expired</span>';
											} else {
												echo ($courseDrop['CourseDrop']['forced'] == 1 ? '<span class="on-process">Forced Drop</span>' : '');
												if ((isset($courseDrop['CourseRegistration']['PublishedCourse']['drop']) && $courseDrop['CourseRegistration']['PublishedCourse']['drop'] == 0) && isset($courseDrop['CourseRegistration']['PublishedCourse']['Course']['id']) && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1 && isset($courseDrop['CourseRegistration']['PublishedCourse']['id']) && !ClassRegistry::init('ExamGrade')->isGradeSubmittedForPublishedCourse($courseDrop['CourseRegistration']['PublishedCourse']['id'])) {
													$confirmMessage = __('Are you sure you want to cancel the course drop of %s for %s? Cancelling this course drop will make the student available for course assigned instructor for grade submission. Are you sure you want cancel the course drop anyway?', $student_full_name, $courseTitleWithCredit);
													//echo '<br>'. $this->Form->postLink(__('[Cancel Course Drop]'), array('action' => 'delete', $courseDrop['CourseDrop']['id']), array('confirm' => $confirmMessage));
													echo '<br>'. $this->Html->link(__('[Cancel Drop]'), array('action' => 'delete', $courseDrop['CourseDrop']['id']), array('escape' => false, 'onclick' => 'return confirm(\'' . $confirmMessage . '\');'));
												}
											}
											
											if (isset($courseDrop['CourseRegistration']['PublishedCourse']['drop']) && $courseDrop['CourseRegistration']['PublishedCourse']['drop'] == 0 && isset($courseDrop['CourseDrop']['registrar_confirmation']) && $courseDrop['CourseDrop']['registrar_confirmation'] == 1 && isset($courseDrop['Student']['graduated']) && $courseDrop['Student']['graduated'] == 0 && isset($courseDrop['CourseDrop']['id']) && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) {
												$confirmMessage = __('REGISTRAR ADMIN COURSE DROP CANCELATION: Use this if the course is dropped by mistake for %s for %s? Cancelling this course drop will make the student available for course assigned instructor for grade submission and make sure the course instructor is available for grade submission and calender is open for ' . $courseDrop['CourseDrop']['academic_year'] .  ' semester ' . $courseDrop['CourseDrop']['semester']. '. Are you sure you want cancel the course drop anyway?', $student_full_name, $courseTitleWithCredit);
												echo '<br>'. $this->Html->link(__('[Cancel Drop]'), array('action' => 'delete', $courseDrop['CourseDrop']['id']), array('escape' => false, 'onclick' => 'return confirm(\'' . $confirmMessage . '\');'));
											} ?>
										</td>
										<?php
										if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) { ?>
											<td class="center">
												<?php
												if (!($courseDrop['CourseDrop']['forced']) && isset($courseDrop['CourseRegistration']['PublishedCourse']['drop']) && $courseDrop['CourseRegistration']['PublishedCourse']['drop'] == 0 && isset($courseDrop['CourseRegistration']['PublishedCourse']['Section']['archive']) && $courseDrop['CourseRegistration']['PublishedCourse']['Section']['archive'] == 0 && is_null($courseDrop['CourseDrop']['department_approval']) && is_null($courseDrop['CourseDrop']['registrar_confirmation']) && !ClassRegistry::init('ExamGrade')->isGradeSubmittedForPublishedCourse($courseDrop['CourseRegistration']['PublishedCourse']['id'])) {
													$confirmMessage = __('READ THE FOLLOWING NOTIFICATION CAREFULLY BEFORE PROCEEDING!! If you cancel course drop of %s you requested previously, you will be allowed to continue with the course and you will be available for course assigned instructor for grade submission. But If you did not attended the class and your are cancelling this course add, you will get NG grade. Are you sure you want cancel the course drop anyway?', $courseTitleWithCredit);
													//echo $this->Form->postLink('Cancel Drop', array('action' => 'delete', $courseDrop['CourseDrop']['id']), array('confirm' => $confirmMessage));
													echo $this->Html->link('Cancel Drop', array('action' => 'delete',  $courseDrop['CourseDrop']['id']), array('escape' => false, 'onclick' => 'return confirm(\'' . $confirmMessage . '\');'));
												} ?>
											</td>
											<?php
										} ?>
									</tr>
									<tr id="c<?= $count++; ?>" style="display:none">
										<td colspan="2" style="background-color: white;"> </td>
										<td colspan=<?= $this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT ? "11" : "10"; ?> style="background-color: white;">
											<?php
											if (isset($courseDrop['CourseRegistration']['PublishedCourse']['Course']) && !empty($courseDrop['CourseRegistration']['PublishedCourse']['Course']['id'])) { ?>
												<table cellpadding="0" cellspacing="0" class="table">
													<tbody>
														<tr>
															<td class="vcenter" style="background-color: white;">
																<span class="fs13 text-gray" style="font-weight: bold">Dropped from Section: </span> <?= ($courseDrop['CourseRegistration']['PublishedCourse']['Section']['name'] . ' (' . (isset($courseDrop['CourseRegistration']['PublishedCourse']['Section']['YearLevel']['name']) ? $courseDrop['CourseRegistration']['PublishedCourse']['Section']['YearLevel']['name'] : 'Pre/1st') . ', ' . $courseDrop['CourseRegistration']['PublishedCourse']['Section']['academicyear'] . ')'); ?>  &nbsp; <?= ($courseDrop['CourseRegistration']['PublishedCourse']['Section']['archive'] ? '<span class="rejected"> (Archieved) </span>' : '<span class="accepted"> (Active) </span>' ); ?>
															</td>
														</tr>
														<tr>
															<td class="vcenter">
																<span class="fs13 text-gray" style="font-weight: bold">Section/Course Curriculum: </span> <?= (isset($courseDrop['CourseRegistration']['PublishedCourse']['Section']['Curriculum']['name']) ? $courseDrop['CourseRegistration']['PublishedCourse']['Section']['Curriculum']['name'] . ' - ' . $courseDrop['CourseRegistration']['PublishedCourse']['Section']['Curriculum']['year_introduced'] :(isset($courseDrop['CourseRegistration']['PublishedCourse']['Course']['Curriculum']['name']) ? $courseDrop['CourseRegistration']['PublishedCourse']['Course']['Curriculum']['name'] . ' - ' . $courseDrop['CourseRegistration']['PublishedCourse']['Course']['Curriculum']['year_introduced'] : ''));  ?>
															</td>
														</tr>
														<tr>
															<td class="vcenter" style="background-color: white;">
																<span class="fs13 text-gray" style="font-weight: bold">Section Department/College: </span> <?= (isset($courseDrop['CourseRegistration']['PublishedCourse']['Department']['name']) ? $courseDrop['CourseRegistration']['PublishedCourse']['Department']['name'] . ' ('  . $courseDrop['CourseRegistration']['PublishedCourse']['Department']['College']['name']. ')' : (isset($courseDrop['CourseRegistration']['PublishedCourse']['College']['name']) ? 'Pre/Freshman (' . $courseDrop['CourseRegistration']['PublishedCourse']['College']['name'] . ')' : '')); ?>
															</td>
														</tr>
														<tr>
															<td class="vcenter">
																<span class="fs13 text-gray" style="font-weight: bold">Course Given By: </span> <?= (isset($courseDrop['CourseRegistration']['PublishedCourse']['GivenByDepartment']['name']) ? $courseDrop['CourseRegistration']['PublishedCourse']['GivenByDepartment']['name']  : 'Not Assigned Yet'); ?>
															</td>
														</tr>
														<tr>
															<td class="vcenter" style="background-color: white;">
																<span class="fs13 text-gray" style="font-weight: bold">Student Attached Curriculum: </span> <?= (!empty($courseDrop['Student']['Curriculum']['name']) ? $courseDrop['Student']['Curriculum']['name'] . ' - ' . $courseDrop['Student']['Curriculum']['year_introduced'] : '<span class="Rejected">No Curriculum Attachement</span>'); ?>
															</td>
														</tr>
														<tr>
															<td class="vcenter">
																<span class="fs13 text-gray" style="font-weight: bold">Student Graduated: </span> <?= ($courseDrop['Student']['graduated'] ? '<span class="rejected"> Yes </span>': '<span class="accepted"> No </span>'); ?>
															</td>
														</tr>
														<?= (!empty($courseDrop['CourseDrop']['reason']) ? '<tr><td class="vcenter" style="background-color: white;"><span class="fs13 text-gray" style="font-weight: bold">Course Drop Reason:  </span>' .$courseDrop['CourseDrop']['reason'] . '</td></tr>' : '');  ?>
														<?= (!empty($courseDrop['CourseDrop']['minute_number']) ? '<tr><td class="vcenter" style="background-color: white;"><span class="fs13 text-gray" style="font-weight: bold">Minute Number:  </span>' .$courseDrop['CourseDrop']['minute_number'] . '</td></tr>' : '');  ?>
														<tr>
															<td class="vcenter" style="background-color: white;">
																<span class="fs13 text-gray" style="font-weight: bold">Course Drop Requested: </span> <?= $this->Time->format("F j, Y h:i:s A", $courseDrop['CourseDrop']['created'], NULL, NULL); ?>
															</td>
														</tr>
														<tr>
															<td class="vcenter">
																<span class="fs13 text-gray" style="font-weight: bold">Course Drop <?= (($courseDrop['CourseDrop']['forced'] == 0 && ($courseDrop['CourseDrop']['department_approval'] == 1 || $courseDrop['CourseDrop']['registrar_confirmation'] == 1)) ? ' Approved' . ($courseDrop['CourseDrop']['registrar_confirmation'] == 1 ? ' By Registrar' : ' By Department'). ':  </span> ' . $this->Time->format("F j, Y h:i:s A", $courseDrop['CourseDrop']['modified'], NULL, NULL) : ((($courseDrop['CourseDrop']['forced'] == 1 || $courseDrop['CourseDrop']['department_approval'] == 0 || $courseDrop['CourseDrop']['registrar_confirmation'] == 0) ? '' . ($courseDrop['CourseDrop']['forced'] == 1 ? ' Forced Drop' : ($courseDrop['CourseDrop']['registrar_confirmation'] == 0 ? ' Rejected By Registrar' : 'Rejected By Department')) . ':  </span> ' . $this->Time->format("F j, Y h:i:s A", $courseDrop['CourseDrop']['modified'], NULL, NULL) : ' Approval:  </span> Waiting ... '))); ?>
															</td>
														</tr>
													</tbody>
												</table>
												<?php
											} else { ?>
												<span class="rejected">Error: Published Course not found or deleted. Could't load Course details!!.</span>
												<?php
												if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
													$confirmMessage = __('Are you sure you want to cancel the course drop of %s for %s? Cancelling this course drop will make the student available for course assigned instructor for grade submission. Are you sure you want cancel the course drop anyway?', $student_full_name, $courseTitleWithCredit);
													//echo '<br>'. $this->Form->postLink(__('[Cancel Course Drop]'), array('action' => 'delete', $courseDrop['CourseDrop']['id']), array('confirm' => $confirmMessage));
													echo '<br>'. $this->Html->link(__('[Cancel Drop]'), array('action' => 'delete', $courseDrop['CourseDrop']['id']), array('escape' => false, 'onclick' => 'return confirm(\'' . $confirmMessage . '\');'));
												}  
											} ?>
										</td>
									</tr>
									<?php
								} ?>
							</tbody>
						</table>
					</div>
					<br>

					<hr>
					<div class="row">
						<div class="large-5 columns">
							<?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total'))); ?>
						</div>
						<div class="large-7 columns">
							<div class="pagination-centered">
								<ul class="pagination">
									<?= $this->Paginator->prev('<< ' . __(''), array('tag' => 'li'), null, array('class' => 'arrow unavailable')); ?> <?= $this->Paginator->numbers(array('separator' => '', 'tag' => 'li')); ?> <?= $this->Paginator->next(__('') . ' >>', array('tag' => 'li'), null, array('class' => 'arrow unavailable')); ?>
								</ul>
							</div>
						</div>
					</div>

					<?php
				} else { ?>
					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no Course Drop in the system in the given criteria.</div>
					<?php
				} ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	
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

	function getDepartment(id) {
		//serialize form data
		var formData = $("#college_id").val();
		$("#department_id_" + id).empty();

		$("#department_id_" + id).append('<option style="width:90%;">loading...</option>');

		if (formData) {
			$("#department_id_" + id).attr('disabled', true);
			//get form action
			var formUrl = '/departments/get_department_combo/' + formData + '/0/1';
			$.ajax({
				type: 'get',
				url: formUrl,
				data: formData,
				success: function(data, textStatus, xhr) {
					$("#department_id_" + id).attr('disabled', false);
					$("#department_id_" + id).empty();
					//$("#department_id_" + id).append('<option style="width:100px"></option>');
					$("#department_id_" + id).append(data);
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;

		} else {
			$("#department_id_" + id).empty().append('<option value="">[ Select College First ]</option>');
		}
	}

	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none') {
			$('#i' + obj.id).attr("src",'/img/minus2.gif');
		} else {
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		}
		$('#c' + obj.id).toggle("slow");
	}
</script>