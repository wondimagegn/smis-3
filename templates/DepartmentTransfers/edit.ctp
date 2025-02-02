<div class="departmentTransfers form">
<?php echo $this->Form->create('DepartmentTransfer');?>
	<fieldset>
		<legend><?php echo __('Edit Department Transfer'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('department_id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('minute_number');
		echo $this->Form->input('transfer_request_date');
		echo $this->Form->input('receiver_department_approval');
		echo $this->Form->input('receiver_department_approval_date');
		echo $this->Form->input('receiver_department_remark');
		echo $this->Form->input('sender_college_approval');
		echo $this->Form->input('sender_college_approval_date');
		echo $this->Form->input('sender_college_remark');
		echo $this->Form->input('receiver_college_approval');
		echo $this->Form->input('receiver_college_approval_date');
		echo $this->Form->input('receiver_college_remark');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
