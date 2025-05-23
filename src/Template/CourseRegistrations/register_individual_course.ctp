<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Register Selected Course for Students in a Section'); ?></span>
        </div>
    </div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				
				<?= $this->Form->create('CourseRegistration', array('onSubmit' => 'return checkForm(this);')); ?>
				
				<div style="margin-top: -30px; ">
					<div style="display:<?= (isset($organized_published_course_by_section) ? 'none' : 'display'); ?>">
						<hr>
						<blockquote>
							<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
							<p style="text-align:justify;"><span class="fs16">This tool will help you to register selected course for students assigned in the selected section. It is important when course registration date is passed for students and you want to register all eligible students in the section who registered for atleast one course, took and got a pass mark for prerequisite course(s) if any, and have current load below the allowed credit set as per program and program type in the general setting system wide. <br><span class="text-red">Use this option only if none or most of the students in the section are not registered for the published course for some reason.</span>
						</blockquote>
					</div>
					
					<hr>

					<div onclick="toggleViewFullId('ListPublishedCourse')">
						<?php
						if (isset ($organized_published_course_by_section) && !empty ($organized_published_course_by_section)) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Display Filter</span>
							<?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Hide Filter</span>
							<?php
						}
						?>
					</div>

					<div id="ListPublishedCourse" style="display:<?= (isset($organized_published_course_by_section) ? 'none' : 'display'); ?>">
						<?php //if (!isset($hide_search)) {  ?>
						<fieldset style="padding-bottom: 5px;">
							<legend>&nbsp;&nbsp; Search &nbsp;&nbsp;</legend>
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('Student.academic_year', array('label' => 'Academic Year: ', 'type' => 'select', 'style' => 'width: 90%;', 'required', 'options' => $acyear_array_data, /* 'empty' => "[ Select Academic Year ]", */ 'default' => isset($this->request->data['Student']['academic_year'])  ? $this->request->data['Student']['academic_year'] : $defaultacademicyear)); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Student.semester', array('label' => 'Semester: ', 'options' => Configure::read('semesters'), 'empty' => '[ Select Semester ]', 'required')); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Student.program_id', array('label' => 'Program: ', 'required', 'empty' => '[ Select Program ]', 'style' => 'width: 90%;')); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Student.program_type_id', array('label' => 'Program Type', 'required',  'empty' => '[ Select Program Type ]', 'style' => 'width: 90%;')); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-6 columns">
									<?= (isset($departments) && !empty($departments) ? $this->Form->input('Student.department_id', array('label' => 'Department: ', 'required', 'empty' => "[ Select Department ]", 'style' => 'width: 90%;')) : (isset($colleges) && !empty($colleges) ? $this->Form->input('Student.college_id', array('label' => 'College:', 'required', 'empty' => "[ Select College ]", 'style' => 'width: 90%;')) : '')); ?>
								</div>
								<?php
								if (isset($departments) && !empty($departments)) { ?>
									<div class="large-3 columns">
										<?= $this->Form->input('Student.year_level_id', array('label' => 'Year Level', 'empty' => "[ Select Year Level ]", 'required', 'style' => 'width: 90%;')); ?>
									</div>
									<?php
								} else { ?>
									<div class="large-3 columns">
										&nbsp;
									</div>
									<?php
								} ?>
								<div class="large-3 columns">
									&nbsp;
								</div>
							</div>
							<hr>
							<?= $this->Form->submit('Search', array('name' => 'getsection', 'class' => 'tiny radius button bg-blue', 'div' => 'false')); ?>
						</fieldset>
						<?php //}  ?>
					</div>
					<hr>
				</div>

				<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
				<br>
				
				<?php
				if (isset ($organized_published_course_by_section) && !empty ($organized_published_course_by_section)) {

					$display_button = 0;
					$section_count = 0;
					$course_count = 0;

					foreach ($organized_published_course_by_section as $section_id => $coursss) {
						$section_count++;
						if (!empty ($coursss)) { ?>
							<div style="overflow-x:auto;">
								<table id='fieldsForm' cellspacing="0" cellpadding="0" class="table">
									<thead>
										<tr>
                                            <td colspan=8 style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                <span style="font-size:16px;font-weight:bold; margin-top: 25px;"> Section: <?= $sections[$section_id]; ?></span>
                                                <br>
                                                <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold"> 
                                                    <?= (isset($coursss[$course_count]['Section']['Program']['id']) && !empty($coursss[$course_count]['Section']['Program']['name']) ? $coursss[$course_count]['Section']['Program']['name'] : $program_name); ?> &nbsp;|&nbsp; <?= (isset($coursss[$course_count]['Section']['ProgramType']['id']) && !empty($coursss[$course_count]['Section']['ProgramType']['name']) ? $coursss[$course_count]['Section']['ProgramType']['name'] : $program_type_name); ?> &nbsp;|&nbsp; 
                                                    <?php //echo (isset($coursss[$course_count]['Section']['Department']['id']) && !empty($coursss[$course_count]['Section']['Department']['name']) ? $coursss[$course_count]['Section']['Department']['name'] : $coursss[$course_count]['Section']['College']['name'] . ' Pre/Freshman'); ?><!-- <br> -->
                                                    <?= (isset($department_name) && !empty($department_name['Department']['name']) ? $department_name['Department']['name'] : ( isset($college_name) && !empty($college_name['College']['name']) ? $college_name['College']['name'] . (isset($coursss[$course_count]['Program']['id']) && $coursss[$course_count]['Program']['id'] == PROGRAM_REMEDIAL ? ' - Remedial Program' : ' - Pre/Freshman') : '')) ; ?><br>
                                                    <?= (isset($coursss[$course_count]['Program']['id']) && $coursss[$course_count]['Program']['id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : ( isset($coursss[$course_count]['Section']['YearLevel']['name']) ? $coursss[$course_count]['Section']['YearLevel']['name'] : (isset($year_level_id) && !empty($year_level_id) ? $year_level_id : 'Pre/1st'))); ?> &nbsp;|&nbsp; <?= isset($coursss[$course_count]['Section']['academicyear']) && !empty($coursss[$course_count]['Section']['academicyear']) ? $coursss[$course_count]['Section']['academicyear'] : (isset($academic_year) && !empty($academic_year) ? $academic_year : $defaultacademicyear); ?> &nbsp;|&nbsp; <?= ($semester == 'I' ? '1st' :($semester == 'II' ? '2nd' :($semester == 'III' ? '3rd': $semester))); ?> semester
                                                </span>
                                            </td>
                                        </tr>
										<tr>
											<th class="center">&nbsp;</th>
											<th class="center">#</th>
											<th class="vcenter">Course Title</th>
											<th class="center">Course Code</th>
											<th class="center">Credit</th>
											<th class="center">Lecture</th>
											<th class="center">Tutorial</th>
											<th class="center">Lab</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$count = 1;
										foreach ($coursss as $kc => $vc) { ?>
											<tr>
												<?php
												if (isset ($vc['grade_submitted']) && !empty ($vc['grade_submitted'])) {
													echo '<td class="center">**</td>';
												} else {
													echo '<td class="center">' . $this->Form->checkbox('PublishedCourse.' . $section_id . '.' . $vc['PublishedCourse']['id'], array('class' => 'listOfPublishedCourse', 'id' => $count)) . '</td>';
												} ?>
												<td class="center"><?= $count; ?></td>
												<td class="vcenter"><?= $vc['Course']['course_title']; ?></td>
												<td class="center"><?= $vc['Course']['course_code']; ?></td>
												<td class="center"><?= $vc['Course']['credit']; ?></td>
												<td class="center"><?= $vc['Course']['lecture_hours']; ?></td>
												<td class="center"><?= $vc['Course']['tutorial_hours']; ?></td>
												<td class="center"><?= $vc['Course']['laboratory_hours']; ?></td>
											</tr>
											<?php
											$count++;
											$course_count++;
										} ?>
									</tbody>
								</table>
							</div>
							<br>
							<?php
						} else {
							$display_button++;
						}
					} ?>

					<hr>
					<?= $this->Form->submit('Register Selected', array('name' => 'registerIndivdualCourse', 'id' => 'registerIndivdualCourse', 'class' => 'tiny radius button bg-blue', (!isset($course_count) || !isset($count) ? 'disabled' : ''),  'div' => 'false')); ?>
					<?php
				} ?>
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

	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

    $(document).ready(function() {
        $('#registerIndivdualCourse').click(function() {
			var checkboxes = document.querySelectorAll('input[type="checkbox"]');
			var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

			if (!checkedOne) {
				alert('At least one published course must be selected to maintain registration.');
				validationMessageNonSelected.innerHTML = 'At least one published course must be selected to maintain registration.';
				return false;
			}

            //return confirm('Are you sure you want to cancel the registration of the selected published course(s) form the selected section students?? Use this option if and only if the the registartions for the whole section students is wrong or needs correction. If the selected courses are no more needed after registration cancellation, communicate the the department to also delete/unpublish the courses so that section upgrade can run smoothly at the end of the semester.');
        });
    });

	var form_being_submitted = false;

	var checkForm = function(form) {
		/* var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

		if (!checkedOne) {
			validationMessageNonSelected.innerHTML = 'At least one published course must be selected to maintain registration.';
			return false;
		} */
	
		if (form_being_submitted) {
			alert("Registering Selected Courses, please wait a moment...");
			form.registerIndivdualCourse.disabled = true;
			return false;
		}

        form.registerIndivdualCourse.value = 'Registering Selected Courses...';
		form_being_submitted = true;
		return true; 
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>