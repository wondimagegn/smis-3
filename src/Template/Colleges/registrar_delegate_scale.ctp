<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Delegate Grade Scale'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('College');
				if (empty($this->request->data)) { ?>
					<div style="margin-top: -20px;">
						<hr>
						<?= $this->Form->input('Search.college_id', array('label' => ' Select College: ', 'type' => 'select', 'options' => $colleges));?>
						<hr>
						<?= $this->Form->Submit('Continue', array('name' => 'continue', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
					</div>
				<?php
				} ?>

				<?php
				if (!empty($this->request->data)) { ?>
					<div style="margin-top: -20px;">
						<div style='padding:15px;font-size:16px'>
							<strong>Delegatation of Grade Scale setting will be applied for the department listed below.</strong>
						</div>
						<div style='padding:15px; font-size:15px'>
							<strong>Campus: &nbsp;&nbsp; <?= $this->request->data['Campus']['name'] ?></strong><br />
							<strong>College: &nbsp;&nbsp; <?= $this->request->data['College']['name'] ?></strong><br />
						</div>

						<div class="large-6 columns">
							<table cellpadding="0" cellspacing="0" class="table-borderless">
								<tbody>
									<?php
									echo $this->Form->hidden('id', array('value' => $this->request->data['College']['id']));
									if (!empty($this->request->data)) {
										foreach ($this->request->data['Department'] as $department_id => $department_name) {
											echo "<tr><td>" . $department_name['name'] . "</td></tr>";
										}
									} ?>
								</tbody>
							</table>
						</div>
						<div class="large-6 columns">
							<?php
							echo '<table cellpadding="0" cellspacing="0" class="table-borderless">';
							echo "<tr><td>" . $this->Form->input('deligate_scale', array('class' => 'fs16', 'label' => false)) . ' <label for="CollegeDeligateScale"> &nbsp; Deligate For Undergraduate Study </label></td></tr>';
							echo "<tr><td>" . $this->Form->input('deligate_for_graduate_study', array(/* 'after' => 'Delegate post graduate grade scale.', */'class' => 'fs16', /* 'label' => false */)) . "</td></tr>";
							echo '</table>';
							?>
							<hr>
							<?= $this->Form->Submit('Update', array('name' => 'update', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
						</div>
					</div>
					<?php
				} ?>
			</div>
		</div>
	</div>
</div>