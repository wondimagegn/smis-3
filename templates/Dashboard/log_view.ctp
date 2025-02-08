<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('View Logs'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<?= $this->Form->create('Dashboard'); ?>
			<div class="large-12 columns">
				<table cellpadding="0" cellspacing="0" class="table">
					<tr>
						<td style="width:5%"> From:</td>
						<td style="width:45%"><?= $this->Form->input('change_date_from', array('label' => false, 'type' => 'datetime', 'style' => 'width:50px', 'dateFormat' => 'MDY', 'minYear' => Configure::read('Calendar.applicationStartYear'), 'maxYear' => date('Y'), 'orderYear' => 'desc', 'selected' => array('year' => (isset($this->request->data['Log']['change_date_from']) ? $this->request->data['Log']['change_date_from']['year'] : date('Y')), 'month' => (isset($this->request->data['Log']['change_date_from']) ? $this->request->data['Log']['change_date_from']['month'] : date('m')), 'day' => (isset($this->request->data['Log']['change_date_from']) ? $this->request->data['Log']['change_date_from']['day'] : date('d') - 14)))); ?></td>
						<td style="width:5%"> To:</td>
						<td style="width:45%"><?= $this->Form->input('change_date_to', array('label' => false, 'type' => 'datetime', 'style' => 'width:50px', 'dateFormat' => 'MDY', 'minYear' => Configure::read('Calendar.applicationStartYear'), 'maxYear' => date('Y'), 'orderYear' => 'desc')); ?></td>
					</tr>
					<tr>
						<td>Action:</td>
						<td><?= $this->Form->input('action', array('label' => false, 'type' => 'select', 'options' => array('edit' => 'Update', 'add' => 'Created', 'delete' => 'Delete/Cancel'), 'empty' => '--select action--')); ?></td>
						<td>Activty:</td>
						<td><?=  $this->Form->input('model', array('label' => false, 'type' => 'select', 'options' => array('ExamGrade' => 'Exam Grade', 'CourseRegistration' => 'Course Registration', 'Section' => 'Section', 'Curriculum' => 'Curriculum', 'Course' => 'Course', 'Student' => 'Admission Students'), 'empty' => '[ Select Activity ]')); ?></td>
					</tr>
					<tr>
						<td>Key:</td>
						<td><?= $this->Form->input('key', array('maxlength' => 1000, 'label' => false, 'style' => 'width:370px', 'type' => 'text')); ?></td>
						<td>Change:</td>
						<td><?= $this->Form->input('change', array('maxlength' => 1000, 'label' => false, 'style' => 'width:370px', 'type' => 'text')); ?></td>
					</tr>
					<tr>
						<td>Role:</td>
						<td><?= $this->Form->input('role_id', array('label' => false, 'style' => 'width:373px')); ?></td>
						<td>User:</td>
						<td><?= $this->Form->input('username', array('label' => false, 'style' => 'width:370px')); ?></td>
					</tr>
					<tr>
						<td colspan="4"><?= $this->Form->submit(__('View logs'), array('div' => false)); ?></td>
					</tr>
				</table>
			</div>

			<div class="large-12 columns">
				<?php
				if (!empty($logs)) { ?>
					<p class="fs15"><?= __('List of logs based on the above given condition/s'); ?></p>
					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" style="table-layout:fixed">
							<thead>
								<tr>
									<th style="width:3%">#</th>
									<th style="width:8%"><?= $this->Paginator->sort('Key'); ?></th>
									<th style="width:13%"><?= $this->Paginator->sort('user_id'); ?></th>
									<th style="width:8%"><?= $this->Paginator->sort('ip'); ?></th>
									<th style="width:15%"><?= $this->Paginator->sort('model'); ?></th>
									<th style="width:8%"><?= $this->Paginator->sort('action'); ?></th>
									<th style="width:12%"><?= $this->Paginator->sort('description'); ?></th>
									<th style="width:24%"><?= $this->Paginator->sort('change'); ?></th>
									<th style="width:10%"><?= $this->Paginator->sort('created', 'Date'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$start = $this->Paginator->counter('%start%');
								foreach ($logs as $log) { ?>
									<tr>
										<td><?= $start++; ?>&nbsp;</td>
										<td><?= $log['Log']['foreign_key']; ?></td>
										<td><?= (!empty($log['User']['first_name']) ? $this->Html->link($log['User']['first_name'] . ' ' . $log['User']['middle_name'] . ' ' . $log['User']['last_name'] . ' (' . $log['User']['username'] . ')', array('controller' => 'users', 'action' => 'view', $log['User']['id'])) : $this->Html->link($log['User']['username'], array('controller' => 'users', 'action' => 'view', $log['User']['id']))); ?></td>
										<td><?= $log['Log']['ip']; ?>&nbsp;</td>
										<td><?= $log['Log']['model']; ?>&nbsp;</td>
										<td><?= $log['Log']['action']; ?>&nbsp;</td>
										<td><?= $log['Log']['description']; ?>&nbsp;</td>
										<td><?= strip_tags($log['Log']['change']); ?>&nbsp;</td>
										<td><?= $this->Time->format("M j, Y g:i:s A", $log['Log']['created'], NULL, NULL); ?></td>
									</tr>
									<?php
								} ?>
							</tbody>
						</table>
					</div>
					<br>

					<p><?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'))); ?> </p>

					<div class="paging">
						<?= $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class' => 'disabled')); ?> | <?= $this->Paginator->numbers(); ?> | <?= $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled')); ?>
					</div>
					<?php
				} ?>
			</div>
		</div>
	</div>
</div>