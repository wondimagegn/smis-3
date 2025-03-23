<?php
if (!empty($student_detail['Curriculum'])) { 
	$grades = Configure::read('exemptedCourseGradesOptions');
	?>
	<SCRIPT language="javascript">
		var courses = Array();
		var courses_combo = '';
		var index = 0;
		var index2 = 0;
		var grades = Array();
		var grades_combo = "<option value=''><?= '[ Select ]'; ?></option>";;
		

		<?php
		if (isset($courses) && !empty($courses)) {
			foreach ($courses as $course_id => $course_name) { ?>
				index = courses.length;
				courses[index] = new Array();
				courses[index][0] = "<?= $course_id; ?>";
				courses[index][1] = "<?= $course_name; ?>";
				courses_combo += "<option value='<?= $course_id; ?>'><?= $course_name; ?></option>";
				<?php
			} 
		} ?>

		<?php
		if (isset($grades) && !empty($grades)) {
			foreach ($grades as $grades_id => $grades_name) { ?>
				index2 = grades.length;
				grades[index2] = new Array();
				grades[index2][0] = "<?= $grades_id; ?>";
				grades[index2][1] = "<?= $grades_name; ?>";
				grades_combo += "<option value='<?= $grades_id; ?>'><?= $grades_name; ?></option>";
				<?php
			} 
		} ?>

		var totalRow = <?= (!empty($this->request->data) ? (count($this->request->data['CourseExemption'])) : (!empty($exemptedCourseLists) ? (count($exemptedCourseLists)) : '2')); ?>;

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

			// added for limiting the total row, comment the if () { and the closing curly bracket it if not working properly, Neway
			if (table.rows.length <= <?= (count($courses) + count($exemptedCourseLists) + 1); ?>) {

				var cell0 = row.insertCell(0);
				
				cell0.innerHTML = rowCount;
				cell0.classList.add("center");

				//construct the other cells
				
				for (var j = 1; j <= no_of_fields; j++) {
					var cell = row.insertCell(j);

					var div = document.createElement("div");
					div.style.marginTop = "10px";

					if (elementArray[j - 1] == "course_id") {
						var element = document.createElement("select");
						string = "";
						for (var f = 0; f < courses.length; f++) {
							string += '<option value="' + courses[f][0] + '"> ' + courses[f][1] + '</option>';
						}
						element.id = "course_id_" + rowCount;
						//element.innerHTML = string;
						element.innerHTML = courses_combo;

						// added by Neway
						element.style = "width:100%";
						element.onchange = function() {
							updateCourseDetailsOnChange(this);
						};
						element.required = "required";
						// end Added By Neway

					} else if (elementArray[j - 1] == 'taken_course_title') {
						var element = document.createElement("input");
						//element.size = "20";
						element.type = "text";
						element.required = "required";
						element.id = "takenCourseTitle_" + rowCount;

					} else if (elementArray[j - 1] == "taken_course_code") {
						var element = document.createElement("input");
						//element.size = "4";
						element.type = "text";
						element.required = "required";
						element.id = "takenCourseCode_" + rowCount;
						//var pattern = '/^[A-Z][a-zA-Z]{1,3}-\d{3,4}$/'; //original
						var pattern = '^[A-Z][a-zA-Z]{1,3}-\\d{3,4}$';
						element.setAttribute('pattern', pattern);
						element.setAttribute('title', 'Ensure the Course code starts with a Capital letter followed by 1 to 3 letters, then a hyphen and 3 to 4 digits(numbers)');
					} else if (elementArray[j - 1] == "course_taken_credit") {
						var element = document.createElement("input");
						//element.size = "4";
						element.type = "number";
						element.required = "required";
						element.id = "courseTakenCredit_" + rowCount;
						element.onchange = function() {
							checkCreditRange(this);
						};

					} else if (elementArray[j - 1] == "grade") {
						var element = document.createElement("select");
						string2 = "";
						for (var f = 0; f < grades.length; f++) {
							string2 += '<option value="' + grades[f][0] + '"> ' + grades[f][1] + '</option>';
						}
						element.id = "grade_" + rowCount;
						//element.innerHTML = string;
						element.innerHTML = grades_combo;
						element.required = "required";
					} else if (elementArray[j - 1] == "action") {
						var element = document.createElement("a");
						element.href="javascript:deleteSpecificRow('CourseExemption_" + rowCount+ "')";
						element.innerText = 'Delete';

						/* var element = document.createElement("span");
						element.innerText = ''; */
						
						// override the previous div and styling
						var div = document.createElement("div");

					} 

					element.name = "data[" + model + "][" + rowCount + "][" + elementArray[j - 1] + "]";

					//cell.appendChild(element);
					div.appendChild(element);
					cell.appendChild(div);
					cell.classList.add("center");
				}
			} // added for limiting the total row, comment the if () { and the closing curly bracket it if not working properly, Neway

			updateSequence(tableID);
		}

		function deleteRow(tableID) {
			try {
				var table = document.getElementById(tableID);
				var rowCount = table.rows.length;
				if (rowCount > 1) {
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
			var result = confirm("Want to delete this course Exemption?");
			if (result) {
				try {
					var row = document.getElementById(id);
					//var table = row.parentElement;
					var table = row.parentNode;
					if (table.rows.length > 1) {
						row.parentNode.removeChild(row);
						updateSequence('course_details');
					} else {
						alert('There must be at least one exam type.');
					}
				} catch (e) {
					alert(e);
				}
			}
		}

		function updateCourseDetailsOnChange(selectObject) {
			//populate unit
			var selectMyStr = selectObject.id;
			var text = selectObject.options[selectObject.selectedIndex].innerText;
			//alert(text);
			//alert(selectMyStr);

			if (typeof text != 'undefined') {
				var selectIds = selectMyStr.split("course_id_");
				document.getElementById('takenCourseTitle_' + selectIds[1]).value = text;

				// Empty the other fields when Course is changed from dropdown
				document.getElementById('takenCourseCode_' + selectIds[1]).value = '';
				document.getElementById('courseTakenCredit_' + selectIds[1]).value = '';
				document.getElementById('grade_' + selectIds[1]).value = '';
			}
		}


		function checkCreditRange(selectObject) {

			var inputCredit = parseInt(selectObject.value);

			if (typeof inputCredit != 'undefined') {
				if (inputCredit < 1) {
					alert('Credit can not less than 0');
					selectObject.value = 0;
				}
				if (inputCredit > 15) {
					alert('Credit can not be more than 15');
					selectObject.value = 15;
				}
			}
		}


		/* document.getElementById("courseTakenCredit_" + rowCount).addEventListener("change", function() {
			let v = parseInt(this.value);
			if (v < 1) this.value = 0;
			if (v > 50) this.value = 15;
		}); */

	</SCRIPT>

	<?php
	$course_details = array(
		'taken_course_title' => 1,
		'taken_course_code' => 2, 
		'course_taken_credit' => 3, 
		'course_id' => 4,
		'grade' => 5,
		'action' => 6
	);
	
	$all_course_details = "";
	$sep = "";

	if (!empty($course_details)) {
		foreach ($course_details as $key => $tag) {
			$all_course_details .= $sep . $key;
			$sep = ",";
		} 
	}
} ?>

<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-vcard" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Add Transferred Courses from other University' ; ?></span>
		</div>

		<a class="close-reveal-modal">&#215;</a>
	</div>
	<div class="row">
		<div class="large-12 columns">
			<hr>
			<h6 class="fs16 text-gray"><?= $student_detail['Student']['first_name'] . ' ' . $student_detail['Student']['middle_name'] . ' ' . $student_detail['Student']['last_name'] . ' (' . $student_detail['Student']['studentnumber'] . ')'; ?></h6>
			<h6 class="fs14 text-gray">Attached Curriculum: <?= (isset($student_detail['Curriculum']['name']) ? $student_detail['Curriculum']['name'] . ' - ' . $student_detail['Curriculum']['year_introduced'] : '<span class="rejected">No curriculum Attachment</span>'); ?></h6>
			<h6 class="fs14 text-gray">All Taken courses: <?= $takenCoursesCount; ?></h6>
			<h6 class="fs14 text-gray">Exempted Courses: <?= (isset($exemptedCourseLists) ? count($exemptedCourseLists) : 0); ?></h6>
			<h6 class="fs14 text-gray"><?= (isset($student_section_exam_status['Section']['YearLevel']['name']) ? 'Year Level: ' . $student_section_exam_status['Section']['YearLevel']['name'] . ' &nbsp;(' . $student_section_exam_status['Section']['academicyear'] . ')' : (isset($student_section_exam_status['Section']) && $student_section_exam_status['Section']['StudentsSection']['archive'] == 0 ? 'Pre/1st' . ' &nbsp;(' . $student_section_exam_status['Section']['academicyear'] . ')' : '---'));  ?> </h6>
			<h6 class="fs14 text-gray"><?= (isset($student_section_exam_status['Section']['name']) ? 'Section: ' . $student_section_exam_status['Section']['name'] . (!$student_section_exam_status['Section']['archive'] && !$student_section_exam_status['Section']['StudentsSection']['archive'] ? ' &nbsp;&nbsp;(<b class="accepted"> Current </b>)' : ' &nbsp;&nbsp;(<span class="rejected"> Previous </span>)') : 'Section: ---'); ?></h6>
			<hr>

			<?php
			if (empty($student_detail['Curriculum'])) { ?>
				<div class='error-box error-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span><?= $student_detail['Student']['full_name']. ' (' . $student_detail['Student']['studentnumber'] . ')'; ?>  doesn't have curricullum attachement. Communicate his/her department to attach a curriculum to the student before trying to add exempted courses.</div>
				<?php
			} else if (!$studentHaveSection) { ?>
				<div class='error-box error-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span><?= $student_detail['Student']['full_name']. ' (' . $student_detail['Student']['studentnumber'] . ')'; ?>  doesn't have any assigned section. Communicate his/her department to add the student to a section before trying to add exempted courses.</div>
				<?php
			} else if (empty($courses)) { ?>
				<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>According to <?= $student_detail['Student']['full_name']. ' (' . $student_detail['Student']['studentnumber'] . ')'; ?> attached curriculum, The student doesn't need any course exemption up to <?= $student_section_exam_status['Section']['YearLevel']['name']; ?> year.</div>
				<?php
			} else { 
				if (isset($student_attached_curriculums_count) && $student_attached_curriculums_count > 1) { ?>
					<div class='error-box error-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span><?= $student_detail['Student']['full_name']. ' (' . $student_detail['Student']['studentnumber'] . ')'; ?>  is attached to more than one curriculums. You might see a given course listed twice in the dropdown menu.  If you are certain thant the student took all of the prequisite courses and encounter pre-requiste course issues while maintaining student registration, you can try interchanging the course with the same course list the bottom if the student's course exemption is made prior to the recent curriculum attachment.</div>
					<?php
				} 
				?>

				<?= $this->Form->create('CourseExemption', array('action' => 'add_student_exemption', "method" => "POST")); ?>
				<?= $this->Form->hidden('CourseExemption.0.student_id', array('value' => $student_detail['Student']['id'])); ?>

				<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal;"><span style='margin-right: 15px;'></span> While Choosing/Changing Student Exempted courses, the system auto-fills student taken course to assist you in filling the course title. You're required to edit the course title according to the official/document provided and fill out the remaining required fields. <br><br> <i class="text-red">NB. Here, Credit/ECTS refers to the course credit/ECTS the student taken while attending the course in the previous university, it's not the credit/ECTS specified on the student attached curriculum.</i></div>

				<h6 class="fs12 text-gray">Please provide the list of courses the student has taken in other university and exempted</h6>

				<?php //debug($exemptedCourseLists); ?>

				<!-- <hr> -->
				<div class="row">
					<div class="large-6 columns">
						<br>
						<?= $this->Form->input('CourseExemption.0.transfer_from', array('style' => 'width: 90%', 'placeholder' => 'University/College the Student is Transferred from?..', 'label' => 'Transferred From: <span class="rejected">*</span> ', 'required', 'value' => (isset($exemptedCourseLists[0]['CourseExemption']['transfer_from']) ? strtoupper($exemptedCourseLists[0]['CourseExemption']['transfer_from']) : '')));?>
					</div>
				</div>

				<?php // debug($student_detail['Curriculum']); ?>

				<table id="course_details" cellpadding="0" cellspacing="0" class="table">
					<thead>
						<tr>
							<td class="center" style="width:3%;">#</td>
							<td class="vcenter" style="width:30%;">Course Title</td>
							<td class="center" style="width:14%;">Course Code</td>
							<td class="center" style="width:7%;"><?= (count(explode('ECTS', $student_detail['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit'); ?></td>
							<td class="center" style="width:30%;">Equivalent Course</td>
							<td class="center" style="width:11%;">Grade</td>
							<td class="center" style="width:5%;">Action</td>
						</tr>
					</thead>
					<tbody>
						<?php
						if (!empty($exemptedCourseLists)) {
							$count = 1;
							$bkc = 0;
							foreach ($exemptedCourseLists as $bk => $bv) { ?>
								<tr id='CourseExemption_<?= $count; ?>'>
									<td class="center"><?= $count; ?></td>
									<td class="center">
										<div style="margin-top: 10px;"><?= $this->Form->input('CourseExemption.' . $bkc . '.taken_course_title', array('value' => (isset($bv['CourseExemption']['taken_course_title']) ? $bv['CourseExemption']['taken_course_title'] : ''), 'id' => 'takenCourseTitle_' . $count .'', 'label' => false, 'div' => false, 'required')); ?></div>
										<?= $this->Form->hidden('CourseExemption.' . $bkc . '.id', array('value' => $bv['CourseExemption']['id'])); ?>
									</td>
									<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('CourseExemption.' . $bkc . '.taken_course_code', array('value' => (isset($bv['CourseExemption']['taken_course_code']) ? $bv['CourseExemption']['taken_course_code'] : ''), 'id' => 'takenCourseCode_' . $count .'', 'label' => false, 'div' => false, 'pattern' => '^[A-Z][a-zA-Z]{1,3}-\\d{3,4}$', 'title' => 'Ensure the Course code starts with a Capital letter followed by 1 to 3 letters, then a hyphen and 3 to 4 digits(numbers)', 'required')); ?></div></td>
									<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('CourseExemption.' . $bkc . '.course_taken_credit', array('value' => (isset($bv['CourseExemption']['course_taken_credit']) ? $bv['CourseExemption']['course_taken_credit'] : ''), 'id' => 'courseTakenCredit_' . $count .'', 'label' => false, 'div' => false, 'type' => 'number', 'min' => 0, 'max' => 15, 'required')); ?></div></td>
									<!-- <td class="center"><?php //echo $this->Form->input('CourseExemption.' . $bkc . '.course_id', array('options' => $courses, 'type' => 'select', 'label' => false, 'id' => 'course_id_' . $count .'', 'default' => (isset($bv['CourseExemption']['course_id']) ? $bv['CourseExemption']['course_id'] : ''), 'onchange' => 'updateCourseDetailsOnChange(this)', 'required')); ?></td> -->
									<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('CourseExemption.' . $bkc . '.course_id', array('options' => $coursesForList, 'type' => 'select', 'label' => false, 'id' => 'course_id_' . $count .'', 'default' => (isset($bv['CourseExemption']['course_id']) ? $bv['CourseExemption']['course_id'] : ''), 'style' => 'width: 100%;', 'onchange' => 'updateCourseDetailsOnChange(this)', 'required')); ?></div></td>
									<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('CourseExemption.' . $bkc . '.grade', array('options' => $grades, 'type' => 'select', 'label' => false, 'id' => 'grade_' . $count .'', 'empty' => '[ Select ]', 'default' => (isset($bv['CourseExemption']['grade']) ? $bv['CourseExemption']['grade'] : ''), 'required')); ?></div></td>
									<td class="center"><a href="javascript:deleteSpecificRow('CourseExemption_<?= $count; ?>')">Delete</a></td>
								</tr>
								<?php
								$count++;
								$bkc++;
							}
						} else { ?>
							<tr id='CourseExemption_1'>
								<td class="center">1</td>
								<td><div style="margin-top: 10px;"><?= $this->Form->input('CourseExemption.0.taken_course_title', array('value' => (isset($this->request->data['CourseExemption'][0]['taken_course_title']) ? $this->request->data['CourseExemption'][0]['taken_course_title'] : ''), 'id' => "takenCourseTitle_1", 'label' => false, 'required')); ?></div></td>
								<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('CourseExemption.0.taken_course_code', array('value' => (isset($this->request->data['CourseExemption'][0]['taken_course_code']) ? $this->request->data['CourseExemption'][0]['taken_course_code'] : ''), 'id' => "takenCourseCode_1", 'label' => false, 'pattern' => '^[A-Z][a-zA-Z]{1,3}-\\d{3,4}$', 'title' => 'Ensure the Course code starts with a Capital letter followed by 1 to 3 letters, then a hyphen and 3 to 4 digits(numbers)', 'required')); ?></div></td>
								<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('CourseExemption.0.course_taken_credit', array('value' => (isset($this->request->data['CourseExemption'][0]['course_taken_credit']) ? $this->request->data['CourseExemption'][0]['course_taken_credit'] : ''), 'id' => "courseTakenCredit_1", 'label' => false, 'type' => 'number', 'min' => 0, 'max' => 15, 'required' )); ?></div></td>
								<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('CourseExemption.0.course_id', array('options' => $courses, 'type' => 'select', 'label' => false, 'default' => (isset($this->request->data['CourseExemption'][0]['course_id']) ? $this->request->data['CourseExemption'][0]['course_id'] : ''), 'id' => "course_id_1", 'required', 'style' => 'width: 100%;', 'onchange' => 'updateCourseDetailsOnChange(this)')); ?></div></td>
								<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('CourseExemption.0.grade', array('options' => $grades, 'type' => 'select', 'label' => false, 'empty' => '[ Select ]', 'default' => (isset($this->request->data['CourseExemption'][0]['grade']) ? $this->request->data['CourseExemption'][0]['grade'] : ''), 'id' => "grade_1", 'required')); ?></div></td>
								<td class="center"><a href="javascript:deleteSpecificRow('CourseExemption_1')">Delete</a></td>
							</tr>
							<?php
						} ?>
					</tbody>
				</table>


				<div class="row">
					<div class="large-2 columns">
						<input type="button" class = 'tiny radius button bg-blue' value="Add Row" onclick="addRow('course_details','CourseExemption',6, '<?= $all_course_details; ?>')" />
					</div>
					<div class="large-2 columns">
						<input type="button" class = 'tiny radius button bg-blue' value="Delete Row" onclick="deleteRow('course_details')" />
					</div>
					<div class="large-8 columns">
						&nbsp;
					</div>
				</div>
				<hr>
				<?php //echo $this->Form->end('Add/Update Exemption'); ?>

				<?= $this->Form->Submit('Add / Update Exemption', array('id' => 'addUpdateExemption', 'class' => 'tiny radius button bg-blue'));  ?>
				<?php
			} ?>
		</div>
	</div>
</div>
