<?php ?>
<div class="box">
     <div class="box-header bg-transparent">
  	 <h6 class="box-title">
		<?php echo __('Departments');?>
	     </h6>
     </div>
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
	  <table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('title');?></th>
			<th><?php echo $this->Paginator->sort('content');?></th>
			<th><?php echo $this->Paginator->sort('college_id');?></th>
			<th><?php echo $this->Paginator->sort('department_id');?></th>
			<th><?php echo $this->Paginator->sort('published_date');?></th>
			<th><?php echo $this->Paginator->sort('start_date');?></th>
			<th><?php echo $this->Paginator->sort('end_date');?></th>
			<th><?php echo $this->Paginator->sort('user_id');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($notes as $note):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $note['Note']['id']; ?>&nbsp;</td>
		<td><?php echo $note['Note']['title']; ?>&nbsp;</td>
		<td><?php echo $note['Note']['content']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($note['College']['name'], array('controller' => 'colleges', 'action' => 'view', $note['College']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($note['Department']['name'], array('controller' => 'departments', 'action' => 'view', $note['Department']['id'])); ?>
		</td>
		<td><?php echo $note['Note']['published_date']; ?>&nbsp;</td>
		<td><?php echo $note['Note']['start_date']; ?>&nbsp;</td>
		<td><?php echo $note['Note']['end_date']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($note['User']['id'], array('controller' => 'users', 'action' => 'view', $note['User']['id'])); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $note['Note']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $note['Note']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $note['Note']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $note['Note']['id'])); ?>
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

