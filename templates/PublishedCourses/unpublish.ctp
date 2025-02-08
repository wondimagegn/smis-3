<?= $this->Form->create('PublishedCourse', array('onSubmit' => 'return checkForm(this);')); ?>
<?= $this->Html->script('jquery-selectall'); ?>
<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-minus" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Unpublish or Delete Courses from Publish Courses List'); ?></span>
        </div>
    </div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div class="publishedCourses form">
					<?php
					if (!isset ($turn_off_search)) { ?>
						<div style="margin-top: -30px;">
							<hr>
							<fieldset style="padding-bottom: 5px; padding-top: 15px;">
								<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
								<div class="row">
									<div class="large-2 columns">
										<?= $this->Form->input('PublishedCourse.academic_year', array('label' => 'Academic Year: ', 'type' => 'select', 'style' => 'width:90%;', 'options' => $acyear_array_data, /* 'empty' => '[ Select Academic Year ]', */ 'default' => isset ($defaultacademicyear) ? $defaultacademicyear : '')); ?>
									</div>
									<div class="large-2 columns">
										<?= $this->Form->input('PublishedCourse.semester', array('label' => 'Semester: ', 'options' => Configure::read('semesters'), 'required', 'empty' => '[ Select semester ]', 'style' => 'width:90%;')); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('PublishedCourse.program_id', array('label' => 'Program: ', 'required', 'empty' => '[ Select Program ]', 'style' => 'width:90%;')); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('PublishedCourse.program_type_id', array('label' => 'Program Type: ', 'required', 'empty' => '[ Select Program Type ]', 'style' => 'width:90%;')); ?>
									</div>
									<div class="large-2 columns">
										<?= $this->Form->input('PublishedCourse.year_level_id', array('label' => 'Year Level: ', 'required', 'empty' => '[ Select Year Level ]', 'style' => 'width:90%;')); ?>
									</div>
								</div>
								<hr>
								<?= $this->Form->submit('Continue', array('name' => 'getsection', 'class' => 'tiny radius button bg-blue', 'div' => 'false')); ?> 
							</fieldset>
						</div>
						<?php
					} ?>

					<?php
					if (isset($show_unpublish_page)) {
						if (!empty ($publishedcourses)) {
							//debug($publishedcourses);
							// echo $this->Form->input('PublishedCourse.publish_up',array('label'=>'Publish Start')); ?>
							<div style="margin-top: -30px;">
								<hr>
								<h6 class='tf16 text-gray'>Select the course you want to unpublish/publish as drop course</h6>

								<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
								<br>

								<div style="overflow-x:auto;">
									<table id='fieldsForm' cellpadding="0" cellspacing="0" class="table">
										<tbody>
											<?php
											foreach ($publishedcourses as $section_name => $sectioned_published_courses) { ?>
												<tr><td colspan=9 class="vcenter" style="border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);"><h6><?= $section_name . ' (' . (!empty($sectioned_published_courses[0]['Section']['YearLevel']['name']) ? $sectioned_published_courses[0]['Section']['YearLevel']['name'] : ($sectioned_published_courses[0]['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', '.  $sectioned_published_courses[0]['Section']['academicyear'] . ', ' . (isset($sectioned_published_courses[0]['PublishedCourse']['semester']) ? ($sectioned_published_courses[0]['PublishedCourse']['semester'] == 'I' ? '1st Semester' : ($sectioned_published_courses[0]['PublishedCourse']['semester'] == 'II' ? '2nd Semester' : ($sectioned_published_courses[0]['PublishedCourse']['semester'] == 'III' ? '3rd Semester' : /* $sk */ $sectioned_published_courses[0]['PublishedCourse']['semester'] . ' Semester'))) : (isset($publishedCourse['semester']) ? $publishedCourse['semester'] :  '')) . ')'; ?></h6></td></tr>
												<tr>
													<?php
													//echo "<th style='padding:0'>Check All/Uncheck All <br/>".$this->Form->checkbox("SelectAll", array('id' => 'select-all','checked'=>''))."</th>";
													//echo '<th style="padding:0"> Check All/Uncheck All <br/>' . $this->Form->checkbox(null, array('id' => 'select-all')) . '</th>';
													?>
													<th class="center">&nbsp;</th>
													<th class="center">#</th>
													<th class="vcenter">Course Title</th>
													<th class="center">Course Code</th>
													<th class="center">Lecture hour</th>
													<th class="center">Tutorial hour</th>
													<th class="center"><?= (isset($sectioned_published_courses[0]['Course']['Curriculum']['type_credit']) ? (count(explode('ECTS', $sectioned_published_courses[0]['Course']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit') : (isset($sectioned_published_courses[0]['Section']['Curriculum']['type_credit']) ? (count(explode('ECTS', $sectioned_published_courses[0]['Section']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit') : 'Credit')) ?></th>
													<th class="center">Year</th>
													<th class="center">Sem</th>
												</tr>
												<?php
												$count = 1;
												$course_registered_only = 0;
												foreach ($sectioned_published_courses as $kc => $vc) { 
													$red = null;

													if (isset ($courses_not_allowed[$vc['PublishedCourse']['section_id']]) && in_array($vc['Course']['id'], $courses_not_allowed[$vc['PublishedCourse']['section_id']])) {
														$red = 'class="redrow"';
													} ?>

													<tr <?= $red; ?>>
														<?php
														//echo '<td>'.$this->Form->checkbox('PublishedCourse.unpublish.'.$vc['Course']['id']).'</td>';
														if ($vc['PublishedCourse']['unpublish_readOnly']) { ?>
															<td class="center">**</td>
															<?php
															$course_registered_only++;
														} else { ?>
															<td class="center"><?= $this->Form->checkbox('Course.pub.' . $vc['PublishedCourse']['section_id'] . '.' . $vc['Course']['id']); ?></td>
															<?php
														} ?>
														<td class="center"><?= $count; ?></td>
														<td class="vcenter"><?= $vc['Course']['course_title']; ?></td>
														<td class="center"><?= $vc['Course']['course_code']; ?></td>
														<td class="center"><?= $vc['Course']['lecture_hours']; ?></td>
														<td class="center"><?= $vc['Course']['tutorial_hours']; ?></td>
														<td class="center"><?= $vc['Course']['credit']; ?></td>
														<td class="center"><?= $vc['YearLevel']['name']; ?></td>
														<td class="center"><?= $vc['Course']['semester']; ?></td>
													</tr>
													<?php
													$count++;
												}

												//if ($course_registered_only > 0) { ?>
													<!-- <tr>
														<td>&nbsp;</td>
														<td class="vcenter" colspan=9>Those courses with ** are not  allowed to unpublished since students has already registered or grade has been submitted.</td>
													</tr> -->
													<?php
												//} ?>
												<?php
											} ?>
										</tbody>
									</table>
								</div>
								<br>

								<?php
								if ($course_registered_only > 0) { ?>
									<h6 class="text-red fs14"> ** marked courses are not allowed to delete. Students are already registered for the course or grade submission has been started. You can try Mass dropping such courses if grade is not submitted.</h6>		
									<?php
								} 
								
								if ($course_registered_only != ($count - 1)) { ?>
									<hr>

									<div class="row">
										<div class="large-3 columns">
											<?= $this->Form->submit('Delete Selected', array('name' => 'deleteselected', 'id' => 'deleteselected', 'class' => 'tiny radius button bg-blue', 'div' => 'false')); ?>
										</div>
										<div class="large-3 columns">
											<?=  $this->Form->submit('Publish Selected as Mass Drop',array('name'=>'dropselected', 'id'=>'dropselected', 'class'=>'tiny radius button bg-red','div'=>'false')); ?>
										</div>
										<div class="large-6 columns">
											&nbsp;
										</div>
									</div>
									<?php
								} ?>
							</div>
							<?php
						} else { ?>
							<div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">No active section with a course publication is found in the given criteria to unpublish courses or to mass drop.</i></div>
							<?php
						}
					}  ?>
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

    $(document).ready(function() {
        $('#dropselected').click(function() {
			var checkboxes = document.querySelectorAll('input[type="checkbox"]');
			var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

			if (!checkedOne) {
				alert('At least one course must be selected to publish as mass drop.');
				validationMessageNonSelected.innerHTML = 'At least one course must be selected to publish as mass drop.';
				return false;
			}

            return confirm('Are you sure you want to publish the selected course(s) as Mass Drop for the selected section?? Use this option if and only if the students are registered for published course and registrar can\'t cancel the students registration normally or  you can\'t use Delete Selected Option to delete the selected published courses.');
        });
    });

	var form_being_submitted = false;

	var checkForm = function(form) {
		var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

		if (!checkedOne) {
			validationMessageNonSelected.innerHTML = 'At least one course must be selected to unpiblish or to publish as mass drop.';
			return false;
		}
	
		if (form_being_submitted) {
			alert("Processing your request, please wait a moment...");
			form.deleteselected.disabled = true;
            form.dropselected.disabled = true;
			return false;
		}

		form_being_submitted = true;
		return true; 
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>
<?= $this->Js->writeBuffer(); ?>