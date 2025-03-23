<div class="medicalHistories index">
	<h2><?php echo __('Medical Histories');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('student_id');?></th>
			<th><?php echo $this->Paginator->sort('user_id');?></th>
			<th><?php echo $this->Paginator->sort('record_type');?></th>
			<th><?php echo $this->Paginator->sort('details');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($medicalHistories as $medicalHistory):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $medicalHistory['MedicalHistory']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($medicalHistory['Student']['id'], array('controller' => 'students', 'action' => 'view', $medicalHistory['Student']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($medicalHistory['User']['id'], array('controller' => 'users', 'action' => 'view', $medicalHistory['User']['id'])); ?>
		</td>
		<td><?php echo $medicalHistory['MedicalHistory']['record_type']; ?>&nbsp;</td>
		<td><?php echo $medicalHistory['MedicalHistory']['details']; ?>&nbsp;</td>
		<td><?php echo $medicalHistory['MedicalHistory']['created']; ?>&nbsp;</td>
		<td><?php echo $medicalHistory['MedicalHistory']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $medicalHistory['MedicalHistory']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $medicalHistory['MedicalHistory']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $medicalHistory['MedicalHistory']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $medicalHistory['MedicalHistory']['id'])); ?>
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
