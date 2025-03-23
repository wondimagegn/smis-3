<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="large-6 columns">
				<h5 class="text-gray"><span><?= __('Basic Information'); ?></span></h5>
				<table cellpadding="0" cellspacing="0" class="table-borderless">
					<tr>
						<td><?= __('Title'); ?></td>
						<td><?= $staff['Title']['title']; ?></td>
					</tr>
					<tr>
						<td><?= __('Name'); ?></td>
						<td><?= $staff['Staff']['full_name']; ?></td>
					</tr>
					<tr>
						<td><?= __('Position'); ?></td>
						<td><?= $staff['Position']['position']; ?></td>
					</tr>
					<tr>
						<td><?= __('Username'); ?></td>
						<td><?= $staff['User']['username']; ?></td>
					</tr>
					<tr>
						<td><?= __('Status'); ?></td>
						<td>
							<?php
							$accountStaus = null;
							if ($staff['User']['active'] == 1) {
								$accountStaus = "Account Active";
							} else if ($staff['User']['active'] == 0) {
								$accountStaus = "Account Deactivated";
							}
							echo $accountStaus;
							?>
						</td>
					</tr>
					<tr>
						<td><?= __('Email'); ?></td>
						<td><?= $staff['Staff']['email']; ?></td>
					</tr>
					<tr>
						<td><?= __('Mobile'); ?></td>
						<td><?= $staff['Staff']['phone_mobile']; ?></td>
					</tr>
					<tr>
						<td><?= __('Gender'); ?></td>
						<td><?= (strcasecmp(trim($staff['Staff']['gender']), 'male') == 0 ?  'Male' : 'Female'); ?></td>
					</tr>
					<tr>
						<td><?= __('Role'); ?></td>
						<td><?= $staff['User']['Role']['name']; ?></td>
					</tr>
					<tr>
						<td><?= __('College'); ?></td>
						<td><?= $this->Html->link($staff['College']['name'], array('controller' => 'colleges', 'action' => 'view', $staff['College']['id'])); ?></td>
					</tr>
					<tr>
						<td><?= __('Department'); ?></td>
						<td><?= $this->Html->link($staff['Department']['name'], array('controller' => 'departments', 'action' => 'view', $staff['Department']['id'])); ?></td>
					</tr>
				</table>
			</div>
			<div class="large-6 columns">
				<?php
				if (!empty($staff['CourseInstructorAssignment'])) { ?>
					<h6 class="text-gray"><span><?= __('Courses Taught'); ?></span></h6>
					<table cellpadding="0" cellspacing="0" class="responsive table-borderless">
						<thead>
							<tr>
								<td style="width: 10%;">#</td>
								<td>Course</td>
								<td>Section</td>
								<td style="text-align: center;">ACY</td>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							$totalCredit = 0;
							foreach ($staff['CourseInstructorAssignment'] as $k => $v) {
								//debug($v);
								if (isset($v['PublishedCourse']) && !empty($v['PublishedCourse'])) {
									$totalCredit += $v['PublishedCourse']['Course']['credit']; ?>
									<tr>
										<td><?= $count++; ?></td>
										<td><?= $v['PublishedCourse']['Course']['course_title']; ?></td>
										<td><?= $v['PublishedCourse']['Section']['name']; ?></td>
										<td style="text-align: center;"><?= $v['PublishedCourse']['academic_year'] . '/' . $v['PublishedCourse']['semester']; ?></td>
									</tr>
								<?php
								}
							} ?>
							<tr>
								<td></td>
								<td colspan="3"> <span style="font-weight: bold;">Total: <?= $count . ' courses (' . $totalCredit . ' Cr.)'; ?></span></td>
							</tr>
						</tbody>
					</table>
					<?php
				} ?>
			</div>
		</div>
	</div>
</div>