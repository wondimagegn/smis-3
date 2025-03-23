<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="transcriptFooters index">
	<div class="smallheading"><?php echo __('List of Transcript Footers');?></div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th style="width:5%"><?php echo 'N<u>o</u>'; ?></th>
			<th style="width:20%"><?php echo $this->Paginator->sort('program_id');?></th>
			<th style="width:20%"><?php echo $this->Paginator->sort('Admission Year','academic_year');?></th>
			<th style="width:20%"><?php echo $this->Paginator->sort('Date Created', 'created');?></th>
			<th style="width:20%"><?php echo $this->Paginator->sort('Date Modified', 'modified');?></th>
			<th style="width:15%; text-align:center" class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($transcriptFooters as $transcriptFooter):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $i; ?>&nbsp;</td>
		<td>
			<?php echo $transcriptFooter['Program']['name']; ?>
		</td>
		<td><?php echo $transcriptFooter['TranscriptFooter']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $this->Format->humanize_date($transcriptFooter['TranscriptFooter']['created']); ?>&nbsp;</td>
		<td><?php echo $this->Format->humanize_date($transcriptFooter['TranscriptFooter']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $transcriptFooter['TranscriptFooter']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $transcriptFooter['TranscriptFooter']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $transcriptFooter['TranscriptFooter']['id']), null, sprintf(__('Are you sure you want to delete '.$transcriptFooter['Program']['name'].' ('.$transcriptFooter['TranscriptFooter']['academic_year'].') transcript footer?'), $transcriptFooter['TranscriptFooter']['id'])); ?>
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
