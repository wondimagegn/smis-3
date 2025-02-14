<?php
if (isset($studentList) && !empty($studentList)) { ?>
	<!-- <h5><?php //echo $headerLabel; ?></h5> -->
	<?php

	$totalStudents = 0;
	$totalMaleStudents = 0;
	$totalFemaleStudents = 0;

	foreach ($studentList as $gkey => $gvalue) {
		$listgEx = explode('~', $gkey);
		/* echo '<p class="fs16">';
		echo "<strong>Program: </strong>" . $listgEx[0] . "<br/>";
		echo "<strong>ProgramType: </strong>" . $listgEx[1] . "<br/>";
		echo "<strong>Section:</strong>" . $listgEx[2] . "<br/>";
		echo "<strong>Course:</strong>" . $listgEx[3] . "<br/>";
		echo '</p>' ;*/
		?>
		<br>
		<div style="overflow-x:auto;">
			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<td colspan="5" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
							<span style="font-size:15px;font-weight:bold;"><?= $listgEx[3]; ?></span><br>
							<span class="text-gray" style="font-size: 13px; font-weight: bold">
								<?= 'Instructor: '. $listgEx[5]; ?><br>
								<?= $listgEx[2]. (!empty($this->request->data['Report']['acadamic_year']) ? ' ('. $this->request->data['Report']['acadamic_year'] . ', '. $listgEx[4] . ' year' . (empty($this->request->data['Report']['semester']) ? '' : (',  ' . ($this->request->data['Report']['semester'] == 'I' ? '1st Semester' : ($this->request->data['Report']['semester'] == 'II' ? '2nd Semester' : ($this->request->data['Report']['semester'] == 'III' ? '3rd Semester' : $this->request->data['Report']['semester'] . ' Semester'))))) . ')' : ''); ?><br>
								<?= (isset($listgEx[0]) && !empty($listgEx[0]) ? $listgEx[0] : ($listgEx[0] == 'Remedial' ? 'Remedial Program' : 'Pre/Freshman')) . '' . (isset($listgEx[1]) && !empty($listgEx[1]) ? ' &nbsp; | &nbsp; ' . $listgEx[1] : ''); ?>
								<?php //echo $this->request->data['Report']['acadamic_year'] . (empty($this->request->data['Report']['semester']) ? '' : (' &nbsp; | &nbsp; ' . ($this->request->data['Report']['semester'] == 'I' ? '1st Semester' : ($this->request->data['Report']['semester'] == 'II' ? '2nd Semester' : ($this->request->data['Report']['semester'] == 'III' ? '3rd Semester' : $this->request->data['Report']['semester'] . ' Semester'))))); ?><!-- <br> -->
							</span>
						</td>
					</tr>
					<tr>
						<th class="center" style="width: 3%;">#</th>
						<th class="vcenter" style="width: 30%;">Full Name</th>
						<th class="center" style="width: 10%;">Sex</th>
						<th class="center" style="width: 15%;">Student ID</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$count = 0;
					foreach ($gvalue['studentList'] as $dkey => $dvalue) {
						$totalStudents++;
						$count++; ?>
						<tr class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?= $dvalue['id']; ?>">
							<td class="center"><?= $count; ?> </td>
							<td class="vcenter"><?= $dvalue['first_name'] . ' ' . $dvalue['middle_name'] . ' ' . $dvalue['last_name']; ?></td>
							<td class="center"><?php if (strcasecmp(trim($dvalue['gender']), 'male') == 0) { echo 'M'; $totalMaleStudents++; } else { echo 'F'; $totalFemaleStudents++; } ?></td>
							<!-- <td class="center"><?php //echo ((strcasecmp(trim($dvalue['gender']), 'male') == 0) ? 'M' : ((strcasecmp(trim($dvalue['gender']), 'female') == 0) ? 'F' : trim($dvalue['gender']))); ?></td> -->
							<td class="center"><?= $dvalue['studentnumber']; ?></td>
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
	<?php
} ?>