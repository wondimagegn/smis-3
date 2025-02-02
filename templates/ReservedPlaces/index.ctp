<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<table cellpadding="0" cellspacing="0"><tr> 
	<?php echo $this->Form->create('ReservedPlace', array('action' => 'index'));?> 
	<td> <?php echo $this->Form->input('ReservedPlace.department_id',array('label'=>'Department'));
			echo $this->Form->input('ReservedPlace.academicyear',array('id'=>'academicyear',
            'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--",'selected'=>isset($selected)?$selected:'')); ?>
	</td></tr>
	<tr><td><?php echo $this->Form->end(__('Search')); ?> </td>	
</tr></table>
<?php 
if(!empty($reservedPlaces)){


?>
 <div class="reservedPlaces index">
	<h2><?php echo __('Reserved Places');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			
			<th><?php echo $this->Paginator->sort('Result Category','placements_results_criteria_id');?></th>
			<th><?php echo $this->Paginator->sort('Department','participating_department_id');?></th>
			<th><?php echo $this->Paginator->sort('college_id');?></th>
			<th><?php echo $this->Paginator->sort('Department Capacity','number');?></th>
			<th><?php echo $this->Paginator->sort('description');?></th>
			<th><?php echo $this->Paginator->sort('Academic Year','academicyear');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	
	$i = 0;
	foreach ($reservedPlaces as $reservedPlace):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		
		<!--<td>
			<?php echo $this->Html->link($reservedPlace['PlacementsResultsCriteria']['name'], array('controller' => 'placements_results_criterias', 'action' => 'view', $reservedPlace['PlacementsResultsCriteria']['id'])); ?>
		</td> -->
		<td>
			<?php echo $this->Html->link($reservedPlace['PlacementsResultsCriteria']['result_category'], array('controller' => 'placements_results_criterias', 'action' => 'view', $reservedPlace['PlacementsResultsCriteria']['id'])).')'; ?>
		</td>
		<td>
			<?php echo $this->Html->link($reservedPlace['ReservedPlace']['participating_department_name'], array('controller' => 'departments', 'action' => 'view', $reservedPlace['ReservedPlace']['participating_department_id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($reservedPlace['College']['name'], array('controller' => 'colleges', 'action' => 'view', $reservedPlace['College']['id'])); ?>
		</td>
		<td><?php echo $reservedPlace['ReservedPlace']['number']; ?>&nbsp;</td>
		<td><?php echo $reservedPlace['ReservedPlace']['description']; ?>&nbsp;</td>
		<td><?php echo $reservedPlace['ReservedPlace']['academicyear']; ?>&nbsp;</td>
		<td><?php echo $reservedPlace['ReservedPlace']['created']; ?>&nbsp;</td>
		<td><?php echo $reservedPlace['ReservedPlace']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $reservedPlace['ReservedPlace']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $reservedPlace['ReservedPlace']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $reservedPlace['ReservedPlace']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $reservedPlace['ReservedPlace']['id'])); ?>
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
<?php 
} 

?>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
