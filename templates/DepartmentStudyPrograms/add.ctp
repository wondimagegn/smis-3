<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Add Department Study Program for a Department'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<?= $this->Form->create('DepartmentStudyProgram', array('data-abide', 'onSubmit' => 'return checkForm(this);')); ?>
			<div class="large-6 columns">
				<?php
				echo $this->Form->input('department_id', array('empty' => '[ Select Department ]', 'required', 'style' => 'width:90%'));
				echo $this->Form->input('study_program_id', array('id' => 'StudyProgramID', 'class' => 'custom-select', 'empty' => '[ Select Study Program ]', 'required', 'style' => 'width:90%'));
				?>
			</div>
			<div class="large-6 columns">
                <?php
                echo $this->Form->input('program_modality_id', array('empty' => '[ Select Program Modality ]', 'required', 'style' => 'width:90%'));
                echo $this->Form->input('qualification_id', array('empty' => '[ Select Qualification ]', 'required', 'style' => 'width:90%'));
                echo $this->Form->input('academic_year', array('label' => 'From Academic Year', 'empty' => '[ Select Academic Year ]', 'default' => '2022/23', 'style' => 'width:90%', 'options' => $academic_year));
				echo '<br>' . $this->Form->input('apply_for_current_students', array('type'=>'checkbox', 'checked' =>'checked')); 
				echo '<br>';
                ?>
			</div>
			<div class="large-12 columns">
				<hr>
				<?= $this->Form->end(array('label' => 'Add Department Study Program', 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue')); ?>
			</div>
		</div>
	</div>
</div>

<script>

    $(function() {
		$("#StudyProgramID").customselect();
	});

	var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Adding Department Study Program, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Adding Department Study Program...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>