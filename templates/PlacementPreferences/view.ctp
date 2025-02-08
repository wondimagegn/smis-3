<div class="placementPreferences view">
<h2><?php echo __('Placement Preference'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($placementPreference['PlacementPreference']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Accepted Student'); ?></dt>
		<dd>
			<?php echo $this->Html->link($placementPreference['AcceptedStudent']['id'], array('controller' => 'accepted_students', 'action' => 'view', $placementPreference['AcceptedStudent']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Student'); ?></dt>
		<dd>
			<?php echo $this->Html->link($placementPreference['Student']['id'], array('controller' => 'students', 'action' => 'view', $placementPreference['Student']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Placement Round Participant'); ?></dt>
		<dd>
			<?php echo $this->Html->link($placementPreference['PlacementRoundParticipant']['name'], array('controller' => 'placement_round_participants', 'action' => 'view', $placementPreference['PlacementRoundParticipant']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Academic Year'); ?></dt>
		<dd>
			<?php echo h($placementPreference['PlacementPreference']['academic_year']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Round'); ?></dt>
		<dd>
			<?php echo h($placementPreference['PlacementPreference']['round']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Preference Order'); ?></dt>
		<dd>
			<?php echo h($placementPreference['PlacementPreference']['preference_order']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User'); ?></dt>
		<dd>
			<?php echo $this->Html->link($placementPreference['User']['id'], array('controller' => 'users', 'action' => 'view', $placementPreference['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Edited By'); ?></dt>
		<dd>
			<?php echo h($placementPreference['PlacementPreference']['edited_by']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($placementPreference['PlacementPreference']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($placementPreference['PlacementPreference']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Placement Preference'), array('action' => 'edit', $placementPreference['PlacementPreference']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Placement Preference'), array('action' => 'delete', $placementPreference['PlacementPreference']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $placementPreference['PlacementPreference']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Placement Preferences'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Placement Preference'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Accepted Students'), array('controller' => 'accepted_students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Accepted Student'), array('controller' => 'accepted_students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Placement Round Participants'), array('controller' => 'placement_round_participants', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Placement Round Participant'), array('controller' => 'placement_round_participants', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>
