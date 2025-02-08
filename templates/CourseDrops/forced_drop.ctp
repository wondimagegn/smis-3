<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Forced Drop'; ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('CourseDrop'); ?>

				<div class="courseDrops form">
					<div style="margin-top: -30px;"><hr></div>
					<blockquote>
						<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<span style="text-align:justify;" class="fs15 text-gray">This tool will help you to perform forced drop for selected academic year and semester. <b style="text-decoration: underline;"><i>Only students who have been registred on hold bases will be displayed</i></b>.</span>
					</blockquote>
					<hr>

					<div onclick="toggleViewFullId('ListPublishedCourse')">
						<?php
						if (!isset($student_lists)) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Display Filter</span>
							<?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
							<?php
						} ?>
					</div>

					<div id="ListPublishedCourse" style="display:<?= (isset($student_lists) ? 'none' : 'display'); ?>">
						<fieldset style="padding-bottom: 0px;padding-top: 15px;">
							<legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('Student.academicyear', array('label' => 'Academic Year: ', 'style' => 'width:90%;', 'empty' => '[ All Applicable ACY ]',  'default' =>  (isset($this->request->data['Student']['academicyear']) ? $this->request->data['Student']['academicyear'] : (isset($defaultacademicyear) ? $defaultacademicyear : '')) , 'options' => $acyear_array_data)); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Student.semester', array('label' => 'Semester: ', 'style' => 'width:90%;', 'empty' => '[ All Semesters ]', 'options' => Configure::read('semesters'))); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Student.program_id', array('label' => 'Program: ', 'style' => 'width:90%;',  'id' => 'program_id_1', 'empty' => '[ All Programs ]', 'options' => $programs)); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Student.program_type_id', array('label' => 'Program Type: ', 'style' => 'width:90%;', 'empty' => '[ All Program Types ]', 'options' => $programTypes)); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-6 columns">
									<?php
									if (isset($departments) && !empty($departments)) {
										echo $this->Form->input('Student.department_id', array('label' => 'Department: ', 'style' => 'width:90%;', 'empty' => '[ All Applicable Departments ]', 'required' => false,  'id' => 'department_id_1', 'default' => (isset($default_department_id) && !empty($default_department_id) ? $default_department_id : '')));
									} else if (isset($colleges) && !empty($colleges)) {
										echo $this->Form->input('Student.college_id', array('label' => 'College: ', 'style' => 'width:90%;', 'empty' => ' All Applicable Colleges ', 'required' => false,  /* 'onchange' => 'getDepartment(1)', */ 'id' => 'college_id', 'default' => (isset($default_college_id) && !empty($default_college_id) ? $default_college_id : '')));
									} ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Student.studentnumber', array('label' => 'Student ID:', 'placeholder' => 'Student ID to filter ..', 'required' => false, 'default' => $studentnumber, 'style' => 'width:90%;')); ?>
								</div>
								<div class="large-3 columns">
									&nbsp;
								</div>
							</div>
							<hr>
							<?= $this->Form->submit(__('Search'), array('name' => 'continue', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
						</fieldset>
					</div>
					<hr>

					<?php
					if (!isset($no_display) && isset($student_lists) && !empty($student_lists)) { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>List of students who have registered on hold bases and system found out the student(s)  is/are not qualified to proceed with his/thier course. You need to drop the courses the student(s) registered.</div>
						<br>
						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<th class="center" style="width: 3%;">#</th>
										<th class="vcenter" style="width: 20%;">Full Name</th>
										<th class="center" style="width: 5%;">Sex</th>
										<th class="center" style="width: 10%;">Student ID</th>
										<th class="center">Program</th>
										<th class="center">Program Type</th>
										<th class="vcenter">Department</th>
										<th class="center">&nbsp;</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 1;
									foreach ($student_lists as $index => $student) { ?>
										<tr>
											<td class="center"><?= $count++; ?></td>
											<td class="vcenter"><?= $student['Student']['full_name']; ?></td>
											<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : $student['Student']['gender'])); ?></td>
											<td class="center"><?= $student['Student']['studentnumber']; ?></td>
											<td class="center"><?= $student['Student']['Program']['name']; ?></td>
											<td class="center"><?= $student['Student']['ProgramType']['name']; ?></td>
											<td class="vcenter"><?= (!empty($student['Student']['Department']['name']) ? $student['Student']['Department']['name'] : ($student['Student']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Pre/Freshman')); ?></td>
											<td class="center"><?= $this->Html->link(__('Drop Course'), array('action' => 'add', $student['Student']['id'])); ?></td>
										</tr>
										<?php 
									} ?>
								</tbody>
							</table>
						</div>
						<br>
						<?php
					} ?>
				</div>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script type='text/javascript'>
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