<div class="studentEvalutionRates index">
	<h2><?= __('Student Evalution Rates'); ?></h2>
	<table cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th><?= $this->Paginator->sort('id'); ?></th>
				<th><?= $this->Paginator->sort('instructor_evalution_question_id'); ?></th>
				<th><?= $this->Paginator->sort('student_id'); ?></th>
				<th><?= $this->Paginator->sort('published_course_id'); ?></th>
				<th><?= $this->Paginator->sort('rating'); ?></th>
				<th><?= $this->Paginator->sort('created'); ?></th>
				<th><?= $this->Paginator->sort('modified'); ?></th>
				<th class="actions"><?= __('Actions'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($studentEvalutionRates as $studentEvalutionRate) : ?>
				<tr>
					<td><?= h($studentEvalutionRate['StudentEvalutionRate']['id']); ?>&nbsp;</td>
					<td>
						<?= $this->Html->link($studentEvalutionRate['InstructorEvalutionQuestion']['id'], array('controller' => 'instructor_evalution_questions', 'action' => 'view', $studentEvalutionRate['InstructorEvalutionQuestion']['id'])); ?>
					</td>
					<td>
						<?= $this->Html->link($studentEvalutionRate['Student']['id'], array('controller' => 'students', 'action' => 'view', $studentEvalutionRate['Student']['id'])); ?>
					</td>
					<td>
						<?= $this->Html->link($studentEvalutionRate['PublishedCourse']['id'], array('controller' => 'published_courses', 'action' => 'view', $studentEvalutionRate['PublishedCourse']['id'])); ?>
					</td>
					<td><?= h($studentEvalutionRate['StudentEvalutionRate']['rating']); ?>&nbsp;</td>
					<td><?= h($studentEvalutionRate['StudentEvalutionRate']['created']); ?>&nbsp;</td>
					<td><?= h($studentEvalutionRate['StudentEvalutionRate']['modified']); ?>&nbsp;</td>
					<td class="actions">
						<?= $this->Html->link(__('View'), array('action' => 'view', $studentEvalutionRate['StudentEvalutionRate']['id'])); ?>
						<?= $this->Html->link(__('Edit'), array('action' => 'edit', $studentEvalutionRate['StudentEvalutionRate']['id'])); ?>
						<?= $this->Form->postLink(__('Delete'), array('action' => 'delete', $studentEvalutionRate['StudentEvalutionRate']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $studentEvalutionRate['StudentEvalutionRate']['id']))); ?>
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
		?> </p>
	<div class="paging">
		<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
		?>
	</div>
</div>
<div class="actions">
	<h3><?= __('Actions'); ?></h3>
	<ul>
		<li><?= $this->Html->link(__('New Student Evalution Rate'), array('action' => 'add')); ?></li>
		<li><?= $this->Html->link(__('List Instructor Evalution Questions'), array('controller' => 'instructor_evalution_questions', 'action' => 'index')); ?> </li>
		<li><?= $this->Html->link(__('New Instructor Evalution Question'), array('controller' => 'instructor_evalution_questions', 'action' => 'add')); ?> </li>
		<li><?= $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?= $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?= $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?= $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
	</ul>
</div>