<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?=  __('Instructor Evalution Setting'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				
				<div style="margin-top: -30px;"><hr></div>
				<?php
				if (!empty($instructorEvalutionSetting)) { ?>

					<fieldset style="padding-bottom: 5px;padding-top: 15px;">
						<legend>&nbsp;&nbsp; Current Setting &nbsp;&nbsp;</legend>
						<div class="row">
							<div class="large-3 columns">
								<?= $this->Form->input('academic_year', array('disabled', 'value' => $instructorEvalutionSetting['InstructorEvalutionSetting']['academic_year'], 'style' =>  'width: 70%;','label' => 'Academic Year: (From)', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($this->request->data['InstructorEvalutionSetting']['academic_year']) ? $this->request->data['InstructorEvalutionSetting']['academic_year'] : $defaultacademicyear))); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('student_percent', array('disabled','value' => $instructorEvalutionSetting['InstructorEvalutionSetting']['student_percent'],'style' => 'width: 50%;', 'type' => 'number', 'min' => '0', 'max' => '50', 'step' => 'any', 'label' => 'Student: (%)')); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('colleague_percent', array('disabled', 'value' => $instructorEvalutionSetting['InstructorEvalutionSetting']['colleague_percent'],  'style' => 'width: 50%;', 'type' => 'number', 'min' => '0', 'max' => '50', 'step' => 'any', 'label' => 'Colleague: (%)')); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('head_percent', array('disabled', 'value' => $instructorEvalutionSetting['InstructorEvalutionSetting']['head_percent'], 'style' => 'width: 50%;', 'type' => 'number', 'min' => '0', 'max' => '50', 'step' => 'any', 'label' => 'Head: (%)')); ?>
							</div>
						</div>
						<hr>
						<?= $this->Html->link("Change Evaluation Setting", array('controller' => 'instructorEvalutionSettings', 'action' => 'edit'), array('class' => 'tiny radius button bg-blue')); ?>
					</fieldset>
					<?php
				} else { ?>
					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No instructor evaluation setting is found.</div>
					<?php
				} ?>

			</div>
		</div>
	</div>
</div>