<div class="dormitories view">
<h2><?php echo __('Dormitory');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $dormitory['Dormitory']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Dormitory Block'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($dormitory['DormitoryBlock']['id'], array('controller' => 'dormitory_blocks', 'action' => 'view', $dormitory['DormitoryBlock']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Dorm Number'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $dormitory['Dormitory']['dorm_number']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Floor'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $dormitory['Dormitory']['floor']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Capacity'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $dormitory['Dormitory']['capacity']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Available'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $dormitory['Dormitory']['available']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $dormitory['Dormitory']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $dormitory['Dormitory']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Dormitory'), array('action' => 'edit', $dormitory['Dormitory']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Dormitory'), array('action' => 'delete', $dormitory['Dormitory']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $dormitory['Dormitory']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Dormitories'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Dormitory'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Dormitory Blocks'), array('controller' => 'dormitory_blocks', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Dormitory Block'), array('controller' => 'dormitory_blocks', 'action' => 'add')); ?> </li>
	</ul>
</div>
