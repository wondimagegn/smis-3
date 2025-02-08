<?php ?>
<div class="box">
     <div class="box-header bg-transparent">
  	 <h2 class="box-title">
	<?php echo __('Campuses');?>
	 </h2>
     </div>
     <div class="box-body">
       <div class="row">
           <div class="large-12 columns">

<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('description');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($campuses as $campus):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $campus['Campus']['id']; ?>&nbsp;</td>
		<td><?php echo $campus['Campus']['name']; ?>&nbsp;</td>
		<td><?php echo $campus['Campus']['description']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $campus['Campus']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $campus['Campus']['id'])); ?>
			
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
        </div>
      </div>
</div>
