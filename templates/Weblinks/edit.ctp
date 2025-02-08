<div class="weblinks form">
<?php echo $this->Form->create('Weblink');?>
	<fieldset>
		<legend><?php echo __('Edit Weblink'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('title');
		echo $this->Form->input('url_address');
		echo $this->Form->input('Course');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('Weblink.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('Weblink.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Weblinks'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Courses'), array('controller' => 'courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Course'), array('controller' => 'courses', 'action' => 'add')); ?> </li>
	</ul>
</div>