<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Published Course Details: ' . (isset($publishedCourse['Course']['course_code_title']) ? $publishedCourse['Course']['course_code_title'] : '') . (isset($publishedCourse['PublishedCourse']['id']) ? '  (' . $publishedCourse['PublishedCourse']['academic_year'] . ', ' . $publishedCourse['PublishedCourse']['semester'] . ')' : ''); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;">
					<hr>
					<table cellpadding="0" cellspacing="0" class="table">
						<tbody>
							<tr>
								<td><span class="text-gray" style="font-weight: bold;">Course:</span> &nbsp;&nbsp; <?= $this->Html->link($publishedCourse['Course']['course_code_title'], array('controller' => 'courses', 'action' => 'view', $publishedCourse['Course']['id'])); ?>  <?= (isset($publishedCourse['PublishedCourse']['published']) && $publishedCourse['PublishedCourse']['drop'] == 1 ? ' &nbsp; &nbsp; &nbsp; <span class="rejected">(Mass Drop)</span>': ((isset($publishedCourse['PublishedCourse']['published']) && $publishedCourse['PublishedCourse']['add'] == 1 ? ' &nbsp; &nbsp; &nbsp; <span class="on-process">(Mass Add)</span>': ' &nbsp; &nbsp; &nbsp; <span class="accepted">(Registration)</span>'))); ?> </td>
							</tr>
							<tr>
								<td><span class="text-gray" style="font-weight: bold;">Section:</span> &nbsp;&nbsp; <?= $this->Html->link($publishedCourse['Section']['name'] . '('. $publishedCourse['PublishedCourse']['academic_year']. ',  ' . ($publishedCourse['PublishedCourse']['semester'] == 'I' ? '1st' : ($publishedCourse['PublishedCourse']['semester'] == 'II' ? '2nd' : ($publishedCourse['PublishedCourse']['semester'] == 'III' ? '3rd' : $publishedCourse['PublishedCourse']['semester']))) . ' semester, '  . (isset($publishedCourse['YearLevel']['id']) ? $publishedCourse['YearLevel']['name'] . ' year' : ($publishedCourse['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $publishedCourse['Program']['name'] . ', ' . $publishedCourse['ProgramType']['name']. ')', array('controller' => 'sections', 'action' => 'view', $publishedCourse['Section']['id'])); ?></td>
							</tr>
							</tr>
							<tr>
								<td><span class="text-gray" style="font-weight: bold;">Department:</span> &nbsp;&nbsp; <?= $this->Html->link($publishedCourse['Department']['name'] . ' ('. $publishedCourse['Department']['College']['name'] . ', ' . $publishedCourse['Department']['College']['Campus']['name'] .')', array('controller' => 'departments', 'action' => 'view', $publishedCourse['Department']['id'])); ?></td>
							</tr>
							<?php
							if (isset($publishedCourse['PublishedCourse']['given_by_department_id']) && isset($publishedCourse['PublishedCourse']['department_id']) && $publishedCourse['PublishedCourse']['given_by_department_id'] != $publishedCourse['PublishedCourse']['department_id']) { ?>
								<tr>
									<td><span class="text-gray" style="font-weight: bold;">Given by Department: </span> &nbsp;&nbsp; <?= $this->Html->link($publishedCourse['GivenByDepartment']['name'] . ' ('. $publishedCourse['GivenByDepartment']['College']['name'] . ', ' . $publishedCourse['GivenByDepartment']['College']['Campus']['name'] .')', array('controller' => 'departments', 'action' => 'view', $publishedCourse['GivenByDepartment']['id'])); ?></td>
								</tr>
								<?php
							} ?>
							<tr>
								<td><span class="text-gray" style="font-weight: bold;">Published on:</span> &nbsp;&nbsp;  <?= $this->Time->format("F j, Y h:i:s A", $publishedCourse['PublishedCourse']['created'], NULL, NULL); ?> </td>
							</tr>
							<?php
							if (isset($publishedCourse['PublishedCourse']['created']) && $publishedCourse['PublishedCourse']['created'] !== $publishedCourse['PublishedCourse']['modified']) { ?>
								<tr>
									<td><span class="text-gray" style="font-weight: bold;">Publication Modified on:</span> &nbsp;&nbsp;  <?= $this->Time->format("F j, Y h:i:s A", $publishedCourse['PublishedCourse']['modified'], NULL, NULL); ?> </td>
								</tr>
								<?php
							} 
							if (isset($publishedCourse['CourseInstructorAssignment'][0]['Staff']) && !empty($publishedCourse['CourseInstructorAssignment'][0]['Staff'])) { ?>
								<tr>
									<td><span class="text-gray" style="font-weight: bold;">Assigned Instructor:</span> &nbsp;&nbsp;  <?= (isset($publishedCourse['CourseInstructorAssignment'][0]['Staff']['Title']['title']) ? $publishedCourse['CourseInstructorAssignment'][0]['Staff']['Title']['title']. '. ' : '') . $publishedCourse['CourseInstructorAssignment'][0]['Staff']['full_name'] . (isset($publishedCourse['CourseInstructorAssignment'][0]['Staff']['Position']['position']) ? ' (' . $publishedCourse['CourseInstructorAssignment'][0]['Staff']['Position']['position']. ')' : '') ?></td>
								</tr>
								<tr>
									<td><span class="text-gray" style="font-weight: bold;">Date Assigned:</span> &nbsp;&nbsp;  <?= $this->Time->format("F j, Y h:i:s A", $publishedCourse['CourseInstructorAssignment'][0]['created'], NULL, NULL); ?></td>
								</tr>
								<?php
							} ?>

							<?php //echo (isset($publishedCourse['CourseInstructorAssignment'][0]['Staff']) && !empty($publishedCourse['CourseInstructorAssignment'][0]['Staff']) ? '<tr><td><span class="text-gray" style="font-weight: bold;">Assigned Instructor:</span> &nbsp;&nbsp;' . (isset($publishedCourse['CourseInstructorAssignment'][0]['Staff']['Title']['title']) ? $publishedCourse['CourseInstructorAssignment'][0]['Staff']['Title']['title']. '. ' : '') . $publishedCourse['CourseInstructorAssignment'][0]['Staff']['full_name'] . (isset($publishedCourse['CourseInstructorAssignment'][0]['Staff']['Position']['position']) ? ' (' . $publishedCourse['CourseInstructorAssignment'][0]['Staff']['Position']['position']. ')' : '') . '</td></tr>': ''); ?>
						</tbody>
					</table>

					<?php 
					/* if (!empty($section['Student'])) { ?>
						<hr>
						<h6 class="text-gray"><?= __('Related Students'); ?></h6>
						<hr>
						<div style="overflow-x:auto;">
							<?php 
							if (!empty($section['Student'])) { ?>
								<table cellpadding="0" cellspacing="0" class="table">
									<thead>
										<tr>
											<td class="center">#</td>
											<td class="center"><?= __('Full Name'); ?></td>
											<td class="center"><?= __('Student ID'); ?></td>
											<td class="center"><?= __('Sex'); ?></td>
											<td class="center"><?= __('Department'); ?></td>
										</tr>
									</thead>
									<tbody>
										<?php
										$count = 1;
										foreach ($section['Student'] as $student) { ?>
											<tr>
												<td class="center"><?= $count++; ?></td>
												<td class="vcenter"><?= $student['full_name']; ?></td>
												<td class="center"><?= $student['studentnumber']; ?></td>
												<td class="center"><?= (strcasecmp(trim($student['gender']), 'male') == 0 ? 'M' :(strcasecmp(trim($student['gender']), 'female') == 0 ? 'F' : $student['gender'])); ?></td>
												<td class="center"><?= (isset($student['Department']['name']) ? $student['Department']['name'] : 'Pre/Freshman'); ?></td>
											</tr>
											<?php
										} ?>
									</tbody>
								</table>
								<?php 
							} ?>
						</div>
						<?php
					} */ ?>
				</div>
			</div>
		</div>
	</div>
</div>
