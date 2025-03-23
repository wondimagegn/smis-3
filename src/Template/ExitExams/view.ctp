<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= 'Exit Exam Result details: ' . (isset($student_section_exam_status['StudentBasicInfo']['full_name']) ? $student_section_exam_status['StudentBasicInfo']['full_name'] . ' (' . $student_section_exam_status['StudentBasicInfo']['studentnumber'] . ')' : ''); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;">
					<hr>
					<?php
					if (isset($exitExams)) {
						$yFrom = date('Y') - 1;
						$yTo = date('Y'); ?>

						<?= $this->Form->create('ExitExam'); ?>

						<div class="large-8 columns">
							<?= $this->element('student_basic'); ?>
						</div>

						<div class="large-4 columns">
							<table cellpadding="0" cellspacing="0" class="table">
								<tr>
									<td>
										<?= (isset($$exitExams['ExitExam']['id']) && is_numeric($exitExams['ExitExam']['id']) && $exitExams['ExitExam']['id'] > 0 ? $this->Form->hidden('ExitExam.id', array('value' => $exitExams['ExitExam']['id'])) : (isset($this->request->data['ExitExam']['id']) && !empty($this->request->data['ExitExam']['id']) && $this->request->data['ExitExam']['id'] > 0 ? $this->Form->hidden('ExitExam.id', array('value' => $this->request->data['ExitExam']['id'])) : '')); ?>
                                    	<?= (isset($exitExams['ExitExam']['student_id']) && !empty($exitExams['ExitExam']['student_id']) && $exitExams['ExitExam']['student_id'] > 0 ? $this->Form->hidden('ExitExam.student_id',  array('value' => $exitExams['ExitExam']['student_id'])) : (isset($student_section_exam_status['StudentBasicInfo']['id']) && !empty($student_section_exam_status['StudentBasicInfo']['id']) && $student_section_exam_status['StudentBasicInfo']['id'] > 0 ? $this->Form->hidden('ExitExam.student_id',  array('value' => $student_section_exam_status['StudentBasicInfo']['id'])) : '')); ?>
										<div class="large-12 columns">
											<?= $this->Form->input('course_id', array('style' => 'width:95%;', 'label' => 'Course: ', 'options' => $courses, 'default' => $exitExams['ExitExam']['course_id'], 'disabled')); ?>
										</div>
										<div class="large-12 columns">
											<?= $this->Form->input('type', array('style' => 'width:95%;', 'label' => 'Exam Type: ', 'options' => $exit_exam_types, 'type' => 'select', 'default' => $exitExams['ExitExam']['type'], 'disabled')); ?>
										</div>
										<div class="large-12 columns">
											<?= $this->Form->input('result', array('style' => 'width:30%;', 'label' => 'Exam Result: ', 'max' => 100, 'min' => 0, 'step' => 'any', 'value' => $exitExams['ExitExam']['result'], 'disabled')); ?>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="large-12 columns">
											Exam Date: &nbsp; <?= (!empty($exitExams['ExitExam']['exam_date']) ? $this->Time->format("M j, Y", $exitExams['ExitExam']['exam_date'], NULL, NULL) : 'N/A');  ?>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="large-12 columns">
											Created: &nbsp; <?= (!empty($exitExams['ExitExam']['created']) ? $this->Time->format("M j, Y h:i:s A", $exitExams['ExitExam']['created'], NULL, NULL) : 'N/A');  ?>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="large-12 columns">
											Modified: &nbsp; <?= (!empty($exitExams['ExitExam']['modified']) ? $this->Time->format("M j, Y h:i:s A", $exitExams['ExitExam']['modified'], NULL, NULL) : 'N/A');  ?>
										</div>
									</td>
								</tr>
							</table>
						</div>
						<?= $this->Form->end(); ?>
						<?php
					} ?>
				</div>
			</div>
		</div>
	</div>
</div>