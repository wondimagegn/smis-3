<?php
$formatOptions = array('places' => 2,  'before' => false, 'decimals' => '.',  'thousands' => ',' );

if (!empty($grade_scale) && !isset($grade_scale['error'])) { ?>
	<h6 class="fs13"><span class="text-gray">Grade Type: </span><i class="text-gray"><?= $grade_scale['GradeType']['type']; ?></i></h6>
	<h6 class="fs13"><span class="text-gray">Grade Scale: </span><i class="text-gray"><?= $grade_scale['GradeScale']['name'] . ' (' . $grade_scale['scale_by'] . ' level)'; ?></i></h6>
	<h6 class="fs13"><span class="text-gray">Course: </span><span class="text-black"><?= $grade_scale['Course']['course_code_title']; ?></span></h6>
	<div style="overflow-x:auto;">
		<table cellpadding="0" cellspacing="0" class="table">
			<tr>
				<th style="text-align: center;width:10%">Grade</th>
				<th style="text-align: center;width:20%">Min</th>
				<th style="text-align: center;width:20%">Max</th>
				<th style="text-align: center;width:20%">Grade Point</th>
				<th style="text-align: center;width:15%">Pass Grade</th>
				<th style="text-align: center;width:15%">Repeatable</th>
			</tr>
			<?php
			foreach($grade_scale['GradeScaleDetail'] as $key => $grade_scale_detail) { ?>
				<tr>
					<td style="text-align: center;"><?= $grade_scale_detail['grade']; ?></td>
					<td style="text-align: center;"><?= $this->Number->format($grade_scale_detail['minimum_result'], $formatOptions); ?></td>
					<td style="text-align: center;"><?= $this->Number->format($grade_scale_detail['maximum_result'], $formatOptions); ?></td>
					<td style="text-align: center;"><?= $grade_scale_detail['point_value']; ?></td>
					<td style="text-align: center;"><?= ($grade_scale_detail['pass_grade'] == 1 ? '<span class="accepted">Yes</span>' : '<span class="rejected">No</span>'); ?></td>
					<td style="text-align: center;"><?= ($grade_scale_detail['repeatable'] == 1 ? '<span class="accepted">Yes</span>' : '<span>No</span>'); ?></td>
				</tr>
				<?php
			} ?>
		</table>
	</div>
	<?php
} else if (isset($grade_scale['error'])) { ?>
	<div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;"><span style='margin-right: 15px;'></span><?= $grade_scale['error']; ?></div>
	<?php
} else { ?>
	<div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;"><span style='margin-right: 15px;'></span>Grade scale for the selected course is not found in the system.</div>
	<?php
}
