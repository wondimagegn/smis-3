<?= $this->Html->script('amharictyping'); ?>
<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Correct Name: ' . $studentDetail['Student']['full_name'] . '  (' .  $studentDetail['Student']['studentnumber'] . ')'; ?></span>
		</div>

		<a class="close-reveal-modal">&#215;</a>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div class="student form">

					<?= $this->Form->create('Student'); ?>

					<div style="margin-top: -30px;">
						<hr>
						<blockquote>
							<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
							<p style="text-align:justify;" class="fs16 text-black">This tool will help you to correct student name. Use this utility <b class="rejected">to correct minor spelling errors in students' name.</b> <br> <strong>For legal name change, Please use "Change Name By Court Decision" functionality.</strong></p> 
						</blockquote>
						<hr>
					</div>

					<?= $this->Form->hidden('Student.id', array('value' => $studentDetail['Student']['id'])); ?>

					<div class="row">
						<div class="large-6 columns">
							<fieldset style="padding-bottom: 10px;">
								<legend>&nbsp;&nbsp; Student's Name (English) &nbsp;&nbsp;</legend>
								<div class="row">
									<div class="large-12 columns">
										<?= $this->Form->input('Student.first_name', array('label' => 'First Name: ')); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-12 columns">
										<?= $this->Form->input('Student.middle_name', array('label' => 'Middle Name: ')); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-12 columns">
										<?= $this->Form->input('Student.last_name', array('label' => 'Last Name: ')); ?>
									</div>
								</div>
							</fieldset>
						</div>

						<div class="large-6 columns">
							<fieldset style="padding-bottom: 10px;">
								<legend>&nbsp;&nbsp; Student's Name (Amharic) &nbsp;&nbsp;</legend>
								<div class="row">
									<div class="large-12 columns">
										<?= $this->Form->input('Student.amharic_first_name', array('label' => 'First Name (Amharic): ')); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-12 columns">
										<?= $this->Form->input('Student.amharic_middle_name', array('label' => 'Middle Name (Amharic): ')); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-12 columns">
										<?= $this->Form->input('Student.amharic_last_name', array('label' => 'Last Name (Amharic): ')); ?>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
					<hr>
					<?= $this->Form->submit('Update Name', array('name' => 'correctName', 'div' => 'false', 'class' => 'tiny radius button bg-blue')); ?>
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>