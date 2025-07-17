<?= $this->Html->script('amharictyping'); ?>
<script type="text/javascript">
	var region = Array();
	var months = Array();

	<?php
	for ($i = 1; $i <= 12; $i++) { ?>
		months[<?= $i - 1; ?>] = new Array();
		months[<?= $i - 1; ?>][0] = "<?= date('m', mktime(0, 0, 0, $i, 1, 2011)); ?>";
		months[<?= $i - 1; ?>][1] = "<?= date('F', mktime(0, 0, 0, $i, 1, 2011)); ?>";
		<?php
	}

	if (!empty($regionsAll)) {
		foreach ($regionsAll as $region_id => $region_name) { ?>
			region["<?= $region_id; ?>"] = "<?= $region_name; ?>";
			<?php
		}
	} ?>

	function addRow(tableID, model, no_of_fields, all_fields, other) {

		var elementArray = all_fields.split(',');
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);
		var cell0 = row.insertCell(0);
		cell0.classList.add("center");

		cell0.innerHTML = rowCount;

		for (var i = 1; i <= no_of_fields; i++) {

			var cell = row.insertCell(i);
			var div = document.createElement("div");
			div.style.marginTop = "10px";

			if (elementArray[i - 1] == "region_id") {
				var element = document.createElement("select");
				var string = '<option value="">[ Select Region ]</option>';

				for (var f = 1; f < region.length; f++) {
					if (!(typeof region[f] === 'undefined')) {
						string += '<option value="' + f + '">' + region[f] + '</option>';
					}
				}

				element.style = "width:100%;";
				element.required = "required";
				element.innerHTML = string;

			} else if (elementArray[i - 1] == "exam_year") {
				var element = document.createElement("select");
				var d = new Date();
				var full_year = d.getFullYear();
				var string = '<option value="">[ Select Year ]</option>';

				for (var j = full_year - 1; j > other; j--) {
					string += '<option value="' + j + '">' + j + '</option>';
				}

				element.innerHTML = string;
				//element.style = "width:70%;";
				element.style = "width:100%;";
				element.required = "required";

			} else if (elementArray[i - 1] == 'grade') {
				var element = document.createElement("input");
				element.type = "text";
				element.style = "width:100%;";
				element.placeholder = "A";
			} else if (elementArray[i - 1] == 'mark') {
				var element = document.createElement("input");
				element.type = "number";
				element.max = "100";
				element.min = "0";
				element.step = "any";
				element.style = "width:100%;";
				element.placeholder = "Mark " + rowCount;
				element.required = "required";

				element.classList.add("subjectMark-input");

				element.onblur = function() {
					checkValidMarkInput(this);
				};

			} else if (elementArray[i - 1] == 'national_exam_taken') {
				var element = document.createElement("input");
				element.type = "checkbox";
				element.style = "width:100%;";
			} else if (elementArray[i - 1] == 'cgpa_at_graduation') {
				var element = document.createElement("input");
				element.type = "number";
				element.max = "4.0";
				element.min = "2.0";
				element.step = "any";
				element.required = "required";
			} else if (elementArray[i - 1] == 'date_graduated') {
				var element = document.createElement("input");
				element.type = "date";
				//element.format = "dd/mm/yyyy";
				// element.minYear = "<?php //echo date('Y') - 30; ?>";
				// element.maxYear = "<?php //echo date('Y') - 1; ?>";
				//element.style.width = '30%';
				element.style = "width:90%;";
				element.required = "required";
			} else if (elementArray[i - 1] == 'subject') {
				var element = document.createElement("input");
				element.type = "text";
				element.style = "width:100%;";
				element.placeholder = "Subject " + rowCount;
				//element.pattern = "^[A-Za-z]+$"

				element.required = "required";

				element.classList.add("subject-input");

				element.onblur = function() {
					checkIsAlpha(this);
				};

			} else {
				var element = document.createElement("input");
				element.type = "text";
				//element.size = "13";
				element.style = "width:100%;";
				element.required = "required";

				// override the previous div and styling
				var div = document.createElement("div");

				element.onblur = function() {
					checkIsAlpha(this);
				};
			}

			element.name = "data[" + model + "][" + rowCount + "][" + elementArray[i - 1] + "]";
			//cell.appendChild(element);
			div.appendChild(element);
			cell.appendChild(div);

			cell.classList.add("center");
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

	function updateSequence(tableID) {
		var s_count = 1;
		for (i = 1; i < document.getElementById(tableID).rows.length; i++) {
			document.getElementById(tableID).rows[i].cells[0].childNodes[0].data = s_count++;
		}
	}

	function updateRegionCity(id) {
		//serialize form data
		var formData = $("#country_id_" + id).val();

		$("#region_id_" + id).empty();
		$("#region_id_" + id).attr('disabled', true);
		$("#city_id_" + id).attr('disabled', true);

		//get form action
		var formUrl = '/students/get_regions/' + formData;

		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data, textStatus, xhr) {
				$("#region_id_" + id).attr('disabled', false);
				$("#region_id_" + id).empty();
				$("#region_id_" + id).append(data);

				//Items list
				var subCat = $("#region_id_" + id).val();
				$("#city_id_" + id).empty();

				//get form action
				var formUrl = '/students/get_cities/' + subCat;
				$.ajax({
					type: 'get',
					url: formUrl,
					data: subCat,
					success: function(data, textStatus, xhr) {
						$("#city_id_" + id).attr('disabled', false);
						$("#city_id_" + id).empty();
						$("#city_id_" + id).append(data);
					},
					error: function(xhr, textStatus, error) {
						alert(textStatus);
					}
				});
				//End of items list
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});

		return false;
	}

	//Update city given region
	function updateCity(id) {
		//serialize form data
		var subCat = $("#region_id_" + id).val();
		$("#city_id_" + id).attr('disabled', true);
		$("#city_id_" + id).empty();

		//get form action
		var formUrl = '/students/get_cities/' + subCat;

		$.ajax({
			type: 'get',
			url: formUrl,
			data: subCat,
			success: function(data, textStatus, xhr) {
				$("#city_id_" + id).attr('disabled', false);
				$("#city_id_" + id).empty();
				$("#city_id_" + id).append(data);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});

		return false;
	}
</script>

<?php
if (isset($studentDetail) && !empty($studentDetail['Student'])) { ?>
	<div class="box">
		<div class="box-header bg-transparent">
			<div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
				<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Update Student Details: ' . $studentDetail['Student']['full_name'] . '  (' .  $studentDetail['Student']['studentnumber'] . ')'; ?></span>
			</div>
		</div>
		<div class="box-body pad-forty">
			<div class="row">
				<div class="large-12 columns">
					<div style="margin-top: -50px;"><hr></div>

					<?php
					if (isset($require_update) && $require_update) { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>The system detected some invalid fields, to save the changes, you're required to review the listed fields and click "Update Student Details" button to save auto corrected changes.</div>
						<?php
						if (isset($require_update_fields) && count($require_update_fields) > 0) { ?>
							<div class="errorSummary">
								<ol>
									<?php
									foreach ($require_update_fields as $key => $value) { ?>
										<li class="rejected">Field: <?= ($value['field']); ?>,  Exitsting Value: <?= ($value['previous_value']); ?>, Auto Corrected Value: <?= ($value['auto_corrected_value']); ?> , Reason: <?= ($value['reason']); ?></li>
										<?php
									} ?>
								</ol>
							</div>
							<?php
						} ?>
						<hr>
						<?php
					} ?>

					<?php $this->assign('title_details', (!empty($this->request->params['controller']) ? ' ' . Inflector::humanize(Inflector::underscore($this->request->params['controller'])) . (!empty($this->request->params['action']) && $this->request->params['action'] != 'index' ? ' | ' . ucwords(str_replace('_', ' ', $this->request->params['action'])) : '') : '') . (isset($studentDetail['Student']['id']) ? ' - '. $studentDetail['Student']['full_name'] . ' ('. $studentDetail['Student']['studentnumber'] .')' : '')); ?>

					<?php
					if (!empty($studentDetail['Attachment'][0]['basename']) || (empty($studentDetail['Attachment'][0]['basename']) && ALLOW_STUDENTS_TO_UPLOAD_PROFILE_PICTURE == 0)) { ?>
						<?= $this->Form->create('Student', array('data-abide', 'novalidate' => true)); ?>
						<?php
					} else { ?>
						<?= $this->Form->create('Student', array('data-abide', 'type' => 'file', 'novalidate' => true)); ?>
						<?php
					} ?>

					<ul class="tabs" data-tab>
						<li class="tab-title active"><a href="#basic_data">Basic Student Information</a></li>
						<li class="tab-title"><a href="#add_address">Address & Primary Contact</a></li>
						<li class="tab-title"><a href="#education_background">Educational Background</a></li>
					</ul>

					<div class="tabs-content edumix-tab-horz">
						<div class="content active" id="basic_data">
							<div class="row">
								<div class="large-12 columns">
									<?php
									echo $this->Form->hidden('id', array('value' => $studentDetail['Student']['id']));
									if (isset($studentDetail['Contact'][0]['id'])) {
										echo $this->Form->hidden('Contact.0.id', array('value' => $studentDetail['Contact'][0]['id']));
									}

									echo $this->Form->hidden('Contact.0.student_id', array('value' => $studentDetail['Student']['id']));

									$errors = $this->Form->validationErrors;

									$ethiopianStudent = (isset($studentDetail['Student']['country_id']) && $studentDetail['Student']['country_id'] == COUNTRY_ID_OF_ETHIOPIA ? true : false);

									$ugProgram = (isset($studentDetail['Student']['program_id']) && $studentDetail['Student']['program_id'] == PROGRAM_UNDEGRADUATE ? true : false);

									if (count($errors['Student']) > 0 && isset($this->data['Student'])) {
										$flatErrors = Set::flatten($errors['Student']); ?>
										<div class="errorSummary">
											<ul>
												<?php
												foreach ($flatErrors as $key => $value) { ?>
													<li class="rejected"><?= ($value); ?></li>
													<?php
												} ?>
											</ul>
										</div>
										<?php
									} ?>
								</div>
							</div>

							<div class="row">
								<div class="large-6 columns">
									<table cellspacing="0" cellpading="0" class="table">
										<tbody>
											<tr>
												<td><strong> Demographic Information</strong></td>
											</tr>
											<tr>
												<td style="background-color: white;">
													<div class="large-12 columns">
														<?= $this->Form->input('first_name', array('readOnly' => true, 'label' => 'First Name (English): ')); ?>
														<?= $this->Form->hidden('first_name', array('value' => (!empty($studentDetail['Student']['first_name']) ? $studentDetail['Student']['first_name'] : (isset($studentDetail['AcceptedStudent']) && !empty($studentDetail['AcceptedStudent']['first_name']) ? $studentDetail['AcceptedStudent']['first_name'] : NULL)))); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('middle_name', array('label' => 'Middle Name (English): ', 'readOnly' => true)); ?>
														<?= $this->Form->hidden('middle_name', array('value' => (!empty($studentDetail['Student']['middle_name']) ? $studentDetail['Student']['middle_name'] : (isset($studentDetail['AcceptedStudent']) && !empty($studentDetail['AcceptedStudent']['middle_name']) ? $studentDetail['AcceptedStudent']['middle_name'] : NULL)))); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('last_name', array('label' => 'Last Name (English): ', 'readOnly' => true)); ?>
														<?= $this->Form->hidden('last_name', array('value' => (!empty($studentDetail['Student']['last_name']) ? $studentDetail['Student']['last_name'] : (isset($studentDetail['AcceptedStudent']) && !empty($studentDetail['AcceptedStudent']['last_name']) ? $studentDetail['AcceptedStudent']['last_name'] : NULL)))); ?>
													</div>
													<div class="large-12 columns">
														<label> First Name (Amharic): <?= ($ethiopianStudent ? '&nbsp;<span class="rejected">*</span>' : ''); ?>
															<?= $this->Form->input('amharic_first_name', array('label' => false, 'required' => $ethiopianStudent, 'id' => 'AmharicText', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);")); ?>
															<?php
															/* if (empty($studentDetail['Student']['amharic_first_name'])) { ?>
																<?= $this->Form->input('amharic_first_name', array('label' => false, 'required' => $ethiopianStudent, 'id' => 'AmharicText', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);")); ?>
																<?php
															} else { ?>
																<?= $this->Form->input('amharic_first_name', array('label' => false, 'readOnly' => true, 'required' => $ethiopianStudent, 'id' => 'AmharicText', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);")); ?>
																<?= $this->Form->hidden('amharic_first_name', array('value' => (!empty($this->data['Student']['amharic_first_name']) ? $this->data['Student']['amharic_first_name'] : $studentDetail['Student']['amharic_first_name']))); ?>
																<?php
															} */ ?>
														</label>
													</div>
													<div class="large-12 columns">
														<label> Middle Name (Amharic): <?= ($ethiopianStudent ? '&nbsp;<span class="rejected">*</span>' : ''); ?>
															<?= $this->Form->input('amharic_middle_name', array('label' => false, 'div' => true, 'required' => $ethiopianStudent, 'id' => 'AmharicTextMiddleName', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);")); ?>

														</label>
													</div>
													<div class="large-12 columns">
														<label> Last Name (Amharic):<?= ($ethiopianStudent ? '&nbsp;<span class="rejected">*</span>' : ''); ?>
															<?= $this->Form->input('amharic_last_name', array('label' => false, 'div' => true, 'required' => $ethiopianStudent, 'id' => 'AmharicTextLastName', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);")); ?>

														</label>
													</div>

													<div class="large-12 columns">
														<label> Estimated Graduation Date:
															<?php //ehco $this->Form->input('estimated_grad_date', array('minYear' => date('Y'), 'maxYear' => date('Y') + Configure::read('Calendar.expectedGraduationInFuture'), 'orderYear' => 'desc', 'label' => false, 'style' => 'width: 25%;')); ?>
															<?= $this->Form->input('estimated_grad_date', array('minYear' => (isset($student_admission_year) && !empty($student_admission_year) ?  $student_admission_year : date('Y')), 'maxYear' => (isset($maximum_estimated_graduation_year_limit) && !empty($maximum_estimated_graduation_year_limit) ?  $maximum_estimated_graduation_year_limit :  (date('Y') + Configure::read('Calendar.expectedGraduationInFuture'))), 'orderYear' => 'desc', 'label' => false, 'style' => 'width: 25%;')); ?>
														</label>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('gender', array('label' => 'Sex: ', 'type' => 'select', 'style' => 'width:30%;', 'div' => false, 'options' => array('Female' => 'Female', 'Male' => 'Male'))); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('lanaguage', array('label' => 'Primary Lanaguage: ')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('email', array('type' => 'email', 'id' => 'email', 'required', 'label' => 'Email: &nbsp;<span class="rejected">*</span>')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('email_alternative', array('type' => 'email', 'id' => 'alternativeEmail', 'label' => 'Alternative Email: ')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('phone_home', array('type' => 'tel', 'id'=>'phoneoffice', 'label' => 'Phone (Home): ')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('phone_mobile', array('type' => 'tel', 'id'=>'etPhone', 'required', 'label' => 'Phone (Mobile): &nbsp;<span class="rejected">*</span>')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('birthdate', array(/* 'type' => 'text', */ 'label' => 'Birth Date: (G.C) &nbsp;<span class="rejected">* (set this carefully!)</span>', 'minYear' => date('Y') - Configure::read('Calendar.birthdayInPast'), 'maxYear' => (date('Y') - 17), 'orderYear' => 'desc', 'style' => 'width: 25%;')); ?>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
									<br><br>
								</div>

								<div class="large-6 columns">
									<table cellpadding="0" cellspacing="0" class="table">
										<tbody>
											<tr><td colspan=2><strong>Profile Picture</strong></td></tr>
											<?php

											$atLeastOneImage = true;

											if (!empty($studentDetail['Attachment'][0]['basename'])) {
												//echo '<tr><td colspan=2><strong>Attachment</strong></td></tr>'; ?>
												<?php
												if ($this->Media->file($studentDetail['Attachment'][0]['dirname'] . DS . $studentDetail['Attachment'][0]['basename'])) { ?>
													<tr>
														<td valign="top">
														<?= $this->Media->embed($this->Media->file($studentDetail['Attachment'][0]['dirname'] . DS . $studentDetail['Attachment'][0]['basename']), array('width' => '144', 'class' => 'profile-picture')); ?>
														</td>
													</tr>
													<?php
													$canbe_deleted = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("n"), date("j") - DAYS_ALLOWED_TO_DELETE_PROFILE_PICTURE_FROM_LAST_UPLOAD, date("Y")));
													//debug($canbe_deleted);

													if ($canbe_deleted < $studentDetail['Attachment'][0]['modified'] && $this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
														$action_controller_id = 'edit~students~' . $studentDetail['Attachment'][0]['foreign_key'];
														?>
														<tr>
															<td><?= $this->Html->link(__('Delete Profile Picture', true), array('controller' => 'attachments', 'action' => 'delete', $studentDetail['Attachment'][0]['id'], $action_controller_id), null, sprintf(__('Are you sure you want to delete your profile picture which is uploaded on %s ?'/* , true */), $studentDetail['Attachment'][0]['modified'] )); ?></td>
														</tr>
													<?php
													}
												} else { ?>
													<tr>
														<td valign="top">
															<span class="rejected">Could't load profile Picture, Directory/File inaccessasible</span> <br><br>
															<img src="/img/noimage.jpg" width="144" class="profile-picture">
														</td>
													</tr>
													<?php
												}
											} else { ?>
												<tr><td valign="top"><img src="/img/noimage.jpg" width="144" class="profile-picture"></td></tr>
												<?= (ALLOW_STUDENTS_TO_UPLOAD_PROFILE_PICTURE ? '<tr><td class="vcenter">'. $this->Form->input('Attachment.0.file', array('type' => 'file', 'label' => 'Uploaad Profile Picture', 'required' => (REQUIRE_STUDENTS_TO_UPLOAD_PROFILE_PICTURE_WHEN_UPDATING_PROFILE == 1 ? 'required' : false), 'accept' => '.jpg, .jpeg, .png')) .'</td></tr>' : ''); ?>
												<?php //ehco $this->element('Media.attachments'); ?>
												<?php
											} ?>

											<tr><td colspan=2><strong>Access Information</strong></td></tr>
											<tr><td style="padding-left:30px;">Username: <?= (!empty($studentDetail['User']['username']) ?  $studentDetail['User']['username'] : ''); ?></td></tr>
											<tr><td style="padding-left:30px;">Last Login: <?= (($studentDetail['User']['last_login'] == '' ||  $studentDetail['User']['last_login'] == '0000-00-00 00:00:00' || is_null($studentDetail['User']['last_login'])) ? '<span class="rejected">Never loggedin</span>' : $this->Time->timeAgoInWords($studentDetail['User']['last_login'], array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month')))); ?></td></tr>
											<tr><td style="padding-left:30px;">Last Password Change: <?= (($studentDetail['User']['last_password_change_date'] == '' ||  $studentDetail['User']['last_password_change_date'] == '0000-00-00 00:00:00' || is_null($studentDetail['User']['last_password_change_date'])) ? '<span class="rejected">Never Changed</span>' : $this->Time->timeAgoInWords($studentDetail['User']['last_password_change_date'], array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month')))); ?></td></tr>
											<tr><td style="padding-left:30px;">Failed Logins: <?= (isset($studentDetail['User']['failed_login']) && $studentDetail['User']['failed_login'] != 0  ?  $studentDetail['User']['failed_login'] : '---'); ?></td></tr>
											<tr><td style="padding-left:30px;">Ecardnumber: <?= (isset($studentDetail['Student']['ecardnumber']) && !empty($studentDetail['Student']['ecardnumber']) ? $studentDetail['Student']['ecardnumber'] : '---'); ?></td></tr>
											<?php
											$preEngineeringColleges = Configure::read('preengineering_college_ids');

											if ($studentDetail['Student']['program_id'] == PROGRAM_REMEDIAL) {
												$stream = 'Remedial Program';
											} else if (isset($studentDetail['College']['stream']) && $studentDetail['College']['stream'] == STREAM_NATURAL && in_array($studentDetail['Student']['college_id'], $preEngineeringColleges)) {
												$stream = 'Freshman - Pre Engineering';
											} else if (isset($studentDetail['College']['stream']) && $studentDetail['College']['stream'] == STREAM_NATURAL) {
												$stream = 'Freshman - Natural Stream';
											} else if (isset($studentDetail['College']['stream']) && $studentDetail['College']['stream'] == STREAM_SOCIAL) {
												$stream = 'Freshman - Social Stream';
											} else {
												$stream = '---';
											} ?>

											<tr><td colspan=2><strong>Classification of Admission</strong></td></tr>
											<tr><td style="padding-left:30px;">Program: <?= $programs[$studentDetail['Student']['program_id']]; ?></td></tr>
											<tr><td style="padding-left:30px;">Program Type: <?= $programTypes[$studentDetail['Student']['program_type_id']]; ?></td></tr>
											<tr><td style="padding-left:30px;"><?= (isset($studentDetail['College']['type']) && !empty($studentDetail['College']['type']) ? $studentDetail['College']['type'] : 'College') ?>: <?= $colleges[$studentDetail['Student']['college_id']]; ?></td></tr>
											<tr><td style="padding-left:30px;"><?= (isset($studentDetail['Department']['type']) && !empty($studentDetail['Department']['type']) ? $studentDetail['Department']['type'] : 'Department') ?>: <?= (!empty($studentDetail['Student']['department_id']) ? $departments[$studentDetail['Student']['department_id']] : $stream ); ?></td></tr>
											<tr><td style="padding-left:30px;">Admission Year: <?= (isset($studentDetail['Student']['academicyear']) ? $studentDetail['Student']['academicyear'] : '---'); ?></td></tr>
											<tr><td style="padding-left:30px;">Admission Date: <?= $this->Time->format("M j, Y", $studentDetail['Student']['admissionyear'], NULL, NULL); ?></td></tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="content" id="add_address">
							<div class="row">
								<div class="large-6 columns">
									<table cellspacing="0" cellpading="0" class="table">
										<tbody>
											<tr>
												<td><strong>Your Home Address</strong></td>
											</tr>
											<tr>
												<td style="background-color: white;">
													<div class="large-12 columns">
														<?= $this->Form->input('country_id', array('id' => 'country_id_2', /* 'onchange' => 'updateRegionCity(2)', */ 'label' => 'Country: ', /* 'error' => false, */ 'empty' => false, 'style' => 'width:70%;', 'default' => COUNTRY_ID_OF_ETHIOPIA)); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('region_id', array('id' => 'region_id_2', /* 'onchange' => 'updateCity(2)', */ 'label' => 'Region: ',  /* 'error' => false, 'empty' => 'Select Country First', */ 'style' => 'width:70%;')); ?>
													</div>
													<div class="large-12 columns">
														<?php
														if ($studentDetail['Student']['graduated'] == 1) { ?>
															<?= $this->Form->input('zone_subcity', array('label' => 'Zone/Subcity: ')); ?>
															<?php
														} else { ?>
															<?= $this->Form->input('zone_id', array('id' => 'zone_id_2', /* 'onchange' => 'updateCity(2)',  */'label' => 'Zone: ', 'empty' => '[ Select Zone ]', 'style' => 'width:70%;')); ?>
															<?php
														} ?>
													</div>
													<div class="large-12 columns">
														<?php
														if ($studentDetail['Student']['graduated'] == 1) { ?>
															<?= $this->Form->input('woreda', array('label' => 'Woreda: ')); ?>
															<?php
														} else { ?>
															<?= $this->Form->input('woreda_id', array('id' => 'woreda_id_2', /* 'onchange' => 'updateCity(2)',  */'label' => 'Woreda: ', 'empty' => '[ Select Woreda ]', 'style' => 'width:70%;')); ?>
															<?php
														} ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('city_id', array('label' => 'City: ', 'id' => 'city_id_2', 'style' => 'width:70%;', 'empty' => '[ Select City or Leave, if not listed ]')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('kebele', array('label' => 'Kebele: ')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('house_number', array('label' => 'House Number: ')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('address1', array('label' => 'Address: ')); ?>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
									<br><br>
								</div>

								<div class="large-6 columns">
									<table cellspacing="0" cellpading="0" class="table">
										<tbody>
											<tr>
												<td><strong>Your Primary Emergency Contact</strong></td>
											</tr>
											<tr>
												<td style="background-color: white;">
													<div class="large-12 columns">
														<?= $this->Form->input('Contact.0.first_name', array('label' => 'First Name: ', 'type' => 'text', 'required', 'onBlur' => 'checkIsAlpha(this)', 'div' => true)); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('Contact.0.middle_name', array('label' => 'Middle Name: ', 'type' => 'text', 'required', 'onBlur' => 'checkIsAlpha(this)')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('Contact.0.last_name', array('label' => 'Last Name: ', 'type' => 'text', 'required', 'onBlur' => 'checkIsAlpha(this)')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('Contact.0.country_id', array('label' => 'Country: ', 'id' => 'country_id_1', 'default' => COUNTRY_ID_OF_ETHIOPIA, 'style' => 'width:70%;', 'onchange' => 'updateRegionCity(1)')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('Contact.0.region_id', array('label' => 'Region: ', 'options' => $regionsAll, 'id' => 'region_id_1', 'empty' => '[ Select Region ]', /* 'onchange' => 'updateCity(1)', */ 'style' => 'width:70%;')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('Contact.0.zone_id', array('label' => 'Zone: ', 'options' => $zonesAll, 'id' => 'zone_id_1',  'empty' => '[ Select Zone ]', /* 'onchange' => 'updateCity(1)', */ 'style' => 'width:70%;')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('Contact.0.woreda_id', array('label' => 'Woreda: ', 'options' => $woredasAll, 'id' => 'woreda_id_1', 'empty' => '[ Select Woreda ]',  /* 'onchange' => 'updateCity(1)', */ 'style' => 'width:70%;')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('Contact.0.city_id', array('label' => 'City: ', 'options' => $citiesAll, 'id' => 'city_id_1', 'style' => 'width:70%;', 'empty' => '[ Select City or Leave, if not listed ]')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('Contact.0.email', array('type' => 'email', 'label' => 'Email: ')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('Contact.0.alternative_email', array('type' => 'email', 'label' => 'Alternative Email: ')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('Contact.0.phone_home', array('type' => 'tel', 'id' => 'intPhone1', 'label' => 'Phone (Home): ')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('Contact.0.phone_office', array('type' => 'tel', 'id' => 'intPhone2', 'label' => 'Phone (Office): ')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('Contact.0.phone_mobile', array('type' => 'tel', 'id' => 'phonemobile', 'label' => 'Phone (Mobile): ')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('Contact.0.address1', array('label' => 'Address: ')); ?>
													</div>
													<div class="large-12 columns">
														<hr>
														<?= $this->Form->input('Contact.0.primary_contact', array('label' => 'Primary Contact?', 'checked' => 'checked')); ?>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="content" id="education_background">

							<?php
							if (($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT && $studentDetail['Program']['id'] == PROGRAM_UNDEGRADUATE) || (!empty($this->data['HighSchoolEducationBackground']))) { ?>

								<hr style="margin-top: -10px;">
									<blockquote>
										<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
										<span style="text-align:justify;" class="fs15 text-black">Information you provide in this page should be properly formated and error free as <b style="text-decoration: underline;"><i class="rejected">it affects official transcript or student copy address contents</i></b>. <br> Please also make sure thay school name doesn't exceed more than 30 characters and replace spacial characters like - or ( or ) by a space if any found in school name. <br> If you want to add more than one record for the required information, you can use 'Add Additional School' or 'Add Additional Subject' buttons and make sure the information you are entering is chronologically ordered from the most recent to old for Highschool background information.</span>
									</blockquote>
								<hr>

								<?php

								$fields = array(
									'school_level' => '1',
									'name' => '2',
									'national_exam_taken' => '3',
									'region_id' => '4',
									'zone' => '5',
									'town' => '6',
								);

								$all_fields = "";
								$sep = "";

								foreach ($fields as $key => $tag) {
									$all_fields .= $sep . $key;
									$sep = ",";
								} ?>

								<div class="row">
									<div class="large-12 columns">
										<div style="overflow-x:auto;">
											<table cellpadding="0" cellspacing="0" class="table">
												<thead>
													<tr>
														<td colspan="7" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;"><h6 class="fs18 text-black">Senior Secondary/Preparatory School Attended</h6></td>
													</tr>
												</thead>
											</table>
											<table id="high_school_education" cellpadding="0" cellspacing="0" class="table">
												<thead>
													<tr>
														<th style="width: 3%;" class="center">#</th>
														<th style="width: 16%;" class="ccenter">School Level</th>
														<th style="width: 21%;" class="vcenter">Name</th>
														<th style="width: 15%;" class="center">National Exam Taken</th>
														<th style="width: 15%;" class="center">Region</th>
														<th style="width: 15%;" class="center">Zone</th>
														<th style="width: 15%;" class="center">Town</th>
													</tr>
												</thead>
												<tbody>
													<?php
													if (!empty($this->data['HighSchoolEducationBackground'])) {
														$count = 1;
														foreach ($this->data['HighSchoolEducationBackground'] as $bk => $bv) {
															echo $this->Form->hidden('HighSchoolEducationBackground.' . $bk . '.student_id', array('value' => $studentDetail['Student']['id']));
															if (!empty($bv['id'])) {
																echo $this->Form->hidden('HighSchoolEducationBackground.' . $bk . '.id');
															} ?>
															<tr>
																<td class="center"><?= $count; ?></td>
																<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HighSchoolEducationBackground.' . $bk . '.school_level', array('class' => "otherRequiredText-input", 'label' => false, 'style' => 'width:100%;', 'placeholder' => 'preparatory, highschool etc..', 'onBlur' => 'checkIsAlpha(this)', 'required')); ?></div></td>
																<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HighSchoolEducationBackground.' . $bk . '.name', array('class' => "otherRequiredText-input", 'label' => false, 'style' => 'width:100%;', 'onBlur' => 'checkIsAlpha(this)', 'required')); ?></div></td>
																<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HighSchoolEducationBackground.' . $bk . '.national_exam_taken', array('label' => false, 'style' => 'width:100%;')); ?></div></td>
																<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HighSchoolEducationBackground.' . $bk . '.region_id', array('options' => $regionsAll, 'style' => 'width:100%;', 'type' => 'select', 'label' => false, 'required')); ?></div></td>
																<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HighSchoolEducationBackground.' . $bk . '.zone', array('class' => "otherRequiredText-input", 'label' => false, 'type' => 'text', 'style' => 'width:100%;', 'onBlur' => 'checkIsAlpha(this)', 'required')); ?></div></td>
																<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HighSchoolEducationBackground.' . $bk . '.town', array('class' => "otherRequiredText-input", 'label' => false, 'style' => 'width:100%;', 'onBlur' => 'checkIsAlpha(this)', 'required')); ?></div></td>
															</tr>
															<?php
															$count++;
														}
													} else { ?>
														<tr>
															<td class="center">1</td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HighSchoolEducationBackground.0.school_level', array('class' => "otherRequiredText-input", 'label' => false, 'placeholder' => 'preparatory, highschool etc..', 'style' => 'width:100%;', 'onBlur' => 'checkIsAlpha(this)', 'required', 'value' => (isset($this->data['HighSchoolEducationBackground'][0]['school_level']) && !empty($this->data['HighSchoolEducationBackground'][0]['school_level']) ? $this->data['HighSchoolEducationBackground'][0]['school_level'] : (isset($studentDetail['AcceptedStudent']['high_school']) && !empty($studentDetail['AcceptedStudent']['high_school']) ? 'Preparatory' : '')))); ?></div></td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HighSchoolEducationBackground.0.name', array('class' => "otherRequiredText-input", 'label' => false,  'style' => 'width:100%;', 'onBlur' => 'checkIsAlpha(this)', 'required', 'value' => (isset($this->data['HighSchoolEducationBackground'][0]['name']) && !empty($this->data['HighSchoolEducationBackground'][0]['name']) ? $this->data['HighSchoolEducationBackground'][0]['name'] : (isset($studentDetail['AcceptedStudent']['high_school']) && !empty($studentDetail['AcceptedStudent']['high_school']) ? (ucwords(strtolower(trim($studentDetail['AcceptedStudent']['high_school'])))) : '')))); ?></div></td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HighSchoolEducationBackground.0.national_exam_taken', array('label' => false, 'style' => 'width:100%;', 'checked' => (isset($studentDetail['AcceptedStudent']['high_school']) && !empty($studentDetail['AcceptedStudent']['high_school']) ? 'checked' : false))); ?></div></td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HighSchoolEducationBackground.0.region_id', array('options' => $regionsAll, 'default' => (isset($this->data['HighSchoolEducationBackground'][0]['region_id']) && !empty($this->data['HighSchoolEducationBackground'][0]['region_id']) ? $this->data['HighSchoolEducationBackground'][0]['region_id'] : (isset($studentDetail['AcceptedStudent']['region_id']) && !empty($studentDetail['AcceptedStudent']['region_id']) ? $studentDetail['AcceptedStudent']['region_id'] :  (isset($studentDetail['Student']['region_id']) && !empty($studentDetail['Student']['region_id']) ? $studentDetail['Student']['region_id'] : ''))), 'type' => 'select',  'style' => 'width:100%;', 'label' => false, 'empty' => '[ Select Region ]')); ?></div></td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HighSchoolEducationBackground.0.zone', array('class' => "otherRequiredText-input", 'label' => false, 'type' => 'text', 'onBlur' => 'checkIsAlpha(this)', 'required', 'style' => 'width:100%;', 'value' => (isset($this->data['HighSchoolEducationBackground'][0]['zone']) && !empty($this->data['HighSchoolEducationBackground'][0]['zone']) ? $this->data['HighSchoolEducationBackground'][0]['zone'] : (isset($studentDetail['AcceptedStudent']['zone_id']) && !empty($studentDetail['AcceptedStudent']['zone_id']) ? $zones[$studentDetail['AcceptedStudent']['zone_id']] :  (isset($studentDetail['Student']['zone_id']) && !empty($studentDetail['Student']['zone_id']) ? $zones[$studentDetail['Student']['zone_id']] : ''))))); ?></div></td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HighSchoolEducationBackground.0.town', array('class' => "otherRequiredText-input", 'label' => false, 'style' => 'width:100%;', 'onBlur' => 'checkIsAlpha(this)', 'required')); ?></div></td>
														</tr>
														<?php
														echo $this->Form->hidden('HighSchoolEducationBackground.0.student_id', array('value' => $studentDetail['Student']['id']));
													} ?>
												</tbody>
											</table>

											<table cellpadding="0" cellspacing="0" class="table">
												<tr>
													<td colspan=7>
														<div style="padding-top: 10px;padding-bottom: 10px;">
															<input type="button" value="Add Additional School" onclick="addRow('high_school_education','HighSchoolEducationBackground',6,'<?= $all_fields; ?>')" /> &nbsp;  &nbsp;  &nbsp;
															<input type="button" value="Delete Last School" onclick="deleteRow('high_school_education')" />
														</div>
													</td>
												</tr>
											</table>

										</div>
										<br>
									</div>
								</div>
								<?php
							}

							if (($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT && ($studentDetail['Program']['id'] == PROGRAM_POST_GRADUATE || $studentDetail['Program']['id'] == PROGRAM_PhD )) || (!empty($this->data['HigherEducationBackground']))) { ?>

								<hr style="margin-top: -10px;">
									<blockquote>
										<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
										<span style="text-align:justify;" class="fs15 text-black">Information you provide in this page should be properly formated and error free as <b><i class="rejected">it affects official transcript or student copy address contents</i></b>.<br> If you want to add more than one record for the required information, you can use 'Add Additional Row' button and make sure the information you are entering is chronologically ordered from the most recent to old for Higher Education you attended.</span>
									</blockquote>
								<hr>

								<?php

								$higher_fields = array(
									'name' => '1',
									'field_of_study' => '2',
									'diploma_awarded' => '3',
									'date_graduated' => '4',
									'cgpa_at_graduation' => '5',
									'city' => '6'
								);

								$higher_all_fields = "";
								$sepp = "";

								foreach ($higher_fields as $key => $tag) {
									$higher_all_fields .= $sepp . $key;
									$sepp = ",";
								} ?>

								<div class="row">
									<div class="large-12 columns">
										<div style="overflow-x:auto;">
											<table cellpadding="0" cellspacing="0" class="table">
												<thead>
													<tr>
														<td colspan="7" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;"><h6 class="fs18 text-black">Higher Education Attended</h6></td>
													</tr>
												</thead>
											</table>
											<table id="higher_education_background" cellpadding="0" cellspacing="0" class="table">
												<thead>
													<tr>
														<th style="width: 3%;" class="center">#</th>
														<th style="width: 18%;" class="vcenter">Institution/College</th>
														<th style="width: 15%;" class="center">Field of study</th>
														<th style="width: 15%;" class="center">Diploma Awared</th>
														<th style="width: 26%;" class="center">Date Graduated</th>
														<th style="width: 8%;" class="center">CGPA</th>
														<th style="width: 15%;" class="center">City</th>
													</tr>
												</thead>
												<tbody>
													<?php
													if (!empty($this->data['HigherEducationBackground'])) {
														$count = 1;
														foreach ($this->data['HigherEducationBackground'] as $bk => $bv) {
															echo $this->Form->hidden('HigherEducationBackground.' . $bk . '.id');
															echo $this->Form->hidden('HigherEducationBackground.' . $bk . '.student_id', array('value' => $studentDetail['Student']['id'])); ?>
															<tr>
																<td class="center"><?= $count; ?></td>
																<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HigherEducationBackground.' . $bk . '.name', array('class' => "otherRequiredText-input", 'required', 'onBlur' => 'checkIsAlpha(this)', 'label' => false, 'style' => 'width:100%;')); ?></div></td>
																<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HigherEducationBackground.' . $bk . '.field_of_study', array('class' => "otherRequiredText-input", 'required', 'onBlur' => 'checkIsAlpha(this)', 'label' => false, 'style' => 'width:100%;')); ?></div></td>
																<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HigherEducationBackground.' . $bk . '.diploma_awarded', array('class' => "otherRequiredText-input", 'required', 'onBlur' => 'checkIsAlpha(this)', 'label' => false, 'style' => 'width:100%;')); ?></div></td>
																<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HigherEducationBackground.' . $bk . '.date_graduated', array('required', 'label' => false, 'style' => 'width:30%;', 'minYear' =>  (isset($student_admission_year) && !empty($student_admission_year) ? ($student_admission_year - 20) : date('Y') - 30), 'maxYear' => (isset($student_admission_year) && !empty($student_admission_year) ?  $student_admission_year : (date('Y') - 1)), /* 'orderYear' => 'desc',  */)); ?></div></td>
																<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HigherEducationBackground.' . $bk . '.cgpa_at_graduation', array('class' => "cgpa-input", 'required', 'label' => false, 'placeholder' => 'CGPA', 'type' => 'text', /* 'min' => '2.00', 'max' => '4.00', 'step' => '0.01', */ 'onBlur' => 'checkCGPA(this)')); ?></div></td>
																<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HigherEducationBackground.' . $bk . '.city', array('class' => "otherRequiredText-input", 'required', 'onBlur' => 'checkIsAlpha(this)', 'style' => 'width:100%;', 'label' => false, 'type' => 'text')); ?></div></td>
															</tr>
															<?php
															$count++;
														}
													} else { ?>
														<tr>
															<td class="center">1</td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HigherEducationBackground.0.name', array('class' => "otherRequiredText-input", 'required', 'onBlur' => 'checkIsAlpha(this)', 'label' => false, 'placeholder' => 'Name of the Institution..')); ?></div></td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HigherEducationBackground.0.field_of_study', array('class' => "otherRequiredText-input", 'required', 'onBlur' => 'checkIsAlpha(this)', 'label' => false, 'placeholder' => 'Field of Study..' )); ?></div></td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HigherEducationBackground.0.diploma_awarded', array('class' => "otherRequiredText-input", 'required', 'onBlur' => 'checkIsAlpha(this)', 'label' => false, 'placeholder' => 'BSc, MSc, BA, MA..')); ?></div></td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HigherEducationBackground.0.date_graduated', array('required', 'label' => false, 'style' => 'width:30%;', 'minYear' =>  (isset($student_admission_year) && !empty($student_admission_year) ? ($student_admission_year - 20) : date('Y') - 30), 'maxYear' => (isset($student_admission_year) && !empty($student_admission_year) ?  $student_admission_year : (date('Y') - 1)), /* 'orderYear' => 'desc',  */)); ?></div></td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HigherEducationBackground.0.cgpa_at_graduation', array('class' => "cgpa-input", 'required', 'label' => false, 'placeholder' => 'CGPA', 'type' => 'text', /* 'min' => '2.00', 'max' => '4.00', 'step' => '0.01', */ 'onBlur' => 'checkCGPA(this)')); ?></div></td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('HigherEducationBackground.0.city', array('class' => "otherRequiredText-input", 'required', 'onBlur' => 'checkIsAlpha(this)', 'style' => 'width:100%;', 'label' => false, 'type' => 'text', 'placeholder' => 'City..')); ?></div></td>
														</tr>
														<?php
														echo $this->Form->hidden('HigherEducationBackground.0.student_id', array('value' => $studentDetail['Student']['id']));
													} ?>
												</tbody>
											</table>
											<table cellpadding="0" cellspacing="0" class="table">
												<tr>
													<td colspan=7>
														<div style="padding-top: 10px;padding-bottom: 10px;">
															<input type="button" value="Add Additional Row" onclick="addRow('higher_education_background','HigherEducationBackground',6,'<?= $higher_all_fields; ?>')" />  &nbsp;  &nbsp;  &nbsp;
															<input type="button" value="Delete Last Row" onclick="deleteRow('higher_education_background')" />
														</div>
													</td>
												</tr>
											</table>
										</div>
										<br>
									</div>
								</div>
								<?php
							}


							$from = date('Y') - 30;
							$to = date('Y') - 1;
							$format = Configure::read('Calendar.yearFormat');
							$yearoptions = array();

							for ($j = $to ; $j >= $from; $j--) {
								$yearoptions[$j] = $j;
							} ?>


							<div class="row">
								<?php
								if (($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT && $studentDetail['Program']['id'] == PROGRAM_UNDEGRADUATE && ALLOW_ESLCE_RESULTS_TO_BE_FILLED_FOR_UNDER_GRADUATE_STUDENTS == 1) || (!empty($this->data['EslceResult']))) {

									$eslce_fields = array('subject' => '1', 'grade' => '2', 'exam_year' => '3');
									$eslce_all_fields = "";
									$sepeslce = "";

									foreach ($eslce_fields as $key => $tag) {
										$eslce_all_fields .= $sepeslce . $key;
										$sepeslce = ",";
									}  ?>

									<div class="large-6 columns">
										<div style="overflow-x:auto;">
											<table cellpadding="0" cellspacing="0" class="table">
												<thead>
													<tr>
														<td colspan="4" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;"><h6 class="fs18 text-black">ESLCE Results (10th Grade)</h6></td>
													</tr>
												</thead>
											</table>
											<table id='eslce_result' cellpadding="0" cellspacing="0" class="table">
												<thead>
													<tr>
														<th style="width: 5%;" class="center">#</th>
														<th style="width: 45%;" class="vcenter">Subject</th>
														<th style="width: 20%;" class="center">Grade</th>
														<th style="width: 30%;" class="center">Exam Year</th>
													</tr>
												</thead>
												<tbody>
													<?php
													if (!empty($this->data['EslceResult'])) {
														$count = 0;
														foreach ($this->data['EslceResult'] as $bk => $bv) {
															echo $this->Form->hidden('EslceResult.' . $bk . '.id');
															echo $this->Form->hidden('EslceResult.' . $bk . '.student_id', array('value' => $studentDetail['Student']['id'])); ?>
															<tr>
																<td class="center"><?= ++$count; ?></td>
																<td class="center"><?= $this->Form->input('EslceResult.' . $bk . '.subject', array('name' => "data[EslceResult][$bk][subject]", 'value' => isset($this->data['EslceResult'][$bk]['subject']) ? $this->data['EslceResult'][$bk]['subject'] : '', 'style' => 'width:100%;',  'label' => false)); ?></td>
																<td class="center"><?= $this->Form->input('EslceResult.' . $bk . '.grade', array('name' => "data[EslceResult][$bk][grade]", 'value' => isset($this->data['EslceResult'][$bk]['grade']) ? $this->data['EslceResult'][$bk]['grade'] : '', 'style' => 'width:100%;',  'label' => false)); ?></td>
																<td class="center"><?= $this->Form->input('EslceResult.' . $bk . '.exam_year', array('value' => isset($this->data['EslceResult'][$bk]['exam_year']) ? $this->data['EslceResult'][$bk]['exam_year'] : '',  'label' => false, 'style' => 'width:100%;', 'type' => 'select', 'options' => $yearoptions, 'selected' => !empty($this->data['EslceResult'][$bk]['exam_year']) ? $this->data['EslceResult'][$bk]['exam_year'] : '')); ?></td>
															</tr>
															<?php
														}
													} else { ?>
														<tr>
															<td class="center">1</td>
															<td class="center"><?= $this->Form->input('EslceResult.0.subject', array('name' => "data[EslceResult][0][subject]", 'value' => isset($this->data['EslceResult'][0]['subject']) ? $this->data['EslceResult'][0]['subject'] : '',  'style' => 'width:100%;', 'label' => false)); ?></td>
															<td class="center"><?= $this->Form->input('EslceResult.0.grade', array('name' => "data[EslceResult][0][grade]", 'value' => isset($this->data['EslceResult'][0]['grade']) ? $this->data['EslceResult'][0]['grade'] : '', 'style' => 'width:100%;',  'label' => false)); ?></td>
															<td class="center"><?= $this->Form->input('EslceResult.0.exam_year', array('name' => "data[EslceResult][0][exam_year]", 'value' => isset($this->data['EslceResult'][0]['exam_year']) ? $this->data['EslceResult'][0]['exam_year'] : '',  'label' => false, 'style' => 'width:100%;', 'type' => 'select', 'options' => $yearoptions, 'empty' => '[ Select Year ]')); ?></td>
														</tr>
														<?php
														echo $this->Form->hidden('EslceResult.0.student_id', array('value' => $studentDetail['Student']['id']));
													} ?>
												</tbody>
											</table>
											<table cellpadding="0" cellspacing="0" class="table">
												<tr>
													<td colspan=4>
														<div style="padding-top: 10px;padding-bottom: 10px;">
															<input type="button" value="Add Additional Subject" onclick="addRow('eslce_result','EslceResult',3,'<?= $eslce_all_fields; ?>','<?= $from ?>')" />  &nbsp;  &nbsp;  &nbsp;
															<input type="button" value="Delete Last Subject" onclick="deleteRow('eslce_result')" />
														</div>
													</td>
												</tr>
											</table>
										</div>
										<br>
									</div>
									<?php
								}

								if ((/* $ethiopianStudent &&  */$this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT && ($studentDetail['Program']['id'] == PROGRAM_UNDEGRADUATE)) || (isset($this->data['EheeceResult'][0]['subject']) && !empty($this->data['EheeceResult'][0]['subject']))) {

									$eheece_fields = array('subject' => '1', 'mark' => '2'/* , 'exam_year' => '3' */);
									$eheece_all_fields = "";
									$sepeheece = "";

									foreach ($eheece_fields as $key => $tag) {
										$eheece_all_fields .= $sepeheece . $key;
										$sepeheece = ",";
									}  ?>

									<div class="large-6 columns">
										<div style="overflow-x:auto;">
											<table cellpadding="0" cellspacing="0" class="table">
												<thead>
													<tr>
														<td colspan="4" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
															<h6 class="fs18 text-black">EHEECE Results (12th Grade)</h6>
															<hr>
															<?php //echo $this->Form->input('EheeceResult.0.exam_year', array('value' => (!empty($this->data['EheeceResult'][0]['exam_year']) ? $this->data['EheeceResult'][0]['exam_year'] : ''),  'label' => 'Exam Taken Date: ', 'style' => 'width:25%;', 'type' => 'date', 'minYear' => (date('Y') - 10), 'maxYear' => date('Y'))); ?>
															<?= $this->Form->input('EheeceResult.0.exam_year', array('value' => (!empty($this->data['EheeceResult'][0]['exam_year']) ? $this->data['EheeceResult'][0]['exam_year'] : ''),  'label' => 'Exam Taken Date: ', 'style' => 'width:25%;', 'type' => 'date', 'minYear' => (date('Y') - 10), 'maxYear' => (isset($student_admission_year) ? $student_admission_year : date('Y')))); ?>
														</td>
													</tr>
												</thead>
											</table>
											<table id='eheece_result' cellpadding="0" cellspacing="0" class="table">
												<thead>
													<tr>
														<th style="width: 5%;" class="center">#</th>
														<th style="width: 45%;" class="vcenter">Subject</th>
														<th style="width: 20%;" class="center">Mark</th>
														<!-- <th style="width: 30%;" class="center">Exam Year</th> -->
													</tr>
												</thead>
												<tbody>
													<?php
													if (!empty($this->data['EheeceResult'])) {
														$count = 0;
														foreach ($this->data['EheeceResult'] as $bk => $bv) {
															echo $this->Form->hidden('EheeceResult.' . $bk . '.id');
															echo $this->Form->hidden('EheeceResult.' . $bk . '.student_id', array('value' => $studentDetail['Student']['id'])); ?>
															<tr>
																<td class="center"><?= ++$count; ?></td>
																<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('EheeceResult.' . $bk . '.subject', array('required', 'class' => "subject-input", 'name' => "data[EheeceResult][$bk][subject]", 'value' => (isset($this->data['EheeceResult'][$bk]['subject']) ? $this->data['EheeceResult'][$bk]['subject'] : ''), 'style' => 'width:100%;', /* 'pattern' => 'alpha', */ 'onBlur' => 'checkIsAlpha(this)', 'placeholder' => 'Subject 1', 'label' => false)); ?></div></td>
																<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('EheeceResult.' . $bk . '.mark', array('class' => "subjectMark-input", 'required', 'onBlur' => 'checkValidMarkInput(this)', 'name' => "data[EheeceResult][$bk][mark]", 'required', 'value' => (isset($this->data['EheeceResult'][$bk]['mark']) ? $this->data['EheeceResult'][$bk]['mark'] : ''),  'style' => 'width:100%;', 'placeholder' => 'Mark 1', 'label' => false, 'min' => '0', 'max' => '100', 'step' => 'any')); ?></div></td>
																<!-- <td class="center"><?php //echo $this->Form->input('EheeceResult.' . $bk . '.exam_year', array('name' => "data[EheeceResult][$bk][exam_year]", 'value' => (!empty(explode('-', $this->data['EheeceResult'][$bk]['exam_year'])[0]) ? (explode('-', $this->data['EheeceResult'][$bk]['exam_year'])[0]) : ''),  'label' => false, 'style' => 'width:100%;', 'type' => 'select', 'options' => $yearoptions, 'default' => !empty((explode('-', $this->data['EheeceResult'][$bk]['exam_year'])[0])) ? (explode('-', $this->data['EheeceResult'][$bk]['exam_year'])[0]) : '')); ?></td> -->
															</tr>
															<?php
														}
													} else { ?>
														<tr>
															<td class="center">1</td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('EheeceResult.0.subject', array('required', 'class' => "subject-input", 'name' => "data[EheeceResult][0][subject]", 'value' => (isset($this->data['EheeceResult'][0]['subject']) ? $this->data['EheeceResult'][0]['subject'] : ''), /* 'pattern' => 'alpha',  */ 'onBlur' => 'checkIsAlpha(this)', 'placeholder' => 'Subject 1',  'style' => 'width:100%;', 'label' => false)); ?></div></td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('EheeceResult.0.mark', array('class' => "subjectMark-input", 'required', 'onBlur' => 'checkValidMarkInput(this)', 'name' => "data[EheeceResult][0][mark]", 'value' => (isset($this->data['EheeceResult'][0]['mark']) ? $this->data['EheeceResult'][0]['mark'] : ''),  'label' => false, 'style' => 'width:100%;', 'placeholder' => 'Mark 1', 'min' => '0', 'max' => '100', 'step' => 'any')); ?></div></td>
															<!-- <td class="center"><?php //echo $this->Form->input('EheeceResult.0.exam_year', array('name' => "data[EheeceResult][0][exam_year]",  'label' => false, 'type' => 'select', 'options' => $yearoptions, 'empty' => '[ Select Year ]', 'style' => 'width:100%;')); ?></td> -->
														</tr>
														<tr>
															<td class="center">2</td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('EheeceResult.1.subject', array('required', 'class' => "subject-input", 'name' => "data[EheeceResult][1][subject]", 'value' => (isset($this->data['EheeceResult'][1]['subject']) ? $this->data['EheeceResult'][1]['subject'] : ''), /* 'pattern' => 'alpha', */ 'onBlur' => 'checkIsAlpha(this)', 'placeholder' => 'Subject 2',  'style' => 'width:100%;', 'label' => false)); ?></div></td>
															<td class="center"><div style="margin-top: 10px;"><?= $this->Form->input('EheeceResult.1.mark', array('class' => "subjectMark-input", 'required', 'onBlur' => 'checkValidMarkInput(this)', 'name' => "data[EheeceResult][1][mark]", 'value' => (isset($this->data['EheeceResult'][1]['mark']) ? $this->data['EheeceResult'][1]['mark'] : ''),  'label' => false, 'style' => 'width:100%;', 'placeholder' => 'Mark 2', 'min' => '0', 'max' => '100', 'step' => 'any')); ?></div></td>
														</tr>
														<?php
														echo $this->Form->hidden('EheeceResult.0.student_id', array('value' => $studentDetail['Student']['id']));
													} ?>
												</tbody>
											</table>
											<table cellpadding="0" cellspacing="0" class="table">
												<tr>
													<td colspan=4>
														<div style="padding-top: 10px;padding-bottom: 10px;">
															<input type="button" value="Add Additional Subject" onclick="addRow('eheece_result','EheeceResult',2,'<?= $eheece_all_fields; ?>','<?= $from; ?>')" />  &nbsp;  &nbsp;  &nbsp;
															<input type="button" value="Delete Last Subject" onclick="deleteRow('eheece_result')" />
														</div>
													</td>
												</tr>
											</table>
										</div>
										<br>
									</div>
									<?php
								} ?>

							</div>
						</div>
					</div>

					<hr>

					<h6 class="fs13 warning-box" style="font-weight: normal;">Inputs/fields marked <b class="rejected">*</b> are required and you have to select or provide the required information, not marked fields are optional. Please check all tabs before updating your profile.</h6>
					<!-- <br> -->

					<?= $this->Form->end(array('label' => 'Update Student Detail', /* 'disabled', */ 'name' => 'updateStudentDetail', 'id' => 'updateStudentDetail', 'class' => 'tiny radius button bg-blue')); ?>

				</div>
			</div>
		</div>
	</div>
	<?php
} ?>

<script type="text/javascript">

function toggleSubmitButtonActive() {
		if ($("#email").val != 0 && $("#email").val != '') {
			$("#SubmitID").attr('disabled', false);
		}
	}

	function isValidPhonenumber(value) {
    	return (/^\d{7,}$/).test(value.replace(/[\s()+\-\.]|ext/gi, ''));
	}

	function isValidEmail(value) {
    	return (/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/).test(value.trim());
	}

	function isAlpha(value) {
    	return (/^[a-zA-Z]+$/).test(value.trim());
	}

	function checkCGPA(obj) {

		let message = document.getElementById("customMessageCGPA");

		if (isNaN(obj.value) || obj.value == '' || obj.value < 2.00 || obj.value > 4.00) {
			obj.style.border = '2px solid red';

			if (!message) {
				message = document.createElement("div");
				message.id = "customMessageCGPA";
				document.body.appendChild(message);
			}

			message.innerText = 'Please enter a valid CGPA between 2.00 and 4.00';
			message.style.position = 'absolute';
			message.style.backgroundColor = '#f8d7da';
			message.style.color = '#721c24';
			message.style.border = '1px solid #f5c6cb';
			message.style.padding = '5px';
			message.style.zIndex = '1000';

			const rect = obj.getBoundingClientRect();
			message.style.top = `${rect.top + window.scrollY + obj.offsetHeight + 5}px`;
			message.style.left = `${rect.left + window.scrollX}px`;

			obj.focus();

			// Remove the message after a few seconds
			setTimeout(() => {
				message.remove();
			}, 3000);

			return false;
		} else {
			obj.style.border = '2px solid #ccc';

			if (message) {
				message.remove();
			}

			return true;
		}
	}

	function checkValidMarkInput(obj) {

		let message = document.getElementById("customMessageMark");

		if (isNaN(obj.value) || obj.value == '' || obj.value < 1 || obj.value > 100) {
			obj.style.border = '2px solid red';

			if (!message) {
				message = document.createElement("div");
				message.id = "customMessageMark";
				document.body.appendChild(message);
			}

			message.innerText = 'Please enter a valid Mark between 1 and 100';
			message.style.position = 'absolute';
			message.style.backgroundColor = '#f8d7da';
			message.style.color = '#721c24';
			message.style.border = '1px solid #f5c6cb';
			message.style.padding = '5px';
			message.style.zIndex = '1000';

			const rect = obj.getBoundingClientRect();
			message.style.top = `${rect.top + window.scrollY + obj.offsetHeight + 5}px`;
			message.style.left = `${rect.left + window.scrollX}px`;

			obj.focus();

			// Remove the message after a few seconds
			setTimeout(() => {
				message.remove();
			}, 3000);

			return false;
		} else {
			obj.style.border = '2px solid #ccc';

			if (message) {
				message.remove();
			}

			return true;
		}
	}

	function capitalizeFirstLetterOfEachWord(str) {
		return str.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ');
	}

	function capitalizeWordsExcludePrepositions(str) {

		const prepositions = ['and', 'or', 'of', 'in', 'on', 'at', 'with', 'from', 'by', 'about', 'as', 'into', 'like', 'through', 'after', 'over', 'between', 'out', 'against', 'during', 'without', 'before', 'under', 'around', 'among'];

		return str.split(' ').map(word => {
			if (prepositions.includes(word.toLowerCase())) {
				return word.toLowerCase();
			} else {
				return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
			}
		}).join(' ');

	}

	function checkIsAlpha(obj) {
		//const pattern = /^[a-zA-Z]+$/; //doesn't support space, only single word is allowed
		const pattern = /^[a-zA-Z\s]+$/; // support space, string allowed
		let message = document.getElementById("customMessage");

		// Trim preceding and trailing spaces
		//obj.value = obj.value.trim();

		// Trim preceding and trailing spaces and capitalize each word if a string.
		//obj.value = capitalizeFirstLetterOfEachWord(obj.value.trim());

		// Trim preceding and trailing spaces and capitalize each word if a string and exclude prepositions
		obj.value = capitalizeWordsExcludePrepositions(obj.value.trim());

		if (!pattern.test(obj.value)) {
			obj.style.border = '2px solid red';

			if (!message) {
				message = document.createElement("div");
				message.id = "customMessage";
				document.body.appendChild(message);
			}

			message.innerText = 'Please use only alphabets, avoid adding special charachters like / ( ) & etc';
			message.style.position = 'absolute';
			message.style.backgroundColor = '#f8d7da';
			message.style.color = '#721c24';
			message.style.border = '1px solid #f5c6cb';
			message.style.padding = '5px';
			message.style.zIndex = '1000';

			const rect = obj.getBoundingClientRect();
			message.style.top = `${rect.top + window.scrollY + obj.offsetHeight + 5}px`;
			message.style.left = `${rect.left + window.scrollX}px`;

			obj.focus();

			// Remove the message after a few seconds
			setTimeout(() => {
				message.remove();
			}, 3000);

			return false;
		} else {
			obj.style.border = '2px solid #ccc';

			if (message) {
				message.remove();
			}

			return true;
		}
	}

	var form_being_submitted = false;
	var ethiopianStudent = <?= json_encode($ethiopianStudent); ?>;
	var ugProgram = <?= json_encode($ugProgram); ?>;

	//alert(ethiopianStudent);
	//alert(ugProgram);

	$('#updateStudentDetail').click(function(event) {

		var isValid = true;

		if (ethiopianStudent) {
			if ($('#AmharicText').val() == '') {
				alert('Please provide amharic first name.');
				$('#AmharicText').focus();
				return false;
			}

			if ($('#AmharicTextMiddleName').val() == '') {
				alert('Please provide amharic middle name.');
				$('#AmharicTextMiddleName').focus();
				return false;
			}

			if ($('#AmharicTextLastName').val() == '') {
				alert('Please provide amharic last name.');
				$('#AmharicTextLastName').focus();
				return false;
			}
		}

		if ($('#email').val() == '') {
			alert('Please provide your primary personal email address.');
			$('#email').focus();
			return false;
		} else if ($('#email').val() != '' && !isValidEmail($('#email').val())) {
			alert('Please provide valid email address. Invalif email address.');
			$('#email').focus();
			return false;
		}

		if ($('#etPhone').val() == '') {
			alert('Please provide mobile phone number without a leading 0.');
			$('#etPhone').focus();
			return false;
		} else if ($('#etPhone').val() != '' && $('#etPhone').val().length != 13) {
			alert('Mobile phone number format is invalid. Please check mobile number length is 13 including +251.');
			$('#etPhone').focus();
			return false;
		}

		if ($('#zone_id_2').val() == '') {
			alert('Please select Zone from Address & Primary Contact tab.');
			$('#zone_id_2').focus();
			return false;
		}

		if ($('#woreda_id_2').val() == '') {
			alert('Please select woreda from Address & Primary Contact tab.');
			$('#woreda_id_2').focus();
			return false;
		}

		/* var inputs = document.querySelectorAll('#StudentProfileForm input[required]');

		for (var i = 0; i < inputs.length; i++) {
			if (!inputs[i].value) {
				isValid = false;
				inputs[i].focus();
				break;
			}
		}

		if (!isValid) {
			alert("Please fill out all required fields in all tabs.");
			return false;
		} */

		document.querySelectorAll('#StudentProfileForm input[required]').forEach(function(input) {
			if (!input.value && input.getAttribute("type") === "select") {
				isValid = false;
				input.focus();
				return false;
			}
		});


		document.querySelectorAll('#StudentProfileForm input[required]').forEach(function(input) {
			if (!input.value) {
				isValid = false;
				//input.style.border = '2px solid red';
				if (input.getAttribute("type") === "select") {
					input.focus();
					return false;
				} else if (input.getAttribute("type") !== "email" && input.getAttribute("type") !== "tel") {
					input.style.border = '2px solid red';
					if (!input.hasAttribute('highlighted')) {
						input.setAttribute('highlighted', 'true');
						input.focus();
						return false; // Stop further iterations to focus on the first empty input
					}
				}
			} else {
				input.style.border = ''; // Remove red border if the input is filled
				input.removeAttribute('highlighted');
			}
		});

		if ($('#region_id_1').val() == '') {
			alert('Please select your primary emergency contatct person Region from Address & Primary Contact tab.');
			$('#region_id_1').focus();
			return false;
		}

		if ($('#zone_id_1').val() == '') {
			alert('Please select your primary emergency contatct person Zone from Address & Primary Contact tab.');
			$('#zone_id_1').focus();
			return false;
		}

		if ($('#woreda_id_1').val() == '') {
			alert('Please select your primary emergency contatct person Woreda from Address & Primary Contact tab.');
			$('#woreda_id_1').focus();
			return false;
		}


		if ($('#phonemobile').val() == '') {
			alert('Please provide your primary emergency contact mobile number in Address & Primary Contact tab.');
			$('#phonemobile').focus();
			return false;
		} else if ($('#phonemobile').val() != '' && $('#phonemobile').val().length != 13) {
			alert('Please provide your a valid primary emergency contact mobile number in Address & Primary Contact tab.');
			$('#phonemobile').focus();
			return false;
		}

		if (!isValidPhonenumber($('#phonemobile').val())) {
			alert('Please provide your a valid primary emergency contact mobile number in Address & Primary Contact tab.');
			$('#phonemobile').focus();
			return false;
		}

		/* document.querySelectorAll('#StudentProfileForm input[type="text"][required]').forEach(function(input) {
			if (!input.value) {
				isValid = false;
				input.style.border = '2px solid red';
				if (!input.hasAttribute('highlighted')) {
					input.setAttribute('highlighted', 'true');
					input.focus();
					return false; // Stop further iterations to focus on the first empty input
				}
			} else {
				input.style.border = ''; // Remove red border if the input is filled
				input.removeAttribute('highlighted');
			}
		}); */


		/* var inputs = document.querySelectorAll('#StudentProfileForm input[required]');

		for (var i = 0; i < inputs.length; i++) {
			if (!inputs[i].value) {
				isValid = false;
				inputs[i].focus();
				break;
			}
		}

		if (!isValid) {
			alert("Please fill out all required fields in all tabs.");
			return false;
		} */

		document.querySelectorAll('.otherRequiredText-input').forEach(function(inputField) {
            if (!checkIsAlpha(inputField)) {
				inputField.focus();
                isValid = false;
				return false;
            }
        });

		if (!ugProgram) {
			document.querySelectorAll('.cgpa-input').forEach(function(inputField) {
				if (!checkCGPA(inputField)) {
					inputField.focus();
					isValid = false;
					return false;
				}
			});
		}

		document.querySelectorAll('.subject-input').forEach(function(inputField) {
            if (!checkIsAlpha(inputField)) {
				inputField.focus();
                isValid = false;
				return false;
            }
        });

		document.querySelectorAll('.subjectMark-input').forEach(function(inputField) {
            if (!checkValidMarkInput(inputField)) {
				inputField.focus();
                isValid = false;
				return false;
            }
        });


		if (!isValid) {
			alert("Please fill out all required fields in all tabs including Educational Background tab and ensure that the required fieds are not empty or selected.");
			return false;
		}

		if (ugProgram && $('#HighSchoolEducationBackground0Name').val().length) {
			const highSchoolNameLength = $('#HighSchoolEducationBackground0Name').val().length;
			const minLength = 5;
			const maxLength = 30;

			if (highSchoolNameLength < minLength || highSchoolNameLength > maxLength) {
				alert(`High School Name Length must be between ${minLength} and ${maxLength} characters long. please make an appropraite adjustment by shortening shool name.`);
				$('#HighSchoolEducationBackground0Name').focus();
				return false;
			}
		}

		if (form_being_submitted) {
			alert("Updating Student Profile, please wait a moment or refresh your browser.");
			$('#updateStudentDetail').attr('disabled', true);
			return false;
		}

		if (!form_being_submitted && isValid) {
			$('#updateStudentDetail').val('Updating Student Profile...');
			form_being_submitted = true;
			return true;
		} else {
			return false;
		}
	});


	/* var countryId = $("#country_id_2").val();

	var regionId2 = $("#region_id_2").val();

	if (countryId == '' && regionId2) {
		$.ajax({
			url: '/students/get_countries/' + regionId2,
			type: 'get',
			data: regionId2,
			success: function(data, textStatus, xhr) {
				$('#country_id_2').attr('disabled', false);
				$('#country_id_2').empty();
				$('#country_id_2').append(data);

				//$('#region_id_2').empty().append('<option value="">[ Select Region ]</option>');
				$('#zone_id_2').empty().append('<option value="">[ Select Zone ]</option>');
				$('#woreda_id_2').empty().append('<option value="">[ Select Woreda ]</option>');
				$('#city_id_2').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});

		//return false;
	} */


	////// For Student Demographic Information ///////////

	// get regions based on selected country

	$('#country_id_2').change(function() {

		var countryId = $(this).val();

		$('#region_id_2').attr('disabled', true);
		$('#zone_id_2').attr('disabled', true);
		$('#woreda_id_2').attr('disabled', true);
		$('#city_id_2').attr('disabled', true);

		if (countryId) {
			$.ajax({
				url: '/students/get_regions/' + countryId,
				type: 'get',
				data: countryId,
				success: function(data, textStatus, xhr) {
					$('#region_id_2').attr('disabled', false);
					$('#region_id_2').empty();
					$('#region_id_2').append(data);

					$('#zone_id_2').empty().append('<option value="">[ Select Zone ]</option>');
					$('#woreda_id_2').empty().append('<option value="">[ Select Woreda ]</option>');
					$('#city_id_2').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;

		} else {
			$('#region_id_2').empty().append('<option value="">[ Select Region ]</option>');
			$('#zone_id_2').empty().append('<option value="">[ Select Zone ]</option>');
			$('#woreda_id_2').empty().append('<option value="">[ Select Woreda ]</option>');
			$('#city_id_2').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
		}
	});

	// Load zone options based on selected region
	$('#region_id_2').change(function() {

		var regionId = $(this).val();

		$('#zone_id_2').attr('disabled', true);
		$('#woreda_id_2').attr('disabled', true);
		$('#city_id_2').attr('disabled', true);

		if (regionId) {
			$.ajax({
				url: '/students/get_zones/'+ regionId,
				type: 'get',
				data: regionId,
				success: function(data, textStatus, xhr) {
					$('#zone_id_2').attr('disabled', false);
					$('#zone_id_2').empty();
					$('#zone_id_2').append(data);

					$('#woreda_id_2').empty().append('<option value="">[ Select Woreda ]</option>');
					$('#city_id_2').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;

		} else {
			$('#zone_id_2').empty().append('<option value="">[ Select Zone ]</option>');
			$('#woreda_id_2').empty().append('<option value="">[ Select Woreda ]</option>');
			$('#city_id_2').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
		}
	});

	// Load woreda options based on selected zone
	$('#zone_id_2').change(function() {

		var zoneId = $(this).val();

		$('#woreda_id_2').attr('disabled', true);
		$("#city_id_2").attr('disabled', true);

		if (zoneId) {
			$.ajax({
				url: '/students/get_woredas/'+ zoneId,
				type: 'get',
				data: zoneId,
				success: function(data, textStatus, xhr) {
					$('#woreda_id_2').attr('disabled', false);
					$('#woreda_id_2').empty();
					$('#woreda_id_2').append(data);

					// sub category
					var regionId = $("#region_id_2").val();
					$("#city_id_2").empty();

					$.ajax({
						type: 'get',
						url: '/students/get_cities/' + regionId,
						data: regionId,
						success: function(data, textStatus, xhr) {
							$("#city_id_2").attr('disabled', false);
							$("#city_id_2").empty();
							$("#city_id_2").append(data);
						},
						error: function(xhr, textStatus, error) {
							alert(textStatus);
						}
					});

					// end of sub category
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;

		} else {
			$('#woreda_id_2').empty().append('<option value="">[ Select Woreda ]</option>');
			$('#city_id_2').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
		}
	});

	////// END For Student Demographic Information ///////////

	//////  END For Emergency Contact  Information///////////

	// get regions based on selected country

	$('#country_id_1').change(function() {

		var countryId = $(this).val();

		$('#region_id_1').attr('disabled', true);
		$('#zone_id_1').attr('disabled', true);
		$('#woreda_id_1').attr('disabled', true);
		$('#city_id_1').attr('disabled', true);

		if (countryId) {
			$.ajax({
				url: '/students/get_regions/' + countryId,
				type: 'get',
				data: countryId,
				success: function(data, textStatus, xhr) {
					$('#region_id_1').attr('disabled', false);
					$('#region_id_1').empty();
					$('#region_id_1').append(data);

					$('#zone_id_1').empty().append('<option value="">[ Select Zone ]</option>');
					$('#woreda_id_1').empty().append('<option value="">[ Select Woreda ]</option>');
					$('#city_id_1').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;

		} else {
			$('#region_id_1').empty().append('<option value="">[ Select Region ]</option>');
			$('#zone_id_1').empty().append('<option value="">[ Select Zone ]</option>');
			$('#woreda_id_1').empty().append('<option value="">[ Select Woreda ]</option>');
			$('#city_id_1').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
		}
	});

	// Load zone options based on selected region
	$('#region_id_1').change(function() {

		var regionId = $(this).val();

		$('#zone_id_1').attr('disabled', true);
		$('#woreda_id_1').attr('disabled', true);
		$('#city_id_1').attr('disabled', true);

		if (regionId) {
			$.ajax({
				url: '/students/get_zones/'+ regionId,
				type: 'get',
				data: regionId,
				success: function(data, textStatus, xhr) {
					$('#zone_id_1').attr('disabled', false);
					$('#zone_id_1').empty();
					$('#zone_id_1').append(data);

					$('#woreda_id_1').empty().append('<option value="">[ Select Woreda ]</option>');
					$('#city_id_1').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;

		} else {
			$('#zone_id_1').empty().append('<option value="">[ Select Zone ]</option>');
			$('#woreda_id_1').empty().append('<option value="">[ Select Woreda ]</option>');
			$('#city_id_1').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
		}
	});

	// Load woreda options based on selected zone
	$('#zone_id_1').change(function() {

		var zoneId = $(this).val();

		$('#woreda_id_1').attr('disabled', true);
		$("#city_id_1").attr('disabled', true);

		if (zoneId) {
			$.ajax({
				url: '/students/get_woredas/'+ zoneId,
				type: 'get',
				data: zoneId,
				success: function(data, textStatus, xhr) {
					$('#woreda_id_1').attr('disabled', false);
					$('#woreda_id_1').empty();
					$('#woreda_id_1').append(data);

					// sub category
					var regionId = $("#region_id_1").val();
					$("#city_id_1").empty();

					$.ajax({
						type: 'get',
						url: '/students/get_cities/' + regionId,
						data: regionId,
						success: function(data, textStatus, xhr) {
							$("#city_id_1").attr('disabled', false);
							$("#city_id_1").empty();
							$("#city_id_1").append(data);
						},
						error: function(xhr, textStatus, error) {
							alert(textStatus);
						}
					});

					// end of sub category
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;

		} else {
			$('#woreda_id_1').empty().append('<option value="">[ Select Woreda ]</option>');
			$('#city_id_1').empty().append('<option value="">[ Select City or Leave, if not listed ]</option>');
		}
	});

	////// END For Emergency Contact  Information ///////////

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>
