<?php
if ($this->Session->read('candidate_publish_courses')) {

	$coursesss = $this->Session->read('candidate_publish_courses');
	$taken_courses_allow_to_publishe_it = $this->Session->read('taken_courses_allow_to_publishe_it');
	$selected_section = $this->Session->read('selected_section');
	$published_courses_disable_not_to_published = $this->Session->read('published_courses_disable_not_to_published');

	if (!empty($coursesss)) {
		$display_button = 0;
		$section_count = 0;

		$enable_publish_button = 1;
		$enable_publish_as_add_button = 1;

		//debug($coursesss);

		echo '<br>';
		foreach ($coursesss as $section_id => $coursss) {
			$section_count++;
			if (!empty($coursss)) { ?>

				<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
				<br>

				<div style="overflow-x:auto;">
					<table id='fieldsForm' cellpadding="0" cellspacing="0" class="table">
						<thead>
							<tr>
								<th colspan=8><?= 'Section: ' . $selected_section[$section_id]; ?></td>
							</tr>
							<tr>
								<th colspan=8>Select the course you want to publish</td>
							</tr>
							<tr>
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
							$count = 0;
							foreach ($coursss as $kc => $vc) { 

								//debug($vc);
								//debug($coursesss[$section_id][$kc]['PublishedCourse'][$count]);
								//debug($coursesss[$section_id][$kc]);
								//debug($coursesss[$section_id][$kc]['Course']['id']);
								//debug($coursesss[$section_id][$kc]['PublishedCourse'][$count]['CourseRegistration'][$count]);
								//debug($coursesss[$section_id][$kc]['PublishedCourse'][$count]['CourseAdd'][$count]);
								/* if (isset($coursesss[$section_id][$kc]['Course']['id']) && isset($coursesss[$section_id][$kc]['PublishedCourse'][$count]['Course']['id']) && $coursesss[$section_id][$kc]['PublishedCourse'][$count]['Course']['id'] == $coursesss[$section_id][$kc]['Course']['id'] && (!empty($coursesss[$section_id][$kc]['PublishedCourse'][$count]['CourseRegistration'][$count]) || !empty($coursesss[$section_id][$kc]['PublishedCourse'][$count]['CourseAdd'][$count]))) {
									// have registration or add
									$enable_publish_as_add_button++;
								} */

								/* if (isset($coursesss[$section_id][$kc]['Course']['id']) && isset($coursesss[$section_id][$kc]['PublishedCourse'][$count]['Course']['id']) && $coursesss[$section_id][$kc]['PublishedCourse'][$count]['Course']['id'] == $coursesss[$section_id][$kc]['Course']['id'] && empty($coursesss[$section_id][$kc]['PublishedCourse'][$count]['CourseRegistration'][$count]) && empty($coursesss[$section_id][$kc]['PublishedCourse'][$count]['CourseAdd'][$count])) {
									//no registration or add
									$enable_publish_button++;
								}  */?>
								<tr>
									<?php
									if (isset($published_courses_disable_not_to_published[$section_id]) && in_array($vc['Course']['id'], $published_courses_disable_not_to_published[$section_id])) { ?>
										<td class="center">**</td>
										<?php
									} else { ?>
										<td class="center"><?= $this->Form->checkbox('Course.' . $section_id . '.' . $vc['Course']['id']); ?></td>
										<?php
									} ?>
									<td class="center"><?= ++$count; ?></td>
									<td class="vcenter"><?= $vc['Course']['course_title']; ?></td>
									<td class="center"><?= $vc['Course']['course_code']; ?></td>
									<td class="center"><?= $vc['Course']['credit']; ?></td>
									<td class="center"><?= $vc['Course']['lecture_hours']; ?></td>
									<td class="center"><?= $vc['Course']['tutorial_hours']; ?></td>
									<td class="center"><?= $vc['Course']['laboratory_hours']; ?></td>
									<?php
									if (isset($published_courses_disable_not_to_published[$section_id]) && in_array($vc['Course']['id'], $published_courses_disable_not_to_published[$section_id])) {
										//find the publish course id
										foreach ($published_courses_disable_not_to_published[$section_id] as $p_id => $p_course_id) {
											if ($p_course_id == $vc['Course']['id']) {
												$published_id = $p_id;
												break 1;
											}
										}
									} 
									//$count++; ?>
								</tr>
								<?php
							} ?>
						</tbody>
						<?php
						if (isset($published_courses_disable_not_to_published[$section_id]) && count($published_courses_disable_not_to_published[$section_id]) > 0) { ?>
							<tfoot>
								<tr>
									<td colspan=2>**</td>
									<td colspan=6 style="font-weight: normal;">Courses marked ** are are already published for the section.</td>
								</tr>
							</tfoot>
							<?php
						} ?>
					</table>
				</div>
				<br>
				<?php
			} else {
				$display_button++;
			}
		}  ?>

		<div class="row">
			<div class="large-12 columns">
				<hr>
				<?php
				if ($display_button != $section_count) { ?>

					<div class="large-4 columns">
						<?= ($enable_publish_button ? $this->Form->submit('Publish Selected Courses', array('name' => 'publishselected', 'id' => 'publishselected', 'div' => 'false', 'class' => 'tiny radius button bg-blue')) : ''); ?>
					</div>
					<div class="large-8 columns">
						<?= (ALLOW_PUBLISH_AS_ADD_COURSE_FOR_COLLEGE_ROLE && $enable_publish_as_add_button  ? $this->Form->submit('Publish Selected as Mass Add', array('name' => 'publishselectedadd', 'id' => 'publishSelectedAsAdd', 'class' => 'tiny radius button bg-red', 'div' => 'false')) : ''); ?>
					</div>
					<?php 
				} else { ?>
					<h6 class="fs14 text-gray">It seems there is no course in selected curriculum. You need to define courses under the curriculum before publishing it.</h6>
					<?php
				} ?>
			</div>
		</div>

		<?php
		/* if (!empty($taken_courses_allow_to_publishe_it) && count($taken_courses_allow_to_publishe_it) > 0) { ?>
			<div class='smallheading'>Already taken coures of the selected section, you can check it the already taken courses to allow students to register for the courses again. This happens when all students fail the course or not able to follow the course</div>
			<?php
			foreach ($taken_courses_allow_to_publishe_it as $section_id => $coursss) {
				if (!empty($coursss)) { ?>
					<table id='fieldsForm'>
						<thead>
							<tr>
								<th colspan=7><?php echo "Section: " . $selected_section[$section_id]; ?></td>
							</tr>
							<tr>
								<th class="center">&nbsp;</th>
								<th class="center">#</th>
								<th class="vcenter">Course Title</th>
								<th class="center">Course Code</th>
								<th class="center">Lecture hour</th>
								<th class="center">Tutorial hour</th>
								<th class="center">Credit</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							foreach ($coursss as $kc => $vc) { ?>
								<tr>
									<td class="center"><?= $this->Form->checkbox('Course.' . $section_id . '.' . $vc['Course']['id']); ?></td>
									<td class="center"><?= $count++; ?></td>
									<td class="vcenter"><?= $vc['Course']['course_title']; ?></td>
									<td class="center"><?= $vc['Course']['course_code']; ?></td>
									<td class="center"><?= $vc['Course']['lecture_hours']; ?></td>
									<td class="center"><?= $vc['Course']['tutorial_hours']; ?></td>
									<td class="center"><?= $vc['Course']['credit']; ?></td>
								</tr>
								<?php
							} ?>
						</tbody>
					</table>
					<?php
				}
			}
		} */
	}

}

