<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? __('Confirm Course Drops') : __('Approve Course Drops')); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;"></div>

				<?= $this->Form->create('CourseDrop'); ?>

				<?php
				if (!empty($coursesss)) { ?>
					<hr>
					<h6 class="fs14 text-gray">List of students who submitted Course drop request for approval:</h6>
					<hr>

					<h6 id="validation-message_non_selected" class="text-red fs14"></h6>

					<?php
					$count = 0;
					$forced_drops = 0;
					foreach ($coursesss as $department_name => $program) { // department
						foreach ($program as $program_name => $programType) { //program 
							foreach ($programType as $program_type_name => $sections) { // program Type
								$display_button = 0;
								$section_count = 0;
								foreach ($sections as $section_id => $courses) { //debug($courses); ?>
									<br>
									<div style="overflow-x:auto;">
										<table cellpadding="0" cellspacing="0" class="table">
											<thead>
												<tr>
													<td colspan=11 style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
														<span style="font-size:16px;font-weight:bold; padding-top: 25px;"> 
															<?= $section_id . ' ' . (isset($courses[0]['CourseRegistration']['PublishedCourse']['Section']['YearLevel']['name'])  ?  ' (' . $courses[0]['CourseRegistration']['PublishedCourse']['Section']['YearLevel']['name'] .', '. $courses[0]['CourseRegistration']['PublishedCourse']['academic_year']. ')' : ' (Pre/1st)'); ?>
														</span>
														<br style="line-height: 0.35;">
														<span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
															<?= (isset($courses[0]['CourseRegistration']['PublishedCourse']['Section']['Department']) && !empty($courses[0]['CourseRegistration']['PublishedCourse']['Section']['Department']['name']) ? $courses[0]['CourseRegistration']['PublishedCourse']['Section']['Department']['name'] : $courses[0]['CourseRegistration']['PublishedCourse']['Section']['College']['name'] . ' - Pre/Freshman'); ?> &nbsp; | &nbsp; 
															<?= (isset($program_name) ? $program_name :  ''); ?> &nbsp; | &nbsp; <?= (isset($program_type_name) ? $program_type_name :  ''); ?> <br>
														</span>
														<span class="text-gray" style="padding-top: 14px; font-size: 13px; font-weight: bold">
															<i><?= (isset($courses[0]['CourseRegistration']['PublishedCourse']['Section']['Curriculum']['name']) ? ucwords(strtolower($courses[0]['CourseRegistration']['PublishedCourse']['Section']['Curriculum']['name'])) . ' - ' . $courses[0]['CourseRegistration']['PublishedCourse']['Section']['Curriculum']['year_introduced'] . ' (' .  (count(explode('ECTS', $courses[0]['CourseRegistration']['PublishedCourse']['Section']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit') . ') <br style="line-height: 0.35;">' : ''); ?></i>
														</span>
													</td>
												</tr>
												<tr>
													<th class="center">#</th>
													<th class="vcenter">Full Name</th>
													<th class="center">Sem</th>
													<th class="center">ACY</th>
													<th class="center">Load</th>
													<th class="vcenter">Course</th>
													<th class="center"><?= (!isset($courses[0]['CourseRegistration']['PublishedCourse']['Section']['Curriculum']['id']) ? 'Cr.' : (count(explode('ECTS', $courses[0]['CourseRegistration']['PublishedCourse']['Section']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit')); ?></th>
													<th class="center">LTL</th>
													<?php
													if ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_COLLEGE) {
														$options = array('1' => ' Accept', '0' => ' Reject');
														$attributes = array('legend' => false, 'separator' => "<br/>"); ?>

														<th class="center">Accept/Reject</th>
														<th class="center">Reason</th>
														<?php
													}
													if ($role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read('Auth.User')['Role']['parent_id']) {
														$options = array('1' => ' Confirm', '0' => ' Deny');
														$attributes = array('legend' => false, 'separator' => "<br/>"); ?>
														<th class="center">Confirm Drop</th>
														<?php
													} ?>
												</tr>
											</thead>
											<tbody>
												<?php
												foreach ($courses as $kc => $vc) { ?>
													<?= $this->Form->hidden('CourseDrop.' . $count . '.id', array('value' => $vc['CourseDrop']['id'], 'label' => false, 'size' => 4, 'div' => false)); ?>
													<tr>
														<td class="center"><?= ($count + 1); ?></td>
														<td class="vcenter"><?= $this->Html->link($vc['Student']['full_name'], '#', array('class' => 'jsview', 'data-animation' => "fade", 'data-reveal-id' => 'myModal', 'data-reveal-ajax' => "/students/get_modal_box/" . $vc['Student']['id'])); ?></td>
														<td class="center"><?= (isset($vc['CourseRegistration']['PublishedCourse']['semester']) ? $vc['CourseRegistration']['PublishedCourse']['semester'] : 'N/A'); ?></td>
														<td class="center"><?= (isset($vc['CourseRegistration']['PublishedCourse']['academic_year']) ? $vc['CourseRegistration']['PublishedCourse']['academic_year'] : 'N/A'); ?></td>
														<td class="center"><?= (isset($vc['Student']['max_load']) ? $vc['Student']['max_load'] : 'N/A'); ?></td>
														<td class="vcenter"><?= (isset($vc['CourseRegistration']['PublishedCourse']['Course']['course_title']) ? $vc['CourseRegistration']['PublishedCourse']['Course']['course_title'] . ' ('. $vc['CourseRegistration']['PublishedCourse']['Course']['course_code'] . ')' . (isset($vc['CourseRegistration']['do_not_allow_drop']) ?  $vc['CourseRegistration']['do_not_allow_drop'] : '') : 'N/A'); ?></td>
														<td class="center"><?= (isset($vc['CourseRegistration']['PublishedCourse']['Course']['credit']) ? $vc['CourseRegistration']['PublishedCourse']['Course']['credit'] : 'N/A'); ?></td>
														<td class="center"><?= (isset($vc['CourseRegistration']['PublishedCourse']['Course']['course_detail_hours']) ? $vc['CourseRegistration']['PublishedCourse']['Course']['course_detail_hours'] : 'N/A'); ?></td>
														<?php
														if ((is_null($vc['CourseDrop']['registrar_confirmation'])  || $vc['CourseDrop']['registrar_confirmation'] == '') && ($role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read('Auth.User')['Role']['parent_id'])) { ?>
															<td><div style="margin-left: 20%;margin-top: 5%;"><?= (isset($vc['CourseRegistration']['PublishedCourse']['Course']['id']) && !isset($vc['CourseRegistration']['do_not_allow_drop']) ? $this->Form->radio('CourseDrop.' . $count . '.registrar_confirmation', $options, $attributes) : ''); ?></div></td>
															<?php
														}
														if (isset($vc['CourseRegistration']['PublishedCourse']['Course']['id']) && (is_null($vc['CourseDrop']['department_approval']) || $vc['CourseDrop']['department_approval'] == '') && ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_COLLEGE)) { ?>
															<td><div style="margin-left: 20%; margin-top: 10%;"><?= (isset($vc['CourseRegistration']['PublishedCourse']['Course']['id']) && !isset($vc['CourseRegistration']['do_not_allow_drop']) ? $this->Form->radio('CourseDrop.' . $count . '.department_approval', $options, $attributes) : ''); ?></div></td>
															<td><div style="margin-top: 5%;"><?= (isset($vc['CourseRegistration']['PublishedCourse']['Course']['id']) && !isset($vc['CourseRegistration']['do_not_allow_drop']) ? $this->Form->input('CourseDrop.' . $count . '.reason', array('placeholder'=> 'Your reason here if any...', 'size' => '2', 'rows' => '2', 'value' => isset($this->request->data['CourseDrop'][$count]['reason']) ? $this->request->data['CourseDrop'][$count]['reason'] : '', 'label' => false, 'div' => false)) : ''); ?></div></td>
															<?php
														} ?>
													</tr>
													<?php
													$count++;
												} ?>
											</tbody>
										</table>
									</div>
									<br>
									<?php
								} ?>
								<?php
							}
						}
					}

					if ($role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read('Auth.User')['Role']['parent_id']) {
						echo '<hr>' . $this->Form->submit('Confirm/Deny Request', array('name' => 'approverejectdrop', 'id' => 'approverejectdrop', 'class' => 'tiny radius button bg-blue', 'div' => false));
					} else if ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_COLLEGE) {
						echo '<hr>' . $this->Form->submit('Approve/Reject Drop', array('name' => 'approverejectdrop', 'id' => 'approverejectdrop', 'class' => 'tiny radius button bg-blue', 'div' => false));
					}
				}  else { ?>
					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No course drop request is found to <?= $this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT ? 'approve' : 'confirm'; ?> for now.</div>
					<?php
				} ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	var form_being_submitted = false;

    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	$('#approverejectdrop').click(function() {

		var isValid = true;
        var radios = document.querySelectorAll('input[type="radio"]');
		var checkedOne = Array.prototype.slice.call(radios).some(x => x.checked);

		if (!checkedOne) {
            alert('At least one Course Drop Must be Accepted or Rejected!');
			validationMessageNonSelected.innerHTML = 'At least one Course Drop Must be Accepted or Rejected!';
			isValid = false;
			return false;
		}

		if (form_being_submitted) {
			alert("Approving/Rejecting course drop, please wait a moment...");
			$("#approverejectdrop").attr('disabled', true);
			return false;
		}


		if (!form_being_submitted && isValid) {
			$('#approverejectdrop').val('Approving/Rejecting Course Drop...');
			form_being_submitted = true;
			return true;
		} else {
			isValid = false;
			return false;
		}
	});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>