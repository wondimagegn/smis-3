<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="offers index">
	<h2><?php echo __('Offers');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('department_id');?></th>
			<th><?php echo $this->Paginator->sort('program_type_id');?></th>
			<th><?php echo $this->Paginator->sort('acadamicyear');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($offers as $offer):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $offer['Offer']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($offer['Department']['name'], array('controller' => 'departments', 'action' => 'view', $offer['Department']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($offer['ProgramType']['name'], array('controller' => 'program_types', 'action' => 'view', $offer['ProgramType']['id'])); ?>
		</td>
		<td><?php echo $offer['Offer']['acadamicyear']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $offer['Offer']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $offer['Offer']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $offer['Offer']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $offer['Offer']['id'])); ?>
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
