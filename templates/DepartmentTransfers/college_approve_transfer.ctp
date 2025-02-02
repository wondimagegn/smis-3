<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Department Transfer Request Approval'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div style="margin-top: -30px;"><hr></div>

				<?php
				
				$students_count_to_approve = 0;

				$options = array('1' => 'Accepted', '-1' => 'Rejected');
				$attributes = array('legend' => false, 'label' => false, 'separator' => '<br/>'); 
				
				echo $this->Form->create('DepartmentTransfer', array('novalidate' => true));

				if (!empty($departmentTransfersIncomingToYourDepartment)) { ?>

					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>List of students department transfer request approved by the sender department and college and waiting your college decision.</div>
					<hr>

					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<th class="center" style="width: 3%;">#</th>
									<th class="vcenter" style="width: 20%;">Student Name</th>
									<th class="center" style="width: 5%;">Semesters Attended</th>
									<th class="center">Sender Department Approval</th>
									<th class="center">Sender College Approval</th>
									<th class="center">Destination Department</th>
									<th class="center" style="width: 15%;">Approval</th>
									<th class="center">Remark</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$start = 0;
								foreach ($departmentTransfersIncomingToYourDepartment as $departmentTransfer) { ?>
									<tr>
										<td class="center"><?= ++$start; ?></td>
										<td class="vcenter"><?= $this->Html->link($departmentTransfer['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $departmentTransfer['Student']['id'])); ?></td>
										<td class="center"><?= count($departmentTransfer['DepartmentTransfer']['semester_attended']); ?></td>
										<td class="center"><?= ($departmentTransfer['DepartmentTransfer']['sender_department_approval'] == 1 ? 'Accepted' : ($departmentTransfer['DepartmentTransfer']['sender_department_approval'] == -1 ? 'Rejected' : '---')); ?></td>
										<td class="center"><?= ($departmentTransfer['DepartmentTransfer']['sender_college_approval'] == 1 ? 'Accepted' : ($departmentTransfer['DepartmentTransfer']['sender_college_approval'] == -1 ? 'Rejected' : '---')); ?></td>
										<td class="center"><?= $departmentTransfer['Department']['name']; ?></td>
										<td class="center">
											<?php
											if (count($departmentTransfer['DepartmentTransfer']['semester_attended']) <= MAXIMUM_ALLOWED_ATTENDED_SEMESTERS_FOR_TRANSFER) { 
												$students_count_to_approve ++; ?>
												<?= $this->Form->hidden('DepartmentTransfer.' . $start . '.id', array('value' => $departmentTransfer['DepartmentTransfer']['id'])); ?>
												<?= $this->Form->hidden('DepartmentTransfer.' . $start . '.student_id', array('value' => $departmentTransfer['Student']['id'])); ?>
												<?= $this->Form->radio('DepartmentTransfer.' . $start . '.receiver_college_approval', $options, $attributes); ?>
												<?php
											} else {
												echo 'TRANSFER CANCELLED<br>MAX SEMESTER';
											} ?>
										</td>
										<td class="center"><?= $this->Form->input('DepartmentTransfer.' . $start . '.receiver_college_remark', array('label' => false)); ?></td>
									</tr>
									<?php 
								} ?>
							</tbody>
						</table>
					</div>
					<?php
				} ?>

				<?php

				if (!empty($departmentTransfersLeaverRequest)) { ?>

					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>List of your college students who submitted department transfer request and waiting your decision for transfer, those accepted transfer requests will be forwarded to destionation department for approval.</div>
					<hr>

					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<th class="center" style="width: 3%;">#</th>
									<th class="vcenter" style="width: 20%;">Student Name</th>
									<th class="center" style="width: 5%;">Semester Attended</th>
									<th class="center">Destination Department</th>
									<th class="center">Minute Number</th>
									<th class="center" style="width: 15%;">Approval</th>
									<th class="center">Remark</th>
								</tr>
							</thead>
							<tbody>
								<?php

								if (!isset($start)) {
									$start = 0;
								} 

								foreach ($departmentTransfersLeaverRequest as $departmentTransfer) { ?>
									<tr>
										<td class="center"><?= ++$start; ?></td>
										<td class="center"><?= $this->Html->link($departmentTransfer['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $departmentTransfer['Student']['id'])); ?></td>
										<td class="center"><?= count($departmentTransfer['DepartmentTransfer']['semester_attended']); ?></td>
										<td class="center"><?=  $departmentTransfer['Department']['name']; ?></td>
										<td class="center"><?= $departmentTransfer['DepartmentTransfer']['minute_number']; ?></td>
										<td class="center">
											<?php
											if (count($departmentTransfer['DepartmentTransfer']['semester_attended']) <= MAXIMUM_ALLOWED_ATTENDED_SEMESTERS_FOR_TRANSFER) { 
												$students_count_to_approve ++; ?>
												<?= $this->Form->hidden('DepartmentTransfer.' . $start . '.id', array('value' => $departmentTransfer['DepartmentTransfer']['id'])); ?>
												<?= $this->Form->hidden('DepartmentTransfer.' . $start . '.student_id', array('value' => $departmentTransfer['Student']['id'])); ?>
												<?= $this->Form->radio('DepartmentTransfer.' . $start . '.sender_college_approval', $options, $attributes); ?>
												<?php
											} else {
												echo 'TRANSFER CANCELLED<br>MAX SEMESTER';
											} ?>
										</td>
										<td class="center"><?= $this->Form->input('DepartmentTransfer.' . $start . '.sender_college_remark', array('label' => false)); ?></td>
									</tr>
									<?php 
								} ?>
							</tbody>
						</table>
					</div>
					<?php
				} 
				
				if ($students_count_to_approve) { 
					echo '<hr>'. $this->Form->submit('submit', array('name' => 'saveIt', 'class' => 'tiny radius button bg-blue', 'div' =>'false'));
				} ?>
			</div>
		</div>
	</div>
</div>