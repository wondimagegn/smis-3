<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Course details: ' . (isset($course['Course']['course_title']) ? $course['Course']['course_title'] : '') . (isset($course['Course']['course_code']) ? '  (' .$course['Course']['course_code'] . ')': '') ; ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<?php 
			if (!empty($course['Course'])) { ?>
				<div class="large-6 columns">
					<table cellspacing="0" cellpading="0" class="table-borderless fs13">
						<tbody>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Course Title:</span> &nbsp;&nbsp; <?= $course['Course']['course_title']; ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Course Code:</span> &nbsp;&nbsp; <?= $course['Course']['course_code']; ?>
								</td>
							</tr>
							<tr>
								<td>
								<span class="text-gray" style="font-weight: bold;"><?= $course['Curriculum']['type_credit']; ?>:</span> &nbsp;&nbsp; <?= $course['Course']['credit']; ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">L T L:</span> &nbsp;&nbsp; <?= $course['Course']['course_detail_hours']; ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold; padding-left: 50px;">Major Course:</span> &nbsp;&nbsp; <?= $course['Course']['major'] == 1 ? 'Yes' : 'No'; ?> 
									<?php
									if ($course['Course']['thesis'] == 1 ) { ?>
										<br>
										<span class="text-gray" style="font-weight: bold; padding-left: 50px;">Thesis/Dissertation/Project:</span> &nbsp;&nbsp; Yes 
										<?php
									}

									if ($course['Course']['exit_exam'] == 1 ) { ?>
										<br>
										<span class="text-gray" style="font-weight: bold; padding-left: 50px;">Exit Exam: </span> &nbsp;&nbsp; Yes 
										<?php
									} 

									if ($course['Course']['elective'] == 1 ) { ?>
										<br>
										<span class="text-gray" style="font-weight: bold; padding-left: 50px;">Elective: </span> &nbsp;&nbsp; Yes 
										<?php
									} ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Course Category:</span> &nbsp;&nbsp; <?= $course['CourseCategory']['name']; ?>
								</td>
							</tr>
							<?php
							if (!empty($course['Course']['course_description'])) { ?>
								<tr>
									<td>
										<div class="input textarea">
											<label for="CourseCourseDescription">
												<span class="text-gray" style="font-weight: bold;">Course Description:</span>
											</label>
											<br>
											<textarea name="data[Course][course_description]" cols="30" rows="10" id="CourseCourseDescription">
												<?= $course['Course']['course_description']; ?>
											</textarea>
										</div>
									</td>
								</tr>
								<?php
							} ?>

							<?php
							if (!empty($course['Course']['course_objective'])) { ?>
								<tr>
									<td>
										<div class="input textarea">
											<label for="CourseCourseObjective">
												<span class="text-gray" style="font-weight: bold;">Course Objective:</span>
											</label>
											<br>
											<textarea name="data[Course][course_objective]" cols="30" rows="10" id="CourseCourseObjective">
												<?= $course['Course']['course_objective']; ?>
											</textarea>
										</div>
									</td>
								</tr>
								<?php
							} ?>
						</tbody>
					</table>
				</div>

				<div class="large-6 columns">
					<table cellspacing="0" cellpading="0" class="table-borderless fs13">
						<tbody>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Curriculum:</span> <br><?= $this->Html->link($course['Curriculum']['name'], array('controller' => 'curriculums', 'action' => 'view', $course['Curriculum']['id'])); ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Department:</span> &nbsp;&nbsp; <?= $this->Html->link($course['Department']['name'], array('controller' => 'departments', 'action' => 'view', $course['Department']['id'])); ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Lecture Attendance Requirement:</span> &nbsp;&nbsp; <?= !empty($course['Course']['lecture_attendance_requirement']) ? $course['Course']['lecture_attendance_requirement'] :'N/A'; ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Lab Attendance Requirement:</span> &nbsp;&nbsp; <?= !empty($course['Course']['lab_attendance_requirement']) ?  $course['Course']['lab_attendance_requirement'] : 'N/A'; ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Grade Type:</span> &nbsp;&nbsp; <?= $this->Html->link($course['GradeType']['type'], array('controller' => 'grade_types', 'action' => 'view', $course['GradeType']['id'])); ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Prerequisite:</span> &nbsp;&nbsp; 
									<?php
									if (isset($course['Prerequisite']) && !empty($course['Prerequisite'])) {
										echo '<br>';
										echo '<ol>';
										foreach ($course['Prerequisite'] as $k => $v) { ?>
											<li><?= $v['PrerequisiteCourse']['course_title'] . '(' . $v['PrerequisiteCourse']['course_code'] . ')'; ?></li>
											<?php
										}
										echo '</ol>';
									} else { ?>
										None
										<?php 
									} ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="large-12 columns">
					<?php
					if (!empty($course['Book'])) { ?>
						<div class="related">
							<hr>
							<h5><?= __('Related Books'); ?></h5>
							<br>
							<?php 
							if (!empty($course['Book'])) { ?>
								<div style="overflow-x:auto;">
									<table cellspacing="0" cellpading="0" class="table">
										<thead>
											<tr>
												<td><?= __('#'); ?></td>
												<td><?= __('Title'); ?></td>
												<td><?= __('Author'); ?></td>
												<td><?= __('Year'); ?></td>
												<td><?= __('Edition'); ?></td>
											</tr>
										</thead>
										<tbody>
											<?php
											$count = 1;
											foreach ($course['Book'] as $book) {  ?>
												<tr>
													<td><?= $count++; ?></td>
													<td><?= $book['title']; ?></td>
													<td><?= $book['author']; ?></td>
													<td><?= $book['year_of_publication']; ?></td>
													<td><?= $book['edition']; ?></td>
												</tr>
												<?php 
											} ?>
										</tbody>
									</table>
								</div>
								<br>
								<?php
							} ?>
						</div>
						<?php
					}

					if (!empty($course['Journal'])) { ?>
						<div class="related">
							<hr>
							<h5><?= __('Related Journals'); ?></h5>
							<br>
							<?php 
							if (!empty($course['Journal'])) { ?>
								<div style="overflow-x:auto;">
									<table cellpadding="0" cellspacing="0" class="table">
										<thead>
											<tr>
												<td><?= __('Id'); ?></td>
												<td><?= __('Title'); ?></td>
												<td><?= __('Created'); ?></td>
												<td><?= __('Modified'); ?></td>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach ($course['Journal'] as $journal) { ?>
												<tr>
													<td><?= $journal['id']; ?></td>
													<td><?= $journal['title']; ?></td>
													<td><?= $journal['created']; ?></td>
													<td><?= $journal['modified']; ?></td>
												</tr>
												<?php 
											} ?>
										</tbody>
									</table>
								</div>
								<br>
								<?php
							} ?>
						</div>
						<?php
					}

					if (!empty($course['Weblink'])) { ?>
						<div class="related">
							<hr>
							<h5><?= __('Related Weblinks'); ?></h5>
							<br>
							<?php 
							if (!empty($course['Weblink'])) { ?>
								<div style="overflow-x:auto;">
									<table cellpadding="0" cellspacing="0" class="table-borderless">
										<thead>
											<tr>
												<td><?= __('Id'); ?></td>
												<td><?= __('Title'); ?></td>
												<td><?= __('Url Address'); ?></td>
												<td><?= __('Created'); ?></td>
												<td><?= __('Modified'); ?></td>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach ($course['Weblink'] as $weblink) { ?>
												<tr>
													<td><?= $weblink['id']; ?></td>
													<td><?= $weblink['title']; ?></td>
													<td><?= $weblink['url_address']; ?></td>
													<td><?= $weblink['created']; ?></td>
													<td><?= $weblink['modified']; ?></td>
												</tr>
												<?php 
											} ?>
										</tbody>
									</table>
								</div>
								<br>
								<?php 
							} ?>
						</div>
						<?php 
					} ?>
				</div>
				<?php
			} else { ?>
				<div class="large-12 columns">
					<div id="ErrorMessage" class="error-box error-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span> Course not found!!</div>
				</div>
				<?php
			} ?>
		</div>
	</div>
</div>