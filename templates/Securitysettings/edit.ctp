<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-lock-filled" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Update Site Security Settings'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('Securitysetting'); ?>
				<?= $this->Form->input('id'); ?>
				<div style="overflow-x:auto;">
					<table cellspacing="0" cellpading="0" class="table fs13">
						<tbody>
							<tr>
								<td style="width:25%">Minimum Password Length:</td>
								<td style="width:75%"><?= $this->Form->input('minimum_password_length', array('label' => false, 'type' => 'number', 'min'=>'6',  'max'=>'10', 'step'=>'1', 'style' => 'width:10%;'/* 'options' => $min_password_length, 'style' => 'width:100px' */)); ?></td>
							</tr>
							<tr>
								<td>Maximum Password Length</td>
								<td><?= $this->Form->input('maximum_password_length', array('label' => false, 'type' => 'number', 'min'=>'15',  'max'=>'20', 'step'=>'1', 'style' => 'width:10%;' /* 'options' => $max_password_length, 'style' => 'width:100px' */)); ?></td>
							</tr>
							<tr>
								<td>Password Strength</td>
								<td><?= $this->Form->input('password_strength', array('label' => false, 'options' => $password_strength, 'style' => 'width:80%')); ?></td>
							</tr>
							<tr>
								<td>Password Duration: (Days)</td>
								<td><?= $this->Form->input('password_duration', array('label' => false, 'type' => 'number', 'min'=>'30',  'max'=>'240', 'step'=>'30', 'style' => 'width:10%;' /* 'options' => $password_duration, 'style' => 'width:100px' */)); ?></td>
							</tr>
							<tr>
								<td>Allow Previously Used Password:</td>
								<td><?= $this->Form->input('previous_password_use_allowance', array('label' => false)); ?></td>
							</tr>
							<tr>
								<td>Session Duration: (Minutes)</td>
								<td><?= $this->Form->input('session_duration', array('label' => false, 'type' => 'number', 'min'=>'60',  'max'=>'240', 'step'=>'30', 'style' => 'width:10%;' /* 'options' => $session_duration, 'style' => 'width:100px' */)); ?></td>
							</tr>
							<tr>
								<td>Maximum Login Attempt Limit: (Times)</td>
								<td><?= $this->Form->input('number_of_login_attempt', array('label' => false, 'type' => 'number', 'min'=>'5',  'max'=>'10',  'step'=>'1', 'style' => 'width:10%;')); ?></td>
							</tr>
							<tr>
								<td>Time to Wait after Maximum Login Attempt: (Minutes)</td>
								<td> <?= $this->Form->input('attempt_period', array('label' => false, 'type' => 'number', 'min'=>'0',  'max'=>'30', 'step'=>'5', 'style' => 'width:10%;')); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<hr>
				<?= $this->Form->end(array('label' => 'Update Security Setting', 'class' => 'tiny radius button bg-blue')); ?>
			</div>
		</div>
	</div>
</div>