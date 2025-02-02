<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Zones'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;">
					<hr>
					<?php
					if (isset($zones) && !empty($zones)) { ?>
						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<th class="center">#</th>
										<th class="vcenter"><?= $this->Paginator->sort('name', 'Friendy Zone Name'); ?></th>
										<th class="vcenter"><?= $this->Paginator->sort('zone', 'Standard Zone Name'); ?></th>
										<th class="center"><?= $this->Paginator->sort('short', 'Short Name'); ?></th>
										<th class="center"><?= $this->Paginator->sort('region_id'); ?></th>
										<th class="center"><?= $this->Paginator->sort('active'); ?></th>
										<th class="center">&nbsp;</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = $this->Paginator->counter('%start%');
									foreach ($zones as $zone) { ?>
										<tr>
											<td class="center"><?= $count++; ?></td>
											<td class="vcenter"><?= $zone['Zone']['name']; ?></td>
											<td class="vcenter"><?= (!empty($zone['Zone']['zone']) ? $zone['Zone']['zone'] : ''); ?></td>
											<td class="center"><?= $zone['Zone']['short']; ?></td>
											<td class="center"><?= /* $this->Html->link( */$zone['Region']['name']/* , array('controller' => 'regions', 'action' => 'view', $zone['Region']['id'])) */; ?></td>
											<td style="text-align: center;"><?= (isset($zone['Zone']['active']) && $zone['Zone']['active'] == 1 ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>'); ?></td>
											<td class="center">
												<?php //echo $this->Html->link(__(''), array('action' => 'view', $zone['Zone']['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?><!--  &nbsp; -->
												<?php
												if (($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN || $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) && $this->Session->read('Auth.User')['is_admin'] == 1) { ?>
													<?= $this->Html->link(__(''), array('action' => 'edit', $zone['Zone']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?> &nbsp;
													<?php
												}
												if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) { ?>
													<?= $this->Html->link(__(''), array('action' => 'delete', $zone['Zone']['id']), array('class' => 'fontello-trash', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete %s zone?'), $zone['Zone']['name'])); ?>
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
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Unable to load zones data. Please Make sure that you have the privilage to view/list regions.</div>
						<hr>
						<?php
					} ?>
				</div>
			</div>
		</div>
	</div>
</div>