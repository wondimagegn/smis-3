<?php
//Registrar grade confirmation
if (isset($courses_for_registrar_approval) && !empty($courses_for_registrar_approval)) { 
	if (empty($courses_for_registrar_approval)) { ?>
		<p>There is no course that needs your grade confirmation</p>
		<?php
	} else { ?>
		<p style="font-size:16px;font-weight:bold">List of courses grade submitted by instructor and approved by department and wait your confirmation.</p>
		<?php
		$row_count = 1;
		foreach ($courses_for_registrar_approval as $key => $course_for_grade_confirmation) {
			if ($row_count <= 100) { ?>
				<?= $this->Html->link(__($course_for_grade_confirmation['Course']['course_title'] . ' (' . $course_for_grade_confirmation['Course']['course_code'] . ')', true), array('controller' => 'exam_grades', 'action' => 'confirm_grade_submission', $course_for_grade_confirmation['PublishedCourse']['id']), array('class' => 'action_link')); ?><br />
				<strong>Section:</strong> <?= $course_for_grade_confirmation['Section']['name'] . ' (' . ((!empty($course_for_grade_confirmation['Department']['name']) ? $course_for_grade_confirmation['Department']['name'] : 'Freshman Program') . ' / ' . $course_for_grade_confirmation['Program']['name'] . ' / ' . $course_for_grade_confirmation['ProgramType']['name']) . ')'; ?><br/> 
				<strong>Semester:</strong> <?= $course_for_grade_confirmation['PublishedCourse']['semester']; ?><br/> 
				<strong>Academic Year:</strong> <?= $course_for_grade_confirmation['PublishedCourse']['academic_year']; ?>
				<?php
			} else {
				if (count($courses_for_registrar_approval) > 100) { ?>
					And other <?= (count($courses_for_registrar_approval) - 100) . ' courses. ' . $this->Html->link(__('View All', true), array('controller' => 'exam_grades', 'action' => 'confirm_grade_submission'), array('class' => 'tiny radius button bg-blue')) . ''; ?> 
					<?php
				}
				break;
			}
			$row_count++;
		}
	}
} ?>

<?php
//College grade approval for department unassigned students
if (isset($courses_for_freshman_approvals) && !empty($courses_for_freshman_approvals)) { ?>
	<table class="small_padding">
		<?php
		if (empty($courses_for_freshman_approvals)) { ?>
			<tr>
				<td style="border:0px solid #ffffff"><p style="font-size:12px"> There is no freshman course that needs grade approval.</p></td>
			</tr>
			<?php
		} else { ?>
			<tr>
				<td style="border:0px solid #ffffff"><p style="font-size:16px;font-weight:bold">List of courses grade submitted by instructor for department unassigned students and needs your approval.</p></td>
			</tr>
			<?php
			$row_count = 1;
			foreach ($courses_for_freshman_approvals as $key => $course_for_grade_confirmation) {
				if ($row_count <= 100) { ?>
					<tr>
						<td class="action_content">
							<?= $this->Html->link(__($course_for_grade_confirmation['Course']['course_title'] . ' (' . $course_for_grade_confirmation['Course']['course_code'] . ')', true), array('controller' => 'exam_grades', 'action' => 'approve_freshman_grade_submission', $course_for_grade_confirmation['PublishedCourse']['id']), array('class' => 'action_link')); ?><br />
							<strong>Section:</strong> <?= $course_for_grade_confirmation['Section']['name'] . ' (' . ((!empty($course_for_grade_confirmation['Department']['name']) ? $course_for_grade_confirmation['Department']['name'] : 'Freshman Program') . ' / ' . $course_for_grade_confirmation['Program']['name'] . ' / ' . $course_for_grade_confirmation['ProgramType']['name']) . ')'; ?><br/> 
							<strong>Semester:</strong> <?= $course_for_grade_confirmation['PublishedCourse']['semester']; ?><br/> 
							<strong>Academic Year:</strong> <?= $course_for_grade_confirmation['PublishedCourse']['academic_year']; ?>
						</td>
					</tr>
					<?php
				} else {
					if (count($courses_for_registrar_approvals) > 100) { ?>
						<tr>
							<td style="font-size:12px">And other <?= (count($courses_for_registrar_approval) - 100) . ' courses. ' . $this->Html->link(__('View All', true), array('controller' => 'exam_grades', 'action' => 'approve_freshman_grade_submission')); ?></td>
						</tr>
						<?php
					}
					break;
				}
				$row_count++;
			}
		} ?>
	</table>
	<?php
} ?>

