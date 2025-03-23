<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="placementsResultsCriterias index">
	<h2><?php echo __('Placements Results Criterias');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			
			<th><?php echo $this->Paginator->sort('result_category');?></th>
			<th><?php echo $this->Paginator->sort('admissionyear');?></th>
			<th><?php echo $this->Paginator->sort('college_id');?></th>
			<th><?php echo $this->Paginator->sort('result_from');?></th>
			<th><?php echo $this->Paginator->sort('result_to');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	
	$i = 0;
	foreach ($placementsResultsCriterias as $placementsResultsCriteria):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		
		<td><?php echo $placementsResultsCriteria['PlacementsResultsCriteria']['result_category'].')'; ?>&nbsp;</td>
		<td><?php echo $placementsResultsCriteria['PlacementsResultsCriteria']['admissionyear']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($placementsResultsCriteria['College']['name'], array('controller' => 'colleges', 'action' => 'view', $placementsResultsCriteria['College']['id'])); ?>
		</td>
		<td><?php echo $placementsResultsCriteria['PlacementsResultsCriteria']['result_from']; ?>&nbsp;</td>
		<td><?php echo $placementsResultsCriteria['PlacementsResultsCriteria']['result_to']; ?>&nbsp;</td>
		
		<td><?php echo $placementsResultsCriteria['PlacementsResultsCriteria']['created']; ?>&nbsp;</td>
		<td><?php echo $placementsResultsCriteria['PlacementsResultsCriteria']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $placementsResultsCriteria['PlacementsResultsCriteria']['id'])); ?>
			<?php //echo $this->Html->link(__('Edit'), array('action' => 'edit', $placementsResultsCriteria['PlacementsResultsCriteria']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $placementsResultsCriteria['PlacementsResultsCriteria']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $placementsResultsCriteria['PlacementsResultsCriteria']['id'])); ?>
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
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
