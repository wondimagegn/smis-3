<div class="studentEvalutionComments view">
<h2><?php echo __('Student Evalution Comment'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($studentEvalutionComment['StudentEvalutionComment']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Instructor Evalution Question'); ?></dt>
		<dd>
			<?php echo $this->Html->link($studentEvalutionComment['InstructorEvalutionQuestion']['id'], array('controller' => 'instructor_evalution_questions', 'action' => 'view', $studentEvalutionComment['InstructorEvalutionQuestion']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Student'); ?></dt>
		<dd>
			<?php echo $this->Html->link($studentEvalutionComment['Student']['id'], array('controller' => 'students', 'action' => 'view', $studentEvalutionComment['Student']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Published Course'); ?></dt>
		<dd>
			<?php echo $this->Html->link($studentEvalutionComment['PublishedCourse']['id'], array('controller' => 'published_courses', 'action' => 'view', $studentEvalutionComment['PublishedCourse']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Comment'); ?></dt>
		<dd>
			<?php echo h($studentEvalutionComment['StudentEvalutionComment']['comment']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($studentEvalutionComment['StudentEvalutionComment']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($studentEvalutionComment['StudentEvalutionComment']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Student Evalution Comment'), array('action' => 'edit', $studentEvalutionComment['StudentEvalutionComment']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Student Evalution Comment'), array('action' => 'delete', $studentEvalutionComment['StudentEvalutionComment']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $studentEvalutionComment['StudentEvalutionComment']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Student Evalution Comments'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student Evalution Comment'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Instructor Evalution Questions'), array('controller' => 'instructor_evalution_questions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Instructor Evalution Question'), array('controller' => 'instructor_evalution_questions', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
	</ul>
</div>
