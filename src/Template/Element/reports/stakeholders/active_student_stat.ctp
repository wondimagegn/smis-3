<?php 
if (isset($currentlyActiveStudentStatistics['result']) && !empty($currentlyActiveStudentStatistics['result'])) {
    foreach($currentlyActiveStudentStatistics['result'] as $program => $statDetail) {
		if (isset($sumByProgramType)) {
			unset($sumByProgramType);
		}
		//debug($program); ?>
    	<p class="fs16"> <?php //echo str_replace('undergraduate/graduate', $program, $headerLabel); ?></p>
		<hr>
		<h6 class="fs16 text-gray"><?= $program; ?> Program for <?= $this->data['Report']['acadamic_year'] . ' Academic Year Semester ' . $this->data['Report']['semester']; ?></h6>
		<hr>
		<div style="overflow-x:auto;">
			<table cellpadding="0" cellspacing="0" class="table">
				<tr>
					<th rowspan="2" class="center" style="width:2%">#</th>
					<th rowspan="2" class="center" style="width:15%">College/Institute/School</th>
					<th rowspan="2" class="center"  style="width:8%">Department</th>
					<th rowspan="2" class="center"  style="width:8%;border-left:2px #000000 solid;border-right:2px #000000 solid;">Year</th>
					<?php
					unset($program_types[0]);
					if (isset($program_types) && !empty($program_types)) {
						foreach ($program_types as $k=>$value) { ?>
							<th colspan="3" class="center" style="border-right:2px #000000 solid;"><?= $value;?></th>
							<?php 
						}
					} ?>
				</tr>
				<tr>
					<?php 
					if (isset($program_types) && !empty($program_types)) {
						foreach ($program_types as $k => $value) { ?>
							<th style="width:5%" class="center">M</th>
							<th style="width:5%" class="center">F</th>
							<th style="width:5%;border-right:2px #000000 solid;" class="center">T</th>
							<?php 
						} 
					} ?>
				</tr>
				<?php 
				$count = 0;
				foreach ($statDetail as $college => $departmentList) { ?>
					<tr>
						<td class="center" rowspan="<?= $currentlyActiveStudentStatistics['collegeRowSpan'][$college] + count($departmentList) + 1; ?>"><?= ++$count; ?></td>
						<td class="vcenter" rowspan="<?= $currentlyActiveStudentStatistics['collegeRowSpan'][$college] + count($departmentList) + 1; ?>"><?= $college; ?></td>
					</tr>
					<?php 
					foreach ($departmentList as $deptname => $yearList) { ?>
						<tr>
							<td class="vcenter" rowspan="<?= count($yearList) + 1; ?>"><?= $deptname; ?></td>
						</tr>
						<?php 
						foreach ($yearList as $yk => $programTypesList) { ?>
							<tr>
								<td class="center" style="border-left:2px #000000 solid;border-right:2px #000000 solid;"><?= $yk; ?></td>
								<?php 
								$k = 0;
								foreach ($programTypesList as $pk => $ppv) {

									if (isset($ppv['male']) && $ppv['male']) {
										if (isset($sumByProgramType[$pk]['male'])) {
											$sumByProgramType[$pk]['male'] += $ppv['male'];
										} else {
											$sumByProgramType[$pk]['male'] = $ppv['male'];
										}
									}

									if (isset($ppv['female']) && $ppv['female']) {
										if (isset($sumByProgramType[$pk]['female'])) {
											$sumByProgramType[$pk]['female'] += $ppv['female'];
										} else {
											$sumByProgramType[$pk]['female'] = $ppv['female'];
										}
									}

									if (isset($ppv['total'])) {
										if (isset($sumByProgramType[$pk]['total'])) {
											$sumByProgramType[$pk]['total'] += $ppv['total'];
										} else {
											$sumByProgramType[$pk]['total'] = $ppv['total'];
										}
										$k += $ppv['total'];
									} ?>

									<td class="center"><?= (isset($ppv['male']) && $ppv['male'] ? $ppv['male'] : ''); ?></td>
									<td class="center"><?= isset($ppv['female']) && $ppv['female'] ? $ppv['female'] : ''; ?></td>
									<td class="center" style="border-right:2px #000000 solid; font-weight: bold;"><?= (isset($ppv['total']) && $ppv['total'] ? $ppv['total'] : ''); ?></td>
									<?php 
								} 

								if ($this->data['Report']['program_type_id'] == 0) { ?>
									<td class="center" style="border-right:2px #000000 solid;"><?= isset($k) && $k ? $k : ''; ?></td>
									<?php
								} ?>

								
							</tr>
							<?php
						} ?>
						<?php 
					}
				} ?>

				<tr>
					<th rowspan="2" class="center" style="vertical-align:bottom; width:2%"></th>
					<th rowspan="2" class="center" style="vertical-align:bottom; width:15%"></th>
					<th rowspan="2" class="center" style="vertical-align:bottom; width:8%"></th>
					<th rowspan="2" class="center" style="vertical-align:bottom; width:8%; font-weight: bold;">Total</th>

					<?php unset($program_types[0]); ?>

				</tr>
				<tr>
					<?php 
					if (isset($program_types) && !empty($program_types)) {
						foreach ($program_types as $k => $pvalue) { ?>
							<th style="width:5%" class="center"><?= (isset($sumByProgramType[$pvalue]['male']) && $sumByProgramType[$pvalue]['male'] ? $sumByProgramType[$pvalue]['male'] : ''); ?></th>
							<th style="width:5%" class="center"><?= (isset($sumByProgramType[$pvalue]['female']) && $sumByProgramType[$pvalue]['female'] ? $sumByProgramType[$pvalue]['female'] : ''); ?></th>
							<th style="width:5%" class="center"><?= (isset($sumByProgramType[$pvalue]['total']) && $sumByProgramType[$pvalue]['total'] ? $sumByProgramType[$pvalue]['total'] : ''); ?></th>
							<?php 
						}
					} ?>
				</tr>
			</table>
		</div>
		<?php 
  	}
 } ?>