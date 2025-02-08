<?= $this->Html->script('amharictyping'); ?>
<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Change Student Name By Court Decision' . (isset($this->data['StudentNameHistory']) ? ' : ' . $this->data['StudentNameHistory']['from_first_name'] . ' ' .  $this->data['StudentNameHistory']['from_middle_name'] . ' ' .  $this->data['StudentNameHistory']['from_last_name'] : ''); ?></span>
		</div>

		<?php
		if (!empty($this->data['StudentNameHistory'])) { ?>
			<a class="close-reveal-modal">&#215;</a>
			<?php
		} ?>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div class="student form">

					<div style="margin-top: -30px;">
						<hr>
						<blockquote>
							<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
							<p style="text-align:justify;" class="fs16 text-black">This tool will help you to change student name legally. <strong class="rejected">Use this form when there is legal change of name or name change by court decision.</strong></p> 
						</blockquote>
						<hr>
					</div>

					<div onclick="toggleViewFullId('ListPublishedCourse')"><?php 
						if (isset($this->data['StudentNameHistory']) &&  !empty($this->data['StudentNameHistory'])) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg'));?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span><?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span><?php
						} ?>
					</div>

					<div id="ListPublishedCourse" style="display:<?= (isset($this->data['StudentNameHistory']) ? 'none' : 'display'); ?>">
						<?= $this->Form->create('Student'); ?>
						<fieldset style="padding-bottom: 5px;">
							<legend>&nbsp;&nbsp; Student Number / ID &nbsp;&nbsp;</legend>
							<div class="row">
								<div class="large-4 columns">
									<?= $this->Form->input('Student.studentnumber', array('label' => false, 'placeholder' => 'Type Student ID...',  'maxlength' => 25)); ?>
								</div>
							</div>
						</fieldset>
						<?= $this->Form->Submit('Search', array('name' => 'searchStudentName', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
						<?= $this->Form->end(); ?>
					</div>

					<?php
					if (!empty($this->data['StudentNameHistory'])) { ?>

						<?= $this->Form->create('Student'); ?>

						<?= $this->Form->hidden('StudentNameHistory.student_id', array('label' => false)); ?>

						<div class="row">
							<div class="large-6 columns">
								<fieldset style="padding-bottom: 10px;">
									<legend>&nbsp;&nbsp; Student's Previous Name (From) &nbsp;&nbsp;</legend>
									<div class="row">
										<div class="large-12 columns">
											<?= $this->Form->input('StudentNameHistory.from_first_name', array('label' => 'First Name: ', 'disabled')); ?>
											<?= $this->Form->hidden('StudentNameHistory.from_first_name', array('label' => false)); ?>
										</div>
									</div>
									<div class="row">
										<div class="large-12 columns">
											<?= $this->Form->input('StudentNameHistory.from_middle_name', array('label' => 'Middle Name: ', 'disabled')); ?>
											<?= $this->Form->hidden('StudentNameHistory.from_middle_name', array('label' => false)); ?>
										</div>
									</div>
									<div class="row">
										<div class="large-12 columns">
											<?= $this->Form->input('StudentNameHistory.from_last_name', array('label' => 'Last Name: ', 'disabled')); ?>
											<?= $this->Form->hidden('StudentNameHistory.from_last_name', array('label' => false)); ?>
										</div>
									</div>
									<div class="row">
										<div class="large-12 columns">
											<?= $this->Form->input('StudentNameHistory.from_amharic_first_name', array('id' => 'AmharicText', 'label' => 'First Name: (Amharic)', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);", 'disabled')); ?>
											<?= $this->Form->hidden('StudentNameHistory.from_amharic_first_name', array('label' => false)); ?>
										</div>
									</div>
									<div class="row">
										<div class="large-12 columns">
											<?= $this->Form->input('StudentNameHistory.from_amharic_middle_name', array('id' => 'AmharicText', 'label' => 'Middle Name: (Amharic)', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);", 'disabled')); ?>
											<?= $this->Form->hidden('StudentNameHistory.from_amharic_middle_name', array('label' => false)); ?>
										</div>
									</div>
									<div class="row">
										<div class="large-12 columns">
											<?= $this->Form->input('StudentNameHistory.from_amharic_last_name', array('id' => 'AmharicText', 'label' => 'Last Name: (Amharic)', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);", 'disabled')); ?>
											<?= $this->Form->hidden('StudentNameHistory.from_amharic_last_name', array('label' => false)); ?>
										</div>
									</div>
								</fieldset>
							</div>

							<div class="large-6 columns">
								<fieldset style="padding-bottom: 10px;">
									<legend>&nbsp;&nbsp; Student's New Name (To) &nbsp;&nbsp;</legend>
									<div class="row">
										<div class="large-12 columns">
											<?= $this->Form->input('StudentNameHistory.to_first_name', array('label' => 'First Name: ')); ?>
										</div>
									</div>
									<div class="row">
										<div class="large-12 columns">
											<?= $this->Form->input('StudentNameHistory.to_middle_name', array('label' => 'Middle Name: ')); ?>
										</div>
									</div>
									<div class="row">
										<div class="large-12 columns">
											<?= $this->Form->input('StudentNameHistory.to_last_name', array('label' => 'Last Name: ')); ?>
										</div>
									</div>
									<div class="row">
										<div class="large-12 columns">
											<?= $this->Form->input('StudentNameHistory.to_amharic_first_name', array('id' => 'AmharicText', 'label' => 'First Name: (Amharic)', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);")); ?>
										</div>
									</div>
									<div class="row">
										<div class="large-12 columns">
											<?= $this->Form->input('StudentNameHistory.to_amharic_middle_name', array('id' => 'AmharicText', 'label' => 'Middle Name: (Amharic)', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);")); ?>
										</div>
									</div>
									<div class="row">
										<div class="large-12 columns">
											<?= $this->Form->input('StudentNameHistory.to_amharic_last_name', array('id' => 'AmharicText', 'label' => 'Last Name: (Amharic)', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);")); ?>
										</div>
									</div>
								</fieldset>
							</div>
						</div>

						<div class="row">
							<div class="large-4 columns">
								<?= $this->Form->input('StudentNameHistory.minute_number', array('label' => 'Minute Number: (Ref No) ', 'required' => 'required')); ?>
							</div>
						</div>
						
						<hr>
						<?= $this->Form->submit('Change Name', array('name' => 'changeName', 'div' => 'false', 'class' => 'tiny radius button bg-blue')); ?>
						<?= $this->Form->end(); ?>
						<?php
					} ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type='text/javascript'>
	function toggleViewFullId(id) {
		if($('#'+id).css("display") == 'none') {
			$('#'+id+'Img').attr("src", '/img/minus2.gif');
			$('#'+id+'Txt').empty();
			$('#'+id+'Txt').append(' Hide Filter');
		} else {
			$('#'+id+'Img').attr("src", '/img/plus2.gif');
			$('#'+id+'Txt').empty();
			$('#'+id+'Txt').append(' Display Filter');
		}
		$('#'+id).toggle("slow");
	}
</script>
