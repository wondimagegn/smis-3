<?= $this->Html->script('amharictyping'); ?>
<?php
if (isset($this->data['Curriculum']['registrar_approved']) && !empty($this->data['Curriculum']['registrar_approved'])) {
	$approvedState = $this->data['Curriculum']['registrar_approved'];
	$readOnly = true;
} else {
	$approvedState = 0;
	$readOnly = false;
} ?>
<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Edit Curriculum : ' . (isset($this->data['Curriculum']['name']) ? $this->data['Curriculum']['name'] . (!is_array($this->data['Curriculum']['year_introduced']) ? ' - ' . $this->data['Curriculum']['year_introduced'] : '') : ''); ?></span>
        </div>
    </div>
	<div class="box-body">
		<div class="row">

			<?php //echo $this->Form->create('Curriculum', array('type' => 'file', 'novalidate' => true, 'enctype' => 'multipart/form-data', /* 'data-abide', */ 'onSubmit' => 'return checkForm(this);')); ?>
			<?= $this->Form->create('Curriculum', array('type' => 'file', 'enctype' => 'multipart/form-data'));?>

			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>
				<blockquote>
					<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
					<p style="text-align:justify;">
						<ol class="fs14 text-black">
							<li><u style="color: red;font-weight: bold;">English and Amharic degree nomenclature</u> fields will used in <u style="color: red;font-weight: bold;">Temporary degree and permanent diploma</u>. Please make sure that these fields are correct and complete before attaching students to this curriculum.</li>
							<li>Course categories with <u style="color: red;font-weight: bold;">Mandatory</u> set other than zero credit, and <u style="color: red;font-weight: bold;">Minimum credit Points</u> will be used to check if a student is eligible for graduation. Please make sure that these fields are correct and complete as well</li>
							<?= REQUIRE_STUDY_PROGRAMS_SELECTED_FOR_CURRICULUM_DEFINITION == 1 ? '<li>Selecting the proper Curriculum Study Program from the list is <u style="color: red;font-weight: bold;">Mandatory</u>. This curriculum will not be approved by the registrar if it is empty and you will not use this curriculum either for attaching students or to publish courses for sections which use this curriculum.' : '' ?>
							<?= REQUIRE_CURRICULUM_PDF_UPLOAD_FOR_CURRICULUM_APPROVAL == 1 ? '<li>Uploading a soft copy of the curriculum is <u style="color: red;font-weight: bold;">Mandatory</u>. This curriculum will not be approved by the registrar you don\'t upload the PDF and you will not use this curriculum either for attaching students or to publish courses for sections which use this curriculum' : '' ?>
						</ol>
					</p> 
				</blockquote>
				<hr>
				<br>
				<br>
			</div>

			<div class="large-7 columns">

				<?= (($approvedState == 1  || $readOnly) ? '<h6 class="fs14 text-red">The curriculum is approved by the registrar and locked.</h6>': '' ); ?>
				
				<table cellpadding="0" cellspacing="0" class="table">
					<thead>
						<tr>
							<td>
								<?= $this->Form->hidden('id',array('value' => $this->data['Curriculum']['id'])); ?>
								<?= $this->Form->hidden('department_id', array('value' => (isset($this->data['Curriculum']['department_id']) ? $this->data['Curriculum']['department_id'] : $department_id))); ?>
								<span class="font">
									<?= $college_name; ?> <br>
									Department: &nbsp;<?= $department_name; ?>
								</span>
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<div class="row">
									<div class="large-12 columns">
										<?= $this->Form->input('name', array('label' => 'Curriculum Name: ', 'required', 'style' => 'width:95%', 'placeholder' => 'Eg. Harmonized B.Sc. Curriculum for Computer Science - Regular', 'readOnly' => $readOnly )); ?>
										<hr>
									</div>
								</div>
								<div class="row">
									<div class="large-6 columns">
										<?= $this->Form->input('type_credit', array('label' => 'Credit Type: ', /* 'empty' => '[ Select Credit Type ]', */ 'options' => array('ECTS Credit Point' => 'ECTS Credit Point', 'Credit' => 'Credit'), 'id' => 'CreditType', 'style' => 'width:90%', 'onchange' => 'updateCreditLable("CreditType")', 'disabled' => $readOnly)); ?>
									</div>
									<div class="large-6 columns">
										<?= $this->Form->input('program_id', array(/* 'empty' => '[ Select Program ]', */ 'options' => $programsss,  'required', 'style' => 'width:90%', 'disabled' => $readOnly)); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-6 columns">
										<?= $this->Form->input('department_study_program_id', array('label' => 'Curriculum Study Program: ', 'style' => 'width:90%', 'empty' => '[ Select Study Program ]', 'options' => $getDepartmentStudyProgramList, 'required' => (REQUIRE_STUDY_PROGRAMS_SELECTED_FOR_CURRICULUM_DEFINITION == 1 ? 'required' : false), 'disabled' => $readOnly)); ?>
									</div>
									<div class="large-6 columns">
										<?php
										if (isset($year_based_curriculum_allowed) && $year_based_curriculum_allowed) {
											debug($year_based_curriculum_allowed);
											echo $this->Form->input('curriculum_type', array('style' => 'width:90%', 'options' => Configure::read('curriculum_types'), 'default' => 1, 'required', 'disabled' => $readOnly));
										} else {
											echo $this->Form->input('curriculum_type', array('style' => 'width:90%', 'options' => Configure::read('curriculum_types'), 'default' => 1, 'disabled', 'disabled' => $readOnly));
											echo $this->Form->hidden('curriculum_type', array('value' => (isset($this->data['Curriculum']['curriculum_type']) ? $this->data['Curriculum']['curriculum_type'] : 1), 'disabled' => $readOnly));
										} ?>
									</div>
									<div class="large-12 columns">
										<hr>
									</div>
								</div>
								<div class="row">
									<div class="large-12 columns">
										<?= $this->Form->input('year_introduced', array('style' => 'width:25%', 'disabled' => $readOnly)); ?>
										<hr>
									</div>
								</div>
								<div class="row">
									<div class="large-12 columns">
										<?= $this->Form->input('english_degree_nomenclature', array('style' => 'width:100%', 'placeholder' => 'Eg. Bachelor of Science Degree in Computer Science', 'after' => '  Eg. Bachelor of Science Degree in Computer Science', 'readOnly' => $readOnly)); ?>
										<hr>
									</div>
								</div>
								<div class="row">
									<div class="large-12 columns">
										<?= $this->Form->input('specialization_english_degree_nomenclature', array('style' => 'width:100%', 'placeholder' => 'Eg. Computer Science', 'readOnly' => $readOnly)); ?>
										<hr>
									</div>
								</div>
								<div class="row">
									<div class="large-12 columns">
										<?= $this->Form->input('amharic_degree_nomenclature', array('id' => 'AmharicText', 'style' => 'width:100%', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);",  'placeholder' => 'ምሳሌ: የሳይንስ ባችለር ዲግሪ በኮምፒውተር ሳይንስ', 'after' => 'ምሳሌ: የሳይንስ ባችለር ዲግሪ በኮምፒውተር ሳይንስ', 'readOnly' => $readOnly));  ?>
										<hr>
									</div>
								</div>
								<div class="row">
									<div class="large-12 columns">
										<?= $this->Form->input('specialization_amharic_degree_nomenclature', array('id' => 'AmharicText', 'style' => 'width:100%', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);", 'placeholder' => 'ምሳሌ: ኮምፒውተር ሳይንስ', 'readOnly' => $readOnly)); ?>
										<hr>
									</div>
								</div>
								<div class="row">
									<div class="large-12 columns">
										<?= $this->Form->input('certificate_name', array('style' => 'width:100%', 'after' => '  E.g M.Sc. Program, B.Sc. Program ', 'placeholder' => 'E.g B.Sc. Program', 'readOnly' => $readOnly)); ?>
										<hr>
									</div>
								</div>
								<div class="row">
									<div class="large-12 columns">
										<?= $this->Form->input('minimum_credit_points', array('style' => 'width:15%', 'id' => 'minimum_credit_points', 'type' => 'number', 'min' => 0, 'max' => 400, 'readOnly' => $readOnly)); ?>
										<hr>
									</div>
								</div>
								<?php
                                if (isset($this->data['Attachment']) && !empty($this->data['Attachment'])) { ?>
                                    <tr>
                                        <td style="background-color: white;">
                                            <table cellpadding="0" cellspacing="0" class="table">
                                                <?php
												//debug($this->data['Attachment']);
                                                foreach ($this->data['Attachment'] as $cuk => $cuv) {
													if (isset($cuv['dirname']) && isset($cuv['basename'])) { ?>
														<tr>
															<td>PDF uploaded on: <?= $this->Time->format("M j, Y g:i A", $cuv['created'], NULL, NULL); ?></td>
														</tr>
														<tr>
															<td style="background-color: white;">
																<?php 
																if ($this->Media->file($cuv['dirname'] . DS . $cuv['basename'])) { ?>
																	<a href="<?= $this->Media->url($cuv['dirname'] . DS . $cuv['basename'], true); ?>" target=_blank>View Attachment</a><br>
																	<?= $cuv['basename']; ?> (<?= $size = $this->Number->toReadableSize($this->Media->size($this->Media->file($cuv['dirname'] . DS . $cuv['basename']))); ?>)
																	<?php // $this->Media->embed($this->Media->file($cuv['dirname'] . DS . $cuv['basename']), array('width' => '144', 'height' => '144'));  ?>
																	<?php
																} else { ?>
																	<span class=" text-red">Attachment not found or deleted</span>
																	<?php
																} ?>
															</td>
														</tr>
														<?php
													}
                                                } ?>
                                            </table>
                                        </td>
                                    </tr>
                                    <?php
                                } else { ?>
									<div class="row">
										<div class="large-12 columns">
											<?= $this->Form->input('Attachment.0.file', array('type' => 'file', 'label' => 'Attach Curriculum PDF', 'required' => (REQUIRE_CURRICULUM_PDF_UPLOAD_FOR_CURRICULUM_DEFINITION == 1 ? 'required' : false), 'accept' => 'application/pdf')); ?>
										</div>
									</div>
									<?php
								} ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="large-5 columns">
				<h6 class="fs14 text-gray">Course/Module category and its total credit points</h6>
				<table id="course_categories" cellspacing="0" cellpadding="0" class="table">
					<thead>
						<?php
						//$fields = array('name' => 1, 'code' => 2, 'total_credit' => 3, 'mandatory_credit' => 4);
						$fields = array('name' => 1, 'total_credit' => 2, 'mandatory_credit' => 3);
						$all_fields = "";
						$sep = "";

						foreach ($fields as $key => $tag) {
							$all_fields .= $sep . $key;
							$sep = ",";
						} ?>
						<tr>
							<td class="center" style="width: 5%;">#</td>
							<td class="vcenter" style="width: 50%;">Category Name</td>
							<!-- <td class="center">Code</td> -->
							<td class="center" style="width: 25%;">Total Credit</td>
							<td class="center" style="width: 20%;">Mandatory Credit</td>
							<?php
							if (($approvedState == 0  || !($readOnly))) { ?>
								<td class="center"></td>
								<?php
							} ?>
						</tr>
					</thead>
					<tbody>
						<?php
						$totalcreditSum = 0;
						$mandatoryCreditSum = 0;
						if (isset($this->data['CourseCategory']) && count($this->data['CourseCategory']) > 0) {
							$count = 1;
							foreach ($this->data['CourseCategory'] as $ck => $cv) { 
								//debug($cv);
								if (!empty($cv['id'])) {
									echo $this->Form->hidden('CourseCategory.'.$ck.'.id');
									echo $this->Form->hidden('CourseCategory.'.$ck.'.curriculum_id');
									$action_controller_id='edit~curriculums~'.$cv['curriculum_id'];

									$totalcreditSum += $cv['total_credit'];
									$mandatoryCreditSum += $cv['mandatory_credit'];

						  		} ?>
								<tr id='CourseCategory_<?= $count; ?>'>
									<td class="center"><?= $count; ?></td>
									<td class="vcenter"><?= $this->Form->input('CourseCategory.' . $ck . '.name', array('label' => false, 'required' => 'required')); ?></td>
									<!-- <td><?php //echo $this->Form->input('CourseCategory.' . $ck . '.code', array('label' => false)); ?></td> -->
									<td class="center"><?= $this->Form->input('CourseCategory.' . $ck . '.total_credit', array('label' => false, 'id' => 't' . $count, 'onBlur' => 'updateSum("course_categories");', 'min' => 0, 'max' => 400, 'required' => 'required')); ?></td>
									<td class="center"><?= $this->Form->input('CourseCategory.' . $ck . '.mandatory_credit', array('label' => false, 'id' => 'm' . $count, 'onBlur' => 'updateSum("course_categories");', 'min' => 0, 'max' => 400, 'required' => 'required')); ?></td>
									<?php
									if (($approvedState == 0  || !($readOnly)) && isset($cv['id'])) { ?>
										<td class="center"><?= (!empty($action_controller_id) ? $this->Html->link(__('Delete', true), array('action' => 'deleteCourseCategory', $cv['id'], $action_controller_id), null, sprintf(__('Are you sure you want to delete %s course category?', $cv['name']))) : ''); ?></td>
										<?php
									} ?>
								</tr>
								<?php
								$count ++;
							}
						} else { ?>
							<tr id='CourseCategory_1'>
								<td class="center">1</td>
								<td class="center" style="width: 50%;"><?= $this->Form->input('CourseCategory.0.name', array('label' => false, 'id' => 'CourseCategoryName_1', 'placeholder' => 'Like: Core/Major, Common, Elective..', 'required' => 'required' /* , 'class' => 'custom-select', 'options' => $course_category_options */)); ?></td>
								<!-- <td><?php //echo $this->Form->input('CourseCategory.0.code', array('label' => false)); ?></td> -->
								<td class="center"><?= $this->Form->input('CourseCategory.0.total_credit', array('id' => 't1', 'onBlur' => 'updateSum("course_categories");', 'label' => false, 'min' => 0, 'max' => 400, 'required' => 'required')); ?></td>
								<td class="center"><?= $this->Form->input('CourseCategory.0.mandatory_credit', array('label' => false, 'id' => 'm1', 'onBlur' => 'updateSum("course_categories");', 'min' => 0, 'max' => 400, 'required' => 'required')); ?></td>
							</tr>
							<?php
						} ?>
					</tbody>
				</table>

				<table style="border:0px;" cellspacing="0" cellpadding="0" class="table">
					<tr>
						<td style="width: 5%;"> &nbsp; </td>
						<td style="width: 45%;text-align:right;"> Total:</td>
						<!-- <td>&nbsp;</td> -->
						<td id="t_sum" style="width: 25%; text-align:center;"><?= $totalcreditSum; ?></td>
						<td id="m_sum" style="width: 25%;text-align:center;"><?=$mandatoryCreditSum; ?></td>
					</tr>
				</table>

				<?php
				if (($approvedState == 0  || !($readOnly))) { ?>
					<table style="border:0px;">
						<tr>
							<td colspan=3>
								<input type="button" class="tiny radius button bg-blue" value="Add Row" onclick="addRow('course_categories','CourseCategory',3, '<?= $all_fields; ?>')" /> &nbsp; &nbsp; &nbsp; 
								<!-- <input type="button" class="tiny radius button bg-blue" value="Delete Row" onclick="deleteRow('course_categories')" /> -->
							</td>
						</tr>
					</table>
					<?php
				} ?>
			</div>
			<?php
			if (($approvedState == 0  || !($readOnly))) { ?>
				<div class="large-12 columns">
					<hr>
					<?= $this->Form->end(array('label' => __('Save Changes', true), 'class' => 'tiny radius button bg-blue', 'name' => 'saveCurriculum', 'id' => 'saveCurriculum', 'div' => false )); ?>
				</div>
				<?php
			} ?>
		</div>
	</div>
</div>


<script type="text/javascript">

	var form_being_submitted = false; 

	var checkForm = function(form) {

		var elements = form.elements;

		if (form.CurriculumName.value == '') { 
			form.saveCurriculum.disabled = true;
		}

		form_being_submitted = true;
		
		for (var i = 0, len = elements.length; i < len; ++i) {
			elements[i].readOnly = true;
			//elements[i].disabled = true;
		}
		
		return true; /* submit form */

	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}

	function updateSum(tableID) {
		var total_sum = 0;
		var total_mandatory = 0;

		for (i = 1; i < document.getElementById(tableID).rows.length; i++) {
			total_sum += adjustSum("t" + i, 0);
			total_mandatory += adjustSum(0, "m" + i);
		}

		updateReft = window.document.getElementById('t_sum');
		//alert(updateReft);
		updateReft.innerHTML = total_sum;
		updateRefm = window.document.getElementById('m_sum');
		updateRefm.innerHTML = total_mandatory;

		updateRefminInput = window.document.getElementById('minimum_credit_points');
		updateRefminInput.value = total_mandatory;
	}

	function updateCreditLable(id) {
		$(".credit").empty();
		if (document.getElementById(id).value !== null) {
			$(".credit").append(document.getElementById(id).value);
		}
	}

	function adjustSum(x, y) {
		if (y == 0) {
			ref = window.document.getElementById(x);
			if (ref.value !== null) {
				if (!isNaN(ref.value) & ref.value >= 0) {
					return Number(ref.value);
				} else {
					return 0;
				}
			}
		}

		if (x == 0) {
			ref = window.document.getElementById(y);
			if (ref.value !== null) {
				if (!isNaN(ref.value) & ref.value >= 0) {
					return Number(ref.value);
				} else {
					return 0;
				}
			}
		}
	}

	function addRow(tableID, model, no_of_fields, all_fields) {

		var elementArray = all_fields.split(',');
		var table = document.getElementById(tableID);

		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);

		// added for limiting the total row, comment the if () { and the closing curly bracket it if not working properly, Neway
		if (table.rows.length <= <?= (count($course_category_values) - 1); ?>) {
			
			var cell0 = row.insertCell(0);
			cell0.innerHTML = rowCount;
			cell0.classList.add("center");

			for (var i = 1; i <= (no_of_fields + 1); i++) {
				
				var cell = row.insertCell(i);

				if (elementArray[i - 1] == "name") {
					var element = document.createElement("input");
					
					element.type = "text";
					element.style = "width:100%;";
					element.required = "required";
					element.id = "CourseCategoryName_" + rowCount;

					//Added by neway
					/* var element = document.createElement("select");
					string = "";
					for (var f = 0; f < course_categories.length; f++) {
						string += '<option value="' + course_categories[f][0] + '"> ' + course_categories[f][1] + '</option>';
					}
					element.id = "CourseCategoryName_" + rowCount;
					element.innerHTML = course_categories_combo; */
					//end Added
				}

				/* else if (elementArray[i - 1] == "code") {
					var element = document.createElement("input");
					element.size = "13";
					element.type = "text";
				} */

				else if (elementArray[i - 1] == "total_credit") {
					var element = document.createElement("input");
					
					element.type = "number";
					element.style = "width:100%;";
					element.required = "required";
					element.id = 't' + rowCount;

					element.onchange = function() {
						checkCreditRange(this);
					};

					element.onblur = function() {
						return updateSum('course_categories');
					};
				} else if (elementArray[i - 1] == "mandatory_credit") {
					var element = document.createElement("input");

					element.type = "number";
					element.style = "width:100%";
					element.required = "required";
					element.id = 'm' + rowCount;

					element.onchange = function() {
						checkCreditRange(this);
					};

					element.onblur = function() {
						return updateSum('course_categories');
					};
				} else {
					// empty td for delete
					var element = document.createElement("span");
					element.innerText = '';
				}

				element.name = "data[" + model + "][" + rowCount + "][" + elementArray[i - 1] + "]";
				cell.appendChild(element);
			}
		}

		updateSum('course_categories');
		updateSequence('course_categories');
	}

	function deleteRow(tableID) {
		try {
			var table = document.getElementById(tableID);
			var rowCount = table.rows.length;
			if (rowCount > 1) {
				table.deleteRow(rowCount - 1);
				updateSequence(tableID);
				updateSum(tableID);
			} else {
				alert('No more rows to delete');
			}
		} catch (e) {
			alert(e);
		}

		updateSum('course_categories');
	}

	function updateSequence(tableID) {
		var s_count = 1;
		for (i = 1; i < document.getElementById(tableID).rows.length; i++) {
			document.getElementById(tableID).rows[i].cells[0].childNodes[0].data = s_count++;
		}
	}

	function checkCreditRange(selectObject) {
		var inputCredit = parseInt(selectObject.value);
		if (typeof inputCredit != 'undefined') {
			if (inputCredit < 1) {
				alert('Credit can not less than 0');
				selectObject.value = 0;
			}
			if (inputCredit > 400) {
				alert('Credit can not be more than 400');
				selectObject.value = 400;
			}
		}
	}

	function addDropdownRow(tableID, controller, no_of_fields, all_fields, course_list_category) {
		
		var elementArray = all_fields.split(',');
		var cclist = course_list_category.split(',');

		var table = document.getElementById(tableID);

		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		var pcount = cclist.length;

		for (var i = 1; i <= no_of_fields; i++) {

			var cell0 = row.insertCell(0);
			cell0.innerHTML = rowCount + 1;
			var cell = row.insertCell(1);
			var element = document.createElement("select");

			var string = '<option value="None">Select Course Category</option>'

			for (var i = 0; i < pcount; i++) {
				string += '<option value="' + i + '"> ' + cclist[i] + '</option>';
			}

			element.innerHTML = string;
			element.name = "data[" + controller + "][" + rowCount + "][" + elementArray[0] + "]";
			cell.appendChild(element);

			var cell1 = row.insertCell(i);
			var element = document.createElement("input");
			element.type = "text";

			if (elementArray[i - 1] == "total_credit") {
				element.size = "13";
			}
			element.name = "data[" + controller + "][" + rowCount + "][" + elementArray[i - 1] + "]";
			cell.appendChild(element);
		}
	}

	updateSum('course_categories');
	updateCreditLable('CreditType');

</script>