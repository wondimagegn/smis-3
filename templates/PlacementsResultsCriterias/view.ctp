<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="placementsResultsCriterias view">
<h2><?php echo __('Placements Results Criteria');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $placementsResultsCriteria['PlacementsResultsCriteria']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $placementsResultsCriteria['PlacementsResultsCriteria']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Admissionyear'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $placementsResultsCriteria['PlacementsResultsCriteria']['admissionyear']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('College'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($placementsResultsCriteria['College']['name'], array('controller' => 'colleges', 'action' => 'view', $placementsResultsCriteria['College']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Result From'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $placementsResultsCriteria['PlacementsResultsCriteria']['result_from']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Result To'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $placementsResultsCriteria['PlacementsResultsCriteria']['result_to']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Number'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $placementsResultsCriteria['PlacementsResultsCriteria']['number']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $placementsResultsCriteria['PlacementsResultsCriteria']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $placementsResultsCriteria['PlacementsResultsCriteria']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="related">
	<h3><?php
	if (isset($placementsResultsCriteria['ReservedPlace']) && !empty($placementsResultsCriteria['ReservedPlace'])) 
	{
	 __('Related Reserved Places');
	 
	} 
	?></h3>
	<?php if (!empty($placementsResultsCriteria['ReservedPlace'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Placements Results Criteria Id'); ?></th>
		<th><?php echo __('Department Id'); ?></th>
		<th><?php echo __('Coolege Id'); ?></th>
		<th><?php echo __('Number'); ?></th>
		<th><?php echo __('Description'); ?></th>
		<th><?php echo __('Academicyear'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($placementsResultsCriteria['ReservedPlace'] as $reservedPlace):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $reservedPlace['id'];?></td>
			<td><?php echo $reservedPlace['placements_results_criteria_id'];?></td>
			<td><?php echo $reservedPlace['department_id'];?></td>
			<td><?php echo $reservedPlace['coolege_id'];?></td>
			<td><?php echo $reservedPlace['number'];?></td>
			<td><?php echo $reservedPlace['description'];?></td>
			<td><?php echo $reservedPlace['academicyear'];?></td>
			<td><?php echo $reservedPlace['created'];?></td>
			<td><?php echo $reservedPlace['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'reserved_places', 'action' => 'view', $reservedPlace['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'reserved_places', 'action' => 'edit', $reservedPlace['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'reserved_places', 'action' => 'delete', $reservedPlace['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $reservedPlace['id'])); ?>
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
