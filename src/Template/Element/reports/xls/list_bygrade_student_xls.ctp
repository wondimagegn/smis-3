<?php 
header ("Expires: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/vnd.ms-excel");
header ("Content-Disposition: attachment; filename=".$filename.".xls" );
header ("Content-Description: Exported as XLS" );

if (isset($studentList) && !empty($studentList)) {
	
	$totalStudents = 0;
	$totalMaleStudents = 0;
	$totalFemaleStudents = 0;

	if (!empty($headerLabel)) { ?>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td colspan='5'><hr><?= $headerLabel; ?></td>
			</tr>
		</table>
		<?php
	}

	foreach ($studentList as $gkey => $gvalue) {
		$listgEx = explode('~', $gkey);
		?>
		<br>
		<div style="overflow-x:auto;">
			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<td colspan="5">
							<hr>
							<span style="font-weight: bold;"><?= $listgEx[3]; ?></span><br>
							<span class="text-gray">
								<?= 'Instructor: '. $listgEx[5]; ?><br>
								<?= $listgEx[2]. (!empty($this->request->data['Report']['acadamic_year']) ? ' ('. $this->request->data['Report']['acadamic_year'] . ', '. $listgEx[4] . ' year' . (empty($this->request->data['Report']['semester']) ? '' : (',  ' . ($this->request->data['Report']['semester'] == 'I' ? '1st Semester' : ($this->request->data['Report']['semester'] == 'II' ? '2nd Semester' : ($this->request->data['Report']['semester'] == 'III' ? '3rd Semester' : $this->request->data['Report']['semester'] . ' Semester'))))) . ')' : ''); ?><br>
								<?= (isset($listgEx[0]) && !empty($listgEx[0]) ? $listgEx[0] : ($listgEx[0] == 'Remedial' ? 'Remedial Program' : 'Pre/Freshman')) . '' . (isset($listgEx[1]) && !empty($listgEx[1]) ? ' &nbsp; | &nbsp; ' . $listgEx[1] : ''); ?>
								<?php //echo $this->request->data['Report']['acadamic_year'] . (empty($this->request->data['Report']['semester']) ? '' : (' &nbsp; | &nbsp; ' . ($this->request->data['Report']['semester'] == 'I' ? '1st Semester' : ($this->request->data['Report']['semester'] == 'II' ? '2nd Semester' : ($this->request->data['Report']['semester'] == 'III' ? '3rd Semester' : $this->request->data['Report']['semester'] . ' Semester'))))); ?><!-- <br> -->
							</span>
							<hr>
						</td>
					</tr>
					<tr>
						<th style="vertical-align: middle; text-align: center;" style="width: 3%;">#</th>
						<th style="vertical-align: middle; text-align: left;" style="width: 30%;">Full Name</th>
						<th style="vertical-align: middle; text-align: center;" style="width: 10%;">Sex</th>
						<th style="vertical-align: middle; text-align: center;" style="width: 15%;">Student ID</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$count = 0;
					foreach ($gvalue['studentList'] as $dkey => $dvalue) {
						$totalStudents++;
						$count++; ?>
						<tr>
							<td style="vertical-align: middle; text-align: center;"><?= $count; ?> </td>
							<td style="vertical-align: middle; text-align: left;"><?= $dvalue['first_name'] . ' ' . $dvalue['middle_name'] . ' ' . $dvalue['last_name']; ?></td>
							<td style="vertical-align: middle; text-align: center;"><?php if (strcasecmp(trim($dvalue['gender']), 'male') == 0) { echo 'M'; $totalMaleStudents++; } else { echo 'F'; $totalFemaleStudents++; } ?></td>
							<!-- <td style="vertical-align: middle; text-align: center;"><?php //echo ((strcasecmp(trim($dvalue['gender']), 'male') == 0) ? 'M' : ((strcasecmp(trim($dvalue['gender']), 'female') == 0) ? 'F' : trim($dvalue['gender']))); ?></td> -->
							<td style="vertical-align: middle; text-align: center;"><?= $dvalue['studentnumber']; ?></td>
							<td>&nbsp;</td>
						</tr>
						<?php 
					} ?>
				</tbody>
			</table>
		</div>
		<br>

		<?php
	} ?>

	<hr/>
	<span class="text-black fs14">
		<strong>Stats for the selected Students List with <?= $grade_selected; ?> Grade</strong><br />
		Total: <?= ($totalStudents) ?> <br />
		Male: <?= ($totalMaleStudents) . ($totalMaleStudents != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($totalMaleStudents / $totalStudents) * 100), 2) . '%)') : ''); ?><br />
		Female: <?= ($totalFemaleStudents) . ($totalFemaleStudents != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($totalFemaleStudents / $totalStudents) * 100), 2) . '%)') : ''); ?><br />
	</span>
	<hr />
	<?php
} ?>