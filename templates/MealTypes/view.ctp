<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="mealTypes view">
<h2><?php echo __('Meal Type');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mealType['MealType']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Meal Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mealType['MealType']['meal_name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mealType['MealType']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mealType['MealType']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>

<div class="related">
	<h3><?php echo __('Related Meal Attendances');?></h3>
	<?php if (!empty($mealType['MealAttendance'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Meal Type Id'); ?></th>
		<th><?php echo __('Student Id'); ?></th>
		<th><?php echo __('Accepted Student Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($mealType['MealAttendance'] as $mealAttendance):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $mealAttendance['id'];?></td>
			<td><?php echo $mealAttendance['meal_type_id'];?></td>
			<td><?php echo $mealAttendance['student_id'];?></td>
			<td><?php echo $mealAttendance['accepted_student_id'];?></td>
			<td><?php echo $mealAttendance['created'];?></td>
			<td><?php echo $mealAttendance['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'meal_attendances', 'action' => 'view', $mealAttendance['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'meal_attendances', 'action' => 'edit', $mealAttendance['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'meal_attendances', 'action' => 'delete', $mealAttendance['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $mealAttendance['id'])); ?>
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
