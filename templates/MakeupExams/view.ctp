<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
              
<div class="makeupExams view">
<h2><?php echo __('Makeup Exam');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $makeupExam['MakeupExam']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Minute Number'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $makeupExam['MakeupExam']['minute_number']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Published Course'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($makeupExam['PublishedCourse']['id'], array('controller' => 'published_courses', 'action' => 'view', $makeupExam['PublishedCourse']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Section'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($makeupExam['Section']['name'], array('controller' => 'sections', 'action' => 'view', $makeupExam['Section']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Student'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($makeupExam['Student']['id'], array('controller' => 'students', 'action' => 'view', $makeupExam['Student']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $makeupExam['MakeupExam']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $makeupExam['MakeupExam']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>

<div class="related">
	<h3><?php echo __('Related Exam Grades');?></h3>
	<?php if (!empty($makeupExam['ExamGrade'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Grade'); ?></th>
		<th><?php echo __('Course Registration Id'); ?></th>
		<th><?php echo __('Makeup Exam Id'); ?></th>
		<th><?php echo __('Department Approval'); ?></th>
		<th><?php echo __('Department Approval Date'); ?></th>
		<th><?php echo __('Department Approved By'); ?></th>
		<th><?php echo __('Registrar Approval'); ?></th>
		<th><?php echo __('Registrar Approval Date'); ?></th>
		<th><?php echo __('Registrar Approved By'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($makeupExam['ExamGrade'] as $examGrade):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $examGrade['id'];?></td>
			<td><?php echo $examGrade['grade'];?></td>
			<td><?php echo $examGrade['course_registration_id'];?></td>
			<td><?php echo $examGrade['makeup_exam_id'];?></td>
			<td><?php echo $examGrade['department_approval'];?></td>
			<td><?php echo $examGrade['department_approval_date'];?></td>
			<td><?php echo $examGrade['department_approved_by'];?></td>
			<td><?php echo $examGrade['registrar_approval'];?></td>
			<td><?php echo $examGrade['registrar_approval_date'];?></td>
			<td><?php echo $examGrade['registrar_approved_by'];?></td>
			<td><?php echo $examGrade['created'];?></td>
			<td><?php echo $examGrade['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'exam_grades', 'action' => 'view', $examGrade['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'exam_grades', 'action' => 'edit', $examGrade['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'exam_grades', 'action' => 'delete', $examGrade['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $examGrade['id'])); ?>
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
