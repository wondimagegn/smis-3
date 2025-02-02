<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Apply For Readmission'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div class="readmissions form">
					<?= $this->Form->create('Readmission'); ?>
					<?= $this->Form->hidden('student_id', array('value' => $student_section_exam_status['StudentBasicInfo']['id'])); ?>
					<table>
						<tr>
							<td>
								<table class="fs13 small_padding">
									<tr>
										<td style="width:26%">Academic Year</td>
										<td style="width:74%"><?= $this->Form->input('academic_year', array('label' => 'Academic Year', 'type' => 'select', 'options' => $acyear_array_data, 'empty' => "--Select Academic Year--", 'default' => isset($defaultacademicyear) ? $defaultacademicyear : '', 'style' => 'width:100px')); ?></td>
									</tr>
									<tr>
										<td style="width:26%">Semester</td>
										<td style="width:74%"><?= $this->Form->input('semester', array('options' => Configure::read('semesters'), 'empty' => '[ select semester ]', 'label' => 'Semester: ', 'style' => 'width:100px')); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td><?= $this->element('student_basic'); ?></td>
						</tr>
					</table>
					<?= $this->Form->end(array('label' => __('Submit'), 'class' => 'tiny radius button bg-blue')); ?>
				</div>
			</div>
		</div>
	</div>
</div>