<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-vcard" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Move Student to New Section '; ?></span>
		</div>

		<a class="close-reveal-modal">&#215;</a>
	</div>
	<div class="row">
		<div class="large-12 columns">
			<div style="margin-top: -10px;"><hr></div>
			<?=  $this->Form->create('Section', array('controller' => 'sections', 'action' => 'section_move_update', 'method' => 'post')); ?>
			<fieldset style="padding-bottom: 5px;padding-top: 5px;">
				<legend>&nbsp;&nbsp; <span class="fs14 text-gray"><?= $students['Student']['full_name'] . ' (' . $students['Student']['studentnumber'] . ')'; ?></span> &nbsp;&nbsp;</legend>
				<span class="fs14 text-gray">
					<?= $this->Form->hidden('Section.1.selected_id', array('value' => 1)); ?>
					<?= $this->Form->hidden('Section.1.student_id', array('value' => $students['Student']['id'])); ?>
					<!-- <strong>Name: </strong> <b><?php //echo $students['Student']['full_name']; ?></b><br />
					<strong>Student ID: </strong> <b><?php //echo $students['Student']['studentnumber']; ?></b><br />
					<strong>Sex: </strong> <b><?php //echo ucfirst(strtolower(trim($students['Student']['gender']))); ?></b><br /> -->
					<strong>Current Section: </strong> <b><?=__($previousSectionName['Section']['name'] . ' (' . (isset($previousSectionName['YearLevel']) && !empty($previousSectionName['YearLevel']['name']) ? $previousSectionName['YearLevel']['name'] . ', ' : ($students['Student']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial, ' : 'Pre/1st, ')) . $previousSectionName['Section']['academicyear'] . ')</b><br/>' . $previousSectionName['Program']['name'] . ' &nbsp; &nbsp; | &nbsp;&nbsp; ' . $previousSectionName['ProgramType']['name'] . ' &nbsp; &nbsp; | &nbsp;&nbsp; ' . (isset($previousSectionName['Department']) && !empty($previousSectionName['Department']['name']) ? $previousSectionName['Department']['name'] : $previousSectionName['College']['name'])); ?><br />
				</span>
				<hr>
				<div class="row">
					<div class="large-6 columns">
						<?= $this->Form->hidden('previous_section_id', array('value' => $previous_section_id)); ?>
						<?= $this->Form->input('Selected_section_id', array('label' => 'Select Target Section: ', 'id' => 'Selected_section_id', 'type' => 'select', 'required', 'style' => 'width: 80%', 'options' => $sections, 'empty' => "[ Select Section ]")); ?>
					</div>
				</div>
			</fieldset>
			<hr>
			<?= $this->Form->Submit(__('Move to Selected Section'), array('div' => false, 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue', 'name' => 'move_to_section')); ?>
			<?=  $this->Form->end(); ?>
		</div>
	</div>
</div>

<script type="text/javascript">

	var form_being_submitted = false;

	$('#SubmitID').click(function() {

		var newSectionSelection = $('#Selected_section_id').val();
		
		if (newSectionSelection == '') {
			$('#Selected_section_id').focus();
			$('#Selected_section_id').setAttribute('title', 'Please select target section to move the student');
			return false;
		}

		if (form_being_submitted) {
			alert('Moving to Selected Section, please wait a moment...');
			$('#SubmitID').attr('disabled', true);
			return false;
		}

		if (!form_being_submitted && newSectionSelection != '') {
			$('#SubmitID').val('Moving to Selected Section...');
			form_being_submitted = true;
			return true;
		} else {
			return false;
		}

	});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}

</script>