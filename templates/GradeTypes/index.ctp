<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Grade Types'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<table cellpadding="0" cellspacing="0" class="responsive table-borderless fs13">
					<thead>
						<tr>
							<td>#</td>
							<td><?= $this->Paginator->sort('type', 'Grade Type'); ?></td>
							<td style="text-align: center;"><?= $this->Paginator->sort('used_in_gpa', 'Used in GPA'); ?></td>
							<td style="text-align: center;"><?= $this->Paginator->sort('active'); ?></td>
							<td><?= $this->Paginator->sort('created', 'Date Created'); ?></td>
							<td><?= $this->Paginator->sort('modified', 'Date Modified'); ?></td>
							<td style="text-align:center" class="actions"><?= __('Actions'); ?></td>
						</tr></thead>
					</thead>
					<tbody>
						<?php
						$count = 1;
						foreach ($gradeTypes as $gradeType) { ?>
							<tr>
								<td><?= $count++; ?></td>
								<td><?= $gradeType['GradeType']['type']; ?></td>
								<td style="text-align: center;"><?= (($gradeType['GradeType']['used_in_gpa'] == 1) ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>'); ?> </td>
								<td style="text-align: center;"><?= (($gradeType['GradeType']['active'] == 1) ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>'); ?> </td>
								<td><?= $this->Time->format("M j, Y g:i A", $gradeType['GradeType']['created'], NULL, NULL); ?></td>
								<td><?= $this->Time->format("M j, Y g:i A", $gradeType['GradeType']['modified'], NULL, NULL); ?></td>
								<td style="text-align: center;">
									<?= $this->Html->link(__(''), array('action' => 'view', $gradeType['GradeType']['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?>&nbsp;
									<?= $this->Html->link(__(''), array('action' => 'edit', $gradeType['GradeType']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?>&nbsp;
									<?= $this->Html->link(__(''), array('action' => 'delete', $gradeType['GradeType']['id']), array('class' => 'fontello-trash', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete %s?'), $gradeType['GradeType']['type'])); ?>
								</td>
							</tr>
							<?php 
						} ?>
					</tbody>
				</table>

				<p><?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'))); ?></p>

				<div class="paging">
					<?= $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class' => 'disabled')); ?> | <?= $this->Paginator->numbers(); ?> | <?= $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled')); ?>
				</div>
			</div>
		</div>
	</div>
</div>