<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
	     <h6 class="box-title">
		<?php echo __('Add Academic Rule'); ?>
	     </h6>
	  </div>
	  <div class="large-12 columns">
             <div class="academicRules index">
	<h2><?php echo __('Academic Rules');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('from');?></th>
			<th><?php echo $this->Paginator->sort('to');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	
	foreach ($academicRules as $academicRule):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $academicRule['AcademicRule']['id']; ?>&nbsp;</td>
		<td><?php echo $academicRule['AcademicRule']['name']; ?>&nbsp;</td>
		<td><?php echo $academicRule['AcademicRule']['from']; ?>&nbsp;</td>
		<td><?php echo $academicRule['AcademicRule']['to']; ?>&nbsp;</td>
		<td><?php echo $academicRule['AcademicRule']['created']; ?>&nbsp;</td>
		<td><?php echo $academicRule['AcademicRule']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $academicRule['AcademicRule']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $academicRule['AcademicRule']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $academicRule['AcademicRule']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $academicRule['AcademicRule']['id'])); ?>
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
