<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div class="senateLists form">
					<?= $this->Form->create('SenateList'); ?>
					<fieldset>
						<legend><?= __('Edit Senate List'); ?></legend>
						<?php
						echo $this->Form->input('id');
						echo $this->Form->input('student_id');
						echo $this->Form->input('minute_number');
						echo $this->Form->input('approved_date');
						?>
					</fieldset>
					<?= $this->Form->end(array('label' => __('Submit'), 'class' => 'tiny radius button bg-blue')); ?>
				</div>
			</div>
		</div>
	</div>
</div>