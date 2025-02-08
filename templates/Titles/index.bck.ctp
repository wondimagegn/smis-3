<?php 
?>
<div class="box">
     <div class="box-header bg-transparent">
  	 <h2 class="box-title"> <?php echo __('Titles');?></h2>
     </div>
     <div class="box-body">
       <div class="dataTables_wrapper">
	<table class="display" style="width:100%" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('title');?></th>
			<th><?php echo $this->Paginator->sort('description');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($titles as $title):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td><?php echo $title['Title']['title']; ?>&nbsp;</td>
		<td><?php echo $title['Title']['description']; ?>&nbsp;</td>
		<td class="actions">
		 
		<?php echo $this->Html->link(__(''), array('action' => 'view', $title['Title']['id']),array('class'=>'fontello-eye-outline')); ?>
		
		  
		<?php echo $this->Html->link(__(''), array('action' => 'edit', $title['Title']['id']),array('class'=>'fontello-pencil')); ?>	
	  
		<?php echo $this->Form->postLink(__(''), array('action' => 'delete', $title['Title']['id']), array('class'=>'icon-trash'), __('Are you sure you want to delete # %s?', $title['Title']['id'])); ?>
		
		</td>
	</tr>
<?php endforeach; ?>
	  </tbody>	
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
