
<?php
$st_count = 1;
$applied_count = 0; ?>
<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Request F(Fx) Exam Sit'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div style="margin-top: -30px;"><hr></div>

				<?php
				if (isset($fx_grade_change) && !empty($fx_grade_change)) { ?>

					<?= $this->Form->create('ExamGrade', array('novalidate' => true)); ?>

					<h6 class="fs14 text-gray">Select the course you want to sit for F(Fx) exam. It is only allowed to sit
						only one F(Fx) on given semester based on the new universities legistlation, and the other F(Fx)
						will be converted to F automatically and it will be calculated on your status. 
					</h6>

					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<th><?= ('No.'); ?> </th>
									<th style="padding:0;width:30px;"></th>
									<th><?= ('Name'); ?></th>
									<th><?= ('ID'); ?></th>
									<th><?= ('Course Name'); ?></th>
									<th><?= ('Academic Year'); ?></th>
									<th><?= ('Semester'); ?></th>
									<th><?= ('Grade'); ?></th>
									<th><?= ('Action'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$count = 1;
								foreach ($fx_grade_change as $fx) { ?>
									<tr>
										<td><?= $count; ?></td>
										<td><?= $this->Form->checkbox('FxResitRequest.' . $count . '.selected_id', array('class' => 'checkbox1')); ?>
											<?= $this->Form->hidden('FxResitRequest.' . $count . '.student_id', array('value' => $fx['Student']['id']));
											if (isset($fx['CourseRegistration']['id']) && !empty($fx['CourseRegistration']['id'])) {
												echo $this->Form->hidden('FxResitRequest.' . $count . '.course_registration_id', array('value' => $fx['CourseRegistration']['id']));
												echo $this->Form->hidden('FxResitRequest.' . $count . '.published_course_id', array('value' => $fx['CourseRegistration']['published_course_id']));
											}
											if (isset($fx['CourseAdd']['id']) && !empty($fx['CourseAdd']['id'])) {
												echo $this->Form->hidden('FxResitRequest.' . $count . '.course_add_id', array('value' => $fx['CourseAdd']['id']));
												echo $this->Form->hidden('FxResitRequest.' . $count . '.published_course_id', array('value' => $fx['CourseAdd']['published_course_id']));
											}
											echo $this->Form->hidden('FxResitRequest.' . $count . '.applied_id', array('value' => $fx['Student']['applied_id'])); ?>
										</td>
										<td><?= $fx['Student']['full_name']; ?></td>
										<td><?= $fx['Student']['studentnumber']; ?></td>
										<td><?= $fx['PublishedCourse']['Course']['course_title']; ?></td>
										<td><?= $fx['PublishedCourse']['academic_year']; ?></td>
										<td><?= $fx['PublishedCourse']['semester']; ?></td>
										<td><?= $fx['ExamGrade'][0]['grade']; ?></td>

										<td>
											<?php
											if (isset($fx['Student']['applied_id']) && !empty($fx['Student']['applied_id']) && $fx['Student']['fxgradesubmitted'] == false) {
												echo $this->Html->link(__('Delete'), array('controller' => 'ExamGrades', 'action' => 'cancel_fx_resit_request', $fx['Student']['applied_id']), null, sprintf(__('Are you sure you want to delete %s?'), $fx['PublishedCourse']['Course']['course_title']));
											}

											if (isset($fx['Student']['applied_id']) && !empty($fx['Student']['applied_id']) || $fx['Student']['fxgradesubmitted'] == true) {
												$applied_count++;
											} ?>
										</td>
									</tr>
									<?php
									$count++;
								} ?>
							</tbody>
						</table>
					</div>
					<hr>

					<?php
					if ($applied_count == 0 && $applied_request == 1) {
						echo $this->Form->submit(__('Apply Fx Exam Resit'), array('name' => 'applyFxExamResit', 'div' => false, 'class' => 'tiny radius button bg-blue'));
					} ?>
					<?= $this->Form->end(); ?>
					<?php
				} else { ?>
					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No result found.</div>
					<?php
				} ?>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {
		$('input[type="checkbox"]').on('change', function () {
			$('input[type="checkbox"]').not(this).prop('checked', false);
		});
	});
</script>