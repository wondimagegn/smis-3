<?php ?>
<script>
$(document).ready(function () {
	$("#ProgramList").change(function(){
		//serialize form data
		var p_id = $("#ProgramList").val();
		window.location.replace("/graduationStatuses/index/"+p_id);
	});
});
</script>

<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="graduationStatuses index">
<div class="smallheading"><?php echo __('Graduation Statuses');?></div>
<table cellpadding="0" cellspacing="0" class="fs12">
	<tr>
		<td style="width:10%">Program:</td>
		<td style="width:90%"><?php echo $this->Form->input('program_id', array('label' => false, 'options' => $programs, 'id' => 'ProgramList', 'default' => $id)); ?></td>
	</tr>
</table>
<?php
if(!empty($graduationStatuses)) {
?>
<table cellpadding="0" cellspacing="0">
	<tr>
			<th style="width:5%"><?php echo $this->Paginator->sort('CGPA', 'cgpa');?></th>
			<th style="width:17%"><?php echo $this->Paginator->sort('status');?></th>
			<th style="width:12%"><?php echo $this->Paginator->sort('academic_year');?></th>
			<th style="width:13%"><?php echo $this->Paginator->sort('Applicable for Current Students', 'applicable_for_current_student');?></th>
			<th style="width:20%"><?php echo $this->Paginator->sort('Date Created', 'created');?></th>
			<th style="width:20%"><?php echo $this->Paginator->sort('Date Modified', 'modified');?></th>
			<th style="width:13%; text-align:center" class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($graduationStatuses as $graduationStatus):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $graduationStatus['GraduationStatus']['cgpa']; ?>&nbsp;</td>
		<td><?php echo $graduationStatus['GraduationStatus']['status']; ?>&nbsp;</td>
		<td><?php echo $graduationStatus['GraduationStatus']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $graduationStatus['GraduationStatus']['applicable_for_current_student'] == 1 ? 'Yes' : 'No'; ?>&nbsp;</td>
		<td><?php echo $this->Format->humanize_date($graduationStatus['GraduationStatus']['created']); ?>&nbsp;</td>
		<td><?php echo $this->Format->humanize_date($graduationStatus['GraduationStatus']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $graduationStatus['GraduationStatus']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $graduationStatus['GraduationStatus']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $graduationStatus['GraduationStatus']['id']), null, sprintf(__('Are you sure you want to delete "%s" status?'), $graduationStatus['GraduationStatus']['status'])); ?>
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
<?php
}
else {
	echo '<div class="info-box info-message"><span></span>There is no graduation status for the selected program.</div>';
}
?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
