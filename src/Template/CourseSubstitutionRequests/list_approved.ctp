<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('View Course Substitutions'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('CourseSubstitutionRequest', array('action' => 'search2')); ?>

				<?php
				if ($this->Session->read('Auth.User')['role_id'] != ROLE_STUDENT) { ?>
					<div style="margin-top: -15px;">
						<div onclick="toggleViewFullId('ListPublishedCourse')">
							<?php
							if (!empty($turn_off_search)) {
								echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
							<?php
							} else {
								echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
							<?php
							} ?>
						</div>
						<div id="ListPublishedCourse" style="display:<?= (!empty($turn_off_search) ? 'none' : 'display'); ?>">
							<fieldset style="padding-bottom: 5px;padding-top: 5px;">
								<legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
								<div class="row">
									<?php
									if (isset($colleges) && !empty($colleges) && $this->Session->read('Auth.User')['role_id'] != ROLE_STUDENT && $this->Session->read('Auth.User')['role_id'] != ROLE_COLLEGE &&  $this->Session->read('Auth.User')['role_id'] != ROLE_DEPARTMENT) { ?>
										<div class="large-6 columns">
											<?= $this->Form->input('Search2.college_id', array('label' => 'College: ', 'style' => 'width:90%', 'empty' => ' ', 'onchange' => 'getDepartment(1)', 'id' => 'college_id', 'default' => $default_college_id)); ?>
										</div>
										<?php
									}
									if (isset($departments) && !empty($departments) && $this->Session->read('Auth.User')['role_id'] != ROLE_STUDENT && $this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR &&  $this->Session->read('Auth.User')['role_id'] != ROLE_COLLEGE &&  $this->Session->read('Auth.User')['role_id'] != ROLE_DEPARTMENT) { ?>
										<div class="large-6 columns">
											<?= $this->Form->input('Search2.department_id', array('label' => 'Department: ', 'style' => 'width:90%', 'empty' => ' ', 'id' => 'department_id_1', 'default' => $default_department_id)); ?>
										</div>
									<?php
									} ?>
								</div>
								<div class="row">
									<div class="large-3 columns">
										<?= $this->Form->input('Search2.program_id', array('label' => 'Program: ', 'style' => 'width:90%',   'id' => 'program_id_1', 'onchange' => 'updateCurriculumGivenProgram(1,' . $department_id . ')', /* 'empty' => ' ', */ 'options' => $programs,  'default' => $default_program_id)); ?>
									</div>
									<div class="large-6 columns">
										<?= $this->Form->input('Search2.curriculum_id', array('label' => 'Curriculum: ', 'style' => 'width:90%', 'id' => 'curriculum_id_1',  'empty' => ' ', 'options' => $curriculums, 'default' => $default_curriculum_id)); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-3 columns">
										<?= $this->Form->input('Search2.studentnumber', array('label' => 'Student ID:', 'placeholder' => 'Leave this if not sure', 'default' => $studentnumber, 'style' => 'width:90%;')); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search2.name', array('label' => 'Name:', 'placeholder' => 'First, Middle or Last name', 'default' => $name, 'style' => 'width:90%;')); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search2.limit', array('id' => 'limit ', 'type' => 'number', 'min' => '100',  'max' => '1000', 'value' => $limit, 'step' => '100', 'label' => ' Limit: ', 'style' => 'width:80%;')); ?>
									</div>
									<div class="large-3 columns">
										<div style="padding-left: 10%;">
											<!-- <br> -->
											<h6 class='fs13 text-gray'>Status: </h6>
											<?php $options = array('accepted' => ' Accepted', 'rejected' => ' Rejected', 'notprocessed' => ' Not Processed');  ?>
											<?= $this->Form->input('Search2.status', array('options' => $options, 'type' => 'radio', 'legend' => false, 'separator' => '<br>', 'label' => false, 'default' => 'notprocessed')); ?>
										</div>
									</div>
								</div>
							</fieldset>

							<?= $this->Form->submit(__('View Substitution'), array('name' => 'viewSubstitution', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>

							<?php
							if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) { ?>
								<br> 
								<br>
								<div style="margin-top: -10px;">
									<blockquote>
										<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
										<span style="text-align:justify;" class="fs14 text-gray">The student list you will get here depends on your <b style="text-decoration: underline;"><i>assigned College or Department, assigned Program and Program Types, and with your search conditions</i></b>. You can contact the registrar to adjust permissions assigned to you if you miss your students here.</span>
									</blockquote>
								</div>
								<?php
							} ?>
						</div>
					</div>
					<?php
				} ?>

				<hr>

				<?php 
				if (!empty($courseSubstitutionRequests)) { ?>
					<?php //debug($courseSubstitutionRequests);  ?>
					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<td class="center"><?= $this->Paginator->sort('id', '#'); ?></td>
									<td class="vcenter"><?= $this->Paginator->sort('student_id', 'Full name'); ?></td>
									<td class="vcenter"><?= $this->Paginator->sort('student_id', 'Sex'); ?></td>
									<td class="vcenter"><?= $this->Paginator->sort('student_id', 'Student ID'); ?></td>
									<td class="vcenter"><?= $this->Paginator->sort('course_for_substitued_id', 'Course For Substition'); ?></td>
									<td class="vcenter"><?= $this->Paginator->sort('course_be_substitued_id', 'Course to be Substitued'); ?></td>
									<td class="center"><?= $this->Paginator->sort('request_date'); ?></td>
									<td class="center"><?= $this->Paginator->sort('Status'); ?></td>
									<td class="center">Actions</td>
								</tr>
							</thead>
							<tbody>
								<?php
								$start = $this->Paginator->counter('%start%');
								foreach ($courseSubstitutionRequests as $courseSubstitutionRequest) { ?>
									<tr>
										<td class="center"><?= $start++; ?></td>
										<td class="vcenter"><?= $this->Html->link($courseSubstitutionRequest['Student']['full_name'], array('controller' => 'students', 'action' => 'student_academic_profile', $courseSubstitutionRequest['Student']['id'])); ?></td>
										<td class="center"><?= (strcasecmp(trim($courseSubstitutionRequest['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($courseSubstitutionRequest['Student']['gender']), 'female') == 0 ? 'F' : '')); ?></td>
										<td class="center"><?= $courseSubstitutionRequest['Student']['studentnumber']; ?></td>
										<td class="vcenter"><?= $this->Html->link($courseSubstitutionRequest['CourseForSubstitued']['course_code'] . ' - ' . $courseSubstitutionRequest['CourseForSubstitued']['course_title'] /* . '-' . $courseSubstitutionRequest['CourseForSubstitued']['Curriculum']['name'] . ' ' . $courseSubstitutionRequest['CourseForSubstitued']['Curriculum']['year_introduced'] . ' (' . $courseSubstitutionRequest['CourseForSubstitued']['Department']['name'] . ')' */, array('controller' => 'courses', 'action' => 'view', $courseSubstitutionRequest['CourseForSubstitued']['id'])); ?></td>
										<td class="vcenter"><?= $this->Html->link($courseSubstitutionRequest['CourseBeSubstitued']['course_code'] . ' - ' . $courseSubstitutionRequest['CourseBeSubstitued']['course_title']/*  . '-' . $courseSubstitutionRequest['CourseBeSubstitued']['Curriculum']['name'] . ' ' . $courseSubstitutionRequest['CourseBeSubstitued']['Curriculum']['year_introduced'] . ' (' . $courseSubstitutionRequest['CourseBeSubstitued']['Department']['name'] . ')' */, array('controller' => 'courses', 'action' => 'view', $courseSubstitutionRequest['CourseBeSubstitued']['id'])); ?></td>
										<td class="center"><?= $this->Time->format("M j, Y", $courseSubstitutionRequest['CourseSubstitutionRequest']['request_date'], NULL, NULL); ?></td>
										<td class="center">
											<?php
											if ($courseSubstitutionRequest['CourseSubstitutionRequest']['department_approve'] == 1) {
												echo 'Accepted';
											} else {
												if (is_null($courseSubstitutionRequest['CourseSubstitutionRequest']['department_approve'])) {
													echo 'waiting approval';
												} else if ($courseSubstitutionRequest['CourseSubstitutionRequest']['department_approve'] == 0) {
													echo 'Rejected';
												}
											} ?>
										</td>
										<?php
										if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE /* || $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR  */) { ?>
											<td class="center">
												<?= $this->Html->link(__('Review & Approve'), array('action' => 'approve_substitution', $courseSubstitutionRequest['CourseSubstitutionRequest']['id'])); ?>
											</td>
											<?php
										} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) { ?>
											<td class="center">
												<?= $this->Html->link(__('View'), array('action' => 'view', $courseSubstitutionRequest['CourseSubstitutionRequest']['id'])); ?>
											</td>
											<?php
										} ?>
									</tr>
									<?php
								} ?>
							</tbody>
						</table>
					</div>
					<hr>

					<p><?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'))); ?> </p>
					
					<div class="paging">
						<?= $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class' => 'disabled')); ?> | <?= $this->Paginator->numbers(); ?> | <?= $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled')); ?>
					</div>

					<?php
				}  else { ?>
					<div class='info-box info-message'><span style='margin-right: 15px;'></span>There is no Course Substitution Requests.</div>
					<?php
				} ?>
			</div>
		</div>
	</div>
</div>



<script type='text/javascript'>
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

	function getDepartment(id) {

		var formData = $("#college_id").val();

		$("#department_id_" + id).empty();
		$("#department_id_" + id).attr('disabled', true);

		var formUrl = '/departments/get_department_combo/' + formData + '/1';
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data, textStatus, xhr) {
				$("#department_id_" + id).attr('disabled', false);
				$("#department_id_" + id).empty();
				$("#department_id_" + id).append(data);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}

	function updateCurriculumGivenProgram(id, department_id) {
		var formData = $("#program_id_" + id).val();
		$("#program_id_" + id).attr('disabled', true);
		$("#curriculum_id_" + id).attr('disabled', true);
		//get form action
		var formUrl = '/curriculums/get_curriculum_combo/' + department_id + '/' + formData;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data, textStatus, xhr) {
				$("#program_id_" + id).attr('disabled', false);
				$("#curriculum_id_" + id).attr('disabled', false);
				$("#curriculum_id_" + id).empty();
				$("#curriculum_id_" + id).append('<option></option>');
				$("#curriculum_id_" + id).append(data);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});

		return false;

	}
</script>