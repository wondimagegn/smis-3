<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Grade Scales'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<table cellpadding="0" cellspacing="0" class="responsive table-borderless fs13">
					<thead>
						<tr>
							<td>#</td>
							<td><?= $this->Paginator->sort('name'); ?></td>
							<td><?= $this->Paginator->sort('grade_type_id'); ?></td>
							<td><?= $this->Paginator->sort('program_id'); ?></td>
							<td style="text-align: center;"><?= $this->Paginator->sort('active'); ?></td>
							<td><?= $this->Paginator->sort('created','Date Created'); ?></td>
							<td><?= $this->Paginator->sort('modified', 'Date Modified'); ?></td>
							<td style="text-align:center"><?= __('Actions'); ?></td>
						</tr>
					</thead>
					<tbody>
						<?php
						$start = $this->Paginator->counter('%start%');
						foreach ($gradeScales as $gradeScale) { ?>
							<tr>
								<td><?= $start++; ?></td>
								<td><?= $gradeScale['GradeScale']['name']; ?></td>
								<td>
									<?= $this->Html->link($gradeScale['GradeScaleDetail'][0]['Grade']['GradeType']['type'], array('controller' => 'grade_types', 'action' => 'view', $gradeScale['GradeScaleDetail'][0]['Grade']['GradeType']['id'])); ?>
								</td>
								<td>
									<?= $this->Html->link($gradeScale['Program']['name'], array('controller' => 'programs', 'action' => 'view', $gradeScale['Program']['id'])); ?>
								</td>
								<td style="text-align: center;"><?= (($gradeScale['GradeScale']['active'] == 1) ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>'); ?> </td>
								<td><?= $this->Time->format("M j, Y g:i A", $gradeScale['GradeScale']['created'], NULL, NULL); ?></td>
								<td><?= $this->Time->format("M j, Y g:i A", $gradeScale['GradeScale']['modified'], NULL, NULL); ?></td>
								<td style="text-align:center">
									<?= $this->Html->link(__(''), array('action' => 'view', $gradeScale['GradeScale']['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?> 
									<?php
									if ($this->Session->read('Auth.User')['is_admin'] == 1) { ?>
										&nbsp;
										<?= $this->Html->link(__(''), array('action' => 'edit', $gradeScale['GradeScale']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?> &nbsp;
										<?= $this->Html->link(__(''), array('action' => 'delete', $gradeScale['GradeScale']['id']), array('class' => 'fontello-trash', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete %s?'), $gradeScale['GradeScale']['name'])); ?>
										<?php
									} ?>
								</td>
							</tr>
							<?php
						} ?>
					</tbody>
				</table>

				<p> <?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'))); ?> </p>

				<div class="paging">
					<?= $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class' => 'disabled')); ?> | <?= $this->Paginator->numbers(); ?> | <?= $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled')); ?>
				</div>

			</div>
		</div>
	</div>
</div>