<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('View Logs'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<?= $this->Form->create('Log'); ?>
			<div class="large-12 columns">
				<div style="margin-top: -30px;">
					<hr>
					<blockquote>
						<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<p style="text-align:justify;"><span class="fs16"> This tool will help you to get user logs with some defined search criterias.</span></p> 
						<p style="text-align:justify;">
							<span class="fs16">
							<ol class="fs14">
								<li>You can enter more than one IP, username, model and action. <span class="text-red" style="font-weight: bold;">Use comma to separate each entry.</span>
									<br>
									Eg. 10.144.10.128, 10.144.10.121, 10.144.10.102 will bring logs recorded from 10.144.10.128, 10.144.10.121, 10.144.10.102 IP address.
								</li>
								<li>You can exclude one or more IP, username, model and action <span class="text-red" style="font-weight: bold;">by using minus (-) before the entry.</span>
									<br>
									Eg. -10.144.10.128 will exclude any log from 10.144.10.128 IP address.
								</li>
							</ol>
							</span>
						</p>
					</blockquote>
				</div>
				<hr>

				<fieldset style="padding-bottom: 5px;padding-top: 5px;">
					<legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
					<div class="row">
						<div class="large-6 columns">
							<?= $this->Form->input('change_date_from', array('label' => 'Logs From: ', /* 'type' => 'datetime', */ 'type' => 'datetime', 'style' => 'width:15%;', 'dateFormat' => 'MDY', /* 'minYear' => Configure::read('Calendar.applicationStartYear') */ 'minYear' => (date('Y') - 2), 'maxYear' => date('Y'), 'orderYear' => 'desc', 'selected' => array('year' => (isset ($this->request->data['Log']['change_date_from']) ? $this->request->data['Log']['change_date_from']['year'] : date('Y')), 'month' => (isset($this->request->data['Log']['change_date_from']) ? $this->request->data['Log']['change_date_from']['month'] : date('m')), 'day' => (isset($this->request->data['Log']['change_date_from']) ? $this->request->data['Log']['change_date_from']['day'] : date('d') - 14)))); ?>
						</div>
						<div class="large-6 columns">
							<?= $this->Form->input('change_date_to', array('label' => 'Logs To: ', /* 'type' => 'datetime', */ 'type' => 'datetime', 'style' => 'width:15%;', 'dateFormat' => 'MDY', /* 'minYear' => Configure::read('Calendar.applicationStartYear') */ 'minYear' => (date('Y') - 2),  'maxYear' => date('Y'), 'orderYear' => 'desc')); ?>
						</div>
					</div>
					<div class="row">
						<div class="large-3 columns">
							<?= $this->Form->input('role_id', array('label' => 'Role: ', 'style' => 'width:90%;', 'type' => 'select')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('username', array('maxlength' => 1000, 'label' => 'Username: ', 'placeholder' => 'eg: username or -username or username1, username2 .. ', 'style' => 'width:90%;', 'type' => 'text')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('ip', array('maxlength' => 1000, 'label' => 'IP Address(es)', 'placeholder' => 'eg: 10.144.140.10 or -10.144.140.10 .. ', 'style' => 'width:90%;', 'type' => 'text')); ?>
						</div>
						<div class="large-3 columns" style="line-height: 1.5px;">
							&nbsp;<br>
							<div style="margin-top: 15px;">
								<?= $this->Form->input('active', array('label' => 'Active User Account', 'type' => 'checkbox', 'checked' => (!isset ($this->request->data['Log']['active']) || $this->request->data['Log']['active'] == 1 ? 'checked' : false))); ?> <br>
								<?= $this->Form->input('deactive', array('label' => 'Non Active User Account', 'type' => 'checkbox', 'checked' => (!isset ($this->request->data['Log']['deactive']) || $this->request->data['Log']['deactive'] == 1 ? 'checked' : false))); ?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="large-3 columns">
							<?= $this->Form->input('action', array('maxlength' => 1000, 'label' => 'Action: ', 'placeholder' => 'eg: Add, Edit, mass_register.. ', 'style' => 'width:90%;', 'type' => 'text')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('model', array('maxlength' => 1000, 'label' => 'Model: ', 'placeholder' => 'eg: ExamGrade, CourseRegistration, ExamGradeChange.. ', 'style' => 'width:90%;', 'type' => 'text')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('change', array('maxlength' => 1000, 'label' => 'Change: ', 'placeholder' => 'eg: last_login, exam_grade_id, ExamGradeChange.. ', 'style' => 'width:90%;', 'type' => 'text')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('description', array('maxlength' => 1000, 'label' => 'Description: ', 'placeholder' => 'eg: updated by, Via backend data entry interface.. ', 'style' => 'width:90%;', 'type' => 'text')); ?>
						</div>
					</div>
					<div class="row">
						<div class="large-3 columns">
							<?= $this->Form->input('key', array('maxlength' => 1000, 'label' => 'Key(Table ID): ', 'placeholder' => 'eg: 103774 or 102445, 133774, 37736', 'style' => 'width:90%;', 'type' => 'text')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('size', array('id' => 'limit ', 'type' => 'number', 'min' => '50',  'max' => '1000', 'value' => $limit, 'step' => '50', 'label' => 'Limit: ', 'style' => 'width:40%;')); ?>
						</div>
						<div class="large-3 columns">
							&nbsp;
						</div>
						<div class="large-3 columns">
							&nbsp;
						</div>
					</div>
					<hr>
					<?= $this->Form->submit(__('Search Logs'), array('name' => 'searchLogs', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
				</fieldset>
				<hr>

			</div>

			<div class="large-12 columns">
				<?php
				if (!empty ($logs)) { ?>
					<hr>
					<h6 class="fs16 text-gray"><?= __('List of logs based on the given search criteria'); ?></h6>

					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
						<!-- <table cellpadding="0" cellspacing="0" style="table-layout:fixed"> -->
							<thead>
								<tr>
									<td style="width:3%">#</td>
									<td style="width:8%"><?= $this->Paginator->sort('Key', 'Table ID'); ?></td>
									<td style="width:13%"><?= $this->Paginator->sort('user_id'); ?></td>
									<td style="width:8%"><?= $this->Paginator->sort('ip', 'IP Address'); ?></td>
									<td style="width:15%"><?= $this->Paginator->sort('model'); ?></td>
									<td style="width:8%"><?= $this->Paginator->sort('action'); ?></td>
									<td style="width:12%"><?= $this->Paginator->sort('description'); ?></td>
									<td style="width:24%"><?= $this->Paginator->sort('change'); ?></td>
									<td style="width:10%"><?= $this->Paginator->sort('Date', 'created'); ?></td>
								</tr>
							</thead>
							<tbody>
								<?php
								$start = $this->Paginator->counter('%start%');
								foreach ($logs as $log) { ?>
									<tr>
										<td><?= $start++; ?></td>
										<td><?= $log['Log']['foreign_key']; ?></td>
										<td>
											<?php
											if (!empty ($log['User']['first_name'])) {
												echo $this->Html->link($log['User']['first_name'] . ' ' . $log['User']['middle_name'] . ' ' . $log['User']['last_name'] . ' (' . $log['User']['username'] . ')', array('controller' => 'users', 'action' => 'view', $log['User']['id']));
											} else {
												echo $this->Html->link($log['User']['username'], array('controller' => 'users', 'action' => 'view', $log['User']['id']));
											}?>
										</td>
										<td><?= $log['Log']['ip']; ?></td>
										<td><?= $log['Log']['model']; ?></td>
										<td><?= $log['Log']['action']; ?></td>
										<td><?= $log['Log']['description']; ?></td>
										<td><?= strip_tags($log['Log']['change']); ?></td>
										<td><?= $this->Format->humanize_date_short2($log['Log']['created']); ?></td>
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
				} ?>
			</div>
		</div>
	</div>
</div>