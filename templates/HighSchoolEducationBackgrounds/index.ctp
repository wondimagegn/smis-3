<div class="highSchoolEducationBackgrounds index">
	<h2><?php echo __('High School Education Backgrounds');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('town');?></th>
			<th><?php echo $this->Paginator->sort('zone');?></th>
			<th><?php echo $this->Paginator->sort('region');?></th>
			<th><?php echo $this->Paginator->sort('school_level');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($highSchoolEducationBackgrounds as $highSchoolEducationBackground):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $highSchoolEducationBackground['HighSchoolEducationBackground']['id']; ?>&nbsp;</td>
		<td><?php echo $highSchoolEducationBackground['HighSchoolEducationBackground']['name']; ?>&nbsp;</td>
		<td><?php echo $highSchoolEducationBackground['HighSchoolEducationBackground']['town']; ?>&nbsp;</td>
		<td><?php echo $highSchoolEducationBackground['HighSchoolEducationBackground']['zone']; ?>&nbsp;</td>
		<td><?php echo $highSchoolEducationBackground['HighSchoolEducationBackground']['region']; ?>&nbsp;</td>
		<td><?php echo $highSchoolEducationBackground['HighSchoolEducationBackground']['school_level']; ?>&nbsp;</td>
		<td><?php echo $highSchoolEducationBackground['HighSchoolEducationBackground']['created']; ?>&nbsp;</td>
		<td><?php echo $highSchoolEducationBackground['HighSchoolEducationBackground']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $highSchoolEducationBackground['HighSchoolEducationBackground']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $highSchoolEducationBackground['HighSchoolEducationBackground']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $highSchoolEducationBackground['HighSchoolEducationBackground']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $highSchoolEducationBackground['HighSchoolEducationBackground']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New High School Education Background'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
	</ul>
</div>