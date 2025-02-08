<div class="large-offset-4 large-4 columns">
	<div class="box bg-white-transparent">
		<!-- <div class="profile">
			<img src="/img/amu.png" style="width:110px;height:110px;">
			<h3>SMiS<small>2.0</small></h3>
		</div> -->
		<div class="box-body" style="display: block;">
			<div class="row">
				<div class="large-12 columns">
					
					<div class="row">
						<div class="edumix-signup-panel">
							<h6> Forgot Password?</h6>
							<?php
							$flash_message = $this->Session->flash();
							if (!empty($flash_message)) {
								echo $flash_message;
							}
							?>
							
							<?= $this->Form->create('User', array('action' => 'forget')); ?>
							<div class="row collapse">
								<div class="small-2  columns">
									<span class="prefix bg-blue"><i class="text-white fontello-at-circled tooltipstered"></i></span>
								</div>
								<div class="small-10  columns">
									<?= $this->Form->input('email', array('size' => '40', 'placeholder' => 'Email', 'class' => 'username', 'label' => false, 'autocomplete' => "off")); ?>
								</div>
							</div>

							
							<div class="row collapse">
								<div class="small-8  columns">
									Please enter the sum of <?= ($mathCaptcha); ?>
								</div>
								<div class="small-4 columns">
									<?= $this->Form->input('security_code', array('label' => false, 'autocomplete' => "off"));  ?>
								</div>
							</div>
							
							<p> <?= $this->Html->link(__('Back to Login', true), array('controller' => 'users', 'action' => 'login'), array('class' => 'forgot-button')); ?> </p>
							
							<div class="error-box error-message">
								<p style="size:8; text-align:justify">
									If you do not get the email from SMiS in inbox after submitting this form with success message,
									Please check Spam and Junk email folders in your email provider before using this form again. <br><br>
									The link in the email is only valid for 30 minutes.
								</p>
							</div>

							<div class="login-button">
								<?= $this->Form->submit(__('Reset Password', true), array('class' => ' radius button bg-blue')); ?>
							</div>

							<?= ($this->Form->end()); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>