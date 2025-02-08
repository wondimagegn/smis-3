<div class="examRoomCourseConstraints view">
<h2><?php echo __('Exam Room Course Constraint');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examRoomCourseConstraint['ExamRoomCourseConstraint']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Class Room'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($examRoomCourseConstraint['ClassRoom']['id'], array('controller' => 'class_rooms', 'action' => 'view', $examRoomCourseConstraint['ClassRoom']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Published Course'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($examRoomCourseConstraint['PublishedCourse']['id'], array('controller' => 'published_courses', 'action' => 'view', $examRoomCourseConstraint['PublishedCourse']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Active'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examRoomCourseConstraint['ExamRoomCourseConstraint']['active']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
