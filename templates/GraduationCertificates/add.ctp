<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Add Graduation Cetrificate Template'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('GraduationCertificate'); ?>
				<table cellpadding="0" cellspacing="0" class="fs13 table-borderless">
					<tbody>
						<tr>
							<td>Program:</td>
							<td><?= $this->Form->input('program_id', array('label' => false, 'style' => 'width:150px')); ?></td>
						</tr>
						<tr>
							<td style="background-color: white;">Program Type:</td>
							<td style="background-color: white;"><?= $this->Form->input('program_type_id', array('label' => false, 'style' => 'width:150px')); ?></td>
						</tr>

						<tr>
							<td>Department:</td>
							<td colspan="3"><?= $this->Form->input('department', array('id' => 'Department', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $departments, 'default' => $default_department_id)); ?></td>
						</tr>

						<tr>
							<td style="background-color: white;">Title (Amharic):</td>
							<td style="background-color: white;"><?= $this->Form->input('amharic_title', array('label' => false, 'type' => 'text')); ?></td>
						</tr>
						<tr>
							<td>Certificate Content (Amharic)</td>
							<td><?= $this->Form->input('amharic_content', array('label' => false, 'cols' => 90, 'rows' => 10)); ?></td>
						</tr>
						<tr>
							<td style="background-color: white;">Content Key Words</td>
							<td  style="background-color: white;">
								STUDENT_NAME => Student name <br />
								<!-- STUDENT_DEPARTMENT => Student Department </br /> -->
								SPECIALIZATION_DEGREE_NOMENCLATURE => Specialization Degree Nomenclature <br />
								DEGREE_NOMENCLATURE => Degree Nomenclature <br />
								GRADUATION_DATE => Graduation Date <br />
								STUDENT_CGPA => Student CGPA <br />
								STUDENT_MCGPA => Student Major CGPA<br />
								EXIT_EXAM_RESULT => National Exit Exam Result<br /><br />
							</td>
						</tr>
						<tr>
							<td>Title Font Size (Amharic)</td>
							<td><?= $this->Form->input('am_title_font_size', array('label' => false, 'type' => 'select', 'options' => array(12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16, 17 => 17, 18 => 18, 19 => 19, 20 => 20, 21 => 21, 22 => 22, 23 => 23, 24 => 24, 25 => 25), 'style' => 'width:50px')); ?></td>
						</tr>
						<tr>
							<td style="background-color: white;">Content Font Size (Amharic)</td>
							<td style="background-color: white;"><?= $this->Form->input('am_content_font_size', array('label' => false, 'type' => 'select', 'options' => array(12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16, 17 => 17, 18 => 18, 19 => 19), 'style' => 'width:50px')); ?></td>
						</tr>
						<tr>
							<td>Title (English)</td>
							<td><?= $this->Form->input('english_title', array('label' => false, 'type' => 'text')); ?></td>
						</tr>
						<tr>
							<td style="background-color: white;">Content (English)</td>
							<td style="background-color: white;"><?= $this->Form->input('english_content', array('label' => false, 'cols' => 90, 'rows' => 10)); ?></td>
						</tr>
						<tr>
							<td>Title Font Size (English)</td>
							<td><?= $this->Form->input('en_title_font_size', array('label' => false, 'type' => 'select', 'options' => array(12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16, 17 => 17, 18 => 18, 19 => 19, 20 => 20, 21 => 21, 22 => 22, 23 => 23, 24 => 24, 25 => 25), 'style' => 'width:50px')); ?></td>
						</tr>
						<tr>
							<td style="background-color: white;">Content Font Size (English)</td>
							<td style="background-color: white;"><?= $this->Form->input('en_content_font_size', array('label' => false, 'type' => 'select', 'options' => array(12 => 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16, 17 => 17, 18 => 18, 19 => 19), 'style' => 'width:50px')); ?></td>
						</tr>
						<tr>
							<td>Academic Year</td>
							<td><?php echo $this->Form->input('academic_year', array('type' => 'select', 'options' => $acs, 'label' => false, 'div' => false, 'style' => 'width:100px', 'class' => 'fs14')); ?>
								<?php //echo $this->Form->year('academic_year', Configure::read('Calendar.universityEstablishement'), date('Y')+1, date('Y'), array('empty' => false, 'label' => false, 'div' => false, 'style' => 'width:100px', 'class' => 'fs14')); 
								?>
							</td>
						</tr>
						<tr>
							<td style="background-color: white;"><br />Applicable for Current Student<br /><br /></td>
							<td style="background-color: white;"><br /><?= $this->Form->input('applicable_for_current_student', array('label' => false)); ?><br /><br /></td>
						</tr>
					</tbody>
				</table>
				<hr>
				<?= $this->Form->end(array('label' => __('Submit', true), 'class' => 'tiny radius button bg-blue')); ?>
			</div>
		</div>
	</div>
</div>