<?php
//Department exam grade changes, makeup exams, supplementary exams
if (!empty($exam_grade_change_requests)) {
	if ($exam_grade_change_requests == 0 && empty($makeup_exam_grades) && empty($rejected_makeup_exams) && empty($rejected_supplementary_exams)) { ?>
		<p style="font-size:12px">There is no exam grade change request.</p>
		<?php
	} else { ?>
		<ul>
			<?php
			if ($exam_grade_change_requests != 0) { ?>
				<li>You have <?= ($exam_grade_change_requests); ?> grade change requests.</li>
				<?php
			}
			if ($makeup_exam_grades != 0) { ?>
				<li>You have <?= ($makeup_exam_grades); ?> makeup exam approval requests.</li>
				<?php
			}
			if ($rejected_makeup_exams != 0) { ?>
				<li class="rejected">You have <?= ($rejected_makeup_exams); ?> rejected makeup exam grade.</li>
				<?php
			}
			if ($rejected_supplementary_exams != 0) { ?>
				<li class="rejected">You have <?= ($rejected_supplementary_exams); ?> rejected supplementary exam grade.</li>
				<?php
			} ?>
		</ul>
		<?= $this->Html->link(__('View All', true), array('controller' => 'exam_grade_changes', 'action' => 'manage_department_grade_change')); ?>
		<?php
	}
} ?>

<?php
//College exam grade changes approval requests 
if (isset($exam_grade_changes_for_college_approval) && !empty($exam_grade_changes_for_college_approval)) {
	if ($exam_grade_changes_for_college_approval == 0) { ?>
		<p>There is no grade change request to be approved.</p>
		<?php
	} else { ?>
		<ul>
			<?php
			if ($exam_grade_changes_for_college_approval != 0) { ?>
				<li>You have <?= ($exam_grade_changes_for_college_approval); ?> grade change requests.</li>
				<?php
			} ?>
		</ul>
		<?php
	} ?>
	<?= $this->Html->link(__('View All', true), array('controller' => 'exam_grade_changes', 'action' => 'manage_college_grade_change')); ?>
	<?php
} ?>

<?php
//Registrar exam grade changes approval requests
if (isset($reg_exam_grade_change_requests) && !empty($reg_exam_grade_change_requests)) { 
	if ($reg_exam_grade_change_requests == 0 && empty($reg_supplementary_exam_grades) && empty($fm_rejected_makeup_exams) && empty($fm_rejected_supplementary_exams)) { ?>
		<p style="font-size:12px">There is no exam grade change requests.</p>
		<?php
	} else { ?>
		<ul>
			<?php
			if ($reg_exam_grade_change_requests != 0) { ?>
				<li>You have <?= ($reg_exam_grade_change_requests); ?> grade change requests.</li>
				<?php
			}
			if ($reg_makeup_exam_grades != 0) { ?>
				<li>You have <?= ($reg_makeup_exam_grades); ?> makeup exam approval requests.</li>
				<?php
			}
			if ($reg_supplementary_exam_grades != 0) { ?>
				<li>You have <?= ($reg_supplementary_exam_grades); ?> supplementary exam approval requests.</li>
				<?php
			} ?>
		</ul>
		<?php
	} ?>
	<a href="/exam_grade_changes/manage_registrar_grade_change" class="tiny radius button bg-blue">View All</a>
	<?php
} ?>

<?php
//Freshman exam grade changes, makeup exams, supplementary exams 
if (isset($fm_exam_grade_change_requests) && !empty($fm_exam_grade_change_requests)) { ?> 
	<table class="small_padding">
		<?php
		if ($fm_exam_grade_change_requests == 0 && empty($fm_makeup_exam_grades) && empty($fm_rejected_makeup_exams) && empty($fm_rejected_supplementary_exams)) { ?>
			<tr>
				<td colspan="2"><p style="font-size:12px">There is no freshman exam grade change request.</p></td>
			</tr>
			<?php
		} else { ?>
			<tr>
				<td>
					<ul>
						<?php
						if ($fm_exam_grade_change_requests != 0) { ?>
							<li>You have <?= ($fm_exam_grade_change_requests); ?> grade change requests.</li>
							<?php
						}
						if ($fm_makeup_exam_grades != 0) { ?>
							<li>You have <?= ($fm_makeup_exam_grades); ?> makeup exam approval requests.</li>
							<?php
						}
						if ($fm_rejected_makeup_exams != 0) { ?>
							<li class="rejected">You have <?= ($fm_rejected_makeup_exams); ?> rejected makeup exam grade.</li>
							<?php
						}
						if ($fm_rejected_supplementary_exams != 0) { ?>
							<li class="rejected">You have <?= ($fm_rejected_supplementary_exams); ?> rejected supplementary exam grade.</li>
							<?php
						} ?>
					</ul>
				</td>
			</tr>
			<?php
		} ?>
	</table>
	<?= $this->Html->link(__('View All', true), array('controller' => 'exam_grade_changes', 'action' => 'manage_freshman_grade_change'), array('class' => 'tiny radius button bg-blue')); ?>
	<?php
} ?>