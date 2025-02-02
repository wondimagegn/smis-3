<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Check official transcript request status'); ?> </span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;">
					<hr>
					<br>
					<?= $this->Form->create('Page', array('controller' => 'pages', 'action' => 'official_request_tracking', 'method' => 'post')); ?>
					<div class="row">
						<div class="large-4 columns">
							<?= $this->Form->input('OfficialTranscriptRequest.trackingnumber', array('label' => 'Tracking number', 'placeholder' => 'Tracking Number? ', 'required', 'type' => 'number', 'style' => 'width: 50%;')); ?>
						</div>
					</div>
					<div class="row">
						<div class="large-4 columns">
							<?= $this->Form->end(array('label' => __('Check Status', true), 'class' => 'tiny radius button bg-blue')); ?>
						</div>
					</div>
					<hr>
				</div>

				<?php
				if (isset($request) && !empty($request)) { ?>
					<fieldset style="margin-top: 15px; margin-bottom: 0px;">
						<div class="large-12 columns"><span class="fs14 text-gray"><b>Name: </b></span> <span class="fs14 text-black"><b><?= $request['OfficialTranscriptRequest']['first_name'] . ' ' . $request['OfficialTranscriptRequest']['father_name'] . ' ' . $request['OfficialTranscriptRequest']['grand_father']; ?></b></span></div>
						<div class="large-12 columns"><span class="fs14 text-gray"><b>Student ID: </b></span> <span class="fs14 text-black"><b><?= $request['OfficialTranscriptRequest']['studentnumber']; ?></b></span></div>
						<div class="large-12 columns">&nbsp;</div>
						<div class="large-12 columns">
							<div style="overflow-x:auto;">
								<table cellpadding="0" cellspacing="0" class="table-borderless">
									<thead>
										<tr>
											<td style="width: 20%;">Status</td>
											<td style="width: 60%;">Request Date</td>
											<td style="width: 20%;">Remark </td>
										</tr>
									</thead>
									<tbody>
										<?php
										if (isset($request['OfficialRequestStatus']) && !empty($request['OfficialRequestStatus'])) {
											foreach ($request['OfficialRequestStatus'] as $kk => $kv) { ?>
												<tr>
													<td> <?= $statuses[$kv['status']]; ?> </td>
													<td> <?= date("F j, Y, g:i a", strtotime($kv['created'])); ?> </td>
													<td> <?= $kv['remark']; ?> </td>
												</tr>
												<?php
											}
										} ?>
									</tbody>
								</table>
							</div>
						</div>
					</fieldset>
					<?php
				} ?>
			</div>
		</div>
	</div>
</div>