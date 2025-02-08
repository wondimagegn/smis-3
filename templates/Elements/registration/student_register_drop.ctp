<?php  
//debug($student_section['CourseRegistration']);
if (isset($coursesDrop) && !empty($coursesDrop)) {
	//debug($published_courses); ?>
	<div style="overflow-x:auto;">
        <table id='fieldsForm' cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th class="center">#</th>
					<th class="center">&nbsp;</th>
					<th class="center">Course Title </th>
					<th class="center">Course Code </th>
					<th class="center">Lecture</th>
					<th class="center">Tutorial</th>
					<th class="center">Lab</th>
					<th class="center"><?= isset($coursesDrop[0]['Course']['Curriculum']['type_credit']) && (count(explode('ECTS', $coursesDrop[0]['Course']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$count = 0;

				foreach ($coursesDrop as $pk => $pv) {

					echo $this->Form->hidden('CourseDrop.' . $count . '.course_registration_id', array('value' => $pv['CourseRegistration']['id']));
					echo $this->Form->hidden('CourseDrop.' . $count . '.academic_year', array('value' => $pv['CourseRegistration']['academic_year']));
					echo $this->Form->hidden('CourseDrop.' . $count . '.semester', array('value' => $pv['CourseRegistration']['semester']));
					echo $this->Form->hidden('CourseDrop.' . $count . '.student_id', array('value' => $pv['CourseRegistration']['student_id']));
					echo $this->Form->hidden('CourseDrop.' . $count . '.year_level_id', array('value' => $pv['CourseRegistration']['year_level_id']));

					if (isset($already_dropped) && is_array($already_dropped) && !empty($already_dropped) && is_array($already_dropped) && in_array($pv['CourseRegistration']['id'], $already_dropped)) { ?>
						<tr class='linethough'>
							<td class="center"><?= ++$count; ?></td>
							<td class="center"><div style="margin-left: 40%;"><?= $this->Form->checkbox('CourseRegistration.drop.' . $pv['CourseRegistration']['id'], array('disabled' => in_array($pv['CourseRegistration']['id'], $already_dropped) ? true : false)); ?></div></td>
							<td class="center"><?= $pv['PublishedCourse']['Course']['course_title']; ?></td>
							<?php
					} else { ?>
						<tr>
							<td class="center"><?= ++$count; ?></td>
							<td class="center"><div style="margin-left: 40%;"><?= $this->Form->checkbox('CourseRegistration.drop.' . $pv['CourseRegistration']['id']); ?></div></td>
							<td class="center"><?= $pv['PublishedCourse']['Course']['course_title']; ?></td>
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
			<?php
			if (isset($already_dropped) && !empty($already_dropped)) { ?>
				<tfoot>
					<tr>
						<td colspan=8 class="vcenter" style="font-weight: normal;">
							Important Note: The uderline courses has already dropped.
						</td>
					</tr>
				</tfoot>
				<?php
			} ?>
		</table>
	</div>
	<?php
	if ($count != count($already_dropped)) {
		echo "<hr>" . $this->Form->Submit('Drop Selected', array('div' => false, 'class' => 'tiny radius button bg-blue', 'name' => 'drop'));
	}
}

if (isset($published_courses) && !empty ($published_courses)) { 
	$elective_courses_count = 0;
    $on_hold_registration_count = 0;
	?>
    <div style="overflow-x:auto;">
		<table id='fieldsForm' cellspacing="0" cellpadding="0" class="table">
			<thead>
				<tr>
					<th class="center">#</th>
					<th class="vcenter">Course Title</th>
					<th class="center">Course Code</th>
					<th class="center">Lecture</th>
					<th class="center">Tutorial</th>
					<th class="center">Lab</th>
					<th class="center"><?= (isset($published_courses[0]['Course']['Curriculum']['type_credit']) && count(explode('ECTS', $published_courses[0]['Course']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$count = 1;
				foreach ($published_courses as $pk => $pv) {
					/** allow registration without passing prerequiste but the registration  */
					$style = " class='accepted'";

					// normal registration 
					if (!isset($pv['prequisite_taken_passsed']) && !isset($pv['exemption'])) {
						echo $this->Form->hidden('CourseRegistration.' . $count . '.published_course_id', array('value' => $pv['PublishedCourse']['id']));
						echo $this->Form->hidden('CourseRegistration.' . $count . '.course_id', array('value' => $pv['Course']['id']));
						echo $this->Form->hidden('CourseRegistration.' . $count . '.semester', array('value' => $pv['PublishedCourse']['semester']));
						echo $this->Form->hidden('CourseRegistration.' . $count . '.academic_year', array('value' => $pv['PublishedCourse']['academic_year']));
						echo $this->Form->hidden('CourseRegistration.' . $count . '.student_id', array('value' => $student_section['Student']['id']));
						echo $this->Form->hidden('CourseRegistration.' . $count . '.section_id', array('value' => $student_section['Section'][0]['id']));
						echo $this->Form->hidden('CourseRegistration.' . $count . '.year_level_id', array('value' => $student_section['Section'][0]['year_level_id']));

						if (isset($pv['PublishedCourse']['elective']) && !empty($pv['PublishedCourse']['elective']) && $pv['PublishedCourse']['elective'] == 1) {
							echo $this->Form->hidden('CourseRegistration.' . $count . '.elective_course', array('value' => 1));
							$elective_courses_count++;
						}
					}

					if (isset($pv['prequisite_taken_passsed']) && $pv['prequisite_taken_passsed'] == 0) {
						$style = ' class="rejected"';
					}

					if (isset($pv['exemption']) && $pv['exemption'] == 1) {
						$style = ' class="exempted"';
					}

					// type of registration
					if ((isset($pv['registration_type']) && $pv['registration_type'] == 2 && !isset($pv['exemption']))) {
						$style = ' class="on-process"';
						$on_hold_registration_count++;
						echo $this->Form->hidden('CourseRegistration.' . $count . '.type', array('value' => 11)); ?>
						<tr <?= $style; ?> >
							<td class="center"><?= $count++; ?></td>
							<td class="vcenter"><?= $pv['Course']['course_title'] . "**"; ?></td>
							<?php
					} else if (isset($pv['prequisite_taken_passsed']) && $pv['prequisite_taken_passsed'] == 2 && !isset($pv['exemption'])) {
						$style = ' class="on-process"';
						$on_hold_registration_count++;
						echo $this->Form->hidden('CourseRegistration.' . $count . '.type', array('value' => 11)); ?>
						<tr <?= $style; ?> >
							<td class="center"><?= $count++; ?></td>
							<td class="vcenter"><?= $pv['Course']['course_title'] . "**"; ?></td>
							<?php
					} else if ((isset($pv['registration_type']) && $pv['registration_type'] == 2) && (isset($pv['prequisite_taken_passsed']) && $pv['prequisite_taken_passsed'] == 2) && !isset($pv['exemption'])) {
						$style = ' class="on-process"';
						$on_hold_registration_count++;
						echo $this->Form->hidden('CourseRegistration.' . $count . '.type', array('value' => 13)); ?>
						<tr <?= $style; ?>>
							<td class="center"><?= $count++; ?></td>
							<td class="vcenter"><?= $pv['Course']['course_title'] . "**"; ?></td>
							<?php
					} else {

						if ((isset($dismissed) && $dismissed === true)) {
							$style = ' class="rejected"';
						} ?>

						<tr <?= $style; ?> >
							<td class="center"><?= $count++; ?></td>
							<td class="vcenter"><?= $pv['Course']['course_title']; ?></td>
							<?php
					} ?>

							<td class="center"><?= $pv['Course']['course_code']; ?></td>
							<td class="center"><?= $pv['Course']['lecture_hours']; ?></td>
							<td class="center"><?= $pv['Course']['tutorial_hours']; ?></td>
							<td class="center"><?= $pv['Course']['laboratory_hours']; ?></td>
							<td class="center"><?= $pv['Course']['credit']; ?></td>
						</tr>
						<?php
				} ?>
			</tbody>
			<?php
			if (!isset($dismissed)) { ?>
				<tfoot>
					<tr>
						<td colspan=8 class="vcenter" style="font-weight: normal;">
							Important Note: 
							<ol>
								<li>Green marked courses are courses you are elegible for registration.</li>
								<li>Red marked courses are courses you are not elegible for registration since you didn't fullfilled prerequisite requirements.</li>
								<li>Blue marked courses are courses that are exempted.</li>
								<?php
								if ($on_hold_registration_count) { ?>
									<li>Orange marked courses ending with ** are registration allowed on hold biases, either grade for the prerequsite course is not submitted or student academic status is not generated for the previous semester. These registrations will be dropped if the student fails to achieve a pass mark for the prequisite course or fails to achive minimum CGPA set for the semester.</li>
									<?php
								}

								if ($elective_courses_count) { ?>
									<li>Courses with checkbox in Elective column are published as elective by the department, <i class="rejected" style="font-weight: bold;">only select the courses you want to take.</i></li>
									<?php
								} ?>
							</ol>
						</td>
					</tr>
				</tfoot>
				<?php
			} ?>
		</table>
	</div>

	<?php
	if ((isset($dismissed) && $dismissed === true)) {
		// dont show 
	} else if (!isset($deadlinepassed)) { ?>
		<?= '<hr>' . $this->Form->submit(__('Register'), array('name' => 'register', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
		<?php
	}
} ?>
