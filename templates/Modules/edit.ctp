<div class="modules form">
<?php echo $this->Form->create('Module');?>
	<fieldset>
		<legend><?php echo __('Edit Module'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('parent_id');
		echo $this->Form->input('name');
		echo $this->Form->input('url');
		echo $this->Form->input('order');
		echo $this->Form->input('status');
		echo $this->Form->input('is_menu');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('Module.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('Module.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Modules'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Role Module Maps'), array('controller' => 'role_module_maps', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Role Module Map'), array('controller' => 'role_module_maps', 'action' => 'add')); ?> </li>
	</ul>
</div>