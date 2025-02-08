<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="dismissals index">
	<h2><?php echo __('Dismissals');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('student_id');?></th>
			<th><?php echo $this->Paginator->sort('reason');?></th>
			<th><?php echo $this->Paginator->sort('request_date');?></th>
			<th><?php echo $this->Paginator->sort('acceptance_date');?></th>
			<th><?php echo $this->Paginator->sort('for_good');?></th>
			<th><?php echo $this->Paginator->sort('dismisal_date');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($dismissals as $dismissal):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $dismissal['Dismissal']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($dismissal['Student']['id'], array('controller' => 'students', 'action' => 'view', $dismissal['Student']['id'])); ?>
		</td>
		<td><?php echo $dismissal['Dismissal']['reason']; ?>&nbsp;</td>
		<td><?php echo $dismissal['Dismissal']['request_date']; ?>&nbsp;</td>
		<td><?php echo $dismissal['Dismissal']['acceptance_date']; ?>&nbsp;</td>
		<td><?php echo $dismissal['Dismissal']['for_good']; ?>&nbsp;</td>
		<td><?php echo $dismissal['Dismissal']['dismisal_date']; ?>&nbsp;</td>
		<td><?php echo $dismissal['Dismissal']['created']; ?>&nbsp;</td>
		<td><?php echo $dismissal['Dismissal']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $dismissal['Dismissal']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $dismissal['Dismissal']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $dismissal['Dismissal']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $dismissal['Dismissal']['id'])); ?>
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
