<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="classRooms view">
<h2><?php echo __('Class Room');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $classRoom['ClassRoom']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Class Room Block'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($classRoom['ClassRoomBlock']['id'], array('controller' => 'class_room_blocks', 'action' => 'view', $classRoom['ClassRoomBlock']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Room Code'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $classRoom['ClassRoom']['room_code']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Available For Lecture'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $classRoom['ClassRoom']['available_for_lecture']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Available For Exam'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $classRoom['ClassRoom']['available_for_exam']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Lecture Capacity'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $classRoom['ClassRoom']['lecture_capacity']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Exam Capacity'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $classRoom['ClassRoom']['exam_capacity']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Class Room'), array('action' => 'edit', $classRoom['ClassRoom']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Class Room'), array('action' => 'delete', $classRoom['ClassRoom']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $classRoom['ClassRoom']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Class Rooms'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Class Room'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Class Room Blocks'), array('controller' => 'class_room_blocks', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Class Room Block'), array('controller' => 'class_room_blocks', 'action' => 'add')); ?> </li>
	</ul>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
