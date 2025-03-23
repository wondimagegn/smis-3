<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Year Levels'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>
				<div style="overflow-x:auto;">
					<table cellpadding="0" cellspacing="0" class="table">
						<thead>
							<tr>
								<td class="center">#</td>
								<td class="center"><?= $this->Paginator->sort('name', 'Year'); ?></td>
								<td class="vcenter"><?= $this->Paginator->sort('department_id'); ?></td>
								<td class="center"><?= $this->Paginator->sort('created'); ?></td>
								<td class="center"><?= $this->Paginator->sort('modified'); ?></td>
								<td class="center"><?= __('Actions'); ?></td>
							</tr>
						</thead>
						<tbody>
							<?php
							$start = $this->Paginator->counter('%start%');
							//debug($yearLevels[0]);
							foreach ($yearLevels as $yearLevel) { ?>
								<tr>
									<td class="center"><?= $start++ ?>&nbsp;</td>
									<td class="center"><?= $yearLevel['YearLevel']['name']; ?>&nbsp;</td>
									<td class="vcenter"><?= $this->Html->link($yearLevel['Department']['name'], array('controller' => 'departments', 'action' => 'view', $yearLevel['Department']['id'])); ?></td>
									<td class="center"><?= $this->Time->format("M j, Y h:i:s A", $yearLevel['YearLevel']['created'], NULL, NULL); ?></td>
									<td class="center"><?= $this->Time->format("M j, Y h:i:s A", $yearLevel['YearLevel']['modified'], NULL, NULL); ?></td>
									<td class="center">
										<?= $this->Html->link(__(''), array('action' => 'view', $yearLevel['YearLevel']['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?> &nbsp;
										<?php //echo $this->Html->link(__(''), array('action' => 'edit', $yearLevel['YearLevel']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?> &nbsp;
										<?= $this->Html->link(__(''), array('action' => 'delete', $yearLevel['YearLevel']['id']), array('class' => 'fontello-trash', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete %s year level from %s department?'), $yearLevel['YearLevel']['name'], $yearLevel['Department']['name'])); ?>
									</td>
								</tr>
							<?php
							} ?>
						</tbody>
					</table>
				</div>
				<hr>
				<p><?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total'))); ?> </p>

				<!-- <div class="paging">
					<?php //echo $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class' => 'disabled')); ?> | <?= $this->Paginator->numbers(); ?> | <?= $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled')); ?>
				</div> -->

			</div>
		</div>
	</div>
</div>