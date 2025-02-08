<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('List F(Fx) Exam Retake Applications'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div style="margin-top: -30px;"><hr></div>

				<h6 class="fs14 text-gray">Please select academic year and semester for which you want to view F(Fx) retake requests you applied.</h6>

				<?= $this->Form->create('ExamGrade', array('novalidate' => true)); ?>

				<fieldset style="padding-bottom: 10px;">
					<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
					<div class="row">
						<div class="large-3 columns">
							<?= $this->Form->input('academic_year', array('id' => 'AcadamicYear', 'label' => 'Acadamic Year: ',  'style' => 'width:90%;', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($defaultacademicyear) ? $defaultacademicyear : $defaultacademicyear))); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('semester', array('id' => 'Semester', 'label' => 'Semester: ', 'type' => 'select', 'style' => 'width:90%;', 'options' => Configure::read('semesters'),  'default' => (isset($semester_selected) ? $semester_selected : false))); ?>
						</div>
						<div class="large-6 columns">
							<br>
							<?= $this->Form->submit(__('View F(Fx) Applications'), array('name' => 'viewFxApplication', 'class' => 'tiny radius button bg-blue')); ?>
						</div>
					</div>
				</fieldset>

				<?php 
				if (isset($fxRequests) && !empty($fxRequests)) { ?>
					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<th><?= ('No.'); ?></th>
									<th><?= ('Name'); ?></th>
									<th><?= ('ID'); ?></th>
									<th><?= ('Course Name'); ?></th>
									<th><?= ('Academic Year'); ?></th>
									<th><?= ('Semester'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$count = 1;
								foreach ($fxRequests as $kf => $fxx) {
									foreach ($fxx['FxResitRequest'] as $kkk => $fx) { ?>
										<tr>
											<td><?= $count; ?></td>
											<td><?= $fx['Student']['full_name']; ?></td>
											<td><?= $fx['Student']['studentnumber']; ?></td>
											<td><?= $fxx['Course']['course_title']; ?></td>
											<td><?= $fxx['PublishedCourse']['academic_year']; ?></td>
											<td><?= $fxx['PublishedCourse']['semester']; ?></td>
										</tr>
										<?php
										$count++;
									}
								} ?>
							</tbody>
						</table>
					</div>
					<?php
				} else { ?>
					<!-- <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No result found.</div> -->
					<?php
				} ?>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>
