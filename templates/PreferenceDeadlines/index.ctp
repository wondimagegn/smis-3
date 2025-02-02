<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="preferenceDeadlines index">
	<h3><?php echo __('Student Department Placement Preference Submition Deadline');?></h3>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('deadline');?></th>
			<th><?php echo $this->Paginator->sort('Academic Year', 'academicyear');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions" style="text-align:center""><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($preferenceDeadlines as $preferenceDeadline):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $preferenceDeadline['PreferenceDeadline']['deadline']; ?>&nbsp;</td>
		<td><?php echo $preferenceDeadline['PreferenceDeadline']['academicyear']; ?>&nbsp;</td>
		<td><?php echo $preferenceDeadline['PreferenceDeadline']['created']; ?>&nbsp;</td>
		<td><?php echo $preferenceDeadline['PreferenceDeadline']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $preferenceDeadline['PreferenceDeadline']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $preferenceDeadline['PreferenceDeadline']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $preferenceDeadline['PreferenceDeadline']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $preferenceDeadline['PreferenceDeadline']['id'])); ?>
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
