<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('SMIS Users Manuals'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>
				<div style="overflow-x:auto;">
					<?= (isset($this->data['page']) ? $this->Form->hidden('page', array('value' => $this->data['page'])) : ''); ?>
					<?= (isset($this->data['sort']) ? $this->Form->hidden('sort', array('value' => $this->data['sort'])) : ''); ?>
					<?= (isset($this->data['direction']) ? $this->Form->hidden('direction', array('value' => $this->data['direction'])) : ''); ?>

					<table cellpadding="0" cellspacing="0" class="table">
						<thead>
							<tr>
								<th style="width:3%" class="center">#</th>
								<th style="width:38%" class="vcenter"><?= $this->Paginator->sort('title', 'Title of the Manual'); ?></th>
								<th class="center"><?= $this->Paginator->sort('document_release_date', 'Manual Release Date'); ?></th>
								<th class="center"><?= $this->Paginator->sort('version', 'Version'); ?></th>
								<th class="center">Manual</th>
								<?php
								if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) { ?>
									<th class="center">Active</th>
									<th class="center">Actions</th>
									<?php
								} ?>
							</tr>
						</thead>
						<tbody>
							<?php

							$start = $this->Paginator->counter('%start%');

							foreach ($helps as $help) { ?>
								<tr>
									<td class="center"><?= $start++; ?></td>
									<td class="vcenter"><?= $help['Help']['title']; ?></td>
									<td class="center"><?= $this->Time->format("M j, Y", $help['Help']['document_release_date'], NULL, NULL); ?></td>
									<td class="center"><?= $help['Help']['version']; ?></td>
									<td  class="center">
										<?php
										$missing_attachment = 0;

										if (!empty($help['Attachment'])) {
											foreach ($help['Attachment'] as $cuk => $cuv) {
												if (!empty($cuv['dirname']) && !empty($cuv['basename'])) {
													if ($this->Media->file($cuv['dirname'] . DS . $cuv['basename'])) {
														echo '<a href=' . $this->Media->url($cuv['dirname'] . DS . $cuv['basename'], true) . ' target=_blank>View Manual</a>';
														break;
													} else {
														$missing_attachment = 1;
													}
												}
												//break;
											}
										} else { 
											$missing_attachment = 1;
										} 
										
										if ($missing_attachment) {
											echo '<span class="rejected">Attachment not found</span>';
										} ?>
									</td>
									<?php
									if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) { ?>
										<td class="center"><?= $help['Help']['active'] == 1 ? '<span class="accepted">Yes</span>': '<span class="rejected">No</span>'; ?></td>
										<td class="center">
											<?= $this->Html->link(__('Edit'), array('action' => 'edit', $help['Help']['id']));  ?> &nbsp; &nbsp;
											<?= $this->Form->postLink(__('Delete'), array('action' => 'delete', $help['Help']['id']), array(), __('Are you sure you want to delete user manual %s (version: %s)?', $help['Help']['title'], $help['Help']['version'])); ?>
										</td>
										<?php
									} ?>
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
		</div>
	</div>
</div>