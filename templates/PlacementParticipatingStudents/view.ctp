<div class="placementParticipatingStudents view">
<h2><?php echo __('Placement Participating Student'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Accepted Student'); ?></dt>
		<dd>
			<?php echo $this->Html->link($placementParticipatingStudent['AcceptedStudent']['id'], array('controller' => 'accepted_students', 'action' => 'view', $placementParticipatingStudent['AcceptedStudent']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Student'); ?></dt>
		<dd>
			<?php echo $this->Html->link($placementParticipatingStudent['Student']['id'], array('controller' => 'students', 'action' => 'view', $placementParticipatingStudent['Student']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Program Id'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['program_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Program Type Id'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['program_type_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Original College Department'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['original_college_department']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Academic Year'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['academic_year']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Round'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['round']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Total Placement Weight'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['total_placement_weight']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Female Placement Weight'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['female_placement_weight']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Disability Weight'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['disability_weight']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Developing Region Weight'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['developing_region_weight']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Placement Round Participant Id'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['placement_round_participant_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Placement Type'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['placement_type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Status'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['status']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Remark'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['remark']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($placementParticipatingStudent['PlacementParticipatingStudent']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Placement Participating Student'), array('action' => 'edit', $placementParticipatingStudent['PlacementParticipatingStudent']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Placement Participating Student'), array('action' => 'delete', $placementParticipatingStudent['PlacementParticipatingStudent']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $placementParticipatingStudent['PlacementParticipatingStudent']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Placement Participating Students'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Placement Participating Student'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Accepted Students'), array('controller' => 'accepted_students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Accepted Student'), array('controller' => 'accepted_students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Placement Participating Programs'), array('controller' => 'placement_participating_programs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Placement Participating Program'), array('controller' => 'placement_participating_programs', 'action' => 'add')); ?> </li>
	</ul>
</div>
