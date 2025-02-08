<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-attach-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Attach Students to a Curricullum') ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<?= $this->Form->create('AcceptedStudent', array('action' => 'attach_curriculum')); ?>

				<div style="margin-top: -30px;">
					<?php
					if (!isset($auto_approve)) { ?>
						<hr>
						<blockquote>
							<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
							<span style="text-align:justify;" class="fs16 text-gray">This tool will help you to attach students to approved curriculums in your department. <br> <i class="rejected">Only students which are admitted, not graduated and does'nt have a curriculum attachement appear here for attachent.</i></span>
						</blockquote>
						<hr>

						<div onclick="toggleViewFullId('ListPublishedCourse')">
							<?php
							if (!empty($autoplacedstudents)) {
								echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
								<?php
							} else {
								echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
								<?php
							} ?>
						</div>

						<div id="ListPublishedCourse" style="display:<?= (isset($auto_approve) ? 'none' : 'display'); ?>">
							<fieldset style="padding-bottom: 0px;padding-top: 15px;">
								<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
								<div class="row">
									<div class="large-4 columns">
										<?= $this->Form->input('AcceptedStudent.academicyear', array('label' => 'Admission Year: ', 'style' => 'width:90%', 'options' => $acyear_list, 'default' => isset($selected_academicyear) ? $selected_academicyear : $defaultacademicyear)); ?>
									</div>
									<div class="large-4 columns">
										<?= $this->Form->input('AcceptedStudent.program_id', array('label' => 'Program: ', 'style' => 'width:90%', 'options' => $programs)); ?>
									</div>
									<div class="large-4 columns">
										<?= $this->Form->input('AcceptedStudent.program_type_id', array('label' => 'Program Type: ', 'style' => 'width:90%', 'options' => $programTypes)); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-4 columns">
										<?= $this->Form->input('AcceptedStudent.name', array('label' => 'Name:', 'placeholder' => 'First, Middle or Last name', 'default' => /* $name */ '', 'style' => 'width:90%;')); ?>
									</div>
									<div class="large-4 columns">
										<?= $this->Form->input('AcceptedStudent.limit', array('id' => 'limit ', 'type' => 'number', 'min' => '100',  'max' => '1000', 'value' => $limit, 'step' => '100', 'label' => ' Limit: ', 'style' => 'width:90%;')); ?>
									</div>
								</div>
								<hr>
								<?= $this->Form->Submit(__('Search'), array('div' => false, 'name' => 'searchbutton', 'class' => 'tiny radius button bg-blue')); ?>
							</fieldset>
						</div>
						<hr>
						<?php
					}

					if (!empty($autoplacedstudents)) { ?>
						
						<?php
						echo $this->Form->hidden('AcceptedStudent.academicyear', array('value' => $selected_academicyear));
						if (!isset($turn_of_approve_button)) { ?>
							<hr>
							<blockquote>
								<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
								<span style="text-align:justify;" class="fs16 text-black"><i class="rejected">Only recent curriculums which are active, locked and approved by the registrar appear here for attachent.</i> <br> If you miss any of the curriculums in the options list here but available in "Curriculum > List Curriculums" page, Contact your respective registrar to approve, lock or activate the required curriculum to appear here for attachement.</span>
							</blockquote>
							<hr>

							<fieldset style="padding-bottom: 5px;padding-top: 5px;">
								<div class="large-2 columns">
									&nbsp;
								</div>
								<div class="large-8 columns">
									<?= $this->Form->input('curriculum_id', array('label' => 'Curriculum: <span></span>', 'id' => 'selectedCurriculum', 'empty' => '[ Select Curriculum ]', 'required' => true, 'style' => 'width: 90%;')); ?>
								</div>
								<div class="large-2 columns">
									&nbsp;
								</div>
							</fieldset>
							<?php
						}
						$count = 0;  ?>
						
						<hr>
						<h6 class="fs15 text-gray">List of students admitted on <?= $selected_academicyear; ?> academic year and placed to <?= $department_name; ?> which are not attached to any curriculum.</h6>
						<hr>

						<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
						<br>

						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<td class="center"><?= $this->Form->checkbox("SelectAll", array('id' => 'select-all', 'checked' => '')); ?></td>
										<td class="center">#</td>
										<td class="center">Full Name</td>
										<td class="center">Sex</td>
										<td class="center">Student ID</td>
										<td class="center">EHEECE</td>
										<td class="center">SGPA</td>
										<td class="center">Department</td>
										<td class="center">Admission Year</td>
										<td class="center">Department Approval</td>
										<td class="center">Placement Type</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$serial_number = 1;
									foreach ($autoplacedstudents as $acceptedStudent) { ?>
										<tr>
											<td class="center">
												<?= $this->Form->hidden('AcceptedStudent.' . $count . '.id', array('value' => $acceptedStudent['AcceptedStudent']['id'])); ?>
												<div style="margin-left: 15%;"><?= $this->Form->checkbox('AcceptedStudent.approve.' . $acceptedStudent['AcceptedStudent']['id'], array('class' => 'checkbox1')); ?></div>
											</td>
											<td class="center"><?= $serial_number++; ?></td>
											<td class="vcenter"><?= $acceptedStudent['AcceptedStudent']['full_name']; ?></td>
											<td class="center"><?= (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'male') == 0 ? 'M' : (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'female') == 0 ? 'F' : trim($acceptedStudent['AcceptedStudent']['sex']))); ?></td>
											<td class="center"><?= $acceptedStudent['AcceptedStudent']['studentnumber']; ?></td>
											<td class="center"><?= (isset($acceptedStudent['AcceptedStudent']['EHEECE_total_results']) ? (int) $acceptedStudent['AcceptedStudent']['EHEECE_total_results'] : ''); ?></td>
											<td class="center"><?= (isset($acceptedStudent['AcceptedStudent']['freshman_result']) ? $acceptedStudent['AcceptedStudent']['freshman_result'] : ''); ?></td>
											<td class="center"><?= $acceptedStudent['Department']['name']; ?></td>
											<td class="center"><?= $acceptedStudent['AcceptedStudent']['academicyear']; ?></td>
											<td class="center"><?= (isset($acceptedStudent['AcceptedStudent']['Placement_Approved_By_Department']) && $acceptedStudent['AcceptedStudent']['Placement_Approved_By_Department'] == 1 ? '<span class="accepted">Yes</span>' : '<span class="on-progress">No</span>'); ?></td>
											<td class="center"><?= (!empty($acceptedStudent['AcceptedStudent']['placementtype']) ? ucwords(strtolower($acceptedStudent['AcceptedStudent']['placementtype'])) : ''); ?></td>
										</tr>
										<?php
										$count++;
									} ?>
								</tbody>
							</table>
						</div>
						<hr>
						<?= $this->Form->Submit(__('Attach Selected'), array('div' => false, 'id' => 'attachCurriculum', 'name' => 'attach', 'class' => 'tiny radius button bg-blue')); ?>
						<?php
					} ?>
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none')
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		else
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		$('#c' + obj.id).toggle("slow");
	}

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

	var form_being_submitted = false;

	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	$('#attachCurriculum').click(function() {
		var isValid = true;
		//var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="data[AcceptedStudent][approve]"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

		var selectedCurriculum = $("#selectedCurriculum option:selected").text();

		/* alert(checkedOne);
		alert(selectedCurriculum); */

		if ($("#selectedCurriculum").val() == '') {
			$("#selectedCurriculum").focus();
			isValid = true;
			return false;
		}

		if (!checkedOne) {
			alert('At least one student must be selected attach to ' + selectedCurriculum + ' curriculum.');
			validationMessageNonSelected.innerHTML = 'At least one student must be selected attach to ' + selectedCurriculum + ' curriculum.';
			isValid = false;
			return false;
		}

		if (form_being_submitted) {
			alert('Attaching selected students to ' + selectedCurriculum + ' curriculum. please wait a moment...');
			$('#attachCurriculum').attr('disabled', true);
			isValid = false;
			return false;
		}

		if (!form_being_submitted && isValid) {
			$('#attachCurriculum').val('Attaching Selected Students...');
			form_being_submitted = true;
			isValid = true
			return true;
		} else {
			return false;
		}
	});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>