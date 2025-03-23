<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Exam Grade Change Approval') . (isset($years_to_look_list_for_display) ? ' (' . $years_to_look_list_for_display . ')' : ''); ?></span>
        </div>
    </div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>
				<?php
				if (!empty($exam_grade_changes)) { ?>
					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal;"><span style='margin-right: 15px;'></span>Approving/rejecting individual grade change is possible by expanding <b>'+'</b> icon, Choosing either accept or reject request by providing reason.</div>
					<hr>
					<?php
					echo $this->Form->create('ExamGradeChange');
					if (!empty($exam_grade_changes)) { ?>
						<div style="font-size:14px; font-weight:bold">Exam Grade Change requests from instructors/departement which are approved/initiated by the department: </div>
						<hr>
						<?php
					} else { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no exam grade change request to approve. Either All Exam Grade Changes are approved or there is no new grade change request placed from instructor or department.</div>
						<?php
					}
					$st_count = 1;
					foreach ($exam_grade_changes as $department_name => $department_grade_changes) {
						foreach ($department_grade_changes as $program_name => $program_grade_changes) {
							foreach ($program_grade_changes as $program_type_name => $program_type_grade_changes) { ?>
								<div style="overflow-x:auto;">
									<table cellpadding="0" cellspacing="0" class="table">
										<thead>
											<tr>
												<td colspan="9" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
													<span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?= $department_name; ?></span>
														<br>
													</span>
													<span class="text-gray" style="padding-top: 14px; font-size: 13px; font-weight: bold"> 
														<?= $program_name . ' &nbsp; | &nbsp; ' . $program_type_name; ?><br>
													</span>
												</td>
											</tr>
											<tr>
												<th style="width:2%"><?= $this->Form->input('Mass.ExamGradeChange.select_all', array('disabled', 'type' => 'checkbox', 'id' => 'l', /* 'div' => false, */ 'label' => false)); ?><!-- <label for="l">All</label> --></th>
												<th style="width:2%" class="center">#</td>
												<th style="width:3%">&nbsp;</td>
												<th style="width:22%" class="vcenter">Student Name</th>
												<th style="width:10%" class="center">Student ID</th>
												<th style="width:28%" class="center">Course</th>
												<th style="width:7%" class="center">Previous</th>
												<th style="width:7%" class="center">New</th>
												<th style="width:19%" class="center">Request Date</th>
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
														<?= $this->Form->input('Mass.ExamGradeChange.' . $st_count . '.college_approval', array('type' => 'hidden', 'value' => 1)); ?>
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
													<td style="background-color: white;">&nbsp;</td>
													<td colspan="2" style="background-color:white;">&nbsp;</td>
													<td colspan="6" style="background-color:white;">
														<table cellpadding="0" cellspacing="0" class="table">
															<?php
															if ($grade_change['ExamGradeChange']['initiated_by_department'] == 1) { ?>
																<tr>
																	<td class="fs14" colspan="2" style="font-weight:bold"><span class="text-red">Important Note: This exam grade change is requested by the department not by course instructor!.</span></td>
																</tr>
																<?php
															} ?>
															<tr>
																<td style="width:20%; font-weight:bold; background-color:white;">Section:</td>
																<td style="width:80%; background-color:white;"><?= $grade_change['Section']['name'] . ' (' . (isset($grade_change['Section']['YearLevel']['id']) && !empty($grade_change['Section']['YearLevel']['name']) ? $grade_change['Section']['YearLevel']['name'] : ($grade_change['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman')) . ', '.   $grade_change['Section']['academicyear'] . ')'; ?></td>
															</tr>
															<tr>
																<td style="font-weight:bold; background-color:white;">Instructor:</td>
																<td style="background-color:white;"><?= $grade_change['Staff']['Title']['title'] . '. ' . $grade_change['Staff']['first_name'] . ' ' . $grade_change['Staff']['middle_name'] . ' ' . $grade_change['Staff']['last_name'] . ' (' . $grade_change['Staff']['Position']['position'] . ')'; ?></td>
															</tr>
															<tr>
																<td style="font-weight:bold; background-color:white;">Reason for Change:</td>
																<td style="background-color:white;"><?= $grade_change['ExamGradeChange']['reason']; ?></td>
															</tr>
														</table>
														<table cellpadding="0" cellspacing="0" class="table">
															<tr>
																<td style="vertical-align:top; width:55%; background-color:white;">
																	<?php
																	//debug($grade_change);
																	//debug($grade_change['ExamGradeHistory'][0]);

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
																	echo $this->element('registered_or_add_course_grade_history');
																	?>
																</td>
																<td style="vertical-align:top; width:45%; background-color:white;">
																	<?php
																	if (!$grade_change['Student']['graduated']) { ?>
																		<table cellpadding="0" cellspacing="0" class="table">
																			<tr>
																				<td colspan="2">
																					<div style="font-weight:bold; font-size:14px">Grade Change Request Approval</div>
																				</td>
																			</tr>
																			<tr>
																				<td style="width:22%; background-color:white;">Decision</td>
																				<td style="width:78%; background-color:white;">
																					<?php
																					echo $this->Form->input('ExamGradeChange.' . $st_count . '.id', array('type' => 'hidden', 'value' => $grade_change['ExamGradeChange']['id']));
																					$options = array('1' => ' Accept (Forward to Regisrar)', '-1' => ' Reject (Send back to department)');
																					$attributes = array('legend' => false, 'label' => false, 'separator' => "<br />", 'default' => 1);

																					if (!empty($grade_change['ExamGradeHistory'][0]['rejected'])) {
																						//$options = array('1' => 'Accept(forward to Regisrar)', '-1' => 'Reject(Send back to department');
																						//$attributes = array('legend' => false, 'label' => false, 'separator' => "<br />", 'default' => 1);
																					}
																					echo $this->Form->radio('ExamGradeChange.' . $st_count . '.college_approval', $options, $attributes); ?>
																				</td>
																			</tr>
																			<tr>
																				<td style="background-color:white;">Remark:</td>
																				<td style="background-color:white;"><?= $this->Form->input('ExamGradeChange.' . $st_count . '.college_reason', array('label' => false, 'cols' => 40)); ?></td>
																			</tr>
																		</table>
																		<br>
																		<?= $this->Form->Submit('Approve Grade Change Request', array('div' => false, 'class' => 'tiny radius button bg-blue', 'name' => 'approveGradeChangeByCollege_' . $st_count++)); ?>
																		<?php
																	} else { ?>
																		<div class='warning-box warning-message'><span style='margin-right: 15px;'></span>Grade Change is not available for graduated student.</div>
																		<?php
																	} ?>
																</td>
															</tr>
														</table>
														<?php
														$student_exam_grade_change_history = $grade_change['ExamGradeHistory'];
														$student_exam_grade_history = $grade_change['ExamGrade'];
														$this->set(compact('student_exam_grade_history', 'student_exam_grade_change_history'));
														echo $this->element('registered_or_add_course_grade_detail_history');
														//debug($grade_change);
														?>
													</td>
												</tr>
												<?php
											} ?>
										</tbody>
									</table>
								</div>
								<br>
								<?php
							}
						}
					}

					/* if ($st_count > 1) {
						//echo $this->Form->Submit('Accept All Grade Change Request', array('div' => false, 'class' => 'tiny radius button bg-blue', 'name' => 'ApproveAllGradeChangeByCollege'));
						echo " <div class='fs14'>Approving/reject individual grade change is possible by cliking on '+' icon and accept or reject request by giving reason.</div>";
					} */

					echo $this->Form->input('grade_change_count', array('type' => 'hidden', 'value' => ($st_count - 1)));
					echo $this->Form->end();
				} else  { ?>
					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no Exam Grade Change or Makeup Exam Grade submission request to approve <?= (!empty($years_to_look_list_for_display) ? $years_to_look_list_for_display : ''); ?>. Exam grade changes and makeup exams are required to be submitted by instructor/department and approved by department in-order to appear here. You can use the "View Grade Change" tool to see the status of any grade change from assigned and department.</div>
					<?php
				} ?>
			</div>
		</div>
	</div>
</div>
<script>
	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none')
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		else
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		$('#c' + obj.id).toggle("slow");
	}
</script>