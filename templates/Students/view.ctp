<?= $this->Html->script('amharictyping'); ?>
<?php
if (isset($studentDetail) && !empty($studentDetail['Student'])) { ?>
	<div class="box">
		<div class="box-header bg-transparent">
			<div class="box-title" style="margin-top: 10px;"><i class="fontello-vcard" style="font-size: larger; font-weight: bold;"></i>
				<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Student Basic Profile: ' . $studentDetail['Student']['full_name'] . '  (' .  $studentDetail['Student']['studentnumber'] . ')'; ?></span>
			</div>
		</div>
		<div class="box-body pad-forty">
			<div class="row">
				<div class="large-12 columns">
					<div style="margin-top: -50px;"><hr><br></div>
					
					<?php $this->assign('title_details', (!empty($this->request->params['controller']) ? ' ' . Inflector::humanize(Inflector::underscore($this->request->params['controller'])) . (!empty($this->request->params['action']) && $this->request->params['action'] != 'index' ? ' | ' . ucwords(str_replace('_', ' ', $this->request->params['action'])) : '') : '') . (isset($studentDetail['Student']['id']) ? ' - '. $studentDetail['Student']['full_name'] . ' ('. $studentDetail['Student']['studentnumber'] .')' : '')); ?>
					
					<?php
					if (!empty($studentDetail['Attachment'][0]['basename'])) { ?>
						<?= $this->Form->create('Student', array(/* 'data-abide',  */ 'onSubmit' => 'return checkForm(this);', 'novalidate' => true)); ?>
						<?php
					} else { ?>
						<?= $this->Form->create('Student', array(/* 'data-abide', */ 'onSubmit' => 'return checkForm(this);', 'type' => 'file', 'novalidate' => true)); ?>
						<?php
					} ?>

					<ul class="tabs" data-tab>
						<li class="tab-title active"><a href="#basic_data">Basic Student Information</a></li>
						<li class="tab-title"><a href="#add_address">Address & Contact</a></li>
						<li class="tab-title"><a href="#education_background">Educational Background</a></li>
					</ul>

					<div class="tabs-content edumix-tab-horz">
						<div class="content active" id="basic_data">
							<div class="row">
								<div class="large-12 columns">
									<?php
									echo $this->Form->hidden('id', array('value' => $studentDetail['Student']['id']));
									//echo $this->Form->hidden('program_id', array('value' => $studentDetail['Student']['program_id']));
									//echo $this->Form->hidden('program_type_id', array('value' => $studentDetail['Student']['program_type_id']));

									if (isset($studentDetail['Contact'][0]['id'])) {
										echo $this->Form->hidden('Contact.0.id', array('value' => $studentDetail['Contact'][0]['id']));
									}
									
									echo $this->Form->hidden('Contact.0.student_id', array('value' => $studentDetail['Student']['id']));

									$errors = $this->Form->validationErrors;
									
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
														<label> First Name (Amharic):
															<?php
															if (empty($studentDetail['Student']['amharic_first_name'])) { ?>
																<?= $this->Form->input('amharic_first_name', array('label' => false, array('id' => 'AmharicText', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);", 'disabled' => 'disabled'))); ?>
																<?php
															} else { ?>
																<?= $this->Form->input('amharic_first_name', array('label' => false, array('readOnly' => true, 'id' => 'AmharicText', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);", 'disabled' => 'disabled'))); ?>
																<?= $this->Form->hidden('amharic_first_name', array('value' => (!empty($this->data['Student']['amharic_first_name']) ? $this->data['Student']['amharic_first_name'] : $studentDetail['Student']['amharic_first_name']))); ?>
																<?php
															} ?>
														</label>
													</div>
													<div class="large-12 columns">
														<label> Middle Name (Amharic):
															<?php
															if (empty($studentDetail['Student']['amharic_middle_name'])) { ?>
																<?= $this->Form->input('amharic_middle_name', array('label' => false, array('id' => 'AmharicTextMiddleName', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);", 'disabled' => 'disabled'))); ?>
																<?php
															} else { ?>
																<?= $this->Form->input('amharic_middle_name', array('label' => false, array('readOnly' => true, 'id' => 'AmharicTextMiddleName', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);", 'disabled' => 'disabled'))); ?>
																<?= $this->Form->hidden('amharic_middle_name', array('value' => (!empty($this->data['Student']['amharic_middle_name']) ? $this->data['Student']['amharic_middle_name'] : $studentDetail['Student']['amharic_middle_name']))); ?>
																<?php
															} ?>
														</label>
													</div>
													<div class="large-12 columns">
														<label> Last Name (Amharic):
															<?php
															if (empty($studentDetail['Student']['amharic_last_name'])) { ?>
																<?= $this->Form->input('amharic_last_name', array('label' => false, array('id' => 'AmharicTextLastName', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);", 'disabled' => 'disabled'))); ?>
																<?php
															} else { ?>
																<?= $this->Form->input('amharic_last_name', array('label' => false, array('readOnly' => true, 'id' => 'AmharicTextLastName', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);", 'disabled' => 'disabled'))); ?>
																<?= $this->Form->hidden('amharic_last_name', array('value' => (!empty($this->data['Student']['amharic_last_name']) ? $this->data['Student']['amharic_last_name'] : $studentDetail['Student']['amharic_last_name']))); ?>
																<?php
															} ?>
														</label>
													</div>

													<div class="large-12 columns">
														<label> Estimated Graduation Date:
															<?= $this->Form->input('estimated_grad_date', array('minYear' => date('Y'), 'maxYear' => date('Y') + Configure::read('Calendar.expectedGraduationInFuture'), 'orderYear' => 'desc', 'label' => false, 'style' => 'width: 25%;', 'disabled' => 'disabled')); ?>
														</label>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('gender', array('label' => 'Sex: ', 'type' => 'select', 'style' => 'width:30%;', 'disabled' => 'disabled',  'options' => array('female' => 'Female', 'male' => 'Male'))); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('lanaguage', array('label' => 'Primary Lanaguage: ', 'disabled' => 'disabled')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('email', array('type' => 'email', 'id' => 'email', 'required', 'label' => 'Email: ', 'disabled' => 'disabled')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('email_alternative', array('type' => 'email', 'id' => 'alternativeEmail', 'label' => 'Alternative Email: ', 'disabled' => 'disabled')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('phone_home', array('type' => 'tel', 'id'=>'phoneoffice', 'label' => 'Phone (Home): ', 'disabled' => 'disabled')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('phone_mobile', array('type' => 'tel', 'id'=>'etPhone', 'required', 'label' => 'Phone (Mobile): ', 'disabled' => 'disabled')); ?>
													</div>
													<div class="large-12 columns">
														<?= $this->Form->input('birthdate', array('label' => 'Birth Date:', 'minYear' => date('Y') - Configure::read('Calendar.birthdayInPast'), 'maxYear' => (date('Y') - 17), 'orderYear' => 'desc', 'style' => 'width: 25%;', 'disabled' => 'disabled')); ?>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
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
												<?php
											} ?>

											<tr><td colspan=2><strong>Access Information</strong></td></tr>
											<tr><td style="padding-left:30px;"> Username: <?= (!empty($studentDetail['User']['username']) ?  $studentDetail['User']['username'] : ''); ?></td></tr>
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
											<tr><td style="padding-left:30px;"> Program: <?= $programs[$studentDetail['Student']['program_id']]; ?></td></tr>
											<tr><td style="padding-left:30px;"> Program Type: <?= $programTypes[$studentDetail['Student']['program_type_id']]; ?></td></tr>
											<tr><td style="padding-left:30px;"> <?= (isset($studentDetail['College']['type']) && !empty($studentDetail['College']['type']) ? $studentDetail['College']['type'] : 'College') ?>: <?= $colleges[$studentDetail['Student']['college_id']]; ?></td></tr>
											<tr><td style="padding-left:30px;"> <?= (isset($studentDetail['Department']['type']) && !empty($studentDetail['Department']['type']) ? $studentDetail['Department']['type'] : 'Department') ?>: <?= (!empty($studentDetail['Student']['department_id']) ? $departments[$studentDetail['Student']['department_id']] : $stream ); ?></td></tr>
											<tr><td style="padding-left:30px;"> Admission Year: <?= (isset($studentDetail['Student']['academicyear']) ? $studentDetail['Student']['academicyear'] : '---'); ?></td></tr>
											<tr><td style="padding-left:30px;"> Admission Date: <?= $this->Time->format("M j, Y", $studentDetail['Student']['admissionyear'], NULL, NULL); ?></td></tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="content" id="add_address">
							<div class="row">
								<div class="large-6 columns">
									<table cellpadding="0" cellspacing="0" class="table">
										<tbody>
											<tr><td colspan=2><strong>Address & Contact </strong></td></tr>
											<tr><td><?= $this->Form->input('country_id', array('id' => 'country_id_2', 'onchange' => 'updateRegionCity(2)', 'label' => 'Country: ', /* 'error' => false, */ 'empty' => '[ Select ]',  'style' => 'width:60%;', 'default' => COUNTRY_ID_OF_ETHIOPIA, 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('region_id', array('id' => 'region_id_2', 'onchange' => 'updateCity(2)', 'label' => 'Region/City: ',  'error' => false, 'empty' => '[ Select ]',  'style' => 'width:60%;', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('city_id', array('label' => 'City: ', 'id' => 'city_id_2', 'style' => 'width:60%;', 'empty' => '[ Select City ]', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('zone_subcity', array('label' => 'Zone/Subcity: ', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('woreda', array('label' => 'Woreda: ', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('kebele', array('label' => 'Kebele: ', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('house_number', array('label' => 'House Number: ', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('address1', array('label' => 'Address: ', 'disabled' => 'disabled')); ?></td></tr>
										</tbody>
									</table>
									<br>
								</div>
							
								<div class="large-6 columns">
									<table cellpadding="0" cellspacing="0" class="table">
										<tbody>
											<tr><td colspan=2><strong>Primary Emergency Contact</strong></td></tr>
											<tr><td><?= $this->Form->input('Contact.0.first_name', array('label' => 'First Name: ', 'type' => 'text', 'required', 'pattern' => '[a-zA-Z]+', 'div' => true, 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('Contact.0.middle_name', array('label' => 'Middle Name: ', 'type' => 'text', 'required', 'pattern' => '[a-zA-Z]+', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('Contact.0.last_name', array('label' => 'Last Name: ', 'type' => 'text', 'required', 'pattern' => '[a-zA-Z]+', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('Contact.0.country_id', array('label' => 'Country: ', 'id' => 'country_id_1', 'default' => COUNTRY_ID_OF_ETHIOPIA, 'style' => 'width:60%;', 'onchange' => 'updateRegionCity(1)', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('Contact.0.region_id', array('label' => 'Region: ', 'id' => 'region_id_1', 'onchange' => 'updateCity(1)', 'style' => 'width:60%;', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('Contact.0.city_id', array('label' => 'City: ', 'id' => 'city_id_1', 'style' => 'width:60%;', 'empty' => '[ Select City ]', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('Contact.0.email', array('type' => 'email', 'label' => 'Email: ', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('Contact.0.alternative_email', array('type' => 'email', 'label' => 'Alternative Email: ', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('Contact.0.phone_home', array('type' => 'tel', 'id' => 'intPhone1', 'label' => 'Phone (Home): ', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('Contact.0.phone_office', array('type' => 'tel', 'id' => 'intPhone2', 'label' => 'Phone (Office): ', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('Contact.0.phone_mobile', array('type' => 'tel', 'id' => 'phonemobile', 'label' => 'Phone (Mobile): ', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('Contact.0.address1', array('label' => 'Address: ', 'disabled' => 'disabled')); ?></td></tr>
											<tr><td><?= $this->Form->input('Contact.0.primary_contact', array('label' => 'Primary Contact?', 'checked' => 'checked', 'disabled' => 'disabled')); ?></td></tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="content" id="education_background">
							<?php
							$fields = array(
								'school_level' => '1',
								'name' => '2',
								'national_exam_taken' => 3,
								'town' => 4,
								'zone' => 5,
								'region_id' => 6
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
													<th style="width: 3%;">#</th>
													<th style="width: 14%;">School Level</th>
													<th style="width: 20%;">Name</th>
													<th style="width: 18%;">National Exam Taken</th>
													<th style="width: 15%;">Town</th>
													<th style="width: 15%;">Zone</th>
													<th style="width: 15%;">Region</th>
												</tr>
											</thead>
											<tbody>
												<?php
												if (!empty($this->data['HighSchoolEducationBackground'])) {
													$count = 1;
													foreach ($this->data['HighSchoolEducationBackground'] as $bk => $bv) {
														if (!empty($bv['id'])) {
															echo $this->Form->hidden('HighSchoolEducationBackground.' . $bk . '.id');
														} ?>
														<tr>
															<td><?= $count; ?></td>
															<td><?= $this->Form->input('HighSchoolEducationBackground.' . $bk . '.school_level', array('label' => false, 'disabled' => 'disabled')); ?></td>
															<td><?= $this->Form->input('HighSchoolEducationBackground.' . $bk . '.name', array('label' => false, 'disabled' => 'disabled')); ?></td>
															<td><?= $this->Form->input('HighSchoolEducationBackground.' . $bk . '.national_exam_taken', array('label' => false, 'disabled' => 'disabled')); ?></td>
															<td><?= $this->Form->input('HighSchoolEducationBackground.' . $bk . '.town', array('label' => false, 'disabled' => 'disabled')); ?></td>
															<td><?= $this->Form->input('HighSchoolEducationBackground.' . $bk . '.zone', array('label' => false, 'disabled' => 'disabled')); ?></td>
															<td><?= $this->Form->input('HighSchoolEducationBackground.' . $bk . '.region_id', array('options' => $regions, 'type' => 'select', 'empty' => '[ Select ]', 'label' => false, 'style' => 'width:90%;', 'disabled' => 'disabled')); ?></td>
														</tr>
														<?php
														$count++;
													}
												} ?>
											</tbody>
										</table>
									</div>
									<br>
								</div>
							</div>

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
													<th style="width: 3%;">#</th>
													<th style="width: 18%;">Institution/College</th>
													<th style="width: 15%;">Field of study</th>
													<th style="width: 15%;">Diploam Awared</th>
													<th style="width: 26%;">Date Graduated </th>
													<th style="width: 8%;">CGPA</th>
													<th style="width: 15%;">City</th>
												</tr>
											</thead>
											<tbody>
												<?php
												if (!empty($this->data['HigherEducationBackground'])) {
													$count = 1;
													foreach ($this->data['HigherEducationBackground'] as $bk => $bv) {
														echo $this->Form->hidden('HigherEducationBackground.' . $bk . '.id'); ?>
														<tr>
															<td><?= $count; ?></td>
															<td><?= $this->Form->input('HigherEducationBackground.' . $bk . '.name', array('label' => false, 'disabled' => 'disabled')); ?></td>
															<td><?= $this->Form->input('HigherEducationBackground.' . $bk . '.field_of_study', array('label' => false, 'disabled' => 'disabled')); ?></td>
															<td><?= $this->Form->input('HigherEducationBackground.' . $bk . '.diploma_awarded', array('label' => false, 'disabled' => 'disabled')); ?></td>
															<td><?= $this->Form->input('HigherEducationBackground.' . $bk . '.date_graduated', array('label' => false, 'minYear' => date('Y') - 30, 'maxYear' => (date('Y') - 1), /* 'orderYear' => 'desc',  */'style' => 'width: 30%;')); ?></td>
															<td><?= $this->Form->input('HigherEducationBackground.' . $bk . '.cgpa_at_graduation', array('label' => false, 'disabled' => 'disabled', 'size' => 5, 'min' => '2.0', 'max' => '4.0', 'step' => 'any')); ?></td>
															<td><?= $this->Form->input('HigherEducationBackground.' . $bk . '.city', array('disabled' => 'disabled', 'label' => false, 'type' => 'text')); ?></td>
														</tr>
														<?php
														$count++;
													}
												} ?>
											</tbody>
										</table>
									</div>
									<br>
								</div>
							</div>

							<?php
							$eheece_fields = array('subject' => '1', 'mark' => '2', 'exam_year' => '3');
							$eheece_all_fields = "";
							$sepeheece = "";

							foreach ($eheece_fields as $key => $tag) {
								$eheece_all_fields .= $sepeheece . $key;
								$sepeheece = ",";
							}

							$eslce_fields = array('subject' => '1', 'grade' => '2', 'exam_year' => '3');
							$eslce_all_fields = "";
							$sepeslce = "";

							foreach ($eslce_fields as $key => $tag) {
								$eslce_all_fields .= $sepeslce . $key;
								$sepeslce = ",";
							}

							$from = date('Y') - 30;
							$to = date('Y') - 1;
							$format = Configure::read('Calendar.yearFormat');
							$yearoptions = array();

							for ($j = $to ; $j >= $from; $j--) {
								$yearoptions[$j] = $j;
							} ?>

							<div class="row">

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
													<th>#</th>
													<th>Subject</th>
													<th>Grade</th>
													<th>Exam Year</th>
												</tr>
											</thead>
											<tbody>
												<?php
												if (!empty($this->data['EslceResult'])) {
													$count = 0;
													foreach ($this->data['EslceResult'] as $bk => $bv) {
														echo $this->Form->hidden('EslceResult.' . $bk . '.id'); ?>
														<tr>
															<td><?= ++$count; ?></td>
															<td><?= $this->Form->input('EslceResult.' . $bk . '.subject', array('name' => "data[EslceResult][$bk][subject]", 'value' => isset($this->data['EslceResult'][$bk]['subject']) ? $this->data['EslceResult'][$bk]['subject'] : '', 'disabled' => 'disabled', 'label' => false)); ?></td>
															<td><?= $this->Form->input('EslceResult.' . $bk . '.grade', array('name' => "data[EslceResult][$bk][grade]", 'value' => isset($this->data['EslceResult'][$bk]['grade']) ? $this->data['EslceResult'][$bk]['grade'] : '', 'size' => 4, 'disabled' => 'disabled', 'label' => false)); ?></td>
															<td><?= $this->Form->input('EslceResult.' . $bk . '.exam_year', array('value' => isset($this->data['EslceResult'][$bk]['exam_year']) ? $this->data['EslceResult'][$bk]['exam_year'] : '', 'disabled' => 'disabled', 'label' => false, 'style' => 'width:70%;', 'type' => 'select', 'options' => $yearoptions, 'selected' => !empty($this->data['EslceResult'][$bk]['exam_year']) ? $this->data['EslceResult'][$bk]['exam_year'] : '')); ?></td>
														</tr>
														<?php
													}
												} ?>
											</tbody>
										</table>
									</div>
									<br>
								</div>

								<div class="large-6 columns">
									<div style="overflow-x:auto;">
										<table cellpadding="0" cellspacing="0" class="table">
											<thead>
												<tr>
													<td colspan="4" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;"><h6 class="fs18 text-black">EHEECE Results(12th Grade)</h6></td>
												</tr>
											</thead>
										</table>
										<table id='eheece_result' cellpadding="0" cellspacing="0" class="table">
											<thead>
												<tr>
													<th style="width: 5%;">#</th>
													<th style="width: 45%;">Subject</th>
													<th style="width: 20%;">Mark</th>
													<th style="width: 30%;">Exam Year</th>
												</tr>
											</thead>
											<tbody>
												<?php
												if (!empty($this->data['EheeceResult'])) {
													$count = 0;
													foreach ($this->data['EheeceResult'] as $bk => $bv) {
														echo $this->Form->hidden('EheeceResult.' . $bk . '.id'); ?>
														<tr>
															<td><?= ++$count; ?></td>
															<td><?= $this->Form->input('EheeceResult.' . $bk . '.subject', array('name' => "data[EheeceResult][$bk][subject]", 'value' => isset($this->data['EheeceResult'][$bk]['subject']) ? $this->data['EheeceResult'][$bk]['subject'] : '', 'disabled' => 'disabled', 'label' => false)); ?></td>
															<td><?= $this->Form->input('EheeceResult.' . $bk . '.mark', array('name' => "data[EheeceResult][$bk][mark]", 'value' => isset($this->data['EheeceResult'][$bk]['mark']) ? $this->data['EheeceResult'][$bk]['mark'] : '', 'disabled' => 'disabled', 'label' => false, 'min' => '0', 'max' => '100', 'step' => 'any')); ?></td>
															<td><?= $this->Form->input('EheeceResult.' . $bk . '.exam_year', array('name' => "data[EheeceResult][$bk][exam_year]", 'value' => (!empty(explode('-', $this->data['EheeceResult'][$bk]['exam_year'])[0]) ? (explode('-', $this->data['EheeceResult'][$bk]['exam_year'])[0]) : ''), 'disabled' => 'disabled', 'label' => false, 'style' => 'width:70%;', 'type' => 'select', 'options' => $yearoptions, 'default' => !empty((explode('-', $this->data['EheeceResult'][$bk]['exam_year'])[0])) ? (explode('-', $this->data['EheeceResult'][$bk]['exam_year'])[0]) : '')); ?></td>
														</tr>
														<?php
													}
												} ?>
											</tbody>
										</table>
									</div>
									<br>
								</div>
								
							</div>

						</div>
					</div>
					
					<?php // '<hr>'. $this->Form->end(array('label' => 'Update Student Detail', /* 'disabled', */ 'name' => 'updateStudentDetail', 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue')); ?>

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

	var form_being_submitted = false;

	var checkForm = function(form) {
		
		if (form.email.value != '' && !isValidEmail(form.email.value)) { 
			form.email.focus();
			return false;
		}

		//alert(isValidPhonenumber(form.Contact0PhoneMobile.value));
		//alert(isValidEmail(form.email.value));
		//alert(isAlpha(form.Contact0FirstName.value));
		//alert(email.test(form.email.value));

		if (form.etPhone.value != '' && form.etPhone.value.length != 13) { 
			form.etPhone.focus();
			return false;
		}

		if (form.Contact0PhoneMobile.value != '' && form.Contact0PhoneMobile.value.length != 13) { 
			form.Contact0PhoneMobile.value.focus();
			return false;
		}

		if (isValidPhonenumber(form.Contact0PhoneMobile.value) == false) {
			form.Contact0PhoneMobile.value.focus();
			return false;
		}

		if (form_being_submitted) {
			alert("Updating Student Profile, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Updating Student Profile...';
		form_being_submitted = true;
		return true; 
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>