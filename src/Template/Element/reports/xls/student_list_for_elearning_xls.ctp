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
if (isset($studentListForElearning) && !empty($studentListForElearning)) { 

	$totalStudents = 0;
	$totalMaleStudents = 0;
	$totalFemaleStudents = 0;
	$graduatedStudentsIncluded = 0;

	foreach ($studentListForElearning as $programD => $list) {
		$headerExplode = explode('~', $programD);  ?>
		<!-- <br>
		<h6 class="fs16"><span class="fs16 text-gray">College:  <?php //echo $headerExplode[0]; ?></span></h6>
		<br> -->

		<div style="overflow-x:auto;">
			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th class="vcenter">username</th>
						<th class="vcenter">password</th>
						<th class="center">firstname</th>
						<th class="center">lastname</th>
						<th class="center">middlename</th>
						<th class="center">department</th>
						<th class="center">idnumber</th>
						<th class="center">email</th>
						<th class="center">institution</th>
						<th class="center">address</th>
						<th class="center">description</th>
						<?php
						if (isset($this->data['Report']['exclude_graduated']) &&  $this->data['Report']['exclude_graduated'] == 1 ) { ?>
							<!-- <th class="center">Graduated</th> -->
							<?php
						} ?>
						
					</tr>
				</thead>
				<tbody>
					<?php
					//$count = 0;
					foreach ($list as $ko => $val) {  ?>
						<?php $totalStudents++; ?>
						<tr class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?= $val['id']; ?>">
							<?php // ++$count; ?>
							<td class="vcenter"><?= (str_replace('/', '.', strtolower(trim($val['studentnumber'])))); ?></td>
							<td class="vcenter"><?= (trim($val['first_name']) . '@'. date('Y')); ?></td>
							<td class="vcenter"><?= (trim($val['first_name'])); ?></td>
							<td class="vcenter"><?= (trim($val['middle_name'])); ?></td>
							<td class="vcenter"><?= (trim($val['last_name'])); ?></td>
							<td class="vcenter"><?= (trim($val['Department'])); ?></td>
							<td class="vcenter"><?= (trim($val['studentnumber'])); ?></td>
							<td class="vcenter"><?= (!empty(trim($val['email'])) ? strtolower(trim($val['email'])) : (str_replace('/', '.', strtolower(trim($val['studentnumber']))) . INSTITUTIONAL_EMAIL_SUFFIX)); ?></td>
							<td class="vcenter"><?= (trim($val['College'])); ?></td>
							<td class="vcenter"><?= (trim($val['Campus'])); ?></td>
							<td class="vcenter"><?= $val['academicyear'] . ': ' . $val['Program'] . ' - ' . $val['ProgramType']; ?></td>

							<?php
							if (isset($val['graduated']) && !empty($val['graduated']) && $val['graduated'] == 1) {
								$graduatedStudentsIncluded++;
							}

							if (strcasecmp(trim($val['gender']), 'male') == 0) { 
								$totalMaleStudents++; 
							} else { 
								$totalFemaleStudents++; 
							}

							if (isset($this->data['Report']['exclude_graduated']) &&  $this->data['Report']['exclude_graduated'] == 1 ) { ?>
								<!-- <td class="center">
									<?php //echo (isset($val['graduated']) && !empty($val['graduated']) && $val['graduated'] == 1 ? 'Yes': 'No'); ?>
								</td> -->
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