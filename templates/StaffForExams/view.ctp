<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="staffForExams view">
<h2><?php echo __('Staff For Exam');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $staffForExam['StaffForExam']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('College'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($staffForExam['College']['name'], array('controller' => 'colleges', 'action' => 'view', $staffForExam['College']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Academic Year'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $staffForExam['StaffForExam']['academic_year']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Semester'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $staffForExam['StaffForExam']['semester']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Staff'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($staffForExam['Staff']['id'], array('controller' => 'staffs', 'action' => 'view', $staffForExam['Staff']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>

<div class="related">
	<h3><?php echo __('Related Instructor Exam Exclude Date Constraints');?></h3>
	<?php if (!empty($staffForExam['InstructorExamExcludeDateConstraint'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Staff For Exam Id'); ?></th>
		<th><?php echo __('Academic Year'); ?></th>
		<th><?php echo __('Semester'); ?></th>
		<th><?php echo __('Exam Date'); ?></th>
		<th><?php echo __('Session'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($staffForExam['InstructorExamExcludeDateConstraint'] as $instructorExamExcludeDateConstraint):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $instructorExamExcludeDateConstraint['id'];?></td>
			<td><?php echo $instructorExamExcludeDateConstraint['staff_id'];?></td>
			<td><?php echo $instructorExamExcludeDateConstraint['staff_for_exam_id'];?></td>
			<td><?php echo $instructorExamExcludeDateConstraint['academic_year'];?></td>
			<td><?php echo $instructorExamExcludeDateConstraint['semester'];?></td>
			<td><?php echo $instructorExamExcludeDateConstraint['exam_date'];?></td>
			<td><?php echo $instructorExamExcludeDateConstraint['session'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'instructor_exam_exclude_date_constraints', 'action' => 'view', $instructorExamExcludeDateConstraint['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'instructor_exam_exclude_date_constraints', 'action' => 'edit', $instructorExamExcludeDateConstraint['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'instructor_exam_exclude_date_constraints', 'action' => 'delete', $instructorExamExcludeDateConstraint['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $instructorExamExcludeDateConstraint['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

</div>
<div class="related">
	<h3><?php echo __('Related Instructor Number Of Exam Constraints');?></h3>
	<?php if (!empty($staffForExam['InstructorNumberOfExamConstraint'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Staff For Exam Id'); ?></th>
		<th><?php echo __('Academic Year'); ?></th>
		<th><?php echo __('Semester'); ?></th>
		<th><?php echo __('Max Number Of Exam'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($staffForExam['InstructorNumberOfExamConstraint'] as $instructorNumberOfExamConstraint):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $instructorNumberOfExamConstraint['id'];?></td>
			<td><?php echo $instructorNumberOfExamConstraint['staff_id'];?></td>
			<td><?php echo $instructorNumberOfExamConstraint['staff_for_exam_id'];?></td>
			<td><?php echo $instructorNumberOfExamConstraint['academic_year'];?></td>
			<td><?php echo $instructorNumberOfExamConstraint['semester'];?></td>
			<td><?php echo $instructorNumberOfExamConstraint['max_number_of_exam'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'instructor_number_of_exam_constraints', 'action' => 'view', $instructorNumberOfExamConstraint['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'instructor_number_of_exam_constraints', 'action' => 'edit', $instructorNumberOfExamConstraint['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'instructor_number_of_exam_constraints', 'action' => 'delete', $instructorNumberOfExamConstraint['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $instructorNumberOfExamConstraint['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

</div>
<div class="related">
	<h3><?php echo __('Related Invigilators');?></h3>
	<?php if (!empty($staffForExam['Invigilator'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Exam Schedule Id'); ?></th>
		<th><?php echo __('Staff Id'); ?></th>
		<th><?php echo __('Staff For Exam Id'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($staffForExam['Invigilator'] as $invigilator):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $invigilator['id'];?></td>
			<td><?php echo $invigilator['exam_schedule_id'];?></td>
			<td><?php echo $invigilator['staff_id'];?></td>
			<td><?php echo $invigilator['staff_for_exam_id'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'invigilators', 'action' => 'view', $invigilator['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'invigilators', 'action' => 'edit', $invigilator['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'invigilators', 'action' => 'delete', $invigilator['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $invigilator['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
