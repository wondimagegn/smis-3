<style>
	input::placeholder {
		color: #d3d3d3; /* Light color for placeholder text */
		opacity: 0.9;
	}
	input {
		color: #000000; /* Dark color for actual input values */
		/* width: 70px; */
	}
</style>
<?php
if (!isset($total_student_count)) {
	$total_student_count = count($students_process);
} ?>

<script>

	var col = Number(<?= count($exam_types); ?>);
	var rows = Number(<?= $total_student_count; ?>);
	var current = 1;
	var next;

	document.onkeydown = moveByKeyPress;

	function moveByKeyPress(e) {

		if (!e) {
			e = window.event;
		}

		var currentId = $(e.target).attr("id");
		var currentRow = currentId.split('_', 3);
		var nextRowNumber = Number(currentRow[1]) + 1;
		var currentCol = Number(currentRow[2]);
		var liveValue = $(e.target).val();

		var key;

		(e.keyCode) ? key = e.keyCode: key = e.which;

		try {
			//if (key == 37 | key == 38 | key == 39 | key == 40 | key == 9) { // bitwise OR, original implementation
			if (key == 37 || key == 38 || key == 39 || key == 40 || key == 9) { // normal OR operator
				if (liveValue != "" && isNaN(liveValue)) {
					alert('Please enter a valid result.');
					$('#' + $(e.target).attr("id")).focus();
				} else if (liveValue != "" && parseFloat(liveValue) > parseFloat($(e.target).attr("data-percent"))) {
					alert('The maximum value of "' + $(e.target).attr("data-type") + '" exam result is ' + $(e.target).attr("data-percent") + '.');
					$('#' + $(e.target).attr("id")).focus();
				} else if (liveValue != "" && parseFloat(liveValue) < 0) {
					alert('The minimum value of "' + $(e.target).attr("data-type") + '" exam result is 0.');
					$('#' + $(e.target).attr("id")).focus();
					$('#' + $(e.target).attr("id")).select();
				} else {
					var next;
					switch (key) {
						case 37: //left
							next = currentCol - 1;
							if (next > col) {
								var nextRowNumber = Number(currentRow[1]) + 1;
								var newRowId = 'result_' + nextRowNumber + '_1';
								$('#' + newRowId).focus();
							} else {
								var currentRowId = 'result_' + currentRow[1] + '_' + next;
								$('#' + currentRowId).focus();
							}
							break;
						case 38: //up
							// next = nextRowNumber-1;
							e.preventDefault(); // Prevent default behavior
							var previousRowNumber = Number(currentRow[1]) - 1;
							var newRowId = 'result_' + previousRowNumber + '_' + currentCol;
							if ($('#' + newRowId).length != 0) {
								$('#' + newRowId).focus();
							} else {
								$('#' + currentId).focus();
							}
							break;
						case 39: //right
						{
							next = (currentCol) + 1;
							if (next > col) {
								var nextRowNumber = Number(currentRow[1]) + 1;
								var newRowId = 'result_' + nextRowNumber + '_1';
								$('#' + newRowId).focus();
							} else {
								var currentRowId = 'result_' + currentRow[1] + '_' + next;
								$('#' + currentRowId).focus();
							}
						}
						break;
						case 40: //down
							e.preventDefault(); // Prevent default behavior
							var nextRowNumber = Number(currentRow[1]) + 1;
							var newRowId = 'result_' + nextRowNumber + '_' + currentCol;
							if ($('#' + newRowId).length != 0) {
								$('#' + newRowId).focus();
							} else {
								$('#' + currentId).focus();
							}
							break;
					}
				}
			} else {
				return;
			}
		} catch (exception) {

		}
	}
</script>

<?php
if (!isset($grade_view_only)) {
	$grade_view_only = false;
} ?>

