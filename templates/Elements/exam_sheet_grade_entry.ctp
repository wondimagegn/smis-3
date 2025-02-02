<?php
if (!isset($grade_view_only)) {
	$grade_view_only = false;
} ?>

<div style="overflow-x:auto;">
	<table cellpadding="0" cellspacing="0" class="table">
		<thead>
			<tr>
				<th class="center" style="width:3%">&nbsp;</th>
				<th class="center" style="width:2%">#</th>
				<th class="vcenter" style="width:16%">Student Name</th>
				<th class="center" style="width:8%">Student ID</th>
				<?php
				$percent = 10;
				$last_percent = "";
				//It it is makeup exam entry
				if ($grade_view_only) {
					//It is exam grade view only and there is nothing to do for now
					$percent = 10;
					$last_percent = 42;
				} else if ($makeup_exam) { ?>
					<th class="center" style="width:<?= (!($grade_submission_status['grade_submited'] || $display_grade || $view_only) ? 72 : 10); ?>%">Total (100%)</th>
					<?php
					//$last_percent = 32;
				} else {
					//If it is not makeup exams (add and registered)
					
				}
				//End of non-makeup exams
				
				//It it is submited grade or on "grade preview" state
				if ($view_only || $grade_submission_status['grade_submited'] || $display_grade) { ?>
					<th class="center" style="width:<?= $percent; ?>%">Grade</th>
					<th class="center" style="width:<?= $percent; ?>%">In Progress</th>
					<th class="center" style="width:<?= ($last_percent != "" ? $last_percent + $percent : $percent); ?>%">Status</th>
					<?php
				} ?>
			</tr>
		</thead>
		<tbody>
			<?php
			//Building every student exam result entry
			//if(!$makeup_exam) {
			if (!isset($total_student_count)) {
				$total_student_count = count($students_process);
			}

			if (!empty($students_process)) {

				foreach($students_process as $key => $student) {

					$grade_history_count = 0;

					if (isset($student['freshman_program']) && $student['freshman_program']) {
						$freshman_program = true;
						$approver = 'freshman program';
						$approver_c = 'Freshman Program';
					} else {
						$freshman_program = false;
						$approver = 'department';
						$approver_c = 'Department';
					}
					
					$total_100 = "";
					$st_count++; ?>
					<tr>
						<td class="center" onclick="toggleView(this)" id="<?= $st_count; ?>"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $st_count)); ?></td>
						<td class="center"><?= $st_count; ?></td>
						<td class="vcenter"><?= $student['Student']['first_name'] . ' ' . $student['Student']['middle_name'] . ' ' . $student['Student']['last_name']; ?></td>
						<td class="center"><?= $student['Student']['studentnumber']; ?></td>
						<?php
						//If it is makeup exam entry
						if ($grade_view_only) {
							//It is exam grade view only and there is nothing to do for now
						} else if ($makeup_exam) {
							echo '<td class="center" style="line-height: 1;">';
							if (!empty($student['ExamGrade']) && $student['ExamGrade'][0]['department_approval'] != -1) {
								echo ($student['ResultEntryAssignment']['result'] != null ? $student['ResultEntryAssignment']['result'] : '---');
							} else {
								if ($display_grade || $view_only) {
									echo ($student['ResultEntryAssignment']['result'] != null ? $student['ResultEntryAssignment']['result'] : '---');
								} else {
									echo '<br>';
									echo $this->Form->input('ResultEntryAssignment.' . $count . '.id', array('type' => 'hidden', 'value' => $student['ResultEntryAssignment']['id']));
									$input_options = array('type' => 'text', 'label' => false, 'maxlength' => '5', 'style' => 'width:50px', 'id' => 'result_' . $st_count . '_1', 'onBlur' => 'updateExamTotal(this, ' . $st_count . ', 1, 100, \'Total\', false)');
									$input_options['value'] = ($student['ResultEntryAssignment']['result'] != null ? $student['ResultEntryAssignment']['result'] : '');
									echo $this->Form->input('ResultEntryAssignment.' . $count . '.result', $input_options);
									$count++;
								}
							}
							echo '</td>';
						}

						if ($view_only || $display_grade || $grade_submission_status['grade_submited']) { ?>
							<td class="center" id="G_<?= ++$in_progress; ?>">
								<?php
								//GRADE
								//If the grade is from the database (regisration and add)
								$latest_grade_detail = $student['LatestGradeDetail'];

								if ($display_grade && isset($student['GeneratedExamGrade'])) {
									echo $student['GeneratedExamGrade']['grade'];
								} else if (!empty($latest_grade_detail['ExamGrade'])) {
									//If the grade from course registration or add
									if ((!isset($latest_grade_detail['ExamGrade']['auto_ng_conversion']) || $latest_grade_detail['ExamGrade']['auto_ng_conversion'] == 0) && (!isset($latest_grade_detail['ExamGrade']['manual_ng_conversion']) || $latest_grade_detail['ExamGrade']['manual_ng_conversion'] == 0) && $latest_grade_detail['ExamGrade']['department_approval'] == -1) {
										echo '<span class="rejected">';
									}
									echo $latest_grade_detail['ExamGrade']['grade'];
									if ($latest_grade_detail['ExamGrade']['department_approval'] == -1) {
										echo '</span>';
									}

									if (strcasecmp($latest_grade_detail['type'], 'Change') == 0) {
										if ($latest_grade_detail['ExamGrade']['makeup_exam_id'] == null && $latest_grade_detail['ExamGrade']['makeup_exam_result'] != null) {
											echo ' (Supplementary)';
										} else if ($latest_grade_detail['ExamGrade']['makeup_exam_result'] != null) {
											echo ' (Makeup)';
										} else {
											echo ' (Change)';
										}
									}

									if (isset($latest_grade_detail['ResultEntryAssignment']) && !empty($latest_grade_detail['ResultEntryAssignment'])) {
										echo ' <br>(Result Entry Assignment)';
									}
									
									if ((strpos($latest_grade_detail['ExamGrade']['registrar_reason'], 'backend') !== false) || $latest_grade_detail['ExamGrade']['registrar_reason'] == 'Via backend data entry interface') {
										echo ' <br>(Backend Data Entry)';
									}
								} else {
									echo '**';
								} ?>
							</td>
							<td class="center">
								<?php
								//IN PROGRESS
								$latest_grade_detail = $student['LatestGradeDetail'];
								//If the result is from the database (it can be registered, add considering its related makeup, and grade change)
								if (($grade_submission_status['grade_submited'] && !$display_grade) || $view_only) {
									//If garde is submitted
									if (isset($student['ResultEntryAssignment'])) {
										if (!isset($student['ExamGrade']) || empty($student['ExamGrade']) || ($student['ExamGrade'][0]['department_approval'] == -1 && $latest_grade_detail['ExamGrade']['auto_ng_conversion'] == 0 && $latest_grade_detail['ExamGrade']['manual_ng_conversion'] == 0)) {
											echo '<span class="on-process">Yes</span>';
										} else {
											echo '<span class="accepted">No</span>';
										}
									} else {
										if((empty($latest_grade_detail['ExamGrade']) || $latest_grade_detail['ExamGrade']['department_approval'] == -1) && (!isset($latest_grade_detail['ExamGrade']['auto_ng_conversion']) || $latest_grade_detail['ExamGrade']['auto_ng_conversion'] == 0) && (!isset($latest_grade_detail['ExamGrade']['manual_ng_conversion']) || $latest_grade_detail['ExamGrade']['manual_ng_conversion'] == 0)) {
											echo '<span class="on-process">Yes</span>';
										} else {
											echo '<span class="accepted">No</span>';
										}
									}
								} else {
									//If it is on grade preview mode
									//If grade is not saved in the database or rejected by the department
									if ((isset($student['ResultEntryAssignment']) && (!isset($student['ExamGradeChange']) || (isset($student['ExamGradeChange']) && (empty($student['ExamGradeChange']) || $student['ExamGradeChange'][0]['department_approval'] == -1)))) || ((!isset($student['MakeupExam']) && !isset($student['ExamGrade'])) || (isset($student['ExamGrade']) && (empty($student['ExamGrade']) || $student['ExamGrade'][0]['department_approval'] == -1)))) {
										if (!$student['GeneratedExamGrade']['fully_taken']) {
											echo $this->Form->input('InProgress.'.$in_progress.'.in_progress', array('type' => 'checkbox', 'value' => $student['Student']['id'], 'label' => false, 'onclick' => 'courseInProgress('.$in_progress.', this)', 'hiddenField' => false));
										} else {
											echo '---';
										}
									} else {
										//If the garde is already in the database
										echo '<span class="accepted">No</span>';
									}
								} ?>
							</td>
							<td class="center">
								<?php
								//STATUS: Status of grade submision
								$latest_grade_detail = $student['LatestGradeDetail'];
								//Make up exam
								if (isset($student['ResultEntryAssignment'])) {
									if (!isset($student['ExamGrade']) || empty($student['ExamGrade'])) {
										echo '<span class="on-process">Grade not submitted</span>';
									} else if ($student['ExamGrade']['0']['department_approval'] == null) {
										echo '<span class="on-process">Waiting for ' . $approver . ' approval</span>';
									} else if ($student['ExamGrade']['0']['department_approval'] == -1) {
										if ($display_grade) {
											echo '<span class="on-process">Re-grade is not submitted</span>';
										} else {
											echo '<span class="rejected">Grade is rejected by ' . $approver . '</span>';
										}
									} else {
										if ($student['ExamGrade']['0']['registrar_approval'] == null) {
											if ($student['ExamGrade']['0']['initiated_by_department'] == 1) {
												echo '<span class="on-process">Requested by ' . $approver . ', waiting for registrar approval</span>';
											} else {
												echo '<span class="on-process">Approved by ' . $approver . ', waiting for registrar approval</span>';
											}
										} else if ($student['ExamGrade']['0']['registrar_approval'] == -1) {
											if ($student['ExamGrade']['0']['initiated_by_department'] == 1) {
												echo '<span class="rejected">Requested by ' . $approver . ', but rejected by registrar</span>';
											} else {
												echo '<span class="rejected">Approved by ' . $approver . ', but rejected by registrar</span>';
											}
										} else {
											echo '<span class="accepted">Accepted</span>';
										}
									}
								} else if (!empty($latest_grade_detail['ExamGrade'])) {
									//If it is registration or add
									if (strcasecmp($latest_grade_detail['type'], 'Register') == 0 || strcasecmp($latest_grade_detail['type'], 'Add') == 0 || (strcasecmp($latest_grade_detail['type'], 'Change') == 0 && $latest_grade_detail['ExamGrade']['makeup_exam_result'] != null)) {
										if ($latest_grade_detail['ExamGrade']['department_approval'] == null) {
											echo '<span class="on-process">Waiting for ' . $approver . ' approval</span>';
										} else if ($latest_grade_detail['ExamGrade']['department_approval'] == -1) {
											if ($display_grade) {
												echo '<span class="on-process">Re-grade is not submitted</span>';
											} else {
												echo '<span class="rejected">Grade is rejected by ' . $approver . '</span>';
											}
										} else {
											if ($latest_grade_detail['ExamGrade']['registrar_approval'] == null) {
												if (strcasecmp($latest_grade_detail['type'], 'Change') == 0 && $latest_grade_detail['ExamGrade']['initiated_by_department'] == 1) {
													echo '<span class="on-process">Requested by ' . $approver . ' and waiting for registrar approval</span>';
												} else {
													echo '<span class="on-process">Approved by ' . $approver . ', waiting for registrar approval</span>';
												}
											} else if ($latest_grade_detail['ExamGrade']['registrar_approval'] == -1) {
												if (strcasecmp($latest_grade_detail['type'], 'Change') == 0 && $latest_grade_detail['ExamGrade']['initiated_by_department'] == 1) {
													echo '<span class="rejected">Requested by ' . $approver . ', but rejected by registrar</span>';
												} else {
													echo '<span class="rejected">Approved by ' . $approver . ', but rejected by registrar</span>';
												}
											} else {
												echo '<span class="accepted">Accepted</span>';
											}
										}
									} else {
										//If it is exam grade change
										if ($latest_grade_detail['ExamGrade']['manual_ng_conversion'] == 1) {
											echo '<span class="accepted">NG Grade Converted</span>';
										} else if ($latest_grade_detail['ExamGrade']['auto_ng_conversion'] == 1) {
											echo '<span class="accepted">Automatic F</span>';
										} else {
											if ($latest_grade_detail['ExamGrade']['initiated_by_department'] == 1 || $latest_grade_detail['ExamGrade']['department_approval'] == 1) {
												if ($latest_grade_detail['ExamGrade']['college_approval'] == 1 || $latest_grade_detail['ExamGrade']['makeup_exam_result'] != null) {
													if ($latest_grade_detail['ExamGrade']['registrar_approval'] == 1) {
														echo '<span class="accepted">Accepted</span>';
													} else if ($latest_grade_detail['ExamGrade']['registrar_approval'] == -1) {
														echo '<span class="rejected">Approved by ' . $approver . ' and college but rejected by registrar approval.</span>';
													} else if ($latest_grade_detail['ExamGrade']['registrar_approval'] == null) {
														echo '<span class="on-process">Approved by ' . $approver . ' and college and waiting for registrar approval.</span>';
													}
												} else if ($latest_grade_detail['ExamGrade']['college_approval'] == -1) {
													echo '<span class="rejected">Approved by ' . $approver . ' but rejected by college</span>';
												} else if ($latest_grade_detail['ExamGrade']['college_approval'] == null) {
													echo '<span class="on-process">Approved by ' . $approver . ' and waiting for college approval.</span>';
												}
											} else if ($latest_grade_detail['ExamGrade']['department_approval'] == -1) {
												echo '<span class="rejected">Rejected by ' . $approver . '</span>';
											} else if ($latest_grade_detail['ExamGrade']['department_approval'] == null) {
												echo '<span class="on-process">Waiting for ' . $approver . ' approval</span>';
											}
										}
									}
								} else {
									echo '<span class="on-process">Grade not submitted</span>';
								} ?>
							</td>
							<?php
						} ?>
					</tr>
					<tr id="c<?= $st_count; ?>" style="display:none">
						<?php
						if ($view_only || $grade_submission_status['grade_submited'] || $display_grade) {
							$grade_width = 3;
						} else {
							$grade_width = 0;
						}
						
						if ($makeup_exam) {
							$colspan = ($grade_width + 4);
						} else {
							$colspan = ($grade_width + 3 + count($exam_types) + 1);
						} ?>

						<td style="background-color: white;">&nbsp;</td>
						<td style="background-color: white;" colspan="<?= $colspan; ?>">
							<?php
							$register_or_add = 'gh';
							if(isset($student['ExamGradeHistory'])) {
								$grade_history = $student['ExamGradeHistory'];
							} else {
								$grade_history = array();
							}
							$this->set(compact('register_or_add', 'grade_history', 'freshman_program')); ?>
							<table>
								<tr>
									<td style="vertical-align:top; width:40%"><?= $this->element('registered_or_add_course_grade_history'); ?></td>
									<td style="vertical-align:top; width:60%">
									<?php
									if ($grade_view_only) {
										//It is exam grade view only and there is nothing to do for now
									} ?>
									</td>
								</tr>
							</table>
							<?php
							$student_exam_grade_change_history = $student['ExamGradeHistory'];
							$student_exam_grade_history = $student['ExamGrade'];
							//debug($student);
							$this->set(compact('student_exam_grade_history', 'student_exam_grade_change_history', 'freshman_program'));
							echo $this->element('registered_or_add_course_grade_detail_history'); ?>
						</td>
					</tr>
					<?php
				} 
			} ?>
		</tbody>
	</table>
</div>
<br>
