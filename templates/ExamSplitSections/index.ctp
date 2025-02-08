<div class="examSplitSections index">
	<h2><?php echo __('Exam Split Sections');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('section_split_for_exam_id');?></th>
			<th><?php echo $this->Paginator->sort('section_name');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($examSplitSections as $examSplitSection):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $examSplitSection['ExamSplitSection']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($examSplitSection['SectionSplitForExam']['id'], array('controller' => 'section_split_for_exams', 'action' => 'view', $examSplitSection['SectionSplitForExam']['id'])); ?>
		</td>
		<td><?php echo $examSplitSection['ExamSplitSection']['section_name']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $examSplitSection['ExamSplitSection']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $examSplitSection['ExamSplitSection']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $examSplitSection['ExamSplitSection']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $examSplitSection['ExamSplitSection']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Exam Split Section'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Section Split For Exams'), array('controller' => 'section_split_for_exams', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Section Split For Exam'), array('controller' => 'section_split_for_exams', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
	</ul>
</div>