<?php
header("Expires: " . gmdate("D,d M YH:i:s") . " GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=" . $filename . ".xls");
header("Content-Description: Exported as XLS");
?>

<?php
if (isset($studentListForOffice) && !empty($studentListForOffice)) { 

	$totalStudents = 0;
	$totalMaleStudents = 0;
	$totalFemaleStudents = 0;
	$graduatedStudentsIncluded = 0;

	foreach ($studentListForOffice as $programD => $list) {
		$headerExplode = explode('~', $programD);  ?>
		<!-- <br>
		<h6 class="fs16"><span class="fs16 text-gray">College:  <?php //echo $headerExplode[0]; ?></span></h6>
		<br> -->

		<div style="overflow-x:auto;">
			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th class="center">#</th>
						<th class="vcenter">University Name</th>
						<th class="vcenter">University ID/Identifier</th>
						<th class="center">Enrollement Type</th>
						<th class="center">Department</th>
						<th class="center">Program</th>
						<th class="center">Year</th>
						<th class="center">Semester</th>
						<th class="center">First name</th>
						<th class="center">Father's Name</th>
						<th class="center">Grandfather's Name</th>
						<th class="center">Gender</th>
						<th class="center">Institutional Email</th>
						<th class="center">Alternaive Email</th>
						<th class="center">Phone Number</th>
						<th class="center">Is-Blind</th>
						<th class="center">Is-Deaf</th>

						<?php
						if (isset($this->data['Report']['exclude_graduated']) &&  $this->data['Report']['exclude_graduated'] == 1 ) { ?>
							<!-- <th class="center">Graduated</th> -->
							<?php
						} ?>

						<!-- Excel Separator for the report and for Import Columns -->
						<th class="center"> XXX </th>

						<th class="center">Username</th>
						<th class="center">First name</th>
						<th class="center">Last name</th>
						<th class="center">Display name</th>
						<th class="center">Job title</th>
						<th class="center">Department</th>
						<th class="center">Office number</th>
						<th class="center">Office phone</th>
						<th class="center">Mobile phone</th>
						<th class="center">Fax</th>
						<th class="center">Alternate email address</th>
						<th class="center">Address</th>
						<th class="center">City</th>
						<th class="center">State or province</th>
						<th class="center">ZIP or postal code</th>
						<th class="center">Country or region</th>

					</tr>
				</thead>
				<tbody>
					<?php
					$count = 0;
					foreach ($list as $ko => $val) {  ?>
						<?php $totalStudents++; ?>
						<tr class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?= $val['id']; ?>">
							<td class="center"><?= ++$count; ?></td>
							<td class="vcenter">Arba Minch University</td>
							<td class="vcenter"><?= $val['studentnumber']; ?></td>
							<td class="vcenter"><?= $val['ProgramType']; ?></td>
							<td class="vcenter"><?= (trim($val['Department'])); ?></td>
							<!-- <td class="vcenter"><?php //echo $val['Curriculum']['specialization_english_degree_nomenclature']; ?></td> -->
							<td class="vcenter"><?= $val['Program']; ?></td>
							<td class="center"><?= $val['YearLevel']; ?></td>
							<td class="center"><?= (strcasecmp($semester, 'I') == 0 ? '1' : (strcasecmp($semester, 'II') == 0 ? '2' : '3')); ?></td>
							<td class="vcenter"><?= (trim($val['first_name'])); ?></td>
							<td class="vcenter"><?= (trim($val['middle_name'])); ?></td>
							<td class="vcenter"><?= (trim($val['last_name'])); ?></td>
							<td class="center"><?php if (strcasecmp(trim($val['gender']), 'male') == 0) { echo 'Male'; $totalMaleStudents++; } else { echo 'Female'; $totalFemaleStudents++; } ?></td>
							<td class="vcenter"><?= (str_replace('/', '.', strtolower(trim($val['studentnumber']))) . INSTITUTIONAL_EMAIL_SUFFIX); ?></td>
							<td class="vcenter"><?= (!empty(trim($val['email'])) ? strtolower(trim($val['email'])) : ''); ?></td>
							<td class="vcenter"><?= (!empty(trim($val['phone_mobile'])) && strlen(str_replace(' ', '', str_replace('-', '', trim($val['phone_mobile'])))) >= 9 ? str_replace(' ', '', str_replace('-', '', trim($val['phone_mobile']))) : ''); ?></td>
							<td class="vcenter"></td>
							<td class="vcenter"></td>

							<?php
							if (isset($val['graduated']) && !empty($val['graduated']) && $val['graduated'] == 1) {
								$graduatedStudentsIncluded++;
							}

							if (isset($this->data['Report']['exclude_graduated']) &&  $this->data['Report']['exclude_graduated'] == 1 ) { ?>
								<!-- <td class="center">
									<?php //echo (isset($val['graduated']) && !empty($val['graduated']) && $val['graduated'] == 1 ? 'Yes': 'No'); ?>
								</td> -->
								<?php
							} ?>

							<!-- Excel Separator for the report and for Import Columns -->
							<td class="center"> XXX </td>

							<td class="vcenter"><?= (str_replace('/', '.', strtolower(trim($val['studentnumber']))) . INSTITUTIONAL_EMAIL_SUFFIX); ?></td>
							<td class="vcenter"><?= (trim($val['first_name'])); ?></td>
							<td class="vcenter"><?= (trim($val['middle_name'])); ?></td>
							<td class="vcenter"><?= (trim($val['first_name']) . ' ' . trim($val['middle_name']) . ' ' . trim($val['last_name'])); ?></td>
							<td class="vcenter"><?= $val['Program'] . '(' . $val['ProgramType'] . ')'; ?></td>
							<td class="vcenter"><?= (trim($val['Department'])); ?></td>
							<td class="vcenter"></td>
							<td class="vcenter"></td>
							<td class="vcenter"><?php //echo (!empty(trim($val['phone_mobile'])) && strlen(str_replace(' ', '', str_replace('-', '', trim($val['phone_mobile'])))) >= 10 ? str_replace(' ', '', str_replace('-', '', trim($val['phone_mobile']))) : ''); ?></td>
							<td class="vcenter"></td>
							<td class="vcenter"><?php //echo (!empty(trim($val['email'])) ? strtolower(trim($val['email'])) : ''); ?></td>
							
							<?php
							if (isset($this->data['Report']['freshman']) &&  $this->data['Report']['freshman'] == 1 ) { ?>
								<td class="vcenter"><?= (trim($val['Campus'])); ?></td>
								<?php
							} else { ?>
								<td class="vcenter"><?= (trim($val['Campus'])); ?></td>
								<?php
							} ?>

							<td class="center">Arba Minch</td>
							<td class="vcenter">South Ethiopia Regional State</td>
							<td class="center">21</td>
							<td class="center">Ethiopia</td>

						</tr>
						<?php
					} ?>
				</tbody>
			</table>
		</div>
		<?php
	} ?>
	<br />
	<span class="text-black fs14">
		<hr />
		<strong>Stats for selected Student List: </strong><br />
		Total: <?= ($totalStudents) ?> <br />
		Male: <?= ($totalMaleStudents) . ($totalMaleStudents != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($totalMaleStudents / $totalStudents) * 100), 2) . '%)') : ''); ?><br />
		Female: <?= ($totalFemaleStudents) . ($totalFemaleStudents != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($totalFemaleStudents / $totalStudents) * 100), 2) . '%)') : ''); ?><br />
		Graduated Students included: <?= ($graduatedStudentsIncluded) . ($graduatedStudentsIncluded != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($graduatedStudentsIncluded / $totalStudents) * 100), 2) . '%)') : ''); ?>
		<hr />
	</span>
	<?php
	
} ?>