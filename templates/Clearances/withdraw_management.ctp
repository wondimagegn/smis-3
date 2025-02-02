<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Withdrawal Management');?></span>
		</div>
	</div>
    <div class="box-body">
    	<div class="row">
	  		<div class="large-12 columns">

				<?= $this->Form->create('Clearance', array('novalidate' => true)); ?>

				<div style=" margin-top: -30px;">
					<hr>
					<blockquote>
						<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<p style="text-align:justify;" class="fs14 text-black"> Only those students who are cleared will appear here to to save their withdrawal</p> 
					</blockquote>
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
								<?=  $this->Form->input('Search.withdrawl', array('type' => 'checkbox', 'label' => 'Withdrawal', 'checked' => 'checked', 'disabled')); ?>
                            </div>
                        </div>
						<hr>
						<?= $this->Form->submit(__('Filter Application'), array('name' => 'filterClearnce', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
					</fieldset>
				</div>

				<div class="clearances index">
					<?php 
					if (!empty($clearances)) {
						$options = array('1' => ' Accept', '-1' => ' Reject');
						$attributes = array('legend' => false, /* 'label' => false,  */'separator' => '<br/>'); ?>

						<h6 id="validation-message_non_selected" class="text-red fs14"></h6>


						<?php

						$start = 0;

						foreach ($clearances as $deptname => $program) {
							//echo '<div class="fs16">Department: '.$deptname.'</div>';
							foreach ($program as $progr_name=>$programType) {
								//echo '<div class="fs16">Progam: '.$progr_name.'</div>'; 
								foreach ($programType as $progr_type_name=>$clearnacess) {
									//echo '<div class="fs16">ProgramType: '.$progr_type_name.'</div>'; ?>
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
													<th class="vcenter" style="width: 20%;"><?= $this->Paginator->sort('student_id', 'Full Name');?></th>
													<th class="center"><?= $this->Paginator->sort('studentnumber', 'Student ID');?></th>
													<th class="center"><?= $this->Paginator->sort('request_date', 'Requested');?></th>
													<th class="center"><?= $this->Paginator->sort('reason');?></th>
													<th class="center"><?= $this->Paginator->sort('Attachment');?></th>
													<th class="center"><?= $this->Paginator->sort('Decision');?></th>
													<th class="center"><?= $this->Paginator->sort('minute_number', 'Minute No');?></th>
												</tr>
											</thead>
											<tbody>
												<?php
												foreach ($clearnacess as $clearance) { ?>
													<tr>
														<td class="center"><?= ++$start; ?></td>
														<td class="vcenter"><?= $this->Html->link($clearance['Student']['full_name'], array('controller' => 'students', 'action' => 'student_academic_profile', $clearance['Student']['id'])); ?></td>
														<td class="center"><?= $this->Html->link($clearance['Student']['studentnumber'], array('controller' => 'students', 'action' => 'student_academic_profile', $clearance['Student']['id'])); ?></td>
														<td class="center"><?= ($this->Time->format("M j, Y", $clearance['Clearance']['request_date'], NULL, NULL)); ?></td>
														<td class="center"><?= $clearance['Clearance']['reason']; ?></td>
														<td class="center">
															<?= $this->Form->hidden('Clearance.' . $start . '.id', array('value' => $clearance['Clearance']['id'])); ?>
															<?= $this->Form->hidden('Clearance.' . $start . '.student_id', array('value' => $clearance['Student']['id'])); ?>
															<?php
															if (isset($clearance['Attachment']) && !empty($clearance['Attachment'])) {
																echo "<br/> <a href=" . $this->Media->url($clearance['Attachment'][0]['dirname'] . DS . $clearance['Attachment'][0]['basename'], true) . " target=_blank'>View Attachment</a>";
															} else {
																echo 'Not Available';
															} ?>
														</td>	
														<td class="center" style="padding-top: 1%;"><?= $this->Form->radio('Clearance.' . $start . '.forced_withdrawal', $options, $attributes); ?></td>
														<td class="center"  style="padding-top: 1%;"><?= $this->Form->input('Clearance.' . $start . '.minute_number', array('label' => false, /* 'required' */)); ?></td>
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
 						<div class="row">
							<div class="large-4 columns">
								<?= $this->Form->submit('Process Selected', array('name' => 'saveIt', 'id' => 'saveIt', 'div' => 'false', 'class' => 'tiny radius button bg-blue')); ?>
							</div>
							<div class="large-8 columns">
								<?= $this->Form->reset('Reset Form', array('name' => 'resetForm', 'id' => 'resetForm', 'class' => 'tiny radius button bg-red')); ?>
							</div>
						</div>
						<?php

					} ?>
					<?=  $this->Form->end(); ?>
				</div>
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
            alert('At least one request must be selected as accepted or not rejected.');
			validationMessageNonSelected.innerHTML = 'At least one request must be selected as accepted or not rejected.';
			return false;
		}

		if (form_being_submitted) {
			alert("Processing Selected withdrawal requests , please wait a moment...");
			$('#saveIt').attr('disabled', true);
			return false;
		}

		/* var isValid = true;

		var inputs = document.querySelectorAll('#ClearanceWithdrawManagementForm input[required]');

		for (var i = 0; i < inputs.length; i++) {
			if (!inputs[i].value) {
				isValid = false;
				inputs[i].focus();
				break;
			}
		}

		if (!isValid) {
			alert("Please fill out all required fields.");
			return false;
		} */

		var confirmm = confirm('Are you sure you want to process the selected withdarwal requests?');

		if (confirmm) {
			$('#saveIt').val('Processing Selected Withdrawals...');
			form_being_submitted = true;
			return true;
		} else {
			return false;
		}

	});

	$('#resetForm').click(function() {
		return confirm('Reseting the form will discard any the selected withdarwal requests. Are you sure you want to reset the form?');
	});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
	
</script>