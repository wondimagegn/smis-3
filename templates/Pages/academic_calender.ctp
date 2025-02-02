<div class="box bg-white">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-calendar" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Academic Calendars'); ?></span>
		</div>
	</div>
	<div class="box-body pad-forty" style="display: block; margin-top: -50px;">
		<div class="row">
			<?= $this->Form->Create('Page'); ?>
			<fieldset>
				<legend> &nbsp; &nbsp; <?= __('Search Academic Calendar '); ?>  &nbsp; &nbsp; </legend>
				<div class="large-3 columns">
					<?= $this->Form->input('Search.academic_year', array('id' => 'academicyear', 'label' => 'Academic Year', 'type' => 'select', 'options' => $acyear_array_data, /* 'empty' => "Select Academic Year",  */ 'default' => isset($defaultacademicyear) ? $defaultacademicyear : '', 'style' => 'width:90%;', 'required')); ?>
				</div>
				<div class="large-3 columns">
					<?= $this->Form->input('Search.semester', array('options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'empty' => 'Select Semester', 'required', 'style' => 'width:90%;' )); ?>
				</div>
				<div class="large-3 columns">
					<?= $this->Form->input('Search.program_id', array('style' => 'width:90%;')); ?>
				</div>
				<div class="large-3 columns">
					<?= $this->Form->input('Search.program_type_id', array('style' => 'width:90%;')); ?>
				</div>
				<div class="large-12 columns">
					<?= $this->Form->submit(__('Search Calendar'), array('name' => 'viewAcademicCalendar', 'class' => 'tiny radius button bg-blue', 'id' => 'viewAcademicCalendar', 'div' => false)); ?>
				</div>
			</fieldset>

			<p>&nbsp;</p>

			<?php
			if (isset($academicCalendars) && !empty($academicCalendars)) { ?>
				<div class="row">
					<div class="large-12 columns">
						<div style="overflow-x:auto;">
							<table style="width:100%" class="display" cellpadding="0" cellspacing="0">
								<thead>
									<!-- <td style="text-align:center; vertical-align:middle"> Program </td> -->
									<td style="text-align:center; vertical-align:middle"> Year Level </td>
									<td style="text-align:center; vertical-align:middle"> Activity / Dates </td>
									<td style="text-align:center; vertical-align:middle"> College / Department </td>
								</thead>
								<tbody>
									<?php foreach ($academicCalendars as $k => $v) { ?>
										<tr>
											<!-- <td style="background: #fff;"> <?php //echo '<br>' . $v['Program']['name']. '<br><br>' . $v['ProgramType']['name']; ?> </td> -->
											<td style="text-align:center; background: #fff;">
												<?php
												$yearLevelList =  explode(', ', $v['AcademicCalendar']['year_name']);
												if (isset($yearLevelList) && !empty($yearLevelList)) {
													echo '<br>';
													foreach ($yearLevelList as $ylkey => $ylval) {
														echo $ylval . ' ';
													}
													echo '<br>';
												}
												?>
											</td>

											<td style="background: #fff;">
												<?= '<br><strong> Registration: </strong> <br>' . (isset($v['AcademicCalendar']['course_registration_start_date']) ? $this->Time->format("M j, Y", $v['AcademicCalendar']['course_registration_start_date'], NULL, NULL) : 'N/A') . ' - ' . (isset($v['AcademicCalendar']['course_registration_end_date']) ? $this->Time->format("M j, Y", $v['AcademicCalendar']['course_registration_end_date'], NULL, NULL) : 'N/A') ?> <br />
												<?= '<br><strong> Course Add: </strong><br>' . (isset($v['AcademicCalendar']['course_add_start_date']) ? $this->Time->format("M j, Y", $v['AcademicCalendar']['course_add_start_date'], NULL, NULL) : 'N/A') . ' - ' . (isset($v['AcademicCalendar']['course_add_end_date']) ? $this->Time->format("M j, Y", $v['AcademicCalendar']['course_add_end_date'], NULL, NULL) : 'N/A'); ?> <br />
												<?= '<br><strong> Course Drop: </strong><br>' . (isset($v['AcademicCalendar']['course_drop_start_date']) ? $this->Time->format("M j, Y", $v['AcademicCalendar']['course_drop_start_date'], NULL, NULL) : 'N/A') . ' -  ' . (isset($v['AcademicCalendar']['course_drop_end_date']) ? $this->Time->format("M j, Y", $v['AcademicCalendar']['course_drop_end_date'], NULL, NULL) : 'N/A'); ?> <br />
												<?= '<br><strong> Grade Submission: </strong><br>' . (isset($v['AcademicCalendar']['grade_submission_start_date']) ? $this->Time->format("M j, Y", $v['AcademicCalendar']['grade_submission_start_date'], NULL, NULL) : 'N/A') . ' - ' . (isset($v['AcademicCalendar']['grade_submission_end_date']) ? $this->Time->format("M j, Y", $v['AcademicCalendar']['grade_submission_end_date'], NULL, NULL) : 'N/A'); ?> <br /><br />
											</td>

											<td style="background: #fff; padding-left:10px">
												<?php
												$deptlist =  explode(', ', $v['AcademicCalendar']['department_name']);
												if (isset($deptlist) && !empty($deptlist)) {
													echo '<br><ul>';
													foreach ($deptlist as $dlkey => $dlval) {
														echo '<li>' . $dlval . '</li>';
													}
													echo '</ul><br>';
												} ?>
											</td>
										</tr>
										<?php
									} ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<?php
			} ?>
		</div>
	</div>
</div>