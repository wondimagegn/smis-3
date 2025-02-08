<div class="exceptionMealAssignments view">
<h2><?php echo __('Exception Meal Assignment');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Student'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($exceptionMealAssignment['Student']['id'], array('controller' => 'students', 'action' => 'view', $exceptionMealAssignment['Student']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Meal Hall'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($exceptionMealAssignment['MealHall']['name'], array('controller' => 'meal_halls', 'action' => 'view', $exceptionMealAssignment['MealHall']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Accept Deny'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $exceptionMealAssignment['ExceptionMealAssignment']['accept_deny']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Start Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $exceptionMealAssignment['ExceptionMealAssignment']['start_date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('End Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $exceptionMealAssignment['ExceptionMealAssignment']['end_date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $exceptionMealAssignment['ExceptionMealAssignment']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $exceptionMealAssignment['ExceptionMealAssignment']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
