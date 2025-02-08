<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?=  __('Request Department Transfer'); ?></span>
		</div>
	</div>
    <div class="box-body">
       <div class="row">
	  		<div class="large-12 columns">

			  	<div style="margin-top: -30px;"><hr></div>

				<blockquote>
					<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
					<p style="text-align:justify;" class="fs16 text-black">Provide receiver college and department, your current department will make decision and forward to receiver department if your transfer is approved by your current department.<strong>You will be responsible for all the consequences that such tranfer may entail.</strong></p> 
				</blockquote>
				<hr>

				
				<?php
				$currentDepartment = (isset($student_section_exam_status['StudentBasicInfo']['department_id']) ? $student_section_exam_status['StudentBasicInfo']['department_id'] : null);
				//debug($currentDepartment);
				$showButton = true;

				if (isset($error_message) && !empty($error_message)) {
					$showButton = false; ?>
					<div class='error-box error-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal;"><span style='margin-right: 15px;'></span><?= $error_message; ?>You also have stayed in <?= $student_section_exam_status['Department']['name'] ; ?> department for <strong><?= count($attended_semester); ?> semester</strong>. You can't ask for transfer at this time.</div>
					<?php
				} else if (isset($attended_semester) && MAXIMUM_ALLOWED_ATTENDED_SEMESTERS_FOR_TRANSFER <= count($attended_semester)) {
					$showButton = false; ?>
					<div class='error-box error-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal;"><span style='margin-right: 15px;'></span>You have stayed in <?= $student_section_exam_status['Department']['name'] ; ?> department for <strong><?= count($attended_semester); ?> semester</strong> which is more than the allowed maximum <?= MAXIMUM_ALLOWED_ATTENDED_SEMESTERS_FOR_TRANSFER ?> semesters to apply for transfer. You can't ask for transfer at this time.</div>
					<?php
				} else if (isset($attended_semester) && count($attended_semester) == 1) { ?>
					<div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal;"><span style='margin-right: 15px;'></span>You have stayed in <?= $student_section_exam_status['Department']['name'] ; ?> department for <strong><?= count($attended_semester); ?> semester</strong>, request for transfer if you are elegible and all of your registered/added course grades are fully submitted and approved. Please do not request transfer if you joined your current department on affirmative basis.</div>
					<?php
				} else if (isset($attended_semester) && count($attended_semester) > 1) { ?>
					<div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal;"><span style='margin-right: 15px;'></span>You have stayed in <?= $student_section_exam_status['Department']['name'] ; ?> department for <strong><?= count($attended_semester); ?> semester</strong>, request for transfer if you are elegible and all of your registered/added course grades are fully submitted and approved. Please do not request transfer if you joined your current department on affirmative basis.</div>
					<?php
				} else { ?>
					<div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: normal;"><span style='margin-right: 15px;'></span>Request for transfer if you are elegible and all of your registered/added course grades are fully submitted and approved. Please do not request transfer if you joined your current department on affirmative basis.</div>
					<?php
				} ?>

				<?= $this->Form->create('DepartmentTransfer'); ?>

				
				
				<div class="row">
					<div class="large-5 columns">
						<fieldset style="padding-bottom: 10px;">
							<legend>&nbsp;&nbsp; Target Department Location &nbsp;&nbsp;</legend>
							<div class="row">
								<div class="large-12 columns">
									<?= $this->Form->hidden('student_id', array('value' => $student_section_exam_status['StudentBasicInfo']['id'])); ?>
									<?= $this->Form->input('Student.college_id', array('id' => 'college_id_1', 'onchange' => 'updateDepartmentCollege(1)', 'label' => 'Receiver College/Institute/School: ', 'empty' => '[ Select College/Institute/School ]', 'required' => 'required', 'style' => 'width:90%;')); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-12 columns">
									<?= $this->Form->input('department_id', array('id' => 'department_id_1', 'empty' => '[ Select College First ]', 'required' => 'required', 'style' => 'width:90%;')); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-12 columns">
									<?= $this->Form->input('transfer_request_date', array('maxYear' => date('Y'), 'minYear' => date('Y'), 'style'=>'width:90px;', 'disabled' => 'disabled')); ?>
									<?= $this->Form->hidden('transfer_request_date', array('value' => date('Y-m-d'))); ?>
								</div>
							</div>
						</fieldset>
						<hr>
						<?= ($showButton ? $this->Form->Submit('Submit Request',array('name' => 'saveTransfer', 'id' => 'saveTransfer', 'class' => 'tiny radius button bg-blue')) : ''); ?>
					</div>

					<div class="large-7 columns" style="padding-top: 25px;">
						<?= $this->element('student_basic'); ?>
					</div>
				</div>
				<?= $this->Form->end(); ?>
			</div>
		</div>
    </div>
</div>

<script type='text/javascript'>
	function updateDepartmentCollege(id) {
		
		var formData = $("#college_id_"+id).val();
		var currentDepartment = <?= $currentDepartment; ?>;
		var dropdown = $("#department_id_"+id);

		var student_program_id = <?= $student_section_exam_status['StudentBasicInfo']['program_id']; ?>;

		if (formData && student_program_id) {
			$("#college_id_"+id).attr('disabled', true);
			$("#department_id_"+id).attr('disabled', true);
			$("#saveTransfer").attr('disabled',true);
			//get form action

			var formUrl = '/departmentTransfers/get_department_combo/' + formData + '/' + currentDepartment + '/' + student_program_id;

			$.ajax({
				type: 'get',
				url: formUrl,
				data: formData,
				success: function(data,textStatus,xhr){
					$("#department_id_"+id).attr('disabled', false);
					$("#college_id_"+id).attr('disabled', false);
					$("#saveTransfer").attr('disabled',false);
					$("#department_id_"+id).empty();
					$("#department_id_"+id).append(data);
					//add required attribute after 
					$("#department_id_"+id).prop('required', true);
				},
				error: function(xhr,textStatus,error){
					alert(textStatus);
				}
			});
			
			return false;
		} else {
			$("#department_id_"+id).empty().append('<option value="">[ Select Department ]</option>');
		}
	}
</script>
