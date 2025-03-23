<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Department Details: ' . (isset($department['Department']['name']) ? $department['Department']['name'] : '') . (isset($department['Department']['shortname']) ? '  (' . $department['Department']['shortname'] . ')' : ''); ?></span>
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
								<td><span class="text-gray" style="font-weight: bold;">Name:</span> &nbsp;&nbsp; <?= $department['Department']['name']; ?></td>
							</tr>
							<tr>
								<td><span class="text-gray" style="font-weight: bold;">Short Name:</span> &nbsp;&nbsp; <?= $department['Department']['shortname']; ?></td>
							</tr>
							<tr>
								<td><span class="text-gray" style="font-weight: bold;">Institution Code:</span> &nbsp;&nbsp; <?= (isset($department['Department']['institution_code']) ? $department['Department']['institution_code'] : '---'); ?></td>
							</tr>
							<tr>
								<td><span class="text-gray" style="font-weight: bold;">Active:</span> &nbsp;&nbsp;  <?= (isset($department['Department']['active']) && $department['Department']['active'] == 0 ? 'No': 'Yes'); ?> </td>
							</tr>
							<tr>
								<td><span class="text-gray" style="font-weight: bold;">Year Level Curriculum Definition Allowed:</span> &nbsp;&nbsp;  <?= (isset($department['Department']['allow_year_based_curriculums']) && $department['Department']['allow_year_based_curriculums'] == 0 ? 'No': 'Yes'); ?> </td>
							</tr>
							<tr>
								<td><span class="text-gray" style="font-weight: bold;">Moodle Category ID:</span> &nbsp;&nbsp; <?= (isset($department['Department']['moodle_category_id']) ? $department['Department']['moodle_category_id'] : 'N/A'); ?></td>
							</tr>
							<?php
							if (!empty($department['Department']['created']) && $department['Department']['created'] != '0000-00-00') { ?>
								<tr>
									<td><span class="text-gray" style="font-weight: bold;">Created on:</span> &nbsp;&nbsp;  <?= $this->Time->format("F j, Y", $department['Department']['created'], NULL, NULL); ?> </td>
								</tr>
								<?php
							} ?>
							<tr>
								<td><span class="text-gray" style="font-weight: bold;">Modified on:</span> &nbsp;&nbsp;  <?= $this->Time->format("F j, Y", $department['Department']['modified'], NULL, NULL); ?> </td>
							</tr>
							<tr>
								<td><span class="text-gray" style="font-weight: bold;">Located at:</span> &nbsp;&nbsp; <?= $this->Html->link($department['College']['name'] . ' (' . $department['College']['Campus']['name'] .')', array('controller' => 'campuses', 'action' => 'view', $department['College']['Campus']['id'])); ?></td>
							</tr> 
							<?php
							if (!empty($department['Department']['description'])) { ?>
								<tr>
									<td>
									<span class="text-gray" style="font-weight: bold;">Description:</span>
										<p class="fs14" style="text-align: justify; margin: 10px">
											<?= $department['Department']['description']; ?>
										</p>
									</td>
								</tr>
								<?php
							} ?>
						</tbody>
					</table>

					<div class="related">
						<hr>
						<h6 class="text-gray">Related Grade Scales (Department Level: <?= $department['Department']['name']; ?> )</h6>
						<br>
						<?php
						if (!empty($department['GradeScale'])) { ?>
							<div style="overflow-x:auto;">
								<table cellpadding="0" cellspacing="0" class="table">
									<thead>
										<tr>
											<td class="center">#</td>
											<td class="vcenter"><?= __('Name'); ?></td>
											<td class="vcenter"><?= __('Grade Type'); ?></td>
											<td class="center"><?= __('Program'); ?></td>
											<td class="center"><?= __('Own'); ?></td>
											<td class="center"><?= __('One-Time'); ?></td>
											<td class="center"><?= __('Active'); ?></td>
											<td class="center"><?= __('Actions'); ?></td>
										</tr>
									</thead>
									<tbody>
										<?php
										$count1 = 1;
										foreach ($department['GradeScale'] as $gradeScale) { ?>
											<tr>
												<td class="center"><?= $count1++; ?></td>
												<td class="vcenter"><?= $gradeScale['name']; ?></td>
												<td class="vcenter"><?= (isset($gradeScale['GradeType']['type']) ? $gradeScale['GradeType']['type'] : '---'); ?></td>
												<td class="center"><?= $gradeScale['Program']['name']; ?></td>
												<td class="center"><?= (isset($gradeScale['own']) && $gradeScale['own'] == 0 ? '<span style="color:red">No</span>' : '<span style="color:green">Yes</span>'); ?></td>
												<td class="center"><?= (isset($gradeScale['one_time']) && $gradeScale['one_time'] == 0 ? '<span style="color:red">No</span>' : '<span style="color:green">Yes</span>'); ?></td>
												<td class="center"><?= (isset($gradeScale['active']) && $gradeScale['active'] == 0 ? '<span style="color:red">No</span>' : '<span style="color:green">Yes</span>'); ?></td>
												<td class="center">
													<?= $this->Html->link(__(''), array('controller' => 'gradeScales', 'action' => 'view', $gradeScale['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?> &nbsp;
												</td>
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

					<div class="related">
						<hr>
						<h6 class="text-gray">Related Grade Scales (College Level: <?= $college_level_defined_grade_scales['College']['name']; ?>)</h6>
						<br>
						<?php
						if (!empty($college_level_defined_grade_scales['GradeScale'])) { ?>
							<div style="overflow-x:auto;">
								<table cellpadding="0" cellspacing="0" class="table">
									<thead>
										<tr>
											<td class="center">#</td>
											<td class="vcenter"><?= __('Name'); ?></td>
											<td class="vcenter"><?= __('Grade Type'); ?></td>
											<td class="center"><?= __('Program'); ?></td>
											<td class="center"><?= __('Own'); ?></td>
											<td class="center"><?= __('One-Time'); ?></td>
											<td class="center"><?= __('Active'); ?></td>
											<td class="center"><?= __('Actions'); ?></td>
										</tr>
									</thead>
									<tbody>
										<?php
										$count1 = 1;
										foreach ($college_level_defined_grade_scales['GradeScale'] as $gradeScale) { ?>
											<tr>
												<td class="center"><?= $count1++; ?></td>
												<td class="vcenter"><?= $gradeScale['name']; ?></td>
												<td class="vcenter"><?= (isset($gradeScale['GradeType']['type']) ? $gradeScale['GradeType']['type'] : '---'); ?></td>
												<td class="center"><?= $gradeScale['Program']['name']; ?></td>
												<td class="center"><?= (isset($gradeScale['own']) && $gradeScale['own'] == 0 ? '<span style="color:red">No</span>' : '<span style="color:green">Yes</span>'); ?></td>
												<td class="center"><?= (isset($gradeScale['one_time']) && $gradeScale['one_time'] == 0 ? '<span style="color:red">No</span>' : '<span style="color:green">Yes</span>'); ?></td>
												<td class="center"><?= (isset($gradeScale['active']) && $gradeScale['active'] == 0 ? '<span style="color:red">No</span>' : '<span style="color:green">Yes</span>'); ?></td>
												<td class="center">
													<?= $this->Html->link(__(''), array('controller' => 'gradeScales', 'action' => 'view', $gradeScale['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?> &nbsp;
												</td>
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

					<div class="related">
						<hr>
						<h6 class="text-gray"><?= __('Related Staffs'); ?></h6>
						<br>
						<?php 
						if (!empty($department['Staff'])) {?>
							<div style="overflow-x:auto;">
								<table cellpadding="0" cellspacing="0" class="table">
									<thead>
										<tr>
											<td class="center">#</td>
											<td class="center"><?= __('Title'); ?></td>
											<td class="vcenter"><?= __('First Name'); ?></td>
											<td class="vcenter"><?= __('Middle Name'); ?></th>
											<td class="vcenter"><?= __('Last Name'); ?></th>
											<td class="vcenter"><?= __('Position'); ?></td>
											<td class="vcenter"><?= __('Department'); ?></td>
											<td class="center"><?= __('Active'); ?></td>
											<td class="center"><?= __('Actions'); ?></td>
										</tr>
									</thead>
									<tbody>
										<?php
										$i = 1;
										foreach ($department['Staff'] as $staff) { ?>
											<tr>
												<td class="center"><?= $i++; ?></td>
												<td class="center"><?= (isset($staff['Title']['title']) ? $staff['Title']['title'] . '.': ''); ?></td>
												<td class="vcenter"><?= $staff['first_name']; ?></td>
												<td class="vcenter"><?= $staff['middle_name']; ?></td>
												<td class="vcenter"><?= $staff['last_name']; ?></td>
												<td class="vcenter"><?= (isset($staff['Position']['position']) ? $staff['Position']['position']: ''); ?></td>
												<td class="vcenter"><?= (isset($staff['Department']['name']) ? $staff['Department']['name']: ''); ?></td>
												<td class="center"><?= (isset($staff['active']) && $staff['active'] == 0 ? '<span style="color:red">No</span>' : '<span style="color:green">Yes</span>'); ?></td>
												<td class="center">
													<?= $this->Html->link(__(''), array('controller' => 'staffs', 'action' => 'view', $staff['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?> &nbsp;
													<?php
													if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) { ?>
														<?= $this->Html->link(__(''), array('controller' => 'staffs', 'action' => 'edit', $staff['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?> &nbsp;
														<?php
													} ?>
												</td>
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
				</div>
			</div>
		</div>
	</div>
</div>