<?php
//Department grade approval
if (isset($courses_for_dpt_approvals) && !empty($courses_for_dpt_approvals)) { ?>
	<table class="small_padding">
		<?php
		if (empty($courses_for_dpt_approvals)) { ?>
			<tr>
				<td style="border:0px solid #ffffff"><p style="font-size:12px">There is no course that needs grade approval.</p></td>
			</tr>
			<?php
		} else { ?>
			<tr>
				<td style="border:0px solid #ffffff"><p style="font-size:16px;font-weight:bold">List of courses grade submitted by instructor and needs department approval.</p></td>
			</tr>
			<?php
			$row_count = 1;
			foreach ($courses_for_dpt_approvals as $key => $course_for_grade_confirmation) {
				if ($row_count <= 100) { ?>
					<tr>
						<td class="action_content">
							<?= $this->Html->link(__($course_for_grade_confirmation['Course']['course_title'] . ' (' . $course_for_grade_confirmation['Course']['course_code'] . ')', true), array('controller' => 'exam_grades', 'action' => 'approve_non_freshman_grade_submission', $course_for_grade_confirmation['PublishedCourse']['id']), array('class' => 'action_link')); ?><br />
							<strong>Section:</strong> <?= $course_for_grade_confirmation['Section']['name'] . ' (' . ((!empty($course_for_grade_confirmation['Department']['name']) ? $course_for_grade_confirmation['Department']['name'] : 'Freshman Program') . ' / ' . $course_for_grade_confirmation['Program']['name'] . ' / ' . $course_for_grade_confirmation['ProgramType']['name']) . ')'; ?><br/> 
							<strong>Semester:</strong> <?= $course_for_grade_confirmation['PublishedCourse']['semester']; ?><br/> 
							<strong>Academic Year:</strong> <?= $course_for_grade_confirmation['PublishedCourse']['academic_year']; ?>
						</td>
					</tr>
					<?php
				} else {
					if (count($courses_for_registrar_approvals) > 100) { ?>
						<tr>
							<td style="font-size:12px">And other <?= (count($courses_for_registrar_approval) - 100) . ' courses. ' . $this->Html->link(__('View All', true), array('controller' => 'exam_grades', 'action' => 'approve_non_freshman_grade_submission')); ?></td>
						</tr>
						<?php
					}
					break;
				}
				$row_count++;
			}
		} ?>
	</table>
	<?php
} ?>

<?php
//Registrar grade confirmation
if (isset($courses_for_registrar_approval) && !empty($courses_for_registrar_approval)) {
	if (empty($courses_for_registrar_approval)) { ?>
		<p>There is no course that needs grade confirmation</p>
		<?php
	} else { ?>
		<p style="font-size:16px;font-weight:bold">List of courses grade submitted by instructor and approved by department and wait your confirmation.</p>
		<?php
		$row_count = 1;
		foreach ($courses_for_registrar_approval as $key => $course_for_grade_confirmation) {
			if ($row_count <= 10) { ?>
				<?= $this->Html->link(__($course_for_grade_confirmation['Course']['course_title'] . ' (' . $course_for_grade_confirmation['Course']['course_code'] . ')', true), array('controller' => 'exam_grades', 'action' => 'confirm_grade_submission', $course_for_grade_confirmation['PublishedCourse']['id']), array('class' => 'action_link')); ?><br />
				<strong>Section:</strong> <?= $course_for_grade_confirmation['Section']['name'] . ' (' . ((!empty($course_for_grade_confirmation['Department']['name']) ? $course_for_grade_confirmation['Department']['name'] : 'Freshman Program') . ' / ' . $course_for_grade_confirmation['Program']['name'] . ' / ' . $course_for_grade_confirmation['ProgramType']['name']) . ')'; ?><br/> 
				<strong>Semester:</strong> <?= $course_for_grade_confirmation['PublishedCourse']['semester']; ?><br/> 
				<strong>Academic Year:</strong> <?= $course_for_grade_confirmation['PublishedCourse']['academic_year']; ?>
				<?php
			} else {
				if (count($courses_for_registrar_approval) > 10) { ?>
					And other <?= (count($courses_for_registrar_approval) - 10) . ' courses. ' . $this->Html->link(__('View All', true), array('controller' => 'exam_grades', 'action' => 'confirm_grade_submission'), array('class' => 'tiny radius button bg-blue')) . ''; ?>
					<?php
				}
				break;
			}
			$row_count++;
		}
	}
} ?>