<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class=" icon-print" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Language Proficiency Printing'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<?= $this->Form->create('GraduateList'); ?>

				<div style=" margin-top: -30px;">
					<hr>
					<fieldset style="padding-top: 15px; padding-bottom: 5px;">
						<!-- <legend>&nbsp;&nbsp; Student Number/ID: &nbsp;&nbsp;</legend> -->
						<div class="row">
							<div class="large-3 columns">
								&nbsp;
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('studentnumber', array('placeholder' => 'Type Student ID here...', 'id' => 'studentNumber', 'label' => false)); ?>
							</div>
							<div class="large-5 columns">
								<?= $this->Form->submit(__('Get Student Details'), array('name' => 'continueLanguageProficiencyLetterPrint', 'id' => 'getStudentDetails', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
							</div>
						</div>
					</fieldset>
				</div>
				<hr>

				<?php
				if (!empty($graduation_letter)) { 
					$credit_hour_sum = 0;
                    foreach ($graduation_letter['Student']['StudentExamStatus'] as $ses_key => $ses_value) {
                        $credit_hour_sum += $ses_value['credit_hour_sum'];
                    } ?>
					<table cellspacing="0" cellpadding="0" class="fs13 table-borderless">
						<!-- <tbody> -->
							<tr>
								<td class="vcenter">Full Name: &nbsp;<?= $graduation_letter['Student']['full_name'] . ' / ' .$graduation_letter['Student']['full_am_name']; ?></td>
							</tr>
							<tr>
								<td class="vcenter">Sex: &nbsp;<?= ucwords(strtolower(trim($graduation_letter['Student']['gender']))); ?></td>
							</tr>
							<tr>
								<td class="vcenter">Student ID: &nbsp;<?= $graduation_letter['Student']['studentnumber']; ?></td>
							</tr>
							<tr>
								<td class="vcenter">College: &nbsp;<?= $graduation_letter['Student']['College']['name']; ?></td>
							</tr>
							<tr>
								<td class="vcenter">Department: &nbsp;<?= (!empty($graduation_letter['Student']['Department']['name']) ? $graduation_letter['Student']['Department']['name'] : 'Freshman Program'); ?></td>
							</tr>
							<tr>
								<td class="vcenter">Program: &nbsp;<?= $graduation_letter['Student']['Program']['name']; ?></td>
							</tr>
							<tr>
								<td class="vcenter">Program Type: &nbsp;<?= $graduation_letter['Student']['ProgramType']['name']; ?></td>
							</tr>
							<tr>
								<td class="vcenter">Degree Designation (EN): &nbsp;<?= $graduation_letter['Student']['Curriculum']['english_degree_nomenclature']; ?></td>
							</tr>
							<tr>
								<td class="vcenter">Degree Designation (AM): &nbsp;<?= $graduation_letter['Student']['Curriculum']['amharic_degree_nomenclature']; ?></td>
							</tr>
							<tr>
								<td class="vcenter">Credit Type: &nbsp; <?= (count(explode('ECTS', $graduation_letter['Student']['Curriculum']['type_credit'])) == 2 ? 'ECTS' : 'Credit'); ?></td>
							</tr>
							<tr>
								<td class="vcenter">Required <?= (count(explode('ECTS', $graduation_letter['Student']['Curriculum']['type_credit'])) == 2 ? 'ECTS' : 'Credit'); ?> for Graduation: &nbsp; <?= $graduation_letter['Student']['Curriculum']['minimum_credit_points']; ?></td>
							</tr>
							<tr>
								<td class="vcenter">Taken <?= (count(explode('ECTS', $graduation_letter['Student']['Curriculum']['type_credit'])) == 2 ? 'ECTS' : 'Credit'); ?>: &nbsp; <?= $credit_hour_sum; ?></td>
							</tr>
							<tr>
								<td class="vcenter">Date Graduated: &nbsp;<?= $this->Time->format("M j, Y", $graduation_letter['GraduateList']['graduate_date'], NULL, NULL); ?></td>
							</tr>
							<tr>
								<td class="vcenter">CGPA: &nbsp;<?= $graduation_letter['Student']['StudentExamStatus'][0]['cgpa']; ?></td>
							</tr>
							<?= (isset($graduation_letter['Student']['Curriculum']['DepartmentStudyProgram']['Qualification']['qualification']) ?  '<td class="vcenter">Qualification: &nbsp;' . $graduation_letter['Student']['Curriculum']['DepartmentStudyProgram']['Qualification']['qualification'] . '</td></tr>' : ''); ?>
						<!-- </tbody> -->
					</table>
					<hr>

					<?= $this->element('cost_sharing_due_and_payment'); ?>
					<hr>
					<?= $this->element('student_clearance_list'); ?>
					<hr>
					
					<?= $this->Form->hidden('student_id', array('value' => $graduation_letter['Student']['id'])); ?>
					<?= $this->Form->input('correct_degree_designation', array('id' => 'showCorrection', 'label' => 'Correct Degree Designation', 'type' => 'checkbox', /* 'checked' => 'checked' */)); ?>

					<div id="showCorrectionForm" style="margin-top: 10px;">
						<blockquote>
							<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
							<p style="text-align:justify;"><span class="fs16"> The Degree Nomenclature in the following field could be auto corrected for proper capitalizations and space removals. This might slightly differ from the original degree designation set in the curriculum.<span class="text-red"> Editing this will not modify the original degree designation which is saved in the system. This is for correcting minor errors and It will be used for one time printing only.</span></p> 
						</blockquote>
						<?= $this->Form->input('degree_nomenclature_formatted', array('label' => 'Degree Nomenclature Correction:', 'value' => $degree_nomenclature_formatted)); ?>
					</div>

					<hr>
					<?= $this->Form->submit(__('Get Language Proficiency Letter'), array('name' => 'displayLanguageProficiencyLetterPrint', 'class' => 'tiny radius button bg-blue', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
					<?php
					
				} ?>

				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    function showIncludePre(id) {
        if ($("#" + id).is(":checked") && $(".onlyPre").css("display") == 'none') {
            $(".onlyPre").css("display", "block");
        } else {
            $(".onlyPre").css("display", "none");
        }
    }

    $(document).ready(function() {
        if ($("#showCorrection").is(":checked")) {
            $("#showCorrectionForm").css("display", "block");
        } else {
            $("#showCorrectionForm").css("display", "none");
        }

        $("#showCorrection").click(function() {
            // If checked
            if ($("#showCorrection").is(":checked")) {
                $("#showCorrectionForm").show(/* "fast" */);
            } else {
                //otherwise, hide it 
                $("#showCorrectionForm").hide(/* "fast" */);
            }
        });
    });

	var form_being_submitted = false;

	$('#getStudentDetails').click(function(event) {
		var isValid = true;
		var studentNumber = $('#studentNumber').val();

		if (studentNumber == '') {
			event.preventDefault();
			$('#studentNumber').focus();
            isValid = false;
            return false;
		}

		if (form_being_submitted) {
			alert('Fetching Student Details, please wait a moment or refresh your page...');
			$('#getStudentDetails').attr('disabled', true);
			isValid = false;
			return false;
		}

		if (!form_being_submitted && isValid) {
			$('#getStudentDetails').val('Fetching Student Details...');
			form_being_submitted = true;
			isValid = true
			return true;
		} else {
			return false;
		}
	});
</script>