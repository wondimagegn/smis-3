<script>
	$(document).ready(function () {
		$("#SectionID").change(function(){
			window.location.replace("/exam_grades/<?= $this->request->action; ?>/"+$("#SectionID").val()+"/"+$("#AcademicYear").val()+"/"+$("#Semester").val()+"/"+$("#ProgramID").val()+"/"+$("#ProgramTypeID").val()+"/"+$("#CompactVersion").val());
		});
	});

	function toggleView(obj) {
		if($('#c'+obj.id).css("display") == 'none') {
			$('#i'+obj.id).attr("src", '/img/minus2.gif');
		} else {
			$('#i'+obj.id).attr("src", '/img/plus2.gif');
		}
		$('#c'+obj.id).toggle("slow");
	}

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

	function updateRemedialSectionsOnChangeofOtherField() {
		//var semester = $("#Semester option:selected").text();

		var formData = '';
		var academic_year = $("#AcademicYear").val().replace("/", "-");
		var semester =  $("#Semester").val();
		var program_id = $("#ProgramID").val();
		var program_type_id = $("#ProgramTypeID").val();

		if (typeof academic_year != "undefined" && typeof semester != "undefined" &&  typeof program_id != "undefined" &&  typeof program_type_id != "undefined") {
			formData = academic_year + '~' + semester + '~' + program_id + '~' + program_type_id;
		} else {
			return false;
		}

		$("#SectionID").attr('disabled', true);
		//get form action
		var formUrl = '/exam_grades/get_remedial_sections_combo/' + formData;

		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data,textStatus, xhr) {
				$("#AcademicYear").attr('disabled', false);
				$("#Semester").attr('disabled', false);
				$("#ProgramID").attr('disabled', false);
				$("#ProgramTypeID").attr('disabled', false);
				$("#SectionID").attr('disabled', false);
				$("#SectionID").empty();
				$("#SectionID").append(data);
			},
			error: function(xhr, textStatus, error) {
				//alert(textStatus);
			}
		});

	}
