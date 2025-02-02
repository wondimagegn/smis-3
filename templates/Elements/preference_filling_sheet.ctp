<?php
if	(isset($students) && !empty($students['Student'])) {
	$total_student_count = count($students['Student']);
} else {
	$total_student_count = 0;
} ?>
<!-- 
	We need a Js class or some sort of programming to disable already selected options from the dropdown menu, 
	it is not that much important here but we need for the student side  
-->
<script>
	var col = Number(<?= count($students['ParticipantUnit']); ?>);
	var rows = Number(<?= $total_student_count; ?>);
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

		(e.keyCode) ? key = e.keyCode: key = e.which;
		try {
			if (key == 37 | key == 38 | key == 39 | key == 40) {
				if (liveValue != "" && isNaN(liveValue)) {
					alert('Please enter a valid result.');
					$('#' + $(e.target).attr("id")).focus();
				} else if (liveValue != "" &&
					parseFloat(liveValue) > parseFloat($(e.target).attr("data-percent"))) {
					alert('The maximum value of "' + $(e.target).attr("data-type") + '" exam result is ' + $(e.target).attr("data-percent") + '.');
					$('#' + $(e.target).attr("id")).focus();
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
							next = (currentCol) + 1;

							if (next > col) {
								var nextRowNumber = Number(currentRow[1]) + 1;
								var newRowId = 'result_' + nextRowNumber + '_1';
								$('#' + newRowId).focus();
							} else {
								var currentRowId = 'result_' + currentRow[1] + '_' + next;
								$('#' + currentRowId).focus();
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
					}
				}
			} else {
				return;
			}
		} catch (exception) {

		}
	}

	function updateExamTotal(obj, row, col, totalParticipant) {
		//	var result = 0;
		var result = $('#result_' + row + '_' + col).val();
		var invalid = true;
		const arr1 = [];

		for (var i = 1; i <= totalParticipant; i++) {
			currentResult = $('#result_' + row + '_' + i).val();
			if (Number.isInteger(currentResult)) {
				arr1.push(currentResult);
			}
		}

		var arr2 = removeDuplicateInArray(arr1);
		if (arr1.length == arr2.length) {
			var autoSaveResult = result;
			$.ajax({
				url: "/PlacementPreferences/autoSaveResult",
				type: 'POST',
				data: $('form').serialize(),
				success: function(data) {}
			});
		} else {
			console.log(arr2);
		}
		return result;
	}

	function removeDuplicateInArray(arr1) {
		var c;
		var len = arr1.length;
		var object = {};
		var result = [];

		for (c = 0; c < len; c++) {
			object[arr1[c]] = 0;
		}

		for (prop in object) {
			result.push(prop);
		}
		return result;
	}
</script>

<?php
if (!isset($grade_view_only)) {
	$grade_view_only = false;
}
//onkeypress="javascript:moveByKeyPress();"
?>



