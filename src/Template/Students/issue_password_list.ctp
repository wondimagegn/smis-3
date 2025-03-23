<script>

	var number_of_students = <?= (isset($students_in_section) ? count($students_in_section) : 0); ?>;

	function check_uncheck(id) {
		var checked = ($('#' + id).attr("checked") == 'checked' ? true : false);
		for (i = 1; i <= number_of_students; i++) {
			$('#StudentSelection' + i).attr("checked", checked);
		}
	}

	$(document).ready(function () {

		$("#Section").change(function () {
			var s_id = $("#Section").val();
			if (s_id != '' || s_id != 0) {
				window.location.replace("/students/<?= $this->request->action; ?>/" + s_id);
			}
		});

		//$("#select-all").val(0);

		$('input[type="checkbox"]').each(function(){
			$(this).prop('checked', false);
		});

		/* $("#listSections").click(function(){
			alert('Im clicked');
			window.location.replace("/students/<?= $this->request->action; ?>/");
		}); */
		
	});

	/* $('input[type="submit"]').click(function(){
		$('input[type="checkbox"]').each(function(){
			$(this).prop('checked', false);
		});
	}); */

	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none') {
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		} else {
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		}
		$('#c' + obj.id).toggle("slow");
	}

	function toggleViewFullId(id) {
		if ($('#' + id).css("display") == 'none') {
			$('#' + id + 'Img').attr("src", '/img/minus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append(' Hide Filter');
		} else {
			$('#' + id + 'Img').attr("src", '/img/plus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append(' Display Filter');
		}
		$('#' + id).toggle("slow");
	}
</script>

<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Issue/Reset Student Password'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div class="examGrades <?= $this->request->action; ?>">
					<div style="margin-top: -30px;">
						<hr>

						<?= $this->Form->create('Student'/* , array('onSubmit' => 'return checkForm(this);') */); ?>

						<div onclick="toggleViewFullId('ListSection')">
							<?php
							if (!empty($sections)) {
								echo $this->Html->image('plus2.gif', array('id' => 'ListSectionImg'));
								?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt"> &nbsp; Display Filter</span><?php
							} else {
								echo $this->Html->image('minus2.gif', array('id' => 'ListSectionImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt"> &nbsp; Hide Filter</span>
								<?php
							} ?>
						</div>

						<div id="ListSection" style="display:<?= (!empty($sections) ? 'none' : 'display'); ?>">
							<fieldset style="padding-bottom: 0px;padding-top: 15px;">
								<div class="row">
									<div class="large-3 columns">
										<?= $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => 'Program: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programs, 'default' => (isset($program_id) ? $program_id : false))); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => 'Program Type: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $program_types, 'default' => (isset($program_type_id) ? $program_type_id : false))); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('year_level_id', array('id' => 'YearLevelId', 'label' => 'Year Level: ', 'style' => 'width:90%;', 'class' => 'fs14', 'type' => 'select', 'options' => $yearLevels)); ?>
									</div>
									<div class="large-3 columns">
										<?php 
										if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE)  { ?>
											<td style="width:20%"><?= $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => 'Admission Year: ', 'style' => 'width:90%;',  'class' => 'fs14', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
											<?php
										} else { ?>
											<?= $this->Form->input('name', array('id' => 'name', 'style' => 'width:90%;',  'class' => 'fs14', 'label' => 'Name/Student ID:')); ?>
											<?php
										} ?>
									</div>
								</div>
								<div class="row">
									<div class="large-3 columns">
										<?= $this->Form->input('common_password', array('placeholder' => 'leave empty for random password', 'id' => 'commonPassword', 'style' => 'width:90%;',  'class' => 'fs14', 'label' => 'Common Password: ', 'maxLength' => 8)); ?>
									</div>
									<?php 
									if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE)  { ?>
										<div class="large-3 columns">
											<?= $this->Form->input('name', array('id' => 'name', 'style' => 'width:90%;',  'class' => 'fs14', 'label' => 'Name/Student ID:')); ?>
										</div>
										<?php
									} ?>
									<div class="large-3 columns">
										<?= $this->Form->input('single_page', array('label' => 'Multiple Students on Single Page: ', 'style' => 'width:90%;', 'type' => 'select', 'div' => false, 'default' => 'yes', 'options' => array('no' => 'No', 'yes' => 'Yes'))); ?>
									</div>
									<?php 
									if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE)  { ?>
										<div class="large-6 columns">
											&nbsp;
										</div>
										<?php
									} else { ?>
										<div class="large-6 columns">
											&nbsp;
										</div>
										<?php
									} ?>
								</div>
								<hr>
								<?= $this->Form->submit(__('Get Sections'), array('name' => 'listSections', 'id' => 'listSections', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
							</fieldset>
						</div>
					</div>
					<hr>

					<?php
					if (!empty($sections)) { ?>
						<table class="fs14" cellpadding="0" cellspacing="0" class='table'>
							<tr>
								<td style="width:25%;" class="center">Sections</td>
								<td colspan="3">
									<div class="large-8 columns">
									<br>
									<?= $this->Form->input('section_id', array('style' => 'width: 90%;', 'class' => 'fs14', 'id' => 'Section', 'label' => false, 'type' => 'select', 'options' => $sections, 'default' => (isset($section_id) ? $section_id : false))); ?>
									</div>
								</td>
							</tr>
						</table>
						<?php
					}

					if (isset($students_in_section) && empty($students_in_section)) { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no student in the selected section</div>
						<?php
					} else if (isset($students_in_section) && !empty($students_in_section)) { ?>

						<br>
						<h6 class="fs13 text-gray">Please select student/s for whom you want to issue/reset password. The password should be changed during first login by student.</h6>
						<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
						<br>

						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<th style="width:3%" class="center"><?= $this->Form->input('select_all', array('type' => 'checkbox', 'id' => 'select-all', 'div' => false, 'label' => false)); ?></th>
										<th style="width:2%" class="center">#</th>
										<th style="width:35%" class="vcenter">Student Name</th>
										<th style="width:10%" class="center">Sex</th>
										<th style="width:20%" class="center">Student ID</th>
										<th style="width:30%">&nbsp;</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$st_count = 0;
									foreach ($students_in_section as $key => $student) {
										$st_count++; ?>
										<tr>
											<td class="center">
												<?= $this->Form->input('Student.' . $st_count . '.gp', array('type' => 'checkbox', 'label' => false, 'id' => 'StudentSelection' . $st_count, 'class' => 'checkbox1')); ?>
												<?= $this->Form->input('Student.' . $st_count . '.student_id', array('type' => 'hidden', 'value' => $student['Student']['id'])); ?>
											</td>
											<td class="center"><?= $st_count; ?></td>
											<td class="vcenter"><?= $student['Student']['full_name']; ?></td>
											<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : $student['Student']['gender'])); ?></td>
											<td class="center"><?= $student['Student']['studentnumber']; ?></td>
											<td>&nbsp;</td>
										</tr>
										<?php
									} ?>
								</tbody>
							</table>
						</div>
						<hr>
						<?= $this->Form->submit(__('Get Student Password'), array('name' => 'issueStudentPassword', 'id' => 'issueStudentPassword', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
						<?php
					} ?>

					<?= $this->Form->end(); ?>

				</div>
			</div>
		</div>
	</div>
</div>

<script>
	
	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	$(document).ready(function() {
        $('#issueStudentPassword').click(function() {
			var checkboxes = document.querySelectorAll('input[type="checkbox"]');
			var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

			if (!checkedOne) {
				alert('At least one student must be selected to issue or reset password.');
				validationMessageNonSelected.innerHTML = 'At least one student must be selected to issue or reset password.';
				return false;
			}

        });
    });

    /* var form_being_submitted = false;

	var checkForm = function(form) {

        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

		//alert(checkedOne);
		if (!checkedOne) {
            alert('At least one student must be selected to issue or reset password.');
			validationMessageNonSelected.innerHTML = 'At least one student must be selected to issue or reset password.';
			return false;
		}
	
		if (form_being_submitted) {
			alert("Issuing/Reseting Password, please wait a moment...");
			form.issueStudentPassword.disabled = true;
			return false;
		}

		form.issueStudentPassword.value = 'Issuing/Reseting Password...';
		form_being_submitted = true;
		return true;
	}; */

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>