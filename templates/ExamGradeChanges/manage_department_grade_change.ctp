<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Exam Grade Change, Makeup & Supplementary Exam Approval') . (isset($years_to_look_list_for_display) ? ' (' . $years_to_look_list_for_display . ')' : ''); ?></span>
        </div>
    </div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>
				
				<?php $st_count = 1; ?>
				<?= $this->Form->create('ExamGradeChange');

				if (!empty($makeup_exam_grade_changes) || !empty($exam_grade_changes) || !empty($rejected_makeup_exam_grade_changes) || !empty($rejected_department_makeup_exam_grade_changes)) { ?>
					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal;"><span style='margin-right: 15px;'></span>Approving/rejecting individual grade change is possible by expanding <b>'+'</b> icon, Choosing either accept or reject request by providing reason.</div>
					<?php
				}

				if (isset($exam_grade_changes) && !empty($exam_grade_changes)) { ?>
					<hr>
					<h6 class="fs16 text-gray">Exam Grade Change requests from instructors for approval</h6>
					<hr>

					<?php
					//debug($exam_grade_changes);
					foreach ($exam_grade_changes as $program_name => $program_grade_changes) {
						foreach ($program_grade_changes as $program_type_name => $program_type_grade_changes) { ?>
							<div style="overflow-x:auto;">
								<table cellpadding="0" cellspacing="0" class="table">
									<thead>
										<tr>
											<td colspan="9" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);">
												<!-- <span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?php //echo $department_name; ?></span>
													<br style="line-height: 0.5;">
													<span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold"> 
														<?php //echo $college_name; ?>
														<br style="line-height: 0.35;">
													</span>
												</span> -->
												<span class="text-gray" style="padding-top: 25px; font-size: 13px; font-weight: bold"> 
													<?= $program_name . ' &nbsp; | &nbsp; ' . $program_type_name; ?><br>
												</span>
											</td>
										</tr>
										<tr>
											<th style="width:2%; " class="center"><?= $this->Form->input('Mass.ExamGradeChange.select_all', array('disabled', 'type' => 'checkbox', 'id' => 'select-all', 'label' => false)); ?></th>
											<th style="width:3%;" class="center">#</th>
											<th style="width:3%;" class="center">&nbsp;</th>
											<th style="width:20%;" class="vcenter">Student Name</th>
											<th style="width:10%;" class="center">Student ID</th>
											<th style="width:29%;" class="center">Course</th>
											<th style="width:7%;" class="center">Previous</th>
											<th style="width:7%;" class="center">New</th>
											<th style="width:19%;" class="center">Request Date</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$counter = 1;
										foreach ($program_type_grade_changes as $key => $grade_change) { ?>
											<tr>
												<td class="center">
													<div style="margin-left: 12%;"><?= $this->Form->input('Mass.ExamGradeChange.' . $st_count . '.gp', array('disabled', 'type' => 'checkbox', 'label' => false, 'id' => 'ExamGradeChange' . $st_count, 'class' => 'checkbox1')); ?></div>
													<?= $this->Form->input('Mass.ExamGradeChange.' . $st_count . '.id', array('type' => 'hidden', 'value' => $grade_change['ExamGradeChange']['id'])); ?>
													<?php //echo $this->Form->input('Mass.ExamGradeChange.' . $st_count . '.department_approval', array('type' => 'hidden', 'value' => 1)); ?>
												</td>
												<td class="center"><?= $counter++; ?></td>
												<td onclick="toggleView(this)" id="<?= $st_count; ?>" class="center"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $st_count)); ?></td>
												<td class="vcenter"><?= $grade_change['Student']['first_name'] . ' ' . $grade_change['Student']['middle_name'] . ' ' . $grade_change['Student']['last_name']; ?></td>
												<td class="center"><?= $grade_change['Student']['studentnumber']; ?></td>
												<td class="center"><?= $grade_change['Course']['course_title'] . ' (' . $grade_change['Course']['course_code'] . ')'; ?></td>
												<td class="center"><?= $grade_change['latest_grade']; ?></td>
												<td class="center"><?= $grade_change['ExamGradeChange']['grade']; ?></td>
												<td class="center"><?= $this->Time->format("M j, Y g:i:s A", $grade_change['ExamGradeChange']['created'], NULL, NULL); ?></td>
											</tr>
											<tr id="c<?= $st_count; ?>" style="display:none">
												<td style="background-color:white;">&nbsp;</td>
												<td colspan="2" style="background-color:white;">&nbsp;</td>
												<td colspan="6" style="background-color:white;">
													<table cellpadding="0" cellspacing="0" class="table">
														<tr>
															<td style="font-weight:bold; background-color:white;" class="vcenter">Section: &nbsp; <?= $grade_change['Section']['name'] . ' (' . (isset($grade_change['Section']['YearLevel']['id']) && !empty($grade_change['Section']['YearLevel']['name']) ? $grade_change['Section']['YearLevel']['name'] : ($grade_change['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman')) . ', '.   $grade_change['Section']['academicyear'] . ')'; ?></td>
														</tr>
														<tr>
															<td style="font-weight:bold; background-color:white;" class="vcenter">Instructor: &nbsp; <?= $grade_change['Staff']['Title']['title'] . '. ' . $grade_change['Staff']['first_name'] . ' ' . $grade_change['Staff']['middle_name'] . ' ' . $grade_change['Staff']['last_name'] . ' (' . $grade_change['Staff']['Position']['position'] . ')'; ?></td>
														</tr>
														<tr>
															<td style="font-weight:bold; background-color:white;" class="vcenter">Reason for Change: &nbsp; <?= $grade_change['ExamGradeChange']['reason']; ?></td>
														</tr>
													</table>
													<br>
													<table cellpadding="0" cellspacing="0" class="table">
														<tr>
															<td style="vertical-align:top; width:60%; background-color:white;">
																<?php
																//debug($grade_change);
																$register_or_add = 'gh';
																if (isset($grade_change['ExamGradeHistory'])) {
																	$grade_history = $grade_change['ExamGradeHistory'];
																} else {
																	$grade_history = array();
																}

																if (is_null($grade_change['Section']['department_id'])) {
																	$freshman_program = true;
																} else {
																	$freshman_program = false;
																}

																$this->set(compact('register_or_add', 'grade_history', 'freshman_program'));
																echo $this->element('registered_or_add_course_grade_history'); ?>
																<br>
															</td>
															<td style="vertical-align:top; width:40%; background-color:white;">
																<?php
																if (!$grade_change['Student']['graduated']) { ?>
																	<table cellpadding="0" cellspacing="0" class="table">
																		<tr>
																			<td colspan="2">
																				<div style="font-weight:bold; font-size:14px">Grade Change Request Approval</div>
																			</td>
																		</tr>
																		<tr>
																			<td style="width:20%; background-color:white;">Decision:</td>
																			<td style="width:80%; background-color:white;">
																				<?php
																				echo $this->Form->input('ExamGradeChange.' . $st_count . '.id', array('type' => 'hidden', 'value' => $grade_change['ExamGradeChange']['id']));
																				$options = array('1' => ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT ? ' Accept (Forward to College)': ' Accept (Forward to REgistrar)') , '-1' => ' Reject (Send back to Instructor)');
																				$attributes = array('legend' => false, 'label' => false, 'separator' => "<br />", 'default' => 1);
																				echo $this->Form->radio('ExamGradeChange.' . $st_count . '.department_approval', $options, $attributes); ?>
																				<br>
																			</td>
																		</tr>
																		<tr>
																			<td style="background-color:white;">Remark:</td>
																			<td style="background-color:white;"><?= $this->Form->input('ExamGradeChange.' . $st_count . '.department_reason', array('label' => false, 'cols' => 40)); ?></td>
																		</tr>
																	</table>
																	<br>
																	<?= $this->Form->Submit('Approve/Reject Grade Change Request', array('div' => false, 'class' => 'tiny radius button bg-blue', 'name' => 'approveGradeChangeByDepartment_' . $st_count++)); ?>
																	<?php
																} else { ?>
																	<div class='warning-box warning-message'><span style='margin-right: 15px;'></span>Grade Change is not available for graduated student.</div>
																	<?php
																} ?>
															</td>
														</tr>
													</table>
													<br>
													<?php
													$student_exam_grade_change_history = $grade_change['ExamGradeHistory'];
													$student_exam_grade_history = $grade_change['ExamGrade'];
													$this->set(compact('student_exam_grade_history', 'student_exam_grade_change_history'));
													echo $this->element('registered_or_add_course_grade_detail_history'); ?>
													<br>
												</td>
											</tr>
											<?php
										} ?>
									</tbody>
								</table>
							</div>
							<hr>
							<?php
						}
					}
				} ?>

				<?php
				/************************************  MAKEUP EXAM **************************************/
				if (isset($makeup_exam_grade_changes) && !empty($makeup_exam_grade_changes)) { ?>
					<hr>
					<h6 class="fs16 text-gray">Makeup/Supplementary Exam Approval requests from Instructors</h6>
					<hr>

					<?php
					foreach ($makeup_exam_grade_changes as $program_name => $program_grade_changes) {
						foreach ($program_grade_changes as $program_type_name => $program_type_grade_changes) { ?>
							<div style="overflow-x:auto;">
								<table cellpadding="0" cellspacing="0" class="table">
									<thead>
										<tr>
											<td colspan="9" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);">
												<!-- <span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?php //echo $department_name; ?></span>
													<br style="line-height: 0.5;">
													<span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold"> 
														<?php //echo $college_name; ?>
														<br style="line-height: 0.35;">
													</span>
												</span> -->
												<span class="text-gray" style="padding-top: 25px; font-size: 13px; font-weight: bold"> 
													<?= $program_name . ' &nbsp; | &nbsp; ' . $program_type_name; ?><br>
												</span>
											</td>
										</tr>
										<tr>
											<th style="width:4%;" class="center">#</th>
											<th style="width:3%;" class="center">&nbsp;</th>
											<th style="width:15%;" class="vcenter">Student Name</th>
											<th style="width:9%;" class="center">Student ID</th>
											<th style="width:23%;" class="center">Exam Taken for</th>
											<th style="width:23%;" class="center">Exam Course</th>
											<th style="width:5%;" class="center">Grade</th>
											<th style="width:18%;" class="center">Request Date</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$cntr = 1;
										foreach ($program_type_grade_changes as $key => $grade_change) { ?>
											<tr>
												<td class="center"><?= $cntr++; ?></td>
												<td onclick="toggleView(this)" id="<?= $st_count; ?>" class="center"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $st_count)); ?></td>
												<td class="vcenter"><?= $grade_change['Student']['first_name'] . ' ' . $grade_change['Student']['middle_name'] . ' ' . $grade_change['Student']['last_name']; ?></td>
												<td class="center"><?= $grade_change['Student']['studentnumber']; ?></td>
												<td class="center"><?= $grade_change['Course']['course_title'] . ' (' . $grade_change['Course']['course_code'] . ')'; ?></td>
												<td class="center"><?= $grade_change['ExamCourse']['course_title'] . ' (' . $grade_change['ExamCourse']['course_code'] . ')'; ?></td>
												<td class="center"><?= $grade_change['ExamGradeChange']['grade']; ?></td>
												<td class="center"><?= $this->Time->format("M j, Y g:i:s A", $grade_change['ExamGradeChange']['created'], NULL, NULL); ?></td>
											</tr>
											<tr id="c<?= $st_count; ?>" style="display:none">
												<td style="background-color: white;">&nbsp;</td>
												<td style="background-color: white;">&nbsp;</td>
												<td colspan="6" style="background-color: white;">
													<?php
													if (!isset($grade_change['MakeupExam'])) { ?>
														<table cellpadding="0" cellspacing="0" class="table">
															<?php
															//debug($grade_change);
															if ($grade_change['ExamGradeChange']['initiated_by_department'] == 1) { ?>
																<tr>
																	<td class="fs14" style="font-weight:bold;" class="vcenter"><span class="text-red">Importnat Note: This exam grade change is requested by the department, not by the course instructor!.</span></td>
																</tr>
																<?php
																if (!empty($grade_change['ExamGradeChange']['department_approved_by'])) { ?>
																	<tr>
																		<td class="fs14" style="font-weight:bold;" class="vcenter"><span>Grade Change Initiated by: <?= ClassRegistry::init('User')->field('User.full_name', array('User.id' => $grade_change['ExamGradeChange']['department_approved_by'])); ?></span></td>
																	</tr>
																	<?php
																}
															} ?>
															<tr>
																<td style="sont-weight:bold; background-color: white;" class="vcenter">Section: &nbsp; <?= $grade_change['Section']['name'] . ' (' . (isset($grade_change['Section']['YearLevel']['id']) && !empty($grade_change['Section']['YearLevel']['name']) ? $grade_change['Section']['YearLevel']['name'] : ($grade_change['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman')) . ', '.   $grade_change['Section']['academicyear'] . ')'; ?></td>
															</tr>
															<tr>
																<td style="font-weight:bold; background-color: white;" class="vcenter">Instructor: &nbsp; <?= (isset($grade_change['Staff']) && !empty($grade_change['Staff']) ? $grade_change['Staff']['first_name'] . ' ' . $grade_change['Staff']['middle_name'] . ' ' . $grade_change['Staff']['last_name'] : '---'); ?></td>
															</tr>
															<tr>
																<td style="font-weight:bold; background-color: white;" class="vcenter">Reason for Change: &nbsp; <?= $grade_change['ExamGradeChange']['reason']; ?></td>
															</tr>
														</table>
														<br>
														<?php
													} else { ?>
														<table cellpadding="0" cellspacing="0" class="table">
															<?php
															//debug($grade_change);
															if ($grade_change['ExamGradeChange']['initiated_by_department'] == 1) { ?>
																<tr>
																	<td class="fs14" style="font-weight:bold;" class="vcenter"><span class="text-red">Importnat Note: This exam grade change is requested by the department, not by the course instructor!.</span></td>
																</tr>
																<?php
																if (!empty($grade_change['ExamGradeChange']['department_approved_by'])) { ?>
																	<tr>
																		<td class="fs14" style="font-weight:bold;" class="vcenter"><span>Grade Change Initiated by: <?= ClassRegistry::init('User')->field('User.full_name', array('User.id' => $grade_change['ExamGradeChange']['department_approved_by'])); ?></span></td>
																	</tr>
																	<?php
																}
															} ?>
															<tr>
																<td style="font-weight:bold; background-color: white;" class="vcenter">Minute Number: &nbsp; <?= $grade_change['MakeupExam']['minute_number']; ?></td>
															</tr>
															<tr>
																<td style="font-weight:bold; background-color: white;" class="vcenter">Exam Section: &nbsp; <?= $grade_change['ExamSection']['name'] . ' (' . (isset($grade_change['ExamSection']['YearLevel']['id']) && !empty($grade_change['ExamSection']['YearLevel']['name']) ? $grade_change['ExamSection']['YearLevel']['name'] : ($grade_change['ExamSection']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman')) . ', '.   $grade_change['ExamSection']['academicyear'] . ')'; ?></td>
															</tr>
															<tr>
																<td style="font-weight:bold; background-color: white;" class="vcenter">Exam Given By: &nbsp; <?= $grade_change['Staff']['first_name'] . ' ' . $grade_change['Staff']['middle_name'] . ' ' . $grade_change['Staff']['last_name']; ?></td>
															</tr>
														</table>
														<br>
														<?php
													} ?>
													<table cellpadding="0" cellspacing="0" class="table">
														<tr>
															<td style="vertical-align:top; width:60%; ; background-color: white;">
																<?php
																//debug($grade_change);
																$register_or_add = 'gh';
																if (isset($grade_change['ExamGradeHistory'])) {
																	$grade_history = $grade_change['ExamGradeHistory'];
																} else {
																	$grade_history = array();
																}
																$this->set(compact('register_or_add', 'grade_history'));
																echo $this->element('registered_or_add_course_grade_history'); ?>
																<br>
															</td>
															<td style="vertical-align:top; width:40%; background-color: white;">
																<table cellpadding="0" cellspacing="0" class="table">
																	<tr>
																		<td colspan="2">
																			<div style="font-weight:bold; font-size:14px">Grade Change Request Approval</div>
																		</td>
																	</tr>
																	<tr>
																		<td style="width:20%; background-color: white;">Decision:</td>
																		<td style="width:80%; background-color: white;">
																			<?php
																			echo $this->Form->input('ExamGradeChange.' . $st_count . '.id', array('type' => 'hidden', 'value' => $grade_change['ExamGradeChange']['id']));
																			$options = array('1' => 'Accept (Forward to College)', '-1' => 'Reject (Back to Instructor)');
																			$attributes = array('legend' => false, 'label' => false, 'separator' => "<br />", 'default' => 1);
																			echo $this->Form->radio('ExamGradeChange.' . $st_count . '.department_approval', $options, $attributes); ?>
																			<br>
																		</td>
																	</tr>
																	<tr>
																		<td style="background-color: white;">Remark:</td>
																		<td style="background-color: white;"><?= $this->Form->input('ExamGradeChange.' . $st_count . '.department_reason', array('label' => false, 'cols' => 40)); ?></td>
																	</tr>
																</table>
																<br>
																<?= $this->Form->Submit('Approve/Reject Grade Change Request', array('div' => false, 'class' => 'tiny radius button bg-blue', 'name' => 'approveGradeChangeByDepartment_' . $st_count++)); ?>
															</td>
														</tr>
													</table>
													<br>
													<?php
													$student_exam_grade_change_history = $grade_change['ExamGradeHistory'];
													$student_exam_grade_history = $grade_change['ExamGrade'];
													$this->set(compact('student_exam_grade_history', 'student_exam_grade_change_history'));
													echo $this->element('registered_or_add_course_grade_detail_history'); ?>
													<br>
												</td>
											</tr>
											<?php
										} ?>
									</tbody>
								</table>
							</div>
							<hr>
							<?php
						}
					} 
				} ?>

				<?php
				/**************************** REJECTED MAKEUP EXAM GRADE CHANGE ***********************************/
				if (isset($rejected_makeup_exam_grade_changes) && !empty($rejected_makeup_exam_grade_changes)) { ?>
					
					<hr>
					<h6 class="fs16 text-red">Rejected Makeup/Supplementary Exam Grades from the Registrar</h6>
					<!-- <p class="fs13">You are required either to reject the makeup exam so that the instructor can modify the result and resubmit the grade again or to accept it again (even if it is rejected by the registrar) so that the registrar can consider the makeup exam grade based on the remark you sent to them.</p> -->
					<hr>

					<?php
					foreach ($rejected_makeup_exam_grade_changes as $program_name => $program_grade_changes) {
						foreach ($program_grade_changes as $program_type_name => $program_type_grade_changes) { ?>
							<div style="overflow-x:auto;">
								<table cellpadding="0" cellspacing="0" class="table">
									<thead>
										<tr>
											<td colspan="8" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);">
												<!-- <span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?php //echo $department_name; ?></span>
													<br style="line-height: 0.5;">
													<span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold"> 
														<?php //echo $college_name; ?>
														<br style="line-height: 0.35;">
													</span>
												</span> -->
												<span class="text-gray" style="padding-top: 25px; font-size: 13px; font-weight: bold"> 
													<?= $program_name . ' &nbsp; | &nbsp; ' . $program_type_name; ?><br>
												</span>
											</td>
										</tr>
										<tr>
											<th style="width:3%;" class="center">&nbsp;</th>
											<th style="width:15%;" class="vcenter">Student Name</th>
											<th style="width:9%;" class="center">Student ID</th>
											<th style="width:25%;" class="center">Exam Taken for</th>
											<th style="width:25%;" class="center">Exam Course</th>
											<th style="width:5%;" class="center">Grade</th>
											<th style="width:18%;" class="center">Request Date</th>
										</tr>
									</thead>
									<tbody>
										<?php
										foreach ($program_type_grade_changes as $key => $grade_change) { ?>
											<tr>
												<td onclick="toggleView(this)" id="<?= $st_count; ?>" class="center"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $st_count)); ?></td>
												<td class="vcenter"><?= $grade_change['Student']['first_name'] . ' ' . $grade_change['Student']['middle_name'] . ' ' . $grade_change['Student']['last_name']; ?></td>
												<td class="center"><?= $grade_change['Student']['studentnumber']; ?></td>
												<td class="center"><?= $grade_change['Course']['course_title'] . ' (' . $grade_change['Course']['course_code'] . ')'; ?></td>
												<td class="center"><?= $grade_change['ExamCourse']['course_title'] . ' (' . $grade_change['ExamCourse']['course_code'] . ')'; ?></td>
												<td class="center"><?= $grade_change['ExamGradeChange']['grade']; ?></td>
												<td class="center"><?= $this->Time->format("M j, Y g:i:s A", $grade_change['ExamGradeChange']['created'], NULL, NULL); ?></td>
											</tr>
											<tr id="c<?= $st_count; ?>" style="display:none">
												<td style="background-color: white;">&nbsp;</td>
												<td colspan="6" style="background-color: white;">
													<?php
													if (!isset($grade_change['MakeupExam'])) { ?>
														<table cellpadding="0" cellspacing="0" class="table">
															<?php
															//debug($grade_change);
															if ($grade_change['ExamGradeChange']['initiated_by_department'] == 1) { ?>
																<tr>
																	<td class="fs14" style="font-weight:bold;" class="vcenter"><span class="text-red">Importnat Note: This exam grade change is requested by the department, not by the course instructor!.</span></td>
																</tr>
																<?php
																if (!empty($grade_change['ExamGradeChange']['department_approved_by'])) { ?>
																	<tr>
																		<td class="fs14" style="font-weight:bold;" class="vcenter"><span>Grade Change Initiated by: <?= ClassRegistry::init('User')->field('User.full_name', array('User.id' => $grade_change['ExamGradeChange']['department_approved_by'])); ?></span></td>
																	</tr>
																	<?php
																}
															} ?>
															<tr>
																<td style="font-weight:bold; background-color: white;" class="vcenter">Section: &nbsp; <?= $grade_change['Section']['name'] . ' (' . (isset($grade_change['Section']['YearLevel']['id']) && !empty($grade_change['Section']['YearLevel']['name']) ? $grade_change['Section']['YearLevel']['name'] : ($grade_change['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman')) . ', '.   $grade_change['Section']['academicyear'] . ')'; ?></td>
															</tr>
															<tr>
																<td style="font-weight:bold; background-color: white;" class="vcenter">Instructor: &nbsp; <?= (isset($grade_change['Staff']) && !empty($grade_change['Staff']) ? $grade_change['Staff']['first_name'] . ' ' . $grade_change['Staff']['middle_name'] . ' ' . $grade_change['Staff']['last_name'] : '---'); ?></td>
															</tr>
															<tr>
																<td style="font-weight:bold; background-color: white;" class="vcenter">Reason for Change: &nbsp; <?= $grade_change['ExamGradeChange']['reason']; ?></td>
															</tr>
														</table>
														<br>
														<?php
													} else { ?>
														<table cellpadding="0" cellspacing="0" class="table">
															<?php
															//debug($grade_change);
															if ($grade_change['ExamGradeChange']['initiated_by_department'] == 1) { ?>
																<tr>
																	<td class="fs14" style="font-weight:bold;" class="vcenter"><span class="text-red">Importnat Note: This exam grade change is requested by the department, not by the course instructor!.</span></td>
																</tr>
																<?php
																if (!empty($grade_change['ExamGradeChange']['department_approved_by'])) { ?>
																	<tr>
																		<td class="fs14" style="font-weight:bold;" class="vcenter"><span>Grade Change Initiated by: <?= ClassRegistry::init('User')->field('User.full_name', array('User.id' => $grade_change['ExamGradeChange']['department_approved_by'])); ?></span></td>
																	</tr>
																	<?php
																}
															} ?>
															<tr>
																<td style="font-weight:bold; background-color: white;" class="vcenter">Minute Number: &nbsp; <?= $grade_change['MakeupExam']['minute_number']; ?></td>
															</tr>
															<tr>
																<td style="font-weight:bold; background-color: white;" class="vcenter">Exam Section: &nbsp; <?= $grade_change['ExamSection']['name'] . ' (' . (isset($grade_change['ExamSection']['YearLevel']['id']) && !empty($grade_change['ExamSection']['YearLevel']['name']) ? $grade_change['ExamSection']['YearLevel']['name'] : ($grade_change['ExamSection']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman')) . ', '.   $grade_change['ExamSection']['academicyear'] . ')'; ?></td>
															</tr>
															<tr>
																<td style="font-weight:bold; background-color: white;" class="vcenter">Exam Given By: &nbsp; <?= $grade_change['Staff']['first_name'] . ' ' . $grade_change['Staff']['middle_name'] . ' ' . $grade_change['Staff']['last_name']; ?></td>
															</tr>
															<tr>
																<td style="font-weight:bold; background-color: white;" class="vcenter">Registrar Remark: &nbsp; <?= ($grade_change['ExamGradeChange']['registrar_reason'] != "" ? $grade_change['ExamGradeChange']['registrar_reason'] : '---'); ?></td>
															</tr>
														</table>
														<br>
														<?php
													} ?>
													<table cellpadding="0" cellspacing="0" class="table">
														<tr>
															<td style="vertical-align:top; width:60%; background-color: white;">
																<?php
																//debug($grade_change);
																$register_or_add = 'gh';
																if (isset($grade_change['ExamGradeHistory'])) {
																	$grade_history = $grade_change['ExamGradeHistory'];
																} else {
																	$grade_history = array();
																}

																$this->set(compact('register_or_add', 'grade_history'));
																echo $this->element('registered_or_add_course_grade_history'); ?>
																<br>
															</td>
															<td style="vertical-align:top; width:40%; background-color: white;">
																<table cellpadding="0" cellspacing="0" class="table">
																	<tr>
																		<th colspan="2">
																			<div style="font-weight:bold; font-size:14px">Grade Change Request Approval</div>
																		</th>
																	</tr>
																	<tr>
																		<td style="width:20%; background-color: white;">Decision:</td>
																		<td style="width:80%; background-color: white;">
																			<?php
																			echo $this->Form->input('ExamGradeChange.' . $st_count . '.id', array('type' => 'hidden', 'value' => $grade_change['ExamGradeChange']['id']));
																			$options = array('1' => ' Accept(Send to Instructor for correction)', '-1' => ' Reject(Back to registrar, Result is Correct)');
																			$attributes = array('legend' => false, 'label' => false, 'separator' => "<br />", 'default' => 1);
																			echo $this->Form->radio('ExamGradeChange.' . $st_count . '.department_approval', $options, $attributes); ?>
																			<br>
																		</td>
																	</tr>
																	<tr>
																		<td style="background-color: white;">Remark:</td>
																		<td  style="background-color: white;"><?= $this->Form->input('ExamGradeChange.' . $st_count . '.department_reason', array('label' => false, 'cols' => 40)); ?></td>
																	</tr>
																</table>
																<br>
																<?= $this->Form->Submit('Approve/Reject Grade Change Request', array('div' => false, 'class' => 'tiny radius button bg-blue', 'name' => 'approveGradeChangeByDepartment_' . $st_count++)); ?>
															</td>
														</tr>
													</table>
													<?php
													$student_exam_grade_change_history = $grade_change['ExamGradeHistory'];
													$student_exam_grade_history = $grade_change['ExamGrade'];
													$this->set(compact('student_exam_grade_history', 'student_exam_grade_change_history'));
													echo $this->element('registered_or_add_course_grade_detail_history'); ?>
													<br>
												</td>
											</tr>
											<?php
										} ?>
									</tbody>
								</table>
							</div>
							<hr>
							<?php
						}
					}
				} ?>

				<?php
				/************ SUPLEMENTARY EXAM REQUESTED BY THE DEPARTMENT BUT REJECTED BY REGISTRAR ************/
				if (isset($rejected_department_makeup_exam_grade_changes) && !empty($rejected_department_makeup_exam_grade_changes)) {
					
					$key_1 = array_keys($rejected_department_makeup_exam_grade_changes);
					$key_2 = array_keys($rejected_department_makeup_exam_grade_changes[$key_1[0]]);
					$key_3 = array_keys($rejected_department_makeup_exam_grade_changes[$key_1[0]][$key_2[0]]);
					$key_4 = array_keys($rejected_department_makeup_exam_grade_changes[$key_1[0]][$key_2[0]][$key_3[0]]);

					if (is_null($rejected_department_makeup_exam_grade_changes[$key_1[0]][$key_2[0]][$key_3[0]][$key_4[0]][0]['Section']['department_id'])) {
						$freshman_program = true;
					} else {
						$freshman_program = false;
					} ?>

					<hr>
					<h6 class="fs16 text-red">Rejected Supplementary Exam Grades from Registrar<?php //echo 'which was directlly requested by the  '.($freshman_program ? 'freshman program' : 'department');  ?>.</h6>
					<hr>

					<?php
					foreach ($rejected_department_makeup_exam_grade_changes as $college_name => $college_grade_changes) {
						foreach ($college_grade_changes as $department_name => $department_grade_changes) {
							foreach ($department_grade_changes as $program_name => $program_grade_changes) {
								foreach ($program_grade_changes as $program_type_name => $program_type_grade_changes) { ?>
									<div style="overflow-x:auto;">
										<table cellpadding="0" cellspacing="0" class="table">
											<thead>
												<tr>
													<td colspan="9" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);">
														<!-- <span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?php //echo $department_name; ?></span>
															<br style="line-height: 0.5;">
															<span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold"> 
																<?php //echo $college_name; ?>
																<br style="line-height: 0.35;">
															</span>
														</span> -->
														<span class="text-gray" style="padding-top: 25px; font-size: 13px; font-weight: bold"> 
															<?= $program_name . ' &nbsp; | &nbsp; ' . $program_type_name; ?><br>
														</span>
													</td>
												</tr>
												<tr>
													<th style="width:3%;" class="center">&nbsp;</th>
													<th style="width:3%;" class="center">#</th>
													<th style="width:20%;" class="vcenter">Student Name</th>
													<th style="width:9%;" class="center">Student ID</th>
													<th style="width:33%;" class="center">Course</th>
													<th style="width:7%;" class="center">Previous</th>
													<th style="width:7%;" class="center">New</th>
													<th style="width:18%;" class="center">Request Date</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$cntrr = 1;
												foreach ($program_type_grade_changes as $key => $grade_change) { ?>
													<tr>
														<td onclick="toggleView(this)" id="<?= $st_count; ?>" class="center"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $st_count)); ?></td>
														<td class="center"><?= $cntrr++; ?></td>
														<td class="vcenter"><?= $grade_change['Student']['first_name'] . ' ' . $grade_change['Student']['middle_name'] . ' ' . $grade_change['Student']['last_name']; ?></td>
														<td class="center"><?= $grade_change['Student']['studentnumber']; ?></td>
														<td class="center"><?= $grade_change['Course']['course_title'] . ' (' . $grade_change['Course']['course_code'] . ')'; ?></td>
														<td class="center"><?= $grade_change['latest_grade']; ?></td>
														<td class="center"><?= $grade_change['ExamGradeChange']['grade']; ?></td>
														<td class="center"><?= $this->Time->format("M j, Y g:i:s A", $grade_change['ExamGradeChange']['created'], NULL, NULL); ?></td>
													</tr>
													<tr id="c<?= $st_count; ?>" style="display:none">
														<td style="background-color: white;">&nbsp;</td>
														<td style="background-color: white;">&nbsp;</td>
														<td colspan="6" style="background-color: white;">
															<table cellpadding="0" cellspacing="0" class="table">
																<?php
																//debug($grade_change);
																if ($grade_change['ExamGradeChange']['initiated_by_department'] == 1) { ?>
																	<tr>
																		<td class="fs14" style="font-weight:bold;" class="vcenter"><span class="text-red">Importnat Note: This exam grade change is requested by the department, not by the course instructor!.</span></td>
																	</tr>
																	<?php
																	if (!empty($grade_change['ExamGradeChange']['department_approved_by'])) { ?>
																		<tr>
																			<td class="fs14" style="font-weight:bold;" class="vcenter"><span>Grade Change Initiated by: <?= ClassRegistry::init('User')->field('User.full_name', array('User.id' => $grade_change['ExamGradeChange']['department_approved_by'])); ?></span></td>
																		</tr>
																		<?php
																	}
																} ?>
																<tr>
																	<td style="font-weight:bold; background-color: white;" class="vcenter">Student Section: &nbsp; <?= $grade_change['Section']['name'] . ' (' . (isset($grade_change['Section']['YearLevel']['id']) && !empty($grade_change['Section']['YearLevel']['name']) ? $grade_change['Section']['YearLevel']['name'] : ($grade_change['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman')) . ', '.   $grade_change['Section']['academicyear'] . ')'; ?></td>
																</tr>
																<tr>
																	<td style="font-weight:bold; background-color: white;" class="vcenter">Course Instructor: &nbsp; <?= (isset($grade_change['Staff']) && !empty($grade_change['Staff']) ? $grade_change['Staff']['first_name'] . ' ' . $grade_change['Staff']['middle_name'] . ' ' . $grade_change['Staff']['last_name'] : '---'); ?></td>
																</tr>
																<?php
																if (!empty($grade_change['ExamGradeChange']['reason'])) { ?>
																	<tr>
																		<td style="font-weight:bold; background-color: white;" class="vcenter">Grade Change Reason: &nbsp; <?= $grade_change['ExamGradeChange']['reason']; ?></td>
																	</tr>
																	<?php
																}
																if (!empty($grade_change['ExamGradeChange']['department_reason'])) { ?>
																	<tr>
																		<td style="font-weight:bold; background-color: white;" class="vcenter">Department Remarks: &nbsp; <?= $grade_change['ExamGradeChange']['department_reason']; ?></td>
																	</tr>
																	<?php
																}
																if (!empty($grade_change['ExamGradeChange']['college_reason'])) { ?>
																	<tr>
																		<td style="font-weight:bold; background-color: white;" class="vcenter">College Remark: &nbsp; <?= $grade_change['ExamGradeChange']['college_reason']; ?></td>
																	</tr>
																	<?php
																}
																if (!empty($grade_change['ExamGradeChange']['registrar_reason'])) { ?>
																	<tr>
																		<td style="font-weight:bold; background-color: white;" class="vcenter">Registrar Remark: &nbsp; <?= $grade_change['ExamGradeChange']['registrar_reason']; ?></td>
																	</tr>
																	<?php
																} ?>
															</table>
															<br>
															<table cellpadding="0" cellspacing="0" class="table">
																<tr>
																	<td style="vertical-align:top; width:60%; background-color: white;">
																		<?php
																		//debug($grade_change);
																		$register_or_add = 'gh';
																		if (isset($grade_change['ExamGradeHistory'])) {
																			$grade_history = $grade_change['ExamGradeHistory'];
																		} else {
																			$grade_history = array();
																		}
																		$this->set(compact('register_or_add', 'grade_history'));
																		echo $this->element('registered_or_add_course_grade_history'); ?>
																		<br>
																	</td>
																	<td style="vertical-align:top; width:40%; background-color: white;">
																		<table cellpadding="0" cellspacing="0" class="table">
																			<tr>
																				<td colspan="2">
																					<div style="font-weight:bold; font-size:14px">Grade Change Request Approval</div>
																				</td>
																			</tr>
																			<tr>
																				<td style="width:20%; background-color: white;">Decision:</td>
																				<td style="width:80%; background-color: white;">
																					<?php
																					echo $this->Form->input('ExamGradeChange.' . $st_count . '.id', array('type' => 'hidden', 'value' => $grade_change['ExamGradeChange']['id']));
																					$options = array('1' => ' Accept(Send to Instructor for correction)', '-1' => ' Reject(Back to registrar, Result is Correct)');
																					$attributes = array('legend' => false, 'label' => false, 'separator' => "<br />", 'default' => 1);
																					echo $this->Form->radio('ExamGradeChange.' . $st_count . '.department_approval', $options, $attributes); ?>
																					<br>
																				</td>
																			</tr>
																			<tr>
																				<td style="background-color: white;">Remark:</td>
																				<td style="background-color: white;"><?= $this->Form->input('ExamGradeChange.' . $st_count . '.department_reason', array('label' => false, 'cols' => 40)); ?></td>
																			</tr>
																		</table>
																		<br>
																		<?= $this->Form->Submit('Approve/Reject Grade Change Request', array('div' => false, 'class' => 'tiny radius button bg-blue', 'name' => 'approveGradeChangeByDepartment_' . $st_count++)); ?>
																	</td>
																</tr>
															</table>
															<?php
															$student_exam_grade_change_history = $grade_change['ExamGradeHistory'];
															$student_exam_grade_history = $grade_change['ExamGrade'];
															$this->set(compact('student_exam_grade_history', 'student_exam_grade_change_history'));
															echo $this->element('registered_or_add_course_grade_detail_history'); ?>
															<br>
														</td>
													</tr>
													<?php
												} ?>
											</tbody>
										</table>
									</div>
									<hr>
									<?php
								}
							}
						}
					}
				}

				if (count($exam_grade_changes) > 1) { ?>
					<?php //echo $this->Form->Submit('Approve All Grade Change Request', array('div' => false, 'class' => 'tiny radius button bg-blue', 'name' => 'ApproveAllGradeChangeByDepartment')); ?></td>
					<?php 
				}

				echo $this->Form->input('grade_change_count', array('type' => 'hidden', 'value' => ($st_count - 1)));

				if (empty($makeup_exam_grade_changes) && empty($exam_grade_changes) && empty($rejected_makeup_exam_grade_changes) && empty($rejected_department_makeup_exam_grade_changes)) {
					if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no exam grade change request or makeup exam grade submission to approve <?= (!empty($years_to_look_list_for_display) ? $years_to_look_list_for_display : ''); ?>. Exam grade changes or makeup exams are required to be submitted by instructor in-order to appear here.</div>
						<?php
					} else { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no Exam Grade Change or Makeup Exam Grade submission request to approve <?= (!empty($years_to_look_list_for_display) ? $years_to_look_list_for_display : ''); ?>. Exam grade changes and makeup exams are required to be submitted by instructor/department and approved by department in-order to appear here. You can use the "View Grade Change" tool to see the status of any grade change from assigned and department.</div>
						<?php
					} 
				}
				echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script>
function toggleView(obj) {
	if($('#c'+obj.id).css("display") == 'none')
		$('#i'+obj.id).attr("src", '/img/minus2.gif');
	else
		$('#i'+obj.id).attr("src", '/img/plus2.gif');
	$('#c'+obj.id).toggle("slow");
}
</script>