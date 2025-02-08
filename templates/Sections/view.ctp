<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= 'Section Details: ' . (isset($section['Section']['name']) ? $section['Section']['name'] . (isset($section['YearLevel']['name']) ? ' (' . $section['YearLevel']['name'] . ', ' . $section['Section']['academicyear'].') ' : ' (Pre/1st) in ' . $section['Section']['academicyear']) : ''); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?php
				if (!empty($section)) { ?>
					<table cellspacing="0" cellpading="0" class="table">
						<tbody>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Name:</span> &nbsp; <?= $section['Section']['name']; ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">College:</span> &nbsp; <?= $this->Html->link($section['College']['name'], array('controller' => 'colleges', 'action' => 'view', $section['College']['id'])); ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Department:</span> &nbsp; <?= (isset($section['Department']['name']) ? $this->Html->link($section['Department']['name'], array('controller' => 'departments', 'action' => 'view', $section['Department']['id'])) : 'Pre/Freshman'); ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Program:</span> &nbsp; <?= $this->Html->link($section['Program']['name'], array('controller' => 'programs', 'action' => 'view', $section['Program']['id'])); ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Program Type:</span> &nbsp; <?= $this->Html->link($section['ProgramType']['name'], array('controller' => 'program_types', 'action' => 'view', $section['ProgramType']['id'])); ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Academic Year:</span> &nbsp; <?= $section['Section']['academicyear']; ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Year Level:</span> &nbsp; <?= isset($section['YearLevel']['name']) ? $section['YearLevel']['name'] : 'Pre/1st'; ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Archieved:</span> &nbsp; <?= $section['Section']['archive'] == 1 ? 'Yes' : 'No'; ?>
								</td>
							</tr>

							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Created:</span> &nbsp; <?= $this->Time->format("M j, Y g:i A", $section['Section']['created'], NULL, NULL); ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Modified:</span> &nbsp; <?= $this->Time->format("M j, Y g:i A", $section['Section']['modified'], NULL, NULL); ?>
								</td>
							</tr>
						</tbody>
					</table>
					<?php
				}  else {
					echo '<div class="large-12 columns"><div id="ErrorMessage" class="error-box error-message"><span style="margin-right: 15px;"></span> Section not found or you don\'t have the privilage to view the selected Section. </div></div>';
				} ?>

				<?php 
				if (!empty($section['Student'])) { ?>
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
										<td class="center"><?= __('College'); ?></td>
										<td class="center"><?= __('Department'); ?></td>
										<td class="center"><?= __('Program'); ?></td>
										<td class="center"><?= __('Program Type'); ?></td>
										<td class="center"><?= __('Email'); ?></td>
										<td class="center"><?= __('Mobile'); ?></td>
										<td class="center"><?= __('Actions'); ?></td>
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
											<td class="center"><?= $student['College']['shortname']; ?></td>
											<td class="center"><?= (isset($student['Department']['name']) ? $student['Department']['name'] : 'Pre/Freshman'); ?></td>
											<td class="center"><?= $student['Program']['shortname']; ?></td>
											<td class="center"><?= $student['ProgramType']['name']; ?></td>
											<td class="center"><?= $student['email']; ?></td>
											<td class="center"><?= $student['phone_mobile']; ?></td>
											<td class="center">
												<?= $this->Html->link(__('View'), array('controller' => 'students', 'action' => 'view', $student['id'])); ?>
											</td>
										</tr>
										<?php
									} ?>
								</tbody>
							</table>
							<?php 
						} ?>
					</div>
					<?php
			 	} ?>
			</div>
		</div>
	</div>
</div>