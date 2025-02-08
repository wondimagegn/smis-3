<div class="instructorExamExcludeDateConstraints view">
<h2><?php echo __('Instructor Exam Exclude Date Constraint');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Staff'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($instructorExamExcludeDateConstraint['Staff']['id'], array('controller' => 'staffs', 'action' => 'view', $instructorExamExcludeDateConstraint['Staff']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Staff For Exam'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($instructorExamExcludeDateConstraint['StaffForExam']['staff_id'], array('controller' => 'staff_for_exams', 'action' => 'view', $instructorExamExcludeDateConstraint['StaffForExam']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Academic Year'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['academic_year']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Semester'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['semester']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Exam Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['exam_date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Session'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['session']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Instructor Exam Exclude Date Constraint'), array('action' => 'edit', $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Instructor Exam Exclude Date Constraint'), array('action' => 'delete', $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Instructor Exam Exclude Date Constraints'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Instructor Exam Exclude Date Constraint'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Staffs'), array('controller' => 'staffs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Staff'), array('controller' => 'staffs', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Staff For Exams'), array('controller' => 'staff_for_exams', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Staff For Exam'), array('controller' => 'staff_for_exams', 'action' => 'add')); ?> </li>
	</ul>
</div>
