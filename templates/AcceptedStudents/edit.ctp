<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Edit Accepted Student: ' . (isset($isAdmittedAndHaveDepartment['AcceptedStudent']) ? $isAdmittedAndHaveDepartment['AcceptedStudent']['full_name'] . '  (' .  $isAdmittedAndHaveDepartment['AcceptedStudent']['studentnumber'] . ')' : ''); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div style="margin-top: -30px;"><hr></div>

				<?= $this->Form->create('AcceptedStudent', array('onSubmit' => 'return checkForm(this);')); ?>

				<?php
				if (($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) && !empty($this->request->data['AcceptedStudent'])) { ?>
					<div class="large-12 columns">
						<div class="row">
							<div class="large-4 columns">
								<?= $this->Form->input('id'); ?>
								<?= $this->Form->input('first_name', array('style' => 'width:100%', 'label' => 'First Name: ', 'required', 'readOnly' => ($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR || (isset($isAdmittedAndHaveDepartment['Student']) || $isAdmittedAndHaveDepartment['Student']['graduated']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1)) ? true : false)); ?>
								<?= (($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR || (isset($isAdmittedAndHaveDepartment['Student']) || $isAdmittedAndHaveDepartment['Student']['graduated']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1)) ? $this->Form->hidden('first_name') : ''); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('middle_name', array('style' => 'width:100%', 'label' => 'Middle Name: ', 'required', 'readOnly' => ($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR || (isset($isAdmittedAndHaveDepartment['Student']) || $isAdmittedAndHaveDepartment['Student']['graduated']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1)) ? true : false)); ?>
								<?= (($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR || (isset($isAdmittedAndHaveDepartment['Student']) || $isAdmittedAndHaveDepartment['Student']['graduated']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1)) ? $this->Form->hidden('middle_name') : ''); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('last_name', array('style' => 'width:100%', 'label' => 'Last Name: ', 'required', 'readOnly' => ($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR || (isset($isAdmittedAndHaveDepartment['Student']) || $isAdmittedAndHaveDepartment['Student']['graduated']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1)) ? true : false)); ?>
								<?= (($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR || (isset($isAdmittedAndHaveDepartment['Student']) || $isAdmittedAndHaveDepartment['Student']['graduated']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1)) ? $this->Form->hidden('last_name') : ''); ?>
							</div>
						</div>
					</div>
					<div class="large-12 columns">
						<div class="row">
							<div class="large-4 columns">
								<?php
								$options = array('male' => ' Male', 'female' => ' Female'); 
								echo '<h6 class="fs13 text-gray">Sex: </h6> ' . $this->Form->input('sex', array('options' => $options, 'type' => 'radio', 'legend' => false, 'separator' => ' &nbsp; ', 'label' => false));
								?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('EHEECE_total_results', array('style' => 'width:100%', 'label' => 'EHEECE Result: ', 'required', 'readOnly' => ($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR) ? true : false)); ?>
								<?php //echo ($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR ? $this->Form->hidden('EHEECE_total_results') : '') ; ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('moeadmissionnumber', array('style' => 'width:100%', 'label' => 'MoE Admission Number: ', 'readOnly' => ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) ? false : true)); ?>
							</div>
						</div>
					</div>
					<div class="large-12 columns">
						<div class="row">
							<?php
							if (isset($isAdmittedAndHaveDepartment['Student']) && !empty($isAdmittedAndHaveDepartment['Student'])) { ?>
								<div class="large-4 columns">
									<?= $this->Form->input('studentnumber', array('style' => 'width:100%', 'label' => 'Student ID: ', 'required', 'readOnly' => true )); ?>
									<?php // echo $this->Form->hidden('studentnumber'); ?>
								</div>
								<?php
							} else { ?>
								<div class="large-4 columns">
									<?= $this->Form->input('studentnumber', array('style' => 'width:100%', 'label' => 'Student ID: ', 'required', 'readOnly' => ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) ? false : true )); ?>
									<?php //echo ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : $this->Form->hidden('studentnumber')); ?> 
								</div>
								<?php
							} ?>
							<div class="large-8 columns">
								<?= $this->Form->input('high_school', array('style' => 'width:100%', 'label' => 'High School Attended: ', 'readOnly' => ($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR) ? true : false)); ?>
								<?php // echo ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? '' : $this->Form->hidden('high_school')); ?> 
							</div>
						</div>
					</div>
					<div class="large-12 columns">
						<hr>
						<div class="row">
							<?php
							if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) { ?>
								<div class="large-3 columns">
									<?= $this->Form->input('academicyear', array('style' => 'width:100%', 'id' => 'academicyear', 'label' => 'Admission Year: ', 'type' => 'select', 'options' => $acyear_array_data, ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : 'disabled'), 'empty' => '[ Select Admission Year ]', 'default' => isset($currentacyeardata) ? $currentacyeardata : '')); ?>
									<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1 ? $this->Form->hidden('academicyear') : ''); ?>
								</div>
								<?php
							} else { ?>
								<div class="large-3 columns">
									<?= $this->Form->input('academicyear', array('style' => 'width:100%', 'id' => 'academicyear', 'label' => 'Admission Year: ', 'type' => 'select', 'options' => $acyear_array_data, 'disabled', 'empty' => '[ Select Admission Year ]', 'default' => isset($currentacyeardata) ? $currentacyeardata : '')); ?>
									<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1 ? $this->Form->hidden('academicyear') : ''); ?>
								</div>
								<?php
							}
							if (isset($isAdmittedAndHaveDepartment['Student']) && !empty($isAdmittedAndHaveDepartment['Student'])) { ?>
								<div class="large-3 columns">
									<?= $this->Form->input('program_id', array('style' => 'width:100%', 'label' => 'Program: ', 'disabled')); ?>
									<?= $this->Form->hidden('program_id'); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('program_type_id', array('style' => 'width:100%', 'label' => 'Program Type: ', 'disabled')); ?>
									<?= $this->Form->hidden('program_type_id'); ?>
								</div>
								<?php
							} else { ?>
								<div class="large-3 columns">
									<?= $this->Form->input('program_id', array('style' => 'width:100%', 'label' => 'Program: ', ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : 'disabled'))); ?>
									<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR  && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : $this->Form->hidden('program_id')); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('program_type_id', array('style' => 'width:100%', 'label' => 'Program Type: ', ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : 'disabled'))); ?>
									<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR  && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : $this->Form->hidden('program_type_id')); ?>
								</div>
								<?php
							}  ?>
							<div class="large-3 columns">
								<?= $this->Form->input('placement_type_id', array('style' => 'width:100%', 'label' => 'Placement Type: ', 'empty' => '[ Select Placement Type ]', ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : 'disabled'))); ?>
								<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR  && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : $this->Form->hidden('placement_type_id')); ?>
							</div>
						</div>
					</div>
					<div class="large-12 columns">
						<?php
						if (isset($isAdmittedAndHaveDepartment['Student']) && !empty($isAdmittedAndHaveDepartment['Student']) && !empty($isAdmittedAndHaveDepartment['Student']['department_id'])) {
							if (isset($isAdmittedAndHaveDepartment['Student']['CourseRegistration']) && !empty($isAdmittedAndHaveDepartment['Student']['CourseRegistration'])) { ?>
								<div class="row">
									<div class="large-4 columns">
										<?= $this->Form->input('campus_id', array('style' => 'width:100%', 'label' => 'Campus: ', 'disabled', 'empty' => '[ Select Campus ]')); ?>
										<?= $this->Form->hidden('campus_id'); ?>
									</div>
									<div class="large-4 columns">
										<?= $this->Form->input('college_id', array('style' => 'width:100%', 'label' => 'College: ', 'id' => 'CollegeID', 'disabled')); ?>
										<?= $this->Form->hidden('college_id'); ?>
									</div>
									<div class="large-4 columns">
										<?= $this->Form->input('department_id', array('style' => 'width:100%', 'label' => 'Department: ', 'default' => isset($selected_department) ? $selected_department : '', 'empty' => ' College Freshman ', 'id' => 'DepartmentID', 'disabled')); ?>
										<?= $this->Form->hidden('department_id'); ?>
									</div>
								</div>
								<?php
							} else { ?>
								<div class="row">
									<div class="large-4 columns">
										<?= $this->Form->input('campus_id', array('style' => 'width:100%', 'label' => 'Campus: ', 'disabled', 'empty' => '[ Select Campus ]')); ?>
										<?= $this->Form->hidden('campus_id'); ?>
									</div>
									<div class="large-4 columns">
										<?= $this->Form->input('college_id', array('style' => 'width:100%', 'label' => 'College: ', 'id' => 'CollegeID', 'disabled')); ?>
										<?= $this->Form->hidden('college_id'); ?>
									</div>
									<div class="large-4 columns">
										<?= $this->Form->input('department_id', array('style' => 'width:100%', 'label' => 'Department: ', 'default' => isset($selected_department) ? $selected_department : '', 'empty' => ' College Freshman ', 'id' => 'DepartmentID', 'disabled')); ?>
										<?= $this->Form->hidden('department_id'); ?>
									</div>
									
								</div>
								<?php
							}
						} else { ?>
							<div class="row">
								<div class="large-4 columns">
									<?= $this->Form->input('campus_id', array('style' => 'width:100%', 'label' => 'Campus: ', 'disabled', 'empty' => '[ Select Campus ]')); ?>
									<?= $this->Form->hidden('campus_id'); ?>
								</div>
								<div class="large-4 columns">
									<?= $this->Form->input('college_id', array('style' => 'width:100%', 'label' => 'College: ', 'id' => 'CollegeID', ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : 'disabled'))); ?>
									<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR  && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : $this->Form->hidden('college_id')); ?>
								</div>
								<div class="large-4 columns">
									<?= $this->Form->input('department_id', array('style' => 'width:100%', 'label' => 'Department: ', 'default' => isset($selected_department) ? $selected_department : '', 'empty' => ' College Freshman ', 'id' => 'DepartmentID', ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : 'disabled'))); ?>
									<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR  && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : $this->Form->hidden('department_id')); ?>
								</div>
							</div>
							<?php
						} ?>
					</div>
					<div class="large-12 columns">
						<div class="row">
							<div class="large-12 columns"><hr></div>
							<div class="large-4 columns">
								<?= $this->Form->input('country_id', array('id' => 'country_id_1', 'label' => 'Country: ', 'required', 'options' => $countries, 'style' => 'width:100%', /* 'empty' => '[ Select Country ]', */ ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? '' : 'disabled'))); ?>
								<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? '' : $this->Form->hidden('country_id')); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('region_id', array('id' => 'region_id_1', 'label' => 'Region: ', 'required', 'options' => $regions, 'style' => 'width:100%', 'empty' => '[ Select Region ]', ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? '' : 'disabled'))); ?>
								<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? '' : $this->Form->hidden('region_id')); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('zone_id', array('id' => 'zone_id_1', 'label' => 'Zone: ', 'required', 'options' => $zones, 'style' => 'width:100%', 'empty' => '[ Select Zone ]', ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? '' : 'disabled'))); ?>
								<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? '' : $this->Form->hidden('zone_id')); ?>
							</div>
						</div>
					</div>

					<div class="large-12 columns">
						<div class="row">
							<div class="large-4 columns">
								<?= $this->Form->input('woreda_id', array('id' => 'woreda_id_1', 'label' => 'Woreda: ', 'required',  'options' => $woredas, 'style' => 'width:100%', 'empty' => '[ Select Woreda ]', ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? '' : 'disabled'))); ?>
								<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? '' : $this->Form->hidden('country_id')); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('city_id', array('id' => 'city_id_1', 'label' => 'City: ', 'options' => $cities, 'default' => (!empty($studentDetail['Student']['city_id']) ? $studentDetail['Student']['city_id'] : ''), 'style' => 'width:100%', 'empty' => '[ Select City ]', ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? '' : 'disabled'))); ?>
								<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? '' : $this->Form->hidden('Student.city_id')); ?>
							</div>
							<div class="large-4 columns">
								&nbsp;
							</div>
						</div>
					</div>

					<div class="large-12 columns">
						<div class="row">
							<div class="large-12 columns"><hr></div>
							<div class="large-4 columns">
								<?= $this->Form->input('benefit_group', array('label' => 'Benefit Group: ', 'options' => Configure::read('benefit_groups'), 'style' => 'width:100%', 'default' => 'Normal', 'empty' => '[ Select Benefit Group ]', ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? '' : 'disabled'))); ?>
								<?= ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? '' : $this->Form->hidden('benefit_group')); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('disability_id', array('label' => 'Disability: ', 'style' => 'width:100%', 'empty' => '[ Select Disability (If Applicable) ]')); ?>
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('foreign_program_id', array('label' => 'Foreign Program: ', 'style' => 'width:100%', 'empty' => '[ Select Foreign Program (If Applicable) ]')); ?>
							</div>
						</div>
					</div>
					<div class="large-12 columns">
						<div class="row">
							<hr>
							<?= $this->Form->Submit('Save Changes', array('id' => 'saveIt', 'name' => 'updateAcceptedStudentDetail', 'class' => 'tiny radius button bg-blue')); ?>
							<?= $this->Form->end(); ?>
						</div>
					</div>
					<?php
				} ?>
			</div>
		</div>
	</div>
