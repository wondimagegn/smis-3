<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="graduationRequirements index">
	<div class="smallheading"><?php echo __('List of Graduation Requirements');?></div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th style="width:10%"><?php echo $this->Paginator->sort('cgpa','CGPA');?></th>
			<th style="width:20%"><?php echo $this->Paginator->sort('program_id');?></th>
			<th style="width:20%"><?php echo $this->Paginator->sort('academic_year','Admission Year');?></th>
			<th style="width:20%"><?php echo $this->Paginator->sort('created','Date Created');?></th>
			<th style="width:20%"><?php echo $this->Paginator->sort('modified','Date Modified');?></th>
			<th style="width:10%; text-align:center" class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($graduationRequirements as $graduationRequirement):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $graduationRequirement['GraduationRequirement']['cgpa']; ?>&nbsp;</td>
		<td>
			<?php echo $graduationRequirement['Program']['name']; ?>
		</td>
		<td><?php echo $graduationRequirement['GraduationRequirement']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $this->Format->humanize_date($graduationRequirement['GraduationRequirement']['created']); ?>&nbsp;</td>
		<td><?php echo $this->Format->humanize_date($graduationRequirement['GraduationRequirement']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $graduationRequirement['GraduationRequirement']['id'])); ?>
			<?php //echo $this->Html->link(__('Delete'), array('action' => 'delete', $graduationRequirement['GraduationRequirement']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $graduationRequirement['GraduationRequirement']['id'])); ?>
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
