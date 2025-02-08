<div id="sign-up">
	<h4 class="color-blue-logo heading">Login To SMiS</h4>
	<hr />
	<?php
		$flash_message = $this->Session->flash();
		if (!empty($flash_message)) {
			echo $flash_message;
		}
	?>

	<?= ($this->Form->create('User', array('action' => 'login'))); ?>
	<br/>

	<div class="row collapse">
		<div class="small-2  columns" style="margin-bottom: 10px;">
			<span class="prefix bg-green"><i class="text-white fontello-user tooltipstered"></i></span>
		</div>
		<div class="small-10  columns" style="margin-bottom: 10px;">
		<?= ($this->Form->input('username', array('placeholder' => 'Username', 'label' => false, 'autocomplete' => "off", 'id' => 'Text1', 'required'))); ?>
		</div>

		<div class="small-2  columns" style="margin-bottom: 10px;">
			<span class="prefix bg-green"><i class="text-white icon-lock tooltipstered"></i></span>
		</div>
		<div class="small-10  columns" style="margin-bottom: 10px;">
			<?= ($this->Form->input('password', array('label' => false, 'autocomplete' => "off", 'placeholder' => 'Password', 'type' => 'password', 'id' => "Text2", 'required'))); ?>
		</div>
	</div>

	<div class="row" style="margin-bottom: 20px;">
		<div class="large-6  columns">
			<?= ($this->Form->Submit('Login', array('div' => false, 'class' => "tiny radius button bg-blue"))); ?>
		</div>
	</div>

	<div class="row">
		<div class="large-6  columns">
			<?= $this->Html->link(__('Forgot Password?', true), array('action' => 'forget'), array('class' => 'tiny radius button secondary')); ?>
		</div>
	</div>

	<?php
	if (isset($mathCaptcha)) { ?>
		<div class="info-box message">
			<div class="row collapse">
				<div class="small-6  columns" style="padding-top: 10px;">
					<span>Enter the sum of: <?= ($mathCaptcha); ?></span>
				</div>
				<div class="small-4 columns">
					<?= $this->Form->input('security_code', array('label' => false, 'div' => false, 'style' => 'width:100px;')); ?>
				</div>
				<div class="small-2 columns">
					<?= ($this->Form->Submit('Enter', array('div' => false, 'class' => 'tiny radius button bg-blue'))); ?>
				</div>
			</div>
		</div>
		<?php
	} ?>
	<?= ($this->Form->end()); ?>
</div>