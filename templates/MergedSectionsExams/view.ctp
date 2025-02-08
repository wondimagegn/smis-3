<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="mergedSectionsExams view">
<h2><?php echo __('Merged Sections Exam');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mergedSectionsExam['MergedSectionsExam']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Published Course'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($mergedSectionsExam['PublishedCourse']['id'], array('controller' => 'published_courses', 'action' => 'view', $mergedSectionsExam['PublishedCourse']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Section Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mergedSectionsExam['MergedSectionsExam']['section_name']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="related">
	<h3><?php echo __('Related Sections');?></h3>
	<?php if (!empty($mergedSectionsExam['Section'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('College Id'); ?></th>
		<th><?php echo __('Department Id'); ?></th>
		<th><?php echo __('Year Level Id'); ?></th>
		<th><?php echo __('Academicyear'); ?></th>
		<th><?php echo __('Program Id'); ?></th>
		<th><?php echo __('Program Type Id'); ?></th>
		<th><?php echo __('Archive'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($mergedSectionsExam['Section'] as $section):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $section['id'];?></td>
			<td><?php echo $section['name'];?></td>
			<td><?php echo $section['college_id'];?></td>
			<td><?php echo $section['department_id'];?></td>
			<td><?php echo $section['year_level_id'];?></td>
			<td><?php echo $section['academicyear'];?></td>
			<td><?php echo $section['program_id'];?></td>
			<td><?php echo $section['program_type_id'];?></td>
			<td><?php echo $section['archive'];?></td>
			<td><?php echo $section['created'];?></td>
			<td><?php echo $section['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'sections', 'action' => 'view', $section['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'sections', 'action' => 'edit', $section['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'sections', 'action' => 'delete', $section['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $section['id'])); ?>
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
