<?= $this->Form->input('ExamType.edit', array('type' => 'hidden', 'value' => $edit));?>
<hr>
<?php
if(!empty($published_course_id)) {

	debug($enable_for_moodle);
	debug($ac_yearsForMoodle);

	$input_disable = ($grade_submitted ? "disabled" : false);
	if ($view_only && empty($exam_types)) { ?>
		<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Exam setup is not yet created by the assigned instructor for the published course you selected. If you want to manage the exam setup on belhalf of the instructor, the instructor account should be closed by the system administrator.</div>
		<?php
	} else if (!$grade_submitted) { ?>
		<h6 class="fs14 text-gray">Please enter all the exam types(assesments) for the course you selected with its weight in the given field, below.</h6>
		<hr>
		<?php
	} else { ?>
		<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Exam grade is submitted for the selected course and changes on the exam setup is disabled.</div>
		<?php
	}

	if(!$view_only || ($view_only && !empty($exam_types))) { ?>
		<div style="overflow-x:auto;">
			<table cellpadding="0" cellspacing="0" id="exam_setup" class="table">
				<thead>
					<tr>
						<th style="width:5%" class="center">#</th>
						<th style="width:25%" class="vcenter">Exam Type</th>
						<th style="width:20%" class= <?= empty($exam_types) && !$view_only ? "vcenter": "center"; ?>>Percent</th>
						<th style="width:20%" class= <?= empty($exam_types) && !$view_only ? "vcenter": "center"; ?>>Order</th>
						<th style="width:10%" class= <?= empty($exam_types) && !$view_only ? "vcenter": "center"; ?>>Mandatory</th>
						<th style="width:20%" class="center">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (empty($exam_types) && !$view_only) { ?>
						<tr id="ExamType_1">
							<td class="center">1</td>
							<td class="vcenter"><?= $this->Form->input('ExamType.1.exam_name', array('label' => false)); ?></td>
							<td class="center"><?= $this->Form->input('ExamType.1.percent', array('label' => false, 'style' => 'width:75px')); ?></td>
							<td class="center"><?= $this->Form->input('ExamType.1.order', array('type' => 'number', 'label' => false, 'min' => '1', 'max' => '10', 'step' => '1', 'style' => 'width:75px')); ?></td>
							<td class="center"><?= $this->Form->input('ExamType.1.mandatory', array('label' => false)); ?></td>
							<td class="center"><!-- <a href="javascript:deleteSpecificRow('ExamType_1')">Delete</a> --></td>
						</tr>
						<?php
					} else {
						$count = 0;
						foreach ($exam_types as $key => $exam_type) {
							if (!$grade_submitted && !$view_only) { ?>
								<tr id="ExamType_<?= ++$count; ?>">
									<td class="center"><?= $count; ?></td>
									<td class="center">
										<?= $this->Form->input('ExamType.'.$count.'.id', array('type' => 'hidden', 'value' => $exam_type['ExamType']['id']));?>
										<?= $this->Form->input('ExamType.'.$count.'.exam_name', array('value' => $exam_type['ExamType']['exam_name'], 'label' => false, 'disabled' => $input_disable)); ?>
									</td>
									<td class="center"><div style="padding-left: 30%;"><?= $this->Form->input('ExamType.'.$count.'.percent', array('value' => $exam_type['ExamType']['percent'], 'label' => false, 'style' => 'width:75px', 'disabled' => $input_disable));?></div></td>
									<td class="center"><div style="padding-left: 30%;"><?= $this->Form->input('ExamType.'.$count.'.order', array('type' => 'number', 'label' => false, 'min' => '1', 'max' => '10', 'step' => '1', 'value' => ($exam_type['ExamType']['order'] != 0 ? $exam_type['ExamType']['order'] : ''), 'style' => 'width:75px', 'disabled' => $input_disable));?></div></td>
									<td class="center">
										<div style="padding-left: 30%;">
											<?php
											$coptions = array();
											$coptions['value'] = 1;
											$coptions['label'] = false;
											$coptions['disabled'] = $input_disable;

											if($exam_type['ExamType']['mandatory'] == 1) {
												$coptions['checked'] = 'checked';
											}
											echo $this->Form->input('ExamType.'.$count.'.mandatory', $coptions); ?>
										</div>
									</td>
									<td class="center">
										<?php 
										if(!$grade_submitted) { ?>
											<a href="javascript:deleteSpecificRow('ExamType_<?= $count; ?>')">Delete</a>
											<?php 
										} ?>
									</td>
								</tr>
								<?php
							} else { ?>
								<tr>
									<td class="center"><?= ++$count; ?></td>
									<td class="vcenter"><?= $exam_type['ExamType']['exam_name']; ?></td>
									<td class="center"><?= $exam_type['ExamType']['percent'].'%'; ?></td>
									<td class="center"><?= $exam_type['ExamType']['order']; ?></td>
									<td class="center"><?= ($exam_type['ExamType']['mandatory'] == 1 ? 'Yes' : 'No'); ?></td>
									<td class="center">&nbsp;</td>
								</tr>
								<?php
							}
						}
						$count++;
					} ?>
				</tbody>
			</table>
		</div>
		<br>
		<?php
	}

	if (!$grade_submitted && !$view_only) { ?>
		<input type="button" value="Add Row" onclick="addRow('exam_setup', 'ExamType', 5, '<?= $all_exam_setup_detail; ?>')" />
		<hr>
		<?= $this->Form->submit(__('Submit Exam Setup'), array('div' => false,'class'=>'tiny radius button bg-blue')); ?>
		<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Important Note: If a student fail to take any of the mandatory exam/s, the system will automatically give NG to the student.</div>
		<?php
		if ($enable_for_moodle) { 

			$activate = ($published_course_department['PublishedCourse']['enable_for_moodle'] == 0 ? 'Enable Course for Moodle' : 'Disable Course on Moodle'); 
			$activateAction = ($published_course_department['PublishedCourse']['enable_for_moodle'] == 0 ? 'enable this course for moodle' : 'delete this course and existing course enrollments from Moodle');
			$buttonColor = ($published_course_department['PublishedCourse']['enable_for_moodle'] == 0 ? 'blue' : 'red'); ?>
			<hr>
			<div class="row">
				<div class="large-12 columns">
					<fieldset>
						<legend> &nbsp; &nbsp;  E-learning Integration Options: &nbsp; &nbsp; </legend>
						<br>
						<div class="large-3 columns">
							<?= $this->Form->postLink(__($activate), array('action' => 'enable_course_for_moodle', $published_course_department['PublishedCourse']['id']), array('class'=>'tiny radius button bg-' . $buttonColor . '', 'confirm' => __('Are you sure you want to ' . $activateAction . ' (%s (%s))? ' . ($published_course_department['PublishedCourse']['enable_for_moodle'] == 1 ? ' Disabling this course will also delete existing moodle course enrollments and data on ' . MOODLE_SITE_URL . '. You can only re-enable it before the grade for the course is is not submitted. Are you sure you want to deactivate the course and delete it anyway?' : ' Enabling this course will create a new blank course on ' . MOODLE_SITE_URL .  ' and enrolls all registered students including added students from other sections. Once the course is activated, you have a limited time to deactivate it. Are you sure you want to proceed?.') . '', $published_course_department['Course']['course_title'], $published_course_department['Course']['course_code']))); ?> <br>
						</div>
						<div class="large-6 columns">
							&nbsp;
						</div>
						<div class="large-3 columns">
							<!-- This buttun is temporarly, it will be replaces when we inject a code to update MoodleUsers Table on password chnage, students, bulk password update, password reset via email etc -->
							<?php //echo $this->Form->postLink(__('Sync Password Changes'), array('action' => 'sync_user_password_changes_for_moodle', $published_course_department['PublishedCourse']['id']), array('class'=>'tiny radius button bg-blue', 'confirm' => __('Are you sure you want to syncronize very recent user password changes made on SMiS to Moodle Site? Synchronization will only involve for students registered or added %s (%s) course. Please use this option before starting online exams and don\'t when students are taking an online exam and you only want some students passwords to sync.', $published_course_department['Course']['course_title'], $published_course_department['Course']['course_code']))); ?> <!-- <br> -->
							<?= ($published_course_department['PublishedCourse']['enable_for_moodle'] == 1 ? $this->Form->postLink(__('Sync New Enrollments'), array('action' => 'sync_new_enrollments', $published_course_department['PublishedCourse']['id']), array('class'=>'tiny radius button bg-purple', 'confirm' => __('This will syncronize any changes on SMiS like missing course registrations, course adds or instructor assignments and that aren\'t synced on ' . MOODLE_SITE_URL . '. Do you want to sync the changes? ', $published_course_department['Course']['course_title'], $published_course_department['Course']['course_code']))) : ''); ?> <br>
						</div>

						<?php
						if ($published_course_department['PublishedCourse']['enable_for_moodle']) { ?>
							<div class="row">
								<div class="large-12 columns">
									<hr>
									<h6 class="fs14 text-gray">Course enrollment statistics for <?=  $published_course_department['Course']['course_title'] . '(' .$published_course_department['Course']['course_code'] . ') on '. MOODLE_SITE_URL; ?></h6><br>
									<div style="overflow-x:auto;">
										<table cellpadding="0" cellspacing="0" class="table" style="width: 40%;">
											<thead>
												<tr>
													<td class="vcenter">Role</td>
													<td class="center">#</td>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td class="vcenter">Primary Instructor</td>
													<td class="center"><?= ($enrolled_primary_instructor ? $enrolled_primary_instructor : 0); ?></td>
												</tr>
												<tr>
													<td class="vcenter">Secondary Instructor</td>
													<td class="center"><?= ($enrolled_secondary_instructor ? $enrolled_secondary_instructor : 0); ?></td>
												</tr>
												<tr>
													<td class="vcenter">Student</td>
													<td class="center"><?= ($enrolled_students ? $enrolled_students : 0);  ?></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<?php
						} ?>
					</fieldset>
				</div>
			</div>
			<?php
		}
	} else if (ALLOW_MOODLE_INTEGRATION_FOR_SUBMITTED_GRADE == 1 && $enable_for_moodle) { ?>
		<div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span><i style="color:red;">Grade is partially or fully submitted for the selected course. Your're not advised to enable this course for Moodle unless you have some other reasons.</i></div>
		<?php
		$activate = ($published_course_department['PublishedCourse']['enable_for_moodle'] == 0 ? 'Enable Course for Moodle' : 'Disable Course on Moodle'); 
		$activateAction = ($published_course_department['PublishedCourse']['enable_for_moodle'] == 0 ? 'enable this course for moodle' : 'delete this course and existing course enrollments from Moodle');
		$buttonColor = ($published_course_department['PublishedCourse']['enable_for_moodle'] == 0 ? 'blue' : 'red'); ?>
		<hr>
		<div class="row">
			<div class="large-12 columns">
				<fieldset>
					<legend> &nbsp; &nbsp;  E-learning Integration Options: &nbsp; &nbsp; </legend>
					<br>
					<div class="large-3 columns">
						<?= $this->Form->postLink(__($activate), array('action' => 'enable_course_for_moodle', $published_course_department['PublishedCourse']['id']), array('class'=>'tiny radius button bg-' . $buttonColor . '', 'confirm' => __('Are you sure you want to ' . $activateAction . ' (%s (%s))? ' . ($published_course_department['PublishedCourse']['enable_for_moodle'] == 1 ? ' Disabling this course will also delete existing moodle course enrollments and data on ' . MOODLE_SITE_URL . '. You can only re-enable it before the grade for the course is is not submitted. Are you sure you want to deactivate the course and delete it anyway?' : ' Enabling this course will create a new blank course on ' . MOODLE_SITE_URL .  ' and enrolls all registered students including added students from other sections. Once the course is activated, you have a limited time to deactivate it. Are you sure you want to proceed?.') . '', $published_course_department['Course']['course_title'], $published_course_department['Course']['course_code']))); ?> <br>
					</div>
					<div class="large-6 columns">
						&nbsp;
					</div>
					<div class="large-3 columns">
						<!-- This buttun is temporarly, it will be replaces when we inject a code to update MoodleUsers Table on password chnage, students, bulk password update, password reset via email etc -->
						<?php //echo $this->Form->postLink(__('Sync Password Changes'), array('action' => 'sync_user_password_changes_for_moodle', $published_course_department['PublishedCourse']['id']), array('class'=>'tiny radius button bg-blue', 'confirm' => __('Are you sure you want to syncronize very recent user password changes made on SMiS to Moodle Site? Synchronization will only involve for students registered or added %s (%s) course. Please use this option before starting online exams and don\'t when students are taking an online exam and you only want some students passwords to sync.', $published_course_department['Course']['course_title'], $published_course_department['Course']['course_code']))); ?> <!-- <br> -->
						<?= ($published_course_department['PublishedCourse']['enable_for_moodle'] == 1 ? $this->Form->postLink(__('Sync New Enrollments'), array('action' => 'sync_new_enrollments', $published_course_department['PublishedCourse']['id']), array('class'=>'tiny radius button bg-purple', 'confirm' => __('This will syncronize any changes on SMiS like missing course registrations, course adds or instructor assignments and that aren\'t synced on ' . MOODLE_SITE_URL . '. Do you want to sync the changes? ', $published_course_department['Course']['course_title'], $published_course_department['Course']['course_code']))) : ''); ?> <br>
					</div>

					<?php
					if ($published_course_department['PublishedCourse']['enable_for_moodle']) { ?>
						<div class="row">
							<div class="large-12 columns">
								<hr>
								<h6 class="fs14 text-gray">Course enrollment statistics for <?=  $published_course_department['Course']['course_title'] . '(' .$published_course_department['Course']['course_code'] . ') on '. MOODLE_SITE_URL; ?></h6><br>
								<div style="overflow-x:auto;">
									<table cellpadding="0" cellspacing="0" class="table" style="width: 40%;">
										<thead>
											<tr>
												<td class="vcenter">Role</td>
												<td class="center">#</td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td class="vcenter">Primary Instructor</td>
												<td class="center"><?= ($enrolled_primary_instructor ? $enrolled_primary_instructor : 0); ?></td>
											</tr>
											<tr>
												<td class="vcenter">Secondary Instructor</td>
												<td class="center"><?= ($enrolled_secondary_instructor ? $enrolled_secondary_instructor : 0); ?></td>
											</tr>
											<tr>
												<td class="vcenter">Student</td>
												<td class="center"><?= ($enrolled_students ? $enrolled_students : 0);  ?></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<?php
					} ?>
				</fieldset>
			</div>
		</div>
		<?php
	}
} else { ?>
	<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Please select a course to get to get exam setup form.</div>
	<?php
} ?>
