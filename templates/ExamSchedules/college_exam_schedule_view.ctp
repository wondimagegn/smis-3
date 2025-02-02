<script type="text/javascript">
function toggleView(obj) {
	if($('#c'+obj.id).css("display") == 'none')
		$('#i'+obj.id).attr("src", '/img/minus2.gif');
	else
		$('#i'+obj.id).attr("src", '/img/plus2.gif');
	$('#c'+obj.id).toggle("slow");
}

function updateClassRoomList(obj, exam_schedule_id) {
	if(obj.value == 0) {
		$("#class_room_id_"+exam_schedule_id).empty();
		$("#class_room_id_"+exam_schedule_id).append('<option value="0">--- Select Class Room ---</option>');
		return false;
	}
	$("#class_room_block_id_"+exam_schedule_id).attr('disabled', true);
	$("#class_room_id_"+exam_schedule_id).attr('disabled', true);
	$("#ChangeClassRoom_"+exam_schedule_id).attr('disabled', true);
	$("#class_room_id_"+exam_schedule_id).empty();
	$("#class_room_id_"+exam_schedule_id).append('<option>Loading ...</option>');
	//get form action
	var formUrl = '/class_room_blocks/get_class_room_block_exam_rooms/'+obj.value;
	$.ajax({
		type: 'get',
		url: formUrl,
		data: obj.value,
		success: function(data,textStatus,xhr){
			$("#class_room_block_id_"+exam_schedule_id).attr('disabled', false);
			$("#class_room_id_"+exam_schedule_id).attr('disabled', false);
			$("#ChangeClassRoom_"+exam_schedule_id).attr('disabled', false);
			$("#class_room_id_"+exam_schedule_id).empty();
			$("#class_room_id_"+exam_schedule_id).append(data);
		},
		error: function(xhr,textStatus,error){
			alert(textStatus);
		}
	});
	return false;
}

