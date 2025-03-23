<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="classRooms index">
	<h2><?php echo __('Class Rooms');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('class_room_block_id');?></th>
			<th><?php echo $this->Paginator->sort('room_code');?></th>
			<th><?php echo $this->Paginator->sort('available_for_lecture');?></th>
			<th><?php echo $this->Paginator->sort('available_for_exam');?></th>
			<th><?php echo $this->Paginator->sort('lecture_capacity');?></th>
			<th><?php echo $this->Paginator->sort('exam_capacity');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($classRooms as $classRoom):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $classRoom['ClassRoom']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($classRoom['ClassRoomBlock']['id'], array('controller' => 'class_room_blocks', 'action' => 'view', $classRoom['ClassRoomBlock']['id'])); ?>
		</td>
		<td><?php echo $classRoom['ClassRoom']['room_code']; ?>&nbsp;</td>
		<td><?php echo $classRoom['ClassRoom']['available_for_lecture']; ?>&nbsp;</td>
		<td><?php echo $classRoom['ClassRoom']['available_for_exam']; ?>&nbsp;</td>
		<td><?php echo $classRoom['ClassRoom']['lecture_capacity']; ?>&nbsp;</td>
		<td><?php echo $classRoom['ClassRoom']['exam_capacity']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $classRoom['ClassRoom']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $classRoom['ClassRoom']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $classRoom['ClassRoom']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $classRoom['ClassRoom']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Class Room'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Class Room Blocks'), array('controller' => 'class_room_blocks', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Class Room Block'), array('controller' => 'class_room_blocks', 'action' => 'add')); ?> </li>
	</ul>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
