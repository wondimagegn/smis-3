<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="books view">
<h2><?php echo __('Book');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $book['Book']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Title'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $book['Book']['title']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('ISBN'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $book['Book']['ISBN']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $book['Book']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $book['Book']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Book'), array('action' => 'edit', $book['Book']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Book'), array('action' => 'delete', $book['Book']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $book['Book']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Books'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Book'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Courses'), array('controller' => 'courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Course'), array('controller' => 'courses', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Courses');?></h3>
	<?php if (!empty($book['Course'])):?>
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
		foreach ($book['Course'] as $course):
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
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
