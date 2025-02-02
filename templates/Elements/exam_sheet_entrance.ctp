<?php ?>
<script type="text/javascript">
	
	var col = 1;
	var rows = Number(<?= count($students); ?>);

	var current = 1;
	var next;

	document.onkeydown = moveByKeyPress;

	function moveByKeyPress(e) {

		if (!e) {
			e = window.event;
		}

		var currentId = $(e.target).attr("id");
		var currentRow = currentId.split('_', 3);
		var nextRowNumber = Number(currentRow[1]) + 1;
		var currentCol = Number(currentRow[2]);
		var liveValue = $(e.target).val();

		var key;

		(e.keyCode) ? key = e.keyCode : key = e.which;

		try {
			if (key == 37 | key == 38 | key == 39 | key == 40 | key == 9) {
				if (liveValue != "" && isNaN(liveValue)) {
					alert('Please enter a valid result.');
					$('#' + $(e.target).attr("id")).focus();
				} else if (liveValue != "" && parseFloat(liveValue) > parseFloat($(e.target).attr("data-percent"))) {
					alert('The maximum value of "' + $(e.target).attr("data-type") + '" exam result is ' + $(e.target).attr("data-percent") + '.');
					$('#' + $(e.target).attr("id")).focus();
					//$('#' + $(e.target).attr("id")).select();
					// $('#' + currentRowId).focus();
					// $('#' + currentRowId).focus();
				} else if (liveValue != "" && parseFloat(liveValue) < 0) {
					alert('The minimum value of "' + $(e.target).attr("data-type") + '" exam result is 0.');
					$('#' + $(e.target).attr("id")).focus();
					$('#' + $(e.target).attr("id")).select();
				} else {
					switch (key) {
						case 37: //left
							next = currentCol - 1;
							if (next > col) {
								var nextRowNumber = Number(currentRow[1]) + 1;
								var newRowId = 'result_' + nextRowNumber + '_1';
								$('#' + newRowId).focus();
							} else {
								var currentRowId = 'result_' + currentRow[1] + '_' + next;
								$('#' + currentRowId).focus();
							}
							break;
						case 38: //up
							// next = nextRowNumber-1;
							var previousRowNumber = Number(currentRow[1]) - 1;
							var newRowId = 'result_' + previousRowNumber + '_' + currentCol;
							if ($('#' + newRowId).length != 0) {
								$('#' + newRowId).focus();
							} else {
								$('#' + currentId).focus();
							}
							break;
						case 39: //right
						{
							next = (currentCol) + 1;
							if (next > col) {
								var nextRowNumber = Number(currentRow[1]) + 1;
								var newRowId = 'result_' + nextRowNumber + '_1';
								$('#' + newRowId).focus();
							} else {
								var currentRowId = 'result_' + currentRow[1] + '_' + next;
								$('#' + currentRowId).focus();
							}
						}
						break;
						case 40: //down
							var nextRowNumber = Number(currentRow[1]) + 1;
							var newRowId = 'result_' + nextRowNumber + '_' + currentCol;
							if ($('#' + newRowId).length != 0) {
								$('#' + newRowId).focus();
							} else {
								$('#' + currentId).focus();
							}
						break;
						case 9: //tab
							var nextRowNumber = Number(currentRow[1]) + 1;
							var newRowId = 'result_' + nextRowNumber + '_' + currentCol;
							if ($('#' + newRowId).length != 0) {
								$('#' + newRowId).focus();
							} else {
								$('#' + currentId).focus();
							}
						break;
					}
				}
			} else {
				return;
			}
		} catch (exception) {

		}
	}
</script>

<hr>
<blockquote>
	<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
	<p style="text-align:justify;"><span class="fs16"> The entrance exam results for the students <span class="text-red"> will be automatically saved upon entering value in the input box. </span>  The result will be readonly if the student auto placement process started by the registrar.</span></p> 
</blockquote>
<hr>

