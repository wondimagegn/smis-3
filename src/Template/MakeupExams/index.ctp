<script>
function toggleView(obj) {
	if ($('#c'+obj.id).css("display") == 'none') {
		$('#i'+obj.id).attr("src", '/img/minus2.gif');
	} else {
		$('#i'+obj.id).attr("src", '/img/plus2.gif');
	}
	$('#c'+obj.id).toggle("slow");
}
</script>
<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Makeup Exam Assignment and Suplementary Exam Result View'); ?></span>
		</div>
	</div>
    <div class="box-body">
    	<div class="row">
	  		<div class="large-12 columns">

				<div class="makeupExams index" style="margin-top: -30px;">
					<hr>

					<?= $this->Form->create('MakeupExam'); ?>
					<fieldset style="padding-bottom: 5px;padding-top: 15px;">
                        <!-- <legend>&nbsp;&nbsp; Search &nbsp;&nbsp;</legend> -->
                        <div class="row">
                            <div class="large-3 columns">
								<?= $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => 'Acadamic Year: ', 'class' => 'fs14', 'type' => 'select', 'options' => $acyear_array_data, 'default' => $defaultacademicyear, 'style' => 'width:90%;')); ?>
                            </div>
                            <div class="large-3 columns">
								<?= $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'label' => 'Semester: ', 'options' => Configure::read('semesters'), 'style' => 'width:90%;')); ?>
                            </div>
                            <div class="large-3 columns">	
								<?= $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => 'Program: ', 'type' => 'select', 'options' => $programs, 'style' => 'width:90%;')); ?>
                            </div>
							<div class="large-3 columns">	
							<?= $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => 'Program Type: ', 'type' => 'select',  'empty' => '[ Any Program Type ]', 'options' => $program_types, 'style' => 'width:90%;')); ?>
                            </div>
                        </div>
						<hr>
						<?= $this->Form->submit(__('View Makeup Exams'), array('div' => false, 'class' => 'tiny radius button bg-blue')); ?>
					</fieldset>
					<?= $this->Form->end(); ?>
					
					<?php
					if (isset($this->request->data)) {
						if(!empty($makeup_exams) && count($makeup_exams) > 0) { ?>
							<hr>
							<br>
							<div style="overflow-x:auto;">
								<table cellpadding="0" cellspacing="0" class="table">
									<thead>
										<tr>
											<th class="center" style="width:4%"></th>
											<th class="center" style="width:3%">#</th>
											<th class="vcenter" style="width:20%">Student Name</th>
											<th class="center" style="width:5%">Sex</th>
											<th class="center" style="width:10%">Student ID</th>
											<th class="center" style="width:10%">Minute No</th>
											<th class="center" style="width:30%">Makeup Exam Taken for</th>
											<th class="center" style="width:10%">Grade</th>
											<th class="center" style="width:8%">Action</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$count = 1;
										foreach($makeup_exams as $key => $makeup_exam) { ?>
											<tr>
												<td class="center" onclick="toggleView(this)" id="<?= $count; ?>"><?= $this->Html->image('plus2.gif', array('id' => 'i'.$count)); ?></td>
												<td class="center"><?= $count; ?></td>
												<td class="vcenter"><?= $makeup_exam['student_name']; ?></td>
												<td class="center"><?= (strcasecmp(trim($makeup_exam['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($makeup_exam['gender']), 'female') == 0 ? 'F' : '')); ?></td>
												<td class="center"><?= $makeup_exam['student_id']; ?></td>
												<td class="center"><?= (isset($makeup_exam['ExamGradeChange']) ? $makeup_exam['ExamGradeChange']['minute_number'] : $makeup_exam['minute_number']); ?></td>
												<td class="center"><?= $makeup_exam['exam_for']; ?></td>
												<td class="center"><?= (isset($makeup_exam['ExamGradeChange']) ? '<span class="' . $makeup_exam['ExamGradeChange']['state'] . '">' . $makeup_exam['ExamGradeChange']['grade'] . '</span>' : '<span class="on-process">Not Submited</span>'); ?></td>
												<!--<td><?php //echo ($makeup_exam['taken_exam'] ? $makeup_exam['taken_exam'] : '---'); ?></td>-->
												<td class="center">
													<?php
													if (isset($makeup_exam['ExamGradeChange'])) {
														if ($makeup_exam['ExamGradeChange']['initiated_by_department'] == 1 && $makeup_exam['ExamGradeChange']['registrar_approval'] == null) {
															echo $this->Html->link(__('Delete'), array('controller' => 'exam_grade_changes', 'action' => 'delete', $makeup_exam['ExamGradeChange']['id']), null, sprintf(__('Are you sure you want to delete %s \'s makeup exam result?'), $makeup_exam['student_name']));
														} else {
															echo '---';
														}
													} else if (empty($makeup_exam['ExamGrade']) && empty($makeup_exam['ExamResult'])) {
														//needs modification after modifying instructor makeup exam submision
														echo $this->Html->link(__('Delete'), array('action' => 'delete', $makeup_exam['id']), null, sprintf(__('Are you sure you want to delete %s \'s makeup exam?'), $makeup_exam['student_name']));
													} else {
														echo '---';
													} ?>
												</td>
											</tr>
											<tr id="c<?= $count; ?>" style="display:none">
												<td style="background-color: white;">&nbsp;</td>
												<td style="background-color: white;">&nbsp;</td>
												<td colspan="7" style="background-color: white;">
													<table cellpadding="0" cellspacing="0" class="table">
														<tr>
															<td class="vcenter" style="width:20%; font-weight:bold;background-color: white;">Section Where Exam Taken:</td>
															<td class="vcenter" style="width:80%; background-color: white;"><?= ((isset($makeup_exam['section_exam_taken']) && !empty($makeup_exam['section_exam_taken']))? $makeup_exam['section_exam_taken'] : '---'); ?></td>
														</tr>
														<tr>
															<td class="vcenter" style="font-weight:bold;">Section Attached Curricullum:</td>
															<td class="vcenter"><?= ((isset($makeup_exam['section_curriculum']) && !empty($makeup_exam['section_curriculum']))? $makeup_exam['section_curriculum'] : '---'); ?></td>
														</tr>
														<tr>
															<td class="vcenter" style="font-weight:bold;background-color: white;">Taken Exam:</td>
															<td class="vcenter" style="background-color: white;"><?= ((isset($makeup_exam['taken_exam']) && !empty($makeup_exam['taken_exam']))? $makeup_exam['taken_exam'] : '---'); ?></td>
														</tr>
														<tr>
															<td class="vcenter" style="font-weight:bold;">Student Attached Curriculum:</td>
															<td class="vcenter"><?= ((isset($makeup_exam['student_attached_curriculum']) && !empty($makeup_exam['student_attached_curriculum']))? $makeup_exam['student_attached_curriculum'] : '---'); ?></td>
														</tr>
														<tr>
															<td class="vcenter" style="font-weight:bold; background-color: white;">Date the Student Assigned:</td>
															<td class="vcenter" style="background-color: white;"><?= (isset($makeup_exam['created']) ? $this->Time->format("M j, Y h:i:s A", $makeup_exam['created'], NULL, NULL) : '---'); ?></td>
														</tr>
														<tr>
															<td class="vcenter" style="font-weight:bold">Date Grade Submitted:</td>
															<td class="vcenter"><?= (isset($makeup_exam['ExamGradeChange']['created']) ? $this->Time->format("M j, Y h:i:s A", $makeup_exam['ExamGradeChange']['created'], NULL, NULL) : '---'); ?></td>
														</tr>
														<tr>
															<td class="vcenter" style="font-weight:bold;background-color: white;">Grade Status:</td>
															<td class="vcenter" style="background-color: white;" <?php if(isset($makeup_exam['ExamGradeChange']['state'])) echo 'class="'.$makeup_exam['ExamGradeChange']['state'].' vcenter"'; ?>><?= (isset($makeup_exam['ExamGradeChange']['state']) ? $makeup_exam['ExamGradeChange']['description'] : '---'); ?></td>
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
							<?php
						} /* else {
							//echo '<p class="fs14">There is no makeup exam taken for the selected criteria.</p>';
						} */
					} ?>
				</div>            
	  		</div>
		</div>
    </div>
</div>