echo $this->Form->end(); ?>

<script type="text/javascript">

	var form_being_submitted = false;

    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	$('#publishSelectedAsAdd').click(function() {

		//var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		//var chckboxs = document.querySelectorAll('input[type="checkbox"]:checked');
		var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="data[Course]"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
		var chckboxs = document.querySelectorAll('input[type="checkbox"][name^="data[Course]"]:checked');

		if (!checkedOne || chckboxs.length == 0) {
			alert('At least one course must be selected to publish as mass add.');
			validationMessageNonSelected.innerHTML = 'At least one course must be selected to publish as mass add.';
			return false;
		}

		if (form_being_submitted) {
			alert("Publishing Selected as Mass Add, please wait a moment...");
			$('#publishSelectedAsAdd').prop('disabled', true);
			return false;
		}

		var confirmmed = confirm('Are you sure you want to publish the selected courses as Mass Add for the selected section? Use this option if and only if there is a previous course publication for the section using the same academic year and semester with section students already registerd for the courses or you are unable to publish the courses using Publish Selected option, i.e. if you forgot to to publish the courses for the section in the given academic year and semester or the students are taking the courses as a block course.');

		if (confirmmed) {
			$('#publishSelectedAsAdd').val('Publishing as Mass Add...');
			form_being_submitted = true;
			return true;
		} else {
			return false;
		}
	});

	$('#publishselected').click(function() {

		//var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		//var chckboxs = document.querySelectorAll('input[type="checkbox"]:checked');
		var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="data[Course]"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
		var chckboxs = document.querySelectorAll('input[type="checkbox"][name^="data[Course]"]:checked');


		if (!checkedOne || chckboxs.length == 0) {
			alert('At least one course must be selected to publish.');
			validationMessageNonSelected.innerHTML = 'At least one course must be selected to publish.';
			return false;
		}

		if (form_being_submitted) {
			alert("Publishing Selected Courses, please wait a moment...");
			$('#publishSelected').prop('disabled', true);
			return false;
		}

		$('#publishSelected').val('Publishing Selected Courses...');
		form_being_submitted = true;
		return true;

	});

    if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>