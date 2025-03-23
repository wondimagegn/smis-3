<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Maintain Graduation Work of Student'); ?></span>
		</div>
	</div>
    <div class="box-body">
       <div class="row">
			<div class="large-12 columns">
	  			
			<div style="margin-top: -40px;"><hr></div>
            
				<?= $this->Form->create('GraduationWork', array('onSubmit' => 'return checkForm(this);')); ?>
				<?php 
				$graduation_work = Configure::read('Graduation.graduation_work');
				
				if (!isset($studentIDs)) { ?>
					<fieldset style="padding-bottom: 5px;">
						<legend>&nbsp;&nbsp; Student Number / ID &nbsp;&nbsp;</legend>
						<div class="row">
							<div class="large-4 columns">
								<?= $this->Form->input('Search.studentID', array('label' => false, 'placeholder' => 'Type Student ID...', 'required', 'maxlength' => 25)); ?>
							</div>
						</div>
					</fieldset>
					<?= $this->Form->Submit('Search', array('name' => 'continue', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
					<?php 
				}

				if (isset($studentIDs)) {
					$from = date('Y') - 5;
					$to = date('Y') + 1; ?>
					<table  cellpadding="0" cellspacing="0" class="table">
						<tr>
							<td style="width: 70%;">
								<?= $this->element('student_basic'); ?>
							</td>
							<td style="background-color: white;">
								<table  cellpadding="0" cellspacing="0" class="table">
									<tr>
										<td>Course: </td>
										<td class="vcenter">
											<?php
											if (!empty($this->request->data['GraduationWork']['id'])) {
												echo $this->Form->hidden('GraduationWork.id');
											}
											echo $this->Form->hidden('GraduationWork.student_id', array('value'=> $student_section_exam_status['StudentBasicInfo']['id'])); ?>
											<?= $this->Form->input('GraduationWork.course_id', array( 'style' => 'width: 70%', 'label' => false, 'options' => $courses)); ?>
										</td>	
									</tr>
									<tr>
										<td>Type: </td>
										<td class="vcenter"><?= $this->Form->input('GraduationWork.type', array('label' => false, 'style' => 'width: 70%', 'options' => $graduation_work, 'type' => 'select')); ?></td>
									</tr>
									<tr>
										<td colspan="2" class="vcenter"><?= $this->Form->input('GraduationWork.title', array('label' => 'Title: ', 'required' => 'required')); ?></td>
									</tr>
								</table>
								<hr>
								<?= $this->Form->Submit('Save Graduation Work', array('name' => 'saveGraduationWork', 'id' => 'saveGraduationWork', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
							</td>
						</tr>
					</table>
					<?php
				} ?>
				<?= $this->Form->end(); ?>
	  		</div>
		</div>
    </div>
</div>

<script>
	var form_being_submitted = false;

	var checkForm = function(form) {

		if (form_being_submitted) {
			alert("Saving Graduation Work of the student, please wait a moment...");
			form.saveGraduationWork.disabled = true;
			return false;
		}

		form.saveGraduationWork.value = 'Saving Graduation Work...';
		form_being_submitted = true;
		return true; 
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>
