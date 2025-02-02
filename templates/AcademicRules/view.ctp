<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
	     <h6 class="box-title">
		<?php echo __('Add Academic Rule'); ?>
	     </h6>
	  </div>
	  <div class="large-12 columns">
            
<div class="academicRules view">
<h2><?php echo __('Academic Rule');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $academicRule['AcademicRule']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $academicRule['AcademicRule']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('From'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $academicRule['AcademicRule']['from']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('To'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $academicRule['AcademicRule']['to']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $academicRule['AcademicRule']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $academicRule['AcademicRule']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Academic Rule'), array('action' => 'edit', $academicRule['AcademicRule']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Academic Rule'), array('action' => 'delete', $academicRule['AcademicRule']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $academicRule['AcademicRule']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Academic Rules'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Academic Rule'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Academic Stands'), array('controller' => 'academic_stands', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Academic Stand'), array('controller' => 'academic_stands', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Academic Stands');?></h3>
	<?php if (!empty($academicRule['AcademicStand'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Year Level Id'); ?></th>
		<th><?php echo __('Semester'); ?></th>
		<th><?php echo __('Academic Year From'); ?></th>
		<th><?php echo __('Academic Year To'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Sort Order'); ?></th>
		<th><?php echo __('Status Visible'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($academicRule['AcademicStand'] as $academicStand):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $academicStand['id'];?></td>
			<td><?php echo $academicStand['year_level_id'];?></td>
			<td><?php echo $academicStand['semester'];?></td>
			<td><?php echo $academicStand['academic_year_from'];?></td>
			<td><?php echo $academicStand['academic_year_to'];?></td>
			<td><?php echo $academicStand['name'];?></td>
			<td><?php echo $academicStand['sort_order'];?></td>
			<td><?php echo $academicStand['status_visible'];?></td>
			<td><?php echo $academicStand['created'];?></td>
			<td><?php echo $academicStand['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'academic_stands', 'action' => 'view', $academicStand['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'academic_stands', 'action' => 'edit', $academicStand['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'academic_stands', 'action' => 'delete', $academicStand['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $academicStand['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Academic Stand'), array('controller' => 'academic_stands', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
