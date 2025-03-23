<div class="examPeriods view">
<h2><?php echo __('Exam Period');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examPeriod['ExamPeriod']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('College'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($examPeriod['College']['name'], array('controller' => 'colleges', 'action' => 'view', $examPeriod['College']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Program'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($examPeriod['Program']['name'], array('controller' => 'programs', 'action' => 'view', $examPeriod['Program']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Program Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($examPeriod['ProgramType']['name'], array('controller' => 'program_types', 'action' => 'view', $examPeriod['ProgramType']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Academic Year'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examPeriod['ExamPeriod']['academic_year']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Semester'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examPeriod['ExamPeriod']['semester']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Year Level'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($examPeriod['YearLevel']['name'], array('controller' => 'year_levels', 'action' => 'view', $examPeriod['YearLevel']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Start Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examPeriod['ExamPeriod']['start_date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('End Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examPeriod['ExamPeriod']['end_date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Default Number Of Invigilator Per Exam'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examPeriod['ExamPeriod']['default_number_of_invigilator_per_exam']; ?>
			&nbsp;
		</dd>
	</dl>
</div>

<div class="related">
	<h3><?php echo __('Related Exam Excluded Date And Sessions');?></h3>
	<?php if (!empty($examPeriod['ExamExcludedDateAndSession'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Exam Period Id'); ?></th>
		<th><?php echo __('Excluded Date'); ?></th>
		<th><?php echo __('Session'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($examPeriod['ExamExcludedDateAndSession'] as $examExcludedDateAndSession):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $examExcludedDateAndSession['id'];?></td>
			<td><?php echo $examExcludedDateAndSession['exam_period_id'];?></td>
			<td><?php echo $examExcludedDateAndSession['excluded_date'];?></td>
			<td><?php echo $examExcludedDateAndSession['session'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'exam_excluded_date_and_sessions', 'action' => 'view', $examExcludedDateAndSession['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'exam_excluded_date_and_sessions', 'action' => 'edit', $examExcludedDateAndSession['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'exam_excluded_date_and_sessions', 'action' => 'delete', $examExcludedDateAndSession['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $examExcludedDateAndSession['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

</div>
