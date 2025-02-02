<?php
if (isset($activeList) && !empty($activeList)) { 

	$totalStudents = 0;
	$totalMaleStudents = 0;
	$totalFemaleStudents = 0;
	$withUnknowntatus = 0;
	$graduatedStudentsIncluded = 0;

	//debug($activeList);
	foreach ($activeList as $programD => $list) {
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
						<td colspan="8" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
							<span style="font-size:15px;font-weight:bold;"><?= $headerExplode[4] . ' ' . (isset($headerExplode[5])  ?  ' ('.  $headerExplode[7] . ',  ' . $headerExplode[5] . '' . (isset($headerExplode[6]) ? ', ' . ($headerExplode[6] == 'I' ? '1st Semester' : ($headerExplode[6] == 'II' ? '2nd Semester' : ($headerExplode[6] == 'III' ? '3rd Semester' : $headerExplode[6] . ' Semester'))) : '') . ')' : ''); ?></span><br>
							<span class="text-gray" style="font-size: 13px; font-weight: bold">
								<?= (isset($headerExplode[1]) && !empty($headerExplode[1]) ? $headerExplode[1] : ($headerExplode[2] == 'Remedial' ? 'Remedial Program' : 'Pre/Freshman')) . '' . (isset($headerExplode[0]) && !empty($headerExplode[0]) ? ' &nbsp; | &nbsp; ' . $headerExplode[0] : ''); ?><br>
								<?= $headerExplode[2] . ' &nbsp; | &nbsp; ' . $headerExplode[3]; ?><br>
								<?php //echo $this->request->data['Report']['acadamic_year'] . (empty($this->request->data['Report']['semester']) ? '' : (' &nbsp; | &nbsp; ' . ($this->request->data['Report']['semester'] == 'I' ? '1st Semester' : ($this->request->data['Report']['semester'] == 'II' ? '2nd Semester' : ($this->request->data['Report']['semester'] == 'III' ? '3rd Semester' : $this->request->data['Report']['semester'] . ' Semester'))))); ?><!-- <br> -->
							</span>
						</td>
					</tr>
					<tr>
						<th class="center">#</th>
						<th class="vcenter">Full Name</th>
						<th class="center">Student ID</th>
						<th class="center">Sex</th>
						<th class="center"><?= (isset($headerExplode[8]) && !empty($headerExplode[8]) ? $headerExplode[8] : 'Credit'); ?></th>
						<th class="center">SGPA</th>
						<th class="center">CGPA</th>
						<th class="center">Status</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$count = 0;
					foreach ($list as $ko => $val) {
						if ($val['academic_status_id'] != DISMISSED_ACADEMIC_STATUS_ID) { ?>
							<?php $totalStudents++; ?>
							<tr class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?= $val['id']; ?>">
								<td class="center"><?= ++$count; ?></td>
								<td class="vcenter"><?= $val['first_name'] . ' ' . $val['middle_name'] . ' ' . $val['last_name']; ?></td>
								<td class="center"><?= $val['studentnumber']; ?></td>
								<td class="center"><?php if (strcasecmp(trim($val['gender']), 'male') == 0) { echo 'M'; $totalMaleStudents++; } else { echo 'F'; $totalFemaleStudents++; } ?></td>
								<td class="center"><?= (isset($val['credit_hour_sum']) ? $val['credit_hour_sum'] : '---'); ?></td>
								<td class="center"><?= $val['sgpa']; ?></td>
								<td class="center" style="font-weight: bold;"><?= $val['cgpa']; ?></td>
								<td class="center">
									<?php
									if (isset($val['graduated']) && !empty($val['graduated']) && $val['graduated'] == 1) {
										echo '<span class="rejected">Graduated</span>';
										$graduatedStudentsIncluded++;
									} else {
										if (isset($academicStatus[$val['academic_status_id']])) {
											echo '<span class="accepted">'. $academicStatus[$val['academic_status_id']] .'</span>';
										} else {
											echo '---';
											$withUnknowntatus++;
										}
									} ?>
								</td>
							</tr>
							<?php
						}
					} ?>
				</tbody>
			</table>
		</div>
		<hr>
		<?php
	} ?>

	<span class="text-black fs14">
		<strong>Stats for selected Active List: </strong><br />
		Total: <?= ($totalStudents) ?> <br />
		Male: <?= ($totalMaleStudents) . ($totalMaleStudents != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($totalMaleStudents / $totalStudents) * 100), 2) . '%)') : ''); ?><br />
		Female: <?= ($totalFemaleStudents) . ($totalFemaleStudents != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($totalFemaleStudents / $totalStudents) * 100), 2) . '%)') : ''); ?><br />
		With Unknown Status: <?= ($withUnknowntatus) . ($withUnknowntatus != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($withUnknowntatus / $totalStudents) * 100), 2) . '%)') : ''); ?><br />
		Graduated Students included: <?= ($graduatedStudentsIncluded) . ($graduatedStudentsIncluded != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($graduatedStudentsIncluded / $totalStudents) * 100), 2) . '%)') : ''); ?>
		<hr />
	</span>

	<?php
	if ($withUnknowntatus != 0) { ?>
		<blockquote>
			<cite>Important Note</cite>
			If there are students with unknown Status, it might be connected with not maintaining the minimum credits required for status determination defined for the selected program and/or program type.
			Make sure stuudents registered for courses above the defined minimum credit for status determination by referring the following table.
		</blockquote>
		
		<div style="overflow-x:auto;">
			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<td class="vcenter">Program</td>
						<td class="vcenter">Program Type</td>
						<td class="center">Credit (Min)</td>
						<td class="center">ECTS (Min)</td>
					</tr>
				</thead>
				<tbody>
					<?php
					$generalSettings = ClassRegistry::init('GeneralSetting')->find('all', array('recursive' => -1));

					if (!empty($generalSettings)) {
						foreach ($generalSettings as $keyyy => &$valll) {
							$programs = ClassRegistry::init('Program')->find('list', array('conditions' => array('id' => unserialize($valll['GeneralSetting']['program_id']))));
							$programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('id' => unserialize($valll['GeneralSetting']['program_type_id']))));
							$valll['GeneralSetting']['program_id'] = array_values($programs);
							$valll['GeneralSetting']['program_type_id'] = array_values($programTypes);
						}
						//debug($generalSettings);

						foreach ($generalSettings as $generalSetting) { ?>
							<tr>
								<td class="vcenter">
									<?php
									foreach ($generalSetting['GeneralSetting']['program_id'] as $key => $value) {
										echo $value . '<br/>';
									} ?>
								</td>
								<td class="vcenter">
									<?php
									foreach ($generalSetting['GeneralSetting']['program_type_id'] as $key => $value) {
										echo $value . ', ';
									} ?>
								</td>
								<td class="center"><?= $generalSetting['GeneralSetting']['minimumCreditForStatus']; ?></td>
								<td class="center"><?= round(($generalSetting['GeneralSetting']['minimumCreditForStatus'] * CREDIT_TO_ECTS), 0); ?></td>
							</tr>
							<?php
						}  
					} ?>
				</tbody>
			</table>
		</div>
		<?php
	}
} ?>