<div class="placementEntranceExamResultEntries view">
<h2><?php echo __('Placement Entrance Exam Result Entry'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($placementEntranceExamResultEntry['PlacementEntranceExamResultEntry']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Accepted Student'); ?></dt>
		<dd>
			<?php echo $this->Html->link($placementEntranceExamResultEntry['AcceptedStudent']['id'], array('controller' => 'accepted_students', 'action' => 'view', $placementEntranceExamResultEntry['AcceptedStudent']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Student'); ?></dt>
		<dd>
			<?php echo $this->Html->link($placementEntranceExamResultEntry['Student']['id'], array('controller' => 'students', 'action' => 'view', $placementEntranceExamResultEntry['Student']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Result'); ?></dt>
		<dd>
			<?php echo h($placementEntranceExamResultEntry['PlacementEntranceExamResultEntry']['result']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Placement Round Participant'); ?></dt>
		<dd>
			<?php echo $this->Html->link($placementEntranceExamResultEntry['PlacementRoundParticipant']['name'], array('controller' => 'placement_round_participants', 'action' => 'view', $placementEntranceExamResultEntry['PlacementRoundParticipant']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($placementEntranceExamResultEntry['PlacementEntranceExamResultEntry']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($placementEntranceExamResultEntry['PlacementEntranceExamResultEntry']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Placement Entrance Exam Result Entry'), array('action' => 'edit', $placementEntranceExamResultEntry['PlacementEntranceExamResultEntry']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Placement Entrance Exam Result Entry'), array('action' => 'delete', $placementEntranceExamResultEntry['PlacementEntranceExamResultEntry']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $placementEntranceExamResultEntry['PlacementEntranceExamResultEntry']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Placement Entrance Exam Result Entries'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Placement Entrance Exam Result Entry'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Accepted Students'), array('controller' => 'accepted_students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Accepted Student'), array('controller' => 'accepted_students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Placement Round Participants'), array('controller' => 'placement_round_participants', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Placement Round Participant'), array('controller' => 'placement_round_participants', 'action' => 'add')); ?> </li>
	</ul>
</div>
