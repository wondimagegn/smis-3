<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="instructorNumberOfExamConstraints index">
	<h2><?php echo __('Instructor Number Of Exam Constraints');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('staff_id');?></th>
			<th><?php echo $this->Paginator->sort('staff_for_exam_id');?></th>
			<th><?php echo $this->Paginator->sort('academic_year');?></th>
			<th><?php echo $this->Paginator->sort('semester');?></th>
			<th><?php echo $this->Paginator->sort('year_level_id');?></th>
			<th><?php echo $this->Paginator->sort('max_number_of_exam');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($instructorNumberOfExamConstraints as $instructorNumberOfExamConstraint):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $instructorNumberOfExamConstraint['InstructorNumberOfExamConstraint']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($instructorNumberOfExamConstraint['Staff']['id'], array('controller' => 'staffs', 'action' => 'view', $instructorNumberOfExamConstraint['Staff']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($instructorNumberOfExamConstraint['StaffForExam']['staff_id'], array('controller' => 'staff_for_exams', 'action' => 'view', $instructorNumberOfExamConstraint['StaffForExam']['id'])); ?>
		</td>
		<td><?php echo $instructorNumberOfExamConstraint['InstructorNumberOfExamConstraint']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $instructorNumberOfExamConstraint['InstructorNumberOfExamConstraint']['semester']; ?>&nbsp;</td>
		<td><?php echo $instructorNumberOfExamConstraint['InstructorNumberOfExamConstraint']['year_level_id']; ?>&nbsp;</td>
		<td><?php echo $instructorNumberOfExamConstraint['InstructorNumberOfExamConstraint']['max_number_of_exam']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $instructorNumberOfExamConstraint['InstructorNumberOfExamConstraint']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $instructorNumberOfExamConstraint['InstructorNumberOfExamConstraint']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $instructorNumberOfExamConstraint['InstructorNumberOfExamConstraint']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $instructorNumberOfExamConstraint['InstructorNumberOfExamConstraint']['id'])); ?>
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
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
