<div class="officialRequestStatuses view">
<h2><?php echo __('Official Request Status'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($officialRequestStatus['OfficialRequestStatus']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Official Transcript Request'); ?></dt>
		<dd>
			<?php echo $this->Html->link($officialRequestStatus['OfficialTranscriptRequest']['id'], array('controller' => 'official_transcript_requests', 'action' => 'view', $officialRequestStatus['OfficialTranscriptRequest']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Status'); ?></dt>
		<dd>
			<?php echo h($officialRequestStatus['OfficialRequestStatus']['status']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($officialRequestStatus['OfficialRequestStatus']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($officialRequestStatus['OfficialRequestStatus']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Official Request Status'), array('action' => 'edit', $officialRequestStatus['OfficialRequestStatus']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Official Request Status'), array('action' => 'delete', $officialRequestStatus['OfficialRequestStatus']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $officialRequestStatus['OfficialRequestStatus']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Official Request Statuses'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Official Request Status'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Official Transcript Requests'), array('controller' => 'official_transcript_requests', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Official Transcript Request'), array('controller' => 'official_transcript_requests', 'action' => 'add')); ?> </li>
	</ul>
</div>
