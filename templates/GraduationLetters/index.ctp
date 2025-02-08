<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="graduationLetters index">
<div class="smallheading"><?php echo __('Graduation Letter Templates');?></div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo 'S. No.';?></th>
			<th><?php echo $this->Paginator->sort('type');?></th>
			<th><?php echo $this->Paginator->sort('program_id');?></th>
			<th><?php echo $this->Paginator->sort('program_type_id');?></th>
			<th><?php echo $this->Paginator->sort('title');?></th>
			<th><?php echo $this->Paginator->sort('academic_year');?></th>
			<th><?php echo $this->Paginator->sort('applicable_for_current_student');?></th>
			<th style="text-align:center" class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$count = 1;
	foreach ($graduationLetters as $graduationLetter):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $count++; ?>&nbsp;</td>
		<td><?php echo $graduationLetter['GraduationLetter']['type']; ?>&nbsp;</td>
		<td>
			<?php echo $graduationLetter['Program']['name']; ?>
		</td>
		<td>
			<?php echo $graduationLetter['ProgramType']['name']; ?>
		</td>
		<td><?php echo $graduationLetter['GraduationLetter']['title']; ?>&nbsp;</td>
		<td><?php echo $graduationLetter['GraduationLetter']['academic_year']; ?>&nbsp;</td>
		<td><?php echo ($graduationLetter['GraduationLetter']['applicable_for_current_student'] == 1 ? 'Yes' : 'No'); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $graduationLetter['GraduationLetter']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $graduationLetter['GraduationLetter']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $graduationLetter['GraduationLetter']['id']), null, sprintf(__('Are you sure you want to delete "%s" graduation letter template?'), $graduationLetter['GraduationLetter']['title'])); ?>
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
