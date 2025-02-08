<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="quotas index">
	<h2><?php echo __('Quotas');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('college_id');?></th>
			<th><?php echo $this->Paginator->sort('female');?></th>
			<th><?php echo $this->Paginator->sort('regions');?></th>
			<th><?php //echo $this->Paginator->sort('developing_regions_id');?></th>
			<th><?php echo $this->Paginator->sort('academicyear');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($quotas as $quota):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $quota['Quota']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($quota['College']['name'], array('controller' => 'colleges', 'action' => 'view', $quota['College']['id'])); ?>
		</td>
		<td><?php echo $quota['Quota']['female']; ?>&nbsp;</td>
		<td><?php echo $quota['Quota']['regions']; ?>&nbsp;</td>
		<td><?php //echo $quota['Quota']['developing_regions_id']; ?>&nbsp;</td>
		<td><?php echo $quota['Quota']['academicyear']; ?>&nbsp;</td>
		<td><?php echo $quota['Quota']['created']; ?>&nbsp;</td>
		<td><?php echo $quota['Quota']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $quota['Quota']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $quota['Quota']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $quota['Quota']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $quota['Quota']['id'])); ?>
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
