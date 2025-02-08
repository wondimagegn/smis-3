<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Approve Clearance/Withdrawal Applicantions'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<?= $this->Form->create('Clearance', array('novalidate' => true)); ?>

				<div style=" margin-top: -30px;">
					<hr>
					<fieldset style="padding-bottom: 5px;">
                        <!-- <legend>&nbsp;&nbsp; Search / Filter Applications &nbsp;&nbsp;</legend> -->
                        <div class="row">
                            <div class="large-4 columns">
							<?= $this->Form->input('Search.academic_year', array('id' => 'AcadamicYear', 'label' => 'Acadamic Year: ', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear), 'style' => 'width:90%;')); ?>
                            </div>
                            <div class="large-4 columns">
							<?= $this->Form->input('Search.program_id', array('label' => 'Program: ', 'empty' => 'All Programs', 'options' => $programs, 'style' => 'width:90%;')); ?>
                            </div>
                            <div class="large-4 columns">	
								<?= $this->Form->input('Search.program_type_id', array('label' => 'Program Type: ', 'empty' => 'All Program Types', 'options' => $programTypes, 'style' => 'width:80%;')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="large-6 columns">
								<?php 
								if (!empty($departments)) { ?>
									<?= $this->Form->input('Search.department_id', array('label' => 'Department: ', 'options' => $departments, 'style' => 'width:80%;')); ?>
									<?php 
								} else if (!empty($colleges)) { ?>
									<?= $this->Form->input('Search.college_id', array('label' => 'College:', 'options' => $colleges, 'style' => 'width:90%;')); ?>
									<?php 
								} ?>
                            </div>
                            <div class="large-6 columns">
								<strong> Type: </strong> <br><br/>
								<?= $this->Form->input('Search.clear', array('type' => 'checkbox', 'label' => 'Clearance', 'div' => false,  (((isset($this->data['Search']) && $this->data['Search']['clear'] == 1) ||  (isset($this->request->data['Search']) && $this->request->data['Search']['clear'] == 'on')) ? 'checked' : ''))); ?><br/>
								<?=  $this->Form->input('Search.withdrawl', array('type' => 'checkbox', 'label' => 'Withdrawal', 'div' => false, (((isset($this->data['Search']) && $this->data['Search']['withdrawl'] == 1) ||  (isset($this->request->data['Search']) && $this->request->data['Search']['withdrawl'] == 'on')) ? 'checked' : ''))); ?>
                            </div>
                        </div>
						<hr>
						<?= $this->Form->submit(__('Filter Application'), array('name' => 'filterClearnce', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
					</fieldset>
				</div>

				<?php
				if (!empty($clearances) /* && isset($search) */) {
					$options = array('1' => ' Clear', '-1' => ' Not Clear');
					$attributes = array('legend' => false,/*  'label' => false, */ 'separator' => '<br/>');  ?>
					
					<br>
					<div class="smallheading fs16"><?= __('List of clearance/withdraw applicant processed by the system and not taken properties from the concerned bodies and waiting your decision.'); ?></div>
					<br>

					<h6 id="validation-message_non_selected" class="text-red fs14"></h6>

					<?php
					$start = 0;
					
					foreach ($clearances as $deptname => $program) {
						//echo '<strong class="fs14 text-gray">Department: ' . $deptname . '</strong><br>';
						foreach ($program as $progr_name => $programType) {
							//echo '<strong class="fs14 text-gray">Progam: ' . $progr_name . '</strong><br>';
							foreach ($programType as $progr_type_name => $clearnacess) {
								//echo '<strong class="fs14 text-gray">ProgramType: ' . $progr_type_name . '</strong><br>'; ?>
								<br>
								<div style="overflow-x:auto;">
									<table cellpadding="0" cellspacing="0" class="table" style="width: 100%;">
										<thead>
											<tr>
												<td colspan="8" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
													<span style="font-size:16px;font-weight:bold; margin-top: 25px;"><?= $deptname; ?></span>
														<br>
														<span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
															<?= $progr_name. ' &nbsp; | &nbsp; ' . $progr_type_name; ?>
															<br>
														</span>
													</span>
													<span class="text-black" style="padding-top: 14px; font-size: 14px; font-weight: bold">
														
													</span>
												</td>
											</tr>
											<tr>
												<th class="center" style="width: 3%;">#</th>
												<th class="vcenter" style="width: 20%;">Full Name</th>
												<th class="center">Student ID</th>
												<th class="center">Sex</th>
												<th class="center">Type</th>
												<th class="center">Reason</th>
												<th class="center">Request Date</th>
												<th class="center" style="width: 15%;">Clearnce</th>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach ($clearnacess as $clearance) { ?>
												<tr>
													<td class="center"><?= ++$start; ?></td>
													<td class="vcenter"><?= $clearance['Student']['full_name']; ?></td>
													<td class="center"><?= $this->Html->link($clearance['Student']['studentnumber'], array('controller' => 'students', 'action' => 'student_academic_profile', $clearance['Student']['id'])); ?></td>
													<td class="center"><?= ((strcasecmp(trim($clearance['Student']['gender']), 'male') == 0) ? 'M' : 'F'); ?></td>
													<td class="center"><?= (isset($clearance['Clearance']['type']) ? ucfirst($clearance['Clearance']['type']) : '');
														if (isset($clearance['Attachment']) && !empty($clearance['Attachment'])) {
															echo "<br/> <a href=" . $this->Media->url($clearance['Attachment'][0]['dirname'] . DS . $clearance['Attachment'][0]['basename'], true) . " target=_blank'>View Attachment</a>";
														} ?>
														<?= $this->Form->hidden('Clearance.' . $start . '.id', array('value' => $clearance['Clearance']['id'])); ?>
														<?= $this->Form->hidden('Clearance.' . $start . '.student_id', array('value' => $clearance['Student']['id'])); ?>
													</td>
													<td class="center"><?= (isset($clearance['Clearance']['reason']) ? $clearance['Clearance']['reason'] : ''); ?></td>
													<td class="center"><?= ($this->Time->format("M j, Y", $clearance['Clearance']['request_date'], NULL, NULL)); ?></td>
													<td class="vcenter" style="padding-left: 5%; padding-top: 2%;"><?= $this->Form->radio('Clearance.' . $start . '.confirmed', $options, $attributes); ?></td>
												</tr>
												<?php
											} ?>
										</tbody>
									</table>
								</div>
								<br>
								<?php
							}
						}
					} ?>

					<hr>

					<div class="large-4 columns">
						<?= $this->Form->submit('Process Selected', array('name' => 'saveIt', 'id' => 'saveIt', 'div' => 'false', 'class' => 'tiny radius button bg-blue')); ?>
					</div>
					<div class="large-8 columns">
						<?= $this->Form->reset('Reset Form', array('name' => 'resetForm', 'id' => 'resetForm', 'class' => 'tiny radius button bg-red')); ?>
					</div>
					<?php
				} ?>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	var form_being_submitted = false;

    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	$('#saveIt').click(function() {
		
		var radios = document.querySelectorAll('input[type="radio"]');
		var checkedOne = Array.prototype.slice.call(radios).some(x => x.checked);

        //alert(checkedOne);
		if (!checkedOne) {
            alert('At least one request must be selected as clear or not clear.');
			validationMessageNonSelected.innerHTML = 'At least one request must be selected as clear or not clear.';
			return false;
		}

		if (form_being_submitted) {
			alert("Processing Selected requests , please wait a moment...");
			$('#saveIt').attr('disabled', true);
			return false;
		}

		var confirmm = confirm('Are you sure you want to process the selected clearance/withdarwal requests?');

		if (confirmm) {
			$('#saveIt').val('Processing Selected ...');
			form_being_submitted = true;
			return true;
		} else {
			return false;
		}

	});

	$('#resetForm').click(function() {
		return confirm('Reseting the form will discard any the selected clearance/withdarwal requests. Are you sure you want to reset the form?');
	});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
	
</script>