<div class="modules view">
<h2><?php echo __('Module');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $module['Module']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Parent Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $module['Module']['parent_id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $module['Module']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Url'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $module['Module']['url']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Order'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $module['Module']['order']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $module['Module']['status']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Is Menu'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $module['Module']['is_menu']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Module'), array('action' => 'edit', $module['Module']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Module'), array('action' => 'delete', $module['Module']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $module['Module']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Modules'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Module'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Role Module Maps'), array('controller' => 'role_module_maps', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Role Module Map'), array('controller' => 'role_module_maps', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Role Module Maps');?></h3>
	<?php if (!empty($module['RoleModuleMap'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Role Id'); ?></th>
		<th><?php echo __('Module Id'); ?></th>
		<th><?php echo __('Status'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($module['RoleModuleMap'] as $roleModuleMap):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $roleModuleMap['id'];?></td>
			<td><?php echo $roleModuleMap['role_id'];?></td>
			<td><?php echo $roleModuleMap['module_id'];?></td>
			<td><?php echo $roleModuleMap['status'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'role_module_maps', 'action' => 'view', $roleModuleMap['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'role_module_maps', 'action' => 'edit', $roleModuleMap['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'role_module_maps', 'action' => 'delete', $roleModuleMap['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $roleModuleMap['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Role Module Map'), array('controller' => 'role_module_maps', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
