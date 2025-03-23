<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Check Graduate'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;">
					<hr>
					<?php
					$flash_message = $this->Session->flash();
					if (!empty($flash_message)) {
						echo $flash_message;
					} ?>

					<h6 class="text-gray">This is an interface for employer and any stakeholder who would like to check graduates of our university. <br> Forgery protection is one of our main value! </h6>
					<hr>
					<div class="row">
						<?= $this->Form->Create('Page', array('data-abide', 'onSubmit' => 'return checkForm(this);')); ?>
						<br>
						<div class="large-4 columns">
							<?= $this->Form->input('studentID', array('id' => 'studentID', 'size' => '40', 'placeholder' => 'Student ID', 'class' => 'username', 'label' => false, 'value' => "", 'required', 'value' => isset($studentID)? $studentID : '')); ?>
						</div>
						<?php
						if (!isset($students['GraduateList'])) { ?>
							<div class="large-4 columns">
								<div class="row collapse">
									<div class="small-8  columns">
										Please enter the sum of <?= ($mathCaptcha); ?>
									</div>
									<div class="small-4 columns">
										<?= $this->Form->input('security_code', array('label' => false, 'autocomplete' => 'off', 'id' => "securityCode"));  ?>
									</div>
								</div>
							</div>
							<?php
						} 

						if (isset($mathCaptcha)) {
							echo $this->Form->hidden('mathCaptcha', array('value' => 1));
						} ?>
						
						<div class="large-12 columns">
							<hr>
							<?= $this->Form->Submit(__('Check', true), array('class' => 'tiny radius button bg-blue btn-primary', 'name' => 'continue', 'id' => 'continue', 'div' => false)); ?>
						</div>
						<?= $this->Form->end(); ?>
					</div>
				</div>

				<?php
				if (isset($students['GraduateList']) && !empty($students['GraduateList'])) { ?>
					<hr>
					<div class="large-12 columns">
						<?php
						if (!empty($students['GraduateList']) && !empty($students['Student'])) {
							echo $this->element('student_graduation_check');
						} else if (empty($students['GraduateList']) && !empty($students['Student'])) {
							echo $this->element('student_graduation_check');
						} ?>
						<br>
					</div>
					<hr>
					<div class="large-12 columns">
						<?php
						if (!empty($students['Student'])) {
							echo '<p style="text-align: justify;"><strong>Note: </strong> If you need student official copy, please send us your company details to our email <a href="email:our@amu.edu.et">our@amu.edu.et</a>. It is going to take 2-4 business days to verify your request and send the student official copy to your company address. </p>';
						} ?>
					</div>
					<?php
				} ?>
			</div>
		</div>
	</div>
</div>

<script>

	var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {

		if (form.studentID.value == '') {
			form.studentID.focus();
			return false;
		}

		if (form.securityCode.value == '') {
			form.securityCode.focus();
			return false;
		}

		if (form_being_submitted) {
			alert("Processing your request, please wait a moment...");
			form.continue.disabled = true;
			return false;
		}

		form.continue.value = 'Checking...';
		form_being_submitted = true;
		return true;
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>