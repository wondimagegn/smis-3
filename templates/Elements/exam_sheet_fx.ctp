<?php
if(!isset($grade_view_only))
	$grade_view_only = false;
?>
<style>
table.grade_list tr td{
	padding:0px;
	vertical-align:middle;
}
</style>

<table class="grade_list">
	<tr>
		<th style="width:2%">&nbsp;</th>
        <th style="width:2%">S.N<u>o</u></th>
		<th style="width:16%">Student Name</th>
		<th style="width:8%">Student ID</th>
		<?php
		$percent = 10;
		$last_percent = "";
		//It it is makeup exam entry
		if($grade_view_only) {
			//It is exam grade view only and there is nothing to do for now
			$percent = 10;
			$last_percent = 42;
		}
		else if($makeup_exam) {
			?><th style="width:<?php echo (!($grade_submission_status['grade_submited'] 
			|| $display_grade || $view_only) ? 72 : 10); ?>%">Total (100%)</th><?php
		$last_percent = 32;
		}
		//If it is not makeup exams (add and registered)
		else {
			$grade_width = 0;
			if($grade_submission_status['grade_submited'])
				$grade_width = 3;
			else if($display_grade || $view_only)
				$grade_width = 3;
			if(((100-28)/((count($exam_types)+1)+$grade_width)) > 10) {
				$last_percent = (100-28) - ((count($exam_types)+1+$grade_width)*10);
			}
			else
				$percent = ((100-28)/(count($exam_types)+1+$grade_width));
			$count_for_percent = 0;

			foreach($exam_types as $key => $exam_type) {
			$count_for_percent++;
			?>
			<th style="width:<?php echo ($count_for_percent == (count($exam_types)+1) && $last_percent != "" && !($grade_submission_status['grade_submited'] || $display_grade || $view_only) ? $last_percent + $percent : $percent); ?>%">
			<?php
			echo $exam_type['ExamType']['exam_name'].' ('.$exam_type['ExamType']['percent'].'%)';
			?>
			</th>
			<?php
			}
			?>
			<th style="width:<?php echo (!($grade_submission_status['grade_submited'] || $display_grade || $view_only) ?$last_percent + $percent : $percent); ?>%">Total (100%)</th>
			<?php
		}
		//End of non-makeup exams
		
		//It it is submited grade or on "grade preview" state
		if($view_only || $grade_submission_status['grade_submited'] || $display_grade) {
			?>
			<th style="width:<?php echo $percent; ?>%">Grade</th>
			<th style="width:<?php echo $percent; ?>%">
				In Progress
			</th>
			<th style="width:<?php echo ($last_percent != "" ? $last_percent + $percent : $percent); ?>%">Status</th>
			<?php
		}
		?>
	</tr>
	<?php
	//Building every student exam result entry
	//if(!$makeup_exam) {
		if(!isset($total_student_count))
			$total_student_count = count($students_process);
		debug($total_student_count);
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
			$st_count++;
		?>
		<tr>

			<td onclick="toggleView(this)" id="<?php echo $st_count; ?>"><?php echo $this->Html->image('plus2.gif', array('id' => 'i'.$st_count)); ?></td>
			<td><?php echo $st_count; ?></td>
			<td><?php
			echo $student['Student']['first_name'].' '.$student['Student']['middle_name'].' '.$student['Student']['last_name']; ?></td>
			<td><?php echo $student['Student']['studentnumber']; ?></td>
			<?php
			//If it is makeup exam entry
			if($grade_view_only) {
				//It is exam grade view only and there is nothing to do for now
			}
			else if($makeup_exam) {
				echo '<td>';
				if(!empty($student['ExamGradeChange']) && $student['ExamGradeChange'][0]['department_approval'] != -1) {
					echo ($student['MakeupExam']['result'] != null ? $student['MakeupExam']['result'] : '---');
				}
				else {
			if($display_grade || $view_only) {
			echo ($student['MakeupExam']['result'] != null ? $student['MakeupExam']['result'] : '---');
					}
					else {
						
						echo $this->Form->input('MakeupExam.'.$count.'.id', array('type' => 'hidden', 'value' => $student['MakeupExam']['id']));
						$input_options = array('type' => 'text', 'label' => false, 'maxlength' =>'5', 'style' => 'width:50px', 'id' => 'result_'.$st_count.'_1', 'onBlur' => 'updateExamTotal(this, '.$st_count.', 1, 100, \'Total\', false)');
						$input_options['value'] = ($student['MakeupExam']['result'] != null ? $student['MakeupExam']['result'] : '');
						echo $this->Form->input('MakeupExam.'.$count.'.result', $input_options);
						  $count++;
						}
				}
				echo '</td>';
			}
			//If it is non-makeup exams (add and registered)
			else {
				$et_count = 0;
				//Each mark entry for each exam type (foreach loop)
				foreach($exam_types as $key => $exam_type) {
					$et_count++;
				?>
				<td>
				<?php
				$id = "";
				$value ="";
				//Searching for the exam result from the databse returned value
				if(isset($student['ExamResult']) && !empty($student['ExamResult'])) {
					foreach($student['ExamResult'] as $key => $examResult) {
						if($examResult['exam_type_id'] == $exam_type['ExamType']['id']) {
							$id = $examResult['id'];	
							$value = $examResult['result'];
							$total_100 += $value;
							break;
					    }
				  }
				}
				//if save exam result button is clicked to add each exam result to get result sum
				$i = (($st_count-1)*count($exam_types))+1;
                
				if(isset($this->request->data['ExamResult'][$i]['result'])) {
					if(isset($this->request->data) && !$display_grade && !$view_only) {
						 $total_100 = "";

						for(; $i <= ((($st_count-1)*count($exam_types))+count($exam_types)); $i++) {
    		 		if(isset($this->request->data['ExamResult'][$i])) {
					
				 if($this->request->data['ExamResult'][$i]['result'] != "" && is_numeric($this->request->data['ExamResult'][$i]['result'])) {
				$total_100 += $this->request->data['ExamResult'][$i]['result'];
						}
  		 			}
				  }
			    }
		       }
				if($display_grade || $view_only || (!empty($student['ExamGrade']) && $student['ExamGrade'][0]['department_approval'] != -1)) {
					echo ($value != "" ? $value : '---');
				}
				else {
					//It is if it is on exam result edit mode
	                 //debug($id);
					if($id != "") {
		echo $this->Form->input('ExamResult.'.$count.'.id', array('type' => 'hidden', 'value' => $id));
						$input_options = array('type' => 'text', 'label' => false, 'maxlength' =>'5', 'style' => 'width:50px', 'id' => 'result_'.$st_count.'_'.$et_count, 'onBlur' => 'updateExamTotal(this, '.$st_count.', '.count($exam_types).', '.$exam_type['ExamType']['percent'].', \''.$exam_type['ExamType']['exam_name'].'\', true)');
	//if(!isset($this->request->data)) // || $display_grade)

$input_options['value'] = $value;						
$input_options['tabindex'] = (($total_student_count * ($et_count - 1)) + $st_count);
		echo $this->Form->input('ExamResult.'.$count.'.result', $input_options);
					}//End of exam result edit mode
					//New exam result entry
					else {
						echo $this->Form->input('ExamResult.'.$count.'.exam_type_id', array('type' => 'hidden', 'value' => $exam_type['ExamType']['id']));
						//Exam result entry for course registration
						if(isset($student['CourseRegistration'])) {
							echo $this->Form->input('ExamResult.'.$count.'.course_registration_id', array('type' => 'hidden', 'value' => $student['CourseRegistration']['id']));
							echo $this->Form->input('ExamResult.'.$count.'.course_add', array('type' => 'hidden', 'value' => 0));
						}
						//Exam result entry for course add
						else if(isset($student['CourseAdd'])){
							echo $this->Form->input('ExamResult.'.$count.'.course_registration_id', array('type' => 'hidden', 'value' => $student['CourseAdd']['id']));
							echo $this->Form->input('ExamResult.'.$count.'.course_add', array('type' => 'hidden', 'value' => 1));
						}
						//Exam result entry for makeup exam (now it becomes obsolete)
						echo $this->Form->input('ExamResult.'.$count.'.result', array('tabindex' => (($total_student_count * ($et_count - 1)) + $st_count), 'type' => 'text', 'label' => false, 'maxlength' =>'5', 'style' => 'width:50px', 'id' => 'result_'.$st_count.'_'.$et_count, 'onBlur' => 'updateExamTotal(this, '.$st_count.', '.count($exam_types).', '.$exam_type['ExamType']['percent'].', \''.$exam_type['ExamType']['exam_name'].'\', true)'));
					}//End of new exam result entry
				}//echo ' - '.$count.' - '.$st_count;
				?>
				</td>
				<?php
				$count++;
			}//End of each mark entry for each exam type (foreach loop)
			?>
			<td id="total_100_<?php echo $st_count; ?>"><?php echo ($total_100 !== "" ? $total_100 : '---'); ?></td>
			<?php
			}//End of non-makeup exams result entry
			?>
			<?php
			if($view_only || $display_grade || $grade_submission_status['grade_submited']) {
			?>
			<td id="G_<?php echo ++$in_progress; ?>">
				<?php
					//GRADE
					//If the grade is from the database (regisration and add)
					$latest_grade_detail = $student['LatestGradeDetail'];
					
					if($display_grade && isset($student['GeneratedExamGrade']))
						echo $student['GeneratedExamGrade']['grade'];
					//If it is makeup exam
					//The following condition will be skipped if if makeup exam result is changed in the form of grade change or supplementary exam
					else if(isset($student['MakeupExam']) && (empty($student['ExamGradeChange']) || $student['ExamGradeChange'][0]['created'] >= $latest_grade_detail['ExamGrade']['created']))// && $latest_grade_detail['ExamGrade']['makeup_exam_result'] != null) 
					{//debug($student['ExamGradeChange']);
						//If the grade is from the database (makeup)
						if(isset($student['ExamGradeChange']) && !empty($student['ExamGradeChange'])) {
							if($student['ExamGradeChange'][0]['department_approval'] == -1)
								echo '<p class="rejected">';
							echo $student['ExamGradeChange'][0]['grade'];
							if($student['ExamGradeChange'][0]['department_approval'] == -1)
								echo '</p>';
						}
						//If the course is on progress (Neither generated or saved)
						else
							echo '**';
					}
					//If the result is about course registration and add 
					//considering makeup and exam change
					else if(!empty($latest_grade_detail['ExamGrade'])) {
						//If the grade from course registration or add
						if((!isset($latest_grade_detail['ExamGrade']['auto_ng_conversion']) || $latest_grade_detail['ExamGrade']['auto_ng_conversion'] == 0) && (!isset($latest_grade_detail['ExamGrade']['manual_ng_conversion']) || $latest_grade_detail['ExamGrade']['manual_ng_conversion'] == 0) && $latest_grade_detail['ExamGrade']['department_approval'] == -1)
							echo '<p class="rejected">';
						echo $latest_grade_detail['ExamGrade']['grade'];
						if($latest_grade_detail['ExamGrade']['department_approval'] == -1)
							echo '</p>';
						if(strcasecmp($latest_grade_detail['type'], 'Change') == 0) {
							if($latest_grade_detail['ExamGrade']['makeup_exam_id'] == null && $latest_grade_detail['ExamGrade']['makeup_exam_result'] != null)
								echo ' (Supplementary)';
							else if($latest_grade_detail['ExamGrade']['makeup_exam_result'] != null)
								echo ' (Makeup)';
							else
								echo ' (Change)';
						}
					}
					
					else
						echo '**';
				?>
			</td>
			<td>
				<?php
				//IN PROGRESS
				$latest_grade_detail = $student['LatestGradeDetail'];
				//If the result is from the database (it can be registered, add considering its related makeup, and grade change)
				if(($grade_submission_status['grade_submited'] && !$display_grade) || $view_only) {
					//If garde is submitted
					if(isset($student['MakeupExam'])) {
						if(!isset($student['ExamGradeChange']) || empty($student['ExamGradeChange']) || ($student['ExamGradeChange'][0]['department_approval'] == -1 && $latest_grade_detail['ExamGrade']['auto_ng_conversion'] == 0 && $latest_grade_detail['ExamGrade']['manual_ng_conversion'] == 0))
							echo '<p class="on-process">Yes</p>';
						else
							echo '<p class="accepted">No</p>';
					}
					else {
						if((empty($latest_grade_detail['ExamGrade']) || $latest_grade_detail['ExamGrade']['department_approval'] == -1) && (!isset($latest_grade_detail['ExamGrade']['auto_ng_conversion']) || $latest_grade_detail['ExamGrade']['auto_ng_conversion'] == 0) && (!isset($latest_grade_detail['ExamGrade']['manual_ng_conversion']) || $latest_grade_detail['ExamGrade']['manual_ng_conversion'] == 0))
							echo '<p class="on-process">Yes</p>';
						else
							echo '<p class="accepted">No</p>';
					}
				}
			
				//If it is on grade preview mode
				else {
					//If grade is not saved in the database or rejected by the department
					if((isset($student['MakeupExam']) && (!isset($student['ExamGradeChange']) || (isset($student['ExamGradeChange']) && (empty($student['ExamGradeChange']) || $student['ExamGradeChange'][0]['department_approval'] == -1))))
						|| ((!isset($student['MakeupExam']) && !isset($student['ExamGrade'])) || (isset($student['ExamGrade']) && (empty($student['ExamGrade']) || $student['ExamGrade'][0]['department_approval'] == -1)))) {
							if(!$student['GeneratedExamGrade']['fully_taken'])
							echo $this->Form->input('InProgress.'.$in_progress.'.in_progress', array('type' => 'checkbox', 'value' => $student['Student']['id'], 'label' => false, 'onclick' => 'courseInProgress('.$in_progress.', this)', 'hiddenField' => false));
						else
							echo '---';
					}
					//If the garde is already in the database
					else
						echo '<p class="accepted">No</p>';
				}
				?>
			</td>
			<td>
				<?php

				//STATUS: Status of grade submision
				$latest_grade_detail = $student['LatestGradeDetail'];
				//Make up exam

				if(isset($student['MakeupExam'])){
				
					if(!isset($student['ExamGradeChange']) || empty($student['ExamGradeChange']))
						echo '<p class="on-process">Grade not submitted</p>';
					else if($student['ExamGradeChange']['0']['department_approval'] == null)
						echo '<p class="on-process">Waiting for '.$approver.' approval</p>';
					else if($student['ExamGradeChange']['0']['department_approval'] == -1)
						if($display_grade)
							echo '<p class="on-process">Re-grade is not submitted</p>';
						else
							echo '<p class="rejected">Grade is rejected by '.$approver.'</p>';
					else
						{
						if($student['ExamGradeChange']['0']['registrar_approval'] == null) {
							if($student['ExamGradeChange']['0']['initiated_by_department'] == 1)
								echo '<p class="on-process">Requested by '.$approver.', waiting for registrar approval</p>';
							else
								echo '<p class="on-process">Approved by '.$approver.', waiting for registrar approval</p>';
						}
						else if($student['ExamGradeChange']['0']['registrar_approval'] == -1) {
							if($student['ExamGradeChange']['0']['initiated_by_department'] == 1)
								echo '<p class="rejected">Requested by '.$approver.', but rejected by registrar</p>';
							else
								echo '<p class="rejected">Approved by '.$approver.', but rejected by registrar</p>';
						}
						else
							echo '<p class="accepted">Accepted</p>';
						}
				}
				else if(!empty($latest_grade_detail['ExamGrade'])) {
					//If it is registration or add
					if(strcasecmp($latest_grade_detail['type'], 'Register') == 0 ||
						strcasecmp($latest_grade_detail['type'], 'Add') == 0 ||
						(strcasecmp($latest_grade_detail['type'], 'Change') == 0 &&
						 $latest_grade_detail['ExamGrade']['makeup_exam_result'] != null) ) {
						if($latest_grade_detail['ExamGrade']['department_approval'] == null)
							echo '<p class="on-process">Waiting for '.$approver.' approval</p>';
						else if($latest_grade_detail['ExamGrade']['department_approval'] == -1)
							if($display_grade)
								echo '<p class="on-process">Re-grade is not submitted</p>';
							else
								echo '<p class="rejected">Grade is rejected by '.$approver.'</p>';
						else {
							if($latest_grade_detail['ExamGrade']['registrar_approval'] == null) {
								if(strcasecmp($latest_grade_detail['type'], 'Change') == 0 &&
									$latest_grade_detail['ExamGrade']['initiated_by_department'] == 1)
									echo '<p class="on-process">Requested by '.$approver.' and waiting for registrar approval</p>';
								else
									echo '<p class="on-process">Approved by '.$approver.', waiting for registrar approval</p>';
							}
							else if($latest_grade_detail['ExamGrade']['registrar_approval'] == -1) {
								if(strcasecmp($latest_grade_detail['type'], 'Change') == 0 &&
									$latest_grade_detail['ExamGrade']['initiated_by_department'] == 1)
									echo '<p class="rejected">Requested by '.$approver.', but rejected by registrar</p>';
								else
									echo '<p class="rejected">Approved by '.$approver.', but rejected by registrar</p>';
							}
							else
								echo '<p class="accepted">Accepted</p>';
						}
					}
					//If it is exam grade change
					else {
						if($latest_grade_detail['ExamGrade']['manual_ng_conversion'] == 1) {
							echo '<span class="accepted">NG Grade Converted</span>';
						}
						else if($latest_grade_detail['ExamGrade']['auto_ng_conversion'] == 1) {
							echo '<span class="accepted">Automatic F</span>';
						}
						else {
							if($latest_grade_detail['ExamGrade']['initiated_by_department'] == 1 || $latest_grade_detail['ExamGrade']['department_approval'] == 1) {
								if($latest_grade_detail['ExamGrade']['college_approval'] == 1 || $latest_grade_detail['ExamGrade']['makeup_exam_result'] != null) {
									if($latest_grade_detail['ExamGrade']['registrar_approval'] == 1) {
										echo '<p class="accepted">Accepted</p>';
									}
									else if($latest_grade_detail['ExamGrade']['registrar_approval'] == -1)
										echo '<p class="rejected">Approved by '.$approver.' and college but rejected by registrar approval.</p>';
									else if($latest_grade_detail['ExamGrade']['registrar_approval'] == null)
										echo '<p class="on-process">Approved by '.$approver.' and college and waiting for registrar approval.</p>';
								}
								else if($latest_grade_detail['ExamGrade']['college_approval'] == -1)
									echo '<p class="rejected">Approved by '.$approver.' but rejected by college</p>';
								else if($latest_grade_detail['ExamGrade']['college_approval'] == null)
									echo '<p class="on-process">Approved by '.$approver.' and waiting for college approval.</p>';
							}
							else if($latest_grade_detail['ExamGrade']['department_approval'] == -1)
								echo '<p class="rejected">Rejected by '.$approver.'</p>';
							else if($latest_grade_detail['ExamGrade']['department_approval'] == null)
								echo '<p class="on-process">Waiting for '.$approver.' approval</p>';
						}
					}
				}
				else
					echo '<p class="on-process">Grade not submitted</p>';
				
				?>
			</td>
			<?php
			}
			?>
		</tr>
		<tr id="c<?php echo $st_count; ?>" style="display:none">
			<td colspan="<?php 
				if($view_only || $grade_submission_status['grade_submited'] || $display_grade)
					$grade_width = 3;
				else
					$grade_width = 0;
				if($makeup_exam) 
					$colspan = ($grade_width + 4); 
				else 
					$colspan = ($grade_width + 3 + count($exam_types) + 1); 
				echo $colspan; ?>">
				<?php
				if(isset($student['MakeupExam']) && isset($student['ExamGradeChange']) && count($student['ExamGradeChange']) > 0) {
					?>
					<table>
						<tr>
							<td style="width:18%; font-weight:bold">Makeup Exam Minute Number:</td>
							<td style="width:82%"><?php echo $student['ExamGradeChange'][0]['minute_number']; ?></td>
						</tr>
					</table>
					<?php
				}
				$register_or_add = 'gh';
				if(isset($student['ExamGradeHistory']))
					$grade_history = $student['ExamGradeHistory'];
				else
					$grade_history = array();
				$this->set(compact('register_or_add', 'grade_history', 'freshman_program'));
				?>
				<table>
					<tr>
						<td style="vertical-align:top; width:40%"><?php
						echo $this->element('registered_or_add_course_grade_history'); ?></td>
						<td style="vertical-align:top; width:60%">
						<?php
