<div class="onlineApplicantStatuses view">
<h2><?php echo __('Online Applicant Status'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($onlineApplicantStatus['OnlineApplicantStatus']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Online Applicant'); ?></dt>
		<dd>
			<?php echo $this->Html->link($onlineApplicantStatus['OnlineApplicant']['id'], array('controller' => 'online_applicants', 'action' => 'view', $onlineApplicantStatus['OnlineApplicant']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Status'); ?></dt>
		<dd>
			<?php echo h($onlineApplicantStatus['OnlineApplicantStatus']['status']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Remark'); ?></dt>
		<dd>
			<?php echo h($onlineApplicantStatus['OnlineApplicantStatus']['remark']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($onlineApplicantStatus['OnlineApplicantStatus']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($onlineApplicantStatus['OnlineApplicantStatus']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Online Applicant Status'), array('action' => 'edit', $onlineApplicantStatus['OnlineApplicantStatus']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Online Applicant Status'), array('action' => 'delete', $onlineApplicantStatus['OnlineApplicantStatus']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $onlineApplicantStatus['OnlineApplicantStatus']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Online Applicant Statuses'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Online Applicant Status'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Online Applicants'), array('controller' => 'online_applicants', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Online Applicant'), array('controller' => 'online_applicants', 'action' => 'add')); ?> </li>
	</ul>
</div>
