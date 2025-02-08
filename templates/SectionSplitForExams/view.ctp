<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="sectionSplitForExams view">
<h2><?php echo __('Section Split For Exam');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Section'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($sectionSplitForExam['Section']['name'], array('controller' => 'sections', 'action' => 'view', $sectionSplitForExam['Section']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Published Course'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($sectionSplitForExam['PublishedCourse']['Course']['course_code_title'], array('controller' => 'published_courses', 'action' => 'view', $sectionSplitForExam['PublishedCourse']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="related">
	<h3><?php echo __('Related Exam Split Sections');?></h3>
	<?php if (!empty($sectionSplitForExam['ExamSplitSection'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('S.N<u>o</u>'); ?></th>
		<th><?php echo __('Section Name'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		$count = 1;
		foreach ($sectionSplitForExam['ExamSplitSection'] as $examSplitSection):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $count++;?></td>
			<td><?php echo $examSplitSection['section_name'];?></td>
			<td class="actions">
			<!---	<?php echo $this->Html->link(__('View'), array('controller' => 'exam_split_sections', 'action' => 'view', $examSplitSection['id'])); ?>

				<?php echo $this->Html->link(__('Edit'), array('controller' => 'exam_split_sections', 'action' => 'edit', $examSplitSection['id'])); ?> --->
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'exam_split_sections', 'action' => 'delete', $examSplitSection['id']), null, sprintf(__('Are you sure you want to delete?'), $examSplitSection['id'])); ?>
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
