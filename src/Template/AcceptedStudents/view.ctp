<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Accepted Student Details: ' . (isset($this->request->data['AcceptedStudent']) ? $this->request->data['AcceptedStudent']['full_name'] . '  (' .  $this->request->data['AcceptedStudent']['studentnumber'] . ')' : ''); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>
				<?= $this->Form->create('AcceptedStudent', array('action' => 'view')); ?>

				<div class="large-12 columns">
					<div class="row">
						<div class="large-4 columns">
							<?= $this->Form->input('id'); ?>
							<?= $this->Form->input('first_name', array('style' => 'width:100%', 'label' => 'First Name: ', 'required', 'readOnly')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('middle_name', array('style' => 'width:100%', 'label' => 'Middle Name: ', 'required', 'readOnly')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('last_name', array('style' => 'width:100%', 'label' => 'Last Name: ', 'required', 'readOnly')); ?>
						</div>
					</div>
				</div>
				<div class="large-12 columns">
					<div class="row">
						<div class="large-4 columns">
							<?php
							$options = array('male' => ' Male', 'female' => ' Female'); 
							echo '<h6 class="fs13 text-gray">Sex: </h6> ' . $this->Form->input('sex', array('options' => $options, 'type' => 'radio', 'disabled', 'div' => false, 'legend' => false, 'separator' => ' &nbsp; ', 'label' => false));
							?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('EHEECE_total_results', array('style' => 'width:100%', 'label' => 'EHEECE Result: ', 'required', 'readOnly')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('moeadmissionnumber', array('style' => 'width:100%', 'label' => 'MoE Admission Number: ', 'readOnly')); ?>
						</div>
					</div>
				</div>
				<div class="large-12 columns">
					<div class="row">
						<div class="large-4 columns">
							<?= $this->Form->input('studentnumber', array('style' => 'width:100%', 'label' => 'Student ID: ', 'required', 'readOnly')); ?>
						</div>
						<div class="large-8 columns">
							<?= $this->Form->input('high_school', array('style' => 'width:100%', 'label' => 'High School Attended: ', 'readOnly')); ?>
						</div>
					</div>
				</div>
				<div class="large-12 columns">
					<hr>
					<div class="row">
						<div class="large-3 columns">
							<?= $this->Form->input('academicyear', array('style' => 'width:100%', 'id' => 'academicyear', 'label' => 'Admission Year: ', 'type' => 'select', 'options' => $acyear_array_data, 'disabled', 'empty' => '[ Select Admission Year ]', 'default' => isset($currentacyeardata) ? $currentacyeardata : '')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('program_id', array('style' => 'width:100%', 'label' => 'Program: ', 'disabled')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('program_type_id', array('style' => 'width:100%', 'label' => 'Program Type: ', 'disabled')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('placement_type_id', array('style' => 'width:100%', 'label' => 'Placement Type: ', 'empty' => '[ Select Placement Type ]', 'disabled')); ?>
						</div>
					</div>
				</div>
				<div class="large-12 columns">
					<div class="row">
						<div class="large-4 columns">
							<?= $this->Form->input('campus_id', array('style' => 'width:100%', 'label' => 'Campus: ', 'disabled', 'empty' => '[ Select Campus ]')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('college_id', array('style' => 'width:100%', 'label' => 'College: ', 'id' => 'CollegeID', 'disabled')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('department_id', array('style' => 'width:100%', 'label' => 'Department: ', 'default' => (isset($selected_department) ? $selected_department : ''), 'empty' => ' College Freshman ', 'id' => 'DepartmentID', 'disabled')); ?>
						</div>
					</div>
				</div>
				<div class="large-12 columns">
					<div class="row">
						<div class="large-12 columns"><hr></div>
						<div class="large-4 columns">
							<?= $this->Form->input('country_id', array('id' => 'country_id_1', 'label' => 'Country: ', 'required', 'options' => $countries, 'style' => 'width:100%', 'empty' => '[ Select Country ]', 'disabled')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('region_id', array('id' => 'region_id_1', 'label' => 'Region: ', 'required', 'options' => $regions, 'style' => 'width:100%', 'empty' => '[ Select Region ]', 'disabled')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('zone_id', array('id' => 'zone_id_1', 'label' => 'Zone: ', 'required', 'options' => $zones, 'style' => 'width:100%', 'empty' => '[ Select Zone ]', 'disabled')); ?>
						</div>
					</div>
				</div>
				<div class="large-12 columns">
					<div class="row">
						<div class="large-4 columns">
							<?= $this->Form->input('woreda_id', array('id' => 'woreda_id_1', 'label' => 'Woreda: ', 'required',  'options' => $woredas, 'style' => 'width:100%', 'empty' => '[ Select Woreda ]', 'disabled')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('city_id', array('id' => 'city_id_1', 'label' => 'City: ', 'options' => $cities, 'default' => (!empty($studentDetail['Student']['city_id']) ? $studentDetail['Student']['city_id'] : ''), 'style' => 'width:100%', 'empty' => '[ Select City ]', 'disabled')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('student_national_id', array('label' => 'Student National ID: ', 'type' => 'text', 'style' => 'width:100%', 'readOnly')); ?>
						</div>
					</div>
				</div>
				<div class="large-12 columns">
					<div class="row">
						<div class="large-12 columns"><hr></div>
						<div class="large-4 columns">
							<?= $this->Form->input('benefit_group', array('label' => 'Benefit Group: ', 'options' => Configure::read('benefit_groups'), 'style' => 'width:100%', 'default' => 'Normal', 'empty' => '[ Select Benefit Group ]', 'disabled')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('disability_id', array('label' => 'Disability: ', 'style' => 'width:100%', 'empty' => '[ Select Disability (If Applicable) ]', 'disabled')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('foreign_program_id', array('label' => 'Foreign Program: ', 'style' => 'width:100%', 'empty' => '[ Select Foreign Program (If Applicable) ]', 'disabled')); ?>
						</div>
					</div>
				</div>
				<br><br>
				<?= $this->Form->end(); ?>
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