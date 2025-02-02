<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-check-outline" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Course Instructor Assignment'); ?></span>
        </div>
    </div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('CourseInstructorAssignment'); ?>
				<!-- <div class="courseInstructorAssignments form"> -->
				<div style="margin-top: -30px;">
					<hr>
					<blockquote>
						<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<p style="text-align:justify;">
							<span class="fs15 text-black">
								This tool will help you to do instructor assignment to your own department published courses and other department who dispatched courses  to be thought by your department staffs.
							</span>
						</p> 
					</blockquote>
					<hr>

					<div onclick="toggleViewFullId('ListPublishedCourse')">
						<?php
						if (!empty($turn_off_search)) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Display Filter</span><?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Hide Filter</span>
							<?php
						} ?>
					</div>

					<div id="ListPublishedCourse" style="display:<?= (!empty($turn_off_search) ? 'none' : 'display'); ?>">
						<br>
						<fieldset style="padding-bottom: 0px;padding-top: 15px;">
							<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
							<div class="row">
								<div class="large-2 columns">
									<?= $this->Form->input('Search.academicyear', array('label' => 'Academic Year: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $acyear_array_data, 'empty' => "[ Select Academic Year ]", 'default' => (isset($this->request->data['Search']['academicyear']) ? $this->request->data['Search']['academicyear'] : (isset($defaultacademicyear ) ? $defaultacademicyear : '')))); ?>
								</div>
								<div class="large-2 columns">
								<?= $this->Form->input('Search.semester', array('options' => Configure::read('semesters'), 'label' => 'Semester: ', 'style' => 'width:90%;', 'empty' => '[ Select ]', 'required')); ?>
								</div>
								<div class="large-2 columns">
									<h6 class='fs13 text-gray'>Program: </h6>
									<?= $this->Form->input('Search.program_id', array('id' => 'program_id', 'label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'div' => false)); ?> </td>
								</div>
								<div class="large-3 columns">
									<h6 class='fs13 text-gray'>Program Type: </h6>
									<?= $this->Form->input('Search.program_type_id', array('id' => 'program_type_id', 'label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'div' => false)); ?>
								</div>
								<div class="large-3 columns">
									<h6 class='fs13 text-gray'>Year Level: </h6>
									<?= $this->Form->input('Search.year_level_id', array('id' => 'year_level_id', 'label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'div' => false)); ?>
								</div>
							</div>
							<hr>
							<?= $this->Form->submit(__('Continue'), array('name' => 'getPublishedCourse', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
						</fieldset>
					</div>
					<hr>

					<?php
					if (!empty($sections_array)) {
						$index = 0;
						foreach ($sections_array as $depat => $depvalue) {
							//echo "<div class='fs16'> Department:" . $depat . "</div>";
							foreach ($depvalue as $pk => $pv) {
								//echo "<div class='fs16'> Program:" . $pk . "</div>";
								foreach ($pv as $ptk => $ptv) {
									//echo "<div class='fs16'> Program Type: " . $ptk . "</div>";
									foreach ($ptv as $yk => $yv) {
										//echo "<div class='fs16'> Year Level: " . $yk . "</div>";
										foreach ($yv as $section_name => $section_value) {
											$count = 1; ?>
											<!-- <div class='fs16'> Section : <?php //echo $section_name; ?></div> -->

											<br>
											<table cellpadding="0" cellspacing="0" class="table">
												<thead>
													<tr>
														<td colspan="8" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
															<span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?= $section_name; ?></span>
																<br>
																<!-- <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold"> 
																	<?php //echo $depat; ?>
																	<br>
																</span> -->
															</span>
															<span class="text-gray" style="padding-top: 14px; font-size: 13px; font-weight: bold"> 
																<?= $depat . ' &nbsp; | &nbsp; ' . $pk . ' &nbsp; | &nbsp; ' . $ptk; ?><br>
															</span>
														</td>
													</tr>
												</thead>
											</table>

											<div style="overflow-x:auto;">
												<table style="border: #CCC double 3px" cellpadding="0" cellspacing="0">
													<thead>
														<tr>
															<th style="width: 3%;" class="center"> # </th>
															<th class="vcenter">Course Title</th>
															<th class="center">Course Code</th>
															<th class="center">Credit</th>
															<th class="center">L T L</th>
														</tr>
													</thead>
													<tbody>
														<?php
														asort($section_value);
														foreach ($section_value as $sk => $sv) {
															$index = $index + 1;
															if (is_string($sk)) { ?>
																<tr>
																	<td colspan="5"><b><i> Split section name for this publish course: <?= $sk; ?></i></b></td>
																</tr>
																<tr>
																	<td class="font_color center"><?= $count++; ?></td>
																	<td class="font_color vcenter"><?= $sv['course_title']; ?></td>
																	<td class="font_color center"><?= $sv['course_code']; ?></td>
																	<td class="font_color center"><?= $sv['credit']; ?></td>
																	<td class="font_color center"><?= $sv['credit_detail']; ?></td>
																</tr>
																<tr>
																	<td colspan="2">
																		<!-- for Primary Instructor -->
																		<table style="border: #CCC dashed 2px" cellpadding="0" cellspacing="0">
																			<thead>
																				<tr>
																					<td colspan="4" style="text-align: center;"><b>Primary Instructor </b></td>
																				</tr>
																				<tr>
																					<th style="border-right: #CCC solid 1px" class="vcenter">Full Name</th>
																					<th style="border-right: #CCC solid 1px" class="center">Position</th>
																					<th style="border-right: #CCC solid 1px" class="center">Assigned For</th>
																					<?php
																					if ($sv['grade_submitted'] == 0) { ?>
																						<th style="border-right: #CCC solid 1px" class="center">Action</th>
																						<?php
																					} ?>
																				</tr>
																			</thead>
																			<?php
																			if (!empty($sv['assign_instructor'][1])) {
																				foreach ($sv['assign_instructor'][1] as $asvalue) { ?>
																					<tr>
																						<td style="border-right: #CCC solid 1px" class="vcenter"><?= $asvalue['full_name']; ?></td>
																						<td style="border-right: #CCC solid 1px" class="center"><?= $asvalue['position']; ?></td>
																						<td style="border-right: #CCC solid 1px" class="center"><?= $asvalue['course_type']; ?></td>
																						<?php
																						if ($sv['grade_submitted'] == 0) { ?>
																							<td style="border-right: #CCC solid 1px" class="center"><?= $this->Html->link(__('Delete'), array('controller' => 'course_instructor_assignments', 'action' => 'delete', $asvalue['CourseInstructorAssignment_id'], $sv['published_course_id']), null, sprintf(__('Are you sure you want to delete this instructor assignment?'), $asvalue['CourseInstructorAssignment_id'], $sv['published_course_id'])); ?></td>
																							<!-- </tr> -->
																							<?php
																						} ?>
																					</tr>
																					<?php
																				}
																			}
																			// debug($sv['course_split_section_id']);
																			$isprimary = 1;

																			// $course_split_section_id = $sv['course_split_section_id'];
																			$course_split_section_id = $sv['course_split_section_id'];

																			if (isset($thisdepartment)) {
																				$reformat_departments = array();
																				$reformat_departments[$thisdepartment] = $departments[$thisdepartment];
																				unset($departments[$thisdepartment]);

																				if (!empty($departments)) {
																					foreach ($departments as $id => $name) {
																						$reformat_departments[$id] = $name;
																					}
																				}

																				$departments = $reformat_departments;
																			}

																			if ($sv['grade_submitted'] == 0) {

																				if (empty($sv['given_by_department_id'])) {
																					$given_department_idddd = 'pre';
																				} else {
																					$given_department_idddd = $sv['given_by_department_id'];
																				}

																				if (empty($sv['assign_instructor'][1])) {  ?>
																					<tr>
																						<td colspan="4"><?= $this->Form->input('CourseInstructorAssignment.' . $index . '.type', array('id' => 'course_type_' . $index, 'style' => 'width: 40%;', 'label' => 'Type', 'empty' => '[ Select Type ]', 'type' => 'select', 'options' => $course_type_array[$depat][$pk][$ptk][$yk][$section_name][$sk], 'onchange' => 'getInstructorCombo(' . $index . ', "' . $given_department_idddd . '", ' . $sv['published_course_id'] . ',' . $isprimary . ',' . $course_split_section_id . ')')); ?></td>
																					</tr>
																					<?php
																				}
																			} ?>

																			<tr>
																				<td colspan="4" id="ajax_instructor_<?= $index; ?>"></td>
																			</tr>
																		</table>
																	</td>
																	<?php
																	//for Secondary Instructor
																	$index = $index + 1; ?>
																	<td colspan="3">
																		<table style="border: #CCC dashed 2px" cellpadding="0" cellspacing="0">
																			<thead>
																				<tr>
																					<td colspan="4" style="text-align: center;"><b>Secondary Instructor</b></td>
																				</tr>
																				<tr>
																					<th style="border-right: #CCC solid 1px" class="vcenter">Full Name</th>
																					<th style="border-right: #CCC solid 1px" class="center">Position</th>
																					<th style="border-right: #CCC solid 1px" class="center">Assigned For</th>
																					<?php
																					if ($sv['grade_submitted'] == 0) { ?>
																						<th style="border-right: #CCC solid 1px" class="center">Action</th>
																						<?php
																					} ?>
																				</tr>
																			</thead>
																			<?php
																			if (!empty($sv['assign_instructor'][0])) {
																				foreach ($sv['assign_instructor'][0] as $asvalue) { ?>
																					<tr>
																						<td style="border-right: #CCC solid 1px" class="vcenter"><?= $asvalue['full_name']; ?></td>
																						<td style="border-right: #CCC solid 1px" class="center"><?= $asvalue['position']; ?></td>
																						<td style="border-right: #CCC solid 1px" class="center"><?= $asvalue['course_type']; ?></td>
																						<?php
																						if ($sv['grade_submitted'] == 0) { ?>
																							<td style="border-right: #CCC solid 1px" class="center"><?= $this->Html->link(__('Delete'), array('controller' => 'course_instructor_assignments', 'action' => 'delete', $asvalue['CourseInstructorAssignment_id'], $sv['published_course_id']), null, sprintf(__('Are you sure you want to delete this instructor assignment?'), $asvalue['CourseInstructorAssignment_id'], $sv['published_course_id'])); ?></td>
																							<?php
																						} ?>
																					</tr>
																					<?php
																				}
																			}

																			$isprimary = 0;
																			$course_split_section_id = $sv['course_split_section_id'];

																			if (isset($thisdepartment)) {
																				$reformat_departments = array();
																				$reformat_departments[$thisdepartment] = $departments[$thisdepartment];
																				unset($departments[$thisdepartment]);

																				foreach ($departments as $id => $name) {
																					$reformat_departments[$id] = $name;
																				}

																				$departments = $reformat_departments;
																			}

																			if ($sv['grade_submitted'] == 0) {

																				if (empty($sv['given_by_department_id'])) {
																					$given_department_idddd = 'pre';
																				} else {
																					$given_department_idddd = $sv['given_by_department_id'];
																				}
																				
																				if (empty($sv['assign_instructor'][0])) { ?>
																					<tr>
																						<td colspan="4"><?= $this->Form->input('CourseInstructorAssignment.' . $index . '.type', array('id' => 'course_type_' . $index, 'style' => 'width: 50%;', 'label' => 'Type', 'empty' => '{ Select Type ]', 'type' => 'select', 'options' => $course_type_array[$depat][$pk][$ptk][$yk][$section_name][$sk], 'onchange' => 'getInstructorCombo(' . $index . ',"' . $given_department_idddd . '", ' . $sv['published_course_id'] . ',' . $isprimary . ',' . $course_split_section_id . ')')); ?></td>
																					</tr>
																					<?php
																				}
																			} ?>
																			<tr>
																				<td colspan="4" id="ajax_instructor_<?= $index; ?>"></td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<?php
															} else { ?>
																<tr>
																	<td class="font_color center"><?= $count++; ?></td>
																	<td class="font_color vcenter"><?= $sv['course_title']; ?></td>
																	<td class="font_color center"><?= $sv['course_code']; ?></td>
																	<td class="font_color center"><?= $sv['credit']; ?></td>
																	<td class="font_color center"><?= $sv['credit_detail']; ?></td>
																</tr>
																<tr>
																	<td colspan="2">
																		<!-- for Primary Instructor -->
																		<table style="border: #CCC dashed 2px" cellpadding="0" cellspacing="0">
																			<thead>
																				<tr>
																					<td colspan="4" style="text-align: center;"><b>Primary Instructor</b></td>
																				</tr>
																				<tr>
																					<th style="border-right: #CCC solid 1px" class="vcenter">Full Name</th>
																					<th style="border-right: #CCC solid 1px" class="center">Position</th>
																					<th style="border-right: #CCC solid 1px" class="center">Assigned For</th>
																					<?php
																					if ($sv['grade_submitted'] == 0) { ?>
																						<th style="border-right: #CCC solid 1px" class="center">Action</th>
																						<?php
																					} ?>
																				</tr>
																			</thead>
																			
																			<?php
																			if (!empty($sv['assign_instructor'][1])) {
																				foreach ($sv['assign_instructor'][1] as $asvalue) { ?>
																					<tr>
																						<td style="border-right: #CCC solid 1px" class="vcenter"><?= $asvalue['full_name']; ?></td>
																						<td style="border-right: #CCC solid 1px" class="center"><?= $asvalue['position']; ?></td>
																						<td style="border-right: #CCC solid 1px" class="center"><?= $asvalue['course_type']; ?></td>
																						<?php
																						if ($sv['grade_submitted'] == 0) { ?>
																							<td style="border-right: #CCC solid 1px" class="center"><?= $this->Html->link(__('Delete'), array('controller' => 'course_instructor_assignments', 'action' => 'delete', $asvalue['CourseInstructorAssignment_id'], $sv['published_course_id']), null, sprintf(__('Are you sure you want to delete this instructor assignment?'), $asvalue['CourseInstructorAssignment_id'], $sv['published_course_id'])); ?></td>
																							<!-- </tr> -->
																							<?php
																						} ?>
																					</tr>
																					<?php
																				}
																			}

																			$isprimary = 1;
																			$course_split_section_id = 0;

																			if (isset($thisdepartment)) {

																				$reformat_departments = array();
																				$reformat_departments[$thisdepartment] = $departments[$thisdepartment];
																				unset($departments[$thisdepartment]);

																				if (!empty($departments)) {
																					foreach ($departments as $id => $name) {
																						$reformat_departments[$id] = $name;
																					}
																				}

																				$departments = $reformat_departments;
																			}

																			if ($sv['grade_submitted'] == 0) {

																				if (empty($sv['given_by_department_id'])) {
																					$given_department_idddd = 'pre';
																				} else {
																					$given_department_idddd = $sv['given_by_department_id'];
																				}

																				if (empty($sv['assign_instructor'][1])) { ?>
																					<tr>
																						<td colspan="4"><?= $this->Form->input('CourseInstructorAssignment.' . $index . '.type', array('id' => 'course_type_' . $index, 'style' => 'width: 40%;', 'label' => 'Type', 'empty' => '[ Select Type ]', 'type' => 'select', 'options' => $course_type_array[$depat][$pk][$ptk][$yk][$section_name][$sk], 'onchange' => 'getInstructorCombo(' . $index . ',"' . $given_department_idddd . '",' . $sv['published_course_id'] . ',' . $isprimary . ',' . $course_split_section_id . ')')); ?></td>
																					</tr>
																					<?php
																				}
																			} else { ?>
																				<tr>
																					<td colspan="4">The course has grade. Not possible to assign/change instructor.</td>
																				</tr>
																				<?php
																			} ?>

																			<tr>
																				<td colspan="4" id="ajax_instructor_<?= $index; ?>"></td>
																			</tr>
																		</table>
																	</td>
																	<!-- for Secondary Instructor -->
																	<?php
																	$index = $index + 1; ?>
																	<td colspan="3">
																		<table style="border: #CCC dashed 2px" cellpadding="0" cellspacing="0">
																			<thead>
																				<tr>
																					<td colspan="4" style="text-align: center;"><b>Secondary Instructor</b></td>
																				</tr>
																				<tr>
																					<th style="border-right: #CCC solid 1px" class="vcenter">Full Name</th>
																					<th style="border-right: #CCC solid 1px" class="center">Position</th>
																					<th style="border-right: #CCC solid 1px" class="center">Assigned For</th>
																					<?php
																					if ($sv['grade_submitted'] == 0) { ?>
																						<th style="border-right: #CCC solid 1px" class="center">Action</th>
																						<?php
																					} ?>
																				</tr>
																			</thead>

																			<?php
																			if (!empty($sv['assign_instructor'][0])) {
																				foreach ($sv['assign_instructor'][0] as $asvalue) { ?>
																					<tr>
																						<td style="border-right: #CCC solid 1px" class="vcenter"><?= $asvalue['full_name']; ?></td>
																						<td style="border-right: #CCC solid 1px" class="center"><?= $asvalue['position']; ?></td>
																						<td style="border-right: #CCC solid 1px" class="center"><?= $asvalue['course_type']; ?></td>
																						<?php
																						if ($sv['grade_submitted'] == 0) { ?>
																							<td style="border-right: #CCC solid 1px" class="center"><?= $this->Html->link(__('Delete'), array('controller' => 'course_instructor_assignments', 'action' => 'delete', $asvalue['CourseInstructorAssignment_id'], $sv['published_course_id']), null, sprintf(__('Are you sure you want to delete this instructor assignment?'), $asvalue['CourseInstructorAssignment_id'], $sv['published_course_id'])); ?></td>
																							<?php
																						} ?>
																					</tr>
																					<?php
																				}
																			}

																			$isprimary = 0;
																			$course_split_section_id = 0;

																			if (isset($thisdepartment)) {
																				$reformat_departments = array();
																				$reformat_departments[$thisdepartment] = $departments[$thisdepartment];
																				unset($departments[$thisdepartment]);

																				foreach ($departments as $id => $name) {
																					$reformat_departments[$id] = $name;
																				}

																				$departments = $reformat_departments;
																			}

																			if ($sv['grade_submitted'] == 0) {

																				if (empty($sv['given_by_department_id'])) {
																					$given_department_idddd = 'pre';
																				} else {
																					$given_department_idddd = $sv['given_by_department_id'];
																				} 
																				
																				if (empty($sv['assign_instructor'][0])) { ?>
																					<tr>
																						<td colspan="4"><?= $this->Form->input('CourseInstructorAssignment.' . $index . '.type', array('id' => 'course_type_' . $index, 'style' => 'width: 50%;', 'label' => 'Type', 'empty' => '[ Select Type ]', 'type' => 'select', 'options' => $course_type_array[$depat][$pk][$ptk][$yk][$section_name][$sk], 'onchange' => 'getInstructorCombo(' . $index . ',"' . $given_department_idddd . '",' . $sv['published_course_id'] . ',' . $isprimary . ',' . $course_split_section_id . ')')); ?></td>
																					</tr>
																					<?php
																				}
																			} ?>

																			<tr>
																				<td colspan="4" id="ajax_instructor_<?= $index; ?>"></td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<?php
															}
														} ?>
													</tbody>
												</table>
											</div>
											<?php
										}
									}
								}
							}
						}
					} ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var image = new Image();
	image.src = '/img/busy.gif';
	//update instructor combo
	function getInstructorCombo(id, department_id, published_course_id, isprimary, course_split_section_id) {
		//serialize form data
		var subCat = department_id + '~' + $("#course_type_" + id).val() + '~' + published_course_id + '~' + isprimary + '~' + course_split_section_id;
		
		$("#ajax_instructor_" + id).attr('disabled', true);
		$("#ajax_instructor_" + id).empty().html('<img src="/img/busy.gif" class="displayed" >');

		var formUrl = '/course_instructor_assignments/assign_instructor/' + subCat;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: subCat,
			success: function (data, textStatus, xhr) {
				$("#ajax_instructor_" + id).attr('disabled', false);
				$("#ajax_instructor_" + id).empty();
				$("#ajax_instructor_" + id).append(data);
			},
			error: function (xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}

	function toggleViewFullId(id) {
		if ($('#' + id).css("display") == 'none') {
			$('#' + id + 'Img').attr("src", '/img/minus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append(' Hide Filter');
		} else {
			$('#' + id + 'Img').attr("src", '/img/plus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append(' Display Filter');
		}
		$('#' + id).toggle("slow");
	}
</script>