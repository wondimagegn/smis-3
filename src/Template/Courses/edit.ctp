<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Edit Course: ' . (isset($this->request->data['Course']['course_code_title']) ? $this->request->data['Course']['course_code_title'] :(isset($course_code_title) ? $course_code_title : '')); ?></span>
        </div>
    </div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div style="margin-top: -30px;"><hr></div>

				<div class="courses form">
					<?= $this->Form->create('Course', array('novalidate' => true, 'data-abide'/* , 'onSubmit' => 'return checkForm(this);' */)); ?>
					<table cellpadding="0" cellspacing="0" class="table-borderless">
						<tr>
							<td>
								<?php
								echo '<table cellpadding="0" cellspacing="0" class="table-borderless"><tbody>';
								echo $this->Form->input('id');
								echo "<tr><td>" . $this->Form->input('year_level_id', array('empty' => "[ Select Year Level ]")) . "</td></tr>";
								echo "<tr><td>" . $this->Form->input('semester', array('type' => 'select', 'options' => Configure::read('semesters'), 'empty' => "[ Select Semester ]")) . "</td></tr>";

								echo $this->Form->hidden('Course.curriculum_id', array('value' => $this->request->data['Course']['curriculum_id']));

								if ($editCreditDetail == 0) {
									echo "<tr><td>" . $this->Form->input('course_title') . "</td></tr>";
									echo "<tr><td>" . $this->Form->input('course_code', array('id'=>'course_code','required', 'pattern' => 'course_code', 'label' => 'Course Code: <small></small></label><small class="error" style="background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Course Code is required and must begin with uppercase letter, followed by 1 to 4 alphabetic characters then a hyphen and ends with 3 to 4 digits. Like: CoSc-1021, SE-726, MAEng-6121</small>')) . "</td></tr>";
								} else {
									echo "<tr><td>Editing this course title, course code and credit is disabled since there are students who are graduated with this course.</td></tr>";
									echo "<tr><td> Course Title: " . $this->request->data['Course']['course_title'] . "</td></tr>";
									echo "<tr><td> Course Code: " . $this->request->data['Course']['course_code'] . "</td></tr>";
									echo $this->Form->hidden('course_title');
									echo $this->Form->hidden('course_code');
								}
								
								if ($editCredit == 0) {
									echo "<tr><td>" . $this->Form->input('credit', array('label' => $creditname, 'pattern' => 'whole_number')) . "</td></tr>";
								} else {
									echo "<tr><td>Editing this course " . $creditname . " is not allowed since there are students who got grade with their result computed.</td></tr>";
									echo "<tr><td>" . $creditname . ': ' . $this->request->data['Course']['credit'] . "</td></tr>";
									echo $this->Form->hidden('credit');
								}

								echo "<tr><td>" . $this->Form->input('lecture_hours', array('pattern' => 'whole_number')) . "</td></tr>";
								echo "<tr><td>" . $this->Form->input('tutorial_hours', array('pattern' => 'whole_number')) . "</td></tr>";
								echo "<tr><td>" . $this->Form->input('laboratory_hours', array('pattern' => 'whole_number')) . "</td></tr>";
								echo "<tr><td>" . $this->Form->input('course_category_id', array('empty' => "[ Select Course Category ]")) . "</td></tr>";

								if ($this->request->data['Course']['major'] == 1) {
									echo "<tr><td style='padding-left:200px;'>" . $this->Form->input('major', array('checked' => 'checked')) . "</td></tr>";
								} else {
									echo "<tr><td style='padding-left:200px;'>" . $this->Form->input('major') . "</td></tr>";
								}

								if ($this->request->data['Course']['thesis'] == 1) {
									echo "<tr><td style='padding-left:200px;'>" . $this->Form->input('thesis', array('checked' => 'checked')) . "</td></tr>";
								} else {
									echo "<tr><td style='padding-left:200px;'>" . $this->Form->input('thesis') . "</td></tr>";
								}

								if ($this->request->data['Course']['exit_exam'] == 1) {
									echo "<tr><td style='padding-left:200px;'>" . $this->Form->input('exit_exam', array('checked' => 'checked')) . "</td></tr>";
								} else {
									echo "<tr><td style='padding-left:200px;'>" . $this->Form->input('exit_exam') . "</td></tr>";
								}

								if ($this->request->data['Course']['elective'] == 1) {
									echo "<tr><td style='padding-left:200px;'>" . $this->Form->input('elective', array('checked' => 'checked')) . "</td></tr>";
								} else {
									echo "<tr><td style='padding-left:200px;'>" . $this->Form->input('elective') . "</td></tr>";
								}

								echo '</tbody></table></td><td><table cellpadding="0" cellspacing="0" class="table-borderless"><tbody>';
								echo "<tr><td>" . $this->Form->input('course_description') . "</td></tr>";
								echo "<tr><td>" . $this->Form->input('course_objective') . "</td></tr>";
								echo $this->Form->hidden('department_id', array('value' => $department_id));
								echo "<tr><td>" . $this->Form->input('lecture_attendance_requirement') . "</td></tr>";
								echo "<tr><td>" . $this->Form->input('lab_attendance_requirement') . "</td></tr>";
								echo "<tr><td>" . $this->Form->input('grade_type_id', array('empty' => "[ Select Grade Type ]")) . "</td></tr>";
								echo "</tbody></table>";
								?>
							</td>
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
							<table id="prerequisite" cellpadding="0" cellspacing="0" class="table-borderless">
								<tr>
									<th>#</th>
									<th>Prerequisite Course</th>
									<th>Is co-requisite ? </th>
									<?php
									if (!empty($this->request->data['Prerequisite'])) {
										echo '<th>';
										echo 'Action';
										echo '</th>';
									} else {
										echo '<th></th>';
									} ?>
								</tr>
								<?php

								if (isset($this->request->data['Prerequisite']) && count($this->request->data['Prerequisite']) > 0) {
									$count = 1;
									foreach ($this->request->data['Prerequisite'] as $ck => $cv) {
										if (!empty($cv['id'])) {
											echo $this->Form->hidden('Prerequisite.' . $ck . '.id');
											$action_model_id = 'edit~Prerequisite~' . $cv['course_id'];
										}

										echo "<tr><td>" . $count . "</td><td>" . $this->Form->input(
											'Prerequisite.' . $ck . '.prerequisite_course_id',
											array(
												'options' => $prerequisite_courses, 'type' => 'select', 'label' => false,
												'default' => (!empty($this->request->data['Prerequisite'][$ck]['prerequisite_course_id'])) ? $this->request->data['Prerequisite'][$ck]['prerequisite_course_id'] : '',
												//'style' => 'width:200px'
											)
										);

										echo "</td>";
										echo '<td>';
										echo $this->Form->input('Prerequisite.' . $ck . '.co_requisite', array('label' => false));
										echo '</td>';

										
										echo '<td>';

										if (!empty($action_model_id)) {
											echo $this->Html->link( __('Delete'), array('action' => 'deleteChildren', $cv['id'], $action_model_id), null, sprintf(__('Are you sure you want to delete # %s?'), $cv['id']));
										}
										echo "</td>";

										echo '</tr>';
										$count++;
									}
								} else { ?>
									<tr>
										<td> 1 </td>
										<td>
											<select style="width:200px" name="data[Prerequisite][0][prerequisite_course_id]">
												<option value="">[ Select Prerequisite Course ]</option>
												<?php 
												if (!empty($prerequisite_courses)) {
													foreach ($prerequisite_courses as $key => $prerequisite_course) { ?>
														<option value="<?= $key; ?>"><?= $prerequisite_course; ?> </option>
														<?php 
													}
												} ?>
											</select>
										</td>
										<td>
											<?= $this->Form->input('Prerequisite.0.co_requisite', array('label' => false)); ?>
										</td>
										<td></td>
									</tr>
									<?php
								} ?>
							</table>

							<table cellpadding="0" cellspacing="0" class="table-borderless">
								<tr>
									<td>
										<input type="button" value="Add Row" onclick="addRow('prerequisite','Prerequisite',2, '<?= $all_fields; ?>')" />
									</td>
									<td>
										<input type="button" value="Delete Row" onclick="deleteRow('prerequisite')" />
									</td>
								</tr>
							</table>

							<?php
							$book_list_fields_left = array('ISBN' => '1', 'title' => 2, 'edition' => 3, 'author' => 4);
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
							} ?>


							<table id="book" cellpadding="0" cellspacing="0" class="table-borderless" >
								<?php
								echo "<tr><th align='right'>#</th><th align='right'>Book Detail</th><th align='right'>Publish</th>";

								if (!empty($this->request->data['Book'])) {
									echo '<th>';
									echo 'Action';
									echo '</th>';
								}

								echo "</tr>";
								if (isset($this->request->data['Book']) && count($this->request->data['Book']) > 0) {
									$count = 1;
									foreach ($this->request->data['Book'] as $book_index => $book_value) {

										if (!empty($book_value['id'])) {
											echo $this->Form->input('Book.' . $book_index . '.id');
											$action_book_id = 'edit~Book~' . $book_value['course_id'];
										}

										echo "<tr>";
										echo '<td>' . $count++ . '</td>';
										echo '<td>';
										echo '<table cellpadding="0" cellspacing="0" class="table-borderless">';
										echo '<tr><td>' . $this->Form->input('Book.' . $book_index . '.ISBN') . '</td></tr>';
										echo '<tr><td>' . $this->Form->input('Book.' . $book_index . '.title', array('required'=>false)) . '</td></tr>';
										echo '<tr><td>' . $this->Form->input('Book.' . $book_index . '.author') . '</td></tr>';
										echo '<tr><td>' . $this->Form->input('Book.' . $book_index . '.edition') . '</td></tr>';
										echo '</table>';
										echo '</td>';
										echo '<td>';
										echo '<table cellpadding="0" cellspacing="0" class="table-borderless">';
										echo '<tr><td>' . $this->Form->input('Book.' . $book_index . '.publisher') . '</td></tr>';
										echo '<tr><td>' . $this->Form->input('Book.' . $book_index . '.place_of_publication') . '</td></tr>';
										echo '<tr><td>' . $this->Form->input('Book.' . $book_index . '.year_of_publication', array('type' => 'text')) . '</td></tr>';
										echo '</table>';

										echo '</td>';

										if (!empty($action_book_id)) {
											echo "<td>" . $this->Html->link(__('Delete'), array('action' => 'deleteChildren', $book_value['id'], $action_book_id), null, sprintf(__('Are you sure you want to delete # %s?'), $book_value['id']) ) . '</td>';
										} else {
											echo '<td></td>';
										}

										echo "</tr>";
									}
								} else {
									echo "<tr>";
									echo '<td>1';
									echo '</td>';
									echo '<td>';
									echo '<table cellpadding="0" cellspacing="0" class="table-borderless">';
									echo '<tr><td>' . $this->Form->input('Book.0.ISBN') . '</td></tr>';
									echo '<tr><td>' . $this->Form->input('Book.0.title', array('required'=>false)) . '</td></tr>';
									echo '<tr><td>' . $this->Form->input('Book.0.author') . '</td></tr>';
									echo '<tr><td>' . $this->Form->input('Book.0.edition') . '</td></tr>';
									echo '</table>';
									echo '</td>';
									echo '<td>';
									echo '<table cellpadding="0" cellspacing="0" class="table-borderless">';
									echo '<tr><td>' . $this->Form->input('Book.0.publisher') . '</td></tr>';
									echo '<tr><td>' . $this->Form->input('Book.0.place_of_publication') . '</td></tr>';
									echo '<tr><td>' . $this->Form->input('Book.0.year_of_publication', array('type' => 'text')) . '</td></tr>';
									echo '</table>';

									echo '</td>';
									echo "</tr>";
								} ?>

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

							<?php

							$journal_list_fields_left = array('journal_title' => '1', 'article_title' => 2, 'author' => 3, 'ISBN' => 4, 'url_address' => 4 );

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
							} ?>

							<table id="journal" cellpadding="0" cellspacing="0" class="table-borderless">
								<?php
								echo "<tr><th>#</th><th>Journal Detail</th><th>Volume</th>";
								if (!empty($this->request->data['Journal'])) {
									echo '<th>';
									echo 'Action';
									echo '</th>';
								}
								echo "</tr>";

								if (isset($this->request->data['Journal']) && count($this->request->data['Journal']) > 0) {
									$count = 1;

									foreach ($this->request->data['Journal'] as $journal_index => $journal_value) {
										
										if (!empty($journal_value['id'])) {
											echo $this->Form->hidden('Journal.' . $journal_index . '.id', array('value' => $this->request->data['Journal'][$journal_index]['id']));
											$action_journal_id = 'edit~Journal~' . $journal_value['course_id'];
										}

										echo "<tr>";
										echo '<td>' . $count++ . '</td>';
										echo '<td>';
										echo '<table cellpadding="0" cellspacing="0" class="table-borderless">';
										echo '<tr><td>' . $this->Form->input('Journal.' . $journal_index . '.journal_title', array('required'=>false)) . '</td></tr>';
										echo '<tr><td>' . $this->Form->input('Journal.' . $journal_index . '.article_title', array('required'=>false)) . '</td></tr>';
										echo '<tr><td>' . $this->Form->input('Journal.' . $journal_index . '.author', array('required'=>false)) . '</td></tr>';
										echo '<tr><td>' . $this->Form->input('Journal.' . $journal_index . '.ISBN') . '</td></tr>';
										echo '<tr><td>' . $this->Form->input('Journal.' . $journal_index . '.url_address') . '</td></tr>';
										echo '</table>';
										echo '</td>';
										echo '<td>';

										echo '<table cellpadding="0" cellspacing="0" class="table-borderless">';
										echo '<tr><td>' . $this->Form->input('Journal.' . $journal_index . '.volume') . '</td></tr>';
										echo '<tr><td>' . $this->Form->input('Journal.' . $journal_index . '.issue') . '</td></tr>';
										echo '<tr><td>' . $this->Form->input('Journal.' . $journal_index . '.page_number') . '</td></tr>';
										echo '</table>';

										echo '</td>';

										if (!empty($action_journal_id)) {
											echo "<td>" . $this->Html->link(__('Delete'), array('action' => 'deleteChildren', $journal_value['id'], $action_journal_id), null, sprintf(__('Are you sure you want to delete # %s?'), $journal_value['id'])) . "</td>";
										} else {
											echo '<td></td>';
										}

										echo "</tr>";
									}
								} else {

									echo "<tr>";
									echo '<td>1</td>';
									echo '<td>';
									echo '<table cellpadding="0" cellspacing="0" class="table-borderless">';
									echo '<tr><td>' . $this->Form->input('Journal.0.journal_title', array('required'=>false)) . '</td></tr>';
									echo '<tr><td>' . $this->Form->input('Journal.0.article_title', array('required'=>false)) . '</td></tr>';
									echo '<tr><td>' . $this->Form->input('Journal.0.author', array('required'=>false)) . '</td></tr>';
									echo '<tr><td>' . $this->Form->input('Journal.0.ISBN') . '</td></tr>';
									echo '<tr><td>' . $this->Form->input('Journal.0.url_address') . '</td></tr>';
									echo '</table>';
									echo '</td>';
									echo '<td>';

									echo '<table cellpadding="0" cellspacing="0" class="table-borderless">';
									echo '<tr><td>' . $this->Form->input('Journal.0.volume') . '</td></tr>';
									echo '<tr><td>' . $this->Form->input('Journal.0.issue') . '</td></tr>';
									echo '<tr><td>' . $this->Form->input('Journal.0.page_number') . '</td></tr>';
									echo '</table>';

									echo '</td>';
									echo "</tr>";
								}
								?>
							</table>

							<table>
								<tr>
									<td>
										<input type="button" value="Add Row" onclick="addRowNew('journal','Journal',2,'<?= $all_journal_fields_left; ?>','<?= $all_journal_fields_right; ?>')" />
									</td>
									<td>
										<input type="button" value="Delete Row" onclick="deleteRow('journal')" />
									</td>
								</tr>
							</table>

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

							<table id="weblink" cellpadding="0" cellspacing="0" class="table-borderless">
								<?php
								echo "<tr><th>#</th><th>Web Link Detail</th><th>Link</th>";
								if (!empty($this->request->data['Weblink'])) {
									echo '<th>';
									echo 'Action';
									echo '</th>';
								}
								echo "</tr>";
								if (isset($this->request->data['Weblink']) && count($this->request->data['Weblink']) > 0) {
									$count = 1;
									foreach ($this->request->data['Weblink'] as $weblink_index => $web_value) {

										if (!empty($web_value['id'])) {
											echo $this->Form->input('Weblink.' . $weblink_index . '.id');
											$action_weblink_id = 'edit~Weblink~' . $web_value['course_id'];
										}

										echo "<tr>";
										echo '<td>' . $count++ . '</td>';
										echo '<td>';
										$fields = array('title' => '1', 'url_address' => '2', 'author' => '3', 'year' => '4');
										echo '<table cellpadding="0" cellspacing="0" class="table-borderless">';
										echo '<tr><td>' . $this->Form->input('Weblink.' . $weblink_index . '.title', array('required'=>false)) . '</td></tr>';
										echo '<tr><td>' . $this->Form->input('Weblink.' . $weblink_index . '.author', array('required'=>false)) . '</td></tr>';

										echo '</table>';
										echo '</td>';
										echo '<td>';

										echo '<table cellpadding="0" cellspacing="0" class="table-borderless">';
										echo '<tr><td>' . $this->Form->input('Weblink.' . $weblink_index . '.url_address', array('required'=>false)) . '</td></tr>';
										echo '<tr><td>' . $this->Form->input('Journal.' . $weblink_index . '.year', array('type' => 'text','required'=>false)) . '</td></tr>';

										echo '</table>';
										echo '</td>';

										if (!empty($action_weblink_id)) {
											echo '<td>' . $this->Html->link(__('Delete'), array('action' => 'deleteChildren', $web_value['id'], $action_weblink_id ), null, sprintf(__('Are you sure you want to delete # %s?'), $web_value['url_address'])) . '</td>';
										} else {
											echo '<td></td>';
										}

										echo "</tr>";
									}
								} else {

									echo "<tr>";
									echo '<td>1</td>';
									echo '<td>';

									echo '<table cellpadding="0" cellspacing="0" class="table-borderless">';
									echo '<tr><td>' . $this->Form->input('Weblink.0.title', array('required'=>false)) . '</td></tr>';
									echo '<tr><td>' . $this->Form->input('Weblink.0.author', array('required'=>false)) . '</td></tr>';
									echo '</table>';
									echo '</td>';
									echo '<td>';

									echo '<table cellpadding="0" cellspacing="0" class="table-borderless">';
									echo '<tr><td>' . $this->Form->input('Weblink.0.url_address', array('required'=>false)) . '</td></tr>';
									echo '<tr><td>' . $this->Form->input('Weblink.0.year', array('type' => 'text','required'=>false)) . '</td></tr>';
									echo '</table>';

									echo '</td>';
									echo "</tr>";
								} ?>

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
				<hr>
				<?= $this->Form->Submit('Save Changes', array('name' => 'submit', 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>						
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

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
		cell0.innerHTML = rowCount + 1;

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
				//element.style.width = "200px";
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