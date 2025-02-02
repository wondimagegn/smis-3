<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-certificate-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Graduation Certificate Templates'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<table cellpadding="0" cellspacing="0" class="table-borderless">
					<thead>
						<tr>
							<td>#</td>
							<td><?= $this->Paginator->sort('program_id'); ?></td>
							<td><?= $this->Paginator->sort('program_type_id'); ?></td>
							<td>Target</td>
							<td><?= $this->Paginator->sort('academic_year'); ?></td>
							<td><?= $this->Paginator->sort('amharic_title'); ?></td>
							<td><?= $this->Paginator->sort('english_title'); ?></td>
							<td><?= $this->Paginator->sort('applicable_for_current_student', 'For Current Student'); ?></td>
							<td style="text-align:center" class="actions"><?= __('Actions'); ?></td>
						</tr>
					</thead>
					<tbody>
						<?php
						$start = $this->Paginator->counter('%start%');
						foreach ($graduationCertificates as $graduationCertificate) { ?>
							<tr>
								<td><?= $start++; ?></td>
								<td><?= $graduationCertificate['Program']['name']; ?></td>
								<td><?= $graduationCertificate['ProgramType']['name']; ?></td>
								<td>
									<?php
									//debug($departments);
									//debug($graduationCertificate['GraduationCertificate']['department']);
									foreach ($departments as $k => $v) {
										if (strcasecmp($k, $graduationCertificate['GraduationCertificate']['department']) == 0) {
											echo $v;
											break;
										} else {
											if (is_array($v)) {
												foreach ($v as $k1 => $v1) {
													if ($k1 == $graduationCertificate['GraduationCertificate']['department']) {
														echo $v1;
														break 2;
													}
												}
											}
										}
									} ?>
								</td>
								<td style="text-align: center;"><?= $graduationCertificate['GraduationCertificate']['academic_year']; ?>&nbsp;</td>
								<td><?= $graduationCertificate['GraduationCertificate']['amharic_title']; ?>&nbsp;</td>
								<td><?= $graduationCertificate['GraduationCertificate']['english_title']; ?>&nbsp;</td>
								<td style="text-align: center;"><?= ($graduationCertificate['GraduationCertificate']['applicable_for_current_student'] == 1 ? 'Yes' : 'No'); ?>&nbsp;</td>
								<td class="actions">
									<?= $this->Html->link(__('View', true), array('action' => 'view', $graduationCertificate['GraduationCertificate']['id'])); ?>
									<?= $this->Html->link(__('Edit', true), array('action' => 'edit', $graduationCertificate['GraduationCertificate']['id'])); ?>
									<?= $this->Html->link(__('Delete', true), array('action' => 'delete', $graduationCertificate['GraduationCertificate']['id']), null, sprintf(__('Are you sure you want to delete "%s" graduation certificate template?', true), $graduationCertificate['GraduationCertificate']['english_title'])); ?>
								</td>
							</tr>
							<?php
						} ?>
					</tbody>
				</table>

				<p> <?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'))); ?> </p>

				<div class="paging">
					<?= $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled')); ?> | <?= $this->Paginator->numbers(); ?> | <?= $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled')); ?>
				</div>

			</div>
		</div>
	</div>
</div>