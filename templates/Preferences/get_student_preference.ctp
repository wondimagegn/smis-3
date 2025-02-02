<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-vcard" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Student Preference List'); ?> :  <?= $studentBasic['AcceptedStudent']['full_name'] . ' (' . $studentBasic['AcceptedStudent']['studentnumber'] . ')'; ?></span>
		</div>

		<a class="close-reveal-modal">&#215;</a>
	</div>
	<div class="box">
		<div class="box-body">
			<div class="row">
				<div class="large-12 columns">
					<div style="margin-top: -40px;"><hr></div>
					<?php 
					if (!empty($studentsPreference)) { ?>
						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<th>Department</th>
										<th style="text-align: center;">Preference Order</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($studentsPreference as $k => $v) { ?>
										<tr>
											<td><?= $v['Department']['name']; ?></td>
											<td style="text-align: center;"><?= $v['Preference']['preferences_order']; ?></td>
										</tr>
										<?php
									} ?>
								</tbody>
							</table>
						</div>
						<?php 
					} else { ?>
						<div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>There is no department preference filled by the selected student.</div>
						<?php 
					} ?>
				</div>
			</div>
		</div>
	</div>
</div>