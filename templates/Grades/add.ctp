<div class="grades form">
<?php echo $this->Form->create('Grade');?>
	<fieldset>
		<legend class="smallheading"><?php echo __('Add Grade'); ?></legend>
	<?php
		echo $this->Form->input('grade');
		echo $this->Form->input('grade_type_id');
		echo $this->Form->input('point_value');
		echo $this->Form->input('pass_grade');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="grades index">
	<h2><?php echo __('Grades');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo __('S.No');?></th>
			<th><?php echo __('Grade');?></th>
			<th><?php echo __('Grade Type');?></th>
			<th><?php echo __('Point Value');?></th>
			<th><?php echo __('Pass/Fail');?></th>
			<th><?php echo __('Active');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$count=1;
	foreach ($gradesss as $grade):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $count++; ?>&nbsp;</td>
		<td><?php echo $grade['Grade']['grade']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($grade['GradeType']['type'], array('controller' => 'grade_types', 'action' => 'view', $grade['GradeType']['id'])); ?>
		</td>
		<td><?php echo $grade['Grade']['point_value']; ?>&nbsp;</td>
		<td><?php echo $grade['Grade']['pass_grade']; ?>&nbsp;</td>
		<td><?php echo $grade['Grade']['active']; ?>&nbsp;</td>
		

	</tr>
<?php endforeach; ?>
	</table>

</div>
