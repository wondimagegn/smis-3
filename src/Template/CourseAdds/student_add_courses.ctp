<?= $this->Form->create('CourseAdd'); ?>
<script type='text/javascript'>

	var student_id = null;
	var year_level_name = '';

	<?php
	if (!empty($student_section_exam_status)) { ?>
		student_id = "<?= $student_section_exam_status['StudentBasicInfo']['id']; ?>";
		<?php
	} ?>

	<?php
	if (!empty($year_level_name)) { ?>
		year_level_name = "<?= $year_level_name; ?>";
		<?php
	} ?>

	<?php
	if ($student_active_section_ac_year == 0) { ?>
		var acYear = "<?= $current_academic_year; ?>"
		<?php
	} else { ?>
		var acYear = "<?= $student_active_section_ac_year; ?>"
		<?php
	} ?>

	//Sub cat combo
	function updateDepartmentCollege(id) {
		//serialize form data
		$("#get_published_add_courses_id_1").empty();

		var formData = $("#college_id_" + id).val();

		var college_id = $("#college_id_" + id).val();

		if (formData != '') {
			//get form action

			$("#college_id_" + id).attr('disabled', true);
			$("#department_id_" + id).attr('disabled', true);
			$("#section_id_" + id).attr('disabled', true);
			$("#add_button_disable").attr('disabled', true);

			var formUrl = '/departments/getDepartmentCombo/' + formData +'/1/1';

			<?php
			if ($student_active_section_ac_year == 0) { ?>
				var acYear = "<?= $current_academic_year; ?>"
				<?php
			} else { ?>
				var acYear = "<?= $student_active_section_ac_year; ?>"
				<?php
			} ?>

			$.ajax({
				type: 'get',
				url: formUrl,
				data: formData,
				success: function(data, textStatus, xhr) {
					$("#department_id_" + id).attr('disabled', false);
					$("#college_id_" + id).attr('disabled', false);
					$("#department_id_" + id).empty();
					$("#department_id_" + id).append(data);

					//student lists
					var subCat = $("#department_id_" + id).val();
					$("#section_id_" + id).attr('disabled', true);

					//get form action

					if (subCat != '') {

						$("#get_published_add_courses_id_1").empty();
						var formUrl = '/sections/getSectionsByDeptAddDrop/' + subCat + '/' + student_id + '/' + year_level_name + '/' + college_id;

						$.ajax({
							type: 'post',
							url: formUrl,
							data: $('form') .serialize(),
							success: function(data, textStatus, xhr) {
								$("#section_id_" + id).attr('disabled', false);
								$("#add_button_disable").attr('disabled', false);
								$("#section_id_" + id).empty();
								$("#section_id_" + id).append(data);
							},
							error: function(xhr, textStatus, error) {
								alert(textStatus);
							}
						});

						return false;

					} else {
						$("#section_id_" + id).empty().append('<option value="">[ Select Section ]</option>');
						$("#section_id_" + id).attr('disabled', false);
						$("#department_id_" + id).attr('disabled', false);
						$("#college_id_" + id).attr('disabled', false);

						$("#get_published_add_courses_id_1").empty();
					}
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;

		} else {
			$("#department_id_" + id).empty().append('<option value="">[ Select Department ]</option>');
			$("#section_id_" + id).empty().append('<option value="">[ Select Section ]</option>');
			$("#department_id_" + id).attr('disabled', false);
			$("#college_id_" + id).attr('disabled', false);

			$("#get_published_add_courses_id_1").empty();
		}
	}

	//Sub cat combo
	function updateSection(id) {
		//serialize form data
		var formData = $("#department_id_" + id).val();

		var college_id = $("#college_id_" + id).val();

		if (formData != '') {

			$("#section_id_" + id).attr('disabled', true);
			$("#add_button_disable").attr('disabled', true);

			var formUrl = '/sections/getSectionsByDeptAddDrop/' + formData + '/' + student_id + '/' + year_level_name + '/' + college_id;

			$.ajax({
				type: 'post',
				url: formUrl,
				data: $('form').serialize(),
				success: function(data, textStatus, xhr) {
					$("#section_id_" + id).attr('disabled', false);
					$("#college_id_" + id).attr('disabled', false);
					$("#department_id_" + id).attr('disabled', false);
					$("#add_button_disable").attr('disabled', false);
					$("#section_id_" + id).empty();
					$("#section_id_" + id).append(data);
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;

		} else {
			$("#section_id_" + id).empty().append('<option value="">[ Select Section ]</option>');
			$("#section_id_" + id).attr('disabled', false);

			$("#college_id_" + id).attr('disabled', false);
			$("#department_id_" + id).attr('disabled', false);

			$("#get_published_add_courses_id_1").empty();
		}
	}

	function updatePublishedCourse(id) {
		//serialize form data

		$("#get_published_add_courses_id_1").empty();

		var formData = $("#section_id_" + id).val();

		if (formData) {

			$("#college_id_" + id).attr('disabled', true);
			$("#section_id_" + id).attr('disabled', true);
			$("#department_id_" + id).attr('disabled', true);
			$("#add_button_disable").attr('disabled', true);

			//get form action
			var formUrl = '/courseAdds/getPublishedAddCourses/' + formData;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: formData,
				success: function(data, textStatus, xhr) {
					$("#section_id_" + id).attr('disabled', false);
					$("#department_id_" + id).attr('disabled', false);
					$("#college_id_" + id).attr('disabled', false);
					$("#add_button_disable").attr('disabled', false);
					$("#get_published_add_courses_id_" + id).empty();
					$("#get_published_add_courses_id_" + id).append(data);
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;
		}
	}
</script>
<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Add Courses'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?php
				if (isset($student_section_exam_status) && !empty($student_section_exam_status)) {
					echo $this->element('student_basic');
				} ?>
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<?php
							$button_visible = 0;
							if (!empty($ownDepartmentPublishedForAdd)) { ?>
								<div class="row">
									<div class="large-12 columns">
										<hr>
										<div class='smallheading fs14 text-gray'> List of courses published as an add to your section.</div>
										<br>
										<div style="overflow-x:auto;">
											<table id='fieldsForm' cellpadding="0" cellspacing="0" class="table">
												<thead>
													<tr>
														<th class="center">#</th>
														<th class="center">&nbsp;</th>
														<th class="center">Course Title</th>
														<th class="center">Course Code</th>
														<th class="center">Lecture hour</th>
														<th class="center">Tutorial hour</th>
														<th class="center">Credit</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$count = 0;
													foreach ($ownDepartmentPublishedForAdd as $pk => $pv) {
														if ($pv['already_added'] == 0) {
															$button_visible++; ?>
															<?= $this->Form->hidden('CourseAdd.' . $count . '.published_course_id', array('value' => $pv['PublishedCourse']['id'])); ?>
															<?= $this->Form->hidden('CourseAdd.' . $count . '.academic_year', array('value' => $pv['PublishedCourse']['academic_year'])); ?>
															<?= $this->Form->hidden('CourseAdd.' . $count . '.semester', array('value' => $pv['PublishedCourse']['semester'])); ?>
															<?= $this->Form->hidden('CourseAdd.' . $count . '.student_id', array('value' => $student_section_exam_status['StudentBasicInfo']['id'])); ?>

															<?php
															if (empty($pv['PublishedCourse']['year_level_id']) || $pv['PublishedCourse']['year_level_id'] == 0) {
																echo $this->Form->hidden('CourseAdd.' . $count . '.year_level_id', array('value' => 0));
															} else {
																echo $this->Form->hidden('CourseAdd.' . $count . '.year_level_id', array('value' => $pv['PublishedCourse']['year_level_id']));
															} ?>

															<tr>
																<td><?= ++$count; ?></td>
																<td><?= $this->Form->checkbox('CourseAdd.add.' . $pv['PublishedCourse']['id']); ?></td>
																<td><?= $pv['Course']['course_title']; ?></td>
																<?php
														} else { ?>
															<tr>
																<td><?= ++$count; ?></td>
																<td>***</td>
																<td><?= $pv['Course']['course_title']; ?></td>
																<?php
														} ?>
																<td><?= $pv['Course']['course_code']; ?></td>
																<td><?= $pv['Course']['lecture_hours']; ?></td>
																<td><?= $pv['Course']['tutorial_hours']; ?></td>
																<td><?= $pv['Course']['credit']; ?></td>
															</tr>
															<?php
													} ?>

												</tbody>
												<tfoot>
													<tr>
														<td colspan=7>*** are courses which are already added.</td>
													</tr>
												</tfoot>
											</table>
										</div>
										<br>
									</div>
								</div>
								<?php
							} ?>

							<div class="row">
								<div class="large-12 columns">
									<table cellpadding="0" cellspacing="0" class="table">
										<tr>
											<td>
												<div class="row">
													<div class="large-5 columns">
														<?= $this->Form->input('Student.college_id', array('label' => 'Select College You want to Add Course.', 'empty' => '[ Select College ]', 'options' => $colleges, 'id' => 'college_id_1', 'style' => 'width: 90%', 'onchange' => 'updateDepartmentCollege(1)')); ?>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<td style="background-color: white;">
												<div class="row">
													<div class="large-5 columns">
														<?= $this->Form->input('Student.department_id', array('id' => 'department_id_1', 'style' => 'width: 90%', 'onchange' => 'updateSection(1)', 'options' => $departments, 'empty' => '[ Select College First ]')); ?>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<div class="row">
													<div class="large-5 columns">
														<?= $this->Form->input('Student.section_id', array('id' => 'section_id_1', 'style' => 'width: 90%', 'onchange' => 'updatePublishedCourse(1)', 'empty' => '[ Select Department/Freshman First ]')); ?>
													</div>
												</div>
											</td>
										</tr>
									</table>

									<!-- AJAX LOADING -->

									<div id="get_published_add_courses_id_1">

									</div>

									<!-- END AJAX LOADING -->

								</div>
							</div>
						</td>
					</tr>
				</table>
				<hr>
			</div>
		</div>
	</div>
</div>
