<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Apply Readmission On Behalf Student '); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div class="senateLists form">
					<?= $this->Form->create('Readmission'); ?>
					<blockquote>
						<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<p style="text-align:justify;">
							<span class="fs15 text-black">
								The system will display all students in the selected criteria without restriction and gives you information for your decision making.
								You are responsible for wrong readmission application on behalf of the selected students so make sure the readmission appliction for the right student who needs readmission application.
							</span>
						</p>
					</blockquote>
					<hr>

					<div onclick="toggleViewFullId('ListPublishedCourse')">
						<?php
						if (!empty($students_for_readmission_list)) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg'));
						?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
						<?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg'));
						?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
						<?php
						} ?>
					</div>

					<div id="ListPublishedCourse" style="display:<?= (!empty($students_for_readmission_list) ? 'none' : 'display'); ?>">
						<fieldset style="padding-bottom: 5px;padding-top: 5px;">
							<legend>&nbsp;&nbsp; Search / Filter &nbsp;&nbsp;</legend>
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => 'Program: ', 'style' => 'width:80%', 'type' => 'select', 'options' => $programs, 'default' => $default_program_id)); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => 'Program Type:', 'style' => 'width:80%', 'type' => 'select', 'options' => $program_types, 'default' => $default_program_type_id)); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('academic_year', array('id' => 'AcadamicYear', 'label' => 'Readmission AC Year: ', 'class' => 'fs14', 'style' => 'width:80%', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'label' => 'Readmission Semester: ', 'style' => 'width:80%', 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => (isset($semester_selected) ? $semester_selected : false))); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-6 columns">
									<?= $this->Form->input('department_id', array('id' => 'DepartmentID', 'class' => 'fs14', 'label' => 'Department: ', 'type' => 'select', 'style' => 'width:90%', 'options' => $departments, 'default' => $default_department_id)); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('name', array('id' => 'Name', 'class' => 'fs14', 'label' => 'Student Name:', 'style' => 'width:80%')); ?>
								</div>
								<div class="large-3 columns">
								</div>
							</div>
						</fieldset>
						<?= $this->Form->submit(__('List Students'), array('name' => 'listStudentsForReadmission', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
						<hr>
					</div>
					

					<?php
					if (!empty($students_for_readmission_list)) {
						$count = 1;
						foreach ($students_for_readmission_list as $c_id => $students) { ?>
							<br>
							<table cellpadding="0" cellspacing="0" class="table">
								<tr>
									<td>
										Department: &nbsp;&nbsp;
										<?php
										if (!empty($students[0]['Department']['name'])) {
											echo $students[0]['Department']['name'];
										} else {
											echo "Pre/Department Non Assigned";
										} ?>
									</td>
								</tr>
								<tr>
									<td>Program:  &nbsp;&nbsp; <?= $students[0]['Program']['name']; ?></td>
								</tr>
								<tr>
									<td>Program Type:  &nbsp;&nbsp; <?= $students[0]['ProgramType']['name']; ?></td>
								</tr>
								<tr>
									<td>
										Curriculum:  &nbsp;&nbsp;
										<?php
										if (!empty($students[0]['Curriculum']['name'])) {
											echo $students[0]['Curriculum']['name'];
										} else {
											echo "Pre/Department Non Assigned";
										} ?>
									</td>
								</tr>
							</table>
							<br>

							<table class="student_list table" cellpadding="0" cellspacing="0">
								<thead>
									<tr>
										<th style="width:5%" class="center"></th>
										<th style="width:5%" class="center"></th>
										<th style="width:5%" class="center">#</th>
										<th style="width:45%">Student Name</th>
										<th style="width:15%" class="center">Student ID</th>
										<th style="width:5%" class="center">Sex</th>
										<th style="width:10%" class="center">CGPA</th>
										<th style="width:10%" class="center">MCGPA</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$s_count = 1;
									foreach ($students as $key => $student) {
										if ($key == 0)
											continue; ?>
										<tr style="color:<?= (empty($student['criteria']['error']) ? 'green' : 'red'); ?>">
											<td style="background-color:white" class="center">
												<?php
												echo $this->Form->input('Student.' . $count . '.id', array('type' => 'hidden', 'value' => $student['Student']['id']));
												echo $this->Form->input('Student.' . $count . '.include_readmission', array('type' => 'checkbox', 'label' => false));
												?>
											</td>
											<?php
											if (isset($student['criteria']['error']) && !empty($student['criteria']['error'])) { ?>
												<td style="background-color:white" class="center" onclick="toggleView(this)" id="<?= $count; ?>"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $count, 'div' => false, 'align' => 'left')); ?>?</td>
												<?php
											} else { ?>
												<td style="background-color:white">&nbsp;</td>
												<?php
											} ?>
											<td style="background-color:white" class="center"><?= $s_count++; ?></td>
											<td style="background-color:white"><?= $this->Html->link(__($student['Student']['full_name']), array('controller' => 'students', 'action' => 'view', $student['Student']['id']), array('target' => '_blank', 'style' =>  'font-weight:normal; color:' . (empty($student['criteria']['error']) ? 'green' : 'red'))); ?></td>
											<td style="background-color:white" class="center"><?= $student['Student']['studentnumber']; ?></td>
											<td style="background-color:white" class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' : 'F'); ?></td>
											<td style="background-color:white" class="center"><?= (isset($student['cgpa']) ? $student['cgpa'] : ''); ?></td>
											<td style="background-color:white" class="center"><?= (isset($student['mcgpa']) ? $student['mcgpa'] : ''); ?></td>
										</tr>
										<?php
										if (isset($student['criteria']['error']) && !empty($student['criteria']['error'])) { ?>
											<tr id="c<?= $count; ?>" style="display:none">
												<td colspan="3" style="background-color:#f0f0f0"> </td>
												<td colspan="5" style="background-color:#f0f0f0">
													<?= $student['criteria']['error']; ?>
												</td>
											</tr>
											<?php
										}
										$count++;
									} ?>
								</tbody>
							</table>
							<?php
						} ?>
						<hr>
						<?= $this->Form->submit(__('Add Student to Readmission List'), array('name' => 'addStudentToReadmissionList', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
						<?php
					} else if (isset($this->request->data) && empty($students_for_readmission_list)) { ?>
						<div class='info-box info-message'><span style='margin-right: 15px;'></span>The system unable to find list of students who need readmission application for selected criteria.</div>
						<?php
					} ?>
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none')
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		else
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
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