function updateInvigilatorList(obj, exam_schedule_id) {
	if(obj.value == 0) {
		$("#invigilator_id_"+exam_schedule_id).empty();
		$("#invigilator_id_"+exam_schedule_id).append('<option value="0">--- Select Invigilator ---</option>');
		return false;
	}
	$("#department_id_"+exam_schedule_id).attr('disabled', true);
	$("#invigilator_id_"+exam_schedule_id).attr('disabled', true);
	$("#AddInvigilator_"+exam_schedule_id).attr('disabled', true);
	$("#invigilator_id_"+exam_schedule_id).empty();
	$("#invigilator_id_"+exam_schedule_id).append('<option>Loading ...</option>');
	//get form action
	var formUrl = '/staffs/get_department_staffs/'+obj.value;
	$.ajax({
		type: 'get',
		url: formUrl,
		data: obj.value,
		success: function(data,textStatus,xhr){
			$("#department_id_"+exam_schedule_id).attr('disabled', false);
			$("#invigilator_id_"+exam_schedule_id).attr('disabled', false);
			$("#AddInvigilator_"+exam_schedule_id).attr('disabled', false);
			$("#invigilator_id_"+exam_schedule_id).empty();
			$("#invigilator_id_"+exam_schedule_id).append(data);
		},
		error: function(xhr,textStatus,error){
			alert(textStatus);
		}
	});
	return false;
}
</script>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="examSchedules form">
<?php echo $this->Form->create('ExamSchedule');
$schedule_count = 1;
?>
<div class="smallheading"><?php echo __('Exam Schedule View'); ?></div>
	<table>
		<tr>
			<td style="width:10%">Academic Year:</td>
			<td style="width:24%"><?php echo $this->Form->input('acadamic_year', array('options' => $acadamicYears, 'label' => false, 'style' => 'width:150px')); ?></td>
			<td style="width:10%">Semester:</td>
			<td style="width:23%"><?php echo $this->Form->input('semester', array('options' => $semesters, 'label' => false, 'style' => 'width:150px')); ?></td>
			<td style="width:10%">Program:</td>
			<td style="width:23%"><?php echo $this->Form->input('program_id', array('options' => $programs, 'label' => false, 'style' => 'width:200px')); ?></td>
		</tr>
		<tr>
			<td>Program Type:</td>
			<td><?php echo $this->Form->input('program_type_id', array('options' => $programTypes, 'label' => false, 'style' => 'width:200px;height:auto;', 'multiple' => true)); ?></td>
			<td>Department:</td>
			<td><?php echo $this->Form->input('department_id', array('options' => $departments, 'label' => false, 'style' => 'width:200px;height:auto;', 'multiple' => true)); ?></td>
			<td>Year Level:</td>
			<td><?php echo $this->Form->input('year_level', array('options' => $yearLevels, 'label' => false, 'style' => 'width:200px;height:auto;', 'multiple' => true)); ?></td>
		</tr>
		<tr>
			<td colspan="6">
				<table>
					<tr>
						<td style="width:20%"><?php echo $this->Form->input('organize_by_department', array('type' => 'checkbox', 'checked' => ((!isset($this->request->data['ExamSchedule']['organize_by_department']) || $this->request->data['ExamSchedule']['organize_by_department'] == 1) ? 'checked' : 'false'))); ?></td>
						<td style="width:80%"><?php echo $this->Form->input('organize_by_year_level', array('type' => 'checkbox', 'checked' => ((!isset($this->request->data['ExamSchedule']['organize_by_year_level']) || $this->request->data['ExamSchedule']['organize_by_year_level'] == 1) ? 'checked' : 'false'))); ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="6">
			<?php echo $this->Form->submit(__('View Exam Schedule'), array('name' => 'viewExamSchedule', 'id' => 'ViewExamSchedule',
'class'=>'tiny radius button bg-blue','div' => false)); ?>
			</td>
		</tr>
	</table>
	<?php
	//Organize by department and year level
	if(isset($examSchedules) && $this->request->data['ExamSchedule']['organize_by_department'] == 1 && $this->request->data['ExamSchedule']['organize_by_year_level'] == 1) {
		?>
		<table class="fs13">
		<?php
		foreach($examSchedules as $department_name => $examSchedulesByYearLevel) {
			foreach($examSchedulesByYearLevel as $year_level => $subExamSchedules) {
				?>
				<tr>
					<td colspan="7" class="fs14">
					Academic Year: <strong><?php echo $academic_year; ?></strong>,
					Semester: <strong><?php echo $semester; ?></strong>,
					Program: <strong><?php echo $program_name; ?></strong>,
					Program Type: <strong>
					<?php
						foreach($program_types_name as $k => $program_type_name) {
							if(count($program_types_name) > 1 && $k > 0) {
								if((count($program_types_name)-1) == $k)
									echo ' &amp; ';
								else if(count($program_types_name) > 1) {
									echo ', ';
								}
							}
							echo $program_type_name;
						}
					?>
					</strong><br />
					Department: <strong><?php echo $department_name; ?></strong>,
					Year Level: <strong><?php echo $year_level.($year_level == 1 ? 'st' : ($year_level == 2 ? 'nd' : ($year_level == 3 ? 'rd' : 'th'))); ?></strong>
					</td>
				</tr>
				<tr>
					<th style="width:4%">&nbsp;</th>
					<th style="width:8%">Exam Date</th>
					<th style="width:8%">Session</th>
					<th style="width:17%">Section</th>
					<th style="width:10%">Course Code</th>
					<th style="width:38%">Course Title</th>
					<th style="width:15%">Exam Hall</th>
				</tr>
				<?php
				$count = 0;
				foreach($subExamSchedules as $examSchedule) {
				$count++;
				?>
				<tr>
					<td onclick="toggleView(this)" id="<?php echo $count; ?>"><?php echo $this->Html->image('plus2.gif', array('id' => 'i'.$count)); ?></td>
					<td><?php echo $this->Format->humanize_date_short($examSchedule['ExamSchedule']['exam_date']); ?></td>
					<td><?php echo ($examSchedule['ExamSchedule']['session'] == 1 ? 'Morning' : ($examSchedule['ExamSchedule']['session'] == 2 ? 'Afternoon' : 'Evening')); ?></td>
					<td><?php //debug($examSchedule);
						echo $examSchedule['PublishedCourse']['Section']['name'].' (IC: '.$examSchedule['PublishedCourse']['id'].')';
						if(isset($examSchedule['ExamSplitSection']['section_name'])) {
							echo ' ('.$examSchedule['ExamSplitSection']['section_name'].')';
						}
					?></td>
					<td><?php echo $examSchedule['PublishedCourse']['Course']['course_code']; ?></td>
					<td><?php echo $examSchedule['PublishedCourse']['Course']['course_title']; ?></td>
					<td><?php echo $examSchedule['ClassRoom']['room_code'].' ('.$examSchedule['ClassRoom']['ClassRoomBlock']['block_code'].')'; ?></td>
				</tr>
				<tr id="c<?php echo $count; ?>" style="display:none">
					<td colspan="7">
						<table>
							<tr>
								<td style="width:30%">
									<strong>Invigilator/s</strong>
									<ol>
									<?php
									//debug($examSchedule['Invigilator']);
										$number_of_invigilator = (isset($examSchedule['ClassRoom']['ExamRoomNumberOfInvigilator'][0]['number_of_invigilator']) ? $examSchedule['ClassRoom']['ExamRoomNumberOfInvigilator'][0]['number_of_invigilator'] : 2);
										if(count($examSchedule['Invigilator']) > $number_of_invigilator) {
											$number_of_invigilator = count($examSchedule['Invigilator']);
										}
										for($i = 0; $i < $number_of_invigilator; $i++) {
											if(isset($examSchedule['Invigilator'][$i])) {
												if(!empty($examSchedule['Invigilator'][$i]['Staff'])) {
													echo '<li>'.$examSchedule['Invigilator'][$i]['Staff']['full_name'].' ('.$this->Html->link(__('Cancel'), array('action' => 'cancel_invigilator_assignment', $examSchedule['Invigilator'][$i]['id']), null, sprintf(__('Are you sure you want to cancel "%s" assignment as an invigilator?'), $examSchedule['Invigilator'][$i]['Staff']['full_name'])).')</li>';
												}
												else {
													echo '<li>'.$examSchedule['Invigilator'][$i]['StaffForExam']['Staff']['full_name'].' ('.$this->Html->link(__('Cancel'), array('action' => 'cancel_invigilator_assignment', $examSchedule['Invigilator'][$i]['id']), null, sprintf(__('Are you sure you want to cancel "%s" assignment as an invigilator?'), $examSchedule['Invigilator'][$i]['StaffForExam']['Staff']['full_name'])).')</li>';
												}
											}
											else
												echo '<li>TBA</li>';
										}
									?>
									</ol>
								</td>
								<td style="width:70%">
									Important Note: All displayed list options are not enforced with constraint. You are free to select any value but the system will give you warning if there is schedule conflict and/or constraint is not fulfilled.
									<table>
										<tr>
											<th colspan="3"><strong>Change Exam Hall</strong></th>
										</tr>
										<tr>
											<td style="width:20%">Class Room Block:</td>
											<td colspan="2" style="width:80%"><?php 
											echo $this->Form->input('exam_schedule_id_'.$schedule_count, array('type' => 'hidden', 'value' => $examSchedule['ExamSchedule']['id']));
											echo $this->Form->input('class_room_block_id_'.$schedule_count, array('id' => 'class_room_block_id_'.$schedule_count, 'type' => 'select', 'onchange' => 'updateClassRoomList(this, \''.$schedule_count.'\')', 'label' => false, 'options' => $class_room_blocks, 'style' => 'width:250px')); ?></td>
										</tr>
										<tr>
											<td style="width:20%">Exam Hall:</td>
											<td style="width:50%"><?php echo $this->Form->input('class_room_id_'.$schedule_count, array('id' => 'class_room_id_'.$schedule_count, 'options' => $class_rooms, 'type' => 'select', 'label' => false, 'style' => 'width:250px')); ?></td>
											<td style="width:30%"><?php echo $this->Form->submit(__('Change Exam Hall'), array('name' => 'changeClassRoom_'.$schedule_count, 'id' => 'ChangeClassRoom_'.$schedule_count, 'div' => false)); ?></td>
										</tr>
										<tr>
											<th colspan="3"><strong>Change Exam Date</strong></th>
										</tr>
										<tr>
											<td style="width:20%">Exam Date:</td>
											<td style="width:40%"><?php
											echo $this->Form->input('exam_date_'.$schedule_count, array('label' => false, 'type' => 'date', 'dateFormat' => 'MDY', 'minYear' => date('Y'), 'maxYear' => date('Y')+1, 'orderYear' => 'desc', 'selected' => array('year' => substr($examSchedule['ExamSchedule']['exam_date'], 0, 4), 'month' => substr($examSchedule['ExamSchedule']['exam_date'], 5, 2), 'day' => substr($examSchedule['ExamSchedule']['exam_date'], 8, 2)))); ?></td>
											<td style="width:40%"><?php echo $this->Form->submit(__('Change Exam Date'), array('name' => 'changeExamDate_'.$schedule_count, 'id' => 'ChangeExamDate_'.$schedule_count, 'div' => false)); ?></td>
										</tr>
										<tr>
											<th colspan="3"><strong>Change Exam Session</strong></th>
										</tr>
										<tr>
											<td style="width:20%">Exam Session:</td>
											<td style="width:40%"><?php
											echo $this->Form->input('session_'.$schedule_count, array('label' => false, 'options' => $sessions, 'style' => 'width:200px', 'default' => $examSchedule['ExamSchedule']['session'])); ?></td>
											<td style="width:40%"><?php echo $this->Form->submit(__('Change Exam Session'), array('name' => 'changeExamSession_'.$schedule_count, 'id' => 'ChangeExamSession_'.$schedule_count, 'div' => false)); ?></td>
										</tr>
										<tr>
											<th colspan="3"><strong>Add Invigilator</strong></th>
										</tr>
										<tr>
											<td style="width:20%">Department:</td>
											<td colspan="2" style="width:80%"><?php
											echo $this->Form->input('department_id_'.$schedule_count, array('label' => false, 'id' => 'department_id_'.$schedule_count, 'options' => $departments_for_change, 'onchange' => 'updateInvigilatorList(this, \''.$schedule_count.'\')', 'style' => 'width:300px', 'default' => 0)); ?></td>
										</tr>
										<tr>
											<td style="width:20%">Invigilator:</td>
											<td style="width:40%"><?php echo $this->Form->input('invigilator_id_'.$schedule_count, array('id' => 'invigilator_id_'.$schedule_count, 'options' => $invigilators, 'type' => 'select', 'label' => false, 'style' => 'width:300px')); ?></td>
											<td style="width:40%"><?php echo $this->Form->submit(__('Add Invigilator'), array('name' => 'addInvigilator_'.$schedule_count, 'id' => 'AddInvigilator_'.$schedule_count, 'div' => false)); ?></td>
										</tr>
										<tr>
											<th colspan="3"><strong>Cancellation</strong></th>
										</tr>
										<tr>
											<td colspan="3"><?php 
											$cancellation_name = "Are you sure you want to cancel ";
											$cancellation_name .= $examSchedule['PublishedCourse']['Course']['course_title'];
											$cancellation_name .= ' ('.$examSchedule['PublishedCourse']['Course']['course_code'].')';
											$cancellation_name .= " published course exam for ".$examSchedule['PublishedCourse']['Section']['name'];
											if(isset($examSchedule['ExamSplitSection']['section_name'])) {
												$cancellation_name .= ' ('.$examSchedule['ExamSplitSection']['section_name'].')';
											}
											$cancellation_name .= "  section?";
											echo $this->Form->submit(__('Cancel Exam Schedule'), array('onclick' => 'return confirm(\''.$cancellation_name.'\')', 'name' => 'cancelExamSchedule_'.$schedule_count, 'id' => 'CancelExamSchedule_'.$schedule_count, 'div' => false)); ?></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<?php
				$schedule_count++;
				}
				?>
				<tr>
					<td colspan="7">&nbsp;</td>
				</tr>
				<?php
			}
		}
		?>
		</table>
		<?php
	}
	//Organize by department
	else if(isset($examSchedules) && $this->request->data['ExamSchedule']['organize_by_department'] == 1) {
		?>
		<table class="fs13">
		<?php
		foreach($examSchedules as $department_name => $examSchedulesByYearLevel) {
			?>
			<tr>
				<td colspan="7" class="fs14">
				Academic Year: <strong><?php echo $academic_year; ?></strong>,
				Semester: <strong><?php echo $semester; ?></strong>,
				Program: <strong><?php echo $program_name; ?></strong>,
				Program Type: <strong>
				<?php
					foreach($program_types_name as $k => $program_type_name) {
						if(count($program_types_name) > 1 && $k > 0) {
							if((count($program_types_name)-1) == $k)
								echo ' &amp; ';
							else if(count($program_types_name) > 1) {
								echo ', ';
							}
						}
						echo $program_type_name;
					}
				?>
				</strong><br />
				Department: <strong><?php echo $department_name; ?></strong>,
				Year Level: <strong>
				<?php
					foreach($year_levels as $k => $year_level) {
						if(count($year_levels) > 1 && $k > 0) {
							if((count($year_levels)-1) == $k)
								echo ' &amp; ';
							else if(count($year_levels) > 1) {
								echo ', ';
							}
						}
						echo $year_level.($year_level == 1 ? 'st' : ($year_level == 2 ? 'nd' : ($year_level == 3 ? 'rd' : 'th')));
					}
				?>
				</strong>
				</td>
			</tr>
			<tr>
				<th style="width:4%">&nbsp;</th>
				<th style="width:8%">Exam Date</th>
				<th style="width:8%">Session</th>
				<th style="width:17%">Section</th>
				<th style="width:10%">Course Code</th>
				<th style="width:38%">Course Title</th>
				<th style="width:15%">Exam Hall</th>
			</tr>
			<?php
			$count = 0;
			foreach($examSchedulesByYearLevel as $examSchedule) {
			$count++;
			?>
			<tr>
				<td onclick="toggleView(this)" id="<?php echo $count; ?>"><?php echo $this->Html->image('plus2.gif', array('id' => 'i'.$count)); ?></td>
				<td><?php echo $this->Format->humanize_date_short($examSchedule['ExamSchedule']['exam_date']); ?></td>
				<td><?php echo ($examSchedule['ExamSchedule']['session'] == 1 ? 'Morning' : ($examSchedule['ExamSchedule']['session'] == 2 ? 'Afternoon' : 'Evening')); ?></td>
				<td><?php
					echo $examSchedule['PublishedCourse']['Section']['name'];
					if(isset($examSchedule['ExamSplitSection']['section_name'])) {
						echo ' ('.$examSchedule['ExamSplitSection']['section_name'].')';
					}
				?></td>
				<td><?php echo $examSchedule['PublishedCourse']['Course']['course_code']; ?></td>
				<td><?php echo $examSchedule['PublishedCourse']['Course']['course_title']; ?></td>
				<td><?php echo $examSchedule['ClassRoom']['room_code'].' ('.$examSchedule['ClassRoom']['ClassRoomBlock']['block_code'].')'; ?></td>
			</tr>
			<tr id="c<?php echo $count; ?>" style="display:none">
				<td colspan="7">
				Invigilators
				<ol>
				<?php
					$number_of_invigilator = (isset($examSchedule['ClassRoom']['ExamRoomNumberOfInvigilator'][0]['number_of_invigilator']) ? $examSchedule['ClassRoom']['ExamRoomNumberOfInvigilator'][0]['number_of_invigilator'] : 2);
					for($i = 0; $i < $number_of_invigilator; $i++) {
						if(isset($examSchedule['Invigilator'][$i]))
							echo '<li>'.$examSchedule['Invigilator'][$i]['Staff']['full_name'].'</li>';
						else
							echo 'TBA';
					}
				?>
				</ol>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td colspan="7">&nbsp;</td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	//Organize by year level
	else if(isset($examSchedules) && $this->request->data['ExamSchedule']['organize_by_year_level'] == 1) {
		?>
		<table class="fs13">
		<?php
		foreach($examSchedules as $year_level => $subExamSchedules) {
			?>
			<tr>
				<td colspan="7" class="fs14">
				Academic Year: <strong><?php echo $academic_year; ?></strong>,
				Semester: <strong><?php echo $semester; ?></strong>,
				Program: <strong><?php echo $program_name; ?></strong>,
				Program Type: <strong>
				<?php
					foreach($program_types_name as $k => $program_type_name) {
						if(count($program_types_name) > 1 && $k > 0) {
							if((count($program_types_name)-1) == $k)
								echo ' &amp; ';
							else if(count($program_types_name) > 1) {
								echo ', ';
							}
						}
						echo $program_type_name;
					}
				?>
				</strong><br />
				Department: <strong>
				<?php
					foreach($department_names as $k => $department_name) {
						if(count($department_names) > 1 && $k > 0) {
							if((count($department_names)-1) == $k)
								echo ' &amp; ';
							else if(count($department_names) > 1) {
								echo ', ';
							}
						}
						echo $department_name;
					}
				?>
				</strong>,
				Year Level: <strong><?php echo $year_level.($year_level == 1 ? 'st' : ($year_level == 2 ? 'nd' : ($year_level == 3 ? 'rd' : 'th'))); ?></strong>
				</td>
			</tr>
			<tr>
				<th style="width:4%">&nbsp;</th>
				<th style="width:8%">Exam Date</th>
				<th style="width:8%">Session</th>
				<th style="width:17%">Section</th>
				<th style="width:10%">Course Code</th>
				<th style="width:38%">Course Title</th>
				<th style="width:15%">Exam Hall</th>
			</tr>
			<?php
			$count = 0;
			foreach($subExamSchedules as $examSchedule) {
			$count++;
			?>
			<tr>
				<td onclick="toggleView(this)" id="<?php echo $count; ?>"><?php echo $this->Html->image('plus2.gif', array('id' => 'i'.$count)); ?></td>
				<td><?php echo $this->Format->humanize_date_short($examSchedule['ExamSchedule']['exam_date']); ?></td>
				<td><?php echo ($examSchedule['ExamSchedule']['session'] == 1 ? 'Morning' : ($examSchedule['ExamSchedule']['session'] == 2 ? 'Afternoon' : 'Evening')); ?></td>
				<td><?php
					echo $examSchedule['PublishedCourse']['Section']['name'];
					if(isset($examSchedule['ExamSplitSection']['section_name'])) {
						echo ' ('.$examSchedule['ExamSplitSection']['section_name'].')';
					}
				?></td>
				<td><?php echo $examSchedule['PublishedCourse']['Course']['course_code']; ?></td>
				<td><?php echo $examSchedule['PublishedCourse']['Course']['course_title']; ?></td>
				<td><?php echo $examSchedule['ClassRoom']['room_code'].' ('.$examSchedule['ClassRoom']['ClassRoomBlock']['block_code'].')'; ?></td>
			</tr>
			<tr id="c<?php echo $count; ?>" style="display:none">
				<td colspan="7">
				Invigilators
				<ol>
				<?php
					$number_of_invigilator = (isset($examSchedule['ClassRoom']['ExamRoomNumberOfInvigilator'][0]['number_of_invigilator']) ? $examSchedule['ClassRoom']['ExamRoomNumberOfInvigilator'][0]['number_of_invigilator'] : 2);
					for($i = 0; $i < $number_of_invigilator; $i++) {
						if(isset($examSchedule['Invigilator'][$i]))
							echo '<li>'.$examSchedule['Invigilator'][$i]['Staff']['full_name'].'</li>';
						else
							echo 'TBA';
					}
				?>
				</ol>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td colspan="7">&nbsp;</td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	//No organization
	else if(isset($examSchedules)) {
		?>
		<table class="fs13">
		<tr>
			<td colspan="7" class="fs13">
			Academic Year: <strong><?php echo $academic_year; ?></strong>,
			Semester: <strong><?php echo $semester; ?></strong>,
			Program: <strong><?php echo $program_name; ?></strong>,
			Program Type: <strong>
			<?php
				foreach($program_types_name as $k => $program_type_name) {
					if(count($program_types_name) > 1 && $k > 0) {
						if((count($program_types_name)-1) == $k)
							echo ' &amp; ';
						else if(count($program_types_name) > 1) {
							echo ', ';
						}
					}
					echo $program_type_name;
				}
			?>
			</strong><br />
			Department: <strong>
			<?php
				foreach($department_names as $k => $department_name) {
					if(count($department_names) > 1 && $k > 0) {
						if((count($department_names)-1) == $k)
							echo ' &amp; ';
						else if(count($department_names) > 1) {
							echo ', ';
						}
					}
					echo $department_name;
				}
			?>
			</strong>,
			Year Level: <strong>
			<?php
				foreach($year_levels as $k => $year_level) {
					if(count($year_levels) > 1 && $k > 0) {
						if((count($year_levels)-1) == $k)
							echo ' &amp; ';
						else if(count($year_levels) > 1) {
							echo ', ';
						}
					}
					echo $year_level.($year_level == 1 ? 'st' : ($year_level == 2 ? 'nd' : ($year_level == 3 ? 'rd' : 'th')));
				}
			?>
			</strong>
			</td>
		</tr>
		<tr>
			<th style="width:4%">&nbsp;</th>
			<th style="width:8%">Exam Date</th>
			<th style="width:8%">Session</th>
			<th style="width:17%">Section</th>
			<th style="width:10%">Course Code</th>
			<th style="width:38%">Course Title</th>
			<th style="width:15%">Exam Hall</th>
		</tr>
		<?php
		$count = 0;
		foreach($examSchedules as $examSchedule) {
		$count++;
		?>
		<tr>
			<td onclick="toggleView(this)" id="<?php echo $count; ?>"><?php echo $this->Html->image('plus2.gif', array('id' => 'i'.$count)); ?></td>
			<td><?php echo $this->Format->humanize_date_short($examSchedule['ExamSchedule']['exam_date']); ?></td>
			<td><?php echo ($examSchedule['ExamSchedule']['session'] == 1 ? 'Morning' : ($examSchedule['ExamSchedule']['session'] == 2 ? 'Afternoon' : 'Evening')); ?></td>
			<td><?php echo $examSchedule['PublishedCourse']['Section']['name']; ?></td>
			<td><?php echo $examSchedule['PublishedCourse']['Course']['course_code']; ?></td>
			<td><?php echo $examSchedule['PublishedCourse']['Course']['course_title']; ?></td>
			<td><?php echo $examSchedule['ClassRoom']['room_code'].' ('.$examSchedule['ClassRoom']['ClassRoomBlock']['block_code'].')'; ?></td>
		</tr>
		<tr id="c<?php echo $count; ?>" style="display:none">
			<td colspan="7">
			Invigilators
			<ol>
			<?php
				$number_of_invigilator = (isset($examSchedule['ClassRoom']['ExamRoomNumberOfInvigilator'][0]['number_of_invigilator']) ? $examSchedule['ClassRoom']['ExamRoomNumberOfInvigilator'][0]['number_of_invigilator'] : 2);
				for($i = 0; $i < $number_of_invigilator; $i++) {
					if(isset($examSchedule['Invigilator'][$i]))
						echo '<li>'.$examSchedule['Invigilator'][$i]['Staff']['full_name'].'</li>';
					else
						echo 'TBA';
				}
			?>
			</ol>
			</td>
		</tr>
		<?php
		}
		?>
		</table>
		<?php
	}
	echo $this->Form->input('exam_schedule_count', array('type' => 'hidden', 'value' => $schedule_count-1)); ?>
	<?php echo $this->Form->end(); ?>
</div>
<div id="x"></div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