//debug($student['ExamGradeHistory']);
                        
						if (isset($student['ExamGradeHistory'][0]['ExamGrade'])
						 && !empty($student['ExamGradeHistory'][0]['ExamGrade'])) {
						
						$date_grade_submited = $student['ExamGradeHistory'][0]['ExamGrade']['created'];
						$grade_change_deadline = 
						date('Y-m-d H:i:s', mktime (substr($date_grade_submited,11 ,2), 
						substr($date_grade_submited,14 ,2), 
						substr($date_grade_submited,17 ,2), 
						substr($date_grade_submited,5 ,2), 
						substr($date_grade_submited,8 ,2)+(isset($days_available_for_grade_change) ? $days_available_for_grade_change : 0), 
						substr($date_grade_submited,0 ,4)));
						$grade_history_count = count($student['ExamGradeHistory']);
						
						}
						
						if($grade_view_only) {
							//It is exam grade view only and there is nothing to do for now
						}
						else if(!$student['AnyExamGradeIsOnProcess'] && isset($days_available_for_grade_change) &&
								((!isset($student['MakeupExam']) && isset($student['ExamGrade']) && !empty($student['ExamGrade']) && $student['ExamGrade'][0]['registrar_approval'] == 1)
								||
						(isset($student['MakeupExam']) && !empty($student['ExamGradeChange']) && ($student['ExamGradeChange'][0]['registrar_approval'] == 1 || $student['ExamGradeChange'][0]['makeup_exam_result'] == null)))) {
							
						}
						else if($grade_history_count > 1){
							$last_grade_change = $student['ExamGradeHistory'][$grade_history_count-1];
							//If the grade change is initiated by department and the action is 
							//from non-native-instructor
							if((strcasecmp($this->request->action, 'add') != 0 && 
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
								$last_grade_change['ExamGrade']['makeup_exam_result'] == null)) {
								echo 'You have grade change request placed by you. You can '.$this->Html->link(__('Cancel'), array('action' => 'cancel_grade_change_request', $last_grade_change['ExamGrade']['id']), null, sprintf(__('Are you sure you want to cancel your grde change request for %s?'), $student['Student']['first_name'].' '.$student['Student']['middle_name'].' '.$student['Student']['last_name'].' ('.$student['Student']['studentnumber'].')')).' the request before it is processed.';
							}
							
						}
						?>
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
}
?>
</table>
