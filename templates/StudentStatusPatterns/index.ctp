<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="studentStatusPatterns index">
	<h2><?php echo __('Student Status Patterns');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('program_id');?></th>
			<th><?php echo $this->Paginator->sort('program_type_id');?></th>
			<th><?php echo $this->Paginator->sort('acadamic_year');?></th>
			<th><?php echo $this->Paginator->sort('application_date');?></th>
			<th><?php echo $this->Paginator->sort('pattern');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($studentStatusPatterns as $studentStatusPattern):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $studentStatusPattern['StudentStatusPattern']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($studentStatusPattern['Program']['name'], array('controller' => 'programs', 'action' => 'view', $studentStatusPattern['Program']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($studentStatusPattern['ProgramType']['name'], array('controller' => 'program_types', 'action' => 'view', $studentStatusPattern['ProgramType']['id'])); ?>
		</td>
		<td><?php echo $studentStatusPattern['StudentStatusPattern']['acadamic_year']; ?>&nbsp;</td>
		<td><?php echo $studentStatusPattern['StudentStatusPattern']['application_date']; ?>&nbsp;</td>
		<td><?php echo $studentStatusPattern['StudentStatusPattern']['pattern']; ?>&nbsp;</td>
		<td><?php echo $studentStatusPattern['StudentStatusPattern']['created']; ?>&nbsp;</td>
		<td><?php echo $studentStatusPattern['StudentStatusPattern']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $studentStatusPattern['StudentStatusPattern']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $studentStatusPattern['StudentStatusPattern']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $studentStatusPattern['StudentStatusPattern']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $studentStatusPattern['StudentStatusPattern']['id'])); ?>
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
