<div class="placementEntranceExamResultEntries index">
	<h2><?php echo __('Placement Entrance Exam Result Entries'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('accepted_student_id'); ?></th>
			<th><?php echo $this->Paginator->sort('student_id'); ?></th>
			<th><?php echo $this->Paginator->sort('result'); ?></th>
			<th><?php echo $this->Paginator->sort('placement_round_participant_id'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($placementEntranceExamResultEntries as $placementEntranceExamResultEntry): ?>
	<tr>
		<td><?php echo h($placementEntranceExamResultEntry['PlacementEntranceExamResultEntry']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($placementEntranceExamResultEntry['AcceptedStudent']['id'], array('controller' => 'accepted_students', 'action' => 'view', $placementEntranceExamResultEntry['AcceptedStudent']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($placementEntranceExamResultEntry['Student']['id'], array('controller' => 'students', 'action' => 'view', $placementEntranceExamResultEntry['Student']['id'])); ?>
		</td>
		<td><?php echo h($placementEntranceExamResultEntry['PlacementEntranceExamResultEntry']['result']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($placementEntranceExamResultEntry['PlacementRoundParticipant']['name'], array('controller' => 'placement_round_participants', 'action' => 'view', $placementEntranceExamResultEntry['PlacementRoundParticipant']['id'])); ?>
		</td>
		<td><?php echo h($placementEntranceExamResultEntry['PlacementEntranceExamResultEntry']['created']); ?>&nbsp;</td>
		<td><?php echo h($placementEntranceExamResultEntry['PlacementEntranceExamResultEntry']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $placementEntranceExamResultEntry['PlacementEntranceExamResultEntry']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $placementEntranceExamResultEntry['PlacementEntranceExamResultEntry']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $placementEntranceExamResultEntry['PlacementEntranceExamResultEntry']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $placementEntranceExamResultEntry['PlacementEntranceExamResultEntry']['id']))); ?>
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
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Placement Entrance Exam Result Entry'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Accepted Students'), array('controller' => 'accepted_students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Accepted Student'), array('controller' => 'accepted_students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Placement Round Participants'), array('controller' => 'placement_round_participants', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Placement Round Participant'), array('controller' => 'placement_round_participants', 'action' => 'add')); ?> </li>
	</ul>
</div>