</script>
<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Remedial Program Master Sheet'); ?></span>
		</div>
	</div>
    <div class="box-body">
      	<div class="row">
	  		<div class="large-12 columns">

				<div class="examGrades <?= $this->request->action; ?>">
					<?= $this->Form->create('ExamGrade');?>
					
					<div style="margin-top: -10px;">
						<hr>
						<div onclick="toggleViewFullId('ListPublishedCourse')">
							<?php
							if (!empty($publishedCourses)) {
								echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
								<?php
							} else {
								echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
								<?php
							} ?>
						</div>

						<div id="ListPublishedCourse" style="display:<?= (!empty($publishedCourses) ? 'none' : 'display'); ?>">
							<fieldset style="padding-bottom: 5px;padding-top: 5px;">
								<legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
								<div class="row">
									<div class="large-3 columns">
										<?= $this->Form->input('acadamic_year', array('onchange' => 'updateRemedialSectionsOnChangeofOtherField()', 'id' => 'AcademicYear', 'label' => 'Acadamic Year: ', 'class' => 'fs14', 'style' => 'width:90%', 'type' => 'select', 'options' => $acyear_list, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('semester', array('onchange' => 'updateRemedialSectionsOnChangeofOtherField()', 'id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:90%', 'label' => 'Semester: ', 'options' => Configure::read('semesters'), 'required', 'default' => (isset($semester_selected) ? $semester_selected : ''))); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('program_id', array('onchange' => 'updateRemedialSectionsOnChangeofOtherField()', 'id' => 'ProgramID', 'class' => 'fs14', 'label' => 'Program: ', 'style' => 'width:90%','type' => 'select', 'options' => $programsss, 'default' => (isset($program_id) ? $program_id : ''), 'required')); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('program_type_id', array('onchange' => 'updateRemedialSectionsOnChangeofOtherField()', 'id' => 'ProgramTypeID', 'class' => 'fs14', 'label' => 'Program Type: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $programTypesss, 'default' => (isset($program_type_id) ? $program_type_id : ''), 'required')); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-6 columns">
										<?php
										if (!empty($remedial_sections)) { ?>
											<?= $this->Form->input('section_id', array('style' => 'width: 90%;',  'class' => 'fs14', 'id' => 'SectionID', 'label' => 'Section: ', 'type' => 'select', 'required', 'options' => $remedial_sections, 'default' => (isset($section_combo_id) ? $section_combo_id : ''))); ?>
											<?php
										} ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('compact_version', array('style' => 'width: 90%;', 'label' => 'Excel Export Options:',  'class' => 'fs14', 'type' => 'select', 'id' => 'CompactVersion', 'options' => array('0' => 'Full Assesment', '1' => 'Compact', ), 'value' => (isset($compact_version_checked) && $compact_version_checked ? $compact_version_checked : ''))); ?>
									</div>
									<div class="large-3 columns">
										&nbsp;
									</div>
								</div>
							</fieldset>
						</div>
						<hr>
					</div>

					<?= $this->Form->end(); ?>

					<?php
					//Displaying list of students with their grade
					if (!empty($publishedCourses) && !isset($published_course_id)) { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Please select a course or section.</div>
						<?php
					} else if (isset($published_course_id) && count($master_sheet['students_and_grades']) <= 0) { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>The system is unable to find list of students for the selected section. Please contact the department for more information.</div>
						<?php
					} else if (isset($published_course_id) && empty($master_sheet['registered_courses']) && empty($master_sheet['added_courses'])) { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>The system is unable to find list of courses section students register and/or add.</div>
						<?php
					} else if (isset ($master_sheet) && count($master_sheet['students_and_grades'])) { ?>

						<table cellpadding="0" cellspacing="0" class="table-borderless">
							<tr>
								<td colspan="2">
									<?= $this->Html->link($this->Html->image("/img/xls-icon.gif",array("alt"=>"Export")) . ' Export Excel', array( 'controller'=>'examGrades', 'action' =>'export_remedial_mastersheet_xls'), array('escape' => false)); ?>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php  // echo $this->Html->link($this->Html->image("/img/pdf_icon.gif",array("alt"=>"Print")),array('controller'=>'examGrades', 'action' =>'export_mastersheet_pdf'),array('escape'=>false)); ?>
								</td>
							</tr>
							<tr>
								<td style="width:40%; background-color: white;">
									<table cellpadding="0" cellspacing="0" class="fs13 table">
										<tr>
											<td style="width:30%" class="vcenter">College:</td>
											<td style="width:70%; font-weight:bold" class="vcenter"><?= $college_detail['name']; ?></td>
										</tr>
										<tr>
											<td class="vcenter">Department:</td>
											<td style="font-weight:bold" class="vcenter"><?= (!empty($department_detail['name']) && $department_detail['name'] != "" ? $department_detail['name'] : 'Freshman Program'); ?></td>
										</tr>
										<tr>
											<td class="vcenter">Program:</td>
											<td style="font-weight:bold" class="vcenter"><?= $program_detail['name']; ?></td>
										</tr>
										<tr>
											<td class="vcenter">Program Type:</td>
											<td style="font-weight:bold" class="vcenter"><?= $program_type_detail['name']; ?></td>
										</tr>
										<tr>
											<td class="vcenter">Section:</td>
											<td style="font-weight:bold" class="vcenter"><?= $section_detail['name']; ?></td>
										</tr>
										<tr>
											<td class="vcenter">Acdamic Year:</td>
											<td style="font-weight:bold" class="vcenter"><?= $academic_year; ?></td>
										</tr>
										<tr>
											<td class="vcenter">Semester:</td>
											<td style="font-weight:bold" class="vcenter"><?= $semester; ?></td>
										</tr>
									</table>
								</td>
								<td style="width:60%; background-color: white;">
									<?php
									if (count($master_sheet['registered_courses']) > 0) { ?>

										<div style="font-weight:bold; background-color:#cccccc; padding:0px; font-size:14px">Registered Courses</div>
										
										<table class="courses_table" cellpadding="0" cellspacing="0" class="fs13 table">
											<thead>
												<tr>
													<th style="width:5%" class="center">#</th>
													<th style="width:55%" class="vcenter">Course Title</th>
													<th style="width:20%" class="center">Course Code</th>
													<th style="width:20%" class="center">Credit</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$registered_and_add_course_count = 0;
												$registered_course_credit_sum = 0;
												foreach($master_sheet['registered_courses'] as $key => $registered_course) {
													$registered_and_add_course_count++;
													$registered_course_credit_sum += $registered_course['credit']; ?>
													<tr>
														<td class="center"><?= $registered_and_add_course_count; ?></td>
														<td class="vcenter"><?= $registered_course['course_title']; ?></td>
														<td class="center"><?= $registered_course['course_code']; ?></td>
														<td class="center"><?= $registered_course['credit']; ?></td>
													</tr>
													<?php
												} ?>
											</tbody>
											<tfoot>
												<tr style="font-weight:bold">
													<td colspan="3" style=" vertical-align: middle; text-align:right">Total</td>
													<td class="center"><?= $registered_course_credit_sum; ?></td>
												</tr>
											</tfoot>
										</table>
										<?php
									} ?>
								</td>
							</tr>
						</table>
						<br>

						<?php $table_width = (count($master_sheet['registered_courses'])*10) + 85; ?>
						
						<div style="overflow-x:auto;">
							<table style="width:<?= ($table_width > 100 ? $table_width : 100); ?>%" cellpadding="0" cellspacing="0" class="fs13 table">
								<thead>
									<tr>
										<th rowspan="2" style="vertical-align:bottom; width:2%" class="center">#</th>
										<th rowspan="2" style="vertical-align:bottom; width:20%" class="vcenter">Full Name</th>
										<th rowspan="2" style="vertical-align:bottom; width:8%" class="center">Student ID</th>
										<th rowspan="2" style="vertical-align:bottom; width:5%; border-right:1px #000000 solid" class="center">Sex</th>
										<?php
										$percent = 10;
										$last_percent = false;
										$total_percent = (count($master_sheet['registered_courses'])*10) + 86;
										
										if ($total_percent > 100) {
											//$percent = (100 - 86) / (count($master_sheet['registered_courses']) + count($master_sheet['added_courses']));
										} else if ($total_percent < 100) {
											$last_percent = 100 - $total_percent;
										}

										$registered_and_add_course_count = 0;

										if (!empty($master_sheet['registered_courses'])) {
											foreach ($master_sheet['registered_courses'] as $key => $registered_course) {
												$registered_and_add_course_count++; 
												$exmTypes = count($registered_course['exam_type']); ?>
												<th colspan=<?= $exmTypes + 3; ?> style="width:<?= $percent; ?>%; border-right:1px #000000 solid" class="center"><?= $registered_course['course_code']; //$registered_and_add_course_count; ?></th>
												<?php
											}

										} ?>
										
										<?php
										if ($last_percent) { ?>
											<th style="width:<?= $last_percent; ?>%;" class="center">&nbsp;</th>
											<?php
										} ?>
									</tr>
									<tr>
										<?php
										if (!empty($master_sheet['registered_courses'])) {
											foreach ($master_sheet['registered_courses'] as $key => $registered_course) { 

												$exmTypes = count($registered_course['exam_type']);
												
												if ($exmTypes) {
													foreach ($registered_course['exam_type'] as $key => $exmty) { ?>
														<th style="width:<?= $percent/($exmTypes +3); ?>%;" class="center"><?= $exmty['exam_name'] .'('. $exmty['percent'] .')'; ?></th>
														<?php
													}  ?>
													<th style="width:<?= $percent/($exmTypes + 3); ?>%; border-left:1px #000000 solid;" class="center">30%</th>
													<th style="width:<?= $percent/($exmTypes + 3); ?>%; border-left:1px #000000 solid;" class="center">100%</th>
													<th style="width:<?= $percent/($exmTypes + 3); ?>%;  border-left:1px #000000 solid; border-right:1px #000000 solid" class="center">G</th>
													<?php

												} else { ?>
													<th style="width:<?= $percent/($exmTypes + 3); ?>%;" class="center">30%</th>
													<th style="width:<?= $percent/($exmTypes + 3); ?>%;" class="center">100%</th>
													<th style="width:<?= $percent/($exmTypes + 3); ?>%; border-right:1px #000000 solid" class="center">G</th>
													<?php
												}
											}
										} ?>

										<?php
										if ($last_percent) { ?>
											<th class="center">&nbsp;</th>
											<?php
										} ?>
									</tr>
								</thead>
								<tbody>
									<?php
									$student_count = 0;
									foreach($master_sheet['students_and_grades'] as $key => $student) {
										$credit_hour_sum = 0;
										$gp_sum = 0;
										$student_count++; ?>
										<tr>
											<td class="center" style="border-left:1px #000000 solid;"><?= $student_count; ?></td>
											<td class="vcenter" style="border-left:1px #000000 solid;"><?= $student['full_name']; ?></td>
											<td class="center" style="border-left:1px #000000 solid;"><?= $student['studentnumber']; ?></td>
											<td class="center" style="border-left:1px #000000 solid; border-right:1px #000000 solid"><?= (strcasecmp(trim($student['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($student['gender']), 'female') == 0 ?'F' : trim($student['gender']))); ?></td>
											<?php
											if (!empty($master_sheet['registered_courses'])) {
												foreach($master_sheet['registered_courses'] as $key => $registered_course) {
													if ($student['courses']['r-'.$registered_course['id']]['registered'] == 1) {

														$thirtyPercent = 0;
														$hundredPercent = 0;
														$exmTypes2 = $exmTypes;
														$haveAssesment = 0;

														if (!empty($student['courses']['r-'.$registered_course['id']]['Assesment'])) {
															$exmTypes2 = $student['courses']['r-'.$registered_course['id']]['Assesment'];
															foreach ($student['courses']['r-'.$registered_course['id']]['Assesment'] as $asskey => $assvalue) {
																if (isset($assvalue['ExamResult']) && !empty($assvalue['ExamResult']['result'])) {
																	$hundredPercent += $assvalue['ExamResult']['result'];
																	if ($assvalue['ExamType']['percent'] < 50 && $thirtyPercent <= 30) {
																		$thirtyPercent += $assvalue['ExamResult']['result'];
																		$haveAssesment++;
																	} else if (is_numeric($assvalue['ExamType']['percent']) && isset($assvalue['ExamResult']['result'])) {
																		$haveAssesment++;
																	}
																	echo '<td class="center">'.$assvalue['ExamResult']['result'].'</td>';

																} else if (isset($assvalue['ExamType'])) {
																	echo '<td class="center">--</td>';
																} else {
																	echo '<td class="center">--</td>';
																} 
															} ?>

															<td class="center" style="border-left:1px #000000 solid;"><?= (isset($thirtyPercent) && $haveAssesment > 0 ? $thirtyPercent : '--'); ?></td>
															<td class="center" style="border-left:1px #000000 solid;"><?= (isset($hundredPercent) && $haveAssesment > 0 ? $hundredPercent : '--'); ?></td>
															<?php
														} else {
															if (isset($exmTypes2) && $exmTypes2) {

																for ($i=0; $i < $exmTypes2; $i++) { 
																	echo '<td class="center">--</td>';
																} ?>

																<td class="center" style="border-left:1px #000000 solid;">--</td>
																<td class="center" style="border-left:1px #000000 solid;">--</td>
																<?php
															} else { ?>
																<td class="center" style="border-left:1px #000000 solid;">--</td>
																<td class="center" style="border-left:1px #000000 solid;">--</td>
																<?php
															}
														}
														
														if (isset($student['courses']['r-'.$registered_course['id']]['grade'])) {
															echo '<td class="center" style="border-left:1px #000000 solid; border-right:1px #000000 solid">'.$student['courses']['r-'.$registered_course['id']]['grade'].'</td>';
														} else {
															echo '<td class="center" style="border-left:1px #000000 solid; border-right:1px #000000 solid">'.($student['courses']['r-'.$registered_course['id']]['droped'] == 1 ? 'DP' : '**').'</td>';
														}
													} else {

														if (isset($exmTypes) && $exmTypes) {
															for ($i=0; $i < $exmTypes; $i++) { 
																echo '<td class="center">--</td>';
															}
														}

														echo '<td class="center" style="border-left:1px #000000 solid;">&nbsp;</td>';
														echo '<td class="center" style="border-left:1px #000000 solid;">&nbsp;</td>';
														echo '<td class="center" style="border-left:1px #000000 solid; border-right:1px #000000 solid">**</td>';

														//the student didn't register and there is nothing to display
													}
												}
											} 

											if ($last_percent) { ?>
												<td class="center">&nbsp;</td>
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
				</div>
			</div>
		</div>
	</div>
</div>
