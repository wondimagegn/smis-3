<?php
if (isset($distributionStatistics['distributionByDepartmentYearLevel']) && !empty($distributionStatistics['distributionByDepartmentYearLevel'])) { ?>
	<h5><?= $headerLabel; ?></h5>
	<?= $this->element('reports/graph'); ?>

	<?php //debug($years); ?>

	<div style="overflow-x:auto;">
		<table cellpadding="0" cellspacing="0" class="table">
			<thead>
				<tr>
					<td class="center">#</td>
					<td class="vcenter">Department </td>
					<td class="center" colspan="<?= (count($years)*3); ?>">Year Level</td>
				</tr>
				<tr>
					<td class="center"> &nbsp;</td>
					<td class="center"> &nbsp; </td>
					<?php
					if (!empty($this->data['Report']['year_level_id'])) { ?>
						<td class="center"  colspan="3">
							<?= $this->data['Report']['year_level_id']; ?> </td>
						<?php
					} else {
						foreach ($years as $ykey => $yvalue) { ?>
							<td class="center" colspan="3"><?= $yvalue; ?> </td>
							<?php
						}
					} ?>

				</tr>
				<tr>
					<td class="center"> &nbsp;</td>
					<td class="center"> &nbsp; </td>
					<?php
					if (!empty($this->data['Report']['year_level_id'])) { ?>
						<td class="center">M</td>
						<td class="center">F</td>
						<td class="center">T</td>
						<?php
					} else {
						foreach ($years as $ykey => $yvalue) { ?>
							<td class="center">M</td>
							<td class="center">F</td>
							<td class="center">T</td>
							<?php
						}
					} ?>
				</tr>
				
			</thead>
			<tbody>
				<?php
				$count = 0;
				debug($years);
				foreach ($distributionStatistics['distributionByDepartmentYearLevel'] as $departmentName => $yll) { ?>
					<tr>
						<td class="center"><?= ++$count; ?> </td>
						<td class="vcenter"><?= $departmentName; ?></td>
						<?php
						if (!empty($this->data['Report']['year_level_id'])) { ?>
							<td class="center">M</td>
							<td class="center">F</td>
							<td class="center">T</td>
							<?php
						} else {
							if (isset($yll) && !empty($yll)) {
								foreach ($yll as $yn => $yv) {
									/* foreach ($years as $ykey => $yvalue) {  */
									
										if (in_array($yn, $years)) { ?>
											<td class="center"><?= (isset($yv['male']) ? $yv['male'] : ''); ?></td>
											<td class="center"><?= (isset($yv['female']) ? $yv['female'] : ''); ?></td>
											<td class="center">T</td>
											
											<?php
											continue;
										} else { ?>
											<td class="center">M</td>
											<td class="center">F</td>
											<td class="center">T</td>
											<?php
											continue;
										}
									//} 
								} 
							}
						} ?>
					</tr>
					<?php
				} ?>
			</tbody>
		</table>
	</div>

	<hr>
	
	<div style="overflow-x:auto;">
		<table cellpadding="0" cellspacing="0" class="table">
			<thead>
				<tr>
					<td class="center">#</td>
					<td class="vcenter">Department </td>
					<td class="center">Gender</td>
					<td class="center" colspan="<?= count($years); ?>">Year Level</td>
				</tr>
				<tr>
					<td class="center"> &nbsp;</td>
					<td class="center"> &nbsp; </td>
					<td class="center"> &nbsp; </td>
					<?php
					if (!empty($this->data['Report']['year_level_id'])) { ?>
						<td class="center">
							<?= $this->data['Report']['year_level_id']; ?> </td>
						<?php
					} else {
						foreach ($years as $ykey => $yvalue) { ?>
							<td class="center"><?= $yvalue; ?> </td>
							<?php
						}
					} ?>
				</tr>
			</thead>
			<tbody>
				<?php
				$count = 0;
				foreach ($distributionStatistics['distributionByDepartmentYearLevel'] as $departmentName => $yll) { ?>
					<tr>
						<td class="center"><?= ++$count; ?> </td>
						<td class="vcenter"><?= $departmentName; ?> </td>
						<td class="center">Male</td>
						<?php
						if (empty($this->data['Report']['year_level_id'])) {
							$ylmale = 0;
							if (isset($yll) && !empty($yll)) {
								foreach ($yll as $yn => $yv) {
									$ylmale++; ?>
									<td class="center"><?= (isset($yv['male']) ? $yv['male'] : ''); ?></td>
									<?php 
								} 
							}
							for ($ylmale; $ylmale < count($years); $ylmale++ ) { ?>
								<td class="center">&nbsp;</td>
								<?php 
							}
						} else { ?>
							<td class="center"><?= $yll[$this->data['Report']['year_level_id']]['male']; ?></td>
							<?php 
						} ?>
					</tr>
					<tr>
						<td class="center"></td>
						<td class="center"></td>
						<td class="center">Female</td>
						<?php
						if (empty($this->data['Report']['year_level_id'])) {
							$ylfemale = 0;
							if (isset($yll) && !empty($yll)) {
								foreach ($yll as $yn => $yv) {
									$ylfemale++; ?>
									<td class="center"><?= (isset($yv['female']) ? $yv['female'] : ''); ?></td>
									<?php 
								} 
							}
							for ($ylfemale; $ylfemale < count($years); $ylfemale++ ) { ?>
								<td class="center">&nbsp;</td>
								<?php 
							} 
						} else { ?>
							<td class="center"><?= $yll[$this->data['Report']['year_level_id']]['female']; ?> </td>
							<?php 
						} ?>
					</tr>
					<?php
				} ?>
			</tbody>
		</table>
	</div>
	<?php
} ?>

