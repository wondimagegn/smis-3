<?php echo $this->Html->script('jquery-selectall'); ?>
<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Unpublish or Delete Semester Courses Form Pre/Freshman/Remedial'); ?></span>
        </div>
    </div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<?= $this->Form->create('PublishedCourse'); ?>

				<div class="publishedCourses form">
					<div style="margin-top: -30px;">
					<hr>
						<?php
						if (!isset($turn_off_search)) { ?>
							<fieldset style="padding-bottom: 5px; padding-top: 15px;">
                                <!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
                                <div class="row">
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('PublishedCourse.academic_year', array('label' => 'Academic Year: ', 'type' => 'select', 'options' => $acyear_array_data, 'empty' => "[ Select Academic Year ]", 'default' => (isset($defaultacademicyear) ? $defaultacademicyear : '') , 'style' => 'width:90%;', 'required' => 'required')); ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('PublishedCourse.semester', array('label' => 'Semester: ', 'options' => Configure::read('semesters'), 'empty' => '[ Select Semester ]', 'style' => 'width:90%;', 'required' => 'required')); ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('PublishedCourse.program_id', array('label' => 'Program: ', 'empty' => "[ Select Program ]", 'style' => 'width:90%;', 'required' => 'required')); ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('PublishedCourse.program_type_id', array('label' => 'Program Type: ', 'options' => $programTypess, 'empty' => "[ Select Program Type ]", 'style' => 'width:90%;', 'required' => 'required')); ?>
                                    </div>
                                </div>
                                <hr>
                                <?= $this->Form->submit('Continue', array('name' => 'getsection', 'div' => 'false', 'id' => 'disabled_publish', 'class' => 'tiny radius button bg-blue')); ?>
                            </fieldset>
							<?php
						} ?>
					</div>

					<?php
					if (isset($show_unpublish_page)) {
						if (!empty($publishedcourses)) {

							$enable_delete_button = 0;
							$enable_publish_as_drop_button = 0;
							//debug($publishedcourses);

							foreach ($publishedcourses as $section_name => $sectioned_published_courses) { ?>

								<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
								<br>

								<div style="overflow-x:auto;">
									<table id='fieldsForm' cellpadding="0" cellspacing="0" class="table">
										<thead>
											<tr>
												<th colspan=8><?= 'Section: ' . $section_name; ?></td>
											</tr>
											<tr>
												<th colspan=8>Select the course(s) you want to unpublish/delete<?= (ALLOW_PUBLISH_AS_DROP_COURSE_FOR_COLLEGE_ROLE ?  ' or puublish as mass drop.' : '.'); ?></td>
											</tr>
											<tr>
											<?php
												//echo "<th style='padding:0'>Check All/Uncheck All <br/>".$this->Form->checkbox("SelectAll", array('id' => 'select-all','checked'=>''))."</th>";
												//echo '<th style="padding:0"> Check All/Uncheck All <br/>' . $this->Form->checkbox(null, array('id' => 'select-all')) . '</th>'; ?>
												<th class="center">&nbsp;</th>
												<th class="center">#</th>
												<th class="vcenter">Course Title</th>
												<th class="center">Course Code</th>
												<th class="center">Credit</th>
												<th class="center">Lecture hour</th>
												<th class="center">Tutorial hour</th>
												<th class="center">Lab hour</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$count = 1;
											$course_registered_only = 0;
											foreach ($sectioned_published_courses as $kc => $vc) { 
												$red = null; 
												if (isset($courses_not_allowed[$vc['PublishedCourse']['section_id']]) && in_array($vc['Course']['id'], $courses_not_allowed[$vc['PublishedCourse']['section_id']])) { 
													$red = 'style="color:red;"';
												} 

												if (!($vc['PublishedCourse']['unpublish_readOnly']) && $vc['PublishedCourse']['have_course_registration_or_add']) {
													// have registration or add but no grade submission
													$enable_publish_as_drop_button++;
												}

												if (!($vc['PublishedCourse']['unpublish_readOnly']) && !($vc['PublishedCourse']['have_course_registration_or_add'])) {
													// no grade submission and no registration or add
													$enable_delete_button++;
												} ?>
												<tr <?= $red; ?>>
													<?php
													//echo '<td>'.$this->Form->checkbox('PublishedCourse.unpublish.'.$vc['Course']['id']).'</td>';
													if ($vc['PublishedCourse']['unpublish_readOnly']) {
														echo '<td class="center">**</td>';
														$course_registered_only++;
													} else {
														echo '<td class="center">' . $this->Form->checkbox('Course.pub.' . $vc['PublishedCourse']['section_id'] . '.' . $vc['Course']['id']) . '</td>';
													} ?>
													<td class="center"><?= $count++; ?></td>
													<td class="vcenter"><?= $vc['Course']['course_title']; ?></td>
													<td class="center"><?= $vc['Course']['course_code']; ?></td>
													<td class="center"><?= $vc['Course']['credit']; ?></td>
													<td class="center"><?= $vc['Course']['lecture_hours']; ?></td>
													<td class="center"><?= $vc['Course']['tutorial_hours']; ?></td>
													<td class="center"><?= $vc['Course']['laboratory_hours']; ?></td>
												</tr>
												<?php
											} ?>
										</tbody>
										<?php
										if ($course_registered_only > 0) { ?>
											<tfoot>
												<tr>
													<td colspan=2>**</td>
													<td colspan=6 style="font-weight: normal;">Courses marked ** are not allowed to unpublish since students already registered or grade has been submitted.</td>
												</tr>
											</tfoot>
											<?php
										} ?>
									</table>
								</div>
								<br>
								<?php
							} ?>

							<hr>
							<div class="row">
								<div class="large-4 columns">
									<?= ($enable_delete_button ? $this->Form->submit('Delete Selected', array('name' => 'deleteselected', 'id' => 'deleteSelected', 'div' => 'false', 'class' => 'tiny radius button bg-blue')) : ''); ?>
								</div>
								<div class="large-8 columns">
									<?= (ALLOW_PUBLISH_AS_DROP_COURSE_FOR_COLLEGE_ROLE && $enable_publish_as_drop_button ? $this->Form->submit('Publish Selected as Mass Drop', array('name' => 'dropselected', 'id' => 'publishSelectedAsMassDrop', 'div' => 'false', 'class' => 'tiny radius button bg-red')) : ''); ?>
								</div>
								<?php
								if (!$enable_delete_button && !$enable_publish_as_drop_button) {

								} ?>
							</div>
							<?php
						} else { ?>
							<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No published courses are found to unpublish or drop with the given search criteria.</div>
							<?php
						}
					} ?>

					<?= $this->Form->end(); ?> 
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	var form_being_submitted = false;

    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');
	
	$('#publishSelectedAsMassDrop').click(function() {

		var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
		var chckboxs = document.querySelectorAll('input[type="checkbox"]:checked');

		if (!checkedOne) {
			alert('At least one course must be selected to publish as mass drop.');
			validationMessageNonSelected.innerHTML = 'At least one course must be selected to publish as mass drop.';
			return false;
		}

		if (form_being_submitted) {
			alert("Publishing Selected Courses as Mass Add, please wait a moment...");
			$('#publishSelectedAsAdd').prop('disabled', true);
			return false;
		} 

		var confirmmed = confirm('Are you sure you want to Mass drop the selected course(s) for the selected section?? Use this option if and only if you are unable to delete the course(s) using Delete Selected option or if one or more students are registered for the course.');

		if (confirmmed) {
			$('#publishSelectedAsAdd').val('Publishing as Mass Drop...');
			form_being_submitted = true;
			return true;
		} else {
			return false;
		}

	});

	$('#deleteSelected').click(function() {

		var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
		var chckboxs = document.querySelectorAll('input[type="checkbox"]:checked');

		if (!checkedOne) {
			alert('At least one course must be selected to delete a published course.');
			validationMessageNonSelected.innerHTML = 'At least one course must be selected to delete a published course.';
			return false;
		}

		if (form_being_submitted) {
			alert("Deleting Selected Courses, please wait a moment...");
			$('#deleteSelected').prop('disabled', true);
			return false;
		}

		var confirmmed =  confirm('Are you sure you want to delete the selected course(s) for the selected section?');

		if (confirmmed) {
			$('#deleteSelected').val('Deleting Selected Courses...');
			form_being_submitted = true;
			return true;
		} else {
			return false;
		}

	});

    if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>