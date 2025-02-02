<script language="javascript">
	var totalRow = <?php 
	if (!empty ($this->request->data)) {
		echo (count($this->request->data['ExamType']));
	} else if (!empty ($exam_types)) {
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
		//construct the other cells
		for (var j = 1; j <= no_of_fields; j++) {
			var cell = row.insertCell(j);
			if (elementArray[j - 1] == 'exam_name') {
				var element = document.createElement("input");
				//element.size = "4";
				element.type = "text";
			} else if (elementArray[j - 1] == 'percent') {
				var element = document.createElement("input");
				element.style.width = "75px";
				element.type = "text";
				element.maxLength = 5;
			} else if (elementArray[j - 1] == 'order') {
				var element = document.createElement("input");
				element.style.width = "75px";
				element.type = "text";
				element.maxLength = 2;
			} else if (elementArray[j - 1] == 'mandatory') {
				var element = document.createElement("input");
				element.type = "checkbox";
				element.value = "1";
			} else if (elementArray[j - 1] == 'edit') {
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
			if (table.rows.length > 2) {
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
<script type="text/javascript">
	function toggleViewFullId(id) {
		if ($('#' + id).css("display") == 'none') {
			$('#' + id + 'Img').attr("src", '/img/minus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append('Hide Filter');
		} else {
			$('#' + id + 'Img').attr("src", '/img/plus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append('Display Filter');
		}
		$('#' + id).toggle("slow");
	}

	$(document).ready(function () {
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

<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Course Exam Setup Management'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div class="examTypes index">

					<?= $this->Form->create('ExamType'); ?>

					<div style="margin-top: -30px;">
						<hr>
						<div onclick="toggleViewFullId('ListPublishedCourse')">
							<?php
							if (!empty ($publishedCourses)) {
								echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
								<?php
							} else {
								echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
								<?php
							} ?>
						</div>
						
						<div id="ListPublishedCourse" style="display:<?= (!empty ($publishedCourses) ? 'none' : 'display'); ?>">
							<fieldset style="padding-bottom: 5px;padding-top: 10px;">
								<!-- <legend>&nbsp;&nbsp; Search &nbsp;&nbsp;</legend> -->
								<div class="row">
									<div class="large-3 columns">
										<?php
										$options = array();
										$options = array('id' => 'AcadamicYear', 'label' => 'Acadamic Year: ', 'class' => 'fs14', 'style' => 'width:90%;', 'type' => 'select', 'options' => $acyear_array_data, 'default' => $defaultacademicyear);
										if (isset ($acadamic_year)) {
											$options['default'] = $acadamic_year;
										}
										echo $this->Form->input('acadamic_year', $options);
										?>
									</div>
									<div class="large-3 columns">
										<?php
										$options = array();
										$options = array('id' => 'Semester', 'class' => 'fs14', 'label' => 'Semester: ', 'style' => 'width:90%;',  'type' => 'select', 'options' => Configure::read('semesters'));
										if (isset ($semester)) {
											$options['default'] = $semester;
										}
										echo $this->Form->input('semester', $options);
										?>
									</div>
									<div class="large-3 columns">
										<?php
										$options = array();
										$options = array('id' => 'Program', 'class' => 'fs14', 'label' => 'Program: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programs);
										if (isset ($program_id)) {
											$options['default'] = $program_id;
										}
										echo $this->Form->input('program_id', $options);
										?>
									</div>
									<div class="large-3 columns">
										<?php
										$options = array();
										$options = array('id' => 'ProgramType', 'class' => 'fs14', 'label' => 'Program Type: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $program_types);
										if (isset ($program_type_id)) {
											$options['default'] = $program_type_id;
										}
										echo $this->Form->input('program_type_id', $options); 
										?>
									</div>
								</div>
								<hr>
								<?= $this->Form->submit(__('List Published Courses'), array('name' => 'listPublishedCourses', 'class' => 'tiny radius button bg-blue',  'div' => false)); ?>
							</fieldset>
						</div>
						<hr>
					</div>
					<?php
					if (!empty ($publishedCourses)) { ?>
						<table class="fs14" cellpadding="0" cellspacing="0" class='table'>
							<tr>
								<td style="width:25%;" class="center">Published Courses</td>
								<td colspan="3">
									<div class="large-10 columns">
									<br>
									<?= $this->Form->input('published_course_id', array('style' => 'width: 90%;',  'class' => 'fs14', 'id' => 'PublishedCourse', 'label' => false, 'type' => 'select', 'required', 'options' => $publishedCourses, 'default' => $published_course_combo_id)); ?>
									</div>
								</td>
							</tr>
						</table>
						<?php
					} 
					

					if (1 || !empty ($publishedCourses)) { ?>
						<div id="ExamSetupDiv">
							<?= $this->Form->input('edit', array('type' => 'hidden', 'value' => $edit)); ?>
							<hr>
							<?php
							//if(count($publishedCourses) > 0) {
							if (!empty ($published_course_combo_id)) {
								$input_disable = ($grade_submitted ? "disabled" : false);
								if (!$grade_submitted) { ?>
									<h6 class="fs14 text-gray">Please enter all the exam types(assesments) for the course you selected with its weight in the given field, below.</h6>
									<hr>
									<?php
								} else { ?>
									<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Exam grade is submitted for the selected course and changes on the exam setup is disabled.</div>
									<?php
								} ?>

								<div style="overflow-x:auto;">
									<table cellspacing="0" cellpadding="0" id="exam_setup" class="table">
										<thead>
											<tr>
												<th style="width:5%" class="center">#</th>
												<th style="width:25%" class="vcenter">Exam Type</th>
												<th style="width:20%" class="center">Percent</th>
												<th style="width:20%" class="center">Order</th>
												<th style="width:10%" class="center">Mandatory</th>
												<th style="width:20%" class="center">&nbsp;</th>
											</tr>
										</thead>
										<tbody>
											<?php
											if (empty ($this->request->data)) {
												if (empty ($exam_types)) { ?>
													<tr id="ExamType_1">
														<td class="center">1</td>
														<td class="vcenter"><?= $this->Form->input('ExamType.1.exam_name', array('label' => false, 'disabled' => $input_disable)); ?></td>
														<td class="center"><?= $this->Form->input('ExamType.1.percent', array('maxlength' => '5', 'label' => false, 'style' => 'width:75px')); ?></td>
														<td class="center"><?= $this->Form->input('ExamType.1.order', array('maxlength' => '2', 'type' => 'text', 'label' => false, 'style' => 'width:75px')); ?></td>
														<td class="center"><?= $this->Form->input('ExamType.1.mandatory', array('value' => 1, 'label' => false)); ?></td>
														<td class="center"><a href="javascript:deleteSpecificRow('ExamType_1')">Delete</a></td>
													</tr>
													<?php
												} else {
													$count = 0;
													foreach ($exam_types as $key => $exam_type) { ?>
														<tr id="ExamType_<?= ++$count; ?>">
															<td class="center"><?= $count; ?></td>
															<td class="vcenter">
																<?= $this->Form->input('ExamType.' . $count . '.id', array('type' => 'hidden', 'value' => $exam_type['ExamType']['id'])); ?>
																<?= $this->Form->input('ExamType.' . $count . '.exam_name', array('value' => $exam_type['ExamType']['exam_name'], 'label' => false, 'disabled' => $input_disable)); ?>
															</td>
															<td class="center"><?= $this->Form->input('ExamType.' . $count . '.percent', array('maxlength' => '5', 'value' => $exam_type['ExamType']['percent'], 'label' => false, 'style' => 'width:75px', 'disabled' => $input_disable)); ?></td>
															<td class="center"><?= $this->Form->input('ExamType.' . $count . '.order', array('maxlength' => '2', 'type' => 'text', 'value' => ($exam_type['ExamType']['order'] != 0 ? $exam_type['ExamType']['order'] : ''), 'label' => false, 'style' => 'width:75px', 'disabled' => $input_disable)); ?></td>
															<td class="center">
																<?php
																$coptions = array();
																$coptions['value'] = 1;
																$coptions['label'] = false;
																$coptions['disabled'] = $input_disable;

																if ($exam_type['ExamType']['mandatory'] == 1) {
																	$coptions['checked'] = 'checked';
																}

																echo $this->Form->input('ExamType.' . $count . '.mandatory', $coptions); ?>
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
													}
													$count++;
												}
											} else {//debug($this->request->data);
												$count = 1;
												foreach ($this->request->data['ExamType'] as $key => $examType) {//debug($examType['mandatory']);
													if (is_array($examType)) { ?>
														<tr id="ExamType_<?= $count; ?>">
															<td class="center"><?= ($count); ?></td>
															<td class="vcenter">
																<?= (isset ($examType['id']) ? $this->Form->input('ExamType.' . $key . '.id', array('type' => 'hidden')) : ''); ?>
																<?= $this->Form->input('ExamType.' . $key . '.exam_name', array('label' => false)); ?>
															</td>
															<td class="center"><?= $this->Form->input('ExamType.' . $key . '.percent', array('maxlength' => '5', 'label' => false, 'style' => 'width:75px')); ?></td>
															<td class="center"><?= $this->Form->input('ExamType.' . $key . '.order', array('maxlength' => '2', 'type' => 'text', 'label' => false, 'style' => 'width:75px')); ?></td>
															<td class="center">
																<?php
																$coptions = array();
																$coptions['value'] = 1;
																$coptions['label'] = false;

																if ($examType['mandatory'] == 1) {
																	$coptions['checked'] = 'checked';
																}

																echo $this->Form->input('ExamType.' . $key . '.mandatory', $coptions); ?>
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
									<input style="margin-bottom:0px" type="button" value="Add Row" onclick="addRow('exam_setup', 'ExamType', 5, '<?= $all_exam_setup_detail; ?>')" />
									<hr>
									<?= $this->Form->submit(__('Submit Exam Setup'), array('div' => false, 'class' => 'tiny radius button bg-blue'));
								} ?>
								<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Important Note: If a student fail to take any of the mandatory exam/s, the system will automatically give NG to the student.</div>
								<?php
							} else if (count($publishedCourses) <= 1) { ?>
								<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Please select academic year and semester to get list of published courses.</div>
								<?php
							} else { ?>
								<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Please select published course to get exam setup form.</div>
								<?php
							} ?>
						</div>
						<?php
					} ?>
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>