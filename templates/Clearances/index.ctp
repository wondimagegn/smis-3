<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('List of Clearances/Withdrawal Requests');?></span>
		</div>
	</div>
    <div class="box-body">
    	<div class="row">
	  		<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>
				<?=  $this->Form->create('Clearance'); ?>
				<?php
				if ($role_id != ROLE_STUDENT && $role_id == ROLE_REGISTRAR) { ?>
					<fieldset>
                        <legend>&nbsp;&nbsp; Search / Filter Applications &nbsp;&nbsp;</legend>
                        <div class="row">
                            <div class="large-4 columns">
							<?= $this->Form->input('Search.academic_year', array('id' => 'AcadamicYear', 'label' => 'Acadamic Year: ', 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear), 'style' => 'width:90%')); ?>
                            </div>
                            <div class="large-4 columns">
							<?= $this->Form->input('Search.program_id', array('class' => 'fs14',  'style' => 'width:125px', 'label' => 'Program: ', 'options' => $programs, 'style' => 'width:90%')); ?>
                            </div>
                            <div class="large-4 columns">	
								<?= $this->Form->input('Search.program_type_id', array('label' => 'Program Type: ', 'class' => 'fs14', 'options' => $programTypes, 'style' => 'width:80%')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="large-6 columns">
								<?php 
								if (!empty($departments)) { ?>
									<?= $this->Form->input('Search.department_id', array('label' => 'Department: ', 'class' => 'fs14', 'options' => $departments, 'style' => 'width:80%')); ?>
									<?php 
								} else if (!empty($colleges)) { ?>
									<?= $this->Form->input('Search.college_id', array('label' => 'College:', 'class' => 'fs14', 'options' => $colleges, 'style' => 'width:90%')); ?>
									<?php 
								} ?>
                            </div>
                            <div class="large-6 columns">
								<strong> Type: </strong> <br><br/>
								<?= $this->Form->input('Search.clear', array('type' => 'checkbox', 'label' => 'Clearance', 'div' => false,  (((isset($this->data['Search']['clear']) && $this->data['Search']['clear'] == 1) ||  (isset($this->request->data['Search']['clear'] ) && $this->request->data['Search']['clear'] == 'on')) ? 'checked' : ''))); ?><br/>
								<?=  $this->Form->input('Search.withdrawl', array('type' => 'checkbox', 'label' => 'Withdrawal', 'div' => false, (((isset($this->data['Search']['withdrawl']) && $this->data['Search']['withdrawl'] == 1) ||  (isset($this->request->data['Search']['withdrawl']) && $this->request->data['Search']['withdrawl'] == 'on')) ? 'checked' : ''))); ?>
                            </div>
                        </div>
						<hr>
						<?= $this->Form->submit(__('Search Applications'), array('name' => 'viewClearance', 'div' => false,'class'=>'tiny radius button bg-blue')); ?>
					</fieldset>
					<?php 
				} ?>

				<div class="clearances index">
					<?php 
					if (isset($clearances) && !empty($clearances)) { ?>

						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<th class="center">#</th>
										<th class="vcenter" style="width: 15%;"><?= $this->Paginator->sort('student_id','Student Name');?></th>
										<th class="center"><?= $this->Paginator->sort('department_id','Department');?></th>
										<th class="center"><?= $this->Paginator->sort('program_id','Program');?></th>
										<th class="center"><?= $this->Paginator->sort('program_type_id','Program Type');?></th>
										<th class="center"><?= $this->Paginator->sort('type','Type');?></th>
										<th class="center"><?= $this->Paginator->sort('reason','Reason');?></th>
										<th class="center"><?= $this->Paginator->sort('request_date','Request Date');?></th>
										<th class="center"><?= $this->Paginator->sort('confirmed', 'Clearance Staus');?></th>
										<th class="center"><?= $this->Paginator->sort('forced_withdrawal', 'Withdrawal status');?></th>
										<?php
										if ($role_id == ROLE_STUDENT) { ?>
											<th class="actions"></th>
											<?php 
										} ?>
									</tr>
								</thead>
								<tbody>
									<?php
									$start = $this->Paginator->counter('%start%');
									foreach ($clearances as $clearance) { ?>
										<tr>
											<td class="center"><?= $start++; ?></td>
											<td class="vcenter"><?= $this->Html->link($clearance['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $clearance['Student']['id'])); ?></td>
											<td class="center"><?= (empty($clearance['Student']['Department']['name']) ? 'Pre/Fresh ' : $clearance['Student']['Department']['name']); ?></td>
											<td class="center"><?= $clearance['Student']['Program']['name']; ?></td>
											<td class="center"><?= $clearance['Student']['ProgramType']['name']; ?></td>
											<td class="center"><?= (!empty($clearance['Clearance']['type']) ? $clearance['Clearance']['type'] : 'N/A'); ?></td>
											<td class="center"><?= (!empty($clearance['Clearance']['reason']) ? $clearance['Clearance']['reason'] : '---'); ?></td>
											<td class="center"><?= $this->Time->format("M j, Y", $clearance['Clearance']['request_date'], NULL, NULL); ?></td>
											<td class="center"><?= ($clearance['Clearance']['confirmed'] == 1 ? '<span class="accepted">Accepted</span>' : ($clearance['Clearance']['confirmed'] == -1 ? '<span class="rejected">Rejected</span>' : '<span class="text-gray">Waiting Decision</span>')); ?></td>
											<td class="center"><?= (strcasecmp($clearance['Clearance']['type'],'withdraw') === 0 ? ($clearance['Clearance']['forced_withdrawal'] == 1 ? '<span class="accepted">Accepted</span>' : ($clearance['Clearance']['forced_withdrawal'] == -1 ? '<span class="rejected">Rejected</span>' : '<span class="text-gray">Waiting Decision</span>')) :  'N/A'); ?></td>
											<?php 
											if ($role_id == ROLE_STUDENT) { ?>
												<td class="center"><?= ((!isset($clearance['Clearance']['forced_withdrawal']) || (isset($clearance['Clearance']['forced_withdrawal']) && $clearance['Clearance']['forced_withdrawal'] != 1)) ? $this->Html->link(__('Cancel Request'), array('action' => 'delete', $clearance['Clearance']['id']), null, sprintf(__('Are you sure you want to cancel this clearance you submitted on at %s?'), $clearance['Clearance']['created'])) : ''); ?></td>   
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
					} else { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No Clearance/Withdrawal requests are found with the given search criteria.</div>
						<?php
					} ?>
				</div>
				<?=  $this->Form->end(); ?>
	  		</div>
		</div>
    </div>
</div>
