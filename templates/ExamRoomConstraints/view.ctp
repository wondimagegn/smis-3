<div class="examRoomConstraints view">
<h2><?php echo __('Exam Room Constraint');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examRoomConstraint['ExamRoomConstraint']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Class Room'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($examRoomConstraint['ClassRoom']['id'], array('controller' => 'class_rooms', 'action' => 'view', $examRoomConstraint['ClassRoom']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Academic Year'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examRoomConstraint['ExamRoomConstraint']['academic_year']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Semester'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examRoomConstraint['ExamRoomConstraint']['semester']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Exam Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examRoomConstraint['ExamRoomConstraint']['exam_date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Session'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examRoomConstraint['ExamRoomConstraint']['session']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Active'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examRoomConstraint['ExamRoomConstraint']['active']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Exam Room Constraint'), array('action' => 'edit', $examRoomConstraint['ExamRoomConstraint']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Exam Room Constraint'), array('action' => 'delete', $examRoomConstraint['ExamRoomConstraint']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $examRoomConstraint['ExamRoomConstraint']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Exam Room Constraints'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Exam Room Constraint'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Class Rooms'), array('controller' => 'class_rooms', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Class Room'), array('controller' => 'class_rooms', 'action' => 'add')); ?> </li>
	</ul>
</div>
