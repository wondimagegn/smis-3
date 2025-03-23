<div class="studentEvalutionComments index">
	<h2><?php echo __('Student Evalution Comments'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('instructor_evalution_question_id'); ?></th>
			<th><?php echo $this->Paginator->sort('student_id'); ?></th>
			<th><?php echo $this->Paginator->sort('published_course_id'); ?></th>
			<th><?php echo $this->Paginator->sort('comment'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($studentEvalutionComments as $studentEvalutionComment): ?>
	<tr>
		<td><?php echo h($studentEvalutionComment['StudentEvalutionComment']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($studentEvalutionComment['InstructorEvalutionQuestion']['id'], array('controller' => 'instructor_evalution_questions', 'action' => 'view', $studentEvalutionComment['InstructorEvalutionQuestion']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($studentEvalutionComment['Student']['id'], array('controller' => 'students', 'action' => 'view', $studentEvalutionComment['Student']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($studentEvalutionComment['PublishedCourse']['id'], array('controller' => 'published_courses', 'action' => 'view', $studentEvalutionComment['PublishedCourse']['id'])); ?>
		</td>
		<td><?php echo h($studentEvalutionComment['StudentEvalutionComment']['comment']); ?>&nbsp;</td>
		<td><?php echo h($studentEvalutionComment['StudentEvalutionComment']['created']); ?>&nbsp;</td>
		<td><?php echo h($studentEvalutionComment['StudentEvalutionComment']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $studentEvalutionComment['StudentEvalutionComment']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $studentEvalutionComment['StudentEvalutionComment']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $studentEvalutionComment['StudentEvalutionComment']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $studentEvalutionComment['StudentEvalutionComment']['id']))); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
		'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Student Evalution Comment'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Instructor Evalution Questions'), array('controller' => 'instructor_evalution_questions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Instructor Evalution Question'), array('controller' => 'instructor_evalution_questions', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
	</ul>
</div>
