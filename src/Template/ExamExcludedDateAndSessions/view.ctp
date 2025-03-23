<div class="examExcludedDateAndSessions view">
<h2><?php echo __('Exam Excluded Date And Session');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examExcludedDateAndSession['ExamExcludedDateAndSession']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Exam Period'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($examExcludedDateAndSession['ExamPeriod']['id'], array('controller' => 'exam_periods', 'action' => 'view', $examExcludedDateAndSession['ExamPeriod']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Excluded Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examExcludedDateAndSession['ExamExcludedDateAndSession']['excluded_date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Session'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examExcludedDateAndSession['ExamExcludedDateAndSession']['session']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
