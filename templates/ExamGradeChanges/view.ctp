<div class="examGradeChanges view">
<h2><?php echo __('Exam Grade Change');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Exam Grade'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($examGradeChange['ExamGrade']['id'], array('controller' => 'exam_grades', 'action' => 'view', $examGradeChange['ExamGrade']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Grade'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['grade']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Reason'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['reason']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Minute Number'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['minute_number']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Initiated By Department'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['initiated_by_department']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Department Approval'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['department_approval']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Department Approval Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['department_approval_date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Department Approved By'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['department_approved_by']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Registrar Approval'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['registrar_approval']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Registrar Approval Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['registrar_approval_date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Registrar Approved By'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['registrar_approved_by']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('College Approval'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['college_approval']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('College Approval Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['college_approval_date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('College Approved By'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['college_approved_by']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $examGradeChange['ExamGradeChange']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Exam Grade Change'), array('action' => 'edit', $examGradeChange['ExamGradeChange']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Exam Grade Change'), array('action' => 'delete', $examGradeChange['ExamGradeChange']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $examGradeChange['ExamGradeChange']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Exam Grade Changes'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Exam Grade Change'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Exam Grades'), array('controller' => 'exam_grades', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Exam Grade'), array('controller' => 'exam_grades', 'action' => 'add')); ?> </li>
	</ul>
</div>
