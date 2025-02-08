<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Add Students to Section : ' //. (!empty($section_detail['Section']['name']) ? $section_detail['Section']['name'] . ' ('. $section_detail['YearLevel']['name'] .', '. $section_detail['Section']['academicyear'] .')' : ''); ?></span>
		</div>

		<a class="close-reveal-modal">&#215;</a>
	</div>
	<div class="row">
		<div class="large-12 columns">
		<div style="margin-top: -10px;"></div>
		<hr>
		<?php
		if (isset($students) && !empty($students)) { ?>
			<?= $this->Form->create('Section', array('action' => 'mass_student_section_add', "method" => "POST", 'onSubmit' => 'return checkForm(this);')); ?>
			<?= $this->Form->hidden('SectionDetail.section_id', array('value' => $section_detail['Section']['id'])); ?>
			
			<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
			<br>

			<h6 class="fs14 text-gray">Select students to add</h6>

			<div style="overflow-x:auto;">
				<table  cellpadding="0" cellspacing="0" class="table">
					<thead>
						<tr>
							<td colspan=6 class="smallheading" style="border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 0.5px;">
								<h6 class="text-gray">
								<?php
								if (!empty($section_detail['Department']['name'])) {
									echo  __('' . $section_detail['Section']['name'] . ' ('. $section_detail['YearLevel']['name'] .', '. $section_detail['Section']['academicyear'] .') <br style="line-height: 0.5;"> <span class="fs13">' . $section_detail['Department']['name'] . ' &nbsp;&nbsp; | &nbsp;&nbsp; '.  $section_detail['Program']['name'] . ' &nbsp;&nbsp; | &nbsp;&nbsp; ' . $section_detail['ProgramType']['name'] . '</span>');
								} else if (empty($section_detail['Department']['name'])) {
									echo  __('' . $section_detail['Section']['name'] . '(' . $section_detail['Program']['name'] . '-' . $section_detail['ProgramType']['name'] . ')');
								} ?>
								</h6>
							</td>
						</tr>
						<tr>
							<th class="center" style="width: 5%;">#</th>
							<th class="center" style="width: 5%;"><?= '' . $this->Form->checkbox("SelectAll", array('id' => 'select-all', 'checked' => '', 'lebel'=> '')); ?>&nbsp;</th>
							<th class="vcenter" style="width: 25%;">Student Name</th>
							<th class="center" style="width: 10%;">Sex</th>
							<th class="center" style="width: 20%;">Student ID</th>
							<th class="center">Department</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$count = 1;
						//debug($students[0]);
						if (isset($students) && !empty($students)) {
							foreach ($students as $k => $student) { ?>
								<tr>
									<td class="center"><?= $count++; ?></td>
									<td class="center">
										<?php
										if ($student['Student']['graduated'] == 0) { ?>
											<div style="margin-left: 15%;"><?= $this->Form->checkbox('Section.' . $count . '.selected_id', array('class' => 'checkbox1')); ?></div>
											<?= $this->Form->hidden('Section.' . $count . '.student_id',array('value' => $student['Student']['id'])); ?>
											<?= $this->Form->hidden('Section.' . $count . '.section_id',array('value' => $section_detail['Section']['id'])); ?>
											<?php
										} else {
											echo '**';
										} ?>
									</td>
									<td class="vcenter"><?= $student['Student']['full_name']; ?></td>
									<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' :(strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : $student['Student']['gender'])); ?></td>
									<td class="center"><?= $student['Student']['studentnumber']; ?></td>
									<td class="center"><?= (!empty($student['Department']['name']) ? $student['Department']['name'] : (isset($student['College']['shortname']) ? 'Pre/Freshman (' . $student['College']['shortname'] .')' : 'Pre/Freshman')); ?></td>
								</tr> 
								<?php
							}
						} ?>
					</tbody>
				</table>
			</div>
			<hr>

			<?php
			if (!empty($students)) { ?>
				<?= $this->Form->Submit('Add to Section', array('name' => 'submit', 'id' => 'SubmitID', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
				<?= $this->Form->end(); ?>
				<?php
			}
		}  else { ?>
			<div class="large-12 columns">
				<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No student is found who attended classes in the last <?= ACY_BACK_FOR_SECTION_LESS; ?> academic years and section less for <?= $section_detail['Section']['academicyear']; ?>  to add to <?= $section_detail['Section']['name'] . ' ('. (isset($section_detail['YearLevel']['name']) && !empty($section_detail['YearLevel']['name']) ? $section_detail['YearLevel']['name'] : ($section_detail['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) .', '. $section_detail['Section']['academicyear'] .')'; ?> section. <?= isset($section_detail['YearLevel']['name']) && !empty($section_detail['YearLevel']['name']) ? 'Either the section\'s curriculum ' . (!empty($section_detail['Curriculum']['name']) ?  '(' . $section_detail['Curriculum']['name'] . '-'. $section_detail['Curriculum']['year_introduced'] . ')' : '') . ' is different from the available sectionless students curriculum or all students are assigned to a section or there are no recently admitted students who are attached to a curriculum in your department' : ' Either all students are assigned to a section or there are no recently admitted students in your college that need section assignment.' ?></div>
			</div>
			<?php
		} ?>

		</div>
	</div>
</div>

<script type="text/javascript">

	var form_being_submitted = false;

	var sectionName = "<?= $section_detail['Section']['name'];?>";

	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	var checkForm = function(form) {
		var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

		//alert(checkedOne);
		if (!checkedOne) {
			alert('At least one student must be selected to add to ' + sectionName + ' Section.');
			validationMessageNonSelected.innerHTML = 'At least one student must be selected to add to ' + sectionName + ' Section.';
			return false;
		}

		if (form_being_submitted) {
			alert('Adding Selected Students to ' + sectionName + ' Section, please wait a moment...');
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Adding Selected to ' + sectionName + ' Section...';
		form_being_submitted = true;
		return true;
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>