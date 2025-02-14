<?php 
if (isset($coursesDrop) && !empty($coursesDrop)) { 
	$have_courses_to_drop = 0;
	$on_hold_registration_count = 0;
	?>

	<h6 id="validation-message_non_selected" class="text-red fs14"></h6>

	<div style="overflow-x:auto;">
		<table id='fieldsForm' cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th class="center"></th>
					<th class="center">#</th>
					<th class="vcenter">Course Title</th>
					<th class="center">Course Code</th>
					<th class="center">Lecture</th>
					<th class="center">Tutorial</th>
					<th class="center">Lab</th>
					<th class="center"><?= (isset($coursesDrop[0]['PublishedCourse']['Course']['Curriculum']['type_credit']) && count(explode('ECTS', $coursesDrop[0]['PublishedCourse']['Course']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$count = 0;

				foreach ($coursesDrop as $pk => $pv) {

					$style = " class= 'accepted'";

					echo $this->Form->hidden('CourseDrop.' . $count . '.course_registration_id', array('value' => $pv['CourseRegistration']['id']));
					echo $this->Form->hidden('CourseDrop.' . $count . '.academic_year', array('value' => $pv['CourseRegistration']['academic_year']));
					echo $this->Form->hidden('CourseDrop.' . $count . '.semester', array('value' => $pv['CourseRegistration']['semester']));
					echo $this->Form->hidden('CourseDrop.' . $count . '.student_id', array('value' => $pv['CourseRegistration']['student_id']));

					if (!empty($pv['CourseRegistration']['year_level_id'])) {
						echo $this->Form->hidden('CourseDrop.' . $count . '.year_level_id', array('value' => $pv['CourseRegistration']['year_level_id']));
					} else {
						echo $this->Form->hidden('CourseDrop.' . $count . '.year_level_id', array('value' => 0));
					}

					if ($pv['PublishedCourse']['drop'] == 1) {
						echo $this->Form->hidden('CourseDrop.' . $count . '.forced', array('value' => 1));
						echo $this->Form->hidden('CourseDrop.' . $count . '.department_approval', array('value' => 1));
						echo $this->Form->hidden('CourseDrop.' . $count . '.registrar_confirmation', array('value' => 1));
					}

					if (isset($already_dropped) && is_array($already_dropped) && in_array($pv['CourseRegistration']['id'], $already_dropped)) { ?>
						<tr class='linethough'>
							<td class="center"><div style="margin-left: 40%;"><?= $this->Form->checkbox('CourseRegistration.drop.' . $pv['CourseRegistration']['id'], array('disabled' => in_array($pv['CourseRegistration']['id'], $already_dropped) ? true : false)); ?></div></td>
							<td class="center"><?= ++$count; ?></td>
							<td class="vcenter"><?= $pv['PublishedCourse']['Course']['course_title']; ?></td>
							<?php
					} else {

						$have_courses_to_drop++;

						if ($pv['PublishedCourse']['drop'] == 1) {
							$style = ' class="exempted"';
						} else {
							if (empty($pv['CourseRegistration']['type'])) {
								$style = ' class="accepted"';
							} else if ($pv['CourseRegistration']['type'] == 11 || $pv['CourseRegistration']['type'] == 12 || $pv['CourseRegistration']['type'] == 13) {
								$style = ' class="rejected"';
								$on_hold_registration_count++;
							}

						} ?>

						<tr <?= $style; ?> >
							<td class="center"><div style="margin-left: 40%;"><?= $this->Form->checkbox('CourseRegistration.drop.' . $pv['CourseRegistration']['id']); ?></div></td>
							<td class="center"><?= ++$count; ?></td>
							<td class="vcenter"><?= $pv['PublishedCourse']['Course']['course_title']; ?></td>
							<?php

					} ?>
					
							<td class="center"><?= $pv['PublishedCourse']['Course']['course_code']; ?></td>
							<td class="center"><?= $pv['PublishedCourse']['Course']['lecture_hours']; ?></td>
							<td class="center"><?= $pv['PublishedCourse']['Course']['tutorial_hours']; ?></td>
							<td class="center"><?= $pv['PublishedCourse']['Course']['laboratory_hours']; ?></td>
							<td class="center"><?= $pv['PublishedCourse']['Course']['credit']; ?></td>
						</tr>
						<?php
					
				} ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan=8 class="vcenter" style="font-weight: normal;">
						Important Note: 
						<ol>
							<li>The underline courses are already dropped courses.</li>
							<li>Green marked courses are courses you are NOT advised to drop.</li>
							<li>Blue marked courses are courses published as drop course by the department to drop, you are advised to drop.</li>
							<?php
							if ($on_hold_registration_count) { ?>
								<li style='font-size:16px;color:red'>Red marked courses are courses you are not elegible to register since you not fullfilled prerequisite course requirement or academic status requirement, Drop these courses.</li>
								<?php
							} ?>
						</ol>
					</td>
				</tr>
			</tfoot>
        </table>
	</div>

	<?php
	if ($count != count($already_dropped) && $have_courses_to_drop) {
		echo '<hr>' . $this->Form->Submit('Drop Selected', array('id' => 'SubmitButton', 'div' => false, 'class' => 'tiny radius button bg-blue', 'name' => 'drop'));
	}      
} ?>

<script type="text/javascript">

    var form_being_submitted = false;

    var have_courses_to_drop = <?= $have_courses_to_drop; ?>;

	var registrar_role = <?= ($this->Session->read('Auth.User')['role_id'] != ROLE_STUDENT ? 1 : 0 ); ?>;

	//alert(registrar_role);

	var student_id = $('#studentNumber').val();

    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	$('#SubmitButton').click(function() {

		// turn off required fields from search filters
		$('input[name*="data[Student]"], select[name*="data[Student]"]').each(function() {
			//$(this).val(''); // Set their values to empty
			$(this).removeAttr('required');
		});

        if (have_courses_to_drop) {
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

            if (!checkedOne) {
                alert('At least one course must be selected to drop.');
                validationMessageNonSelected.innerHTML = 'At least one course must be selected to drop.';
                return false;
            }
        }

		if (form_being_submitted) {
			alert('Course Drop in progress, please wait a moment or refresh your page...');
			$('#SubmitButton').attr('disabled', true);
			return false;
		}

		if (registrar_role) {
			var confirmm = confirm('You are about to DROP the selected courses from ' + student_id + ' and your decision is final. Are you sure you want to drop the selected courses?');
		} else {
			var confirmm = true;
		}
		

		if (!form_being_submitted && have_courses_to_drop && confirmm) {
			$('#SubmitButton').val('Dropping Selected Courses...');
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