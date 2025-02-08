<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Accepted Student Details: ' . (isset($this->request->data['AcceptedStudent']) ? $this->request->data['AcceptedStudent']['full_name'] . '  (' .  $this->request->data['AcceptedStudent']['studentnumber'] . ')' : ''); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('AcceptedStudent', array('action' => 'view')); ?>
				<div class="large-12 columns">
					<div class="row">
						<div class="large-4 columns">
							<?php // echo $this->Form->input('id'); 
							?>
							<?= $this->Form->input('first_name', array('style' => 'width:90%', 'label' => 'First Name: ', 'required', 'readOnly')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('middle_name', array('style' => 'width:90%', 'label' => 'Middle Name: ', 'required', 'readOnly')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('last_name', array('style' => 'width:90%', 'label' => 'Last Name: ', 'required', 'readOnly')); ?>
						</div>
					</div>
				</div>
				<div class="large-12 columns">
					<div class="row">
						<div class="large-4 columns">
							<?php
							$options = array('male' => ' Male', 'female' => ' Female');
							echo '<h6 class="fs13 text-gray">Sex: </h6> ' . $this->Form->input('sex', array('options' => $options, 'type' => 'radio', 'disabled', 'legend' => false, 'separator' => ' &nbsp; ', 'label' => false));
							?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('EHEECE_total_results', array('style' => 'width:90%', 'label' => 'EHEECE Result: ', 'required', 'readOnly')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('moeadmissionnumber', array('style' => 'width:90%', 'label' => 'MoE Admission Number: ', 'readOnly')); ?>
						</div>
					</div>
				</div>
				<div class="large-12 columns">
					<div class="row">
						<div class="large-4 columns">
							<?= $this->Form->input('studentnumber', array('style' => 'width:90%', 'label' => 'Student ID: ', 'required', 'readOnly')); ?>
						</div>
						<div class="large-8 columns">
							<?= $this->Form->input('high_school', array('style' => 'width:95%', 'label' => 'High School Attended: ', 'readOnly')); ?>
						</div>
					</div>
				</div>
				<div class="large-12 columns">
					<div class="row">
						<div class="large-3 columns">
							<?= $this->Form->input('academicyear', array('style' => 'width:90%', 'id' => 'academicyear', 'label' => 'Admission Year', 'type' => 'select', 'options' => $acyear_array_data, ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : 'disabled'), 'empty' => '[ Select Admission Year ]', 'default' => isset($currentacyeardata) ? $currentacyeardata : '')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('program_id', array('style' => 'width:90%', 'label' => 'Program: ', 'disabled')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('program_type_id', array('style' => 'width:90%', 'label' => 'Program Type: ', 'disabled')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('placement_type_id', array('style' => 'width:90%', 'label' => 'Placement Type: ', 'empty' => '', ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : 'disabled'))); ?>
						</div>
					</div>
				</div>
				<div class="large-12 columns">
					<div class="row">
						<div class="large-4 columns">
							<?= $this->Form->input('campus_id', array('style' => 'width:90%', 'label' => 'Campus: ', 'disabled', 'empty' => '[ Select Campus ]')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('college_id', array('style' => 'width:90%', 'label' => 'College: ', 'id' => 'CollegeID', ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : 'disabled'))); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('department_id', array('style' => 'width:90%', 'label' => 'Department: ', 'default' => isset($selected_department) ? $selected_department : '', 'empty' => ' College Freshman ', 'id' => 'DepartmentID', ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1 ? '' : 'disabled'))); ?>
						</div>
					</div>
				</div>
				<div class="large-12 columns">
					<div class="row">
						<div class="large-4 columns">
							<?= $this->Form->input('region_id', array('label' => 'Region: ', 'required', 'style' => 'width:90%', 'empty' => '[ Select Region ]', 'disabled')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('disability_id', array('label' => 'Disability: ', 'style' => 'width:90%', 'empty' => '[ Select Disability(If Applicable) ]', 'disabled')); ?>
						</div>
						<div class="large-4 columns">
							<?= $this->Form->input('foreign_program_id', array('label' => 'Foreign Program: ', 'style' => 'width:90%', 'empty' => '[ Select Foreign Program(If Applicable) ]', 'disabled')); ?>
						</div>
					</div>
				</div>
				<br>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script>
	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>