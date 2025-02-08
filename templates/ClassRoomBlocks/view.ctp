<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="classRoomBlocks view">
<h2><?php echo __('Class Room Block');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('College'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($classRoomBlock['College']['name'], array('controller' => 'colleges', 'action' => 'view', $classRoomBlock['College']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Campus'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($classRoomBlock['Campus']['name'], array('controller' => 'campuses', 'action' => 'view', $classRoomBlock['Campus']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Block Code'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $classRoomBlock['ClassRoomBlock']['block_code']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="related">
	<h3><?php echo __('Related Class Rooms');?></h3>
	<?php if (!empty($classRoomBlock['ClassRoom'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('S.N<u>o</>'); ?></th>
		<!--<th><?php echo __('Class Room Block Id'); ?></th>-->
		<th><?php echo __('Room Code'); ?></th>
		<th><?php echo __('Available For Lecture'); ?></th>
		<th><?php echo __('Available For Exam'); ?></th>
		<th><?php echo __('Lecture Capacity'); ?></th>
		<th><?php echo __('Exam Capacity'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		$count =1;
		foreach ($classRoomBlock['ClassRoom'] as $classRoom):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			if($classRoom['available_for_lecture']== 1){
				$available_for_lecture = "Yes";
			} else {
			 	$available_for_lecture = "No";
			}
			if($classRoom['available_for_exam']== 1){
				$available_for_exam = "Yes";
			} else {
			 	$available_for_exam = "No";
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $count++;?></td>
			<!--<td><?php echo $classRoom['class_room_block_id'];?></td>-->
			<td><?php echo $classRoom['room_code'];?></td>
			<td><?php echo $available_for_lecture; ?></td>
			<td><?php echo $available_for_exam; ?></td>
			<td><?php echo $classRoom['lecture_capacity'];?></td>
			<td><?php echo $classRoom['exam_capacity'];?></td>
			<td class="actions">
				<!--<?php echo $this->Html->link(__('View'), array('controller' => 'class_rooms', 'action' => 'view', $classRoom['id'])); ?>

				<?php echo $this->Html->link(__('Edit'), array('controller' => 'class_rooms', 'action' => 'edit', $classRoom['id'])); ?> -->
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'class_rooms', 'action' => 'delete', $classRoom['id']), null, sprintf(__('Are you sure you want to delete class room %s?'), $classRoom['room_code'])); ?>
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
