<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= ($role_id != ROLE_STUDENT ? __('List Department Transfer Requests') : __('Your Department Transfer Requests')); ?></span>
		</div>
	</div>
    <div class="box-body">
       	<div class="row">
	  		<div class="large-12 columns">
			  	<div style="margin-top: -30px;"><hr></div>
            
				<?= $this->Form->create('DepartmentTransfer', array('action' => 'search')); ?>

				<?php

				$defaltDaysAgoForRequests = (new DateTime())->modify('-'.DEFAULT_DAYS_FOR_DEPARTMENT_TRANSFER_REQUEST_CHECK.' days')->format('Y-m-d');

				if ($role_id != ROLE_STUDENT) { ?>
					<?php
                    $yFrom = date('Y') -ACY_BACK_FOR_DEPARTMENT_TRANSFER_DROP_DOWN;
                    $yTo = date('Y'); ?>

                    <fieldset style="padding-bottom: 0px;padding-top: 15px;">
                        <!-- <legend>&nbsp;&nbsp; Search &nbsp;&nbsp;</legend> -->
                        <div class="row">
                            <div class="large-6 columns">
                                <?= $this->Form->input('Search.department_id', array('id' => 'Department', 'class' => 'fs13', 'label' => 'Requests to Department: ', 'type' => 'select',  'style' => 'width:90%;')); ?>
                            </div>
							<div class="large-3 columns">
								<?= $this->Form->input('Search.status', array('label' => 'Status: ',  'style' => 'width:90%;', 'options' => Configure::read('status_types_for_seach_approvals'))); ?>
							</div>
							<div class="large-3 columns">
							<?= $this->Form->input('Search.limit', array('id' => 'limit ', 'type' => 'number', 'min' => '100',  'max' => '50000', 'value' => (!empty($selectedLimit) ? $selectedLimit : 100), 'step' => '100', 'class' => 'fs13', 'label' =>'Limit: ', 'style' => 'width:45%')); ?>
							</div>
                        </div>
                        <div class="row">
                            <div class="large-6 columns">
                                <?= $this->Form->input('Search.transfer_request_date_from', array('label' => 'Request Date From: ', 'type' => 'date', 'minYear' => $yFrom, 'maxYear' => $yTo, 'default' => false, 'style' => 'width:25%')); ?>
                            </div>
                            <div class="large-6 columns">

                                <?= $this->Form->input('Search.transfer_request_date_to', array('label' => 'Request Date to: ', 'type' => 'date', 'minYear' => $yFrom, 'maxYear' => $yTo, 'default' =>  date('Y-m-d'), 'style' => 'width:25%')); ?>
								
								<?= (isset($this->data['Search']['page']) ? $this->Form->hidden('page', array('value' => $this->data['Search']['page'])) : ''); ?>
								<?= (isset($this->data['Search']['sort']) ? $this->Form->hidden('sort', array('value' => $this->data['Search']['sort'])) : ''); ?>
								<?= (isset($this->data['Search']['direction']) ? $this->Form->hidden('direction', array('value' => $this->data['Search']['direction'])) : ''); ?>
                            </div>
                        </div>
                        <hr>
						<?= $this->Form->submit(__('View Department Requests'), array('name' => 'viewTransferApplication', 'class' => 'tiny radius button bg-blue')); ?>
                    </fieldset>
                    <hr>
					<?php 
				}
				
    			if (!empty($departmentTransfers)) { ?>
					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<th class="center" style="width: 3%;">#</th>
									<th class="vcenter" style="width: 15%;"><?= $this->Paginator->sort('student_id', 'Student Name');?></th>
									<th class="center" style="width: 10%;">From</th>
									<th class="center" style="width: 10%;"><?= $this->Paginator->sort('department_id', 'To');?></th>
									<th class="center"><?= $this->Paginator->sort('transfer_request_date', 'Request Date');?></th>
									<th class="center"><?= $this->Paginator->sort('sender_department_approval', 'Sending Department Approval');?></th>
									<th class="center"><?= $this->Paginator->sort('sender_college_approval', 'Sending College Approval');?></th>
									<th class="center"><?= $this->Paginator->sort('receiver_college_approval', 'Destination College Approval');?></th>
									<th class="center"><?= $this->Paginator->sort('receiver_department_approval', 'Destination Department Approval');?></th>
									<?php 
									if ($role_id == ROLE_STUDENT) { ?>
										<th class="center"><?= __('Actions');?></th>
										<?php 
									} ?>
								</tr>
							</thead>
							<tbody>
								<?php
								$count = $this->Paginator->counter('%start%');
								foreach ($departmentTransfers as $departmentTransfer) { ?> 
									<tr>
										<td class="center"><?= $count++; ?></td>
										<td class="vcenter"><?= $this->Html->link($departmentTransfer['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $departmentTransfer['Student']['id'])); ?></td>
										<td class="center"><?= (isset($departmentsss[$departmentTransfer['DepartmentTransfer']['from_department_id']]) ? $departmentsss[$departmentTransfer['DepartmentTransfer']['from_department_id']] : ''); ?></td>
										<td class="center"><?= (isset($departmentTransfer['Department']['name']) ? $departmentTransfer['Department']['name'] : ''); ?></td>
										<td class="center"><?= $this->Time->format("M j, Y", $departmentTransfer['DepartmentTransfer']['transfer_request_date'], NULL, NULL); ?></td>
										<td class="center">
											<?= ($departmentTransfer['DepartmentTransfer']['transfer_request_date'] < $defaltDaysAgoForRequests ? '<span class="text-red">Request Expired</span>' : ($departmentTransfer['DepartmentTransfer']['sender_department_approval'] == 1 ? '<span class="accepted">Accepted</span>' : ($departmentTransfer['DepartmentTransfer']['sender_department_approval'] == -1 ? '<span class="rejected">Rejected</span>' : '<span class="text-gray">Waiting Decision</span>'))); ?>
											<?= (($departmentTransfer['DepartmentTransfer']['sender_department_approval_date'] == '0000-00-00 00:00:00' || $departmentTransfer['DepartmentTransfer']['sender_department_approval_date'] == '' || is_null($departmentTransfer['DepartmentTransfer']['sender_department_approval_date'])) ? '' : '<br>'. ($this->Time->timeAgoInWords($departmentTransfer['DepartmentTransfer']['sender_department_approval_date'], array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month'))))); ?>
										</td>
										<td class="center">
											<?= ($departmentTransfer['DepartmentTransfer']['transfer_request_date'] < $defaltDaysAgoForRequests ? '---' : ($departmentTransfer['DepartmentTransfer']['sender_college_approval'] == 1 ? '<span class="accepted">Accepted</span>' : ($departmentTransfer['DepartmentTransfer']['sender_college_approval'] == -1 ? '<span class="rejected">Rejected</span>' : '<span class="text-gray">Waiting Decision</span>'))); ?>
											<?= (($departmentTransfer['DepartmentTransfer']['sender_college_approval_date'] == '0000-00-00 00:00:00' || $departmentTransfer['DepartmentTransfer']['sender_college_approval_date'] == '' || is_null($departmentTransfer['DepartmentTransfer']['sender_college_approval_date'])) ? '' : '<br>'. ($this->Time->timeAgoInWords($departmentTransfer['DepartmentTransfer']['sender_college_approval_date'], array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month'))))); ?>
										</td>
										<td class="center">
											<?= ($departmentTransfer['DepartmentTransfer']['transfer_request_date'] < $defaltDaysAgoForRequests ? '---' : ($departmentTransfer['DepartmentTransfer']['receiver_college_approval'] == 1 ? '<span class="accepted">Accepted</span>' : ($departmentTransfer['DepartmentTransfer']['receiver_college_approval'] == -1 ? '<span class="rejected">Rejected</span>' : '<span class="text-gray">Waiting Decision</span>'))); ?>
											<?= (($departmentTransfer['DepartmentTransfer']['receiver_college_approval_date'] == '0000-00-00 00:00:00' || $departmentTransfer['DepartmentTransfer']['receiver_college_approval_date'] == '' || is_null($departmentTransfer['DepartmentTransfer']['receiver_college_approval_date'])) ? '' : '<br>'. ($this->Time->timeAgoInWords($departmentTransfer['DepartmentTransfer']['receiver_college_approval_date'], array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month'))))); ?>
										</td>
										<td class="center">
											<?= ($departmentTransfer['DepartmentTransfer']['transfer_request_date'] < $defaltDaysAgoForRequests ? '---' : ($departmentTransfer['DepartmentTransfer']['receiver_department_approval'] == 1 ? '<span class="accepted">Accepted</span>' : ($departmentTransfer['DepartmentTransfer']['receiver_department_approval'] == -1 ? '<span class="rejected">Rejected</span>' : '<span class="text-gray">Waiting Decision</span>'))); ?>
											<?= (($departmentTransfer['DepartmentTransfer']['receiver_department_approval_date'] == '0000-00-00 00:00:00' || $departmentTransfer['DepartmentTransfer']['receiver_department_approval_date'] == '' || is_null($departmentTransfer['DepartmentTransfer']['receiver_department_approval_date'])) ? '' : '<br>'. ($this->Time->timeAgoInWords($departmentTransfer['DepartmentTransfer']['receiver_department_approval_date'], array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month'))))); ?>
										</td>
										<?php 
										if ($role_id == ROLE_STUDENT) { ?>
											<td class="center"><?= $this->Html->link(__('Cancel Request'), array('action' => 'delete', $departmentTransfer['DepartmentTransfer']['id']), null, sprintf(__('Are you sure you want to delete department transfer request to %s ?', $departmentTransfer['Department']['name']))); ?></td>
											<?php 
										} ?>
									</tr>
									<?php 
								} ?>
							</tbody>
						</table>
					</div>
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
				} 
				if (empty($departmentTransfers) && $role_id == ROLE_STUDENT) { ?>
					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no department transfer request which is submitted by you or onbehalf of you.</div>
					<?php
				} ?>
	  		</div>
		</div>
    </div>
</div>
