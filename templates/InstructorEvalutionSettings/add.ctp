<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Add Instructor Evalution Setting'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				
				<div style="margin-top: -30px;"><hr></div>

				<?= $this->Form->create('InstructorEvalutionSetting'); ?>

				<fieldset style="padding-bottom: 5px;padding-top: 15px;">
                    <legend>&nbsp;&nbsp; Add Evaluation Setting &nbsp;&nbsp;</legend>
					<div class="row">
						<div class="large-3 columns">
							<?= $this->Form->input('academic_year', array('style' =>  'width: 70%;','label' => 'Academic Year: (From)', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($this->request->data['InstructorEvalutionSetting']['academic_year']) ? $this->request->data['InstructorEvalutionSetting']['academic_year'] : $defaultacademicyear))); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('student_percent', array('style' => 'width: 50%;', 'type' => 'number', 'min' => '0', 'max' => '50', 'step' => 'any', 'label' => 'Student: (%)')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('colleague_percent', array('style' => 'width: 50%;', 'type' => 'number', 'min' => '0', 'max' => '50', 'step' => 'any', 'label' => 'Colleague: (%)')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('head_percent', array('style' => 'width: 50%;', 'type' => 'number', 'min' => '0', 'max' => '50', 'step' => 'any', 'label' => 'Head: (%)')); ?>
						</div>
					</div>
					<hr>
					<?= $this->Form->end(array('label' => 'Add Evaluation Setting','class' => 'tiny radius button bg-blue'));?>
				</fieldset>

				<div class="row">
					<div class="large-4 columns">
						<li><?= $this->Html->link(__('Existing Instructor Evalution Setting'), array('action' => 'index')); ?></li>
					</div>
				</div>

			</div>

			
		</div>
	</div>
</div>
