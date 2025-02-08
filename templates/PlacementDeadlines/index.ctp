<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;">
			<i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('List of Defined Placement Deadlines'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>
				<?php
				if (!empty($placementDeadlines)) { ?>
					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<td class="center">
										<?= $this->Paginator->sort('id', '#'); ?>
									</td>
									<td class="vcenter">
										<?= $this->Paginator->sort('applied_for', 'For Students in'); ?>
									</td>
									<td class="center">
										<?= $this->Paginator->sort('program_id', 'Program'); ?>
									</td>
									<td class="center">
										<?= $this->Paginator->sort('program_type_id', 'Program Type'); ?>
									</td>
									<td class="center">
										<?= $this->Paginator->sort('academic_year', 'ACY'); ?>
									</td>
									<td class="center">
										<?= $this->Paginator->sort('placement_round', 'Round'); ?>
									</td>
									<td class="center">
										<?= $this->Paginator->sort('deadline', 'Deadline'); ?>
									</td>
									<td class="center">
										<?= __('Actions'); ?>
									</td>
								</tr>
							</thead>
							<tbody>
								<?php
								$start = $this->Paginator->counter('%start%');
								foreach ($placementDeadlines as $placementDeadline) { ?>
									<tr>
										<td class="center">
											<?= $start++; ?>
										</td>
										<td class="vcenter">
											<?= h($allUnits[$placementDeadline['PlacementDeadline']['applied_for']]); ?>
										</td>
										<td class="center">
											<?= h($placementDeadline['Program']['name']); ?>
										</td>
										<td class="center">
											<?= h($placementDeadline['ProgramType']['name']); ?>
										</td>
										<td class="center">
											<?= h($placementDeadline['PlacementDeadline']['academic_year']); ?>
										</td>
										<td class="center">
											<?= h($placementDeadline['PlacementDeadline']['placement_round']); ?>
										</td>
										<td class="center">
											<?= $this->Time->format("M j, Y h:i:s A", $placementDeadline['PlacementDeadline']['deadline'], NULL, NULL); ?>
										</td>
										<td class="center">
											<?php //echo $this->Html->link(__(''), array('action' => 'view', $placementDeadline['PlacementDeadline']['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?> &nbsp;
											<?= $this->Html->link(__(''), array('action' => 'edit', $placementDeadline['PlacementDeadline']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?> &nbsp;
											<?= $this->Html->link(__(''), array('action' => 'delete', $placementDeadline['PlacementDeadline']['id']), array('class' => 'fontello-trash', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete the deadline for %s for round %s of %s academic year?'), $allUnits[$placementDeadline['PlacementDeadline']['applied_for']], $placementDeadline['PlacementDeadline']['placement_round'], $placementDeadline['PlacementDeadline']['academic_year'])); ?>
										</td>
									</tr>
									<?php
								} ?>
							</tbody>
						</table>
					</div>
					<br>

					<p><?= $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total'))); ?></p>

					<div class="paging">
						<?php echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
						echo $this->Paginator->numbers(array('separator' => ''));
						echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
						?>
					</div>
					<?php
				} else { ?>
					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No Placement Deadline definition is found in the system.</div>
					<?php
				} ?>
			</div>
		</div>
	</div>
</div>