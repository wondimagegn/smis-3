<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Cities'); ?></span>
		</div>
	</div>
    <div class="box-body">
    	<div class="row">
	  		<div class="large-12 columns">
             
				<div class="cities index" style="margin-top: -30px;">
					<hr>
					<?php
					if (isset($cities) && !empty($cities)) { ?>
						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
								<tr>
									<th style="width: 5%;" class="center">#</th>
									<th class="vcenter"><?= $this->Paginator->sort('name');?></th>
									<th class="center"><?= $this->Paginator->sort('region_id');?></th>
									<th class="center"><?= $this->Paginator->sort('zone_id');?></th>
									<th class="center"><?= $this->Paginator->sort('created');?></th>
									<th class="center"><?= $this->Paginator->sort('modified');?></th>
									<th class="center"></th>
								</tr>
								</thead>
								<tbody>
									<?php
									$start = $this->Paginator->counter('%start%');
									foreach ($cities as $city) { ?>
										<tr>
											<td class="center"><?= $start++; ?>&nbsp;</td>
											<td class="vcenter"><?= $city['City']['name']; ?>&nbsp;</td>
											<td class="center"><?= (isset($city['Region']['name']) ? $city['Region']['name'] : '---'); ?></td>
											<td class="center"><?= (isset($city['Zone']['name']) ? $city['Zone']['name'] : '---'); ?></td>
											<td class="center"><?= $this->Time->format("M j, Y", $city['City']['created'], NULL, NULL); ?></td>
											<td class="center"><?= $this->Time->format("M j, Y", $city['City']['modified'], NULL, NULL); ?></td>
											<td class="center">
												<?php //echo $this->Html->link(__(''), array('action' => 'view', $city['City']['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?><!--  &nbsp; -->
												<?php
												if (($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN || $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) && $this->Session->read('Auth.User')['is_admin'] == 1) { ?>
													<?= $this->Html->link(__(''), array('action' => 'edit', $city['City']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?> &nbsp;
													<?php
												}
												if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) { ?>
													<?= $this->Html->link(__(''), array('action' => 'delete', $city['City']['id']), array('class' => 'fontello-trash', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete %s city?'), $city['City']['name'])); ?>
													<?php
												} ?>
											</td>
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
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Unable to load cities data. Please Make sure that you have the privilage to view/list cities.</div>
						<hr>
						<?php
					} ?>
				
				</div>
	  		</div>
		</div>
    </div>
</div>