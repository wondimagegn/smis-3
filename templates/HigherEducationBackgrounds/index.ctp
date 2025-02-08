<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="higherEducationBackgrounds index">
	<h2><?php echo __('Higher Education Backgrounds');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('field_of_study');?></th>
			<th><?php echo $this->Paginator->sort('diploma_awarded');?></th>
			<th><?php echo $this->Paginator->sort('date_graduated');?></th>
			<th><?php echo $this->Paginator->sort('cgpa_at_graduation');?></th>
			<th><?php echo $this->Paginator->sort('city');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($higherEducationBackgrounds as $higherEducationBackground):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $higherEducationBackground['HigherEducationBackground']['id']; ?>&nbsp;</td>
		<td><?php echo $higherEducationBackground['HigherEducationBackground']['name']; ?>&nbsp;</td>
		<td><?php echo $higherEducationBackground['HigherEducationBackground']['field_of_study']; ?>&nbsp;</td>
		<td><?php echo $higherEducationBackground['HigherEducationBackground']['diploma_awarded']; ?>&nbsp;</td>
		<td><?php echo $higherEducationBackground['HigherEducationBackground']['date_graduated']; ?>&nbsp;</td>
		<td><?php echo $higherEducationBackground['HigherEducationBackground']['cgpa_at_graduation']; ?>&nbsp;</td>
		<td><?php echo $higherEducationBackground['HigherEducationBackground']['city']; ?>&nbsp;</td>
		<td><?php echo $higherEducationBackground['HigherEducationBackground']['created']; ?>&nbsp;</td>
		<td><?php echo $higherEducationBackground['HigherEducationBackground']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $higherEducationBackground['HigherEducationBackground']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $higherEducationBackground['HigherEducationBackground']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $higherEducationBackground['HigherEducationBackground']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $higherEducationBackground['HigherEducationBackground']['id'])); ?>
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
