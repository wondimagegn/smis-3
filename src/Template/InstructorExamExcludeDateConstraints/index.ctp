<div class="instructorExamExcludeDateConstraints index">
	<h2><?php echo __('Instructor Exam Exclude Date Constraints');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('staff_id');?></th>
			<th><?php echo $this->Paginator->sort('staff_for_exam_id');?></th>
			<th><?php echo $this->Paginator->sort('academic_year');?></th>
			<th><?php echo $this->Paginator->sort('semester');?></th>
			<th><?php echo $this->Paginator->sort('exam_date');?></th>
			<th><?php echo $this->Paginator->sort('session');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($instructorExamExcludeDateConstraints as $instructorExamExcludeDateConstraint):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($instructorExamExcludeDateConstraint['Staff']['id'], array('controller' => 'staffs', 'action' => 'view', $instructorExamExcludeDateConstraint['Staff']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($instructorExamExcludeDateConstraint['StaffForExam']['staff_id'], array('controller' => 'staff_for_exams', 'action' => 'view', $instructorExamExcludeDateConstraint['StaffForExam']['id'])); ?>
		</td>
		<td><?php echo $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['semester']; ?>&nbsp;</td>
		<td><?php echo $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['exam_date']; ?>&nbsp;</td>
		<td><?php echo $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['session']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Instructor Exam Exclude Date Constraint'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Staffs'), array('controller' => 'staffs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Staff'), array('controller' => 'staffs', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Staff For Exams'), array('controller' => 'staff_for_exams', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Staff For Exam'), array('controller' => 'staff_for_exams', 'action' => 'add')); ?> </li>
	</ul>
</div>