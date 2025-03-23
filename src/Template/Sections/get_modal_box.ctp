<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">Students not qualified for year level upgrade</span>
        </div>

		<a class="close-reveal-modal">&#215;</a>
    </div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -40px;"><hr></div>
				<?php
				if (isset($students_details)) {
					$unqualified_students_count = count($students_details);
					if ($unqualified_students_count == 0) { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>All Students of this Section are qualified for the year level upgrade.</div>
						<?php
					} else { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span><?= ($unqualified_students_count == 1 ? $unqualified_students_count . ' student is not qualified to upgrade with this section. Thus, the student will be' : $unqualified_students_count . ' students are not qualified to upgrade with their section, Thus, they will be'); ?> section-less if this section is upgraded to the next year level.</div>
						<div style="overflow-x:auto;">
                            <table  cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<th class="center" style="width: 5%">#</th>
										<th class="vcenter" style="width: 25%">Full Name</th>
										<th class="vcenter" style="width: 5%">Sex</th>
										<th class="center" style="width: 15%">Student ID</th>
										<th class="vcenter">Reason</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 1;
									foreach ($students_details as $sdk => $sdv) { ?>
										<tr>
											<td class="center"><?= $count++; ?></td>
											<td class="vcenter"><?= $sdv['Student']['full_name']; ?></td>
											<td class="center"><?= (strcasecmp(trim($sdv['Student']['gender']), 'male') == 0 ? 'M' :(strcasecmp(trim($sdv['Student']['gender']), 'female') == 0 ? 'F' : $sdv['Student']['gender'])); ?></td>
											<td class="center"><?= $sdv['Student']['studentnumber']; ?></td>
											<td class="vcenter"><?= (!empty($status_name) ? 'Status not generated or Have invalid grades or ' . $status_name : 'Status not generated/Student have invalid grade'); ?></td>
										</tr>
										<?php
									} ?>
								</tbody>
							</table>
						</div>
						<?php
					}
				} ?>
			</div>
		</div>
	</div>
</div>