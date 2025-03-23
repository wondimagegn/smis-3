<div class="studentEvalutionRates view">
	<h2><?= __('Student Evalution Rate'); ?></h2>
	<dl>
		<dt><?= __('Id'); ?></dt>
		<dd>
			<?= h($studentEvalutionRate['StudentEvalutionRate']['id']); ?>
			&nbsp;
		</dd>
		<dt><?= __('Instructor Evalution Question'); ?></dt>
		<dd>
			<?= $this->Html->link($studentEvalutionRate['InstructorEvalutionQuestion']['id'], array('controller' => 'instructor_evalution_questions', 'action' => 'view', $studentEvalutionRate['InstructorEvalutionQuestion']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?= __('Student'); ?></dt>
		<dd>
			<?= $this->Html->link($studentEvalutionRate['Student']['id'], array('controller' => 'students', 'action' => 'view', $studentEvalutionRate['Student']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?= __('Published Course'); ?></dt>
		<dd>
			<?= $this->Html->link($studentEvalutionRate['PublishedCourse']['id'], array('controller' => 'published_courses', 'action' => 'view', $studentEvalutionRate['PublishedCourse']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?= __('Rating'); ?></dt>
		<dd>
			<?= h($studentEvalutionRate['StudentEvalutionRate']['rating']); ?>
			&nbsp;
		</dd>
		<dt><?= __('Created'); ?></dt>
		<dd>
			<?= h($studentEvalutionRate['StudentEvalutionRate']['created']); ?>
			&nbsp;
		</dd>
		<dt><?= __('Modified'); ?></dt>
		<dd>
			<?= h($studentEvalutionRate['StudentEvalutionRate']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?= __('Actions'); ?></h3>
	<ul>
		<li><?= $this->Html->link(__('Edit Student Evalution Rate'), array('action' => 'edit', $studentEvalutionRate['StudentEvalutionRate']['id'])); ?> </li>
		<li><?= $this->Form->postLink(__('Delete Student Evalution Rate'), array('action' => 'delete', $studentEvalutionRate['StudentEvalutionRate']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $studentEvalutionRate['StudentEvalutionRate']['id']))); ?> </li>
		<li><?= $this->Html->link(__('List Student Evalution Rates'), array('action' => 'index')); ?> </li>
		<li><?= $this->Html->link(__('New Student Evalution Rate'), array('action' => 'add')); ?> </li>
		<li><?= $this->Html->link(__('List Instructor Evalution Questions'), array('controller' => 'instructor_evalution_questions', 'action' => 'index')); ?> </li>
		<li><?= $this->Html->link(__('New Instructor Evalution Question'), array('controller' => 'instructor_evalution_questions', 'action' => 'add')); ?> </li>
		<li><?= $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?= $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?= $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?= $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
	</ul>
</div>