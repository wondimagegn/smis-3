<?php echo $this->Form->create('MealHall');?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="mealHalls index">
	<h2><?php echo __('List of Meal Halls');?></h2>
	
	<table cellpadding="0" cellspacing="0">
	<?php 
		echo "<tr><td width='50%'>".$this->Form->input('campus_id',array('type'=>'select', 'empty'=>'All'))."</td></tr>";

		echo '<tr><td colspan=2>'.$this->Form->submit(__('Search'), array('div' => false,'class'=>'tiny radius button bg-blue')).'</td></tr>'; 
	?>
	</table>

	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('campus_id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($mealHalls as $mealHall):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($mealHall['Campus']['name'], array('controller' => 'campuses', 'action' => 'view', $mealHall['Campus']['id'])); ?>
		</td>
		<td><?php echo $mealHall['MealHall']['name']; ?>&nbsp;</td>
		<td><?php echo $this->Format->short_date($mealHall['MealHall']['created']); ?>&nbsp;</td>
		<td><?php echo $this->Format->short_date($mealHall['MealHall']['modified']); ?>&nbsp;</td>
		<td class="actions">
		<!-- <?php echo $this->Html->link(__('View'), array('action' => 'view', $mealHall['MealHall']['id'])); ?> -->
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $mealHall['MealHall']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $mealHall['MealHall']['id']), null, sprintf(__('Are you sure you want to delete %s?'), $mealHall['MealHall']['name'])); ?>
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
