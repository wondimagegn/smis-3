<div class="instructorEvalutionSettings view">
<h2><?php echo __('Instructor Evalution Setting'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($instructorEvalutionSetting['InstructorEvalutionSetting']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Academic Year'); ?></dt>
		<dd>
			<?php echo h($instructorEvalutionSetting['InstructorEvalutionSetting']['academic_year']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Head Percent'); ?></dt>
		<dd>
			<?php echo h($instructorEvalutionSetting['InstructorEvalutionSetting']['head_percent']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Colleague Percent'); ?></dt>
		<dd>
			<?php echo h($instructorEvalutionSetting['InstructorEvalutionSetting']['colleague_percent']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Student Percent'); ?></dt>
		<dd>
			<?php echo h($instructorEvalutionSetting['InstructorEvalutionSetting']['student_percent']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($instructorEvalutionSetting['InstructorEvalutionSetting']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($instructorEvalutionSetting['InstructorEvalutionSetting']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Instructor Evalution Setting'), array('action' => 'edit', $instructorEvalutionSetting['InstructorEvalutionSetting']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Instructor Evalution Setting'), array('action' => 'delete', $instructorEvalutionSetting['InstructorEvalutionSetting']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $instructorEvalutionSetting['InstructorEvalutionSetting']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Instructor Evalution Settings'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Instructor Evalution Setting'), array('action' => 'add')); ?> </li>
	</ul>
</div>
