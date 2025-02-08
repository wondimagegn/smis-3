<div class="grades view">
<h2><?php echo __('Grade');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $grade['Grade']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Grade'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $grade['Grade']['grade']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Grade Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($grade['GradeType']['name'], array('controller' => 'grade_types', 'action' => 'view', $grade['GradeType']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Point Value'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $grade['Grade']['point_value']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Pass Grade'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $grade['Grade']['pass_grade']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Active'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $grade['Grade']['active']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $grade['Grade']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $grade['Grade']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Grade'), array('action' => 'edit', $grade['Grade']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Grade'), array('action' => 'delete', $grade['Grade']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $grade['Grade']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Grades'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Grade'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Grade Types'), array('controller' => 'grade_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Grade Type'), array('controller' => 'grade_types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Grade Scale Details'), array('controller' => 'grade_scale_details', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Grade Scale Detail'), array('controller' => 'grade_scale_details', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Grade Scale Details');?></h3>
	<?php if (!empty($grade['GradeScaleDetail'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Minimum Result'); ?></th>
		<th><?php echo __('Maximum Result'); ?></th>
		<th><?php echo __('Grade Scale Id'); ?></th>
		<th><?php echo __('Grade Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($grade['GradeScaleDetail'] as $gradeScaleDetail):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $gradeScaleDetail['id'];?></td>
			<td><?php echo $gradeScaleDetail['minimum_result'];?></td>
			<td><?php echo $gradeScaleDetail['maximum_result'];?></td>
			<td><?php echo $gradeScaleDetail['grade_scale_id'];?></td>
			<td><?php echo $gradeScaleDetail['grade_id'];?></td>
			<td><?php echo $gradeScaleDetail['created'];?></td>
			<td><?php echo $gradeScaleDetail['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'grade_scale_details', 'action' => 'view', $gradeScaleDetail['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'grade_scale_details', 'action' => 'edit', $gradeScaleDetail['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'grade_scale_details', 'action' => 'delete', $gradeScaleDetail['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $gradeScaleDetail['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
</div>
