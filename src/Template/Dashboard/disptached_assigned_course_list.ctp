<?php
//dispatched courses 
if (isset($dispatched_course_list) && !empty($dispatched_course_list)) { ?>
	<h3 class="box-title"><i class="fontello-folder"></i>
		<span>Courses Instructor Assignment for Dispatched Courses is not done!</span>
	</h3>
	<table class="small_padding">
		<?php
		if (empty($dispatched_course_list)) { ?>
			<tr>
				<td style="border:0px solid #ffffff"><p style="font-size:12px">There is no dispatched course from other department to assign instructor from your department.</p></td>
			</tr>
			<?php
		} else { ?>
			<tr>
				<td style="border:0px solid #ffffff"><p style="font-size:16px;font-weight:bold">List of dispatched courses for instructor assignment.</p></td>
			</tr>
			<?php
			$row_count = 1;
			foreach ($dispatched_course_list as $dk => $dc) {
				if ($row_count <= 100) { ?>
					<tr>
						<td class="action_content">
							<br/> 
							<strong>Dispatched To your Department: </strong><?= $dc['GivenByDepartment']['name']; ?><br/>
							<strong>Course: </strong> <a href="/course_instructor_assignments/assign_course_instructor/<?= $dc['PublishedCourse']['id']; ?>"><?= $dc['Course']['course_title'] . ' (' . $dc['Course']['course_code'] . ')'; ?></a><br/>
							<strong>Section:</strong> <?= $dc['Section']['name'] . ' (' . ((!empty($dc['Department']['name']) ? $dc['Department']['name'] : 'Freshman Program') . ' / ' . $dc['Program']['name'] . ' / ' . $dc['ProgramType']['name']) . ')'; ?><br/> 
							<strong>Semester:</strong> <?= $dc['PublishedCourse']['semester']; ?><br/> 
							<strong>Academic Year:</strong><?= $dc['PublishedCourse']['academic_year']; ?>
						</td>
					</tr>
					<?php
				}
				$row_count++;
			}
		} ?>
	</table>
	<?php
} ?>

<?php
//Latest assigned courses to instructor
if (!empty($latest_assigned_courses)) {
	if (empty($latest_assigned_courses)) { ?>
		<p>Currently you do not have assigned courses.</p>
		<?php
	} else {
		foreach ($latest_assigned_courses as $key => $latest_assigned_course) { ?>
			<strong>Course:</strong> <?= $latest_assigned_course['Course']['course_title'] . ' (' . $latest_assigned_course['Course']['course_code'] . ')'; ?><br />
			<strong>Section:</strong> <?= $latest_assigned_course['Section']['name'] . ' (' . (isset($latest_assigned_course['Department']['name']) ? $latest_assigned_course['Department']['name'] . ' Department' : $latest_assigned_course['College']['name'] . ' Freshman Program') . ')'; ?>
			<?= $this->Html->link(__('Manage Exam', true), array('controller' => 'exam_results', 'action' => 'add', $latest_assigned_course['PublishedCourse']['id'])) . ' | '; ?>
			<?= $this->Html->link(__('Take Attendance', true), array('controller' => 'attendances', 'action' => 'take_attendance', $latest_assigned_course['PublishedCourse']['id'])) . ' | '; ?>
			<?= $this->Html->link(__('View Attendance', true), array('controller' => 'attendances', 'action' => 'instructor_view_attendance', $latest_assigned_course['PublishedCourse']['id'])); ?>
			<?php
		}
	}
} ?>