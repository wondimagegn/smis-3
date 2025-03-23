<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Add Course'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<?= $this->Form->create('Course', array('novalidate' => true, 'data-abide'/* , 'onSubmit' => 'return checkForm(this);' */)); ?>
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>
				<?php 
				if (!isset($turn_off_search)) { ?>

					<blockquote>
						<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<p style="text-align:justify;">
							<span class="fs15 text-black">
								<!-- If you did'nt see some of the curriculums here, that is because the registrar locked them to prevent any further modifications once the studnets attached to it are graduated or got grades for some or all of the courses from the curriculum. <br><br> -->
								You can consult your college registrar to unlock a specific curricullum which is approved and locked before trying to add a new course to a curriculi. <br> <br> 
								<b><i>Only Curricullums which are not approved and not locked by the registrar appear here.</i></b>
							</span>
						</p> 
					</blockquote>
					<hr>

					<table cellpadding="0" cellspacing="0" class="table-borderless">
						<tr><td class="fs15"><strong>College:</strong> <?= $college_name; ?></td></tr>
						<tr><td class="fs15"><strong>Department:</strong> <?= $department_name; ?></td></tr>
						<tr><td><?= $this->Form->input('Course.curriculum_id', array('empty' => 'Select Curriculum')); ?></td></tr>
					</table>
					<hr>
					<?= $this->Form->Submit('Continue', array('name' => 'selectcurriculum', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
					<?php 
				} ?>
			</div>

			<?php 
			if (isset($this->request->data['Course']['curriculum_id']) && !empty($this->request->data['Course']['curriculum_id'])) { ?>
				<div class="large-12 columns">
					<table cellpadding="0" cellspacing="0" class="table-borderless">
						<tr>
							<td>
								<table cellpadding="0" cellspacing="0" class="table-borderless">
									<tr><td class="fs15"><strong>College:</strong> <?= $college_name; ?></td></tr>
									<tr><td class="fs15"><strong>Department:</strong> <?= $department_name; ?></td></tr>
									<tr><td class="fs15"><strong>Curriculum:</strong> <?= $curriculums[$this->request->data['Course']['curriculum_id']] ; ?></td></tr>
									<tr><td class="fs15"><strong>Program:</strong> <?=  $curriculum_program_name; ?></td></tr>
								</table>
							</td>
							
							<?php
							if ($is_there_a_course_in_selected_curriculum == 0) { ?>
								<td>
									<table cellpadding="0" cellspacing="0" class="table-borderless">
										<tr>
											<td class="vcenter" style="width: 70%;">
												<?= $this->Form->hidden('Course.curriculum_id', array('value' => $this->request->data['Course']['curriculum_id'])); ?>
												<?= $this->Form->input('form_curriculum', array('label' => 'Copy Courses From: ', 'type' => 'select', 'id' => 'formCurriculum', 'options' => $otherCurriculumList, 'empty' => '[ Select Curriculum ]', 'style' => 'width:100%;')); ?>
											</td>
											<td  class="center" style="width: 30%;">
												<br>
												<?= $this->Form->Submit('Copy Courses', array('name' => 'copycourses', 'id' => 'copyCourses', 'class' => 'tiny radius button bg-blue', 'div' => false)) ?>
											</td>
										</tr>
									</table>
								</td>		
								<?php
							} ?>
						</tr>
					</table>
				</div>

				<div class="large-6 columns">
					<table cellpadding="0" cellspacing="0" class="table-borderless">
						<tr>
							<td>
								<table cellpadding="0" cellspacing="0" class="table-borderless">
									<tbody>
										<?php
										if (empty($yearLevels)) { ?>
											<tr><td><?= $this->Form->input('year_level_id', array('empty' => "[ Select Year Level ]", 'style' => 'width:150px')); ?><a href='/yearLevels/add'>Create Year Level</a></td></tr>
											<?php
										} else { ?>
											<tr><td><?= $this->Form->input('year_level_id', array('empty' => "[ Select Year Level ]", 'style' => 'width:150px')); ?></td></tr>
											<?php
										} ?>
										<?= $this->Form->hidden('Course.curriculum_id', array('value' => isset($this->request->data['Course']['curriculum_id']) && !empty($this->request->data['Course']['curriculum_id']) ? $this->request->data['Course']['curriculum_id'] : '')); ?>
										<?= $this->Form->hidden('department_id', array('value' => $department_id)); ?>
										<tr><td><?= $this->Form->input('semester', array('type' => 'select', 'options' => Configure::read('semesters'), 'empty' => "[ Select Semester ]", 'style' => 'width:150px')); ?></td></tr>
										<tr><td><?= $this->Form->input('course_title'); ?></td></tr>
										<tr><td><?= $this->Form->input('course_code', array('id'=>'course_code','required', 'pattern' => 'course_code', 'label' => 'Course Code: <small></small></label><small class="error" style="background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Course Code is required and must begin with uppercase letter, followed by 1 to 4 alphabetic characters then a hyphen and ends with 3 to 4 digits. Like: CoSc-1021, SE-726, MAEng-6121</small>')); ?></td></tr>
										<tr><td><?= $this->Form->input('credit', array('label' => $creditname, 'pattern' => 'whole_number')); ?></td></tr>
										<tr><td><?= $this->Form->input('lecture_hours', array('pattern' => 'whole_number')); ?></td></tr>
										<tr><td><?= $this->Form->input('tutorial_hours', array('pattern' => 'whole_number')); ?></td></tr>
										<tr><td><?= $this->Form->input('laboratory_hours', array('pattern' => 'whole_number')); ?></td></tr>
										<tr><td><?= $this->Form->input('course_category_id', array('empty' => "[ Select Course/Module Category ]", 'label' => 'Course/Module Category'/* , 'style' => 'width:320px' */)); ?></td></tr>
										<tr><td style='padding-left:200px;'><?= $this->Form->input('major', array('checked' => 'checked')); ?></td></tr>
										<tr><td style='padding-left:200px;'><?= $this->Form->input('thesis', array('label' => 'Thesis/Project')); ?></td></tr>
										<tr><td style='padding-left:200px;'><?= $this->Form->input('exit_exam', array('label' => 'Exit Exam')); ?></td></tr>
										<tr><td><?= $this->Form->input('course_description'); ?></td></tr>
										<tr><td><?= $this->Form->input('course_objective'); ?></td></tr>
										<tr><td><?= $this->Form->input('lecture_attendance_requirement'); ?></td></tr>
										<tr><td><?= $this->Form->input('lab_attendance_requirement'); ?></td></tr>
										<tr><td><?= $this->Form->input('grade_type_id', array('empty' => "[ Select Grade Type ]")); ?></td></tr>
									</tbody>
								</table>
							</td>
						</tr>
					</table>
				</div>

				<div class="large-6 columns">
					<table cellpadding="0" cellspacing="0" class="table-borderless"> 
						<tr>
							<td>
								<table cellpadding="0" cellspacing="0" class="table-borderless">
									<?php
									$fields = array('prerequisite_course_id' => 1, 'co_requisite' => 2);
									$all_fields = "";
									$sep = "";

									foreach ($fields as $key => $tag) {
										$all_fields .= $sep . $key;
										$sep = ",";
									}

									$prerequisite_course_list = "";
									$se = "";

									if (!empty($prerequisite_courses)) {
										foreach ($prerequisite_courses as $pk => $pv) {
											$prerequisite_course_list .= $se . $pv;
											$se = ",";
										} 
									} ?>
									<tr><th colspan=3>Prerequisite/Co-Requisite Course</th></tr>
								</table>

								<table id='prerequisite' cellpadding="0" cellspacing="0" class="table-borderless">
									<thead>
										<tr><td>#</td><td>Prerequisite Course</td><td>Is co-requisite ? </td></tr> 
									</thead>
									<tbody>
										<?php
										if (isset($this->request->data['Prerequisite']) && count($this->request->data['Prerequisite']) > 0) {
											$count = 1;
											foreach ($this->request->data['Prerequisite'] as $ck => $cv) {
												if (!empty($cv['id'])) {
													echo $this->Form->hidden('Prerequisite.' . $ck . '.id');
													$action_model_id = 'edit~Prerequisite~' . $cv['course_id'];
												}

												$prerequisite_courses['None'] = 'none'; ?>

												<tr>
													<td><?= $count;?></td>
													<td>
														<?= $this->Form->input('Prerequisite.' . $ck . '.prerequisite_course_id', array(
															'options' => $prerequisite_courses, 'type' => 'select', 'label' => false,
															'default' => (!empty($this->request->data['Prerequisite'][$ck]['prerequisite_course_id'])) ? $this->request->data['Prerequisite'][$ck]['prerequisite_course_id'] : '',
															'style' => 'width:200px'
														)); ?>
													</td>
													<td><?= $this->Form->input('Prerequisite.' . $ck . '.co_requisite', array('label' => false)); ?></td>
												</tr>
												<?php
												$count++;
											}
										} else { ?>
											<tr>
												<td>1</td>
												<td>
													<select style='width:250px' name="data[Prerequisite][0][prerequisite_course_id]">
														<option value="">[ Select Prerequisite Course ]</option>
														<?php
														if (!empty($prerequisite_courses)) {
															foreach ($prerequisite_courses as $key => $prerequisite_course) {
																//$code=explode("-",$prerequisite_course) ?>
																<option value="<?= $key; ?>"><?= $prerequisite_course; ?> </option>
																<?php 
															}
														} ?>
														<option value="None">None</option>
													</select>
												</td>
												<td><?= $this->Form->input('Prerequisite.0.co_requisite', array('label' => false)); ?></td>
											</tr>
											<?php
										} ?>
									</tbody>
								</table>

								<table cellpadding="1" cellspacing="0" class="table-borderless">
									<tr>
										<td>
											<input type="button" value="Add Row" onclick="addRow('prerequisite','Prerequisite',2,'<?= $all_fields; ?>')" />
										</td>
										<td>
											<input type="button" value="Delete Row" onclick="deleteRow('prerequisite')" />
										</td>
									</tr>
								</table>

							</td>
						</tr>
					</table>

					<table cellpadding="0" cellspacing="0" class="table-borderless"> 
						<tr>
							<td>
								<?php
								$book_list_fields_left = array('ISBN' => '1', 'title' => 2, 'edition' => 3, 'author' => 4 );
								$all_fields_left = "";
								$sep_left = "";

								foreach ($book_list_fields_left as $key => $tag) {
									$all_fields_left .= $sep_left . $key;
									$sep_left = ",";
								}

								$book_list_fields_left = array('publisher' => 1, 'place_of_publication' => 2, 'year_of_publication' => 3);
								$all_fields_right = "";
								$sep_right = "";

								foreach ($book_list_fields_left as $key => $tag) {
									$all_fields_right .= $sep_right . $key;
									$sep_right = ",";
								}

								$fields = array('ISBN' => '1', 'title' => '2', 'publisher' => '3', 'edition' => '4', 'author' => '5', 'place_of_publication' => '6', 'year_of_publication' => '7');
								$all_fields = "";
								$sep = "";

								foreach ($fields as $key => $tag) {
									$all_fields .= $sep . $key;
									$sep = ",";
								} ?>

								<table id="book" cellpadding="0" cellspacing="0" class="table-borderless">
									<thead>
										<tr><td>#</td><td>Book Detail</td><td>Published</td></tr>
									</thead>
									<tbody>
										<?php
										if (isset($this->request->data['Book']) && count($this->request->data['Book']) > 0) { 
											$count = 1;
											foreach ($this->request->data['Book'] as $book_index => $book_value) { ?>
												<tr>
													<td><?= $count++; ?></td>
													<td>
														<table cellpadding="0" cellspacing="0" class="table-borderless">
															<tr><td><?= $this->Form->input('Book.' . $book_index . '.ISBN'); ?></td></tr>
															<tr><td><?= $this->Form->input('Book.' . $book_index . '.title', array('required' => false)); ?></td></tr>
															<tr><td><?= $this->Form->input('Book.' . $book_index . '.Autor'); ?></td></tr>
															<tr><td><?= $this->Form->input('Book.' . $book_index . '.edition'); ?></td></tr>
														</table>
													</td>
													<td>
														<table cellpadding="0" cellspacing="0" class="table-borderless">
															<tr><td><?= $this->Form->input('Book.' . $book_index . '.publisher'); ?></td></tr>
															<tr><td><?= $this->Form->input('Book.' . $book_index . '.place_of_publication'); ?></td></tr>
															<tr><td><?= $this->Form->input('Book.' . $book_index . '.year_of_publication'); ?></td></tr>
														</table>
													</td>
												</tr>
												<?php
											}
										} else { ?>
											<tr>
												<td>1</td>
												<td>
													<table cellpadding="0" cellspacing="0" class="table-borderless">
														<tr><td><?= $this->Form->input('Book.0.ISBN'); ?></td></tr>
														<tr><td><?= $this->Form->input('Book.0.title', array('required' => false)); ?></td></tr>
														<tr><td><?= $this->Form->input('Book.0.Autor'); ?></td></tr>
														<tr><td><?= $this->Form->input('Book.0.edition'); ?></td></tr>
													</table>
												</td>
												<td>
													<table cellpadding="0" cellspacing="0" class="table-borderless">
														<tr><td><?= $this->Form->input('Book.0.publisher'); ?></td></tr>
														<tr><td><?= $this->Form->input('Book.0.place_of_publication'); ?></td></tr>
														<tr><td><?= $this->Form->input('Book.0.year_of_publication', array('type' => 'text')); ?></td></tr>
													</table>
												</td>
											</tr>
											<?php
										} ?>
									</tbody>
								</table>

								<table cellpadding="0" cellspacing="0" class="table-borderless">
									<tr>
										<td>
											<input type="button" value="Add Row" onclick="addRowNew('book','Book',7,'<?= $all_fields_left; ?>','<?= $all_fields_right;  ?>')" />
										</td>
										<td>
											<input type="button" value="Delete Row" onclick="deleteRow('book')" />
										</td>
									</tr>
								</table>

							</td>
						</tr>
					</table>

					
					<table cellpadding="0" cellspacing="0" class="table-borderless"> 
						<tr>
							<td>
								<?php
								$journal_list_fields_left = array('journal_title' => '1', 'article_title' => 2, 'author' => 3, 'ISBN' => 4, 'url_address' => 4);

								$all_journal_fields_left = "";
								$sep_left_journal = "";

								foreach ($journal_list_fields_left as $jkey => $jtag) {
									$all_journal_fields_left .= $sep_left_journal . $jkey;
									$sep_left_journal = ",";
								}

								$journal_list_fields_right = array('volume' => 1, 'issue' => 2, 'page_number' => 3);

								$all_journal_fields_right = "";
								$sep_right_journal = "";

								foreach ($journal_list_fields_right as $jkey => $jtag) {
									$all_journal_fields_right .= $sep_right_journal . $jkey;
									$sep_right_journal = ",";
								}

								?>
								<table id="journal" cellpadding="0" cellspacing="0" class="table-borderless">
									<thead>
										<tr><th>#</th><th>Journal Detail</th><th>Volume</th></tr>
									</thead>
									<tbody>
										<?php
										if (isset($this->request->data['Journal']) && count($this->request->data['Journal']) > 0) {
											$count = 1;
											foreach ($this->request->data['Journal'] as $journal_index => $journal_value) { ?>
												<tr>
													<td><?= $count++; ?></td>
													<td>
														<table cellpadding="0" cellspacing="0" class="table-borderless">
															<tr><td><?= $this->Form->input('Journal.' . $journal_index . '.journal_title'); ?></td></tr>
															<tr><td><?= $this->Form->input('Journal.' . $journal_index . '.article_title'); ?></td></tr>
															<tr><td><?= $this->Form->input('Journal.' . $journal_index . '.author'); ?></td></tr>
															<tr><td><?= $this->Form->input('Journal.' . $journal_index . '.ISBN'); ?></td></tr>
															<tr><td><?= $this->Form->input('Journal.' . $journal_index . '.url_address'); ?></td></tr>
														</table>
													</td>
													<td>
														<table cellpadding="0" cellspacing="0" class="table-borderless">
															<tr><td><?= $this->Form->input('Journal.' . $journal_index . '.volume'); ?></td></tr>
															<tr><td><?= $this->Form->input('Journal.' . $journal_index . '.issue'); ?></td></tr>
															<tr><td><?= $this->Form->input('Journal.' . $journal_index . '.page_number'); ?></td></tr>
														</table>
													</td>
												</tr>
												<?php
											}
										} else { ?>
											<tr>
												<td>1</td>
												<td>
													<table cellpadding="0" cellspacing="0" class="table-borderless">
														<tr><td><?= $this->Form->input('Journal.0.journal_title', array('required' => false)); ?></td></tr>
														<tr><td><?= $this->Form->input('Journal.0.article_title', array('required' => false)); ?></td></tr>
														<tr><td><?= $this->Form->input('Journal.0.author', array('required' => false)); ?></td></tr>
														<tr><td><?= $this->Form->input('Journal.0.ISBN', array('required' => false)); ?></td></tr>
														<tr><td><?= $this->Form->input('Journal.0.url_address'); ?></td></tr>
													</table>
												</td>
												<td>
													<table cellpadding="0" cellspacing="0" class="table-borderless">
														<tr><td><?= $this->Form->input('Journal.0.volume'); ?></td></tr>
														<tr><td><?= $this->Form->input('Journal.0.issue'); ?></td></tr>
														<tr><td><?= $this->Form->input('Journal.0.page_number'); ?></td></tr>
													</table>
												</td>
											</tr>
											<?php
										} ?>
									</tbody>
								</table>
							

								<table cellpadding="0" cellspacing="0" class="table-borderless">
									<tr>
										<td>
											<input type="button" value="Add Row" onclick="addRowNew('journal','Journal',2, '<?= $all_journal_fields_left; ?>','<?= $all_journal_fields_right; ?>')" />
										</td>
										<td>
											<input type="button" value="Delete Row" onclick="deleteRow('journal')" />
										</td>
									</tr>
								</table>

							</td>
						</tr>
					</table>

					<table cellpadding="0" cellspacing="0" class="table-borderless"> 
						<tr>
							<td>
								<?php
								$web_link_fields_left = array('title' => '1', 'author' => 2);

								$all_web_link_fields_left = "";
								$sep_left_weblink = "";

								foreach ($web_link_fields_left as $wkey => $wtag) {
									$all_web_link_fields_left .= $sep_left_weblink . $wkey;
									$sep_left_weblink = ",";
								}

								$web_link_fields_right = array('url_address' => '1', 'year' => 2);
								$all_web_link_fields_right = "";
								$sep_right_weblink = "";

								foreach ($web_link_fields_right as $wrkey => $wrtag) {
									$all_web_link_fields_right .= $sep_right_weblink . $wrkey;
									$sep_right_weblink = ",";
								} ?>

								<table id="weblink" cellpadding="0" cellspacing="0" class="table-borderless" >
									<thead>
										<tr><th>#</th><th>Web Link Detail</th><th>Link</th></tr>
									</thead>
									<tbody>
										<?php
										if (isset($this->request->data['Weblink']) && count($this->request->data['Weblink']) > 0) {
											$count = 1;
											foreach ($this->request->data['Weblink'] as $weblink_index => $web_value) { ?>
												<tr>
													<td><?= $count++ ; ?></td>
													<td>
														<?php $fields = array('title' => '1', 'url_address' => '2', 'author' => '3', 'year' => '4'); ?>
														<table cellpadding="0" cellspacing="0" class="table-borderless">
															<tr><td><?= $this->Form->input('Weblink.' . $weblink_index . '.title', array('required' => false)); ?></td></tr>
															<tr><td><?= $this->Form->input('Weblink.' . $weblink_index . '.author', array('required' => false)); ?></td></tr>
														</table>
													</td>
													<td>
														<table cellpadding="0" cellspacing="0" class="table-borderless">
															<tr><td><?= $this->Form->input('Weblink.' . $weblink_index . '.url_address', array('required' => false)); ?></td></tr>
															<tr><td><?= $this->Form->input('Journal.' . $weblink_index . '.year', array('type' => 'text','required' => false)); ?></td></tr>
														</table>
													</td>
												</tr>
												<?php
											}
										} else { ?>
											<tr>
												<td>1</td>
												<td>
													<table cellpadding="0" cellspacing="0" class="table-borderless">
														<tr><td><?= $this->Form->input('Weblink.0.title', array('required' => false)); ?></td></tr>
														<tr><td><?= $this->Form->input('Weblink.0.author', array('required' => false)); ?></td></tr>
													</table>
												</td>
												<td>
													<table cellpadding="0" cellspacing="0" class="table-borderless">
														<tr><td><?= $this->Form->input('Weblink.0.url_address', array('required' => false)); ?></td></tr>
														<tr><td><?= $this->Form->input('Weblink.0.year', array('type' => 'text','required' => false)); ?></td></tr>
													</table>
												</td>
											</tr>
											<?php
										} ?>
									</tbody>
								</table>
								

								<table cellpadding="0" cellspacing="0" class="table-borderless">
									<tr>
										<td>
											<input type="button" value="Add Row" onclick="addRowNew('weblink','Weblink',2,'<?= $all_web_link_fields_left; ?>','<?= $all_web_link_fields_right; ?>')" />
										</td>
										<td>
											<input type="button" value="Delete Row" onclick="deleteRow('weblink')" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
						
					</table>
				</div>

				<div class="large-12 columns">
					<hr>
					<?= $this->Form->Submit('Add Course', array('name' => 'submit', 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue')); ?>
				</div>
				<?php 
			} ?>
		</div>
	</div>
</div>

<?= $this->Form->end(); ?>

<script type="text/javascript">

	var form_being_submitted = false;

	$('#SubmitID').click(function() {

		if ($('#copyCourses').length) {
			$('#copyCourses').attr('disabled', true);
		}

		if ($('#CourseYearLevelId').val() == '') {
			$('#CourseYearLevelId').focus();
			return false;
		}

		if ($('#CourseSemester').val() == '') {
			$('#CourseSemester').focus();
			return false;
		}

		if ($('#CourseCourseTitle').val() == '') {
			//alert('Please provide course title.');
			$('#CourseCourseTitle').focus();
			return false;
		}

		if ($('#course_code').val() == '') {
			$('#course_code').focus();
			return false;
		}

		if ($('#CourseCredit').val() == '') {
			$('#CourseCredit').focus();
			return false;
		}

		if ($('#CourseLectureHours').val() == '') {
			$('#CourseLectureHours').focus();
			return false;
		}

		if ($('#CourseTutorialHours').val() == '') {
			$('#CourseTutorialHours').focus();
			return false;
		}

		if ($('#CourseLaboratoryHours').val() == '') {
			$('#CourseLaboratoryHours').focus();
			return false;
		}

		if ($('#CourseCourseCategoryId').val() == '') {
			$('#CourseCourseCategoryId').focus();
			return false;
		}

		if ($('#CourseGradeTypeId').val() == '') {
			$('#CourseGradeTypeId').focus();
			return false;
		}

		if (form_being_submitted) {
			alert("Adding Course, please wait a moment...");
			$('#SubmitID').attr('disabled', true);
			return false;
		}

		$('#SubmitID').val('Adding Course...');
		form_being_submitted = true;

	});

	$('#copyCourses').click(function() {

		if ($('#SubmitID').length) {
			$('#SubmitID').attr('disabled', true);
		}

		$('form input, form select, form textarea').removeAttr('required');

		var selectedCurriculumID = $('#formCurriculum').val();

		//alert(selectedCurriculumID);

		if (selectedCurriculumID == '') {
			alert('Please select the Curriculum to copy courses from.');
			$('#formCurriculum').focus();
			return false;
		}

		
		if (form_being_submitted) {
			alert("Copying courses, please wait a moment...");
			$('#copyCourses').attr('disabled', true);
			return false;
		}

		$('#copyCourses').val('Copying Courses...');
		form_being_submitted = true;

	});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
	
</script>

<script type="text/javascript">

	var prerequisite_courses = Array();
	var index = 0;

	<?php
	if (!empty($prerequisite_courses)) {
		foreach ($prerequisite_courses as $course_id => $course_name) { ?>
			index = prerequisite_courses.length;
			prerequisite_courses[index] = new Array();
			prerequisite_courses[index][0] = "<?= $course_id; ?>";
			prerequisite_courses[index][1] = "<?= $course_name; ?>";
		<?php
		}
	} ?>

	function addRowNew(tableID, model, no_of_fields, left_fields, right_fields) {
		var elementArrayLeft = left_fields.split(',');
		var elementArrayRight = right_fields.split(',');

		var table = document.getElementById(tableID);

		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);

		var cell0 = row.insertCell(0);
		cell0.innerHTML = rowCount;
		var left = 1;
		var right = 2;

		for (var i = 1; i <= no_of_fields; i++) {
			var cell = row.insertCell(i);
			//construct left_fields
			if (left == i) {

				for (var l = 1; l <= elementArrayLeft.length; l++) {
					if (elementArrayLeft[l - 1] == "ISBN") {
						var text = document.createTextNode('ISBN');
						var td1 = document.createElement('td');
						td1.appendChild(text);
						var tr1 = document.createElement('tr');
						tr1.appendChild(td1);
						cell.appendChild(tr1);
						var td2 = document.createElement('td');
						var element = document.createElement("input");
						element.type = "text";

						element.name = "data[" + model + "][" + rowCount + "][" + elementArrayLeft[l - 1] + "]";
						td2.appendChild(element);
						var tr2 = document.createElement('tr');
						tr2.appendChild(td2);
						cell.appendChild(tr2);
					}

					if (elementArrayLeft[l - 1] == "title") {
						var text = document.createTextNode('Title');
						var td1 = document.createElement('td');
						td1.appendChild(text);
						var tr1 = document.createElement('tr');
						tr1.appendChild(td1);
						cell.appendChild(tr1);
						var td2 = document.createElement('td');
						var element = document.createElement("input");
						element.type = "text";

						element.name = "data[" + model + "][" + rowCount + "][" + elementArrayLeft[l - 1] + "]";
						td2.appendChild(element);
						var tr2 = document.createElement('tr');
						tr2.appendChild(td2);
						cell.appendChild(tr2);
					}

					if (elementArrayLeft[l - 1] == "edition") {
						var text = document.createTextNode('Edition');
						var td1 = document.createElement('td');
						td1.appendChild(text);
						var tr1 = document.createElement('tr');
						tr1.appendChild(td1);
						cell.appendChild(tr1);
						var td2 = document.createElement('td');
						var element = document.createElement("input");
						element.type = "text";

						element.name = "data[" + model + "][" + rowCount + "][" + elementArrayLeft[l - 1] + "]";
						td2.appendChild(element);
						var tr2 = document.createElement('tr');
						tr2.appendChild(td2);
						cell.appendChild(tr2);
					}

					if (elementArrayLeft[l - 1] == "author") {
						var text = document.createTextNode('Author');
						var td1 = document.createElement('td');
						td1.appendChild(text);
						var tr1 = document.createElement('tr');
						tr1.appendChild(td1);
						cell.appendChild(tr1);
						var td2 = document.createElement('td');
						var element = document.createElement("input");
						element.type = "text";

						element.name = "data[" + model + "][" + rowCount + "][" + elementArrayLeft[l - 1] + "]";
						td2.appendChild(element);
						var tr2 = document.createElement('tr');
						tr2.appendChild(td2);
						cell.appendChild(tr2);
					}

					if (elementArrayLeft[l - 1] == "journal_title") {
						var text = document.createTextNode('Journal Title');
						var td1 = document.createElement('td');
						td1.appendChild(text);
						var tr1 = document.createElement('tr');
						tr1.appendChild(td1);
						cell.appendChild(tr1);
						var td2 = document.createElement('td');
						var element = document.createElement("input");
						element.type = "text";

						element.name = "data[" + model + "][" + rowCount + "][" + elementArrayLeft[l - 1] + "]";
						td2.appendChild(element);
						var tr2 = document.createElement('tr');
						tr2.appendChild(td2);
						cell.appendChild(tr2);
					}

					if (elementArrayLeft[l - 1] == "article_title") {
						var text = document.createTextNode('Article Title');
						var td1 = document.createElement('td');
						td1.appendChild(text);
						var tr1 = document.createElement('tr');
						tr1.appendChild(td1);
						cell.appendChild(tr1);
						var td2 = document.createElement('td');
						var element = document.createElement("input");
						element.type = "text";

						element.name = "data[" + model + "][" + rowCount + "][" + elementArrayLeft[l - 1] + "]";
						td2.appendChild(element);
						var tr2 = document.createElement('tr');
						tr2.appendChild(td2);
						cell.appendChild(tr2);
					}

					if (elementArrayLeft[l - 1] == "url_address") {
						var text = document.createTextNode('URL Address');
						var td1 = document.createElement('td');
						td1.appendChild(text);
						var tr1 = document.createElement('tr');
						tr1.appendChild(td1);
						cell.appendChild(tr1);
						var td2 = document.createElement('td');
						var element = document.createElement("input");
						element.type = "text";

						element.name = "data[" + model + "][" + rowCount + "][" + elementArrayLeft[l - 1] + "]";
						td2.appendChild(element);
						var tr2 = document.createElement('tr');
						tr2.appendChild(td2);
						cell.appendChild(tr2);
					}

					var table = document.createElement('TABLE');
					table.appendChild(tr1);
					table.appendChild(tr2);
					cell.appendChild(table);
				} // end of foreach
			} // end of left fields

			if (right == i) {
				for (var r = 1; r <= elementArrayRight.length; r++) {
					if (elementArrayRight[r - 1] == "publisher") {
						var text = document.createTextNode('Publisher');
						var td1 = document.createElement('td');
						td1.appendChild(text);
						var tr1 = document.createElement('tr');
						tr1.appendChild(td1);
						cell.appendChild(tr1);
						var td2 = document.createElement('td');
						var element = document.createElement("input");
						element.type = "text";

						element.name = "data[" + model + "][" + rowCount + "][" + elementArrayRight[r - 1] + "]";
						td2.appendChild(element);
						var tr2 = document.createElement('tr');
						tr2.appendChild(td2);
						cell.appendChild(tr2);
					}

					if (elementArrayRight[r - 1] == "place_of_publication") {
						var text = document.createTextNode('Place of Publication');
						var td1 = document.createElement('td');
						td1.appendChild(text);
						var tr1 = document.createElement('tr');
						tr1.appendChild(td1);
						cell.appendChild(tr1);
						var td2 = document.createElement('td');
						var element = document.createElement("input");
						element.type = "text";

						element.name = "data[" + model + "][" + rowCount + "][" + elementArrayRight[r - 1] + "]";
						td2.appendChild(element);
						var tr2 = document.createElement('tr');
						tr2.appendChild(td2);
						cell.appendChild(tr2);
					}

					//year_of_publication
					if (elementArrayRight[r - 1] == "year_of_publication") {
						var text = document.createTextNode('Year of Publication');
						var td1 = document.createElement('td');
						td1.appendChild(text);
						var tr1 = document.createElement('tr');
						tr1.appendChild(td1);
						cell.appendChild(tr1);
						var td2 = document.createElement('td');
						var element = document.createElement("input");
						element.type = "text";

						element.name = "data[" + model + "][" + rowCount + "][" + elementArrayRight[r - 1] + "]";
						td2.appendChild(element);
						var tr2 = document.createElement('tr');
						tr2.appendChild(td2);
						cell.appendChild(tr2);
					}

					//year
					if (elementArrayRight[r - 1] == "year") {
						var text = document.createTextNode('Year');
						var td1 = document.createElement('td');
						td1.appendChild(text);
						var tr1 = document.createElement('tr');
						tr1.appendChild(td1);
						cell.appendChild(tr1);
						var td2 = document.createElement('td');
						var element = document.createElement("input");
						element.type = "text";

						element.name = "data[" + model + "][" + rowCount + "][" + elementArrayRight[r - 1] + "]";
						td2.appendChild(element);
						var tr2 = document.createElement('tr');
						tr2.appendChild(td2);
						cell.appendChild(tr2);
					}

					if (elementArrayRight[r - 1] == "url_address") {
						var text = document.createTextNode('URL Address');
						var td1 = document.createElement('td');
						td1.appendChild(text);
						var tr1 = document.createElement('tr');
						tr1.appendChild(td1);
						cell.appendChild(tr1);
						var td2 = document.createElement('td');
						var element = document.createElement("input");
						element.type = "text";

						element.name = "data[" + model + "][" + rowCount + "][" + elementArrayRight[r - 1] + "]";
						td2.appendChild(element);
						var tr2 = document.createElement('tr');
						tr2.appendChild(td2);
						cell.appendChild(tr2);
					}

					if (elementArrayRight[r - 1] == "volume") {
						var text = document.createTextNode('Volume');
						var td1 = document.createElement('td');
						td1.appendChild(text);
						var tr1 = document.createElement('tr');
						tr1.appendChild(td1);
						cell.appendChild(tr1);
						var td2 = document.createElement('td');
						var element = document.createElement("input");
						element.type = "text";

						element.name = "data[" + model + "][" + rowCount + "][" + elementArrayRight[r - 1] + "]";
						td2.appendChild(element);
						var tr2 = document.createElement('tr');
						tr2.appendChild(td2);
						cell.appendChild(tr2);
					}

					if (elementArrayRight[r - 1] == "issue") {
						var text = document.createTextNode('Issue');
						var td1 = document.createElement('td');
						td1.appendChild(text);
						var tr1 = document.createElement('tr');
						tr1.appendChild(td1);
						cell.appendChild(tr1);
						var td2 = document.createElement('td');
						var element = document.createElement("input");
						element.type = "text";

						element.name = "data[" + model + "][" + rowCount + "][" + elementArrayRight[r - 1] + "]";
						td2.appendChild(element);
						var tr2 = document.createElement('tr');
						tr2.appendChild(td2);
						cell.appendChild(tr2);
					}

					if (elementArrayRight[r - 1] == "page_number") {
						var text = document.createTextNode('Page Number');
						var td1 = document.createElement('td');
						td1.appendChild(text);
						var tr1 = document.createElement('tr');
						tr1.appendChild(td1);
						cell.appendChild(tr1);
						var td2 = document.createElement('td');
						var element = document.createElement("input");
						element.type = "text";

						element.name = "data[" + model + "][" + rowCount + "][" + elementArrayRight[r - 1] + "]";
						td2.appendChild(element);
						var tr2 = document.createElement('tr');
						tr2.appendChild(td2);
						cell.appendChild(tr2);
					}
				}
			} // end of right fields 
		} // end for each

	}

	function addRow(tableID, controller, no_of_fields, all_fields) {
		var elementArray = all_fields.split(',');
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		var cell0 = row.insertCell(0);
		cell0.innerHTML = rowCount;
		for (var i = 1; i <= no_of_fields; i++) {
			var cell = row.insertCell(i);
			var element = document.createElement("input");
			element.type = "text";
			if (controller == "Book") {
				element.size = "13";
			} else if (controller == "Journal") {
				element.size = "10";
			} else if (controller == "Weblink") {
				element.size = "30";
			} else if (elementArray[i - 1] == "prerequisite_course_id") {
				var element = document.createElement("select");
				var string = '<option value="">[ Select Prerequisite Course ]</option>';
				for (var f = 0; f < prerequisite_courses.length; f++) {
					string += '<option value="' + prerequisite_courses[f][0] + '">' + prerequisite_courses[f][1] + '</option>';
				}
				string += '<option value="">None</option>';
				element.style.width = "200px";
				element.innerHTML = string;
			} else if (elementArray[i - 1] == 'co_requisite') {
				var element = document.createElement("input");
				element.value = 1;
				element.type = "checkbox";
			}
			element.name = "data[" + controller + "][" + rowCount + "][" + elementArray[i - 1] + "]";
			cell.appendChild(element);
		}

	}

	/* function deleteRow(tableID) {
		try {
			var table = document.getElementById(tableID);
			var rowCount = table.rows.length;
			if (rowCount != 0) {
				table.deleteRow(rowCount - 1);
			} else {

				alert('No more rows to delete');
			}

		} catch (e) {
			alert(e);
		}
	} */

	function deleteRow(tableID) {
		try {
			var table = document.getElementById(tableID);
			var rowCount = table.rows.length;
			if (rowCount != 2) {
				table.deleteRow(rowCount - 1);
			} else {
				//alert('No more rows to delete');
			}

		} catch (e) {
			alert(e);
		}
	}
</script>