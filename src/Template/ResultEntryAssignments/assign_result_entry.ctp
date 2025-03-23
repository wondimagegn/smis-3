<script>
	$(document).ready(function () {
		$("#PublishedCourse").change(function () {
			//serialize form data
			window.location.replace("/resultEntryAssignments/assign_result_entry/" + $("#PublishedCourse").val());
		});
	});
</script>
<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Assign Grade Entry for Instructor '); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<?= $this->Form->create('ResultEntryAssignment', array('novalidate' => true)); ?>

				<?= $this->element('publish_course_filter_by_dept'); ?>
				<?php
				if (!empty($students_no_entry)) { ?>
					
					<hr>

					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>After you create an assingment for the instructor, the system will make the student available for exam result entry and grade submission to the instructor curentlly assigned to the selected course the student is taking.</div>
					
					<table class="fs14" cellpadding="0" cellspacing="0" class='table'>
						<tr>
							<td style="width:25%;" class="center">Minute Number:</td>
							<td colspan="3">
								<div class="large-8 columns">
									<br>
									<?= $this->Form->input('ResultEntryAssignment.minute_number', array('style' => 'width: 90%;', 'label' => false, 'class' => 'fs14', 'value' => 'Instructor Grade Entry', 'readonly' => 'true', 'required')); ?>
									<?= $this->Form->hidden('ResultEntryAssignment.minute_number', array('value' => 'Inst. Grade Entry')); ?>
								</div>
							</td>
						</tr>
					</table>

					<br>

					<h6 class="fs14 text-gray">Select the students for whom you want to assign instructor grade entry</h6>
					<br>

					<div style="overflow-x:auto;">
						<table id="StudentListEntry" cellpadding="0" cellspacing="0" class="fs14 table">
							<thead>
								<tr>
									<th class="center" style="width: 3%;"><?= $this->Form->input('select_all', array('type' => 'checkbox', 'id' => 'select-all', 'div' => false, 'label' => false, 'onchange' => 'check_uncheck(this.id)')); ?></th>
									<th class="vcenter" style="width: 25%;">Full Name</th>
									<th class="center" style="width: 7%;">Sex</th>
									<th class="center" style="width: 15%;">Student ID</th>
									<th class="center">Section</th>
									<th class="center">&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$st_count = 0;
								$isAllMakeUpApplied = count($students_no_entry);
								foreach ($students_no_entry as $key => $student) {
									$st_count++; ?>
									<tr>
										<td class="center">
											<?= $this->Form->input('ResultEntryAssignment.' . $st_count . '.gp', array('type' => 'checkbox', 'label' => false, 'class' => 'checkbox1', 'checked' => false, 'id' => 'StudentSelection' . $st_count)); ?>
											<?= $this->Form->input('ResultEntryAssignment.' . $st_count . '.student_id', array('type' => 'hidden', 'value' => $student['Student']['id'])); ?>
										</td>
										<td class="vcenter"><?= $student['Student']['full_name']; ?></td>
										<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($student['Student']['gender']), 'female') == 0 ?'F' : trim($student['Student']['gender']))); ?></td>
										<td class="center"><?= $student['Student']['studentnumber']; ?></td>
										<td class="center"><?= $selectedPublishedCourseDetail['Section']['name'] . '(' . $selectedPublishedCourseDetail['YearLevel']['name'] . ')'; ?></td>
										<td class="center">
											<?= ((empty($student['result']) && isset($student['makeupalreadyapplied']) && !empty($student['makeupalreadyapplied'])) ? $this->Html->link(__('Delete'), array('action' => 'deleteExamResultEntryAssignment', $student['makeupalreadyapplied']), null, sprintf(__('Are you sure you want to delete %s \'s grade entry assignment ?'), $student['full_name'])) : ''); ?>
										</td>
									</tr>
									<?php
								} ?>
							</tbody>
						</table>
					</div>
					<hr>

					<h6 class="fs14 text-gray"><?= "Available students:" . $isAllMakeUpApplied; ?></h6>
					<br>

					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="fs14 table">
							<tbody>
								<?php
								if (isset($sectionsHaveSameCourses) && !empty($sectionsHaveSameCourses)) { ?>
									<tr>
										<td style="width:25%" class="vcenter">In which section students are going to take exam?</td>
										<td style="width:75%" class="vcenter"><br><?= $this->Form->input('ResultEntryAssignment.exam_published_course_id', array('id' => 'ExamPublishedCourse', 'label' => false, 'required', 'style' => 'width: 98%;', 'type' => 'select', 'class' => 'fs14', 'options' => $sectionsHaveSameCourses)); ?></td>
									</tr>
									<tr>
										<td colspan="2" class="vcenter">
											<?= $this->Html->link('Assign Students as add courses', '#', array('data-animation' => "fade", 'data-reveal-id' => 'myModal', 'data-reveal-ajax' => '/resultEntryAssignments/get_student_to_add_course/' . $published_course_combo_id)); ?>
										</td>
									<tr>
									<?php 
								} ?>
							</tbody>
						</table>
					</div>

					<hr>
					<?= ($isAllMakeUpApplied != 0) ? $this->Form->submit(__('Assign Grade Entry'), array('name' => 'assignGradeEntry', 'div' => false, 'class' => 'tiny radius button bg-blue')) : ''; ?>
					<?php
				} ?>

				<?= $this->Form->end(); ?>

			</div>
		</div>
	</div>
</div>