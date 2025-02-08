<div class="specializations view">
<h2><?php echo __('Specialization'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($specialization['Specialization']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Department'); ?></dt>
		<dd>
			<?php echo $this->Html->link($specialization['Department']['name'], array('controller' => 'departments', 'action' => 'view', $specialization['Department']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($specialization['Specialization']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Active'); ?></dt>
		<dd>
			<?php echo h($specialization['Specialization']['active']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($specialization['Specialization']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($specialization['Specialization']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Specialization'), array('action' => 'edit', $specialization['Specialization']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Specialization'), array('action' => 'delete', $specialization['Specialization']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $specialization['Specialization']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Specializations'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Specialization'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Departments'), array('controller' => 'departments', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Department'), array('controller' => 'departments', 'action' => 'add')); ?> </li>
	</ul>
</div>