<div style="overflow-x:auto;">
	<table cellpadding="0" cellspacing="0" class="table" onkeypress="javascript:moveByKeyPress();">
		<thead>
			<tr>
				<td style="width:3%" class="center">#</td>
				<td style="width:27%" class="vcenter">Student Name</td>
				<td style="width:20%" class="center">Student ID</td>
				<td style="width:10%" class="vcenter">Result</td>
				<td style="width:40%" class="center">&nbsp;</td>
			</tr>
		</thead>
		<tbody>
			<?php
			//Building every student exam result entry
			$st_count = 1;
			$et_count = 0;
			$total_student_count = count($students);

			foreach ($students as $key => $student) { 
				$et_count++; ?>
				<tr>
					<td class="center"><?= $st_count; ?></td>
					<td class="vcenter"><?= $student['Student']['first_name'] . ' ' . $student['Student']['middle_name'] . ' ' . $student['Student']['last_name']; ?></td>
					<td class="center"><?= $student['Student']['studentnumber']; ?></td>
					<td class="center" style="line-height: 1;">
						<?php
						//If it is makeup exam entry
						if ($student['PlacementStatus']) {
							//It is exam grade view only and there is nothing to do for now
							echo $student['EntranceResult']['result'];
						} else {
							echo '<br>';
							//$input_options = array('type' => 'text', 'label' => false, 'maxlength' => '5', 'style' => 'width:50px', 'onBlur' => 'updateExamTotal(this, ' . $st_count . ')', 'id' => 'result_' . $st_count . '_1');
							//$input_options = array('type' => 'number', 'label' => false,  'style' => 'width:70px', 'onBlur' => 'updateExamTotal(this, ' . $st_count . ')', 'id' => 'result_' . $st_count . '_1', 'min' => 0, 'max' => ENTRANCEMAXIMUM, 'step' => 'any');

							$input_options = array(
								'type' => 'number', 'label' => false, 'style' => 'width:70px', /* 'id' => 'result_' . $st_count . '_' . $et_count, */ 'id' => 'result_' . $st_count . '_1', 'min' => 0, 'max' => ENTRANCEMAXIMUM, 'step' => 'any',
								'onBlur' => 'updateExamTotal(this, ' . $st_count . ', 1, ' . ENTRANCEMAXIMUM . ', \'' . 'entrance_exam' . '\', true)',
								'data-type' => 'Entrance Exam', 'data-percent' => ENTRANCEMAXIMUM
							);

							if (isset($student['EntranceResult']) && !empty($student['EntranceResult'])) {
								echo $this->Form->input('PlacementEntranceExamResultEntry.' . $st_count . '.id', array('type' => 'hidden', 'value' => $student['EntranceResult']['id']));
								$input_options['value'] = $student['EntranceResult']['result'];
								//$input_options['tabindex'] = $st_count;
								$input_options['tabindex'] = (($total_student_count * ($et_count - 1)) + $st_count);
								echo $this->Form->input('PlacementEntranceExamResultEntry.' . $st_count . '.result', $input_options);
							} else {
								echo $this->Form->input('PlacementEntranceExamResultEntry.' . $st_count . '.student_id', array('type' => 'hidden', 'value' => $student['Student']['id']));
								echo $this->Form->input('PlacementEntranceExamResultEntry.' . $st_count . '.accepted_student_id', array('type' => 'hidden', 'value' => $student['Student']['accepted_student_id']));
								echo $this->Form->input('PlacementEntranceExamResultEntry.' . $st_count . '.placement_round_participant_id', array('type' => 'hidden', 'value' => $student['Student']['placement_round_participant_id']));
								echo $this->Form->input('PlacementEntranceExamResultEntry.' . $st_count . '.result', $input_options);
							}

						}
						$st_count++; ?>
					</td>
					<td>&nbsp;</td>
				</tr>
				<?php
			} ?>
		</tbody>
	</table>
</div>