<div class="examGrades form">
<?php echo $this->Form->create('ExamGrade');?>
	<fieldset>
		<legend><?php echo __('Add Exam Grade'); ?></legend>
	<?php
		echo $this->Form->input('grade');
		echo $this->Form->input('course_registration_id');
		echo $this->Form->input('makeup_exam_id');
		echo $this->Form->input('department_approval');
		echo $this->Form->input('department_approval_date');
		echo $this->Form->input('department_approved_by');
		echo $this->Form->input('registrar_approval');
		echo $this->Form->input('registrar_approval_date');
		echo $this->Form->input('registrar_approved_by');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
