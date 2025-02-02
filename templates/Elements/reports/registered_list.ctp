<?php
if (isset($registeredList) && !empty($registeredList)) { 

	$totalStudents = 0;
	$totalMaleStudents = 0;
	$totalFemaleStudents = 0;
	$withUnknowntatus = 0;
	$graduatedStudentsIncluded = 0;
	//debug($registeredList);
	foreach ($registeredList as $programD => $list) {
		$headerExplode = explode('~', $programD);  ?>
		<!-- <br /> -->
		<!-- <p class="fs16">
			<strong>College: </strong> <?php //echo $headerExplode[0]; ?><br />
			<strong>Department: </strong> <?php //echo $headerExplode[1]; ?><br />
			<strong>Program: </strong> <?php //echo $headerExplode[2]; ?><br />
			<strong>Program Type: </strong> <?php //echo $headerExplode[3]; ?><br />
			<strong>Section: </strong> <?php //echo $headerExplode[4]; ?><br />
			<strong>Academic Year: </strong> <?php //echo $headerExplode[5]; ?><br />
			<strong>Semester: </strong> <?php //echo $headerExplode[6]; ?><br />
		</p> -->

		<div style="overflow-x:auto;">
			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<td colspan= <?= (isset($this->data['Report']['exclude_graduated']) &&  $this->data['Report']['exclude_graduated'] == 1 ? '8' : '7'); ?> style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
							<span style="font-size:15px;font-weight:bold;"><?= $headerExplode[4] . ' ' . (isset($headerExplode[5])  ?  ' (' .  $headerExplode[7] . ',  ' . $headerExplode[5] . '' . (isset($headerExplode[6]) ? ', ' . ($headerExplode[6] == 'I' ? '1st Semester' : ($headerExplode[6] == 'II' ? '2nd Semester' : ($headerExplode[6] == 'III' ? '3rd Semester' : $headerExplode[6] . ' Semester'))) : '') . ')' : ''); ?></span><br>
							<span class="text-gray" style="font-size: 13px; font-weight: bold">
								<?= (isset($headerExplode[1]) && !empty($headerExplode[1]) ? $headerExplode[1] : ($headerExplode[2] == 'Remedial' ? 'Remedial Program' : 'Pre/Freshman')) . '' . (isset($headerExplode[0]) && !empty($headerExplode[0]) ? ' &nbsp; | &nbsp; ' . $headerExplode[0] : ''); ?><br>
								<?= $headerExplode[2] . ' &nbsp; | &nbsp; ' . $headerExplode[3]; ?> <br>
								<?php //echo $this->request->data['Report']['acadamic_year'] . (empty($this->request->data['Report']['semester']) ? '' : (' &nbsp; | &nbsp; ' . ($this->request->data['Report']['semester'] == 'I' ? '1st Semester' : ($this->request->data['Report']['semester'] == 'II' ? '2nd Semester' : ($this->request->data['Report']['semester'] == 'III' ? '3rd Semester' : $this->request->data['Report']['semester'] . ' Semester'))))); ?><!-- <br> -->
							</span>
						</td>
					</tr>
					<tr>
						<th class="center">#</th>
						<th class="vcenter">Full Name</th>
						<th class="center">Student ID</th>
						<th class="center">Sex</th>
						<th class="center">Registered</th>
						<th class="center">Added</th>
						<th class="center">Total <?= (isset($headerExplode[8]) && !empty($headerExplode[8]) ? $headerExplode[8] : 'Credit'); ?></th>
						<?php
						if (isset($this->data['Report']['exclude_graduated']) &&  $this->data['Report']['exclude_graduated'] == 1 ) { ?>
							<th class="center">Graduated</th>
							<?php
						} ?>
					</tr>
				</thead>
				<tbody>
					<?php
					$count = 0;
					foreach ($list as $ko => $val) {  ?>
						<?php $totalStudents++; ?>
						<tr class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?= $val['id']; ?>">
							<td class="center"><?= ++$count; ?></td>
							<td class="vcenter"><?= $val['first_name'] . ' ' . $val['middle_name'] . ' ' . $val['last_name']; ?></td>
							<td class="center"><?= $val['studentnumber']; ?></td>
							<td class="center"><?php if (strcasecmp(trim($val['gender']), 'male') == 0) { echo 'M'; $totalMaleStudents++; } else { echo 'F'; $totalFemaleStudents++; } ?></td>
							<td class="center"><?= ((isset($val['registered']) && $val['registered'] != 0 ) ? $val['registered'] : '---'); ?></td>
							<td class="center"><?= ((isset($val['added']) && $val['added'] != 0) ? $val['added'] : '---'); ?></td>
							<td class="center"><?= ((isset($val['total']) && $val['total'] != 0 )? $val['total'] : '---'); ?></td>
							<?php
							if (isset($val['graduated']) && !empty($val['graduated']) && $val['graduated'] == 1) {
								$graduatedStudentsIncluded++;
							}

							if (isset($this->data['Report']['exclude_graduated']) &&  $this->data['Report']['exclude_graduated'] == 1 ) { ?>
								<td class="center">
									<?= (isset($val['graduated']) && !empty($val['graduated']) && $val['graduated'] == 1 ? 'Yes': 'No'); ?>
								</td>
								<?php
							} ?>
						</tr>
						<?php
					} ?>
				</tbody>
			</table>
		</div>
		<hr>
		<?php
	} ?>
	<!-- <br /> -->
	<span class="text-black fs14">
		<!-- <hr /> -->
		<strong>Stats for selected Registered List: </strong><br />
		Total: <?= ($totalStudents) ?> <br />
		Male: <?= ($totalMaleStudents) . ($totalMaleStudents != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($totalMaleStudents / $totalStudents) * 100), 2) . '%)') : ''); ?><br />
		Female: <?= ($totalFemaleStudents) . ($totalFemaleStudents != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($totalFemaleStudents / $totalStudents) * 100), 2) . '%)') : ''); ?><br />
		<!-- With Unknown Status: <?php //echo ($withUnknowntatus) . ($withUnknowntatus != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($withUnknowntatus / $totalStudents) * 100), 2) . '%)') : ''); ?><br /> -->
		Graduated Students included: <?= ($graduatedStudentsIncluded) . ($graduatedStudentsIncluded != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($graduatedStudentsIncluded / $totalStudents) * 100), 2) . '%)') : ''); ?>
		<!-- <hr /> -->
	</span>
	<?php
} ?>