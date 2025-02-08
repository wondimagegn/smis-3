<div class="mealAttendances view">
<h2><?php echo __('Meal Attendance');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mealAttendance['MealAttendance']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Meal Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($mealAttendance['MealType']['meal_name'], array('controller' => 'meal_types', 'action' => 'view', $mealAttendance['MealType']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Student'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($mealAttendance['Student']['id'], array('controller' => 'students', 'action' => 'view', $mealAttendance['Student']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Accepted Student'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($mealAttendance['AcceptedStudent']['id'], array('controller' => 'accepted_students', 'action' => 'view', $mealAttendance['AcceptedStudent']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mealAttendance['MealAttendance']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mealAttendance['MealAttendance']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Meal Attendance'), array('action' => 'edit', $mealAttendance['MealAttendance']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Meal Attendance'), array('action' => 'delete', $mealAttendance['MealAttendance']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $mealAttendance['MealAttendance']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Meal Attendances'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Meal Attendance'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Meal Types'), array('controller' => 'meal_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Meal Type'), array('controller' => 'meal_types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Accepted Students'), array('controller' => 'accepted_students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Accepted Student'), array('controller' => 'accepted_students', 'action' => 'add')); ?> </li>
	</ul>
</div>
