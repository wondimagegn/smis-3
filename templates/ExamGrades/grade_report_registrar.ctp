<script>
	var number_of_students = <?= (isset($students_in_section) ? count($students_in_section) : 0); ?>;

	function check_uncheck(id) {
		var checked = ($('#' + id).attr("checked") == 'checked' ? true : false);
		for (i = 1; i <= number_of_students; i++) {
			$('#StudentSelection' + i).attr("checked", checked);
		}
	}

	$(document).ready(function() {
		$("#Section").change(function() {
			//serialize form data
			var s_id = $("#Section").val();
			window.location.replace("/exam_grades/<?= $this->request->action; ?>/" + s_id + "/" + $("#SemesterSelected").val());
		});
	});

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
			$('#' + id + 'Txt').append('Hide Filter');
		} else {
			$('#' + id + 'Img').attr("src", '/img/plus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append('Display Filter');
		}
		$('#' + id).toggle("slow");
	}
</script>

<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Student Examination Grade Report'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div class="examGrades <?= $this->request->action; ?>">
					<?= $this->Form->create('ExamGrade'); ?>
					<div style="margin-top: -30px;">
						<hr>
						<div onclick="toggleViewFullId('ListSection')">
							<?php
							if (!empty($sections)) {
								echo $this->Html->image('plus2.gif', array('id' => 'ListSectionImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Display Filter</span>
								<?php
							} else {
								echo $this->Html->image('minus2.gif', array('id' => 'ListSectionImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Hide Filter</span>
								<?php
							} ?>
						</div>
						<div id="ListSection" style="display:<?= (!empty($sections) ? 'none' : 'display'); ?>">
							<fieldset style="padding-bottom: 5px;padding-top: 5px;">
								<legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
								<div class="row">
									<div class="large-3 columns">
										<?= $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => 'Academic Year: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('semester', array('id' => 'Semester', 'type' => 'select', 'label' => 'Semester: ', 'style' => 'width:90%', 'options' => Configure::read('semesters'), 'default' => (isset($semester_selected) ? $semester_selected : false)));
										if (isset($semester_selected)) {
											echo $this->Form->input('semester_selected', array('id' => 'SemesterSelected', 'type' => 'hidden', 'value' => $semester_selected));
										} ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('program_id', array('id' => 'Program', 'label' => 'Program: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $programs, 'default' => (isset($program_id) ? $program_id : false))); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('program_type_id', array('id' => 'ProgramType', 'label' => 'Program Type: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $program_types, 'default' => (isset($program_type_id) ? $program_type_id : false))); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-6 columns">
										<?php
										if (!empty($departments)) { ?>
											<?= $this->Form->input('department_id', array('label' => 'Department: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $departments, 'default' => (isset($department_id) ? $department_id : false)));
										} else if (!empty($colleges)) { ?>
											<?= $this->Form->input('college_id', array('label' => 'College: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $colleges, 'default' => (isset($college_id) ? $college_id : false)));
										} ?>
									</div>
									<div class="large-6 columns">
									</div>
								</div>
							</fieldset>
							<?= $this->Form->submit(__('Get Sections'), array('name' => 'listSections', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
						</div>
						<hr>
					</div>

					<?php
					if (!empty($sections)) { ?>
						<table class="fs14" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:25%;" class="center">Sections</td>
								<td colspan="3" style="width:75%">
									<br>
									<div class="row">
										<div class="large-6 columns">
											<?= $this->Form->input('section_id', array('style' => 'width:90%', 'id' => 'Section', 'label' => false, 'type' => 'select', 'options' => $sections, 'default' => (isset($section_id) ? $section_id : false))); ?>
										</div>
									</div>
								</td>
							</tr>
						</table>
						<?php
					}

					if (isset($students_in_section) && empty($students_in_section)) { ?>
						<div id="flashMessage" class="info-box info-message"><span style='margin-right: 15px;'></span>There is no student in the selected section</div>
						<?php
					} else if (isset($students_in_section) && !empty($students_in_section)) { ?>
 						<br>
						<blockquote>
							<!-- <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6> -->
							<span style="text-align:justify;" class="fs14 text-gray">Please select student/s for whom you want to prepare student examination grade report. The report will <b style="text-decoration: underline;"><i>only be displayed for students with course registration </i></b>for the selected academic year and semster.</span>
						</blockquote>

						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<th class="center" style="width:5%"><?= $this->Form->input('select_all', array('type' => 'checkbox', 'id' => 'select-all','label' => false)); ?><!-- <br><label for="select-all">All</label> --></th>
										<th class="vcenter" style="width:30%">Student Name</th>
										<th class="center" style="width: 10%">Sex</th>
										<th class="vcenter" style="width:30%">Student ID</th>
										<th style="width:25%"></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$st_count = 0;
									foreach ($students_in_section as $key => $student) {
										$st_count++; ?>
										<tr>
											<td class="center">
												<?= $this->Form->input('Student.' . $st_count . '.gp', array('type' => 'checkbox', 'class' => 'checkbox1', 'label' => false, 'id' => 'StudentSelection' . $st_count)); ?>
												<?= $this->Form->input('Student.' . $st_count . '.student_id', array('type' => 'hidden', 'value' => $student['Student']['id'])); ?>
											</td>
											<td class="vcenter" ><?= $student['Student']['full_name']; ?></td>
											<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : $student['Student']['gender'])); ?></td>
											<td class="vcenter" ><?= $student['Student']['studentnumber']; ?></td>
											<td></td>
										</tr>
										<?php
									} ?>
								</tbody>
							</table>
						</div>
						<hr>
						<?= $this->Form->submit(__('Get Grade Report'), array('name' => 'getGradeReport', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
						<?php
					} ?>
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>