<?php
if (isset($students) && !empty($students['Student'])) { ?>
	<br>
	<blockquote>
		<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
		<p style="text-align:justify;"><ol><li class="fs16 rejected">The preference of the students will be automatically saved upon selection of different preference for each choice.</li>
		<li class="fs16 rejected"> Preference choice will be readonly if the student auto placement process started by the registrar or students has already assigned to their choice before.</li></ol></p> 
	</blockquote>
	<br>

	<div style="overflow-x:auto;">
		<table cellpadding="0" cellspacing="0" class="table">
			<thead>
				<tr>
					<th style="width:2%" class="center">#</th>
					<th style="width:11%" class="center">Student Name</th>
					<th style="width:5%" class="center">Sex</th>
					<th style="width:8%" class="center">Student ID</th>
					<th style="width:3%" class="center">Prep</th>
					<th style="width:3%" class="center">Fresh</th>
					<th style="width:3%" class="center">CGPA</th>
					<th style="width:3%" class="center">Entrance</th>
					<th style="width:3%" class="center">Females</th>
					<th style="width:3%" class="center">Total</th>

					<?php
					$percent = 10;
					$count = 0;
					$st_count = 0;
					$last_percent = "";
					$grade_width = 0;
					$count_for_percent = 0;

					if (((100 - 28) / ((count($students['ParticipantUnit']) + 1) + $grade_width)) > 10) {
						$last_percent = (100 - 28) - ((count($students['ParticipantUnit']) + 1 + $grade_width) * 10);
					} else {
						$percent = ((100 - 28) / (count($students['ParticipantUnit']) + 1 + $grade_width));
					}

					foreach ($students['ParticipantUnit'] as $key => $name) {
						$count_for_percent++; ?>
						<th style="width:<?= ($count_for_percent == (count($students['ParticipantUnit']) + 1) ? $last_percent + $percent : $percent); ?>" class="center"><?= $name; ?></th>
						<?php
					} ?>
				</tr>
			</thead>
			<tbody>
				<?php
				if (!isset($total_student_count)) {
					$total_student_count = count($students['Student']);
				}

				foreach ($students['Student'] as $key => $student) {
					$total_100 = "";
					$total_placement_wight = 0;
					$st_count++; 
					
					if (isset($student['Student']['AcceptedStudent']['freshman_result']) && is_numeric($student['Student']['AcceptedStudent']['freshman_result']) && ((int) $student['Student']['AcceptedStudent']['freshman_result'])) {
						$total_placement_wight += $student['Student']['AcceptedStudent']['freshman_result'];
					}

					if (isset($student['Student']['AcceptedStudent']['EHEECE_total_results']) && is_numeric($student['Student']['AcceptedStudent']['EHEECE_total_results']) && ((int) $student['Student']['AcceptedStudent']['EHEECE_total_results'])) {
						$total_placement_wight += $student['Student']['AcceptedStudent']['EHEECE_total_results'];
					}

					if (isset($student['Student']['AcceptedStudent']['entrance_result']) && is_numeric($student['Student']['AcceptedStudent']['entrance_result']) && ((int) $student['Student']['AcceptedStudent']['entrance_result'])) {
						$total_placement_wight += $student['Student']['AcceptedStudent']['EHEECE_total_results'];
					}

					if (strcasecmp(trim($student['Student']['gender']), 'female') == 0) {
						$total_placement_wight += DEFAULT_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT;
					} ?>

					<tr>
						<td class="center"><?= $st_count; ?></td>
						<td class="venter"><?= $student['Student']['first_name'] . ' ' . $student['Student']['middle_name'] . ' ' . $student['Student']['last_name']; ?></td>
						<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' : 'F'); ?></td>
						<td class="center"><?= $student['Student']['studentnumber']; ?></td>
						<td class="center"><?= $this->Number->format($student['Student']['AcceptedStudent']['EHEECE_total_results'], array('places' => DECIMAL_PLACES_PLACEMENT,  'before' => false, 'decimals' => '.',  'thousands' => ',' )); ?></td>
						<td class="center"><?= $this->Number->format($student['Student']['AcceptedStudent']['freshman_result'], array('places' => DECIMAL_PLACES_PLACEMENT,  'before' => false, 'decimals' => '.',  'thousands' => ',' )); ?></td>
						<td class="center"><?= (isset($student['Status']['cgpa']) ? $student['Status']['cgpa'] : '---'); ?></td>
						<td class="center"><?= (isset($student['Student']['AcceptedStudent']['entrance_result']) ? $student['Student']['AcceptedStudent']['entrance_result'] : '---'); ?></td>
						<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? DEFAULT_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT : 0 ); ?></td>
						<td class="center"><?= $this->Number->format($total_placement_wight, array('places' => DECIMAL_PLACES_PLACEMENT,  'before' => false, 'decimals' => '.',  'thousands' => ',' )); ?></td>
						<?php

						$et_count = 0;
						//Each mark entry for each exam type (foreach loop)
						foreach ($students['ParticipantUnit'] as $key => $exam_type) {
							$et_count++; ?>
							<td class="center">
								<?php
								$id = "";
								$value = "";
								//Searching for the exam result from the databse returned value
								if (isset($student['PlacementPreference']) && !empty($student['PlacementPreference'])) {
									foreach ($student['PlacementPreference'] as $pkey => $examResult) {
										if ($examResult['PlacementPreference']['placement_round_participant_id'] == $key) {
											$id = $examResult['PlacementPreference']['id'];
											$value = $examResult['PlacementPreference']['preference_order'];
											$total_100 += $value;
											break;
										}
									}
								}
								//if save exam result button is clicked to add each exam result to get result sum
								$i = (($st_count - 1) * count($students['ParticipantUnit'])) + 1;

								//It is if it is on exam result edit mode
								//$student['PlacementStatus'] = 1; //uncomment this line for making the drop down to be just a value for excel export and to prevent roles other students to fill preference on behalf of the students. adding a global variable or in a field placement_deadlines labeled days_after_deadline_registrar

								if ($student['PlacementStatus'] == 0 && !$student['DeadlinePassed']) {
									$prOr = count($students['ParticipantUnitPreferenceOrder']);

									$input_options = array(
										'type' => 'select', 'options' => $students['ParticipantUnitPreferenceOrder'],
										'label' => false, 'empty' => '---', 'maxlength' => '5', 'style' => 'width:50px',
										'id' => 'result_' . $st_count . '_' . $et_count,
										'onchange' => 'updateExamTotal(this, ' . $st_count . ',' . $et_count . ',' . $prOr . ')'
									);
									//echo $this->Form->input('PlacementPreference.' . $count . '.accepted_student_id', array('type' => 'hidden', 'value' => $student['Student']['AcceptedStudent']['id']));

									echo $this->Form->input('PlacementPreference.' . $count . '.accepted_student_id', array('type' => 'hidden', 'value' => $student['Student']['accepted_student_id']));
									echo $this->Form->input('PlacementPreference.' . $count . '.student_id', array('type' => 'hidden', 'value' => $student['Student']['id']));

									if ($id != "") {
										echo $this->Form->input('PlacementPreference.' . $count . '.id', array('type' => 'hidden', 'value' => $id));
										echo $this->Form->input('PlacementPreference.' . $count . '.placement_round_participant_id', array('type' => 'hidden', 'value' => $key));
										$input_options['value'] = $value;
										//$input_options['tabindex'] = (($total_student_count * ($et_count - 1)) + $st_count);
										echo $this->Form->input('PlacementPreference.' . $count . '.preference_order', $input_options);
									} //New exam result entry
									else {
										echo $this->Form->input('PlacementPreference.' . $count . '.placement_round_participant_id', array('type' => 'hidden', 'value' => $key));
										echo $this->Form->input('PlacementPreference.' . $count . '.preference_order', $input_options);
									} //End of entry for one student
								} else {
									echo $value;
								} ?>
							</td>
							<?php
							$count++;
						} //End of each mark entry for each exam type (foreach loop)
						?>
					</tr>
					<?php
				} ?>
			</tbody>
		</table>
	</div>
	<?php
} else if (!empty($students)) { ?>
	<div class="large-12 columns">
		<div class='info-box info-message'><span style='margin-right: 15px;'></span>No student is found with the givem search criteria.</div>
	</div>
	<?php
} ?>