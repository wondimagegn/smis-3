<?php
if (isset($freshman_program) && $freshman_program) {
	$approver = 'freshman program';
	$approver_c = 'Freshman Program';
} else {
	$approver = 'department';
	$approver_c = 'Department';
} ?>

<?php
if (count($student_exam_grade_history) > 0) {
	echo '<div style="font-weight:bold; font-size:14px; padding: 10px;">Grade History Detail (From recent to old)</div>'; ?>
	<table cellpadding="0" cellspacing="0" class="table">
		<?php
	}
	if (isset($student_exam_grade_change_history) && !empty($student_exam_grade_change_history)) {
		for ($i = count($student_exam_grade_change_history) - 1; $i >= 0; $i--) {
			if (strcasecmp($student_exam_grade_change_history[$i]['type'], 'Change') == 0) {
				$exam_grade_change = $student_exam_grade_change_history[$i]['ExamGrade'];
				$reject_count = 1; ?>
				<tr>
					<th colspan="2">
						<?php
						$department_reply = false;
						if ($exam_grade_change['manual_ng_conversion'] == 1) {
							echo '<b>Registrar NG Grade Conversion</b>';
						} else if ($exam_grade_change['auto_ng_conversion'] == 1) {
							echo '<b>Automatic F</b>';
						} else if (!is_null($exam_grade_change['makeup_exam_result'])) {
							echo ($exam_grade_change['department_reply'] == 0 ? (is_null($exam_grade_change['makeup_exam_id']) ? 'Supplementary Exam' : 'Makeup Exam') : $approver_c . ' response for registrar ' . (is_null($exam_grade_change['makeup_exam_id']) ? 'supplementary exam' : 'makeup exam') . ' grade rejection');
							if ($exam_grade_change['department_reply'] == 1) {
								$department_reply = true;
							}
						} else {
							echo 'Exam Grade Change (' . ($exam_grade_change['initiated_by_department'] == 1 ? 'By the Department' : 'By the Instructor') . ')';
						} ?>
					</th>
				</tr>
				<?php
				if ($exam_grade_change['manual_ng_conversion'] == 1) { ?>
					<tr>
						<td style="width:30%; font-weight:bold; background-color:white;">NG Converted to:</td>
						<td style="width:70%; background-color:white;"><b><?= (!empty($exam_grade_change['grade']) ? $exam_grade_change['grade'] : '---'); ?></b></td>
					</tr>
					<tr>
						<td style="font-weight:bold">Minute Number:</td>
						<td><b><?= (!empty($exam_grade_change['minute_number']) ? $exam_grade_change['minute_number'] : '---'); ?></b></td>
					</tr>
					<tr>
						<td style="font-weight:bold; background-color:white;">Convertion Date:</td>
						<td style="background-color:white;"><?= $this->Time->format("F j, Y h:i:s A", $exam_grade_change['created'], NULL, NULL); ?></td>
					</tr>
					<tr>
						<td style="font-weight:bold">Converted By:</td>
						<td><?= (!empty($exam_grade_change['manual_ng_converted_by_name']) ? $exam_grade_change['manual_ng_converted_by_name'] : '---'); ?></td>
					</tr>
					<?php
				} else if ($exam_grade_change['auto_ng_conversion'] == 1) { ?>
					<tr>
						<td style="width:30%; font-weight:bold; background-color:white;">Auto Grade:</td>
						<td style="width:70%; background-color:white;"><b><?= (!empty($exam_grade_change['grade']) ? $exam_grade_change['grade'] : '---'); ?></b></td>
					</tr>
					<tr>
						<td style="font-weight:bold">Auto Convertion Date:</td>
						<td><?= $this->Time->format("F j, Y h:i:s A", $exam_grade_change['created'], NULL, NULL); ?></td>
					</tr>
					<?php
				} else {
					if (!$department_reply) { ?>
						<tr>
							<td style="width:30%; font-weight:bold; background-color:white;">Grade:</td>
							<td style="width:70%; background-color:white;"><b><?= (!empty($exam_grade_change['grade']) ? $exam_grade_change['grade'] : '---'); ?></b></td>
						</tr>
						<?php
						if (!empty($exam_grade_change['minute_number'])) { ?>
							<tr>
								<td style="width:28%; font-weight:bold">Minute Number:</td>
								<td style="width:72%"><?= $exam_grade_change['minute_number']; ?></td>
							</tr>
							<?php
						} ?>
						<tr>
							<td style="width:28%; font-weight:bold; background-color:white;">Exam Result:</td>
							<td style="width:72%; background-color:white;"><b><?= (is_null($exam_grade_change['makeup_exam_result']) ? $exam_grade_change['result'] : $exam_grade_change['makeup_exam_result']); ?></b></td>
						</tr>
						<?php
						if (is_null($exam_grade_change['makeup_exam_result'])) { ?>
							<tr>
								<td style="width:28%; font-weight:bold">Grade Change Reason:</td>
								<td style="width:72%"><?= (!empty($exam_grade_change['reason']) ? $exam_grade_change['reason'] : '---'); ?></td>
							</tr>
							<?php
						}
						if (!is_null($exam_grade_change['makeup_exam_result']) && is_null( $exam_grade_change['makeup_exam_id'])) { ?>
							<tr>
								<td style="width:28%; font-weight:bold">Remark:</td>
								<td style="width:72%"><?= (!empty($exam_grade_change['reason']) ? $exam_grade_change['reason'] : '---'); ?></td>
							</tr>
							<?php
						} ?>
						<tr>
							<td style="font-weight:bold; background-color:white;">Request Date:</td>
							<td style="background-color:white;"><?= $this->Time->format("F j, Y h:i:s A", $exam_grade_change['created'], NULL, NULL); ?></td>
						</tr>
						<?php
					}

					if (!(empty($exam_grade_change['makeup_exam_id']) && $exam_grade_change['makeup_exam_result'] != null)) { ?>
						<tr>
							<td style="font-weight:bold"><?= $approver_c; ?> Approval:</td>
							<td class="<?= ($exam_grade_change['department_approval'] == 1 ? 'accepted' : ($exam_grade_change['department_approval'] == -1 ? 'rejected' : 'on-process')); ?>"><?= ($exam_grade_change['department_approval'] == 1 ? 'Accepted' : ($exam_grade_change['department_approval'] == -1 ? 'Rejected' : 'On Process')); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold; background-color:white;"><?= ($exam_grade_change['department_approval'] == -1 ? 'Rejected By:' : 'Approved By:'); ?></td>
							<td style="background-color:white;"><?= (!empty($exam_grade_change['department_approved_by_name']) ? $exam_grade_change['department_approved_by_name'] : '---'); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold"><?= $approver_c; ?> Remark:</td>
							<td><?= (!empty($exam_grade_change['department_reason']) ? $exam_grade_change['department_reason'] : '---'); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold; background-color:white;"><?= $approver_c . ' ' . ($exam_grade_change['department_approval'] == -1 ? 'Rejection Date:' : 'Approval Date:'); ?></td>
							<td style="background-color:white;"><?= (($exam_grade_change['department_approval_date'] == '0000-00-00 00:00:00' || empty($exam_grade_change['department_approval_date']) || is_null($exam_grade_change['department_approval_date'])) ? '---' : ($this->Time->format("F j, Y h:i:s A", $exam_grade_change['department_approval_date'], NULL, NULL))); ?></td>
						</tr>
						<?php
					}

					if ($exam_grade_change['department_reply'] == 1 && empty($exam_grade_change['makeup_exam_id']) && $exam_grade_change['makeup_exam_result'] != null) { ?>
						<tr>
							<td style="width:30%; font-weight:bold; background-color:white;">Grade:</td>
							<td style="width:70%; background-color:white;"><b><?= (!empty($exam_grade_change['grade']) ? $exam_grade_change['grade'] : '---'); ?></b></td>
						</tr>
						<tr>
							<td style="width:28%; font-weight:bold">Exam Result:</td>
							<td style="width:72%"><?= (is_null($exam_grade_change['makeup_exam_result']) ? $exam_grade_change['result'] : $exam_grade_change['makeup_exam_result']); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold; background-color:white;"><?= $approver_c; ?> Reply:</td>
							<td style="background-color:white;"><?= (!empty($exam_grade_change['department_reason']) ? $exam_grade_change['department_reason'] : '---'); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Reply By:</td>
							<td><?= (!empty($exam_grade_change['department_approved_by_name']) ? $exam_grade_change['department_approved_by_name'] : '---'); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold; background-color:white;">Reply Date:</td>
							<td style="background-color:white;"><?= (($exam_grade_change['department_approval_date'] == '0000-00-00 00:00:00' || empty($exam_grade_change['department_approval_date']) || is_null($exam_grade_change['department_approval_date'])) ? '---' : ($this->Time->format("F j, Y h:i:s A", $exam_grade_change['department_approval_date'], NULL, NULL))); ?></td>
						</tr>
						<?php
					}
					//If it is only grade chnage
					if (is_null($exam_grade_change['makeup_exam_result'])) { ?>
						<tr>
							<td style="font-weight:bold; background-color:white;">College Approval:</td>
							<td style="background-color:white;" class="<?= ($exam_grade_change['college_approval'] == 1 ? 'accepted' : ($exam_grade_change['college_approval'] == -1 ? 'rejected' : ($exam_grade_change['department_approval'] == 1 ? 'on-process' : ''))); ?>"><?= ($exam_grade_change['college_approval'] == 1 ? 'Accepted' : ($exam_grade_change['college_approval'] == -1 ? 'Rejected' : ($exam_grade_change['department_approval'] == 1 ? 'On Process' : '---'))); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold"><?= ($exam_grade_change['college_approval'] == -1 ? 'Rejected By:' : 'Approved By:'); ?></td>
							<td><?= (!empty($exam_grade_change['college_approved_by_name']) ? $exam_grade_change['college_approved_by_name'] : '---'); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold; background-color:white;">College Remark:</td>
							<td style="background-color:white;"><?= (!empty($exam_grade_change['college_reason']) ? $exam_grade_change['college_reason'] : '---'); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold"><?= ($exam_grade_change['college_approval'] == -1 ? 'College Rejected Date:' : 'College Approval Date:'); ?></th>
							<td><?= (($exam_grade_change['college_approval_date'] == '0000-00-00 00:00:00' || empty($exam_grade_change['college_approval_date']) || is_null($exam_grade_change['college_approval_date'])) ? '---' : ($this->Time->format("F j, Y h:i:s A", $exam_grade_change['college_approval_date'], NULL, NULL))); ?></td>
						</tr>
						<?php
					}
					if (!is_null($exam_grade_change['makeup_exam_result'])) { ?>
						<tr>
							<td style="font-weight:bold; background-color:white;">Registrar Confirmation:</td>
							<td style="background-color:white;" class="<?= ($exam_grade_change['registrar_approval'] == 1 ? 'accepted' : ($exam_grade_change['registrar_approval'] == -1 ? 'rejected' : ($exam_grade_change['department_approval'] == 1 ? 'on-process' : ''))); ?>"><?= ($exam_grade_change['registrar_approval'] == 1 ? 'Accepted' : ($exam_grade_change['registrar_approval'] == -1 ? 'Rejected' : ($exam_grade_change['department_approval'] == 1 ? 'On Process' : '---'))); ?></td>
						</tr>
						<?php
					} else { ?>
						<tr>
							<td style="font-weight:bold; background-color:white;">Registrar Confirmation:</td>
							<td style="background-color:white;" class="<?= ($exam_grade_change['registrar_approval'] == 1 ? 'accepted' : ($exam_grade_change['registrar_approval'] == -1 ? 'rejected' : ($exam_grade_change['college_approval'] == 1 ? 'on-process' : ''))); ?>"><?= ($exam_grade_change['registrar_approval'] == 1 ? 'Accepted' : ($exam_grade_change['registrar_approval'] == -1 ? 'Rejected' : ($exam_grade_change['college_approval'] == 1 ? 'On Process' : '---'))); ?></td>
						</tr>
						<?php
					} ?>
					<tr>
						<td style="font-weight:bold"><?= ($exam_grade_change['registrar_approval'] == -1 ? 'Registrar Rejected By:' : 'Registrar Confirmed By:'); ?></td>
						<td><?= (!empty($exam_grade_change['registrar_approved_by_name']) ? $exam_grade_change['registrar_approved_by_name'] : '---'); ?></td>
					</tr>
					<tr>
						<td style="font-weight:bold; background-color:white;">Registrar Remark:</td>
						<td style="background-color:white;"><?= (!empty($exam_grade_change['registrar_reason']) ? $exam_grade_change['registrar_reason'] : '---'); ?></td>
					</tr>
					<tr>
						<td style="font-weight:bold"><?= ($exam_grade_change['registrar_approval'] == -1 ? 'Registrar Rejected Date:' : 'Registrar Confirmation Date:'); ?></th>
						<td><?= (($exam_grade_change['registrar_approval_date'] == '0000-00-00 00:00:00' || empty($exam_grade_change['registrar_approval_date']) || is_null($exam_grade_change['registrar_approval_date'])) ? '---' : ($this->Time->format("F j, Y h:i:s A", $exam_grade_change['registrar_approval_date'], NULL, NULL))); ?></td>
					</tr>
					<?php
				}
			}
		}
	}

	//Grade history
	if (count($student_exam_grade_history) > 0) {
		$reject_count = 1;
		foreach ($student_exam_grade_history as $key => $exam_grade_detail) {
			if (isset($exam_grade_detail['ExamGrade'])) {
				$exam_grade_detail = $exam_grade_detail['ExamGrade'];
			} ?>
			<tr>
				<th colspan="2"><?= ($exam_grade_detail['department_reply'] == 0 ? 'Grade History ' . $reject_count++ : $approver_c . ' response for registrar exam grade rejection'); ?></th>
			</tr>
			<?php 
			if ($exam_grade_detail['department_reply'] == 0) { ?>
				<tr>
					<td style="width:28%; font-weight:bold;background-color:white;">Grade:</td>
					<td style="width:72%;background-color:white;"><b><?= (!empty($exam_grade_detail['grade']) ? $exam_grade_detail['grade'] : '---'); ?></b></td>
				</tr>
				<tr>
					<td style="font-weight:bold">Date Grade Submitted:</td>
					<td><?= (($exam_grade_detail['created'] == '0000-00-00 00:00:00' || empty($exam_grade_detail['created']) || is_null($exam_grade_detail['created'])) ? '---' :  $this->Time->format("F j, Y h:i:s A", $exam_grade_detail['created'], NULL, NULL)); ?></td>
				</tr>
				<?php
			} ?>
			<tr>
				<td style="font-weight:bold;background-color:white;"><?= $approver_c; ?> Approval:</td>
				<td style="background-color:white;" class="<?= ($exam_grade_detail['department_approval'] == 1 ? 'accepted' : ($exam_grade_detail['department_approval'] == -1 && ($exam_grade_detail['department_reply'] && $exam_grade_detail['registrar_approval'] == 1) ? 'accepted' : 'rejected')); ?>"><?= ($exam_grade_detail['department_approval'] == 1 ? 'Accepted' : ($exam_grade_detail['department_approval'] == -1 ? 'Rejected' . (($exam_grade_detail['department_reply'] && $exam_grade_detail['registrar_approval'] == 1) ? '<span class="accepted" style="padding-left: 20px;">(Rejected Registrar Rejection)</span>' : '') : 'On Process')); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold"><?= ($exam_grade_detail['department_approval'] == -1 ? 'Rejected By:' : 'Approved By:'); ?></td>
				<td><?= (!empty($exam_grade_detail['department_approved_by_name']) ? $exam_grade_detail['department_approved_by_name'] : '---'); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;background-color:white;"><?= $approver_c; ?> Remark:</td>
				<td style="background-color:white;"><?= (!empty($exam_grade_detail['department_reason']) ? $exam_grade_detail['department_reason'] : '---'); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold"><?= $approver_c . ' ' . ($exam_grade_detail['department_approval'] == -1 ? 'Rejected Date:' : 'Approved Date:'); ?></td>
				<td><?= (($exam_grade_detail['department_approval_date'] == '0000-00-00 00:00:00' || empty($exam_grade_detail['department_approval_date']) || is_null($exam_grade_detail['department_approval_date'])) ? '---' : ($this->Time->format("F j, Y h:i:s A", $exam_grade_detail['department_approval_date'], NULL, NULL))); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;background-color:white;">Registrar Confirmation:</td>
				<td style="background-color:white;" class="<?= ($exam_grade_detail['registrar_approval'] == 1 ? 'accepted' : ($exam_grade_detail['registrar_approval'] == -1 ? 'rejected' : ($exam_grade_detail['department_approval'] != -1 ? 'on-process' : ''))); ?>"><?= ($exam_grade_detail['registrar_approval'] == 1 ? 'Accepted' : ($exam_grade_detail['registrar_approval'] == -1 ? 'Rejected' : ($exam_grade_detail['department_approval'] == 1 ? 'On Process' : '---'))); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold"><?= ($exam_grade_detail['registrar_approval'] == -1 ? 'Registrar Rejected By:' : 'Registrar Confirmed By:'); ?></td>
				<td><?= (!empty($exam_grade_detail['registrar_approved_by_name']) ? $exam_grade_detail['registrar_approved_by_name'] : '---'); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;background-color:white;">Registrar Remark:</td>
				<td style="background-color:white;"><?= (!empty($exam_grade_detail['registrar_reason']) ? $exam_grade_detail['registrar_reason'] : '---'); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold"><?= ($exam_grade_detail['registrar_approval'] == -1 ? 'Registrar Rejected Date:' : 'Registrar Confirmation Date:'); ?></th>
				<td><?= (($exam_grade_detail['registrar_approval_date'] == '0000-00-00 00:00:00' || empty($exam_grade_detail['registrar_approval_date']) || is_null($exam_grade_detail['registrar_approval_date'])) ? '---' : ($this->Time->format("F j, Y h:i:s A", $exam_grade_detail['registrar_approval_date'], NULL, NULL))); ?></td>
			</tr>
			<?php
		}
	}

	if (count($student_exam_grade_history) > 0) { ?>
		</table>
		<?php
	}
?>