<?php
if (isset($distributionStatistics['distributionByRegionYearLevel']) && !empty($distributionStatistics['distributionByRegionYearLevel'])) { ?>
	<h5><?= $headerLabel; ?></h5>
	<?= $this->element('reports/graph'); ?>
	<div style="overflow-x:auto;">
		<table cellpadding="0" cellspacing="0" class="table">
			<thead>
				<tr>
					<td class="center">#</td>
					<td class="center">Department</td>
					<td class="center">Region</td>
					<td class="center">Gender</td>
					<td class="center" colspan="<?= count($years); ?>">Year Level</td>
				</tr>
				<tr>
					<td class="center"> &nbsp;</td>
					<td class="center"> &nbsp; </td>
					<td class="center"> &nbsp; </td>
					<td class="center"> &nbsp; </td>
					<?php
					if (empty($this->data['Report']['year_level_id'])) {
						foreach ($years as $ykey => $yvalue) { ?>
							<td class="center"><?= $yvalue; ?> </td>
							<?php
						}
					} else if (!empty($this->data['Report']['year_level_id'])) { ?>
						<td class="center"> <?= $this->data['Report']['year_level_id']; ?></td>
						<?php 
					} ?>
				</tr>
			</thead>
			<tbody>
				<?php
				$count = 0;
				foreach ($distributionStatistics['distributionByRegionYearLevel'] as $departmentNamee => $regionss) {
					$nameDisplay = false;
					foreach ($regionss as $rkey => $rvalue) { 
						if (isset($rvalue['male'])) { ?>
							<tr>
								<td class="center">
									<?= ($nameDisplay == false ? ++$count : ''); ?>
								</td>
								<td class="center">
									<?php
									if ($nameDisplay == false) {
										echo $departmentNamee;
										$nameDisplay = true;
									} ?> 
								</td>
								<td class="center"><?= $rkey; ?></td>
								<td class="center">Male</td>
								<?php
								if (empty($this->data['Report']['year_level_id'])) {
									$counttdm = 0;
									foreach ($rvalue['male'] as $mn => $ym) {
										$counttdm++; ?>
										<td class="center"><?= $ym; ?></td>
										<?php 
									}
									for ($counttdm; $counttdm < count($years); $counttdm++ ) { ?>
										<td class="center">&nbsp;</td>
										<?php 
									} 
								} else { ?>
									<td class="center"><?= $rvalue['male'][$this->data['Report']['year_level_id']]; ?></td>
									<?php 
								} ?>
							</tr>
							<?php 
						}  
						if (isset($rvalue['female'])) { ?>
							<tr>
								<td class="center">
									<?= ($nameDisplay == false ? ++$count: ''); ?>
								</td>
								<td class="center">
									<?php
									if ($nameDisplay == false) {
										echo $departmentNamee;
										$nameDisplay = true;
									} ?> 
								</td>
								<td class="center"><?= $rkey; ?></td>
								<td class="center">Female</td>
								<?php
								if (empty($this->data['Report']['year_level_id'])) {
									$counttdf = 0;
									foreach ($rvalue['female'] as $mn => $ym) {
										$counttdf++; ?>
										<td class="center"><?= $ym; ?></td>
										<?php 
									} 
									for ($counttdf; $counttdf < count($years); $counttdf++) { ?>
										<td class="center">&nbsp;</td>
										<?php 
									}
								} else { ?>
									<td class="center"><?= $rvalue['female'][$this->data['Report']['year_level_id']]; ?></td>
									<?php 
								} ?>
							</tr>
							<?php 
						}
					}
				} ?>
			</tbody>
		</table>
	</div>
	<?php
} ?>