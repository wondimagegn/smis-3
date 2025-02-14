<hr>
<table cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td class="vcenter"> &nbsp; Select/ Unselect All <?= $this->Form->checkbox("SelectAll", array('id' => 'select-all', 'checked' => '')); ?></td>
					</tr>
					<?php
					if (!empty($colleges)) {
						foreach ($colleges as $college_id => $college_name) {
							if (isset($college_department[$college_id]) && count($college_department[$college_id]) > 0) { ?>
								<tr>
									<td style="background-color: white;">
										<!-- <h6 class='fs15 text-gray'><?= $college_name; ?></h6> -->
										<fieldset style="padding-bottom: 20px;padding-top: 10px;">
											<legend>&nbsp;&nbsp; <?= $college_name; ?> &nbsp;&nbsp;</legend>
										<table cellpadding="0" cellspacing="0" class="table">
											<tbody>
												<?php
												if (!empty($college_department[$college_id])) {
													foreach ($college_department[$college_id] as $department_id => $department_name) {

														$recorded = null;
														
														if (isset($alreadyexisteddepartment) && !empty($alreadyexisteddepartment) && in_array($department_id, $alreadyexisteddepartment)) {
															$recorded = 'class="rejected"';
														}

														if (isset($this->request->data['AcademicCalendar']['department_id'])) {
															if (in_array($department_id, $this->request->data['AcademicCalendar']['department_id'])) {
																if (isset($recorded) && !empty($recorded)) { ?>
																	<tr <?= $recorded; ?>>
																		<td style="background-color: white;" class="vcenter"><input class="checkbox1" type="checkbox" name="data[AcademicCalendar][department_id][]" value=<?= $department_id; ?> id="AcademicCalendarDepartmentId<?= $department_id; ?>"><label for="AcademicCalendarDepartmentId<?= $department_id; ?>"><?= $department_name; ?></label></td>
																	</tr>
																	<?php
																} else { ?>
																	<tr>
																		<td style="background-color: white;" class="vcenter"><input type="checkbox" class="checkbox1" checked="checked" name="data[AcademicCalendar][department_id][]" value=<?= $department_id; ?> id="AcademicCalendarDepartmentId<?= $department_id; ?>"><label for="AcademicCalendarDepartmentId<?= $department_id; ?>"><?= $department_name; ?></label></td>
																	</tr>
																	<?php
																}
															} else { ?>
																<tr>
																	<td style="background-color: white;" class="vcenter"><input type="checkbox" class="checkbox1" name="data[AcademicCalendar][department_id][]" value=<?= $department_id; ?> id="AcademicCalendarDepartmentId<?= $department_id; ?>"><label for="AcademicCalendarDepartmentId<?= $department_id; ?>"><?= $department_name; ?></label></td>
																</tr>
																<?php
															}
														} else { ?>
															<tr>
																<?php
																//debug(count(explode('pre_', $department_id)) > 1);
																if (count(explode('pre_', $department_id)) > 1) { ?>
																	<td style="background-color: white;" class="vcenter"><input type="checkbox" class="checkbox1" name="data[AcademicCalendar][department_id][]" value=<?= $department_id; ?> id="AcademicCalendarDepartmentId<?= $department_id; ?>"><label for="AcademicCalendarDepartmentId<?= $department_id; ?>"><?= $department_name; ?></label></td>
																	<?php
																} else { ?>
																	<td style="background-color: white;" class="vcenter"><input type="checkbox" class="checkbox1" checked="checked" name="data[AcademicCalendar][department_id][]" value=<?= $department_id; ?> id="AcademicCalendarDepartmentId<?= $department_id; ?>"><label for="AcademicCalendarDepartmentId<?= $department_id; ?>"><?= $department_name; ?></label></td>
																	<?php
																} ?>
															</tr>
															<?php
														}
													}
												} ?>
											</tbody>
										</table>
										</fieldset>
									</td>
								</tr>
								<?php
							}
						}
					} ?>
				</table>
			</td>
			<td>
				<table cellpadding="0" cellspacing="0" class="table">
					<tbody>
						<tr>
							<td colspan='2'><?= $this->Form->input('course_registration_start_date', array('id'=> 'course_registration_start_date', 'label' => 'Registration Start', 'type' => 'date', 'class' => 'form-control',  'minYear' => date('Y') - 2, 'maxYear' => date('Y') + 1, 'style' => 'width:90px')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('course_registration_end_date', array('label' => 'Registration End', 'type' => 'date', 'minYear' => date('Y') - 2, 'maxYear' => date('Y') + 1, 'style' => 'width:90px')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('course_add_start_date', array('label' => 'Course Add Start', 'type' => 'date', 'minYear' => date('Y') - 2, 'style' => 'width:90px')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('course_add_end_date', array('label' => 'Course Add End', 'type' => 'date', 'maxYear' => date('Y') + 1, 'style' => 'width:90px')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('course_drop_start_date', array('label' => 'Course Drop Start', 'type' => 'date', 'minYear' => date('Y') - 2, 'maxYear' => date('Y') + 1, 'style' => 'width:90px')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('course_drop_end_date', array('label' => 'Course Drop End', 'type' => 'date', 'minYear' => date('Y') - 2, 'maxYear' => date('Y') + 1, 'style' => 'width:90px')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('grade_submission_start_date', array('label' => 'Grade Submission Start', 'type' => 'date', 'minYear' => date('Y') - 2, 'maxYear' => date('Y') + 1, 'style' => 'width:90px')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('grade_submission_end_date', array('label' => 'Grade Submission End', 'type' => 'date', 'minYear' => date('Y') - 2, 'maxYear' => date('Y') + 1, 'style' => 'width:90px')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('grade_fx_submission_end_date', array('label' => 'Fx Grade Submission', 'type' => 'date', 'minYear' => date('Y') - 2, 'maxYear' => date('Y') + 1, 'style' => 'width:90px')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('senate_meeting_date', array('label' => 'Senate Meeting Date', 'type' => 'date', 'minYear' => date('Y') - 2, 'maxYear' => date('Y') + 1, 'style' => 'width:90px')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('graduation_date', array('label' => 'Graduation Date', 'type' => 'date', 'minYear' => date('Y') - 2,  'maxYear' => date('Y') + 1, 'style' => 'width:90px')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('online_admission_start_date', array('label' => 'Online Admission Start Date', 'type' => 'date', 'minYear' => date('Y') - 2, 'maxYear' => date('Y') + 1, 'style' => 'width:90px')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('online_admission_end_date', array('label' => 'Online Admission End Date', 'type' => 'date', 'minYear' => date('Y') - 2, 'maxYear' => date('Y') + 1, 'style' => 'width:90px')); ?></td>
						</tr>
						<!-- <tr>
							<td><?php //echo $this->Form->input('excluding_department_id', array('type' => 'select', 'name' => 'excluding_department_ids[]', 'style' => 'width:200px;height:auto;', 'multiple' => true, 'options' => $departments)); ?></td>
							<td><?php //echo $this->Form->input('excluding_year_level_id', array('type' => 'select', 'name' => 'excluding_year_level_ids[]', 'style' => 'width:200px;height:auto;', 'multiple' => true, 'options' => $yearLevels)); ?></td>
						</tr>
						<tr>
							<td><?php //echo $this->Form->input('excluding_program_id', array('type' => 'select', 'name' => 'excluding_program_ids[]', 'style' => 'width:200px;height:auto;', 'multiple' => true, 'options' => $programs)); ?></td>
							<td><?php //echo $this->Form->input('excluding_program_type_id', array('type' => 'select', 'name' => 'excluding_program_type_ids[]', 'style' => 'width:200px;height:auto;', 'multiple' => true, 'options' => $programTypes)); ?></td>
						</tr> -->
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<hr>
<?= $this->Form->Submit('Set Calendar', array('div' => false, 'id' => 'setCalendar', 'class' => 'tiny radius button bg-blue')); ?>
