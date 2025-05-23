<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('List Course Adds'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->Create('CourseAdd', array('action' => 'search')); ?>
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
											<?php $options = array('accepted' => ' Accepted', 'rejected' => ' Rejected', 'notprocessed' => ' Not Processed', 'auto_rejected' => ' Auto Rejected', );  ?>
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
							<br>

							<?php
							if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) { ?>
								<div style="margin-top: -10px;">
									<hr>
									<blockquote>
										<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
										<span style="text-align:justify;" class="fs15 text-gray">The student list you will get here depends on your <b><i>assigned College or Department, assigned Program and Program Types, and with your search conditions</i></b>. You can contact the registrar to adjust permissions assigned to you if you miss your students here.</span>
										<?php
										if (!isset($this->data['Search']['status']) || (isset($this->data['Search']['status']) && $this->data['Search']['status'] != 'auto_rejected')) { ?>
											<br><br>
											<span style="text-align:justify;" class="fs15 text-gray">You can view auto rejected student course add requests by adjusting the <b>Status</b> filter to <b>Auto Rejected</b> and check the auto rejection reason by clicking <b>'+'</b> icon for each rejected course add request.
												<br><br>You can also approve auto rejected course add requests for <b style="text-decoration: underline;">graduating class students</b> if:
												<ol class="fs15 text-gray" style="padding-top: 10px;">
													<li>Grade is not submitted for the course</li>
													<li>The student must have atleast one course registration in the requested academic year and semester</li>
												</ol>
												<b style="padding-top: 10px;">and the sum of current load of the student and the requested course add credit is below the allowed maximum Credit/ECTS specified in the senate legislation</b>
											</span>
											<?php
										} ?>
									</blockquote>
								</div>
								<?php
							} else {
								if (!isset($this->data['Search']['status']) || (isset($this->data['Search']['status']) && $this->data['Search']['status'] != 'auto_rejected')) { ?>
									<div style="margin-top: -10px;">
										<hr>
										<blockquote>
											<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
											<span style="text-align:justify;" class="fs15 text-gray">You can view auto rejected student course add requests by adjusting the <b>Status</b> filter to <b>Auto Rejected</b> and check the auto rejection reason by clicking <b>'+'</b> icon for each rejected course add request. <b><i>You can also approve auto rejected course add requests for graduating class students if the sum of current load of the student and the requested course add credit is below the allowed maximum credit/ECTS specified in the senate legislation</i></b></span>
										</blockquote>
									</div>
									<?php
								}
							} ?>
						</div>
					</div>
					<hr>
					<?php
				}  else {
					echo '<div style="margin-top: -30px;"><hr></div>';
				} ?>

				<?php
				if (!empty($courseAdds)) { 
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
									<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) ? '<td class="center"></td>' : ''; ?>
								</tr>
							</thead>
							<tbody>
								<?php
								$start = $this->Paginator->counter('%start%');
								//debug($courseAdds[0]);
								$grade_submitted_for_course = 0;
								foreach ($courseAdds as $courseAdd) { ?>
									<tr>
										<td class="center" onclick="toggleView(this)" id="<?= $count; ?>"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $count, 'div' => false/* , 'align' => 'center' */)); ?></td>
										<td class="center"><?= $start++; ?></td>
										<td class="vcenter"><?= $this->Html->link($courseAdd['Student']['full_name'], array('controller' => 'students', 'action' => 'student_academic_profile', $courseAdd['Student']['id'])); ?></td>
										<td class="center"><?= (strcasecmp(trim($courseAdd['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($courseAdd['Student']['gender']), 'female') == 0 ? 'F' : '')); ?></td>
										<td class="center"><?= $courseAdd['Student']['studentnumber']; ?></td>
										<td class="center"><?= (isset($courseAdd['CourseAdd']['id']) ? $courseAdd['CourseAdd']['academic_year'] : 'N/A'); ?></td>
										<td class="center"><?= (isset($courseAdd['CourseAdd']['id']) ? $courseAdd['CourseAdd']['semester'] : 'N/A'); ?></td>
										<td class="center"><?= (!empty($courseAdd['YearLevel']['name']) ? $courseAdd['YearLevel']['name'] : 'Pre/1st'); ?></td>
										<td class="center"><?= (isset($courseAdd['PublishedCourse']['Course']) && !empty($courseAdd['PublishedCourse']['Course']['id']) ? $this->Html->link((trim(str_replace('  ', ' ', $courseAdd['PublishedCourse']['Course']['course_title']))), array('controller' => 'courses', 'action' => 'view', $courseAdd['PublishedCourse']['Course']['id'])) : 'N/A'); ?></td>
										<td class="center"><?= (isset($courseAdd['PublishedCourse']['Course']) && !empty($courseAdd['PublishedCourse']['Course']['id']) ? $courseAdd['PublishedCourse']['Course']['credit'] : 'N/A'); ?></td>
										<td class="center">
											<?php
											
											if (isset($this->data['Search']['academic_year']) && isset($this->data['Search']['semester']) && $this->data['Search']['academic_year'] == $courseAdd['CourseAdd']['academic_year'] && $this->data['Search']['semester'] == $courseAdd['CourseAdd']['semester']) {
												$courseTitleWithCredit = (isset($courseAdd['PublishedCourse']['Course']['course_title']) ? $courseAdd['PublishedCourse']['Course']['course_title'] . ' (' . $courseAdd['PublishedCourse']['Course']['course_code']. ') course with ' . $courseAdd['PublishedCourse']['Course']['credit'] . ' ' . (count(explode('ECTS', $courseAdd['PublishedCourse']['Course']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : '');
												$credit_type = (isset($courseAdd['Student']['Curriculum']['type_credit']) ? (count(explode('ECTS', $courseAdd['Student']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit') : 'Credit');
												$student_full_name = (isset($courseAdd['Student']['full_name']) ? $courseAdd['Student']['full_name'] . ' ('.  $courseAdd['Student']['studentnumber'] . ')' : '');
												$enable_auto_rejection_override = false;
											}

											if ($courseAdd['CourseAdd']['department_approval'] == 1) {
												if (!$courseAdd['CourseAdd']['auto_rejected']) {
													echo '<span class="accepted">Accepted</span>';
												} else {
													echo '<span class="rejected">Auto Reject Override (Department)</span>';
												}
											} else {
												if (isset($courseAdd['Student']['graduated']) && $courseAdd['Student']['graduated'] == 1) {
													echo '<span class="on-process">Graduated Student</span>';
												} else if (!$courseAdd['CourseAdd']['auto_rejected']) {
													if (is_null($courseAdd['CourseAdd']['department_approval'])) {
														if ((isset($courseAdd['PublishedCourse']['Section']['archive']) && $courseAdd['PublishedCourse']['Section']['archive']) || (isset($allowed_academic_years_for_add_drop) && !empty($allowed_academic_years_for_add_drop) && !in_array($courseAdd['CourseAdd']['academic_year'], $allowed_academic_years_for_add_drop))) {
															echo '<span class="rejected">Expired</span>';
														} else {
															echo '<span class="text-gray"><i>Waiting Decision</i></span>';
														}
													} else if ($courseAdd['CourseAdd']['department_approval'] == 0) {
														echo '<span class="rejected">Rejected</span>';
													}
												} else {
													echo '<span class="rejected">Auto Rejected (System)</span>';
													if (isset($this->data['Search']['academic_year']) && isset($this->data['Search']['semester']) && $this->data['Search']['academic_year'] == $courseAdd['CourseAdd']['academic_year'] && $this->data['Search']['semester'] == $courseAdd['CourseAdd']['semester']) {
														$confirmMessage = __('Are you sure you want to cancel the auto rejected course add of %s for %s? Cancelling this auto rejection will auto approve the course add request. Are you sure you want to cancel the auto rejection anyway?', $student_full_name, $courseTitleWithCredit);
														if (isset($current_load) && isset($courseAdd['Student']['id']) && isset($current_load[$courseAdd['Student']['id']])) {
															echo '<br>Load: ' . $current_load[$courseAdd['Student']['id']] . ' ' . $credit_type;
															//debug($graduatingClassStudent[$courseAdd['Student']['id']]);
															if (isset($graduatingClassStudent) && $graduatingClassStudent[$courseAdd['Student']['id']] && $credit_type == 'Credit') {
																if (isset($courseAdd['PublishedCourse']['Course']['credit']) && $courseAdd['PublishedCourse']['Course']['credit'] >= 0 && (($current_load[$courseAdd['Student']['id']] + $courseAdd['PublishedCourse']['Course']['credit']) <= (DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER + ADDITIONAL_CREDIT_ALLOWED_FOR_GRADUATING_STUDENTS))) {
																	$enable_auto_rejection_override = true;
																}
															} else if (isset($graduatingClassStudent) && $graduatingClassStudent[$courseAdd['Student']['id']] && $credit_type == 'ECTS') {
																if (isset($courseAdd['PublishedCourse']['Course']['credit']) && $courseAdd['PublishedCourse']['Course']['credit'] >= 0 && (($current_load[$courseAdd['Student']['id']] + $courseAdd['PublishedCourse']['Course']['credit']) <= ((int) ((DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER * CREDIT_TO_ECTS) + ADDITIONAL_ECTS_ALLOWED_FOR_GRADUATING_STUDENTS)))) {
																	$enable_auto_rejection_override = true;
																}
															}
														}
														if (isset($courseAdd['Student']['graduated']) && $courseAdd['Student']['graduated'] == 0 && isset($courseAdd['PublishedCourse']['Course']['id']) && $this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT && isset($courseAdd['PublishedCourse']['id']) && ClassRegistry::init('ExamGrade')->isGradeSubmittedForPublishedCourse($courseAdd['PublishedCourse']['id']) == 0) {
															if ($enable_auto_rejection_override && ENABE_AUTO_COURSE_ADD_REJECTION_OVERRIDE_FOR_DEPARTMENTS == 1) {
																//echo '<br>'. $this->Form->postLink(__('[Approve Anyway]'), array('action' => 'approve_auto_rejected_course_add', $courseAdd['CourseAdd']['id']), array('confirm' => __('Are you sure you want to cancel the auto rejected course add of %s for %s? Cancelling this auto rejection will auto approve the Course Add Request. Are you sure you want cancel the auto rejection anyway?', $courseAdd['Student']['full_name'] . ' ('.  $courseAdd['Student']['studentnumber'] . ')', $courseAdd['PublishedCourse']['Course']['course_title'] . ' (' .  $courseAdd['PublishedCourse']['Course']['course_code']. ') course  with ' . $courseAdd['PublishedCourse']['Course']['credit']. ' ' . (count(explode('ECTS', $courseAdd['PublishedCourse']['Course']['Curriculum']['type_credit'])) >= 2  ? 'ECTS' : 'Credit'))));
																echo '<br>'. $this->Html->link(__('[Approve Anyway]'), array('action' => 'approve_auto_rejected_course_add', $courseAdd['CourseAdd']['id']), array('escape' => false, 'onclick' => 'return confirm(\'' . $confirmMessage . '\');')); 
															}
														} else if (isset($courseAdd['Student']['graduated']) && $courseAdd['Student']['graduated'] == 0 && ClassRegistry::init('ExamGrade')->isGradeSubmittedForPublishedCourse($courseAdd['PublishedCourse']['id'])) {
															echo '<br><span class="on-process">Grade Submitted</span>';
															$grade_submitted_for_course++;
														}
													}
												}
											} ?>
										</td>
										<td class="center">
											<?php
											if (isset($courseAdd['Student']['graduated']) && $courseAdd['Student']['graduated'] == 1) {
												echo '<span class="on-process">Graduated Student</span>';
											} else if (!$courseAdd['CourseAdd']['auto_rejected']) {
												if ($courseAdd['CourseAdd']['department_approval'] == 1) {
													if (is_null($courseAdd['CourseAdd']['registrar_confirmation'])) {
														if ((isset($courseAdd['PublishedCourse']['Section']['archive']) && $courseAdd['PublishedCourse']['Section']['archive']) || (isset($allowed_academic_years_for_add_drop) && !empty($allowed_academic_years_for_add_drop) && !in_array($courseAdd['CourseAdd']['academic_year'], $allowed_academic_years_for_add_drop))) {
															echo '<span class="rejected">Expired</span>';
														} else {
															echo '<span class="text-gray"><i>Waiting Decision</i></span>';
														}
													} else if ($courseAdd['CourseAdd']['registrar_confirmation'] == 1) {
														echo '<span class="accepted">Accepted</span>';
													} else if ($courseAdd['CourseAdd']['registrar_confirmation'] == 0) {
														echo '<span class="rejected">Rejected</span>';
													}
												}
											} else {
												echo '<span class="rejected">Auto Rejected (System)</span>';
												if (isset($this->data['Search']['academic_year']) && isset($this->data['Search']['semester']) && $this->data['Search']['academic_year'] == $courseAdd['CourseAdd']['academic_year'] && $this->data['Search']['semester'] == $courseAdd['CourseAdd']['semester']) {
													$confirmMessage = __('Are you sure you want to cancel the auto rejected course add of %s for %s? Cancelling this auto rejection will auto confirm the course add request. Are you sure you want to cancel the auto rejection anyway?', $student_full_name, $courseTitleWithCredit);
													if (isset($current_load) && isset($courseAdd['Student']['id']) && isset($current_load[$courseAdd['Student']['id']])) {
														echo '<br>Load: ' . $current_load[$courseAdd['Student']['id']] . ' ' . $credit_type;
														if (isset($graduatingClassStudent) && $graduatingClassStudent[$courseAdd['Student']['id']] && $credit_type == 'Credit') {
															if (isset($courseAdd['PublishedCourse']['Course']['credit']) && $courseAdd['PublishedCourse']['Course']['credit'] >= 0 && (($current_load[$courseAdd['Student']['id']] + $courseAdd['PublishedCourse']['Course']['credit']) <= (DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER + ADDITIONAL_CREDIT_ALLOWED_FOR_GRADUATING_STUDENTS))) {
																$enable_auto_rejection_override = true;
															}
														} else if (isset($graduatingClassStudent) && $graduatingClassStudent[$courseAdd['Student']['id']] && $credit_type == 'ECTS') {
															if (isset($courseAdd['PublishedCourse']['Course']['credit']) && $courseAdd['PublishedCourse']['Course']['credit'] >= 0 && (($current_load[$courseAdd['Student']['id']] + $courseAdd['PublishedCourse']['Course']['credit']) <= ((int) ((DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER * CREDIT_TO_ECTS) + ADDITIONAL_ECTS_ALLOWED_FOR_GRADUATING_STUDENTS)))) {
																$enable_auto_rejection_override = true;
															}
														}
													}
													if (isset($courseAdd['PublishedCourse']['add']) && !($courseAdd['PublishedCourse']['add']) && isset($courseAdd['PublishedCourse']['Section']['archive']) && !($courseAdd['PublishedCourse']['Section']['archive']) && isset($courseAdd['Student']['graduated']) && $courseAdd['Student']['graduated'] == 0 && isset($courseAdd['PublishedCourse']['Course']['id']) && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1 && isset($courseAdd['PublishedCourse']['id']) && !ClassRegistry::init('ExamGrade')->isGradeSubmittedForPublishedCourse($courseAdd['PublishedCourse']['id'])) {
														if ($enable_auto_rejection_override && ENABE_AUTO_COURSE_ADD_REJECTION_OVERRIDE_FOR_REGISTRAR) {
															//echo '<br>'. $this->Form->postLink('[Confirm Anyway]', array('action' => 'approve_auto_rejected_course_add', $courseAdd['CourseAdd']['id']), array('escape' => false, 'confirm' => __( $confirmMessage)));
															echo '<br>'. $this->Html->link(__('[Confirm Anyway]'), array('action' => 'approve_auto_rejected_course_add', $courseAdd['CourseAdd']['id']), array('escape' => false, 'onclick' => 'return confirm(\'' . $confirmMessage . '\');')); 
														}
													} else if (isset($courseAdd['Student']['graduated']) && $courseAdd['Student']['graduated'] == 0 && isset($courseAdd['PublishedCourse']['Course']['id']) && $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1 && isset($courseAdd['PublishedCourse']['id'])) {
														//echo '<br>'. $this->Form->postLink('[Confirm Anyway]', array('action' => 'approve_auto_rejected_course_add', $courseAdd['CourseAdd']['id']), array('escape' => false, 'confirm' => __( $confirmMessage)));
														echo '<br>'. $this->Html->link(__('[Confirm Anyway]'), array('action' => 'approve_auto_rejected_course_add', $courseAdd['CourseAdd']['id']), array('escape' => false, 'onclick' => 'return confirm(\'' . $confirmMessage . '\');')); 
														if (ClassRegistry::init('ExamGrade')->isGradeSubmittedForPublishedCourse($courseAdd['PublishedCourse']['id'])) {
															echo '<br><span class="on-process">Grade Submitted</span><br>';
															$grade_submitted_for_course++;
														}
													} else if (isset($courseAdd['Student']['graduated']) && $courseAdd['Student']['graduated'] == 0 && ClassRegistry::init('ExamGrade')->isGradeSubmittedForPublishedCourse($courseAdd['PublishedCourse']['id'])) {
														echo '<br><span class="on-process">Grade Submitted</span>';
														$grade_submitted_for_course++;
													}
												}
											} ?>
											<?php
											if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) { ?>
												<td class="center">
													<?php
													if (isset($courseAdd['PublishedCourse']['add']) && !($courseAdd['PublishedCourse']['add']) && isset($courseAdd['PublishedCourse']['Section']['archive']) && !($courseAdd['PublishedCourse']['Section']['archive']) && is_null($courseAdd['CourseAdd']['department_approval']) && is_null($courseAdd['CourseAdd']['registrar_confirmation']) && !($courseAdd['CourseAdd']['auto_rejected']) && !($courseAdd['CourseAdd']['cron_job']) && !ClassRegistry::init('ExamGrade')->isGradeSubmittedForPublishedCourse($courseAdd['PublishedCourse']['id'])) {
														$confirmMessage = __('Are you sure you want to cancel course add of %s, you requested previously? Cancelling this course add will delete your course add request and you can not add this course again if course add deadline is passed. Are you sure you want cancel this course add anyway?', $courseTitleWithCredit);
														//echo $this->Form->postLink('Cancel Course Add', array('action' => 'delete', $courseAdd['CourseAdd']['id']), array('escape' => false, 'confirm' => __( $confirmMessage)));
														echo $this->Html->link(__('Cancel Add'), array('action' => 'delete', $courseAdd['CourseAdd']['id']), array('escape' => false, 'onclick' => 'return confirm(\'' . $confirmMessage . '\');'));
													} ?>
												</td>
												<?php
											} ?>
										</td>
									</tr>
									<tr id="c<?= $count++; ?>" style="display:none">
										<td colspan="2" style="background-color: white;"> </td>
										<td colspan=<?= $this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT ? '11': '10' ?> style="background-color: white;">
											<?php
											if (isset($courseAdd['PublishedCourse']['Course']) && !empty($courseAdd['PublishedCourse']['Course']['id'])) { ?>
												<table cellpadding="0" cellspacing="0" class="table">
													<tbody>
														<tr>
															<td class="vcenter" style="background-color: white;">
																<span class="fs13 text-gray" style="font-weight: bold">Added from Section: </span> <?= ($courseAdd['PublishedCourse']['Section']['name'] . ' (' . (isset($courseAdd['PublishedCourse']['Section']['YearLevel']['name']) ? $courseAdd['PublishedCourse']['Section']['YearLevel']['name'] : 'Pre/1st') . ', ' . $courseAdd['PublishedCourse']['Section']['academicyear'] . ')'); ?>  &nbsp; <?= ($courseAdd['PublishedCourse']['Section']['archive'] ? '<span class="rejected"> (Archieved) </span>' : '<span class="accepted"> (Active) </span>' ); ?>
															</td>
														</tr>
														<tr>
															<td class="vcenter">
																<span class="fs13 text-gray" style="font-weight: bold">Section/Course Curriculum: </span> <?= (isset($courseAdd['PublishedCourse']['Section']['Curriculum']['name']) ? $courseAdd['PublishedCourse']['Section']['Curriculum']['name'] . ' - ' . $courseAdd['PublishedCourse']['Section']['Curriculum']['year_introduced'] :(isset($courseAdd['PublishedCourse']['Course']['Curriculum']['name']) ? $courseAdd['PublishedCourse']['Course']['Curriculum']['name'] . ' - ' . $courseAdd['PublishedCourse']['Course']['Curriculum']['year_introduced'] : ''));  ?>
															</td>
														</tr>
														<tr>
															<td class="vcenter" style="background-color: white;">
																<span class="fs13 text-gray" style="font-weight: bold">Section Department/College: </span> <?= (isset($courseAdd['PublishedCourse']['Department']['name']) ? $courseAdd['PublishedCourse']['Department']['name'] . ' ('  . $courseAdd['PublishedCourse']['Department']['College']['name']. ')' : (isset($courseAdd['PublishedCourse']['College']['name']) ? 'Pre/Freshman (' . $courseAdd['PublishedCourse']['College']['name'] . ')' : '')); ?>
															</td>
														</tr>
														<tr>
															<td class="vcenter">
																<span class="fs13 text-gray" style="font-weight: bold">Course Given By: </span> <?= (isset($courseAdd['PublishedCourse']['GivenByDepartment']['name']) ? $courseAdd['PublishedCourse']['GivenByDepartment']['name']  : 'Not Assigned Yet'); ?>
															</td>
														</tr>
														<tr>
															<td class="vcenter" style="background-color: white;">
																<span class="fs13 text-gray" style="font-weight: bold">Student Attached Curriculum: </span> <?= (!empty($courseAdd['Student']['Curriculum']['name']) ? $courseAdd['Student']['Curriculum']['name'] . ' - ' . $courseAdd['Student']['Curriculum']['year_introduced'] : '<span class="Rejected">No Curriculum Attachement</span>'); ?>
															</td>
														</tr>
														<tr>
															<td class="vcenter">
																<span class="fs13 text-gray" style="font-weight: bold">Student Graduated: </span> <?= ($courseAdd['Student']['graduated'] ? '<span class="rejected"> Yes </span>': '<span class="accepted"> No </span>'); ?>
															</td>
														</tr>
														<?= (!empty($courseAdd['CourseAdd']['minute_number']) ? '<tr><td class="vcenter" style="background-color: white;"><span class="fs13 text-gray" style="font-weight: bold">Minute Number:  </span>' .$courseAdd['CourseAdd']['minute_number'] . '</td></tr>' : '');  ?>
														<tr>
															<td class="vcenter" style="background-color: white;">
																<span class="fs13 text-gray" style="font-weight: bold">Course Add Requested: </span> <?= $this->Time->format("F j, Y h:i:s A", $courseAdd['CourseAdd']['created'], NULL, NULL); ?>
															</td>
														</tr>
														<tr>
															<td class="vcenter">
																<span class="fs13 text-gray" style="font-weight: bold">Course Add <?= (($courseAdd['CourseAdd']['auto_rejected'] == 0 && ($courseAdd['CourseAdd']['department_approval'] == 1 || $courseAdd['CourseAdd']['registrar_confirmation'] == 1)) ? ' Approved' . ($courseAdd['CourseAdd']['registrar_confirmation'] == 1 ? ' By Registrar' : ' By Department'). ':  </span> ' . $this->Time->format("F j, Y h:i:s A", $courseAdd['CourseAdd']['modified'], NULL, NULL) : ((($courseAdd['CourseAdd']['auto_rejected'] == 1 || $courseAdd['CourseAdd']['department_approval'] == 0 || $courseAdd['CourseAdd']['registrar_confirmation'] == 0) ? '' . ($courseAdd['CourseAdd']['auto_rejected'] == 1 ? ' Auto Rejected By System' : ($courseAdd['CourseAdd']['registrar_confirmation'] == 0 ? ' Rejected By Registrar' : 'Rejected By Department')) . ':  </span> ' . $this->Time->format("F j, Y h:i:s A", $courseAdd['CourseAdd']['modified'], NULL, NULL) : ' Approval:  </span> Waiting ... '))); ?>
															</td>
														</tr>
														<?= (!empty($courseAdd['CourseAdd']['reason']) ? '<tr><td class="vcenter" style="background-color: white;"><span class="fs13 text-gray" style="font-weight: bold">Reason:  </span>' . $courseAdd['CourseAdd']['reason'] . '</td></tr>' : '');  ?>
													</tbody>
												</table>
												<?php
											} else { ?>
												<span class="rejected">Error: Published Course not found or deleted. Could't load Course details!!.</span>
												<?php
											} ?>
										</td>
									</tr>
									<?php
								} ?>
							</tbody>
						</table>
					</div>
					<br>

					<?php
					if ($grade_submitted_for_course != 0) { ?>
						<div class='info-box' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">Grade Submitted: one or more grade is submitted for a student or students for the published course section, not possible to process your course add request at this time.</div>
						<?php
					} ?>

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
					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no Course Add in the system in the given criteria.</div>
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