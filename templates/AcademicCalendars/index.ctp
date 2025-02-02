<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-calendar" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Academic Calendars'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->Create('AcademicCalendar'); ?>
				<div style="margin-top: -30px;">
					<hr>
                    <fieldset style="padding-bottom: 5px;padding-top: 15px;">
                        <!-- <legend>&nbsp;&nbsp; Search / Filter &nbsp;&nbsp;</legend> -->
                        <div class="row">
                            <div class="large-3 columns">
								<?= $this->Form->input('Search.academic_year', array('id' => 'academicyear', 'label' => 'Academic Year: ', 'required', 'style' => 'width:90%', 'type' => 'select', 'options' => $acyear_array_data, /* 'empty' => "[ Select ACY ]", */ 'default' => (isset($defaultacademicyear) ? $defaultacademicyear : ''))); ?>
                            </div>
							<div class="large-3 columns">
								<?= $this->Form->input('Search.semester', array('label' => 'Semester: ', 'style' => 'width:80%;', 'options' => Configure::read('semesters'), 'empty' => '[ All Semesters ]')); ?>
                            </div>
                            <div class="large-3 columns">
								<?= $this->Form->input('Search.program_id', array('label' => 'Program: ', 'empty' => '[ All Programs ]', 'style' => 'width:90%;')); ?>
                            </div>
                            <div class="large-3 columns">
								<?= $this->Form->input('Search.program_type_id', array('label' => 'Program Type: ',  'empty' => '[ All Program Types ]', 'style' => 'width:90%;')); ?>
                            </div>
                        </div>
						<div class="row">
                            <div class="large-6 columns">
								<?php
								if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
									echo $this->Form->input('Search.department_id', array('label' => 'Department: ', 'style' => 'width:90%;'));
								} else {
									echo $this->Form->input('Search.department_id', array('label' => 'Department: ', 'style' => 'width:90%;', 'empty' => '[ All Departments ]'));
								} ?>
                            </div>
							<div class="large-3 columns">
								<?= $this->Form->input('Search.year_level_id', array('label' => 'Year Level: ', 'empty' => '[ All Year Levels ]', 'style' => 'width:90%;')); ?>
                            </div>
							<div class="large-3 columns">
								&nbsp;
                            </div>
						</div>
						<hr>
						<?= $this->Form->submit(__('View Academic Calendar'), array('name' => 'viewAcademicCalendar', 'class' => 'tiny radius button bg-blue', 'id' => 'viewAcademicCalendar', 'div' => false)); ?>
                    </fieldset>
                </div>
			</div>

			<div class="large-12 columns">
				<?php 
				if (isset($academicCalendars) && !empty($academicCalendars)) { ?>
					<hr>
					<br>
					<div style="overflow-x:auto;">
						<table class="display table" cellpadding="0" cellspacing="0">
							<thead>
								<tr>
									<th class="center" style="width: 5%;">&nbsp;</th>
									<th class="center" style="width: 5%;"><?= $this->Paginator->sort('id', '#'); ?></th>
									<th class="center"><?= $this->Paginator->sort('full_year', 'ACY-Sem'); ?></th>
									<th class="center"><?= $this->Paginator->sort('year_name', 'Year Level'); ?></th>
									<th class="center"><?= $this->Paginator->sort('program_id', 'Program'); ?></th>
									<th class="center"><?= $this->Paginator->sort('program_type_id', 'Program Type'); ?></th>
									<?php
									if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) { ?>
										<th class="center"><?= __(''); ?></th>
										<?php
									} ?>
								</tr>
							</thead>
							<tbody>
								<?php
								$count = 1;
								foreach ($academicCalendars as $academicCalendar) { ?>
									<tr>
										<td onclick="toggleView(this)" id="<?= $count; ?>" class="center"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $count)); ?> </td>
										<td class="center"><?= $count; ?></td>
										<td class="center"><?= $academicCalendar['AcademicCalendar']['full_year']; ?></td>
										<td class="center"><?= $this->Html->link($academicCalendar['AcademicCalendar']['year_name'], array('action' => 'view', $academicCalendar['AcademicCalendar']['id'])); ?></td>
										<td class="center"><?= $this->Html->link($academicCalendar['Program']['name'], array('controller' => 'programs', 'action' => 'view', $academicCalendar['Program']['id'])); ?></td>
										<td class="center"><?= $this->Html->link($academicCalendar['ProgramType']['name'], array('controller' => 'program_types', 'action' => 'view', $academicCalendar['ProgramType']['id'])); ?></td>
										<?php
										if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) { ?>
											<td  class="center">
												<?php //echo $this->Html->link(__(''), array('action' => 'view', $academicCalendar['AcademicCalendar']['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?><!-- &nbsp; &nbsp; -->
												<?= $this->Html->link(__(''), array('action' => 'edit', $academicCalendar['AcademicCalendar']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?>
											</td>
											<?php
										} ?>
									</tr>
									<tr id="c<?= $count; ?>" style="display:none">
										<td style="background-color: white;"></td>
										<td style="background-color: white;"></td>
										<td colspan=<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? 3 : 3); ?> style="background-color: white;">
											<table cellpadding="0" cellspacing="0">
												<tr>
													<td>Department / College</td>
													<td class="center">Activty and Date</td>
												</tr>
												<tr>
													<td style="background-color: white;">
														<?php
														$department_lists = explode(", ", $academicCalendar['AcademicCalendar']['department_name']);
														if (!empty($department_lists)) {
															$list = '<ul>';
															foreach ($department_lists as $dpl => $dptv) {
																if (!empty($dptv)) {
																	$list .= '<li>' . $dptv . '</li>';
																}
															}
															$list .= '</ul>';
															echo $list;
														} 
														// Freshman calendars
														if (isset($academicCalendar['AcademicCalendar']['college_name'])) {
															$college_lists = explode(", ", $academicCalendar['AcademicCalendar']['college_name']);
															if (!empty($college_lists)) {
																$clist = '<ul>';
																foreach ($college_lists as $clgl => $clglv) {
																	if	(!empty($clglv)) {
																		$clist .= '<li>' . $clglv . '</li>';
																		//$clist .= '<li>' . $clglv . ' (Pre/Freshman)' . '</li>';
																	}
																}
																$clist .= '</ul>';
																echo $clist;
															} 
														}?>
													</td>
													<td style="background-color: white;">
														<table cellpadding="0" cellspacing="0" class="table">
															<tr>
																<td style="background-color: white;">
																	<strong>Registration: </strong><br>
																	<?= $this->Time->format("M j, Y", $academicCalendar['AcademicCalendar']['course_registration_start_date'], NULL, NULL) . ' - ' . $this->Time->format("M j, Y", $academicCalendar['AcademicCalendar']['course_registration_end_date'], NULL, NULL); ?>
																</td>
															</tr>
															<tr>
																<td style="background-color: white;">
																	<strong>Course Add: </strong><br>
																	<?= $this->Time->format("M j, Y", $academicCalendar['AcademicCalendar']['course_add_start_date'], NULL, NULL) . ' - ' . $this->Time->format("M j, Y", $academicCalendar['AcademicCalendar']['course_add_end_date'], NULL, NULL); ?>
																</td>
															</tr>
															<tr>
																<td style="background-color: white;">
																	<strong>Course Drop: </strong><br>
																	<?= $this->Time->format("M j, Y", $academicCalendar['AcademicCalendar']['course_drop_start_date'], NULL, NULL) . ' - ' . $this->Time->format("M j, Y", $academicCalendar['AcademicCalendar']['course_drop_end_date'], NULL, NULL); ?>
																</td>
															</tr>
															<tr>
																<td style="background-color: white;">
																	<strong>Grade Submission: </strong><br>
																	<?= $this->Time->format("M j, Y", $academicCalendar['AcademicCalendar']['grade_submission_start_date'], NULL, NULL) . ' - ' . $this->Time->format("M j, Y", $academicCalendar['AcademicCalendar']['grade_submission_end_date'], NULL, NULL); ?>
																</td>
															</tr>
															
															<?php
															if (isset($academicCalendar['AcademicCalendar']['grade_fx_submission_end_date']) && !empty($academicCalendar['AcademicCalendar']['grade_fx_submission_end_date'])) { ?>
																<tr>
																	<td style="background-color: white;">
																		<strong>Fx Grade Submission: </strong><br>
																		<?= $this->Time->format("M j, Y", $academicCalendar['AcademicCalendar']['grade_fx_submission_end_date'], NULL, NULL); ?>
																	</td>
																</tr>
																<?php
															} 

															if (isset($academicCalendar['AcademicCalendar']['senate_meeting_date']) && !empty($academicCalendar['AcademicCalendar']['senate_meeting_date'])) { ?>
																<tr>
																	<td style="background-color: white;">
																		<strong>Senate Meeting: </strong> <br>
																		<?= $this->Time->format("M j, Y", $academicCalendar['AcademicCalendar']['senate_meeting_date'], NULL, NULL); ?>
																	</td>
																</tr>
																<?php
															}

															if (isset($academicCalendar['AcademicCalendar']['graduation_date']) && !empty($academicCalendar['AcademicCalendar']['graduation_date'])) { ?>
																<tr>
																	<td style="background-color: white;">
																		<strong>Graduation Day: </strong><br>
																		<?= $this->Time->format("M j, Y", $academicCalendar['AcademicCalendar']['graduation_date'], NULL, NULL); ?>
																	</td>
																</tr>
																<?php
															} ?>
														</table>
													</td>
												</tr>
											</table>
										</td>

										<td colspan=<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? 2 : 1); ?> style="background-color: white;">
											<?php 
											if (isset($academicCalendar['ExtendingAcademicCalendar']) && !empty($academicCalendar['ExtendingAcademicCalendar'])) { ?>
												<table cellpadding="0" cellspacing="0" class="table">
													<tr><td colspan="5"><h6 class="fs14">List of departments which have deadline extension.</h6></td></tr>
													<tr>
														<td>#</td>
														<td>Year</td>
														<td>Department</td>
														<td>Activty</td>
														<td>Extened Days</td>
													</tr>
													<?php
													$e_count = 1;

													foreach ($academicCalendar['ExtendingAcademicCalendar'] as $exk => $exv) { ?>
														<tr>
															<td><?= $e_count; ?></td>
															<td><?= $exv['year_level_id'] ?></td>
															<td><?= $exv['Department']['name'] ?></td>
															<td><?= ucwords(str_replace('_', ' ', $exv['activity_type'])); ?></td>
															<td>
																<?= $this->Form->input('ExtendingAcademicCalendar.' . $e_count . $count . '.id', array('type' => 'hidden', 'value' => $exv['id'])); ?>
																<?= $this->Form->input('ExtendingAcademicCalendar.' . $e_count . $count . '.days', array('type' => 'number', 'label' => false, 'maxlength' => '5', 'style' => 'width:50px', 'id' => 'extension_' . $e_count . $count, 'value' => $exv['days'])); ?>
															</td>

														</tr>
														<?php
														$e_count++;
													} ?>
												</table>
												<?php
											} else { ?>
												<!-- <p style=" writing-mode: vertical-rl; text-orientation: upright;"><strong class="center">No deadline extension</strong></p> -->
												<p class="center"><strong>No deadline extension</strong></p>
												<?php 
											} ?>
										</td>
									</tr>
									<?php
									$count++;
								} ?>
							</tbody>
						</table>
					</div>
					<br>
					<hr>
					<div class="row">
						<div class="large-5 columns">
							<?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total'))); ?>
						</div>
						<div class="large-7 columns">
							<div class="pagination-centered">
								<ul class="pagination">
									<?= $this->Paginator->prev('<< ' . __(''), array('tag' => 'li'), null, array('class' => 'arrow unavailable')); ?> <?= $this->Paginator->numbers(array('separator' => '', 'tag' => 'li')); ?> <?= $this->Paginator->next(__('') . ' >>', array('tag' => 'li'), null, array('class' => 'arrow unavailable')); ?>
								</ul>
							</div>
						</div>
					</div>
					<?php 
				} else { ?>
					<div class='info-box info-message'><span style='margin-right: 15px;'></span>No recent academic calendar defined for <?= isset($defaultacademicyear) ? $defaultacademicyear : 'the current' ?> academic year, try adjusting search filters to get previous academic calendars.</div>
					<?php
				} ?>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script>

	$(document).ready(function() {
		$('form input').change(function(e) {
			updateExtension(e);
			e.preventDefault();
		});
	});

	function updateExtension(e) {
		if ($(e.target).val() != "" &&
			isNaN($(e.target).val())) {
			alert('Please enter a valid result.');
			$('#' + $(e.target).attr("id")).focus();
			$('#' + $(e.target).attr("id")).blur();
			return false;
		} else if ($(e.target).val() != "" &&
			parseInt($(e.target).val()) < 0) {
			$('#' + $(e.target).attr("id")).focus();
			$('#' + $(e.target).attr("id")).blur();
			return false;
		}

		$.ajax({
			url: "/academicCalendars/autoSaveExtension",
			type: 'POST',
			data: $('form').serialize(),
			success: function(data) {}
		});

	}

	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none') {
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		} else {
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		}
		$('#c' + obj.id).toggle("slow");
	}
</script>