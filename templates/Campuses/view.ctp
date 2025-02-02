<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="campuses view">
<div class="smallheading"><?php echo __('Campus');?></div>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<!-- <dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $campus['Campus']['id']; ?>
			&nbsp;
		</dd> -->
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $campus['Campus']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Description'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $campus['Campus']['description']; ?>
			&nbsp;
		</dd>
	</dl>
</div>

<div class="related">
	<p class="fs15"><?php echo __('Colleges in the Campus');?></p>
	<?php if (!empty($campus['College'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<!--<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Campus Id'); ?></th> -->
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Description'); ?></th>
		<th><?php echo __('Type'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($campus['College'] as $college):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<!--- <td><?php echo $college['id'];?></td>
			<td><?php echo $college['campus_id'];?></td> --->
			<td><?php echo $college['name'];?></td>
			<td><?php echo $college['description'];?></td>
			<td><?php echo $college['type'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'colleges', 'action' => 'view', $college['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'colleges', 'action' => 'edit', $college['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'colleges', 'action' => 'delete', $college['id']), null, sprintf(__('Are you sure you want to delete "%s" college?'), $college['name'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
