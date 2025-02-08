<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Grade Scale Details: ' . (isset($gradeScale['GradeScale']['name']) ? $gradeScale['GradeScale']['name'] : ''); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">

			<?php
			if (!empty($gradeScale['GradeScale'])) { ?>
				<div class="large-12 columns">
					<table cellspacing="0" cellpading="0" class="table-borderless fs13">
						<tbody>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Name:</span> &nbsp;&nbsp; <?= $gradeScale['GradeScale']['name']; ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Grade Type:</span> &nbsp;&nbsp; <?= $this->Html->link($gradeType['GradeType']['type'], array('controller' => 'grade_types', 'action' => 'view', $gradeType['GradeType']['id'])); ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Program:</span> &nbsp;&nbsp; <?= $this->Html->link($gradeScale['Program']['name'], array('controller' => 'programs', 'action' => 'view', $gradeScale['Program']['id'])); ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Active:</span> &nbsp;&nbsp; <?= $gradeScale['GradeScale']['active'] == 1 ? 'Yes' : 'No'; ?>
								</td>
							</tr>

							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Created:</span> &nbsp;&nbsp; <?= $this->Time->format("M j, Y g:i A", $gradeScale['GradeScale']['created'], NULL, NULL); ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Modified:</span> &nbsp;&nbsp; <?= $this->Time->format("M j, Y g:i A", $gradeScale['GradeScale']['modified'], NULL, NULL); ?>
								</td>
							</tr>
							<tr>
								<td>
									<span class="text-gray" style="font-weight: bold;">Target:</span> &nbsp;&nbsp; 
									<?php 
									if (isset($college) && !empty($college)) { ?>
										<?= $gradeScale['GradeScale']['model'] . ' (' . $college['College']['name'] . ')'; ?> &nbsp;
										<?php
									} else if (isset($department) && !empty($department)) { ?>
										<?= $gradeScale['GradeScale']['model'] . ' (' . $department['Department']['name'] . ')'; ?> &nbsp;
										<?php 
									} ?>
								</td>
							</tr>
						</tbody>
					</table>

					<p><h6 class="text-gray"><?= __('List of defined exam result ranges and  applicable grades for <b>' . $gradeScale['GradeScale']['name'] . '</b> Grade Scale'); ?></h6></p>

					<?php 
					if (!empty($gradeScale['GradeScaleDetail'])) { ?>
						<table cellpadding="0" cellspacing="0" class="responsive table-borderless fs13">
							<thead>
								<tr>
									<!-- <td>#</td> -->
									<td style="text-align:center"><?= __('Grade'); ?></td>
									<td style="text-align:center"><?= __('Minimum'); ?></th>
									<td style="text-align:center"><?= __('Maximum'); ?></td>
									<td style="text-align:center"><?= __('Pass Grade'); ?></td>
									<td style="text-align:center"><?= __('Point Value'); ?></td>
									<td style="text-align:center"><?= __('Repeatable'); ?></td>
									<td style="text-align:center"><?= __('Date Created'); ?></td>
									<td style="text-align:center"><?= __('Date Modified'); ?></td>
								</tr>
							</thead>
							<tbody>
								<?php
								$counter = 1;
								foreach ($gradeScale['GradeScaleDetail'] as $gradeScaleDetail) { //debug($gradeScale['GradeScaleDetail']); ?>
									<tr>
										<!-- <td><?= $counter++; ?></td> -->
										<td style="text-align:center"><?= $gradeScaleDetail['Grade']['grade']; ?></td>
										<td style="text-align:center"><?= $gradeScaleDetail['minimum_result']; ?></td>
										<td style="text-align:center"><?= $gradeScaleDetail['maximum_result']; ?></td>
										<td style="text-align:center"><?= $gradeScaleDetail['Grade']['pass_grade'] == 1 ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>'; ?></td>
										<td style="text-align:center"><?= $gradeScaleDetail['Grade']['point_value']; ?></td>
										<td style="text-align:center"><?= $gradeScaleDetail['Grade']['allow_repetition'] == 1 ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>'; ?></td>
										<td style="text-align:center"><?= $this->Time->format("M j, Y g:i A", $gradeScaleDetail['created'], NULL, NULL); ?></td>
										<td style="text-align:center"><?= $this->Time->format("M j, Y g:i A", $gradeScaleDetail['modified'], NULL, NULL); ?></td>
									</tr>
									<?php 
								} ?>
							</tbody>
						</table>
					<?php } ?>
				</div>
			<?php
			} else {
				echo '<div class="large-12 columns"><div id="ErrorMessage" class="error-box error-message"><span style="margin-right: 15px;"></span> Grade scale not found!! </div></div>';
			} ?>
		</div>
	</div> 
</div>