<div style="overflow-x:auto;">
	<table cellpadding="0" cellspacing="0" class="table" onkeypress="javascript:moveByKeyPress();">
		<thead>
			<tr>
				<td class="center" style="width:3%">&nbsp;</td>
				<td class="center" style="width:2%">#</td>
				<td class="vcenter" style="width:14%">Student Name</td>
				<td class="center" style="width:8%">Student ID</td>
				<?php
				$percent = 10;
				$last_percent = "";
				//It it is makeup exam entry
				if ($grade_view_only) {
					//It is exam grade view only and there is nothing to do for now
					$percent = 10;
					$last_percent = 42;
				} else if ($makeup_exam) { ?>
					<td class="center" style="width:<?= (!($grade_submission_status['grade_submited'] || $display_grade || $view_only) ? 72 : 10); ?>%">Total (100%)</td>
					<?php
					$last_percent = 32;
				} else {
					//If it is not makeup exams (add and registered)
					$grade_width = 0;
					if ($grade_submission_status['grade_submited']) {
						$grade_width = 3;
					} else if ($display_grade || $view_only) {
						$grade_width = 3;
					}

					if (((100 - 28) / ((count($exam_types) + 1) + $grade_width)) > 10) {
						$last_percent = (100 - 28) - ((count($exam_types) + 1 + $grade_width) * 10);
					} else {
						$percent = ((100 - 28) / (count($exam_types) + 1 + $grade_width));
					}

					$count_for_percent = 0;

					foreach ($exam_types as $key => $exam_type) {
						$count_for_percent++; ?>
						<td class="center" style="width:<?= ($count_for_percent == (count($exam_types) + 1) && $last_percent != "" && !($grade_submission_status['grade_submited'] || $display_grade || $view_only) ? $last_percent + $percent : $percent); ?>%">
							<?= $exam_type['ExamType']['exam_name'] . ' (' . $exam_type['ExamType']['percent'] . '%)'; ?>
						</td>
						<?php
					} ?>
					<td class="center" style="width:<?= (!($grade_submission_status['grade_submited'] || $display_grade || $view_only) ? $last_percent + $percent : $percent); ?>%">Total (100%)</td>
					<?php
				}
				//End of non-makeup exams

				//It it is submited grade or on "grade preview" state
				if ($view_only || $grade_submission_status['grade_submited'] || $display_grade) { ?>
					<td class="center" style="width:<?= $percent; ?>%">Grade</td>
					<td class="center" style="width:<?= $percent; ?>%">In Progress</td>
					<td class="center" style="width:<?= ($last_percent != "" ? $last_percent + $percent : $percent); ?>%">Status</td>
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

			foreach ($students_process as $key => $student) {
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
					} else if ($makeup_exam) { ?>
						<td class="center" style="line-height: 1;">
							<?php
							if (!empty($student['ExamGradeChange']) && $student['ExamGradeChange'][0]['department_approval'] != -1) {
								echo ($student['MakeupExam']['result'] != null ? $student['MakeupExam']['result'] : '---');
							} else {
								if ($display_grade || $view_only) {
									echo ($student['MakeupExam']['result'] != null ? $student['MakeupExam']['result'] : '---');
								} else {
									echo $this->Form->input('MakeupExam.' . $count . '.id', array('type' => 'hidden', 'value' => $student['MakeupExam']['id']));
									//$input_options = array('type' => 'text', 'label' => false, 'maxlength' => '5', 'style' => 'width:50px', 'id' => 'result_' . $st_count . '_1', 'onBlur' => 'updateExamTotal(this, ' . $st_count . ', 1, 100, \'Total\', false)');
									
									//Check By Neway
									$input_options = array('type' => 'number', 'label' => false, 'style' => 'width:70px', 'id' => 'result_' . $st_count . '_1', /* 'min' => 0, */ 'max' => 100, 'step' => 'any', 'onBlur' => 'updateExamTotal(this, ' . $st_count . ', 1, 100, \'Total\', false)');

									$input_options['value'] = ($student['MakeupExam']['result'] != null ? $student['MakeupExam']['result'] : '');
									echo $this->Form->input('MakeupExam.' . $count . '.result', $input_options);
									$count++;
								}
							} ?>
						</td>
						<?php
					} else {
						//If it is non-makeup exams (add and registered)
						$et_count = 0;
						//Each mark entry for each exam type (foreach loop)
						foreach ($exam_types as $key => $exam_type) {
							$et_count++; ?>
							<td class="center" style="line-height: 1;">
								<?php
								$id = "";
								$value = "";
								//Searching for the exam result from the databse returned value
								if (isset($student['ExamResult']) && !empty($student['ExamResult'])) {
									foreach ($student['ExamResult'] as $key => $examResult) {
										if ($examResult['exam_type_id'] == $exam_type['ExamType']['id']) {
											$id = $examResult['id'];
											$value = $examResult['result'];
											$total_100 += $value;
											break;
										}
									}
								}

								//if save exam result button is clicked to add each exam result to get result sum
								$i = (($st_count - 1) * count($exam_types)) + 1;

								if (isset($this->request->data['ExamResult'][$i]['result'])) {
									if (isset($this->request->data) && !$display_grade && !$view_only) {
										$total_100 = "";
										for (; $i <= ((($st_count - 1) * count($exam_types)) + count($exam_types)); $i++) {
											if (isset($this->request->data['ExamResult'][$i])) {
												if ($this->request->data['ExamResult'][$i]['result'] != "" && is_numeric($this->request->data['ExamResult'][$i]['result'])) {
													$total_100 += $this->request->data['ExamResult'][$i]['result'];
												}
											}
										}
									}
								}

								if ($display_grade || $view_only || (!empty($student['ExamGrade']) && $student['ExamGrade'][0]['department_approval'] != -1)) {
									echo ($value != "" ? $value : '---');
								} else {
									//It is if it is on exam result edit mode
									//debug($id);
									?>
									<div style="padding-left: 25%;">
									<br>
									<?php
									if ($id != "") {
										echo $this->Form->input('ExamResult.' . $count . '.id', array('type' => 'hidden', 'value' => $id));
										/* $input_options = array(
											'type' => 'text', 'label' => false, 'maxlength' => '5', 'style' => 'width:50px', 'id' => 'result_' . $st_count . '_' . $et_count,
											'onBlur' => 'updateExamTotal(this, ' . $st_count . ', ' . count($exam_types) . ', ' . $exam_type['ExamType']['percent'] . ', \'' . $exam_type['ExamType']['exam_name'] . '\', true)',
											'data-type' => $exam_type['ExamType']['exam_name'], 'data-percent' => $exam_type['ExamType']['percent']
										); */

										//Check By Neway
										$input_options = array(
											'type' => 'number', 'label' => false, 'placeholder' => ' /' . ($exam_type['ExamType']['percent']) .'%', 'style' => 'width:70px', 'id' => 'result_' . $st_count . '_' . $et_count, /* 'min' => 0, */ 'max' => $exam_type['ExamType']['percent'], 'step' => 'any',
											'onBlur' => 'updateExamTotal(this, ' . $st_count . ', ' . count($exam_types) . ', ' . $exam_type['ExamType']['percent'] . ', \'' . $exam_type['ExamType']['exam_name'] . '\', true)',
											'data-type' => $exam_type['ExamType']['exam_name'], 'data-percent' => $exam_type['ExamType']['percent']
										);

										$input_options['value'] = $value;
										$input_options['tabindex'] = (($total_student_count * ($et_count - 1)) + $st_count);
										echo $this->Form->input('ExamResult.' . $count . '.result', $input_options);
										//End of exam result edit mode
									} else {
										//New exam result entry
										echo $this->Form->input('ExamResult.' . $count . '.exam_type_id', array('type' => 'hidden', 'value' => $exam_type['ExamType']['id']));
										//Exam result entry for course registration
										if (isset($student['CourseRegistration'])) {
											echo $this->Form->input('ExamResult.' . $count . '.course_registration_id', array('type' => 'hidden', 'value' => $student['CourseRegistration']['id']));
											echo $this->Form->input('ExamResult.' . $count . '.course_add', array('type' => 'hidden', 'value' => 0));
										} else if (isset($student['CourseAdd'])) {
											//Exam result entry for course add
											echo $this->Form->input('ExamResult.' . $count . '.course_registration_id', array('type' => 'hidden', 'value' => $student['CourseAdd']['id']));
											echo $this->Form->input('ExamResult.' . $count . '.course_add', array('type' => 'hidden', 'value' => 1));
										}
										//Exam result entry for makeup exam (now it becomes obsolete)
										/* echo $this->Form->input('ExamResult.' . $count . '.result', array(
											'tabindex' => (($total_student_count * ($et_count - 1)) + $st_count),
											'type' => 'text', 'label' => false, 'maxlength' => '5', 'style' => 'width:50px', 'id' => 'result_' . $st_count . '_' . $et_count,
											'onBlur' => 'updateExamTotal(this, ' . $st_count . ', ' . count($exam_types) . ', ' . $exam_type['ExamType']['percent'] . ', \'' . $exam_type['ExamType']['exam_name'] . '\', true)',
											'data-type' => $exam_type['ExamType']['exam_name'], 'data-percent' => $exam_type['ExamType']['percent']
										)); */

										// Check By Neway
										echo $this->Form->input('ExamResult.' . $count . '.result', array(
											'tabindex' => (($total_student_count * ($et_count - 1)) + $st_count),
											'type' => 'number', 'label' => false, 'placeholder' => ' /' . ($exam_type['ExamType']['percent']) .'%', 'style' => 'width:70px', 'id' => 'result_' . $st_count . '_' . $et_count, /* 'min' => 0, */ 'max' => $exam_type['ExamType']['percent'], 'step' => 'any',
											'onBlur' => 'updateExamTotal(this, ' . $st_count . ', ' . count($exam_types) . ', ' . $exam_type['ExamType']['percent'] . ', \'' . $exam_type['ExamType']['exam_name'] . '\', true)',
											'data-type' => $exam_type['ExamType']['exam_name'], 'data-percent' => $exam_type['ExamType']['percent']
										));
										//End of new exam result entry
									} ?>
									</div>
									<?php
								}  ?>
							</td>
							<?php
							$count++;
							//End of each mark entry for each exam type (foreach loop)
						}  ?>
						<td class="center" id="total_100_<?= $st_count; ?>"><?= ($total_100 !== "" ? $total_100 : '---'); ?></td>
						<?php
						//End of non-makeup exams result entry
					} 
					
					if ($view_only || $display_grade || $grade_submission_status['grade_submited']) { ?>
						<td class="center" id="G_<?= ++$in_progress; ?>">
							<?php
							//GRADE
							//If the grade is from the database (regisration and add)
							$latest_grade_detail = $student['LatestGradeDetail'];
							//debug($latest_grade_detail);
							//If it is computed grade but not submitted (Preview grade mode)
							//debug($latest_grade_detail);
							//debug($student);
							if ($display_grade && isset($student['GeneratedExamGrade'])) {
								echo $student['GeneratedExamGrade']['grade'];
								//If it is makeup exam
								//The following condition will be skipped if if makeup exam result is changed in the form of grade change or supplementary exam
							} else if (isset($student['MakeupExam']) && (empty($student['ExamGradeChange']) || $student['ExamGradeChange'][0]['created'] >= $latest_grade_detail['ExamGrade']['created'])) { 
								//debug($student['ExamGradeChange']);
								//If the grade is from the database (makeup)
								if (isset($student['ExamGradeChange']) && !empty($student['ExamGradeChange'])) {
									if ($student['ExamGradeChange'][0]['department_approval'] == -1) {
										echo '<p class="rejected">';
									}
									echo $student['ExamGradeChange'][0]['grade'];
									if ($student['ExamGradeChange'][0]['department_approval'] == -1) {
										echo '</p>';
									}
								} else {
									//If the course is on progress (Neither generated or saved)
									echo '**';
								}
							} else if (!empty($latest_grade_detail['ExamGrade'])) {
								//If the result is about course registration and add 
								//considering makeup and exam change
								//If the grade from course registration or add
								if ((!isset($latest_grade_detail['ExamGrade']['auto_ng_conversion']) || $latest_grade_detail['ExamGrade']['auto_ng_conversion'] == 0) && (!isset($latest_grade_detail['ExamGrade']['manual_ng_conversion']) || $latest_grade_detail['ExamGrade']['manual_ng_conversion'] == 0) && $latest_grade_detail['ExamGrade']['department_approval'] == -1) {
									echo '<p class="rejected">';
								}

								echo $latest_grade_detail['ExamGrade']['grade'];

								if ($latest_grade_detail['ExamGrade']['department_approval'] == -1) {
									echo '</p>';
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
									echo ' (Result Entry Assignment)';
								}

								if ((strpos($latest_grade_detail['ExamGrade']['registrar_reason'], 'backend') !== false) || $latest_grade_detail['ExamGrade']['registrar_reason'] == 'Via backend data entry interface') {
									echo ' (Backend Data Entry)';
								}
							} else {
								//If the course is on progress (Neither generated or saved)
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
								if (isset($student['MakeupExam'])) {
									if (!isset($student['ExamGradeChange']) || empty($student['ExamGradeChange']) || ($student['ExamGradeChange'][0]['department_approval'] == -1 && $latest_grade_detail['ExamGrade']['auto_ng_conversion'] == 0 && $latest_grade_detail['ExamGrade']['manual_ng_conversion'] == 0)) {
										echo '<span class="on-process">Yes</span>';
									} else {
										echo '<span class="accepted">No</span>';
									}
								} else {
									if ((empty($latest_grade_detail['ExamGrade']) || $latest_grade_detail['ExamGrade']['department_approval'] == -1) && (!isset($latest_grade_detail['ExamGrade']['auto_ng_conversion']) || $latest_grade_detail['ExamGrade']['auto_ng_conversion'] == 0) && (!isset($latest_grade_detail['ExamGrade']['manual_ng_conversion']) || $latest_grade_detail['ExamGrade']['manual_ng_conversion'] == 0)) {
										echo '<span class="on-process">Yes</span>';
									} else {
										echo '<span class="accepted">No</span>';
									}
								}
							} else {
								//If grade is not saved in the database or rejected by the department
								if ((isset($student['MakeupExam']) && (!isset($student['ExamGradeChange']) || (isset($student['ExamGradeChange']) && (empty($student['ExamGradeChange']) || $student['ExamGradeChange'][0]['department_approval'] == -1)))) || ((!isset($student['MakeupExam']) && !isset($student['ExamGrade'])) || (isset($student['ExamGrade']) && (empty($student['ExamGrade']) || $student['ExamGrade'][0]['department_approval'] == -1)))) {
									if (!$student['GeneratedExamGrade']['fully_taken']) {
										echo '<div style="margin-left: 40%;">' . $this->Form->input('InProgress.' . $in_progress . '.in_progress', array('type' => 'checkbox', 'value' => $student['Student']['id'], 'label' => false, 'onclick' => 'courseInProgress(' . $in_progress . ', this)', 'hiddenField' => false)) . '</div>';
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
							//debug($student['ExamGradeChange']);
							//debug($latest_grade_detail['ExamGrade']);
							//debug($student['MakeupExam']);
							//debug($student['ExamGradeChange']);
							if (isset($student['MakeupExam'])) // && isset($latest_grade_detail['ExamGrade']['makeup_exam_result']) && $latest_grade_detail['ExamGrade']['makeup_exam_result'] != null) 
							{
								if (!isset($student['ExamGradeChange']) || empty($student['ExamGradeChange'])) {
									echo '<span class="on-process">Grade not submitted</span>';
								} else if ($student['ExamGradeChange']['0']['department_approval'] == null) {
									echo '<span class="on-process">Waiting for ' . $approver . ' approval</span>';
								} else if ($student['ExamGradeChange']['0']['department_approval'] == -1) {
									if ($display_grade) {
										echo '<span class="on-process">Re-grade is not submitted</span>';
									} else {
										echo '<span class="rejected">Grade is rejected by ' . $approver . '</span>';
									}
								} else {
									if ($student['ExamGradeChange']['0']['registrar_approval'] == null) {
										if ($student['ExamGradeChange']['0']['initiated_by_department'] == 1) {
											echo '<span class="on-process">Requested by ' . $approver . ', waiting for registrar approval</span>';
										} else {
											echo '<span class="on-process">Approved by ' . $approver . ', waiting for registrar approval</span>';
										}
									} else if ($student['ExamGradeChange']['0']['registrar_approval'] == -1) {
										if ($student['ExamGradeChange']['0']['initiated_by_department'] == 1) {
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
						if (isset($student['MakeupExam']) && isset($student['ExamGradeChange']) && count($student['ExamGradeChange']) > 0) { ?>
							<table cellpadding="0" cellspacing="0" class="table">
								<tr>
									<td style="width:18%; font-weight:bold; background-color: white;" class="vcenter">Makeup Exam Minute Number:</td>
									<td style="width:82%; background-color: white;" class="vcenter"><?= $student['ExamGradeChange'][0]['minute_number']; ?></td>
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
								<td style="vertical-align:top; width:60%; background-color: white;">
									<?= $this->element('registered_or_add_course_grade_history'); ?>
								</td>
								<td style="vertical-align:top; width:40%; background-color: white;">
									<?php
									//debug($student['ExamGradeHistory']);
									if (isset($student['ExamGradeHistory'][0]['ExamGrade']) && !empty($student['ExamGradeHistory'][0]['ExamGrade'])) {
										
										$date_grade_submited = $student['ExamGradeHistory'][0]['ExamGrade']['created'];
										
										$grade_change_deadline = date('Y-m-d H:i:s', mktime(
											substr($date_grade_submited, 11, 2),
											substr($date_grade_submited, 14, 2),
											substr($date_grade_submited, 17, 2),
											substr($date_grade_submited, 5, 2),
											substr($date_grade_submited, 8, 2) + (isset($days_available_for_grade_change) ? $days_available_for_grade_change : 0),
											substr($date_grade_submited, 0, 4)
										));

										$grade_history_count = count($student['ExamGradeHistory']);

										if (isset($lastGradeSubmissionDate) && !empty($lastGradeSubmissionDate) && USE_CALENDAR_GRADE_SUBMISSION_END_DATE_INSTEAD_OF_GRADE_SUBMITTED_DATE_FOR_GRADE_CHANGE_DEADLINE_CALCULATION) {
											debug($lastGradeSubmissionDate);
											$grade_change_deadline = date('Y-m-d H:i:s', mktime(
												substr($lastGradeSubmissionDate, 11, 2),
												substr($lastGradeSubmissionDate, 14, 2),
												substr($lastGradeSubmissionDate, 17, 2),
												substr($lastGradeSubmissionDate, 5, 2),
												substr($lastGradeSubmissionDate, 8, 2) + (isset($days_available_for_grade_change) ? $days_available_for_grade_change : 0),
												substr($lastGradeSubmissionDate, 0, 4)
											));
										}
									}

									if ($grade_view_only || (isset($view_only) && $view_only)) { 
										//It is exam grade view only and there is nothing to do for now ?>
										<div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: normal;"><span style="margin-right: 15px;"></span>Grade change is not available in view mode or if grade is not submitted.</div>
										<?php
									} else if (!$student['AnyExamGradeIsOnProcess'] && isset($days_available_for_grade_change) && ((!isset($student['MakeupExam']) && isset($student['ExamGrade']) && !empty($student['ExamGrade']) && $student['ExamGrade'][0]['registrar_approval'] == 1) || (isset($student['MakeupExam']) && !empty($student['ExamGradeChange']) && ($student['ExamGradeChange'][0]['registrar_approval'] == 1 || $student['ExamGradeChange'][0]['makeup_exam_result'] == null)))) {
										//debug($student);
										//debug($student['Student']['graduated']);
										if (strcasecmp($latest_grade_detail['ExamGrade']['grade'], 'NG') == '0' || strcasecmp($latest_grade_detail['ExamGrade']['grade'], 'I') == '0' || strcasecmp($latest_grade_detail['ExamGrade']['grade'], 'DO') == '0' || strcasecmp($latest_grade_detail['ExamGrade']['grade'], 'W') == '0' ) { ?>
											<div id="flashMessage" class="warning-box warning-message" style="font-family: 'Times New Roman', Times, serif; font-weight: normal;"><span style="margin-right: 15px;"></span>Grade change is not available for NG, I , W, DO Grades.</div>
											<?php
										} else if ($student['Student']['graduated']) { ?>
											<div id="flashMessage" class="warning-box warning-message" style="font-family: 'Times New Roman', Times, serif; font-weight: normal;"><span style="margin-right: 15px;"></span>Grade change is not available for graduated student.</div>
											<?php
										} else {
											if (!$student['AnyExamGradeIsOnProcess'] && $grade_change_deadline >= date('Y-m-d H:i:s')) { ?>
												<br><span style="text-align:left" class="fs13 accepted">Exam grade change deadline: <?= $this->Time->format("M j, Y g:i A", $grade_change_deadline, NULL, NULL); ?></span><br><br>
												<table cellpadding="0" cellspacing="0" class="table">
													<thead>
														<tr><td colspan="2"><?= '<div style="font-weight:bold; font-size:14px">Grade Change Request</div>'; ?></td></tr>
													</thead>
													<tbody>
														<tr>
															<td style="font-weight:bold; background-color: white;">Exam Result (out of 100%):</td>
															<td style="background-color: white;">
																<?= $this->Form->input('ExamGradeChange.' . $st_count . '.exam_grade_id', array('type' => 'hidden', 'value' => (!isset($student['MakeupExam']) ? $student['ExamGrade'][0]['id'] : $student['ExamGradeChange'][0]['ExamGrade']['id']))); ?>
																<?php //echo $this->Form->input('ExamGradeChange.' . $st_count . '.result', array('id' => 'GradeChangeResult_result_' . $st_count, 'label' => false, 'maxlength' => 5, 'style' => 'width:100px', 'onBlur' => 'updateExamGradeChange(this, ' . $st_count . ')')); ?>
																<!-- Check By Neway -->
																<?= $this->Form->input('ExamGradeChange.' . $st_count . '.result', array('id' => 'GradeChangeResult_result_' . $st_count, 'label' => false, 'type' => 'number', 'min' => 0, 'max' => 100, 'step' => 'any',  'style' => 'width:100px', 'onBlur' => 'updateExamGradeChange(this, ' . $st_count . ')')); ?>
															</td>
														</tr>
														<tr>
															<td style="font-weight:bold; background-color: white;">New Grade:</td>
															<td style="background-color: white;" id="GradeChangeResult_grade_<?= $st_count; ?>">---</td>
														</tr>
														<tr>
															<td style="font-weight:bold; background-color: white;">Reason for Change:</td>
															<td style="background-color: white;">
																<?php //echo debug($st_count); ?>
																<?= $this->Form->input('ExamGradeChange.' . $st_count . '.reason', array('type' => 'textarea', 'label' => false, 'cols' => 35, 'rows' => 5, 'id' => 'GradeChangeResult_reason_' . $st_count)); ?>
															</td>
														</tr>
													</tbody>
												</table>
												<br>
												<?= $this->Form->submit(__('Send Grade Change Request to ' . $approver_c . ''), array('name' => 'grade_change_request_' . $st_count, 'class' => 'tiny radius button bg-blue', 'onclick' => 'return submitGrdeChangeRequest(\'GradeChangeResult\', ' . $st_count . ')')); ?>
												<?php
											} else if ($grade_change_deadline < date('Y-m-d H:i:s')) { ?>
												<br><span style="text-align:center" class="fs13 rejected"><b style="font-family: 'Times New Roman', Times, serif;">Exam grade change deadline was <?= $this->Time->format("M j, Y g:i A", $grade_change_deadline, NULL, NULL); ?></b></span><br>
												<?php
											} else if ($student['AnyExamGradeIsOnProcess']) { ?>
												<br><span style="text-align:center" class="fs13 on-process"><b style="font-family: 'Times New Roman', Times, serif;">Exam grade is on process to request grade change.</b></span><br>
												<?php
											}
										}
									} else if ($grade_history_count > 1) {
										$last_grade_change = $student['ExamGradeHistory'][$grade_history_count - 1];
										//If the grade change is initiated by department and the action is 
										//from non-native-instructor
										if ((strcasecmp($this->request->action, 'add') != 0 &&
												$last_grade_change['ExamGrade']['initiated_by_department'] == 1 &&
												$last_grade_change['ExamGrade']['manual_ng_conversion'] == 0 &&
												$last_grade_change['ExamGrade']['auto_ng_conversion'] == 0 &&
												$last_grade_change['ExamGrade']['college_approval'] == null &&
												$last_grade_change['ExamGrade']['makeup_exam_result'] == null) ||
												//If the grade change is initiated by the instructor and the action is 
												//from native-instructor (course instructor)
												(strcasecmp($this->request->action, 'add') == 0 &&
												$last_grade_change['ExamGrade']['initiated_by_department'] == 0 &&
												$last_grade_change['ExamGrade']['manual_ng_conversion'] == 0 &&
												$last_grade_change['ExamGrade']['auto_ng_conversion'] == 0 &&
												$last_grade_change['ExamGrade']['department_approval'] == null &&
												$last_grade_change['ExamGrade']['makeup_exam_result'] == null)
										) {
											echo 'You already requested a grade change request which is pending ' . $approver_c . ' approval. You can ' . $this->Html->link(__('Cancel Grade Change Request'), array('action' => 'cancel_grade_change_request', $last_grade_change['ExamGrade']['id']), null, sprintf(__('Are you sure you want to cancel grade change request for %s?'), $student['Student']['first_name'] . ' ' . $student['Student']['middle_name'] . ' ' . $student['Student']['last_name'] . ' (' . $student['Student']['studentnumber'] . ')')) . ' before it is processed by the ' . $approver_c . '.';
										}
										//debug($last_grade_change['ExamGrade']['id']);
										//debug($last_grade_change);
										//debug($student);
									} ?>
								</td>
							</tr>
						</table>
						<?php
						$student_exam_grade_change_history = $student['ExamGradeHistory'];
						$student_exam_grade_history = $student['ExamGrade'];
						//debug($student);
						$this->set(compact('student_exam_grade_history', 'student_exam_grade_change_history', 'freshman_program'));
						echo $this->element('registered_or_add_course_grade_detail_history');
						?>
					</td>
				</tr>
				<?php
				//End of building every student exam result entry
			} ?>
		</tbody>
	</table>
</div>
<br>
