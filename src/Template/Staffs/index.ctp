<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Staff List'); ?></span>
        </div>
    </div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('Staff', array('action' => 'search')); ?>
				<div style="margin-top: -30px;">
					<hr>
					<fieldset style="padding-bottom: 5px; padding-top: 15px;">
						<div class="row">
							<div class="large-6 columns">
								<?= $this->Form->input('search', array('label' => 'Search Filter: ', 'style' => 'width:90%;', 'placeholder' => 'Find by name, email, phone...', 'maxLength' => 50, 'default' => (isset($this->request->data['Staff']['search']) ? $this->request->data['Staff']['search'] : ''))); ?>
							</div>
							<?php
							if (isset($departments) && !empty($departments)) { ?>
								<div class="large-6 columns">
									<?php
									if ($role_id == ROLE_DEPARTMENT) { ?>
										<?= $this->Form->input('department_id', array('label' => 'Department: ',  'style' => 'width:90%;', 'options' => $departments)); ?>
										<?php
									} else { ?>
										<?= $this->Form->input('department_id', array('label' => 'Department: ',  'style' => 'width:90%;', 'empty' => '[ All Departments ]',  'options' => $departments)); ?>
										<?php
									} ?>
								</div>
								<?php
							}  else { ?>
								<div class="large-6 columns">
									&nbsp;
								</div>
								<?php
							} ?>
						</div>
						<div class="row">
							<div class="large-3 columns">
								<?= $this->Form->input('status', array('label' => 'Staff Status: ', 'style' => 'width:80%;', 'type' => 'select', /* 'empty' => '[ All Statuses ]', */ 'options' => array('1' => 'Active', '0' => 'Deactivated',), 'default' => '1')); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('haveuser', array('label' => 'User Fiters: ', 'style' => 'width:80%;', 'type' => 'select', /* 'empty' => '[ With/without Account ]', */  'options' => array('1' => 'Have Account', '0' => 'Doesn\'t Have Account'), 'default' => '1')); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('limit', array('id' => 'limit ', 'type' => 'number', 'min' => '100',  'max' => '1000', 'value' => $limit, 'step' => '100', 'label' => 'Limit: ', 'style' => 'width:40%;')); ?>
							</div>
							<div class="large-3 columns">
								&nbsp;
							</div>
						</div>
						<hr>
						<?= $this->Form->hidden('page', array('value' => $page)); ?>
						<?= $this->Form->submit(__('Search'), array('name' => 'viewStaff', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
						<?= $this->Form->end(); ?>
					</fieldset>
				</div>

				<?php
				//ClassRegistry::init('Staff')->deleteDoubleStaff(34);

				if (isset($staffs) && !empty($staffs)) { ?>
					<div class="staffs index">
						<hr>
						<div style="overflow-x:auto;">
							<table id="studentTableIndex" cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<td class="center">#</td>
										<td class="vcenter"><?= $this->Paginator->sort('position', 'Position'); ?></td>
										<td class="vcenter"><?= $this->Paginator->sort('full_name', 'Full name'); ?></td>
										<td class="center"><?= $this->Paginator->sort('gender', 'Sex'); ?></td>
										<td class="center"><?= $this->Paginator->sort('college_id', 'College'); ?></td>
										<td class="center"><?= $this->Paginator->sort('department_id', 'Department'); ?></td>
										<td class="center"><?= $this->Paginator->sort('active', 'Active'); ?></td>
										<td class="center"><?= $this->Paginator->sort('created', 'Created'); ?></td>
										<td class="center">&nbsp;</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$start = $this->Paginator->counter('%start%');
									foreach ($staffs as $staff) { ?>
										<tr>
											<td class="center"><?= $start++; ?></td>
											<td class="vcenter"><?= (isset($staff['Position']['position']) ? $staff['Position']['position'] : ''); ?></td>
											<td class="vcenter"><?= $staff['Staff']['full_name']; ?></td>
											<td class="center"><?= (strcasecmp(trim($staff['Staff']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($staff['Staff']['gender']), 'female') == 0 ? 'F' : $staff['Staff']['gender'])); ?></td>
											<td class="center"><?= (isset($staff['College']['shortname']) ? $staff['College']['shortname'] : (isset($staff['College']['name']) ? $staff['College']['name'] : '')); ?></td>
											<td class="center"><?= (isset($staff['Department']['name']) ? $staff['Department']['name'] : ''); ?></td>
											<td class="center"><?= ($staff['Staff']['active'] == 1 ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>'); ?></td>
											<td class="center"><?= /* $this->Time->niceShort($staff['Staff']['created']); */ $this->Time->format("M j, Y g:i A", $staff['Staff']['created'], NULL, NULL); ?></td>
											<td class="center">
												<?= $this->Html->link(__(''), array('action' => 'staff_profile', $staff['Staff']['id']), array('class' => 'fontello-eye', 'title' => 'View Staff Profile')); ?> &nbsp;
												<?php 
												if ($role_id == ROLE_SYSADMIN) { ?>
													<?= $this->Html->link(__(''), array('action' => 'edit', $staff['Staff']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?> &nbsp;
													<?php //echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $staff['Staff']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $staff['Staff']['full_name']))); ?>
													<?= $this->Html->link(__(''), array('action' => 'delete', $staff['Staff']['id']), array('class' => 'fontello-trash', 'title' => 'Delete Staff'), sprintf(__('Are you sure you want to delete %s (%s) from list of Staffs?'), $staff['Staff']['full_name'], $staff['Position']['position'] )); ?>
													<?php 
												} elseif ($role_id == ROLE_COLLEGE || $role_id == ROLE_DEPARTMENT) { ?>
													<?php //echo $this->Html->link(__('View'), array('action' => 'staff_profile', $staff['Staff']['id'])); ?>
													<?php 
												} else { ?>
													<?php // echo $this->Html->link(__('Update Profile'), array('action' => 'update_staff_profile', $staff['Staff']['id']));  ?>
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
					</div>
					<?php
				} ?>
			</div>
		</div>
	</div>
</div>
