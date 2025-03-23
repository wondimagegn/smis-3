<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Add Courses on behalf of a Student'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<?php //debug($this->request->data); ?>

				<div style="margin-top: -30px;">
					<hr>
					<div style="display:<?= (isset($hide_search) && empty($student_lists) ? 'display' : 'none'); ?>">
						<blockquote>
							<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
							<p style="text-align:justify;"><span class="fs16"> This tool will help you to perform course add on behalf of students. Please note that <span class="text-red"> only students that have at least one registration in the specified academic year, semester and year level appear for course add</span>.</span></p> 
						</blockquote>
					</div>

					<hr>

					<div onclick="toggleViewFullId('ListPublishedCourse')">
						<?php
						if (!empty($hide_search)) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
							<?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
							<?php
						} ?>
					</div>

					<div id="ListPublishedCourse" style="display:<?= (isset($hide_search) && !empty($student_lists) ? 'none' : 'display'); ?>">
						<?= $this->Form->Create('CourseAdd', array('action'=> 'add')); ?>
						<fieldset style="padding-bottom: 5px;padding-top: 5px;">
							<legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('Search.academicyear', array('label' => 'Academic Year: ', 'type' => 'select', 'id' => "AcademicYear", 'style' => 'width:90%', 'options' => $acyear_array_data, 'default' => isset($this->request->data['Search']['academicyear']) ? $this->request->data['Search']['academicyear'] : (isset($defaultacademicyear) ? $defaultacademicyear : ''))); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Search.semester', array('label' => 'Semester: ', 'id' => "Semester", 'style' => 'width:90%',  'options' => Configure::read('semesters'))); ?>
									<?php //echo (isset($semester_selected) ? $this->Form->input('semester_selected', array('id' => 'SemesterSelected', 'type' => 'hidden', 'value' => $semester_selected)) : ''); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Search.program_id', array('label' => 'Program: ', 'style' => 'width:90%',  'id' => 'program_id_1', 'options' => $programs)); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Search.program_type_id', array('label' => 'Program Type: ', 'style' => 'width:90%', 'options' => $programTypes)); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-6 columns">
									<?php
									if (!empty($departments)) {
										echo $this->Form->input('Search.department_id', array('label' => 'Department: ', 'style' => 'width:90%'));
									} else if (!empty($colleges)) {
										echo $this->Form->input('Search.college_id', array('label' => 'College: ', 'style' => 'width:90%'));
									} ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Search.year_level_name', array('label' => 'Year Level: ', 'style' => 'width:90%',  'id' => 'yearLevelName', 'options' => $yearLevels)); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Search.studentnumber', array('label' => 'Student ID: ', 'style' => 'width:90%', 'type' => 'text', 'placeholder' => 'Type Student ID if needed...', 'maxlength' => 25)); ?>
								</div>
							</div>
						</fieldset>
						<?= $this->Form->Submit('Continue', array('div' => false, 'class' => 'tiny radius button bg-blue', 'name' => 'continue')); ?>
						<?= $this->Form->end(); ?>
					</div>
					<hr>

					<?= $this->Form->create('CourseAdd'); ?>
					<?php
					if (isset($student_section_exam_status) && !empty($student_section_exam_status)) { ?>
						<?= $this->element('student_basic'); ?>
						<?php
						$button_visible = 0;
						if ($role_id  != ROLE_REGISTRAR) {
							if (!empty($ownDepartmentPublishedForAdd)) { ?>
								<div class="row">
									<div class="large-12 columns">
										<div class='smallheading'> List of courses published as an add to your section.</div>";
											<table id='fieldsForm' cellpadding="0" cellspacing="0" class="table">
												<thead>
													<tr>
														<th style='padding:0'>#</th>
														<th style='padding:0'>&nbsp;</th>
														<th style='padding:0'>Course Title</th>
														<th style='padding:0'>Course Code </th>
														<th style='padding:0'>Lecture hour </th>
														<th style='padding:0'>Tutorial hour </th>
														<th style='padding:0'>Credit </th>
													</tr>
												</thead>
												<tbody>
													<?php
													$count = 0;
													foreach ($ownDepartmentPublishedForAdd as $pk => $pv) {
														if ($pv['already_added'] == 0) {
															
															$button_visible++;
															
															echo $this->Form->hidden('CourseAdd.' . $count . '.published_course_id', array('value' => $pv['PublishedCourse']['id']));
															echo $this->Form->hidden('CourseAdd.' . $count . '.academic_year', array('value' => $pv['PublishedCourse']['academic_year']));
															echo $this->Form->hidden('CourseAdd.' . $count . '.semester', array('value' => $pv['PublishedCourse']['semester']));
															echo $this->Form->hidden('CourseAdd.' . $count . '.student_id', array('value' => $student_section_exam_status['StudentBasicInfo']['id']));
															echo $this->Form->hidden('CourseAdd.' . $count . '.year_level_id', array('value' => $pv['PublishedCourse']['year_level_id'])); ?>

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
													<tr>
														<td colspan=7>*** are courses which is already added.</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<?php
							}
						} ?>
							
						<div class="row">
							<div class="large-12 columns">
								<?php debug($year_level_id); ?>
								<?= $this->Form->hidden('Student.id', array('value' => $student_section_exam_status['StudentBasicInfo']['id'])); ?>
								<div class="row">
									<div class="large-6 columns">
										<?= $this->Form->input('Student.college_id', array('label' => 'Select College You want to Add Course.',  'style' => 'width: 100%',  'empty' => '[ Select College ]', 'id' => 'college_id_1', 'options' => $collegess, 'onchange' => 'updateDepartmentCollege(1)')); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-6 columns">
										<?= $this->Form->input('Student.department_id', array('id' => 'department_id_1', 'style' => 'width: 100%',  'onchange' => 'updateSection(1)', 'options' => $departments, 'empty' => '[ Select College ]')); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-6 columns">
										<?= $this->Form->input('Student.section_id', array('id' => 'section_id_1', 'style' => 'width: 100%', 'empty' => '[ Select College ]', 'onchange' => 'updatePublishedCourse(1)')); ?>
									</div>
								</div>

								<div class="row">
									<div class="large-12 columns">

										<!-- AJAX LOADING -->
										<div id="get_published_add_courses_id_1"> 

										</div>
										<!-- END AJAX LOADING -->

									</div>
								</div>

							</div>
						</div>

						<?php // echo $this->Form->submit('Add Selected',array('id'=>'add_button_disable','div'=>false,'name'=>'add')); ?>
						<?php
					}

					if (isset($coursesAdd) && !empty($coursesAdd)) { ?>
						<table id='fieldsForm' cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<th class="center">#</th>
									<th class="center">&nbsp;</th>
									<th class="vcenter">Course Title</th>
									<th class="center">Course Code</th>
									<th class="center">Lecture hour</th>
									<th class="center">Tutorial hour</th>
									<th class="center">Credit</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$count = 0;
								foreach ($coursesAdd as $pk => $pv) {
									echo $this->Form->hidden('CourseRegistration.' . $count . '.published_course_id', array('value' => $pv['PublishedCourse']['id']));
									echo $this->Form->hidden('CourseRegistration.' . $count . '.academic_year', array('value' => $pv['PublishedCourse']['academic_year']));
									echo $this->Form->hidden('CourseRegistration.' . $count . '.semester', array('value' => $pv['PublishedCourse']['semester']));
									echo $this->Form->hidden('CourseRegistration.' . $count . '.student_id', array('value' => $student_section['Student']['id']));
									echo $this->Form->hidden('CourseRegistration.' . $count . '.year_level_id', array('value' => $pv['PublishedCourse']['year_level_id'])); ?>
									<tr>
										<td class="center"><?= ++$count; ?></td>
										<td class="center"><?= $this->Form->checkbox('CourseRegistration.add.' . $pv['PublishedCourse']['id']); ?></td>
										<td class="vcenter"><?= $pv['Course']['course_title']; ?></td>
										<td class="center"><?= $pv['Course']['course_code']; ?></td>
										<td class="center"><?= $pv['Course']['lecture_hours']; ?></td>
										<td class="center"><?= $pv['Course']['tutorial_hours']; ?></td>
										<td class="center"><?= $pv['Course']['credit']; ?></td>
									</tr>
									<?php
								} ?>
								<tr>
									<td colspan=7><?= $this->Form->submit('Add Selected', array('div' => false, 'class' => 'tiny radius button bg-blue', 'name' => 'add')); ?></td>
								</tr>
							</tbody>
						</table>
						<?php
					} ?>

					<?php
					//debug($student_lists);
					if (!empty($student_lists) && !isset($no_display)) {
						debug($student_lists[0]);
						$cnt = 1; ?>
						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<td class="center">&nbsp;</td>
										<td class="center">#</td>
										<td class="vcenter">Full Name</td>
										<td class="vcenter">Sex</td>
										<td class="center">Student Number</td>
										<td class="center">Section</td>
										<td class="center">Load</td>
										<td class="center">Max Allowed</td>
										<td class="center">Actions</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 1;
									foreach ($student_lists as $student)  { ?>
										<tr <?= ($student['Load'] >= $student['MaxLoadAllowed'] ? ' class="rejected"' : ''); ?>>
											<td class="center" onclick="toggleView(this)" id="<?= $cnt; ?>"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $cnt, 'div' => false, 'align' => 'center')); ?></td>
											<td class="center"><?= $count++; ?></td>
											<td class="vcenter"><?= $student['Student']['full_name']; ?></td>
											<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : '')); ?></td>
											<td class="center"><?= $student['Student']['studentnumber']; ?></td>
											<td class="center"><?= $student['Section']['name']; ?></td>
											<td class="center"><?= $student['Load']; ?></td>
											<td class="center"><?= $student['MaxLoadAllowed']; ?></td>
											<td class="center"><?= ($student['Load'] <= $student['MaxLoadAllowed'] ? $this->Html->link(__('Add Course'), array('action' => 'add', $student['Student']['id'])) : '<span class="text-gray">Add Course</span>'); ?></td>
										</tr>
										<tr id="c<?= $cnt++; ?>" style="display:none">
											<td colspan="2" style="background-color: white;"> </td>
											<td colspan="7" style="background-color: white;">
												<?php
												if (isset($student['Registration'])) { ?>
													<table cellpadding="0" cellspacing="0" class="table">
														<tbody>
															<tr>
																<td class="vcenter" style="background-color: white;">
																	<span class="fs13 text-gray" style="font-weight: bold"><?= (isset($student['Department']['name']) ? 'Department: ': 'College: '); ?> </span> <?= (isset($student['Department']['name']) ? $student['Department']['name'] : (isset($student['College']['name']) ? 'Pre/Freshman (' . $student['College']['name'] . ')' : '')); ?>
																</td>
															</tr>
															<tr>
																<td class="vcenter" style="background-color: white;">
																	<span class="fs13 text-gray" style="font-weight: bold">Program: </span> <?= ($student['Program']['name']); ?> 
																</td>
															</tr>
															<tr>
																<td class="vcenter" style="background-color: white;">
																	<span class="fs13 text-gray" style="font-weight: bold">Program Type: </span> <?= ($student['ProgramType']['name']); ?> 
																</td>
															</tr>
															<tr>
																<td class="vcenter" style="background-color: white;">
																	<span class="fs13 text-gray" style="font-weight: bold">Section: </span> <?= ($student['Section']['name'] . ' (' . (isset($student['YearLevel']['name']) ? $student['YearLevel']['name'] : 'Pre/1st') . ', ' . $student['Section']['academicyear'] . ')'); ?>  &nbsp; <?= ($student['Section']['archive'] ? '<span class="rejected"> (Archieved) </span>' : '<span class="accepted"> (Active) </span>' ); ?>
																</td>
															</tr>
															<tr>
																<td class="vcenter" style="background-color: white;">
																	<span class="fs13 text-gray" style="font-weight: bold">Student Attached Curriculum: </span> <?= (!empty($student['Curriculum']['name']) ? $student['Curriculum']['name'] . ' - ' . $student['Curriculum']['year_introduced'] .  ' ( Credit Type: ' . $student['Curriculum']['type_credit'] . ' )': '<span class="Rejected">No Curriculum Attachement</span>'); ?>
																</td>
															</tr>
															<?= ($student['Load'] >= $student['MaxLoadAllowed'] ? '<tr><td class="vcenter rejected" style="background-color: white;">' . $student['Student']['full_name'] . ' ('. $student['Student']['studentnumber'] . ') already registered '.  ($student['Load'] > $student['MaxLoadAllowed'] ? ' over the allowed maximum ' : ' the allowed maximum ' ) . ((isset($student['Curriculum']) && !empty($student['Curriculum']['type_credit'])) ? (count(explode('ECTS', $student['Curriculum']['type_credit'])) >= 2  ? 'ECTSs' : 'Credits') : 'Credits') . ' for '. $student['Program']['name'] . '/' . $student['ProgramType']['name']. ' Program per semester (' . $student['MaxLoadAllowed'] . ' ' .((isset($student['Curriculum']) && !empty($student['Curriculum']['type_credit'])) ? (count(explode('ECTS', $student['Curriculum']['type_credit'])) >= 2  ? 'ECTSs' : 'Credits') : 'Credits'). ')</td></tr>' : '');  ?>
														</tbody>
													</table>
													<?php
												} else { ?>
													<span class="rejected"><?= $student['Student']['full_name'] . ' ('. $student['Student']['studentnumber'] . ')'; ?> have no registration in <?= $this->data['Search']['academicyear'] . '/' . $this->data['Search']['semester']; ?> using <?= $this->data['Search']['year_level_name']; ?> year Level. </span>
													<?php
												} ?>
											</td>
										</tr>
										<?php 
									} ?>
								</tbody>
							</table>
						</div>
						<br>
						<?php
					} else if (empty($student_lists) && isset($this->data['continue'])) { ?>
						<!-- <div class='info-box info-message'><span style='margin-right: 15px;'></span>There is no student that needs course add in the given criteria.</div> -->
						<?php
					}
					?>
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type='text/javascript'>
	//Sub cat combo
	var student_id = null;
	var year_level_name = '';

	<?php
	if (!empty($student_section_exam_status)) { ?>
		student_id = "<?= $student_section_exam_status['StudentBasicInfo']['id']; ?>";
		<?php
	} ?>

	<?php
	if (!empty($year_level_id)) { ?>
		year_level_name = "<?= $year_level_id; ?>";
		<?php
	} ?>

	function updateDepartmentCollege(id) {
		//serialize form data
		$("#get_published_add_courses_id_1").empty();

		var formData = $("#college_id_" + id).val();

		if (formData != '') {
			//get form action
			$("#college_id_" + id).attr('disabled', true);
			$("#department_id_" + id).attr('disabled', true);
			$("#section_id_" + id).attr('disabled', true);
			$("#add_button_disable").attr('disabled', true);

			var college_id = $("#college_id_" + id).val();

			var formUrl = '/departments/get_department_combo/' + formData +'/1/1';

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

					/* if (year_level_name == '') {
						subCat = '-1';
					} */

					if (subCat != '') {
						//get form action
						$("#get_published_add_courses_id_1").empty();
						
						$("#section_id_" + id).attr('disabled', true);

						var formUrl = '/sections/get_sections_by_dept_add_drop/' + subCat + '/' + student_id + '/' + year_level_name + '/' + college_id;
						
						$.ajax({
							type: 'post',
							url: formUrl,
							data: $('form').serialize(),
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

					}  else {

						$("#section_id_" + id).empty().append('<option value="">[ Select Department ]</option>');

						$("#college_id_" + id).attr('disabled', false);
						$("#department_id_" + id).attr('disabled', false);
						$("#section_id_" + id).attr('disabled', false);
						$("#add_button_disable").attr('disabled', false);

						$("#get_published_add_courses_id_1").empty();
					}
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;
		} else {

			$("#department_id_" + id).empty().append('<option value="">[ Select College First ]</option>');
			$("#section_id_" + id).empty().append('<option value="">[ Select College First ]</option>');

			$("#college_id_" + id).attr('disabled', false);
			$("#department_id_" + id).attr('disabled', false);
			$("#section_id_" + id).attr('disabled', false);
			$("#add_button_disable").attr('disabled', false);

			$("#get_published_add_courses_id_1").empty();
		}
	}

	//Sub cat combo
	function updateSection(id) {
		//serialize form data
		$("#get_published_add_courses_id_1").empty();
		
		var formData = $("#department_id_" + id).val();

		var college_id = $("#college_id_" + id).val();

		if (formData != '') {

			$("#section_id_" + id).attr('disabled', true);
			$("#college_id_" + id).attr('disabled', true);
			$("#department_id_" + id).attr('disabled', true);
			$("#add_button_disable").attr('disabled', true);

			/* if (year_level_name == '') {
				subCat = '-1';
			} */

			//get form action
			var formUrl = '/sections/get_sections_by_dept_add_drop/' + formData + '/' + student_id + '/' + year_level_name + '/' + college_id;

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

			$("#department_id_" + id).empty().append('<option value="">[ Select College First ]</option>');
			$("#section_id_" + id).empty().append('<option value="">[ Select College First ]</option>');

			$("#college_id_" + id).attr('disabled', false);
			$("#department_id_" + id).attr('disabled', false);
			$("#section_id_" + id).attr('disabled', false);
			$("#add_button_disable").attr('disabled', false);

			$("#get_published_add_courses_id_1").empty();

		}
	}

	function updatePublishedCourse(id) {

		$("#get_published_add_courses_id_1").empty();

		//serialize form data
		var formData = $("#section_id_" + id).val();

		if ($("#AcademicYear").val() && $("#Semester").val()) {
			var academic_year = $("#AcademicYear").val().replace("/", "-");
			var semester = $("#Semester").val();
			var academicYearandSemester = academic_year + "," + semester;
		} else {
			var academicYearandSemester = "";
		}

		$("#college_id_" + id).attr('disabled', true);
		$("#section_id_" + id).attr('disabled', true);
		$("#department_id_" + id).attr('disabled', true);
		$("#add_button_disable").attr('disabled', true);

		//get form action
		var formUrl = '/courseAdds/get_published_add_courses/' + formData + '/' + student_id + '/' + academicYearandSemester;

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

	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none') {
			$('#i' + obj.id).attr("src",'/img/minus2.gif');
		} else {
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		}
		$('#c' + obj.id).toggle("slow");
	}
</script>