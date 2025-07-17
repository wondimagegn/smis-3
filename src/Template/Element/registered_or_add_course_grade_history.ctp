<?php
use Cake\I18n\Time;

if (isset($freshman_program) && $freshman_program) {
	$approver = 'freshman program';
} else {
	$approver = 'department';
}
echo '<div style="font-weight:bold; font-size:14px">Grade History Summary (From old to recent)</div>';

if ($register_or_add) {
	if (!empty($grade_history)) {
		foreach ($grade_history as $key => $grade) {
			if (strcasecmp($grade['type'], 'Add') == 0) {
				echo '<b>Course Added:</b> ';
			} else if (strcasecmp($grade['type'], 'Register') == 0) {
				echo '<b>Course Registered:</b> ';
			} else if (!is_null($grade['ExamGrade']['makeup_exam_result']) && is_null($grade['ExamGrade']['makeup_exam_id'])) {
				echo '<b>Supplementary Exam:</b> ';
			} else if (!is_null($grade['ExamGrade']['makeup_exam_result'])) {
				echo '<b>Makeup Exam:</b> ';
			} else {
				echo '<b>Grade Change:</b> ';
			}
			if ((strcasecmp($grade['type'], 'Add') == 0 || strcasecmp($grade['type'], 'Register') == 0)) {
				if (empty($grade['ExamGrade'])) {
					echo '<br /><i style="padding-left:15px"><b>Status:</b></i> <span class="on-process">Waiting for Grade Submission.</span>';
				} else {
					echo '<strong>' . $grade['ExamGrade']['grade'] . '</strong> ' . ($grade['ExamGrade']['department_reply'] == 1 ? '(Re-Submit)' : '') .
                        ' (' .$grade['ExamGrade']['created'] instanceof \Cake\I18n\FrozenTime
                        ? $grade['ExamGrade']['created']->format('F j, Y h:i:s A')
                        : (new \Cake\I18n\Time($grade['ExamGrade']['created']))->format('F j, Y h:i:s A'). ')';
					echo '<br /><i style="padding-left:15px"><b>Status:</b> </i> ';
					if ($grade['ExamGrade']['department_approval'] == 1 && $grade['ExamGrade']['registrar_approval'] == 1) {
						echo '<span class="accepted">Accepted</span>';
					} else if ($grade['ExamGrade']['department_approval'] == 1 && $grade['ExamGrade']['registrar_approval'] == null) {
						echo '<span class="on-process">Approved by ' . $approver . ', waiting for registrar confirmation.</span>';
					} else if ($grade['ExamGrade']['department_approval'] == 1 && $grade['ExamGrade']['registrar_approval'] == -1) {
						echo '<span class="rejected">Approved by ' . $approver . ' but rejcted by registrar.</span>';
					} else if ($grade['ExamGrade']['department_approval'] == null) {
						echo '<span class="on-process">Waiting for ' . $approver . ' approval.</span>';
					} else if ($grade['ExamGrade']['department_approval'] == -1) {
						if ($grade['ExamGrade']['registrar_approval'] == 1) {
							echo '<span class="accepted">Accepted.</span>';
						} else if ($grade['ExamGrade']['department_reply'] == 1 && $grade['ExamGrade']['department_approval'] == -1 && is_null($grade['ExamGrade']['registrar_approval'])) {
							echo '<span class="on-process">Waiting for registrar\'s response for ' . $approver . '\'s rejection of previously rejected grade by the registrar.</span>';
						} else if ($grade['ExamGrade']['department_reply'] == 1 && $grade['ExamGrade']['department_approval'] == -1 && $grade['ExamGrade']['registrar_approval'] == -1) {
							echo '<span class="on-process">Waiting for ' . $approver . '\'s response for registrar\'s consequetive rejections(two or more times).</span>';
						} else if (($grade['ExamGrade']['department_approval'] == -1 && is_null($grade['ExamGrade']['registrar_approval'])) || ($grade['ExamGrade']['department_reply'] == 1 && $grade['ExamGrade']['department_approval'] == 1) || ($grade['ExamGrade']['department_reply'] == 1 && $grade['ExamGrade']['department_approval'] == 1 && $grade['ExamGrade']['registrar_approval'] == -1)) {
							if ($grade['ExamGrade']['department_reply'] == 1 && $grade['ExamGrade']['department_approval'] == 1 && $grade['ExamGrade']['registrar_approval'] == -1) {
								echo '<span class="on-process">Waiting for Instructor grade re-submission in response for ' . $approver . '\'s acceptance of previously rejected grade by the registrar.</span>';
							} else {
								echo '<span class="on-process">Waiting for Instructor grade re-submission in response for '.  $approver . '\'s rejection of previously submitted grade by the instructor.</span>';
							}
						} else {
							echo '<span class="rejected">Rejected by the ' . $approver . '</span>';
						}
					}
				}

				echo (isset($grade['result']) && !empty($grade['result']) && !is_array($grade['result']) && isset($grade['ResultEntryAssignment']) ? '<br><span style="margin-left: 15px;" class="rejected"><i><b>From Instructor Result Entry Assignment</i></b></span>' : '');

				echo '<br/><b>Assessement Detail:</b> <br/>';

				if (isset($grade['result']) && !empty($grade['result']) && is_array($grade['result'])) {
					$assessment_sum = 0;
					$percent = 0;
					foreach ($grade['result'] as $k => $v) {
						$kk = explode('%', $k);
						$kkK = explode('(', $kk[0]);
						$percent += $kkK[1];
						if ($percent <= 100) {
							echo $k . '=' . (is_numeric($v) ? $v . ', ' : ' ');
							if (is_numeric($v)) {
								$assessment_sum += $v;
							} else {
								echo '--, ';
							}
						}
					}
					echo '&nbsp;&nbsp; <b>Total('.$percent.'%) = '. ($assessment_sum ? $assessment_sum : '--')  .'</b>';
				} else if (isset($grade['result']) && !empty($grade['result']) && !is_array($grade['result'])) {
					echo '&nbsp;&nbsp; <b>Total(100%) = '. ($grade['result'] ? $grade['result'] : '--') .'</b>';
				} else {
					echo '&nbsp;&nbsp; <b>Assesment Data not available</b>';
				}

			} else {
				//Grade change or makeup exam
				if (empty($grade['ExamGrade'])) {
					echo '<br /><i style="padding-left:15px"><b>Status:</b></i> <span class="on-process">Waiting for Grade Submission.</span>';
				} else {
					//debug($grade['ExamGrade']);
					echo '<strong>' . $grade['ExamGrade']['grade'] . '</strong> ' . ($grade['ExamGrade']['department_reply'] == 1 ? '(Re-Submit)' : '') .
                        ' (' .$grade['ExamGrade']['created'] instanceof \Cake\I18n\FrozenTime
                        ? $grade['ExamGrade']['created']->format('F j, Y h:i:s A')
                        : (new \Cake\I18n\Time($grade['ExamGrade']['created']))->format('F j, Y h:i:s A').')';
					echo '<br /><i style="padding-left:15px"><b>Status:</b></i> ';
					if ($grade['ExamGrade']['manual_ng_conversion'] == 1) {
						echo '<span class="accepted"><b>NG Grade Converted</b></span>';
					} else if ($grade['ExamGrade']['auto_ng_conversion'] == 1) {
						echo '<span class="accepted"><b>Automatic F</b></span>';
					} else {
						if (($grade['ExamGrade']['initiated_by_department'] == 1 || $grade['ExamGrade']['department_approval'] == 1) && $grade['ExamGrade']['department_approval'] != -1) {
							if ($grade['ExamGrade']['college_approval'] == 1 || $grade['ExamGrade']['makeup_exam_result'] != null) {
								if ($grade['ExamGrade']['registrar_approval'] == 1) {
									echo '<span class="accepted">Accepted</span>';
								} else if ($grade['ExamGrade']['registrar_approval'] == -1) {
									if ($grade['ExamGrade']['makeup_exam_result'] == null) {
										echo '<span class="rejected">Approved by ' . $approver . ' and college but rejected by registrar.</span>';
									} else {
										if ($grade['ExamGrade']['initiated_by_department'] == 1) {
											echo '<span class="rejected">Requested by ' . $approver . ' but rejected by registrar.</span>';
										} else {
											echo '<span class="rejected">Approved by ' . $approver . ' but rejected by registrar.</span>';
										}
									}
								} else if ($grade['ExamGrade']['registrar_approval'] == null) {
									if ($grade['ExamGrade']['makeup_exam_result'] == null) {
										echo '<span class="on-process">Approved by ' . $approver . ' and college and waiting for registrar confirmation.</span>';
									} else {
										if ($grade['ExamGrade']['initiated_by_department'] == 1) {
											echo '<span class="on-process">Requested by ' . $approver . ', waiting for registrar confirmation.</span>';
										} else {
											echo '<span class="on-process">Approved by ' . $approver . ', waiting for registrar confirmation.</span>';
										}
									}
								}
							} else if ($grade['ExamGrade']['college_approval'] == -1) {
								echo '<span class="rejected">Approved by ' . $approver . ' but rejected by college.</span>';
							} else if ($grade['ExamGrade']['college_approval'] == null) {
								echo '<span class="on-process">Approved by ' . $approver . ', waiting for college approval.</span>';
							}
						} else if ($grade['ExamGrade']['department_approval'] == -1) {
							if ($grade['ExamGrade']['registrar_approval'] == 1) {
								echo '<span class="accepted">Accepted.</span>';
							} else if ($grade['ExamGrade']['department_reply'] == 1 && $grade['ExamGrade']['department_approval'] == -1 && is_null($grade['ExamGrade']['registrar_approval'])) {
								echo '<span class="on-process">Waiting for registrar\'s response for ' . $approver . '\'s rejection of previously rejected grade by the registrar.</span>';
							} else if ($grade['ExamGrade']['department_reply'] == 1 && $grade['ExamGrade']['department_approval'] == -1 && $grade['ExamGrade']['registrar_approval'] == -1) {
								echo '<span class="on-process">Waiting for ' . $approver . '\'s response for registrar\'s consequetive rejections(two or more times).</span>';
							} else if (($grade['ExamGrade']['department_approval'] == -1 && is_null($grade['ExamGrade']['registrar_approval'])) || ($grade['ExamGrade']['department_reply'] == 1 && $grade['ExamGrade']['department_approval'] == 1) || ($grade['ExamGrade']['department_reply'] == 1 && $grade['ExamGrade']['department_approval'] == 1 && $grade['ExamGrade']['registrar_approval'] == -1)) {
								if ($grade['ExamGrade']['department_reply'] == 1 && $grade['ExamGrade']['department_approval'] == 1 && $grade['ExamGrade']['registrar_approval'] == -1) {
									echo '<span class="on-process">Waiting for Instructor grade re-submission in response for ' . $approver . '\'s acceptance of previously rejected grade by the registrar.</span>';
								} else {
									echo '<span class="on-process">Waiting for Instructor grade re-submission in response for '.  $approver . '\'s rejection of previously submitted grade by the instructor.</span>';
								}
							} else {
								echo '<span class="rejected">Rejected by the ' . $approver . '</span>';
							}
						} else if ($grade['ExamGrade']['department_approval'] == null) {
							echo '<span class="on-process">Waiting for ' . $approver . ' approval.</span>';
						}
					}
				}
			}
			echo '<br />';
		}
	} else {
		echo 'There is no recorded/submited grade for the selected course.';
	}
} else {
	echo '---';
}
