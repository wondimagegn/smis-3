<?php ?>
<div class="box">
     <div class="box-header bg-transparent">
  	 <h2 class="box-title">
	<?php echo __('Announcements'); ?>
	 </h2>
     </div>
     <div class="box-body">
       <div class="row">
           <div class="large-12 columns">
             <table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id',
			'S.No'); ?></th>
			<th><?php echo $this->Paginator->sort('headline','Title'); ?></th>
			<th><?php echo $this->Paginator->sort('story','Story'); ?></th>
			<th><?php echo $this->Paginator->sort('is_published','Status'); ?></th>
			<th><?php echo $this->Paginator->sort('annucement_start','Announcement Start'); ?></th>
			<th><?php echo $this->Paginator->sort('annucement_end','Announcement End'); ?></th>
			
			<th class="actions">
			<?php echo __('Actions'); ?>
			</th>
	</tr>
	</thead>
	<tbody>
	<?php 
	$count=1;
	foreach ($announcements as $announcement): ?>
	<tr>
		<td><?php echo $count++; ?>&nbsp;</td>
		<td><?php echo h($announcement['Announcement']['headline']); ?>&nbsp;</td>
		<td><?php echo h($announcement['Announcement']['story']); ?>&nbsp;</td>
		<td><?php echo h($announcement['Announcement']['is_published']==1 ? 'Yes':'No'); ?>&nbsp;</td>
		<td><?php echo h($announcement['Announcement']['annucement_start']); ?>&nbsp;</td>
		<td><?php echo h($announcement['Announcement']['annucement_end']); ?>&nbsp;</td>
		
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $announcement['Announcement']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $announcement['Announcement']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $announcement['Announcement']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $announcement['Announcement']['id']))); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
		'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
	
            </div>
        </div>
      </div>
</div>
