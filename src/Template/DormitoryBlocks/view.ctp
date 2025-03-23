<div class="dormitoryBlocks view">
<h2><?php echo __('Dormitory Block');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Campus'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($dormitoryBlock['Campus']['name'], array('controller' => 'campuses', 'action' => 'view', $dormitoryBlock['Campus']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Block Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $dormitoryBlock['DormitoryBlock']['block_name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $dormitoryBlock['DormitoryBlock']['type']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Location'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $dormitoryBlock['DormitoryBlock']['location']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Telephone Number'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $dormitoryBlock['DormitoryBlock']['telephone_number']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Alt Telephone Number'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $dormitoryBlock['DormitoryBlock']['alt_telephone_number']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->short_date($dormitoryBlock['DormitoryBlock']['created']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->short_date($dormitoryBlock['DormitoryBlock']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="related">
	<h3><?php echo __('List of Dormitories');?></h3>
	<?php if (!empty($dormitoryBlock['Dormitory'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('S.N<u>o</u>'); ?></th>
		<th><?php echo __('Dorm Name'); ?></th>
		<th><?php echo __('Floor'); ?></th>
		<th><?php echo __('Capacity'); ?></th>
		<th><?php echo __('Available'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		$count = 1;
		foreach ($dormitoryBlock['Dormitory'] as $dormitory):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			$floor = null;
			if($dormitory['floor'] ==1){
				$floor = "Ground Floor";
			} else if($dormitory['floor']==2){
				$floor = ($dormitory['floor']-1)."st Floor";
			} else if($dormitory['floor']==3){
				$floor = ($dormitory['floor']-1)."nd Floor";
			} else if($dormitory['floor']==4){
				$floor = ($dormitory['floor']-1)."rd Floor";
			} else {
				$floor = ($dormitory['floor']-1)."th Floor";
			}
			
			if($dormitory['available']== 1){
				$available = "Yes";
			} else {
			 	$available = "No";
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $count++;?></td>
			<td><?php echo $dormitory['dorm_number'];?></td>
			<td><?php echo $floor;?></td>
			<td><?php echo $dormitory['capacity'];?></td>
			<td><?php echo $available;?></td>
			<td><?php echo $this->Format->short_date($dormitory['created']);?></td>
			<td><?php echo $this->Format->short_date($dormitory['modified']);?></td>
			<td class="actions">
			<!--<?php echo $this->Html->link(__('View'), array('controller' => 'dormitories', 'action' => 'view', $dormitory['id'])); ?> -->
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'dormitories', 'action' => 'edit', $dormitory['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'dormitories', 'action' => 'delete', $dormitory['id']), null, sprintf(__('Are you sure you want to delete dorm %s?'), $dormitory['dorm_number'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Dormitory Blocks'), array('controller' => 'dormitory_blocks', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Dormitory Block'), array('controller' => 'dormitory_blocks', 'action' => 'add')); ?> </li>
	</ul>
</div>
</div>
