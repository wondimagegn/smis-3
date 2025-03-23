<?php ?>
<script>
	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none')
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		else
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		$('#c' + obj.id).toggle("slow");
	}
</script>
<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('List Result Entry Exam Assignments'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div class="makeupExams index">
					<?= $this->Form->create('ResultEntryAssignment'); ?>
					
					<?php
					$semesters = Configure::read('semesters');
					$semesters = array('0' => '[ Any Semster ]') + $semesters; ?>

					<div style="margin-top: -30px;"><hr></div>

					<fieldset style="padding-bottom: 0px; padding-top: 15px;">
						<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
						<div class="row">
							<div class="large-3 columns">
								<?= $this->Form->input('acadamic_year', array('label' => 'Acadamic Year: ', 'id' => 'AcadamicYear', 'style' => 'width: 90%;', 'class' => 'fs14', 'type' => 'select', 'options' => $acyear_array_data, 'required', 'default' => $defaultacademicyear)); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('semester', array('label' => 'Semester: ','id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width: 90%;', 'required', 'options' => $semesters)); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('program_id', array('label' => 'Program: ', 'id' => 'Program', 'class' => 'fs14', 'style' => 'width: 90%;', 'type' => 'select',  'required', 'options' => $programsss)); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('program_type_id', array('label' => 'Program Type: ', 'id' => 'ProgramType', 'class' => 'fs14', 'style' => 'width: 90%;', 'type' => 'select',  'required', 'options' => $program_typesss)); ?>
							</div>
						</div>
						<div class="row">
							<div class="large-6 columns">
							<?= $this->Form->input('department_id', array('label' => 'Department: ','id' => 'Department', 'class' => 'fs14', 'style' => 'width: 90%;', 'type' => 'select',  'required', 'options' => $departments)); ?>
							</div>
							<div class="large-6 columns">
								&nbsp;
							</div>
						</div>
						<hr>
						<?= $this->Form->submit(__('List Result Entry Assignmets'), array('div' => false, 'id' =>'listResultEntryAssignmets', 'class' => 'tiny radius button bg-blue')); ?>
						<?= $this->Form->end(); ?>
					</fieldset>

					<hr>

					<?php
					if (isset($this->request->data)) {
						if (count($makeup_exams) > 0) { ?>
							<div style="overflow-x:auto;">
								<table cellpadding="0" cellspacing="0" class="fs14 table">
									<thead>
										<tr>
											<th style="width:3%" class="center"></th>
											<th style="width:3%" class="center">#</th>
											<th style="width:23%" class="vcenter">Student Name</th>
											<th style="width:12%" class="center">Student ID</th>
											<th style="width:39%" class="center">Exam Taken for</th>
											<th style="width:13%" class="center">Grade</th>
											<th style="width:8%" class="center">&nbsp;</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$count = 1;
										foreach ($makeup_exams as $key => $makeup_exam) {

											if (!isset($makeup_exam['student_id']) && empty($makeup_exam['student_id'])) {
												continue;
											} ?>
											<tr>
												<td class="center" onclick="toggleView(this)" id="<?= $count; ?>"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $count)); ?></td>
												<td class="center"><?= $count; ?></td>
												<td class="vcenter"><?= $makeup_exam['student_name']; ?></td>
												<td class="center"><?= $makeup_exam['student_id']; ?></td>
												<td class="center"><?= $makeup_exam['exam_for']; ?></td>
												<td class="center">
													<?php
													if (!isset($makeup_exam['ExamGrade']['id'])) {
														echo '<span class="on-process">Not Submited</span>';
													} else if ($makeup_exam['ExamGrade']['department_approval'] == 1 && $makeup_exam['ExamGrade']['registrar_approval'] == 1) {
														echo '<span class="accepted">' . $makeup_exam['ExamGrade']['grade'] . '</span>';
													} else if (!empty($makeup_exam['ExamGrade']) && (is_null($makeup_exam['ExamGrade']['department_approval']) || is_null($makeup_exam['ExamGrade']['registrar_approval']))) {
														if (is_null($makeup_exam['ExamGrade']['department_approval'])) {
															echo $makeup_exam['ExamGrade']['grade'] . '<br><span class="on-process">Waiting for Department Approval</span>';
														} else {
															echo $makeup_exam['ExamGrade']['grade'] . '<br><span class="on-process">Waiting for Registrar Approval</span>';
														}
													} ?>
												</td>
												<td class="center">
													<?= (empty($makeup_exam['ExamGrade'])) ? $this->Form->postLink(__('Delete'), array('action' => 'deleteAssignment', $makeup_exam['id']), array('confirm' => __('Are you sure you want to delete %s \'s  grade entry assignment for the course %s ?', $makeup_exam['student_name'], $makeup_exam['exam_for']))) : '---'; ?>
												</td>
											</tr>
											<tr id="c<?= $count; ?>" style="display:none">
												<td style="background-color: white;">&nbsp;</td>
												<td style="background-color: white;">&nbsp;</td>
												<td colspan="6" style="background-color: white;">
													<table cellpadding="0" cellspacing="0" class="fs14 table">
														<tr>
															<td style="width:25%; font-weight:bold; background-color: white;" class="vcenter">Section Where Exam Taken:</td>
															<td style="width:75%; background-color: white;" class="vcenter"><?= ((isset($makeup_exam['section_exam_taken']) && !empty($makeup_exam['section_exam_taken'])) ? $makeup_exam['section_exam_taken'] : '---'); ?></td>
														</tr>
														<tr>
															<td style="font-weight:bold; background-color: white;" class="vcenter">Taken Exam:</td>
															<td style="background-color: white;" class="vcenter"><?= ((isset($makeup_exam['taken_exam']) && !empty($makeup_exam['taken_exam'])) ? $makeup_exam['taken_exam'] : '---'); ?></td>
														</tr>
														<tr>
															<td style="font-weight:bold; background-color: white;" class="vcenter">Date the Student Assigned:</td>
															<td style="background-color: white;" class="vcenter"><?= (isset($makeup_exam['created']) ? $this->Time->format("M j, Y h:i:s A", $makeup_exam['created'], NULL, NULL) : '---'); ?></td>
														</tr>

														<?php
														if (!empty($makeup_exam['assigned_instructor'])) { ?>
															<tr>
																<td style="font-weight:bold; background-color: white;" class="vcenter">Assigned Instructor:</td>
																<td style="background-color: white;" class="vcenter"><?= $makeup_exam['assigned_instructor']; ?></td>
															</tr>
															<tr>
																<td style="font-weight:bold; background-color: white;" class="vcenter">Assigned Instructor Contact:</td>
																<td style="background-color: white;" class="vcenter"><?= $makeup_exam['assigned_instructor_contact']; ?></td>
															</tr>
															<?php
														} ?>
														<tr>
															<td style="font-weight:bold; background-color: white;" class="vcenter">Exam Result(/100): </td>
															<td style="background-color: white;" class="vcenter"><?= (!empty($makeup_exam['result']) ?  $makeup_exam['result'] : '---'); ?></td>
														</tr>
														<tr>
															<td style="font-weight:bold; background-color: white;" class="vcenter">Date Grade Submitted:</td>
															<td style="background-color: white;" class="vcenter"><?= (isset($makeup_exam['ExamGrade']['created']) ? $this->Time->format("M j, Y h:i:s A", $makeup_exam['ExamGrade']['created'], NULL, NULL) : '---'); ?></td>
														</tr>
														<tr>
															<td style="font-weight:bold; background-color: white;" class="vcenter">Grade Submission Status:</td>
															<td style="background-color: white;" class="vcenter">
																<?php

																if (!empty($makeup_exam['ExamGrade']['grade'])) {
																	echo '<b>'. $makeup_exam['ExamGrade']['grade'] . '</b> &nbsp;  &nbsp;';
																}

																if (!empty($makeup_exam['ExamGrade']) && $makeup_exam['ExamGrade']['department_approval'] == 1 && $makeup_exam['ExamGrade']['registrar_approval'] == 1) {
																	echo ' - <span class="accepted">Accepted</span>';
																} else if (!empty($makeup_exam['ExamGrade']) && (is_null($makeup_exam['ExamGrade']['department_approval']) || is_null($makeup_exam['ExamGrade']['registrar_approval']))) {
																	if (is_null($makeup_exam['ExamGrade']['department_approval'])) {
																		echo ' - <span class="on-process">Waiting for Department Approval</span>';
																	} else {
																		echo $makeup_exam['ExamGrade']['grade'] . ' - <span class="on-process">Waiting for Registrar Approval</span>';
																	}
																} else {
																	echo '<span class="on=process">Waiting for Grade Submission</span>';
																} ?>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<?php
											$count++;
										} ?>
									</tbody>
								</table>
							</div>
							<br>
							<?php
						}
					} ?>
				</div>
			</div>
		</div>
	</div>
</div>