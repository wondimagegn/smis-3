<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-check-outline" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Dispatch/Change Instructor Assignment to Other Department'); ?></span>
        </div>
    </div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('CourseInstructorAssignment'); ?>
				<div style="margin-top: -30px;">
					<hr>
					<blockquote>
						<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<p style="text-align:justify;">
							<span class="fs15 text-black">
								Please select the appropriate "Given by Department" from the list who will give the course, then the system will allow the other department to assign instructor accordingly.
								By default, the system will take the current department who published the courses are responsible for assigning an instructor. 
								Only courses which are not assigned to instructor are allowed for dispatch or department change.
							</span>
						</p> 
					</blockquote>
					<hr>

					<div onclick="toggleViewFullId('ListPublishedCourse')">
						<?php
						if (!empty($turn_off_search)) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
							<?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
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
				</div>

				<?php
				if (isset($turn_off_search) && !empty($organizedPublishedCourse)) { ?>
					<hr>

					<!-- <p class='smallheading' style='font-weight:bold'>Academic Year: <?php //echo $academic_year; ?><br />Semester: <?php //echo $semester; ?> </p> -->

					<?php
					//debug($organizedPublishedCourse);
					$enable_dispatch_button = 0;
					foreach ($organizedPublishedCourse as $pk => $pv) {
						//echo "<span class='fs14' style='font-weight:bold'>Program: " . $pk . "</span><br>";
						foreach ($pv as $ptk => $ptv) {
							//echo "<span class='fs14' style='font-weight:bold'>Program Type: " . $ptk . "</span><br>";
							foreach ($ptv as $yk => $yv) {
								//echo "<br><span class='fs14' style='font-weight:bold'>Year Level: " . $yk . "</span><br>";
								foreach ($yv as $section_name => $section_value) {
									//debug($section_value[4]);
									//echo "<br><span class='fs14' style='font-weight:bold'>Section : " . $section_name . "</span><br><br>"; ?>
									<div style="overflow-x:auto;">
										<table id='fieldsForm' cellpadding="0" cellspacing="0" class='table'>
											<thead>
												<tr>
                                                    <td colspan="7" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                        <span style="font-size:16px;font-weight:bold; margin-top: 25px;">
                                                        	Section: <?= (isset($section_name) ? $section_name  . ' ' .  (isset($section_value[0]['Program']['id']) && $section_value[0]['Program']['id'] == PROGRAM_REMEDIAL ? ' (Remedial)' : (isset($yk)  ?  ' (' . $yk . ')' : (isset($section_value[0]['YearLevel']['name']) ? $section_value[0]['YearLevel']['name'] : ' (Pre/1st)'))) : ''); ?>
                                                        </span>
                                                        <br>
                                                        <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold"> 
                                                            <?= (isset($deptKey) ? $deptKey : (isset($section_value[0]['Department']['name']) ? $section_value[0]['Department']['name'] :  $section_value[0]['College']['name'] . (isset($section_value[0]['Program']['id']) && $section_value[0]['Program']['id'] == PROGRAM_REMEDIAL ? ' - Remedial Program' : ' - Pre/Freshman'))); ?> &nbsp; | &nbsp; <?= (isset($pk) ? $pk : (isset($section_value[0]['Program']['name']) ? $section_value[0]['Program']['name'] : '')); ?>  &nbsp; | &nbsp; <?= (isset($ptk) ? $ptk : (isset($section_value[0]['ProgramType']['name']) ? $section_value[0]['ProgramType']['name'] : '')); ?> 
                                                        </span>
                                                        <br>
                                                        <span class="text-black" style="padding-top: 14px; font-size: 13px; font-weight: bold"> 
                                                        	<?= (isset($academic_year) ? $academic_year : (isset($section_value[0]['academic_year']) ? $section_value[0]['PublishedCourse']['academic_year'] :  $section_value[0]['Section']['academicyear'])); ?> &nbsp; | &nbsp; <?= (isset($sk) ? ($sk == 'I' ? '1st Semester' : ( $sk == 'II' ? '2nd Semester' : ($sk == 'III' ? '3rd Semester' : $sk . ' Semester'))) : (isset($section_value[0]['PublishedCourse']['semester']) ? ($section_value[0]['PublishedCourse']['semester'] == 'I' ? '1st Semester' : ($section_value[0]['PublishedCourse']['semester'] == 'II' ? '2nd Semester' : ($section_value[0]['PublishedCourse']['semester'] == 'III' ? '3rd Semester' : $section_value[0]['PublishedCourse']['semester'] . ' Semester'))) :  '')); ?> <br>
                                                        </span>
                                                    </td>
                                                </tr>
												<tr>
													<th class="center" style="width:5%;">#</th>
													<th class="vcenter" style="width:27%;">Course Title</th>
													<th class="center" style="width:15%;">Course Code</th>
													<th class="center" style="width:3%;"><?= (!isset($section_value[0]['Course']['Curriculum']['type_credit']) ? 'Credit' : (count(explode('ECTS', $section_value[0]['Course']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit')); ?></th>
													<th class="center" style="width:8%;">LTL</th>
													<th class="center" style="width:10%;">Prerequisite</th>
													<th class="center" style="width:32%;">Given By Department</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$count = 1;
												foreach ($section_value as $type_index => $publishedCourse) { ?>
													<tr>
														<td class="center">
															<?php
															if (isset($publishedCourse['CourseInstructorAssignment']) && !empty($publishedCourse['CourseInstructorAssignment'])) {
															} else {
																echo $this->Form->input('PublishedCourse.' . $publishedCourse['PublishedCourse']['id'] . '.id', array('value' => $publishedCourse['PublishedCourse']['id']));
															}
															echo $count++; ?>
														</td>
														<td class="vcenter"><?= $this->Html->link($publishedCourse['Course']['course_title'], array('controller' => 'courses', 'action' => 'view', $publishedCourse['Course']['id'])); ?></td>
														<td class="center"><?= $publishedCourse['Course']['course_code']; ?></td>
														<td class="center"><?= $publishedCourse['Course']['credit']; ?></td>
														<td class="center"><?= $publishedCourse['Course']['course_detail_hours']; ?></td>
														<td class="center">
															<?php
															if (!empty($publishedCourse['Course']['Prerequisite'])) {
																foreach ($publishedCourse['Course']['Prerequisite'] as $ppindex => $pvlll) {
																	echo $pvlll['PrerequisiteCourse']['course_code'];
																}
															} else {
																echo 'none';
															} ?>
														</td>
														<td class="center">
															<?php
															if (isset($publishedCourse['CourseInstructorAssignment']) && !empty($publishedCourse['CourseInstructorAssignment'])) {
																if (isset($publishedCourse['PublishedCourse']['given_by_department_id']) && !empty($publishedCourse['PublishedCourse']['given_by_department_id'])) {
																	echo $departmentsAll[$publishedCourse['PublishedCourse']['given_by_department_id']];
																	if ($publishedCourse['CourseInstructorAssignment'][0]['isprimary']) {
																		//debug($publishedCourse['CourseInstructorAssignment'][0]['Staff']['full_name']);
																		echo '<br>'. $publishedCourse['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . '. ' . $publishedCourse['CourseInstructorAssignment'][0]['Staff']['full_name'] . (isset($publishedCourse['CourseInstructorAssignment'][0]['Staff']['phone_mobile']) && !empty($publishedCourse['CourseInstructorAssignment'][0]['Staff']['phone_mobile']) ? '<br> Mobile: ' . $publishedCourse['CourseInstructorAssignment'][0]['Staff']['phone_mobile'] : '');
																	}
																} else {
																	echo '---';
																}
															} else {
																/* echo $this->Form->input('PublishedCourse.' . $publishedCourse['PublishedCourse']['id'] . '.given_by_college_id', array(
																	'label' => false, 'type' => 'select', 'options' => $colleges,
																	'default' => isset($this->request->data['PublishedCourse'][$publishedCourse['PublishedCourse']['id']]['given_by_college_id']) ? $this->request->data['PublishedCourse'][$publishedCourse['PublishedCourse']['id']]['given_by_college_id'] : ((isset($publishedCourse['GivenByDepartment']['college_id']) ? $publishedCourse['GivenByDepartment']['college_id'] : $defaultCollege)),
																	'onchange' => 'updateDepartmentCollege(' . $publishedCourse['PublishedCourse']['id'] . ')', 'id' => 'college_id_' . $publishedCourse['PublishedCourse']['id'], 'style' => 'width:90%', 'empty' => '[ Select ]'
																)); */
																echo $this->Form->input('PublishedCourse.' . $publishedCourse['PublishedCourse']['id'] . '.given_by_department_id', array(
																	'label' => false, 
																	'options' => /* isset($publishedCourse['departments']) ? $publishedCourse['departments'] : */ $departments, 
																	'default' => (isset($this->request->data['PublishedCourse'][$publishedCourse['PublishedCourse']['id']]['given_by_department_id']) && !empty($this->request->data['PublishedCourse'][$publishedCourse['PublishedCourse']['id']]['given_by_department_id']) ? $this->request->data['PublishedCourse'][$publishedCourse['PublishedCourse']['id']]['given_by_department_id'] : (isset($publishedCourse['PublishedCourse']['given_by_department_id']) && !empty($publishedCourse['PublishedCourse']['given_by_department_id']) ? $publishedCourse['PublishedCourse']['given_by_department_id'] : $defaultDepartment)),
																	//'id' => 'department_id_' . $publishedCourse['PublishedCourse']['id'], 
																	'empty' => '[ Select to change Department ]', 'style' => 'width:90%'
																));
																$enable_dispatch_button++;
															} ?>
														</td>

													</tr>
													<?php
												} ?>
											</tbody>
										</table>
									</div>
									<br>
									<?php
								}
							} 
						}
					}  ?>
					<?= ($enable_dispatch_button ? '<hr>' . $this->Form->submit('Change/Dispatch', array('name' => 'changeDispatch', 'class' => 'tiny radius button bg-blue', 'div' => false)) : ''); ?>
					<?php
				} ?>
			</div>
		</div>
	</div>
</div>

<script type='text/javascript'>
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

	//Sub cat combo
	function updateDepartmentCollege(id) {
		//serialize form data
		var formData = $("#college_id_" + id).val();
		$("#college_id_" + id).attr('disabled', true);
		$("#department_id_" + id).attr('disabled', true);
		//get form action
		var formUrl = '/departments/get_department_combo/' + formData + '/0/1';
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data, textStatus, xhr) {
				$("#department_id_" + id).attr('disabled', false);
				$("#college_id_" + id).attr('disabled', false);
				$("#department_id_" + id).empty();
				$("#department_id_" + id).append(data);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});

		return false;
	}
</script>