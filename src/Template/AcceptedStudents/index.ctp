<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Accepted Students'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?php
				if ($this->Session->read('Auth.User')['role_id'] != ROLE_STUDENT) { ?>
					<?= $this->Form->Create('AcceptedStudent', array('action'=> 'search')); ?>
					<div style="margin-top: -30px;">
						<hr>
						<div onclick="toggleViewFullId('ListPublishedCourse')">
							<?php
							if (!empty($turn_off_search)) {
								echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
							<?php
							} else {
								echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
							<?php
							} ?>
						</div>

						<div id="ListPublishedCourse" style="display:<?= (!empty($turn_off_search) ? 'none' : 'display'); ?>">
							<fieldset style="padding-bottom: 0px;padding-top: 5px;">
								<legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
								<div class="row">
									<div class="large-3 columns">
										<?= $this->Form->input('Search.academicyear', array('label' => 'Admission Year: ', 'style' => 'width:90%', 'empty' => 'All Admission Year', 'options' => $acyear_array_data, 'default' => (isset($selected_academic_year) ? $selected_academic_year : ''))); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search.program_id', array('label' => 'Program: ', 'style' => 'width:90%', 'empty' => 'All Programs', 'options' => $programs)); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search.program_type_id', array('label' => 'Program Type: ', 'style' => 'width:90%', 'empty' => 'All Program Types', 'options' => $programTypes)); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search.admitted', array('label' => 'Status: ', 'style' => 'width:90%', 'options' => array('0' => 'All', '1' => 'Not Admitted', '2' => 'Admitted'), 'default' => '2')); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-6 columns">
										<?php
										if (isset($colleges) && !empty($colleges)) {
											echo $this->Form->input('Search.college_id', array('label' => 'College: ', 'style' => 'width:90%', 'empty' => 'All Colleges'));
										} else if (isset($departments) && !empty($departments)) {
											echo $this->Form->input('Search.department_id', array('label' => 'Department: ', 'style' => 'width:90%', 'empty' => 'All Departments'));
										} ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search.name', array('label' => 'Student Name ID: ', 'placeholder' => 'Name or Student ID ..', 'default' => $name, 'style' => 'width:90%;')); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search.limit', array('id' => 'limit ', 'type' => 'number', 'min' => '1',  'max' => '5000', 'value' => (isset($this->data['Search']['limit']) ? $this->data['Search']['limit'] : $limit), 'step' => '1', 'label' => 'Limit: ', 'style' => 'width:40%;')); ?>
										
										<?= (isset($this->data['Search']['page']) ? $this->Form->hidden('page', array('value' => $this->data['Search']['page'])) : ''); ?>
										<?= (isset($this->data['Search']['sort']) ? $this->Form->hidden('sort', array('value' => $this->data['Search']['sort'])) : ''); ?>
										<?= (isset($this->data['Search']['direction']) ? $this->Form->hidden('direction', array('value' => $this->data['Search']['direction'])) : ''); ?>

									</div>
								</div>
								<?php
								if (isset($departments) && !empty($departments) && $this->Session->read('Auth.User')['role_id'] != ROLE_STUDENT && $this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR &&  $this->Session->read('Auth.User')['role_id'] != ROLE_COLLEGE &&  $this->Session->read('Auth.User')['role_id'] != ROLE_DEPARTMENT) { ?>
									<div class="row">
										<div class="large-6 columns">
											<?= $this->Form->input('Search.department_id', array('label' => 'Department: ', 'style' => 'width:90%', 'empty' => 'All Departments')); ?>
										</div>
										<div class="large-6 columns">
										</div>
									</div>
									<?php
								} ?>
								<hr>

								<?= $this->Form->submit('Search', array('name' => 'search', 'id' => 'search', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
							</fieldset>

							<?= $this->Form->end(); ?>

							<?php
							if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) { ?>
								<br> 
								<hr>
								<div style="margin-top: 5px;">
									<blockquote>
										<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
										<span style="text-align:justify;" class="fs16 text-gray">The student list you will get here depends on your <b style="text-decoration: underline;"><i>assigned College or Department, assigned Program and Program Types, and with your search conditions</i></b>. You can contact the registrar to adjust permissions assigned to you if you miss your students here.</span>
									</blockquote>
								</div>
								<?php
							} ?>

						</div>
					</div>
					<hr>
					<?php
				} ?>
			<!-- </div>
		</div>
	</div>
