<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="courseSplitSections index">
	<h2><?php echo __('Course Split Sections');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('section_split_for_published_course_id');?></th>
			<th><?php echo $this->Paginator->sort('section_name');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($courseSplitSections as $courseSplitSection):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $courseSplitSection['CourseSplitSection']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($courseSplitSection['SectionSplitForPublishedCourse']['id'], array('controller' => 'section_split_for_published_courses', 'action' => 'view', $courseSplitSection['SectionSplitForPublishedCourse']['id'])); ?>
		</td>
		<td><?php echo $courseSplitSection['CourseSplitSection']['section_name']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $courseSplitSection['CourseSplitSection']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $courseSplitSection['CourseSplitSection']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $courseSplitSection['CourseSplitSection']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $courseSplitSection['CourseSplitSection']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Course Split Section'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Section Split For Published Courses'), array('controller' => 'section_split_for_published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Section Split For Published Course'), array('controller' => 'section_split_for_published_courses', 'action' => 'add')); ?> </li>
	</ul>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
