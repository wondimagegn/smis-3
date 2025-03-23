<div class="placementRoundParticipants view">
<h2><?php echo __('Placement Round Participant'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($placementRoundParticipant['PlacementRoundParticipant']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo h($placementRoundParticipant['PlacementRoundParticipant']['type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Foreign Key'); ?></dt>
		<dd>
			<?php echo h($placementRoundParticipant['PlacementRoundParticipant']['foreign_key']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Academic Year'); ?></dt>
		<dd>
			<?php echo h($placementRoundParticipant['PlacementRoundParticipant']['academic_year']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Placement Round'); ?></dt>
		<dd>
			<?php echo h($placementRoundParticipant['PlacementRoundParticipant']['placement_round']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($placementRoundParticipant['PlacementRoundParticipant']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($placementRoundParticipant['PlacementRoundParticipant']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Placement Round Participant'), array('action' => 'edit', $placementRoundParticipant['PlacementRoundParticipant']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Placement Round Participant'), array('action' => 'delete', $placementRoundParticipant['PlacementRoundParticipant']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $placementRoundParticipant['PlacementRoundParticipant']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Placement Round Participants'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Placement Round Participant'), array('action' => 'add')); ?> </li>
	</ul>
</div>