</div>


<div class="box">
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns"> -->
				<?php
				$not_admitted_students_count = 0;
				if (!empty($acceptedStudents)) {
					if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read('Auth.User')['Role']['parent_id'] == ROLE_REGISTRAR) {
						echo $this->Form->create('AcceptedStudent', array('action' => 'delete', 'id' => 'accepted-form', 'onSubmit' => 'return checkForm(this);'));
					} ?>
					
					<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                    <br>

					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<?php
									if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) { ?>
										<td class="center"><?= $this->Form->checkbox(null, array('id' => 'select-all', 'name' => 'select-all', 'checked' => '')); ?></td>
										<?php
									}
									if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) { ?>
										<td class="center">&nbsp;</td>
										<?php
									} ?>
									<td class="center"><?= $this->Paginator->sort('id', '#'); ?></td>
									<td style="width: 25%;" class="vcenter"><?= $this->Paginator->sort('full_name', 'Full Name'); ?></td>
									<td class="center"><?= $this->Paginator->sort("sex", 'Sex'); ?></td>
									<td class="center"><?= $this->Paginator->sort("studentnumber", "Student ID"); ?></td>
									<td class="center"><?= $this->Paginator->sort('EHEECE_total_results', "EHEECE"); ?></td>
									<?php
									if ($this->Session->read('Auth.User')['role_id'] != ROLE_COLLEGE && $this->Session->read('Auth.User')['role_id'] != ROLE_DEPARTMENT) { ?>
										<td class="center"><?= $this->Paginator->sort('college_id', 'College'); ?></td>
										<?php
									}
									if ($this->Session->read('Auth.User')['role_id'] != ROLE_DEPARTMENT) { ?>
										<td class="center"><?= $this->Paginator->sort('department_id', 'Department'); ?></td>
										<?php
									} ?>
									<td class="center"><?= $this->Paginator->sort('program_id', 'Program'); ?></td>
									<td class="center"><?= $this->Paginator->sort('program_type_id', 'Program Type'); ?></td>
									<td class="center"><?= $this->Paginator->sort("Placement_Approved_By_Department", "Department Approval"); ?></td>
									<td class="center"><?= $this->Paginator->sort("placementtype", "Placement Type"); ?></td>
								</tr>
							</thead>
							<tbody>
								<?php
								$start = $this->Paginator->counter('%start%');
								foreach ($acceptedStudents as $acceptedStudent) {
									$class = null;
									$red = null;

									if (isset($student_not_deleted) && in_array($acceptedStudent['AcceptedStudent']['id'], $student_not_deleted)) {
										$class .= 'redrow';
									} ?>

									<tr class="<?= $class; ?>">
										<?php
										if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) { ?>
											<td class="center">
												<?php
												if (isset($acceptedStudent['Student']['id']) && !empty($acceptedStudent['Student']['id']) && $acceptedStudent['Student']['id'] > 0) {
													echo '**';
												} else {
													echo '<div style="margin-left: 15%;">' . $this->Form->checkbox('AcceptedStudent.delete.' . $acceptedStudent['AcceptedStudent']['id'], array('class' => 'checkbox1')) . '</div>';
													$not_admitted_students_count++;
												} ?>
											</td>
											<?php
										}
										if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) { ?>
											<td class="center">
												<?php
												if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
													echo $this->Html->link(__('Update Disability'), array('action' => 'edit', $acceptedStudent['AcceptedStudent']['id']));
												} else {
													echo $this->Html->link(__('Edit'), array('action' => 'edit', $acceptedStudent['AcceptedStudent']['id']));
												} ?>
											</td>
											<?php
										} ?>
										<td class="center"><?= $start++; ?></td>
										<td class="vcenter"><?= $acceptedStudent['AcceptedStudent']['full_name']; ?></td>
										<td class="center"><?= (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'male') == 0 ? 'M' : (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'female') == 0 ? 'F' : '')); ?></td>
										<td class="center"><?= $acceptedStudent['AcceptedStudent']['studentnumber']; ?></td>
										<td class="center"><?= (isset($acceptedStudent['AcceptedStudent']['EHEECE_total_results']) ? (int)$acceptedStudent['AcceptedStudent']['EHEECE_total_results'] : ''); ?></td>
										<?php
										if ($this->Session->read('Auth.User')['role_id'] != ROLE_COLLEGE && $this->Session->read('Auth.User')['role_id'] != ROLE_DEPARTMENT) { ?>
											<td class="center"><?= $acceptedStudent['College']['shortname']; ?></td>
											<?php
										}
										if ($this->Session->read('Auth.User')['role_id'] != ROLE_DEPARTMENT) { ?>
											<td class="center"><?= (isset($acceptedStudent['Department']['name']) ? $acceptedStudent['Department']['name'] : ($acceptedStudent['ProgramType']['id'] == PROGRAM_UNDEGRADUATE ? 'Pre/Freshman' : ($acceptedStudent['AcceptedStudent']['program_id'] == PROGRAM_REMEDIAL || $acceptedStudent['Program']['id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : ''))); ?></td>
											<?php
										} ?>
										<td class="center"><?= (isset($acceptedStudent['Program']['shortname']) ? $acceptedStudent['Program']['shortname'] : $acceptedStudent['Program']['name']); ?></td>
										<td class="center"><?= $acceptedStudent['ProgramType']['name']; ?></td>
										<td class="center"><?= (isset($acceptedStudent['AcceptedStudent']['Placement_Approved_By_Department']) && $acceptedStudent['AcceptedStudent']['Placement_Approved_By_Department'] == 1 ? '<span class="accepted">Yes</span>' : ''); ?></td>
										<td class="center">
											<?php
											if (empty($acceptedStudent['AcceptedStudent']['placementtype']) && !empty($acceptedStudent['AcceptedStudent']['online_applicant_id'])) {
												echo "Online Processed";
											} else {
												echo  ucwords(strtolower($acceptedStudent['AcceptedStudent']['placementtype']));
											} ?>
										</td>
									</tr>
									<?php
								} ?>
							</tbody>
						</table>
					</div>
					<br>

					<?php
					if ($not_admitted_students_count > 0 && ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read('Auth.User')['Role']['parent_id'] == ROLE_REGISTRAR) && $this->Session->read('Auth.User')['is_admin'] == 1) { ?>
						<?= $this->Form->Submit('Delete Selected', array('class' => 'tiny radius button bg-blue', 'id' => 'deleteSelected')); ?>
						<?php
					} ?>

					<hr>
					<div class="row">
						<div class="large-5 columns">
							<?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total'))); ?>
						</div>
						<div class="large-7 columns">
							<div class="pagination-centered">
								<ul class="pagination">
									<?= $this->Paginator->prev('<< ' . __(''), array('tag' => 'li'), null, array('class' => 'arrow unavailable')); ?> <?= $this->Paginator->numbers(array('separator' => '', 'tag' => 'li')); ?> <?= $this->Paginator->next(__('') . ' >>', array('tag' => 'li'), null, array('class' => 'arrow unavailable')); ?>
								</ul>
							</div>
						</div>
					</div>
					<?php
				} else { ?>
					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No Accepted Student is found with the given search criteria</div>
					<?php
				} ?>

				<?= $this->Form->end(); ?>
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

	var checkForm = function(form) {
		var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

		//alert(checkedOne);
		if (!checkedOne) {
			alert('At least one accepted student must be selected to delete!');
			validationMessageNonSelected.innerHTML = 'At least one accepted student must be selected to delete!';
			return false;
		}

		if (form_being_submitted) {
			alert("Deleting Selected Accepted Students, please wait a moment...");
			form.deleteSelected.disabled = true;
			return false;
		}

		form.deleteSelected.value = 'Deleting Selected Accepted Students...';
		form_being_submitted = true;
		return true;
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>