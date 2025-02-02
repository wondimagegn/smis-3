
<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class=" icon-print" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Print Student Temporary ID Card'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div style="margin-top: -30px;">
					<hr>
					<?= $this->Form->create('Student'); ?>

					<div onclick="toggleViewFullId('LisAdmittedStudent')">
						<?php
						if (!empty($acceptedStudents)) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg'));
							?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="LisAdmittedStudentTxt"> &nbsp;Display Filter</span><?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'LisAdmittedStudentImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="LisAdmittedStudentTxt"> &nbsp;Hide Filter</span>
							<?php
						} ?>
					</div>

					<div id="LisAdmittedStudent" style="display:<?= ((!empty($acceptedStudents)) ? 'none' : 'display'); ?>">
						<fieldset style="padding-bottom: 0px;padding-top: 15px;">
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('Search.academicyear', array('id' => 'academicyear', 'label' => 'Admission Year: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $acyear_array_data, 'empty' => '[ Select Admission Year ]', 'default' => (isset($defaultacademicyear) ? $defaultacademicyear : ''))) ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Search.program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => 'Program: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programs, 'default' => (isset($program_id) ? $program_id : false))); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Search.program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => 'Program Type: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $program_types, 'default' => (isset($program_type_id) ? $program_type_id : false))); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Search.name', array('id' => 'name', 'style' => 'width:90%;',  'class' => 'fs14', 'label' => 'Name/Student ID:')); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-6 columns">
									<?= $this->Form->input('Search.department_id', array('id' => 'departmentId', 'style' => 'width:90%;',  'class' => 'fs14', 'label' => 'Department: ', 'type' => 'select', 'options' => $departments)); ?>
								</div>
								<div class="large-3 columns">
								<?= $this->Form->input('Search.students_per_page', array('id' => 'studentsPerPage ', 'type' => 'select', 'options' => array(6 => '6 students per A4 Page', 8 => '8 students per A4 Page'), 'label' => 'Students Per Page: ', 'style' => 'width:90%;')); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Search.limit', array('id' => 'limit ', 'type' => 'number', 'min' => '1',  'max' => '5000', 'value' => (isset($this->data['Search']['limit']) ? $this->data['Search']['limit'] : $limit), 'step' => '1',  'label' => 'Limit: ', 'style' => 'width:90%;')); ?>
								</div>
							</div>
							<hr>
							<?= $this->Form->submit(__('Get Students', true), array('name' => 'getacceptedstudent', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
						</fieldset>
					</div>
					<hr>
				</div>

				<?php
				if (!empty($acceptedStudents)) { ?>
					
					<br>
					<h6 class="fs13 text-gray">Select List of student you want to print ID Card.</h6>
					<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
					<br>

					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<th class="center">#</th>
									<th class="center"><?= $this->Form->checkbox("SelectAll", array('id' => 'select-all', 'checked' => '')); ?></th>
									<th class="vcenter">Full Name</th>
									<th class="center">Sex</th>
									<th class="center">Student ID</th>
									<th class="center">College/Department</th>
									<th class="center">Admission Year</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$serial_number = 1;
								foreach ($acceptedStudents as $acceptedStudent) { ?>
									<tr>
										<td class="center"><?= $serial_number++; ?></td>
										<td class="center"><?= $this->Form->checkbox('AcceptedStudent.approve.' . $acceptedStudent['AcceptedStudent']['id'], array('class' => 'checkbox1')); ?></td>
										<td class="vcenter"><?= $acceptedStudent['AcceptedStudent']['full_name']; ?></td>
										<td class="center"><?= (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'male') == 0 ? 'M' : (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'female') == 0 ? 'F' : $acceptedStudent['AcceptedStudent']['sex'])); ?></td>
										<td class="center"><?= $acceptedStudent['AcceptedStudent']['studentnumber']; ?></td>
										<td class="center"><?= (isset($acceptedStudent['Department']['name']) ? $acceptedStudent['Department']['name'] : $acceptedStudent['College']['name']); ?></td>
										<td class="center"><?= $acceptedStudent['AcceptedStudent']['academicyear']; ?></td>
									</tr>
									<?php
								} ?>
							</tbody>
						</table>
					</div>
					<hr>

					<?= $this->Form->Submit('Print ID Card For Selected Students', array('div' => false, 'name' => 'printIDCard', 'id' => 'printIDCard', 'class' => 'tiny radius button bg-blue')); ?>

					<?php
				} ?>

				<?= $this->Form->end(); ?>

			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	$(document).ready(function () {
		
		$("#departmentId").change(function () {
			$('input[type="checkbox"]').each(function(){
				$(this).prop('checked', false);
			});
		});

		$("#Program").change(function () {
			$('input[type="checkbox"]').each(function(){
				$(this).prop('checked', false);
			});
		});

		$("#ProgramType").change(function () {
			$('input[type="checkbox"]').each(function(){
				$(this).prop('checked', false);
			});
		});

		$('input[type="checkbox"]').each(function(){
			$(this).prop('checked', false);
		});

	});

	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none') {
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		} else {
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		}
		$('#c' + obj.id).toggle("slow");
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

	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	$(document).ready(function() {
        $('#printIDCard').click(function() {
			var checkboxes = document.querySelectorAll('input[type="checkbox"]');
			var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

			if (!checkedOne) {
				alert('At least one student must be selected to print temporary ID card.');
				validationMessageNonSelected.innerHTML = 'At least one student must be selected to print temporary ID card.';
				return false;
			}

        });
    });

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}

</script>