<div class="studentEvalutionRates form">
	<?= $this->Form->create('StudentEvalutionRate'); ?>
	<fieldset>
		<legend><?= __('Edit Student Evalution Rate'); ?></legend>
		<?php
		echo $this->Form->input('id');
		echo $this->Form->input('instructor_evalution_question_id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('published_course_id');
		echo $this->Form->input('rating');
		?>
	</fieldset>
	<?= $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?= __('Actions'); ?></h3>
	<ul>
		<li><?= $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('StudentEvalutionRate.id')), array('confirm' => __('Are you sure you want to delete # %s?', $this->Form->value('StudentEvalutionRate.id')))); ?></li>
		<li><?= $this->Html->link(__('List Student Evalution Rates'), array('action' => 'index')); ?></li>
		<li><?= $this->Html->link(__('List Instructor Evalution Questions'), array('controller' => 'instructor_evalution_questions', 'action' => 'index')); ?> </li>
		<li><?= $this->Html->link(__('New Instructor Evalution Question'), array('controller' => 'instructor_evalution_questions', 'action' => 'add')); ?> </li>
		<li><?= $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?= $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?= $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?= $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
	</ul>
</div>