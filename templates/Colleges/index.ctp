<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Colleges'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;">
					<hr>
					<?php
					if (isset($colleges) && !empty($colleges)) { ?>
						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<td class="center">#</td>
										<td class="vcenter"><?= $this->Paginator->sort('name'); ?></th>
										<td class="center"><?= $this->Paginator->sort('shortname', 'Short'); ?></td>
										<td class="center"><?= $this->Paginator->sort('type'); ?></td>
										<td class="center"><?= $this->Paginator->sort('institution_code'); ?></td>
										<td class="center"><?= $this->Paginator->sort('active'); ?></td>
										<td class="center"><?= $this->Paginator->sort('campus_id'); ?></td>
										<td class="center"><?= __('Actions'); ?></td>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = $this->Paginator->counter('%start%');
									foreach ($colleges as $college) { ?>
										<tr>
											<td class="center"><?= $count++; ?></td>
											<td class="vcenter"><?= $college['College']['name']; ?></td>
											<td class="center"><?= $college['College']['shortname']; ?></td>
											<td class="center"><?= $college['College']['type']; ?></td>
											<td class="center"><?= (isset($college['College']['institution_code']) ? $college['College']['institution_code'] : ''); ?></td>
											<td class="center"><?= (isset($college['College']['active']) && $college['College']['active'] == 1 ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>'); ?></td>
											<td class="center">
												<?= $this->Html->link($college['Campus']['name'], array('controller' => 'campuses', 'action' => 'view', $college['Campus']['id'])); ?>
											</td>
											<td class="center">
												<?= $this->Html->link(__(''), array('action' => 'view', $college['College']['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?> &nbsp;
												<?php
												if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN || (($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE || $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) && $this->Session->read('Auth.User')['is_admin'] == 1)) { ?>
													<?= $this->Html->link(__(''), array('action' => 'edit', $college['College']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?> &nbsp;
													<?php
												}
												if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) { ?>
													<?= $this->Html->link(__(''), array('action' => 'delete', $college['College']['id']), array('class' => 'fontello-trash', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete %s college?'), $college['College']['name'])); ?>
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
						<?php
					} else { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Unable to load colleges data. Please Make sure that you have the privilage to view/list colleges.</div>
						<hr>
						<?php
					} ?>
				</div>
			</div>
		</div>
	</div>
</div>