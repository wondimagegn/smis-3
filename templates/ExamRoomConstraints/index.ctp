<div class="examRoomConstraints index">
	<h2><?php echo __('Exam Room Constraints');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('class_room_id');?></th>
			<th><?php echo $this->Paginator->sort('academic_year');?></th>
			<th><?php echo $this->Paginator->sort('semester');?></th>
			<th><?php echo $this->Paginator->sort('exam_date');?></th>
			<th><?php echo $this->Paginator->sort('session');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($examRoomConstraints as $examRoomConstraint):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $examRoomConstraint['ExamRoomConstraint']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($examRoomConstraint['ClassRoom']['id'], array('controller' => 'class_rooms', 'action' => 'view', $examRoomConstraint['ClassRoom']['id'])); ?>
		</td>
		<td><?php echo $examRoomConstraint['ExamRoomConstraint']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $examRoomConstraint['ExamRoomConstraint']['semester']; ?>&nbsp;</td>
		<td><?php echo $examRoomConstraint['ExamRoomConstraint']['exam_date']; ?>&nbsp;</td>
		<td><?php echo $examRoomConstraint['ExamRoomConstraint']['session']; ?>&nbsp;</td>
		<td><?php echo $examRoomConstraint['ExamRoomConstraint']['active']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $examRoomConstraint['ExamRoomConstraint']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $examRoomConstraint['ExamRoomConstraint']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $examRoomConstraint['ExamRoomConstraint']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $examRoomConstraint['ExamRoomConstraint']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Exam Room Constraint'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Class Rooms'), array('controller' => 'class_rooms', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Class Room'), array('controller' => 'class_rooms', 'action' => 'add')); ?> </li>
	</ul>
</div>