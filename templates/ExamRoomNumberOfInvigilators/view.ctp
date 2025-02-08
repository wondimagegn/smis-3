<div class="examRoomNumberOfInvigilators view">
<h2><?php echo __('Exam Room Number Of Invigilator');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Class Room'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($examRoomNumberOfInvigilator['ClassRoom']['id'], array('controller' => 'class_rooms', 'action' => 'view', $examRoomNumberOfInvigilator['ClassRoom']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Academic Year'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['academic_year']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Semester'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['semester']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Number Of Invigilator'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['number_of_invigilator']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Exam Room Number Of Invigilator'), array('action' => 'edit', $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Exam Room Number Of Invigilator'), array('action' => 'delete', $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $examRoomNumberOfInvigilator['ExamRoomNumberOfInvigilator']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Exam Room Number Of Invigilators'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Exam Room Number Of Invigilator'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Class Rooms'), array('controller' => 'class_rooms', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Class Room'), array('controller' => 'class_rooms', 'action' => 'add')); ?> </li>
	</ul>
</div>
