<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class=" icon-print" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Graduation Certificate Printing'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				
				<div style="margin-top: -30px;"><hr></div>
				<?= $this->Form->create('GraduateList'); ?>
				<div style=" margin-top: -20px;">
					<fieldset style="padding-top: 15px; padding-bottom: 5px;">
						<!-- <legend>&nbsp;&nbsp; Student Number/ID: &nbsp;&nbsp;</legend> -->
						<div class="row">
							<div class="large-3 columns">
								&nbsp;
							</div>
							<div class="large-4 columns">
								<?= $this->Form->input('studentnumber', array('placeholder' => 'Type Student ID here...', 'label' => false)); ?>
							</div>
							<div class="large-5 columns">
							<?= $this->Form->submit(__('Get Student Details'), array('name' => 'continueGraduationCertificatePrint', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
							</div>
						</div>
					</fieldset>
				</div>

				<?php
				if (!empty($graduation_certificate)) { ?>
					<hr>
					<table ccellspacing="0" cellpadding="0" class="table">
						<tbody>
							<tr>
								<td class="vcenter">Full Name: &nbsp;<?= $graduation_certificate['student_detail']['Student']['first_name'] . ' ' . $graduation_certificate['student_detail']['Student']['middle_name'] . ' ' . $graduation_certificate['student_detail']['Student']['last_name']; ?></td>
							</tr>
							<tr>
								<td class="vcenter">Sex: &nbsp;<?= ucwords(strtolower($graduation_certificate['student_detail']['Student']['gender'])); ?></td>
							</tr>
							<tr>
								<td class="vcenter">Student ID: &nbsp;<?= $graduation_certificate['student_detail']['Student']['studentnumber']; ?></td>
							</tr>
							<tr>
								<td class="vcenter">College: &nbsp;<?= $graduation_certificate['student_detail']['College']['name']; ?></td>
							</tr>
							<tr>
								<td class="vcenter">Department: &nbsp;<?= (!empty($graduation_certificate['student_detail']['Department']['name']) ? $graduation_certificate['student_detail']['Department']['name'] : 'Freshman Program'); ?></td>
							</tr>
							<tr>
								<td class="vcenter">Program: &nbsp;<?= $graduation_certificate['student_detail']['Program']['name']; ?></td>
							</tr>
							<tr>
								<td class="vcenter">Program Type: &nbsp;<?= $graduation_certificate['student_detail']['ProgramType']['name']; ?></td>
							</tr>
						</tbody>
					</table>
					<hr>
					<?php
					if (!empty($graduation_certificate_template)) {
						echo $this->Form->input('id', array('value' => $graduation_certificate['student_detail']['Student']['id']));
						echo $this->Form->submit(__('Get Graduation Certificate'), array('name' => 'displayGraduationCertificatePrint', 'class' => 'tiny radius button bg-blue', 'div' => false));
					}
				}
				echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>