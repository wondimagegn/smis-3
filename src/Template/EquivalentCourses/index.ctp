<div class="box" style="display: block;">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('View Equivalent Course Maps'); ?></span>
        </div>
    </div>
    <div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('EquivalentCourse'); ?>
				<div style=" margin-top: -10px;">
					<blockquote>
						<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<p style="text-align:justify;">
							<span class="fs15 text-black">
								You can consult your college registrar to unlock a specific curricullum which is approved and locked before so that Delete Map it is available for courses with wrong course mapping. <br> <br> 
								<b><i>You can Only able to Delete wrong Course Mappings for Curricullums which are not approved and locked.</i></b>
							</span>
						</p> 
					</blockquote>
				</div>
				<br>
				<div style=" margin-top: -20px;">
					<fieldset>
                        <legend>&nbsp;&nbsp; Search / Filter Curriculums &nbsp;&nbsp;</legend>
                        <div class="row">
                            <div class="large-3 columns">
								<?= $this->Form->input('Search.program_id', array('label' => 'Program: ', 'id' => 'program_id_1', 'onchange' => 'updateCurriculumGivenProgram(1,' . $department_id . ')', 'empty' => '[ Select Program ]', 'required', 'style' => 'width:90%')); ?>
                            </div>
                            <div class="large-9 columns">
								<?= $this->Form->input('Search.curriculum_id', array('label' => 'Curiculum: ',  'empty' => '[ Select Program ]', 'id' => 'curriculum_id_1', 'required', 'style' => 'width:90%')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="large-3 columns">
								<?= $this->Form->input('Search.title', array('label' => 'Course Title: ', 'placeholder' => 'Full or part of course title...', 'id' => 'course_title', 'style' => 'width:90%')); ?>
                            </div>
                            <div class="large-9 columns">
								&nbsp;
                            </div>
                        </div>
						<hr>
						<?= $this->Form->submit(__('View Course Map '), array('name' => 'viewCourseMap', 'id' => 'viewCourseMap', 'class'=>'tiny radius button bg-blue')); ?>
					</fieldset>
				</div>

				<?php

				if (!empty($equivalentCourses)) { ?>

					<?= debug($isCurriculumApproved); ?>
					<br>
					<h6 class="fs14 text-gray"><?= __('Equivalent Courses'); ?></h6>
					<br>

					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table fs14">
							<thead>
								<tr>
									<td class="center">#</td>
									<td class="vcenter"><?= $this->Paginator->sort('course_for_substitued_id', 'Course to be Equivalent'); ?></td>
									<td class="vcenter"><?= $this->Paginator->sort('course_be_substitued_id', 'Equivalent Course'); ?></td>
									<?php
									if (!isset($isCurriculumApproved) || (isset($isCurriculumApproved) && $isCurriculumApproved == 0 )) { ?>
										<td class="center">Actions</td>
										<?php
									} ?>
								</tr>
							</thead>
							<tbody>
								<?php
								$start = $this->Paginator->counter('%start%');
								foreach ($equivalentCourses as $equivalentCourse) { ?>
									<tr>
										<td class="vcenter"><?= $start++; ?></td>
										<td class="vcenter"><?= $this->Html->link($equivalentCourse['CourseForSubstitued']['course_title'] . ' (<b>'.  $equivalentCourse['CourseForSubstitued']['course_code'] . '</b>)<br>' . $equivalentCourse['CourseForSubstitued']['Curriculum']['name'] . ' - ' . $equivalentCourse['CourseForSubstitued']['Curriculum']['year_introduced'] . '<br>(' . $equivalentCourse['CourseForSubstitued']['Department']['name'] . ')', array('controller' => 'courses', 'action' => 'view', $equivalentCourse['CourseForSubstitued']['id']), array('escape' => false)); ?></td>
										<td class="vcenter"><?= $this->Html->link($equivalentCourse['CourseBeSubstitued']['course_title'] . ' (<b>' . $equivalentCourse['CourseBeSubstitued']['course_code'] . '</b>)<br>From: ' . $equivalentCourse['CourseBeSubstitued']['Curriculum']['name'] . ' - ' . $equivalentCourse['CourseBeSubstitued']['Curriculum']['year_introduced'] . '<br>(' . $equivalentCourse['CourseBeSubstitued']['Department']['name'] . ')', array('controller' => 'courses', 'action' => 'view', $equivalentCourse['CourseBeSubstitued']['id']), array('escape' => false)); ?></td>
										<?php
										if (!isset($isCurriculumApproved) || (isset($isCurriculumApproved) && $isCurriculumApproved == 0 )) { ?>
											<td class="center"><?= $this->Html->link(__('Delete Map'), array('action' => 'delete', $equivalentCourse['EquivalentCourse']['id']), null, sprintf(__('Are you sure you want to delete  %s?'), $equivalentCourse['CourseBeSubstitued']['course_code'] . '-' . $equivalentCourse['CourseBeSubstitued']['course_title'] . ' mapped to ' . $equivalentCourse['CourseForSubstitued']['course_code'] . '-' . $equivalentCourse['CourseForSubstitued']['course_title'])); ?></td>
											<?php
										} ?>
									</tr>
									<?php 
								} ?>
							</tbody>
						</table>
					</div>
					<br>

					<hr>
					<div class="row">
						<div class="large-7 columns">
							<div style="padding-left: 5%;">
								<?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total'))); ?>
							</div>
						</div>
						<div class="large-5 columns right">
							<div class="paging">
								<?= $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class' => 'disabled')); ?> | <?= $this->Paginator->numbers(); ?> | <?= $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled')); ?>
							</div>
						</div>
					</div>
					<hr>
					<?php
				} ?>
			</div>
		</div>
	</div>
</div>


<script type='text/javascript'>
	//Sub cat combo
	function updateCurriculumGivenProgram(id, department_id) {
		//serialize form data
		var formData = $("#program_id_" + id).val();
		$("#program_id_" + id).attr('disabled', true);
		$("#curriculum_id_" + id).attr('disabled', true);

		//get form action
		var formUrl = '/curriculums/get_curriculum_combo/' + department_id + '/' + formData + '/3';
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data, textStatus, xhr) {
				$("#program_id_" + id).attr('disabled', false);
				$("#curriculum_id_" + id).attr('disabled', false);
				$("#curriculum_id_" + id).empty();
				//$("#curriculum_id_" + id).append('<option></option>');
				$("#curriculum_id_" + id).append(data);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}
</script>