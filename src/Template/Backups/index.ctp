<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-database-1"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Download Database Backup'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;">
					<hr>
					<blockquote>
						<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<p style="text-align:justify;">
							<span class="fs16">After download, <u class="text-red">please don't forget to store the backup to external backup device outside of the server room.</u></span>
						</p>
					</blockquote>

					<?= $this->Form->create('Backup'); ?>

					<hr>
					<fieldset style="padding-bottom: 10px;padding-top: 10px;">
						<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
						<div class="row">
							<div class="large-4 columns">
								<?= $this->Form->input('backup_date_from', array('label' => 'Backup Date From: ', 'style' => 'width:30%;', 'type' => 'date', 'dateFormat' => 'MDY', 'minYear' => Configure::read('Calendar.applicationStartYear'), 'maxYear' => date('Y'), 'orderYear' => 'desc', 'selected' => array('year' => (isset($this->request->data['Backup']['backup_date_from']) ? $this->request->data['Backup']['backup_date_from']['year'] : date('Y')), 'month' => (isset($this->request->data['Backup']['backup_date_from']) ? $this->request->data['Backup']['backup_date_from']['month'] : date('m')), 'day' => (isset($this->request->data['Backup']['backup_date_from']) ? $this->request->data['Backup']['backup_date_from']['day'] : (date('d') - 7 > 0 ? date('d') - 7 : 1))))); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('backup_date_to', array('label' => 'Backup Date To: ', 'type' => 'date', 'dateFormat' => 'MDY', 'style' => 'width:30%;', 'minYear' => Configure::read('Calendar.applicationStartYear'), 'maxYear' => date('Y'), 'orderYear' => 'desc')); ?>
							</div>
							<div class="large-4 columns">
								<br>
								<?= $this->Form->submit(__('View Backup'), array('name' => 'viewBackup', 'id' => 'ViewBackupButton', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
							</div>
						</div>
					</fieldset>
					<hr>

					<?= $this->Form->end(); ?>
					
					<?php
					if (!empty($files_for_download)) { ?>
						<div style="overflow-x:auto;">
							<table id="backup" class="display table" style="width:100%" cellpadding="0" cellspacing="0">
								<thead>
									<tr>
										<td class="center">#</td>
										<td class="vcenter"><?= $this->Paginator->sort('Backup File', 'Backup File name'); ?></td>
										<td class="center"><?= $this->Paginator->sort('size'); ?></td>
										<td class="center"><?= $this->Paginator->sort('backup_taken'); ?></td>
										<td class="center"><?= $this->Paginator->sort('first_backup_taken_date'); ?></td>
										<td class="center"><?= $this->Paginator->sort('last_backup_taken_date'); ?></td>
										<td class="center"><?= $this->Paginator->sort('last_backup_taken_date', 'Date Generated'); ?></td>
										<td class="center">Action</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$start = $this->Paginator->counter('%start%');
									foreach ($files_for_download as $file) { ?>
										<tr>
											<td class="center"><?= $start++; ?></td>
											<td class="vcenter"><?= $file['Backup']['name']; ?></td>
											<td class="center"><?= number_format(($file['Backup']['size'] / 1048576), 2, '.', ',') . ' MB'; ?></td>
											<td class="center <?= $file['Backup']['backup_taken'] == 1 ? 'accepted' : 'rejected' ?>"><?= $file['Backup']['backup_taken'] == 1 ? 'Yes' : 'No'; ?></td>
											<td class="center">
												<?php
												if ($file['Backup']['first_backup_taken_date'] == null || $file['Backup']['first_backup_taken_date'] == '0000-00-00') {
													echo '---';
												} else {
													$first_backup_taken_date = date('Y-m-d H:i:s', mktime(
														substr($file['Backup']['first_backup_taken_date'], 11, 2),
														substr($file['Backup']['first_backup_taken_date'], 14, 2),
														substr($file['Backup']['first_backup_taken_date'], 17, 2),
														substr($file['Backup']['first_backup_taken_date'], 5, 2),
														substr($file['Backup']['first_backup_taken_date'], 8, 2),
														substr($file['Backup']['first_backup_taken_date'], 0, 4)
													));
													echo $this->Time->format("M j, Y h:i:s A", $first_backup_taken_date, NULL, NULL);
												} ?>
											</td>
											<td class="center">
												<?php
												if ($file['Backup']['last_backup_taken_date'] == 0 || is_null($file['Backup']['last_backup_taken_date']) || $file['Backup']['last_backup_taken_date'] == '0000-00-00') {
													echo '---';
												} else {
													$last_backup_taken_date = date('Y-m-d H:i:s', mktime(
														substr($file['Backup']['last_backup_taken_date'], 11, 2),
														substr($file['Backup']['last_backup_taken_date'], 14, 2),
														substr($file['Backup']['last_backup_taken_date'], 17, 2),
														substr($file['Backup']['last_backup_taken_date'], 5, 2),
														substr($file['Backup']['last_backup_taken_date'], 8, 2),
														substr($file['Backup']['last_backup_taken_date'], 0, 4)
													));
													echo $this->Time->format("M j, Y h:i:s A", $last_backup_taken_date, NULL, NULL);
												} ?>
											</td>
											<td class="center"><?= $this->Time->format("M j, Y h:i:s A", $file['Backup']['created'], NULL, NULL); ?></td>
											<td class="center"><?= (!$file['Backup']['file_exists'] ? 'Not Available' : $this->Html->link(__('Download'), array('action' => 'index', $file['Backup']['id']))); ?></td>
										</tr>
										<?php
									} ?>
								</tbody>
							</table>
						</div>
						<br>

						<hr>
						<div class="row">
							<div class="large-7 columns">
								<div style="padding-left: 5%;">
									<?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total'))); ?>
								</div>
							</div>
							<div class="large-5 columns right">
								<div class="paging">
									<?= $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class' => 'disabled')); ?> | <?= $this->Paginator->numbers(); ?> | <?= $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled')); ?>
								</div>
							</div>
						</div>
						<hr>
						<?php
					} ?>
				</div>
			</div>
		</div>
	</div>
</div>