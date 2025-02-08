<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Edit Academic Calendar'); ?></span>
        </div>
    </div>
    <div class="box-body">
    	<div class="row">
	  		<div class="large-12 columns">
			  	<div style="margin-top: -30px;"><hr></div>

			  	<?= $this->Form->create('AcademicCalendar'); ?>
				<?= $this->Form->input('id'); ?>

				<fieldset style="padding-bottom: 15px;padding-top: 15px;">
					<!-- <legend>&nbsp;&nbsp; Search / Filter &nbsp;&nbsp;</legend> -->
					<div class="row">
						<div class="large-2 columns">
							<?= $this->Form->input('academic_year',array('id' => 'academicYear', 'label' =>'Academic Year: ', 'style' => 'width:90%', 'type' => 'select', 'required', 'options' => $acyear_array_data, 'empty'=>"[ Select Academic Year ]", 'default' => (isset($this->request->data['AcademicCalendar']['academic_year']) ? $this->request->data['AcademicCalendar']['academic_year'] : $defaultacademicyear))); ?>
						</div>
						<div class="large-2 columns">
							<?= $this->Form->input('semester', array('id' => 'semester', 'label' => 'Semester: ', 'style' => 'width:90%', 'options' => Configure::read('semesters'), 'required', 'empty' => '[ Select Semester ]')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('program_id',  array('id' => 'programType', /* 'onchange' => 'getDepartmentsOnProgramChange()', */ 'label' => 'Program: ', 'style' => 'width:90%', 'required', 'empty' => '[ Select ]')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('program_type_id', array('id' => 'programTypeId', /* 'onchange' => 'getDepartmentsOnProgramChange()', */ 'label' => 'Program Type: ', 'style' => 'width:90%', 'required', 'empty' => '[ Select ]')); ?>
						</div>
						<div class="large-2 columns">
							<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
							<?= $this->Form->input('year_level_id', array('type' => 'select', 'id' => 'yearLevels', 'multiple' => 'checkbox')); ?>
						</div>
					</div>
				</fieldset>
				<hr>

				<table cellpadding="0" cellspacing="0" class="table">
					<tbody>
						<tr>
							<td style="background-color: white;">
								<table cellpadding="0" cellspacing="0" class="table" >
									<tr>
										<td><?= $this->Form->input('AcademicCalendar.department_id',array('multiple'=> 'checkbox', 'options' => $departments, 'div' => false, 'label' => false, 'checked' => (isset($this->request->data['AcademicCalendar']['department_id']) ? $this->request->data['AcademicCalendar']['department_id'] : array_keys($departments_ids)))); ?></td>
									</tr>
								</table>	
							</td>
							<td style="background-color: white;">
								<table cellpadding="0" cellspacing="0" class="table">
									<tbody>
										<tr><td><?= $this->Form->input('course_registration_start_date', array('id' => 'crStart', 'label' => 'Registration Start', 'style' => 'width:80px;', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+1)); ?></td></tr>
										<tr><td><?= $this->Form->input('course_registration_end_date', array('id' => 'crEnd', 'label' => 'Registration End', 'style' => 'width:80px;', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+1)); ?></td></tr>
										<tr><td><?= $this->Form->input('course_add_start_date', array('id' => 'caStart', 'label' => 'Course Add Start', 'style' => 'width:80px;', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+1)); ?></td></tr>
										<tr><td><?= $this->Form->input('course_add_end_date', array('id' => 'caEnd', 'label' => 'Course Add End', 'style' => 'width:80px;', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+1)); ?></td></tr>
										<tr><td><?= $this->Form->input('course_drop_start_date', array('id' => 'cdStart', 'label' => 'Course Drop Start', 'style' => 'width:80px;', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+1)); ?></td></tr>
										<tr><td><?= $this->Form->input('course_drop_end_date', array('id' => 'cdEnd', 'label' => 'Course Drop End', 'style' => 'width:80px;', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+1)); ?></td></tr>
										<tr><td><?= $this->Form->input('grade_submission_start_date', array('id' => 'gsStart', 'label' => 'Grade Submission Start', 'style' => 'width:80px;', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+1)); ?></td></tr>
										<tr><td><?= $this->Form->input('grade_submission_end_date', array('id' => 'gsEnd', 'label' => 'Grade Submission End', 'style' => 'width:80px;', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+1)); ?></td></tr>
										<tr><td colspan='2'><?= $this->Form->input('grade_fx_submission_end_date', array('id' => 'fxEnd', 'label' => 'Fx Grade Submission', 'type' => 'date', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+1, 'style' => 'width:80px;')); ?></td></tr>
										<tr><td colspan='2'><?= $this->Form->input('senate_meeting_date', array('id' => 'senateDate', 'label' => 'Senate Meeting Date', 'type' => 'date', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+1, 'style' => 'width:80px;')); ?></td></tr>
										<tr><td colspan='2'><?= $this->Form->input('graduation_date', array('id' => 'graduationDate', 'label' => 'Graduation Date', 'type' => 'date', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+1, 'style' => 'width:80px;')); ?></td></tr>
										<tr><td><?= $this->Form->input('online_admission_start_date', array('id' => 'oaStart', 'label' => 'Online Admission Start Date', 'type' => 'date', 'minYear' => date('Y')-2,'maxYear' => date('Y')+1, 'style' => 'width:80px;')); ?></td></tr>
										<tr><td><?= $this->Form->input('online_admission_end_date', array('id' => 'oaEnd', 'label' => 'Online Admission End Date', 'type' => 'date', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+1, 'style' => 'width:80px;')); ?></td></tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
				<hr>
				<?= $this->Form->end(array('label'=>__('Save Changes'),'class' => 'tiny radius button bg-blue')); ?>
	  		</div>
		</div>
    </div>
</div>
