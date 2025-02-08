<?php ?>
<div class="box">
     <div class="box-header bg-transparent">
  	 <h2 class="box-title"> <?php echo __('Roles');?></h2>
     </div>
     <div class="box-body">
       <div class="dataTables_wrapper">
		<table  class="display" style="width:100%" cellpadding="0" cellspacing="0">
		 
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('description');?></th>

			
	</tr>
	<?php
	$i = 0;
	foreach ($roles as $role):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $role['Role']['id']; ?>&nbsp;</td>
		<td><?php echo $role['Role']['name']; ?>&nbsp;</td>
		<td><?php echo $role['Role']['description']; ?>&nbsp;</td>
		
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
