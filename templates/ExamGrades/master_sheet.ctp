<script>
	$(document).ready(function () {
		$("#PublishedCourse").change(function(){
			//serialize form data
			var pc = $("#PublishedCourse").val().split('~', 2);
			if(pc.length > 1) {
				window.location.replace("/exam_grades/<?= $this->request->action; ?>/"+pc[1]+"/section/"+$("#AcadamicYear").val()+"/"+$("#Semester").val());
			} else {
				window.location.replace("/exam_grades/<?= $this->request->action; ?>/"+pc[0]+"/pc/"+$("#AcadamicYear").val()+"/"+$("#Semester").val());
			}
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
</script>
<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Exam Grade View by Section'); ?></span>
		</div>
	</div>
    <div class="box-body">
      	<div class="row">
	  		<div class="large-12 columns">

				<div class="examGrades <?= $this->request->action; ?>">
					<?= $this->Form->create('ExamGrade');?>
					
					<?= $this->element('publish_course_filter_by_dept'); ?>
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
					} else { ?>

						<!-- <style>
						.bordering {
							border-left:1px #cccccc solid;
							border-right:1px #cccccc solid;
						}
						.bordering2 {
							border-left:1px #000000 solid;
							border-right:1px #000000 solid;
						}
						.courses_table tr td, .courses_table tr th {
							padding:1px
						}
						</style> -->

						<table cellpadding="0" cellspacing="0" class="table-borderless">
							<tr>
								<td colspan="2">
									<?= $this->Html->link($this->Html->image("/img/xls-icon.gif", array("alt"=>"Export")). ' Export Excel', array('controller'=>'examGrades', 'action' =>'export_mastersheet_xls'), array('escape'=>false)); ?>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php  // echo $this->Html->link($this->Html->image("/img/pdf_icon.gif",array("alt"=>"Print")),array('controller'=>'examGrades', 'action' =>'export_mastersheet_pdf'),array('escape'=>false)); ?>
								</td>
							</tr>
							<tr>
								<td style="width:40%">
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
								<td style="width:60%">
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
									}

									if (count($master_sheet['added_courses']) > 0) { ?>
										<div style="font-weight:bold; background-color:#cccccc; padding:0px; font-size:14px">Add Courses</div>
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
												$added_course_credit_sum = 0;
												foreach($master_sheet['added_courses'] as $key => $added_course) {
													$registered_and_add_course_count++;
													$added_course_credit_sum += $added_course['credit']; ?>
													<tr>
														<td class="center"><?= $registered_and_add_course_count; ?></td>
														<td class="vcenter"><?= $added_course['course_title']; ?></td>
														<td class="center"><?= $added_course['course_code']; ?></td>
														<td class="center"><?= $added_course['credit']; ?></td>
													</tr>
													<?php
												} ?>
											</tbody>
											<tfoot>
												<tr style="font-weight:bold">
													<td colspan="3" style="vertical-align: middle; text-align:right">Total</td>
													<td class="center"><?= $added_course_credit_sum; ?></td>
												</tr>
											</tfoot>
										</table>
										<?php
									} ?>
								</td>
							</tr>
						</table>
						<br>

						<?php $table_width = (count($master_sheet['registered_courses'])*10) + (count($master_sheet['added_courses'])*10) + 86; ?>
						
						<div style="overflow-x:auto;">
							<table style="width:<?= ($table_width > 100 ? $table_width : 100); ?>%" cellpadding="0" cellspacing="0" class="fs13 table">
								<thead>
									<tr>
										<th rowspan="2" style="vertical-align:bottom; width:2%" class="center">#</th>
										<th rowspan="2" style="vertical-align:bottom; width:18%" class="vcenter">Full Name</th>
										<th rowspan="2" style="vertical-align:bottom; width:8%" class="center">Student ID</th>
										<th rowspan="2" style="vertical-align:bottom; width:3%" class="center">Sex</th>
										<?php
										$percent = 10;
										$last_percent = false;
										$total_percent = (count($master_sheet['registered_courses'])*10) +  (count($master_sheet['added_courses'])*10) + 86;
										
										if ($total_percent > 100) {
											//$percent = (100 - 86) / (count($master_sheet['registered_courses']) + count($master_sheet['added_courses']));
										} else if ($total_percent < 100) {
											$last_percent = 100 - $total_percent;
										}

										$registered_and_add_course_count = 0;

										foreach ($master_sheet['registered_courses'] as $key => $registered_course) {
											$registered_and_add_course_count++; ?>
											<th colspan="2" style="width:<?= $percent; ?>%;" class="center">
											<?= $registered_and_add_course_count; ?></th>
											<?php
										}

										foreach ($master_sheet['added_courses'] as $key => $added_course) {
											$registered_and_add_course_count++; ?>
											<th colspan="2" style="width:<?= $percent; ?>%;" class="center"><?= $registered_and_add_course_count; ?></th>
											<?php
										} ?>

										<th colspan="3" style="width:15%;" class="center">Semester</th>
										<th colspan="3" style="width:15%" class="center">Previous</th>
										<th colspan="3" style="width:15%" class="center">Cumulative</th>
										<th rowspan="2" style="width:10%" class="center">Status</th>
										
										<?php
										if ($last_percent) { ?>
											<th style="width:<?= $last_percent; ?>%;" class="center">&nbsp;</th>
											<?php
										} ?>
									</tr>
									<tr>
										<?php
										foreach ($master_sheet['registered_courses'] as $key => $registered_course) { ?>
											<th style="width:<?= $percent/2; ?>%;" class="center">G</th>
											<th style="width:<?= $percent/2; ?>%;" class="center">GP</th>
											<?php
										}
										foreach ($master_sheet['added_courses'] as $key => $added_course) { ?>
											<th style="width:<?= $percent/2; ?>%;" class="center">G</th>
											<th style="width:<?= $percent/2; ?>%;" class="center">GP</th>
											<?php
										} ?>
										<th style="width:5%" class="center">CH</th>
										<th style="width:5%" class="center">GP</th>
										<th style="width:5%" class="center">SGPA</th>
							
										<th style="width:5%" class="center">CH</th>
										<th style="width:5%" class="center">GP</th>
										<th style="width:5%" class="center">CGPA</th>
										
										<th style="width:5%"  class="center">CH</th>
										<th style="width:5%" class="center">GP</th>
										<th style="width:5%" class="center">CGPA</th>
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
											<td class="center"><?= $student_count; ?></td>
											<td class="vcenter"><?= $student['full_name']; ?></td>
											<td class="center"><?= $student['studentnumber']; ?></td>
											<td class="center"><?= (strcasecmp($student['gender'], 'male') == 0 ? 'M' : 'F'); ?></td>
											<?php
											foreach($master_sheet['registered_courses'] as $key => $registered_course) {
												if ($student['courses']['r-'.$registered_course['id']]['registered'] == 1) {
													
													if(isset($student['courses']['r-'.$registered_course['id']]['grade'])) {
														echo '<td class="center">'.$student['courses']['r-'.$registered_course['id']]['grade'].'</td>';
														echo '<td class="center">';
														if (isset($student['courses']['r-'.$registered_course['id']]['point_value'])) {
															echo number_format(($student['courses']['r-'.$registered_course['id']]['credit'] * $student['courses']['r-'.$registered_course['id']]['point_value']), 2, '.', '');
															$gp_sum += ($student['courses']['r-'.$registered_course['id']]['credit'] * $student['courses']['r-'.$registered_course['id']]['point_value']);
														}
														echo '</td>';
													} else {
														echo '<td class="center">'.($student['courses']['r-'.$registered_course['id']]['droped'] == 1 ? 'DP' : '**').'</td>';
														echo '<td class="center">&nbsp;</td>';
													}

													if ($student['courses']['r-'.$registered_course['id']]['droped'] == 0) {
														$credit_hour_sum += $student['courses']['r-'.$registered_course['id']]['credit'];
													}
												} else {
													echo '<td class="center">---</td>';
													echo '<td class="center">&nbsp;</td>';
													//the student didn't register and there is nothing to display
												}
											}

											foreach($master_sheet['added_courses'] as $key => $added_course) {
												if ($student['courses']['a-'.$added_course['id']]['added'] == 1) {
													if(isset($student['courses']['a-'.$added_course['id']]['grade'])) {
														echo '<td class="center">'.$student['courses']['a-'.$added_course['id']]['grade'].'</td>';
														echo '<td class="center">';
														if (isset($student['courses']['a-'.$added_course['id']]['point_value'])) {
															echo number_format(($student['courses']['a-'.$added_course['id']]['credit'] * $student['courses']['a-'.$added_course['id']]['point_value']), 2, '.', '');
															$gp_sum += ($student['courses']['a-'.$added_course['id']]['credit'] * $student['courses']['a-'.$added_course['id']]['point_value']);
														}
														echo '</td>';
													} else {
														echo '<td class="center">**</td>';
														echo '<td class="center">&nbsp;</td>';
													}
													$credit_hour_sum += $student['courses']['a-'.$added_course['id']]['credit'];
												} else {
													echo '<td class="center">---</td>';
													echo '<td class="center">&nbsp;</td>';
													//the student didn't register and there is nothing to display
												}
											} ?>

											<td class="center"><?= (!empty($student['StudentExamStatus']) ? $student['StudentExamStatus']['credit_hour_sum'] : '---'); ?></td>
											<td class="center"><?= (!empty($student['StudentExamStatus']) ? $student['StudentExamStatus']['grade_point_sum'] : '---'); ?></td>
											<td class="center"><?= (!empty($student['StudentExamStatus']) ? $student['StudentExamStatus']['sgpa'] : '---'); ?></td>

											<td class="center"><?= (!empty($student['PreviousStudentExamStatus']) ? $student['PreviousStudentExamStatus']['previous_credit_hour_sum'] : '---'); ?></td>
											<td class="center"><?= (!empty($student['PreviousStudentExamStatus']) ? $student['PreviousStudentExamStatus']['previous_grade_point_sum'] : '---'); ?></td>
											<td class="center"><?= (!empty($student['PreviousStudentExamStatus']) ? $student['PreviousStudentExamStatus']['cgpa'] : '---'); ?></td>

											<td class="center">
												<?php
												if(!empty($student['StudentExamStatus']) && !empty($student['PreviousStudentExamStatus'])) {
													echo (($student['StudentExamStatus']['credit_hour_sum']+$student['PreviousStudentExamStatus']['previous_credit_hour_sum']) - $student['deduct_credit']);
												} else if(!empty($student['StudentExamStatus'])) {
													echo $student['StudentExamStatus']['credit_hour_sum'];
												} else if(!empty($student['PreviousStudentExamStatus'])) {
													echo $student['PreviousStudentExamStatus']['previous_credit_hour_sum'];
												} else {
													echo '---';
												} ?>
											</td>
											<td class="center">
												<?php
												if (!empty($student['StudentExamStatus']) && !empty($student['PreviousStudentExamStatus'])) {
													echo (($student['StudentExamStatus']['grade_point_sum']+$student['PreviousStudentExamStatus']['previous_grade_point_sum']) - $student['deduct_gp']);
												} else if(!empty($student['StudentExamStatus'])) {
													echo $student['StudentExamStatus']['grade_point_sum'];
												} else if(!empty($student['PreviousStudentExamStatus'])) {
													echo $student['PreviousStudentExamStatus']['previous_grade_point_sum'];
												} else {
													echo '---';
												} ?>
											</td>
											<td class="center">
												<?= (!empty($student['StudentExamStatus']) ? $student['StudentExamStatus']['cgpa'] : '---'); ?>
											</td>

											<td class="center">
												<?= (!empty($student['AcademicStatus']) &&  !empty($student['AcademicStatus']['id'])? $student['AcademicStatus']['name'] : '---'); ?>
											</td>
											<?php
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
