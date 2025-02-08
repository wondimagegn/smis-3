<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('List Courses'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<?= $this->Form->create('Course'); ?>
				
				<div style="margin-top: -30px;">
					<hr>
					<fieldset style="padding-bottom: 5px;">
						<legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
						<div class="row">
							<div class="large-6 columns">
								<?php
								if (!empty($college_name) && ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_COLLEGE)) {
									echo '<br><strong>' . $college_name . '</strong><hr>';
								} else {
									echo $this->Form->input('Search.college_id', array('empty' => '[ Select College ]', 'label' => 'College/Institute/School: ', 'onchange' => 'getDepartment(1)', 'style' => 'width:90%;', 'id' => 'college_id_1'));
								} ?>
							</div>
							<div class="large-6 columns">
								<?php
								if (!empty($department_name) && $role_id == ROLE_DEPARTMENT) {
									echo '<br><strong>' . $department_name . '</strong><hr>';
								} else {
									echo $this->Form->input('Search.department_id', array('empty' => '[ Select Department ]', 'label' => 'Department: ', 'style' => 'width:90%;', 'onchange' => 'updateCurriculumAndYearLevel(1)', 'id' => 'department_id_1'));
								} ?>
							</div>
						</div>
						<div class="row">
							<div class="large-6 columns">
								<?= $this->Form->input('Search.curriculum_id', array('empty' => '[ Select Curriculum ]', 'onchange' => 'updateCourseCategory(1)', 'style' => 'width:90%;', 'id' => 'curriculum_id_1', 'label' => 'Curriculum: ', 'required')); ?>
							</div>
							<div class="large-2 columns">
								<?= $this->Form->input('Search.course_category_id', array('empty' => '[ All or Select ]', 'style' => 'width:90%;', 'id' => "course_category_id_1", 'label' => 'Course Category: ')); ?>
							</div>
							<div class="large-2 columns">
								<?= $this->Form->input('Search.semester', array('empty' => '[ All or Select ]', 'style' => 'width:90%;', 'options' => Configure::read('semesters'), 'label' => 'Semester: ')); ?>
							</div>
							<div class="large-2 columns">
								<?= $this->Form->input('Search.year_level_id', array('empty' => '[ All or Select ]', 'style' => 'width:90%;',  'id' => 'year_level_id_1', 'label' => 'Year Level: ')); ?>
							</div>
						</div>
						<hr>
						<?= $this->Form->submit(__('Get Courses'), array('name' => 'search', 'id' => 'getCourses', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
					</fieldset>
					<hr>
				</div>
				
				<?php
				//isset($selected_curriculum_details) ? debug(($selected_curriculum_details)) : '';
				if (!empty($course_associate_array)) { ?>
					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<td colspan=8 style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);">
										<!-- <br style="line-height: 0.35;"> -->
										<span style="font-size:14px;font-weight:bold; margin-top: 25px;"> 
											<?= $selected_curriculum_details['Curriculum']['name'] . ' - ' . $selected_curriculum_details['Curriculum']['year_introduced'] ; ?>
										</span>
										<br style="line-height: 0.35;">
										<span class="text-gray" style="padding-top: 15px; font-size: 13px; font-weight: bold"> 
                                       		Credit Type: <?= $selected_curriculum_details['Curriculum']['type_credit'] ; ?> <br style="line-height: 0.35;">
											<?= $selected_curriculum_details['Department']['name']; ?> <br style="line-height: 0.35;">
                                        	Program: <?= $program_name; ?>
                                        </span>
									</td>
									<td style="text-align: right; vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);">
										<?php //echo $this->Html->link($this->Html->image("/img/pdf_icon.gif", array("alt" => "Print To Pdf")) . ' Export to PDF', array('action' => 'print_courses_pdf'), array('escape' => false)) ?><!-- &nbsp;&nbsp;&nbsp;&nbsp; -->
										<?= $this->Html->link($this->Html->image("/img/xls-icon.gif", array("alt" => "Export TO Excel")) . ' Export to Excel', array('action' => 'export_courses_xls'), array('escape' => false)); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                                    </td>
								</tr>
							</thead>
						</table>
					</div>
					<br>

					<?php
					foreach ($course_associate_array as $yearkey => $yearvalue) { 
						foreach ($yearvalue as $semesterKey => $semestervalue) { ?>
							<!-- <div class="fs15"> <strong>Year Level:</strong><?php //echo $yearvalue[$semesterKey][0]['YearLevel']['name']; ?></div>
							<div class="fs15"> <strong>Semester:</strong><?php //echo$semesterKey; ?></div> -->
							<div style="overflow-x:auto;">
								<table cellpadding="0" cellspacing="0" class="table">
									<thead>
										<tr>
											<td colspan=9 style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);">
												<!-- <br style="line-height: 0.35;"> -->
												<span style="font-size:14px;font-weight:bold; margin-top: 25px;"> 
													<?= $yearvalue[$semesterKey][0]['YearLevel']['name'] . ' year, ' . ($semesterKey == 'I' ? '1st' : ($semesterKey == 'II' ? '2nd' : ($semesterKey == 'III' ? '3rd' : $semesterKey))) . ' Semester'; ?>
												</span>
												<br style="line-height: 0.35;">
											</td>
										</tr>
										<tr>
											<th class="center">#</th>
											<th class="vcenter"><?= $this->Paginator->sort('course_title'); ?></th>
											<th class="center"><?= $this->Paginator->sort('course_code'); ?></th>
											<th class="center"><?= $this->Paginator->sort('credit', (!isset($semestervalue[0]['Curriculum']['id']) ? 'Credit' : (count(explode('ECTS', $semestervalue[0]['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit'))); ?></th>
											<th class="center">LTL</th>
											<th class="center"><?= $this->Paginator->sort('course_category_id', 'Module/Category'); ?></th>
											<th class="center">Prerequisite</th>
											<th class="center">Grade Type</th>
											<th class="center">Actions</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$count =  1;
										foreach ($semestervalue as $course)  { ?>
											<tr>
												<td class="center"><?= $count++; ?></td>
												<td class="vcenter"><?= $course['Course']['course_title']; ?></td>
												<td class="center"><?= $course['Course']['course_code']; ?></td>
												<td class="center"><?= $course['Course']['credit']; ?></td>
												<td class="center"><?= $course['Course']['course_detail_hours']; ?></td>
												<td class="center"><?= $course['CourseCategory']['name']; ?></td>
												<td class="center">
													<?php
													if (isset($course['Prerequisite']) && !empty($course['Prerequisite'])) {
														echo '<ol style="text-align:left;">';
														foreach ($course['Prerequisite'] as $k => $v) {
															echo '<li style="text-align:left;">' . $v['PrerequisiteCourse']['course_title'] . '</li>';
														}
														echo '</ol>';
													} else {
														echo 'none';
													} ?>
												</td>
												<td class="center"><?= $course['GradeType']['type']; ?></td>
												<td class="center">
													<?= $this->Html->link(__(''), array('action' => 'view', $course['Course']['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?> 
													<?php
													if ($role_id == ROLE_DEPARTMENT) { ?>
														&nbsp;
														<?= $this->Html->link(__(''), array('action' => 'edit', $course['Course']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?>&nbsp;
														<?= $this->Html->link(__(''), array('action' => 'delete', $course['Course']['id']), array('class' => 'fontello-trash-1', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete %s course from %s curriculum?'), $course['Course']['course_title'] . '(' . $course['Course']['course_code']. ')', $course['Curriculum']['name'])); ?>
														<?php
													} ?>
												</td>
											</tr>
											<?php
										} ?>
									</tbody>
								</table>
							</div>
							<br><br>
							<?php
						}
					}
				} ?>
			</div>
		</div>
	</div>
</div>

<script type='text/javascript'>
	function getDepartment(id) {
		//serialize form data
		var formData = $("#college_id_" + id).val();
		$("#department_id_" + id).empty();
		$("#department_id_" + id).append('<option style="width:100px">loading...</option>');
		$("#department_id_" + id).attr('disabled', true);
		$("#curriculum_id_" + id).empty();
		$("#curriculum_id_" + id).append('<option style="width:100px">loading...</option>');
		$("#curriculum_id_" + id).attr('disabled', true);
		//get form action
		var formUrl = '/departments/get_department_combo/' + formData + '/0/1';
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data, textStatus, xhr) {
				$("#department_id_" + id).attr('disabled', false);
				$("#department_id_" + id).empty();
				$("#department_id_" + id).append(data);
				//Curriculum list
				var subCat = $("#department_id_" + id).val();
				$("#curriculum_id_" + id).empty();
				//get form action
				var formUrl = '/curriculums/get_curriculum_combo/' + subCat;
				$.ajax({
					type: 'get',
					url: formUrl,
					data: subCat,
					success: function(data, textStatus, xhr) {
						$("#curriculum_id_" + id).attr('disabled', false);
						$("#curriculum_id_" + id).empty();
						$("#curriculum_id_" + id).append(data);
					},
					error: function(xhr, textStatus, error) {
						alert(textStatus);
					}
				});
				//Curriculum list
				var subCat = $("#department_id_" + id).val();
				$("#year_level_id_" + id).empty();
				//get form action
				var formUrl = '/dormitory_assignments/get_year_levels/' + subCat;
				$.ajax({
					type: 'get',
					url: formUrl,
					data: subCat,
					success: function(data, textStatus, xhr) {
						$("#year_level_id_" + id).attr('disabled', false);
						$("#year_level_id_" + id).empty();
						$("#year_level_id_" + id).append(data);
					},
					error: function(xhr, textStatus, error) {
						alert(textStatus);
					}
				});
				//End of items list
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}

	function updateCourseCategory(id) {
		//serialize form data
		var formData = $("#curriculum_id_" + id).val();
		$("#curriculum_id_" + id).attr('disabled', true);
		$("#course_category_id_" + id).attr('disabled', true);
		//get form action
		var formUrl = '/curriculums/get_course_category_combo/' + formData;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data, textStatus, xhr) {
				$("#curriculum_id_" + id).attr('disabled', false);
				$("#course_category_id_" + id).attr('disabled', false);
				$("#course_category_id_" + id).empty();
				//$("#course_category_id_" + id).append('<option></option>');
				$("#course_category_id_" + id).append(data);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}

	function updateCurriculumAndYearLevel(id) {
		//serialize form data
		var formData = $("#department_id_" + id).val();
		$("#department_id_" + id).attr('disabled', true);
		$("#curriculum_id_" + id).attr('disabled', true);
		$("#year_level_id_" + id).attr('disabled', true);
		//get form action
		var formUrl = '/curriculums/get_curriculum_combo/' + formData;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data, textStatus, xhr) {
				$("#department_id_" + id).attr('disabled', false);
				$("#curriculum_id_" + id).attr('disabled', false);
				$("#curriculum_id_" + id).empty();
				$("#curriculum_id_" + id).append(data);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});

		//get form action
		var formUrl = '/course_schedules/get_year_levels/' + formData;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data, textStatus, xhr) {
				$("#department_id_" + id).attr('disabled', false);
				$("#year_level_id_" + id).attr('disabled', false);
				$("#year_level_id_" + id).empty();
				$("#year_level_id_" + id).append(data);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>