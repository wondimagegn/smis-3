<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Grade Type Details: ' . (isset($gradeType['GradeType']['type']) ? $gradeType['GradeType']['type'] : ''); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<table cellpadding="0" cellspacing="0" class="table-borderless fs13">
					<tr>
						<td style="width:15%"><span class="text-gray" style="font-weight: bold;">Type:</span> &nbsp;&nbsp; <?= $gradeType['GradeType']['type']; ?></td>
					</tr>
					<tr>
						<td><span class="text-gray" style="font-weight: bold;">Date Created:</span> &nbsp;&nbsp; <?= $this->Time->format("M j, Y g:i A", $gradeType['GradeType']['created'], NULL, NULL); ?></td>
					</tr>
					<tr>
						<td><span class="text-gray" style="font-weight: bold;">Date Modified:</span> &nbsp;&nbsp; <?= $this->Time->format("M j, Y g:i A", $gradeType['GradeType']['modified'], NULL, NULL); ?></td>
					</tr>
				</table>
				<hr>
				<h5 class="fs15 text-gray"><?= __('List of Grades for ' . $gradeType['GradeType']['type'] . ' grade type'); ?></h5>
				<br>
			</div>
			<?php
			if (!empty($gradeType['Grade'])) { ?>
				<div class="large-12 columns">
					<table cellpadding="0" cellspacing="0" class="responsive table-borderless fs13">
						<thead>
							<tr>
								<!-- <td>#</td> -->
								<td style="text-align: center;"><?= __('Grade'); ?></th>
								<td style="text-align: center;"><?= __('Point Value'); ?></td>
								<td style="text-align: center;"><?= __('Pass Grade'); ?></td>
								<td style="text-align: center;"><?= __('Repeatable'); ?></td>
								<td style="text-align: center;"><?= __('Active'); ?></td>
								<td style="text-align:center"><?= __('Actions'); ?></td>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							foreach ($gradeType['Grade'] as $grade) { ?>
								<tr>
									<!-- <td><?= $count++; ?></td> -->
									<td style="text-align: center;"><?= $grade['grade']; ?></td>
									<td style="text-align: center;"><?= $grade['point_value']; ?></td>
									<td style="text-align: center;"><?= ($grade['pass_grade'] == 1 ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>'); ?></td>
									<td style="text-align: center;"><?= ($grade['allow_repetition'] == 1 ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>'); ?></td>
									<td style="text-align: center;"><?= ($grade['active'] == 1 ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>'); ?></td>
									<td class="actions">
										<?= $this->Html->link(__(''), array('action' => 'edit', $gradeType['GradeType']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit'));
										$action_controller_id = 'view~gradeTypes~' . $grade['grade_type_id']; ?> &nbsp;
										<?= $this->Html->link(__(''), array('controller' => 'grades', 'action' => 'delete', $grade['id'], $action_controller_id), array('class' => 'fontello-trash', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete %s grade?'), $grade['grade'])); ?>
									</td>
								</tr>
								<?php
							} ?>
						</tbody>
					</table>
				</div>
				<?php
			} ?>
		</div>
	</div>
</div>