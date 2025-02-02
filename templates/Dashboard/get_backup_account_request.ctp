<?php
//BACKUP
if (isset($latest_backups) && !empty($latest_backups)) { 
	if (!empty($latest_backups)) { ?>
		<table>
			<?php
			foreach ($latest_backups as $backup) { ?>
				<tr>
					<td style="width:65%"><?= $this->Time->format("M j, Y g:i:s A", $backup['Backup']['created'], NULL, NULL); ?></td>
					<td style="width:35%; text-align:center"><?= (!$backup['Backup']['file_exists'] ? 'Not Available' : $this->Html->link(__('Download', true), array('controller' => 'backups', 'action' => 'index', $backup['Backup']['id']))); ?></td>
				</tr>
				<?php
			} ?>
		</table>
		<?php
	} else { ?>
		<p>There is no backup. Please make sure that you configured the system to generate database backup regularly.</p>
		<?php
	}
	?>
	<div class="utils">
		<?= $this->Html->link(__('View More', true), array('controller' => 'backups', 'action' => 'index'), array('class' => '')); ?>
	</div>
	<?php
} ?>

<?php
//Admin Account Confirmation Requests
if (isset($password_reset_confirmation_request) || isset($admin_cancelation_confirmation_request) || isset($confirmed_tasks) || isset($admin_assignment_confirmation_request) || isset($role_change_confirmation_request) || isset($deactivation_confirmation_request) || isset($activation_confirmation_request)) {
	if (!($password_reset_confirmation_request > 0 || $admin_cancelation_confirmation_request > 0 || count($confirmed_tasks) > 0 || $admin_assignment_confirmation_request > 0 || $role_change_confirmation_request > 0 || $deactivation_confirmation_request > 0 || $activation_confirmation_request > 0)) { ?>
		<p style="padding-top:20px; padding-bottom:30px">There is no confirmation request.</p>
		<?php
	} else { ?>
		<table class="tableOnDashborad">
			<?php
			if ($password_reset_confirmation_request > 0) { ?>
				<tr>
					<td>You have <?= $password_reset_confirmation_request; ?> password reset confirmation request. <?= $this->Html->link(__('View', true), array('action' => 'task_confirmation', 'controller' => 'users')); ?></td>
				</tr>
				<?php
			}
			if ($admin_cancelation_confirmation_request > 0) { ?>
				<tr>
					<td>You have <?= $admin_cancelation_confirmation_request; ?> administrator cancellation confirmation request. <?= $this->Html->link(__('View', true), array('action' => 'task_confirmation', 'controller' => 'users')); ?></td>
				</tr>
				<?php
			}
			if ($admin_assignment_confirmation_request > 0) { ?>
				<tr>
					<td>You have <?= $admin_assignment_confirmation_request; ?> administrator assignment confirmation request. <?= $this->Html->link(__('View', true), array('action' => 'task_confirmation', 'controller' => 'users')); ?></td>
				</tr>
				<?php
			}
			if (count($confirmed_tasks) > 0) { ?>
				<tr>
					<td>There are <?= count($confirmed_tasks); ?> tasks which are done by other system administrators. <?= $this->Html->link(__('View', true), array('action' => 'task_confirmation', 'controller' => 'users')); ?></td>
				</tr>
				<?php
			}
			if ($role_change_confirmation_request > 0) { ?>
				<tr>
					<td>You have <?= $role_change_confirmation_request; ?> role change request. <?= $this->Html->link(__('View', true), array('action' => 'task_confirmation', 'controller' => 'users')); ?></td>
				</tr>
				<?php
			}
			if ($deactivation_confirmation_request > 0) { ?>
				<tr>
					<td>You have <?= $deactivation_confirmation_request; ?> user account deactivation request. <?= $this->Html->link(__('View', true), array('action' => 'task_confirmation', 'controller' => 'users')); ?></td>
				</tr>
				<?php
			}
			if ($activation_confirmation_request > 0) { ?>
				<tr>
					<td>You have <?= $activation_confirmation_request; ?> user account activation request. <?= $this->Html->link(__('View', true), array('action' => 'task_confirmation', 'controller' => 'users')); ?></td>
				</tr>
				<?php
			} ?>
		</table>
		<?php
	}
} ?>