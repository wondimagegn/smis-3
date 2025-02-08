<div class="withdrawals form">
<?php echo $this->Form->create('Withdrawal');?>
	<fieldset>
		<legend><?php echo __('Edit Withdrawal'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('reason');
		echo $this->Form->input('acceptance_date');
		echo $this->Form->input('forced_withdrawal');
		echo $this->Form->input('minute_number');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('Withdrawal.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('Withdrawal.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Withdrawals'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
	</ul>
</div>