<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="programTypes view">
<h2><?php echo __('Program Type');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $programType['ProgramType']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $programType['ProgramType']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Description'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $programType['ProgramType']['description']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Program Type'), array('action' => 'edit', $programType['ProgramType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Program Type'), array('action' => 'delete', $programType['ProgramType']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $programType['ProgramType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Program Types'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Program Type'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Applications'), array('controller' => 'applications', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Application'), array('controller' => 'applications', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Offers'), array('controller' => 'offers', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Offer'), array('controller' => 'offers', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Placements'), array('controller' => 'placements', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Placement'), array('controller' => 'placements', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Applications');?></h3>
	<?php if (!empty($programType['Application'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Department Id'); ?></th>
		<th><?php echo __('Student Id'); ?></th>
		<th><?php echo __('Program Type Id'); ?></th>
		<th><?php echo __('Acadamicyear'); ?></th>
		<th><?php echo __('Applicationstatus'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($programType['Application'] as $application):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $application['id'];?></td>
			<td><?php echo $application['department_id'];?></td>
			<td><?php echo $application['student_id'];?></td>
			<td><?php echo $application['program_type_id'];?></td>
			<td><?php echo $application['acadamicyear'];?></td>
			<td><?php echo $application['applicationstatus'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'applications', 'action' => 'view', $application['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'applications', 'action' => 'edit', $application['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'applications', 'action' => 'delete', $application['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $application['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

</div>
<div class="related">
	<h3><?php echo __('Related Offers');?></h3>
	<?php if (!empty($programType['Offer'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Department Id'); ?></th>
		<th><?php echo __('Program Type Id'); ?></th>
		<th><?php echo __('Acadamicyear'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($programType['Offer'] as $offer):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $offer['id'];?></td>
			<td><?php echo $offer['department_id'];?></td>
			<td><?php echo $offer['program_type_id'];?></td>
			<td><?php echo $offer['acadamicyear'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'offers', 'action' => 'view', $offer['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'offers', 'action' => 'edit', $offer['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'offers', 'action' => 'delete', $offer['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $offer['id'])); ?>
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
