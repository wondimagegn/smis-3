<div class="examTypes view">
<h2><?php echo __('Exam Type');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examType['ExamType']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Exam Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examType['ExamType']['exam_name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Percent'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examType['ExamType']['percent']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Published Course'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($examType['PublishedCourse']['id'], array('controller' => 'published_courses', 'action' => 'view', $examType['PublishedCourse']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Section'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($examType['Section']['name'], array('controller' => 'sections', 'action' => 'view', $examType['Section']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examType['ExamType']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examType['ExamType']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Exam Type'), array('action' => 'edit', $examType['ExamType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Exam Type'), array('action' => 'delete', $examType['ExamType']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $examType['ExamType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Exam Types'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Exam Type'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Sections'), array('controller' => 'sections', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Section'), array('controller' => 'sections', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Exam Results'), array('controller' => 'exam_results', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Exam Result'), array('controller' => 'exam_results', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Exam Results');?></h3>
	<?php if (!empty($examType['ExamResult'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Grade'); ?></th>
		<th><?php echo __('Grade Submission Date'); ?></th>
		<th><?php echo __('Exam Type Id'); ?></th>
		<th><?php echo __('Course Registration Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($examType['ExamResult'] as $examResult):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $examResult['id'];?></td>
			<td><?php echo $examResult['grade'];?></td>
			<td><?php echo $examResult['grade_submission_date'];?></td>
			<td><?php echo $examResult['exam_type_id'];?></td>
			<td><?php echo $examResult['course_registration_id'];?></td>
			<td><?php echo $examResult['created'];?></td>
			<td><?php echo $examResult['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'exam_results', 'action' => 'view', $examResult['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'exam_results', 'action' => 'edit', $examResult['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'exam_results', 'action' => 'delete', $examResult['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $examResult['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Exam Result'), array('controller' => 'exam_results', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
