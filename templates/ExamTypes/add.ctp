<?php ?>
<script type="text/javascript">
	//Sub Cat Combo 1
	$(document).ready(function () {
		$(".AYS").change(function () {
			//serialize form data
			$("#flashMessage").remove();
			var ay = $("#AcadamicYear").val();
			var sem = $("#Semester").val();

			$("#PublishedCourse").empty();
			$("#AcadamicYear").attr('disabled', true);
			$("#PublishedCourse").attr('disabled', true);
			$("#Semester").attr('disabled', true);
			$("#ExamSetupDiv").empty();
			$("#ExamSetupDiv").append('<p>Loading ...</p>');
			//get form action
			var formUrl = '/course_instructor_assignments/get_assigned_courses_of_instructor_by_section_for_combo/' + ay + '/' + sem;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: ay,
				success: function (data, textStatus, xhr) {
					$("#PublishedCourse").empty();
					$("#PublishedCourse").append(data);
					//Items list
					var pc = $("#PublishedCourse").val();
					//get form action
					var formUrl = '/examTypes/get_exam_type_entry_form/' + pc;
					$.ajax({
						type: 'get',
						url: formUrl,
						data: pc,
						success: function (data, textStatus, xhr) {
							$("#AcadamicYear").attr('disabled', false);
							$("#PublishedCourse").attr('disabled', false);
							$("#Semester").attr('disabled', false);
							$("#ExamSetupDiv").empty();
							$("#ExamSetupDiv").append(data);
						},
						error: function (xhr, textStatus, error) {
							alert(textStatus);
						}
					});
					//End of items list
				},
				error: function (xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;
		});

		//Exam setup retrival
		$("#PublishedCourse").change(function () {
			//serialize form data
			var pc = $("#PublishedCourse").val();
			$("#ExamSetupDiv").empty();
			$("#ExamSetupDiv").append('<p>Loading ...</p>');
			//get form action
			var formUrl = '/examTypes/get_exam_type_entry_form/' + pc;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: pc,
				success: function (data, textStatus, xhr) {
					$("#ExamSetupDiv").empty();
					$("#ExamSetupDiv").append(data);
				},
				error: function (xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;
		});
	});
</script>
<script language="javascript">
	var totalRow = <?php if (!empty($this->request->data)) {
		echo (count($this->request->data['ExamType']));
	} else if (!empty($exam_types)) {
		echo (count($exam_types));
	} else {
		echo 2; 
	} ?>;

	function updateSequence(tableID) {
		var s_count = 1;
		for (i = 1; i < document.getElementById(tableID).rows.length; i++) {
			document.getElementById(tableID).rows[i].cells[0].childNodes[0].data = s_count++;
		}
	}

	function addRow(tableID, model, no_of_fields, all_fields) {
		var elementArray = all_fields.split(',');
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		totalRow++;
		row.id = model + '_' + totalRow;
		var cell0 = row.insertCell(0);
		cell0.innerHTML = rowCount;
		cell0.classList.add("center");

		//construct the other cells
		for (var j = 1; j <= no_of_fields; j++) {
			var cell = row.insertCell(j);
			if (elementArray[j - 1] == 'exam_name') {
				var element = document.createElement("input");
				//element.size = "4";
				element.type = "text";
				cell.classList.add("vcenter");
			} else if (elementArray[j - 1] == 'percent') {
				cell.classList.add("center");
				var element = document.createElement("input");
				element.style.width = "75px";
				element.type = "number";
				//element.maxLength = 5;
			} else if (elementArray[j - 1] == 'order') {
				cell.classList.add("center");
				var element = document.createElement("input");
				element.style.width = "75px";
				element.type = "number";
				//element.maxLength = 2;
			} else if (elementArray[j - 1] == 'mandatory') {
				cell.classList.add("center");
				var element = document.createElement("input");
				element.type = "checkbox";
				element.value = "1";
			} else if (elementArray[j - 1] == 'edit') {
				cell.classList.add("center");
				var element = document.createElement("a");
				element.innerText = "Delete";
				element.textContent = "Delete";
				element.setAttribute('href', 'javascript:deleteSpecificRow(\'' + model + '_' + totalRow + '\')');
			}
			element.name = "data[" + model + "][" + rowCount + "][" + elementArray[j - 1] + "]";
			cell.appendChild(element);
		}
		updateSequence(tableID);
	}

	function deleteRow(tableID) {
		try {
			var table = document.getElementById(tableID);
			var rowCount = table.rows.length;
			if (rowCount > 2) {
				table.deleteRow(rowCount - 1);
				updateSequence(tableID);
			} else {
				alert('No more rows to delete');
			}
		} catch (e) {
			alert(e);
		}
	}

	function deleteSpecificRow(id) {
		try {
			var row = document.getElementById(id);
			//var table = row.parentElement;
			var table = row.parentNode;
			if (table.rows.length > 1) {
				row.parentNode.removeChild(row);
				updateSequence('exam_setup');
				//row.parentElement.removeChild(row);
			} else {
				alert('There must be at least one exam type.');
			}
		} catch (e) {
			alert(e);
		}
	}
</script>

<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Course Exam Setup Management'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div class="examTypes form">
					<?= $this->Form->create('ExamType'); ?>
					<div style="margin-top: -30px;">
						<hr>
                    	<fieldset style="padding-bottom: 5px;padding-top: 10px;">
                        	<!-- <legend>&nbsp;&nbsp; Search &nbsp;&nbsp;</legend> -->
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('acadamic_year', array('class' => 'AYS', 'id' => 'AcadamicYear', 'label' => 'Acadamic Year: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($selected_acadamic_year) ? $selected_acadamic_year : (isset($defaultacademicyear) ? $defaultacademicyear : '')))); ?>
								</div>
								<div class="large-2 columns">
									<?= $this->Form->input('semester', array('class' => 'AYS', 'id' => 'Semester', 'label' => 'Semester: ', 'style' => 'width:90%;', /* 'empty' => '[ Select ]', */ 'type' => 'select', 'options' => Configure::read('semesters'), 'default' => (isset($selected_semester) ? $selected_semester : ''))); ?>
								</div>
								<div class="large-7 columns">
									<?= $this->Form->input('published_course_id', array('id' => 'PublishedCourse', 'label' => 'Assigned Course: ', 'style' => 'width:95%;', 'type' => 'select', 'empty' => '[ Select Course ]', 'options' => $publishedCourses, 'default' => $published_course_combo_id)); ?>
								</div>
							</div>
						</fieldset>
					</div>
					
					<div id="ExamSetupDiv">
						<?= $this->Form->input('edit', array('type' => 'hidden', 'value' => $edit)); ?>
						<?php
						//if(count($publishedCourses) > 0) {
						if (!empty ($published_course_combo_id)) {

							$input_disable = ($grade_submitted ? "disabled" : false);

							if (!$grade_submitted) { ?>
								<h6 class="fs14 text-gray">Please enter all the exam types (assesments) for the course you selected with its weight in the given field below.</h6>
								<hr>
								<?php
							} else { ?>
								<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Exam grade is submitted for the selected course and changes on the exam setup is disabled.</div>
								<?php
							} ?>

							<div style="overflow-x:auto;">
                                <table cellpadding="0" cellspacing="0" id="exam_setup" class="table">
									<thead>
										<tr>
											<th style="width:5%" class="center">#</th>
											<th style="width:25%" class="vcenter">Exam Type</th>
											<th style="width:20%" class=<?= ((isset($view_only) && !$view_only) || empty($exam_types) ? "vcenter": "center"); ?>>Percent</th>
											<th style="width:20%" class=<?= ((isset($view_only) && !$view_only) || empty($exam_types) ? "vcenter": "center"); ?>>Order</th>
											<th style="width:10%" class=<?= ((isset($view_only) && !$view_only) || empty($exam_types) ? "vcenter": "center"); ?>>Mandatory</th>
											<th style="width:20%" class=<?= ((isset($view_only) && !$view_only) || empty($exam_types) ? "vcenter": "center"); ?>>&nbsp;</th>
										</tr>
									</thead>
									<tbody>
										<?php
										if (empty($this->request->data)) {
											if (empty($exam_types)) { ?>
												<tr id="ExamType_1">
													<td class="center">1</td>
													<td class="vcenter"><?= $this->Form->input('ExamType.1.exam_name', array('label' => false, 'disabled' => $input_disable)); ?></td>
													<td class="center"><?= $this->Form->input('ExamType.1.percent', array('maxlength' => '5', 'label' => false, 'style' => 'width:75px')); ?></td>
													<td class="center"><?= $this->Form->input('ExamType.1.order', array('maxlength' => '2', 'type' => 'number', 'label' => false, 'style' => 'width:75px')); ?></td>
													<td class="center"><?= $this->Form->input('ExamType.1.mandatory', array('value' => 1, 'label' => false)); ?></td>
													<td class="center"><!-- <a href="javascript:deleteSpecificRow('ExamType_1')">Delete</a> --></td>
												</tr>
												<?php
											} else {
												$count = 0;
												foreach ($exam_types as $key => $exam_type) {
													if (!$grade_submitted) { ?>
														<tr id="ExamType_<?= ++$count; ?>">
															<td class="center"><?= $count; ?></td>
															<td class="vcenter">
																<?= $this->Form->input('ExamType.' . $count . '.id', array('type' => 'hidden', 'value' => $exam_type['ExamType']['id'])); ?>
																<?= $this->Form->input('ExamType.' . $count . '.exam_name', array('value' => $exam_type['ExamType']['exam_name'], 'label' => false, 'disabled' => $input_disable)); ?>
															</td>
															<td class="center"><?= $this->Form->input('ExamType.' . $count . '.percent', array('maxlength' => '5', 'value' => $exam_type['ExamType']['percent'], 'label' => false, 'style' => 'width:75px', 'disabled' => $input_disable)); ?></td>
															<td class="center"><?= $this->Form->input('ExamType.' . $count . '.order', array('maxlength' => '2', 'type' => 'number', 'value' => ($exam_type['ExamType']['order'] != 0 ? $exam_type['ExamType']['order'] : ''), 'label' => false, 'style' => 'width:75px', 'disabled' => $input_disable)); ?></td>
															<td class="center">
																<?php
																$coptions = array();
																$coptions['value'] = 1;
																$coptions['label'] = false;
																$coptions['disabled'] = $input_disable;

																if ($exam_type['ExamType']['mandatory'] == 1) {
																	$coptions['checked'] = 'checked';
																}

																echo '<div style="margin-left: 50%;">' . $this->Form->input('ExamType.' . $count . '.mandatory', $coptions) . '</div>'; ?>
															</td>
															<td class="center">
																<?php 
																if (!$grade_submitted) { ?>
																	<a href="javascript:deleteSpecificRow('ExamType_<?= $count; ?>')">Delete</a>
																	<?php 
																} ?>
															</td>
														</tr>
														<?php
													} else { ?>
														<tr>
															<td class="center"><?= ++$count; ?></td>
															<td class="vcenter"><?= $exam_type['ExamType']['exam_name']; ?></td>
															<td class="center"><?= $exam_type['ExamType']['percent'] . '%'; ?></td>
															<td class="center"><?= $exam_type['ExamType']['order']; ?></td>
															<td class="center"><?= ($exam_type['ExamType']['mandatory'] == 1 ? 'Yes' : 'No'); ?></td>
															<td class="center">&nbsp;</td>
														</tr>
														<?php
													}
												}
												$count++;
											}
										} else {
											$count = 1;
											foreach ($this->request->data['ExamType'] as $key => $examType) {
												if (is_array($examType)) { ?>
													<tr id="ExamType_<?= $count; ?>">
														<td class="center"><?= ($count); ?></td>
														<td class="vcenter">
															<?= (isset ($examType['id']) ? $this->Form->input('ExamType.' . $key . '.id', array('type' => 'hidden')) : ''); ?>
															<?= $this->Form->input('ExamType.' . $key . '.exam_name', array('label' => false)); ?>
														</td>
														<td class="center"><?= $this->Form->input('ExamType.' . $key . '.percent', array('maxlength' => '5', 'label' => false, 'style' => 'width:75px')); ?></td>
														<td class="center"><?= $this->Form->input('ExamType.' . $key . '.order', array('maxlength' => '2', 'type' => 'number', 'label' => false, 'style' => 'width:75px')); ?></td>
														<td class="center">
															<?php
															$coptions = array();
															$coptions['value'] = 1;
															$coptions['label'] = false;

															if ($examType['mandatory'] == 1) {
																$coptions['checked'] = 'checked';
															}

															echo '<div style="margin-left: 50%;">' . $this->Form->input('ExamType.' . $key . '.mandatory', $coptions) . '</div>'; ?>
														</td>
														<td class="center"><a href="javascript:deleteSpecificRow('ExamType_<?= $count++; ?>')">Delete</a></td>
													</tr>
													<?php
												}
											}
										} ?>
									</tbody>
								</table>
							</div>
							<br>
							
							<?php
							if (!$grade_submitted) { ?>
								<input type="button" value="Add Row" onclick="addRow('exam_setup', 'ExamType', 5, '<?= $all_exam_setup_detail; ?>')" />
								<hr>
								<?= $this->Form->submit(__('Submit Exam Setup'), array('div' => false, 'class' => 'tiny radius button bg-blue'));
							} ?>
							<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Important Note: If a student fail to take any of the mandatory exam/s, the system will automatically give NG to the student.</div>
							<?php
						} else if (count($publishedCourses) <= 1) { ?>
							<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Please select academic year and semester to get list of courses you are assigned for.</div>
							<?php
						} else { ?>
							<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Please select a course to get exam setup form.</div>
							<?php
						} ?>
					</div>
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>