<div class="journals view">
<h2><?php echo __('Journal');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $journal['Journal']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Title'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $journal['Journal']['title']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $journal['Journal']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $journal['Journal']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Journal'), array('action' => 'edit', $journal['Journal']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Journal'), array('action' => 'delete', $journal['Journal']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $journal['Journal']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Journals'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Journal'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Courses'), array('controller' => 'courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Course'), array('controller' => 'courses', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Courses');?></h3>
	<?php if (!empty($journal['Course'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Course Title'); ?></th>
		<th><?php echo __('Course Code'); ?></th>
		<th><?php echo __('Ectc Credit'); ?></th>
		<th><?php echo __('Credit Hour'); ?></th>
		<th><?php echo __('Lecture Hours'); ?></th>
		<th><?php echo __('Tutorial Hours'); ?></th>
		<th><?php echo __('Course Status'); ?></th>
		<th><?php echo __('Course Description'); ?></th>
		<th><?php echo __('Course Objective'); ?></th>
		<th><?php echo __('Curriculum Id'); ?></th>
		<th><?php echo __('Laboratory Hours'); ?></th>
		<th><?php echo __('Department Id'); ?></th>
		<th><?php echo __('Lecture Attendance Requirement'); ?></th>
		<th><?php echo __('Lab Attendance Requirement'); ?></th>
		<th><?php echo __('Grade Type Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($journal['Course'] as $course):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $course['id'];?></td>
			<td><?php echo $course['course_title'];?></td>
			<td><?php echo $course['course_code'];?></td>
			<td><?php echo $course['ectc_credit'];?></td>
			<td><?php echo $course['credit_hour'];?></td>
			<td><?php echo $course['lecture_hours'];?></td>
			<td><?php echo $course['tutorial_hours'];?></td>
			<td><?php echo $course['course_status'];?></td>
			<td><?php echo $course['course_description'];?></td>
			<td><?php echo $course['course_objective'];?></td>
			<td><?php echo $course['curriculum_id'];?></td>
			<td><?php echo $course['laboratory_hours'];?></td>
			<td><?php echo $course['department_id'];?></td>
			<td><?php echo $course['lecture_attendance_requirement'];?></td>
			<td><?php echo $course['lab_attendance_requirement'];?></td>
			<td><?php echo $course['grade_type_id'];?></td>
			<td><?php echo $course['created'];?></td>
			<td><?php echo $course['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'courses', 'action' => 'view', $course['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'courses', 'action' => 'edit', $course['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'courses', 'action' => 'delete', $course['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $course['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Course'), array('controller' => 'courses', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
