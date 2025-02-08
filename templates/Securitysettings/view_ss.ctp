<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-lock-filled" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Site Security Settings'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="overflow-x:auto;">
					<table cellspacing="0" cellpading="0" class="table fs13">
						<tbody>
							<tr>
								<td style="width:25%"><?= __('Minimum Password Length'); ?></td>
								<td style="width:75%"><?= $securitysetting['Securitysetting']['minimum_password_length']; ?></td>
							</tr>
							<tr>
								<td><?= __('Maximum Password Length'); ?></td>
								<td><?= $securitysetting['Securitysetting']['maximum_password_length']; ?></td>
							</tr>
							<tr>
								<td><?= __('Password Strength'); ?></td>
								<td><?= (($securitysetting['Securitysetting']['password_strength'] == 1) ? 'Password should contain Uppercase Letters, Lowercase Letters, and Numbers.' : 'Password should contain Uppercase Letters, Lowercase Letters, Numbers and Symbols.'); ?></td>
							</tr>
							<tr>
								<td><?= __('Password Duration'); ?></td>
								<td><?= $securitysetting['Securitysetting']['password_duration']; ?> Days</td>
							</tr>
							<tr>
								<td><?= __('Allow Previously Used Password'); ?></td>
								<td><?= ($securitysetting['Securitysetting']['previous_password_use_allowance'] == 1 ? 'Yes' : 'No'); ?></td>
							</tr>
							<tr>
								<td><?= __('Session Duration'); ?></td>
								<td><?= $securitysetting['Securitysetting']['session_duration']; ?> Minues</td>
							</tr>
							<tr>
								<td><?= __('Maximum Login Attempt Limit'); ?></td>
								<td><?= $securitysetting['Securitysetting']['number_of_login_attempt']; ?> times</td>
							</tr>
							<tr>
								<td><?= __('Time to Wait after Maximum Login Attempt'); ?></td>
								<td><?= $securitysetting['Securitysetting']['attempt_period']; ?> Minues</td>
							</tr>
							<tr>
								<td><?= __('Last Updated'); ?></td>
								<td><?= $this->Time->format("M j, Y g:i A", $securitysetting['Securitysetting']['modified'], NULL, NULL); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<hr>
				<?= $this->Html->link('Change Site Security Setting', array('controller' => 'securitysettings', 'action' => 'edit'), array('style' => 'font-weight:bold', 'class' => 'tiny radius button bg-blue')); ?>
			</div>
		</div>
	</div>
</div>