<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
		<div class="large-12 columns">
                     <h2 class="box-title">
			<?php echo __('List of Academic Status');?>
		      </h2>
		</div>
		<div class="large-12 columns">
 <table class="display" cellpadding="0" cellspacing="0">
	<tr>
			<th style="width:75%"><?php echo $this->Paginator->sort('name','Acdamic Status');?></th>
			<th style="width:20%"><?php echo $this->Paginator->sort('order','Computation Order');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($academicStatuses as $academicStatus):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $academicStatus['AcademicStatus']['name']; ?>&nbsp;</td>
		<td><?php echo $academicStatus['AcademicStatus']['order']; ?>&nbsp;</td>
		
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
