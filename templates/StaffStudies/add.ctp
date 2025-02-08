<div class="staffStudies form">
<?php echo $this->Form->create('StaffStudy'); ?>
	<fieldset>
		<legend><?php echo __('Add Staff Study'); ?></legend>
	<?php
		echo $this->Form->input('staff_id');
		echo $this->Form->input('education');
		echo $this->Form->input('leave_date');
		echo $this->Form->input('return_date');
		echo $this->Form->input('committement_signed');
		echo $this->Form->input('specialization');
		echo $this->Form->input('country_id');
		echo $this->Form->input('university_joined');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Staff Studies'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Staffs'), array('controller' => 'staffs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Staff'), array('controller' => 'staffs', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Countries'), array('controller' => 'countries', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Country'), array('controller' => 'countries', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Attachments'), array('controller' => 'attachments', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Attachment'), array('controller' => 'attachments', 'action' => 'add')); ?> </li>
	</ul>
</div>