</div>

<script type='text/javascript'>
	$(document).ready(function() {
		$("#CollegeID").change(function() {
			$("#DepartmentID").attr('disabled', true);
			$("#CollegeID").attr('disabled', true);
			var cid = $("#CollegeID").val();
			//get form action
			var formUrl = '/departments/get_department_combo/' + cid;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: cid,
				success: function(data, textStatus, xhr) {
					$("#DepartmentID").attr('disabled', false);
					$("#CollegeID").attr('disabled', false);
					$("#DepartmentID").empty();
					//$("#DepartmentID").append('<option>No department</option>');
					$("#DepartmentID").append(data);

				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;
		});
	});

	var form_being_submitted = false;

	var checkForm = function(form) {
		
		/* if (form.email.value != '' && !isValidEmail(form.email.value)) { 
			form.email.focus();
			return false;
		}


		if (form.etPhone.value != '' && form.etPhone.value.length != 13) { 
			form.etPhone.focus();
			return false;
		} */


		if (form_being_submitted) {
			alert("Updating Student Profile, please wait a moment...");
			form.saveIt.disabled = true;
			return false;
		}

		form.saveIt.value = 'Updating Student Profile...';
		form_being_submitted = true;
		return true; 
	};


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
					$('#city_id_1').empty().append('<option value="">[ Select City ]</option>');
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
			$('#city_id_1').empty().append('<option value="">[ Select City ]</option>');
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
					$('#city_id_1').empty().append('<option value="">[ Select City ]</option>');
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;
			
		} else {
			$('#zone_id_1').empty().append('<option value="">[ Select Zone ]</option>');
			$('#woreda_id_1').empty().append('<option value="">[ Select Woreda ]</option>');
			$('#city_id_1').empty().append('<option value="">[ Select City ]</option>');
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
			$('#city_id_1').empty().append('<option value="">[ Select City ]</option>');
		}
	});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>