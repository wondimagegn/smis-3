<div class="votes form">
<?php echo $this->Form->create('Vote');?>
	<fieldset>
		<legend><?php echo __('Edit Vote'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('task');
		echo $this->Form->input('requester_user_id');
		echo $this->Form->input('applicable_on_user_id');
		echo $this->Form->input('data');
		echo $this->Form->input('confirmation');
		echo $this->Form->input('confirmation_date');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('Vote.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('Vote.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Votes'), array('action' => 'index'));?></li>
	</ul>
</div>