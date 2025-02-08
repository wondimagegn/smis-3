<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('List Admitted Students'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<?php
				if ($this->Session->check('display_field_student')) {
					$this->request->data['Display'] = $this->Session->read('display_field_student');
				} ?>
				
				<?= $this->Form->Create('Student', array('action' => 'search')); ?>

				<?php
				if ($role_id != ROLE_STUDENT) { ?>
					<div style="margin-top: -30px;">
						<hr>
						<?php
						if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) { ?>
							<div style="margin-top: -5px;">
								<blockquote>
									<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
									<span style="text-align:justify;" class="fs14 text-gray">The student list you will get here depends on your <b style="text-decoration: underline;"><i>assigned College or Department, assigned Program and Program Types, and with your search conditions</i></b>. You can contact the registrar to adjust permissions assigned to you if you miss your students here.</span>
								</blockquote>
							</div>
							<?php
						} ?>

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
							<fieldset style="padding-bottom: 0px;padding-top: 15px;">
								<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
								<div class="row">
									<div class="large-3 columns">
										<?= $this->Form->input('Search.academicyear', array('label' => 'Admission Year: ', 'style' => 'width:90%', 'empty' => 'All Admission Year', 'options' => $acyear_array_data)); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search.program_id', array('label' => 'Program: ', 'style' => 'width:90%', 'empty' => 'All Programs', 'options' => $programs)); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search.program_type_id', array('label' => 'Program Type: ', 'style' => 'width:90%;', 'empty' => 'All Program Types', 'options' => $programTypes)); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Search.gender', array('label' => 'Sex', 'style' => 'width:90%;', 'type' => 'select', 'empty' => 'All', 'options' => array('female' => 'Female', 'male' => 'Male'))); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-6 columns">
										<?php
										if (isset($colleges) && !empty($colleges)) {
											echo $this->Form->input('Search.college_id', array('label' => 'College: ', 'style' => 'width:95%;', 'empty' => 'All Assigned Colleges'));
										} else if (isset($departments) && !empty($departments)) {
											echo $this->Form->input('Search.department_id', array('label' => 'Department: ', 'style' => 'width:90%;', 'empty' => 'All Assigned Departments'));
										} ?>
									</div>
									<div class="large-2 columns">
										<?= $this->Form->input('Search.status', array('label' => 'Status: ', 'empty' => 'All',  'options' => array('0' => 'Not Graduated', '1' => 'Graduated'), 'default' => 0, 'type' => 'select', 'style' => 'width:90%;')); ?>
									</div>
									<div class="large-2 columns">
										<?= $this->Form->input('Search.name', array('label' => 'Student Name or ID:', 'placeholder' => 'Name or ID ..', 'default' => $name, 'style' => 'width:90%;')); ?>
									</div>
									<div class="large-2 columns">
										<?= $this->Form->input('Search.limit', array('id' => 'limit ', 'value' => (isset($this->data['Search']['limit']) && !empty($this->data['Search']['limit']) ? $this->data['Search']['limit'] : $limit), 'type' => 'number', 'min' => '0',  'max' => '5000', 'step' => '100',  'label' => 'Limit: ', 'style' => 'width:90%;')); ?>

										<?= (isset($this->data['Search']['page']) ? $this->Form->hidden('page', array('value' => $this->data['Search']['page'])) : ''); ?>
										<?= (isset($this->data['Search']['sort']) ? $this->Form->hidden('sort', array('value' => $this->data['Search']['sort'])) : ''); ?>
										<?= (isset($this->data['Search']['direction']) ? $this->Form->hidden('direction', array('value' => $this->data['Search']['direction'])) : ''); ?>
									</div>
								</div>

								<?php
								if (isset($departments) && !empty($departments) && $this->Session->read('Auth.User')['role_id'] != ROLE_STUDENT && $this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR && $this->Session->read('Auth.User')['role_id'] != ROLE_COLLEGE && $this->Session->read('Auth.User')['role_id'] != ROLE_DEPARTMENT) { ?>
									<div class="row">
										<div class="large-6 columns">
											<?= $this->Form->input('Search.department_id', array('label' => 'Department: ', 'style' => 'width:90%;', 'empty' => 'All Departments')); ?>
										</div>
										<div class="large-6 columns">
										</div>
									</div>
									<?php
								} ?>
							
								<hr>
								<div class="large-12 columns">
									<div onclick="toggleViewFullId('ListStudents')">
										<?php
										if (!empty($students)) {
											echo $this->Html->image('plus2.gif', array('id' => 'ListStudentsImg')); ?>
											<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListStudentsTxt"> Adjust Fields</span>
											<?php
										} else {
											echo $this->Html->image('minus2.gif', array('id' => 'ListStudentsImg')); ?>
											<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListStudentsTxt"> Hide Fields</span>
											<?php
										} ?>
									</div>
								</div>
								<div class="large-12 columns" id="ListStudents"
									style="display:<?= (!empty($students) ? 'none' : 'display'); ?>">
									<div class="row">
										<div class="large-12 columns">
											&nbsp;
										</div>
									</div>
									<div class="row">
										<div class="large-2 columns">
											<?= $this->Form->input('Display.full_name', array('label' => 'Full Name', 'type' => 'checkbox', 'checked' => true)); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.full_am_name', array('label' => 'Amharic Name', 'type' => 'checkbox')); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.gender', array('label' => 'Sex', 'type' => 'checkbox', 'checked' => true)); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.studentnumber', array('label' => 'Student ID', 'type' => 'checkbox', 'checked' => true)); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.academicyear', array('label' => 'Admission Year', 'type' => 'checkbox')); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.program_id', array('label' => 'Program', 'type' => 'checkbox', 'checked' => true)); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.program_type_id', array('label' => 'Program Type', 'type' => 'checkbox', 'checked' => true)); ?>
										</div>
									</div>
									<div class="row">
										<div class="large-2 columns">
											<?= $this->Form->input('Display.college_id', array('label' => 'College', 'type' => 'checkbox')); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.curriculum_id', array('label' => 'Specilization', 'type' => 'checkbox')); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.birthdate', array('label' => 'Birthdate', 'type' => 'checkbox')); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.is_disable', array('label' => 'Disabled', 'type' => 'checkbox')); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.city_id', array('label' => 'City', 'type' => 'checkbox')); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.region_id', array('label' => 'Region', 'type' => 'checkbox')); ?>
										</div>
									</div>
									<div class="row">
										<div class="large-2 columns">
											<?= $this->Form->input('Display.department_id', array('label' => 'Department', 'type' => 'checkbox', 'checked' => (isset($this->data['Display']['department_id']) && !empty($this->data['Display']['department_id']) ? true : false))); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.zone_id', array('label' => 'Zone', 'type' => 'checkbox')); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.woreda_id', array('label' => 'Woreda', 'type' => 'checkbox')); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.email', array('label' => 'Email', 'type' => 'checkbox')); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.phone_mobile', array('label' => 'Phone', 'type' => 'checkbox')); ?>
										</div>
										<div class="large-2 columns">
											<?= $this->Form->input('Display.student_national_id', array('label' => 'National ID', 'type' => 'checkbox')); ?>
										</div>
									</div>
									<div class="row">
										<div class="large-12 columns">
											&nbsp;
										</div>
									</div>
								</div>
								<br>
								<hr>
								<?= $this->Form->Submit('Search', array('class' => 'tiny radius button bg-blue', 'div' => false)); ?>
							</fieldset>
						</div>
						<hr>
					</div>
					<?php
				} ?>
			</div>
		</div>
	</div>

	<div class="box-body">
		<div class="dataTables_wrapper">
			<?php
			if (!empty($students)) { ?>
				<!-- <hr>
					<?php //echo $this->Html->link($this->Html->image("/img/pdf_icon.gif", array("alt" => "Print To Pdf")) . ' Export to PDF', array('action' => 'print_record', 'label' => 'Export PDF'), array('escape' => false)); ?>
				<hr> -->

				<div style="overflow-x:auto;">
					<!-- <table id="studentTableIndex" class="display responsive" style="width:100%" cellpadding="0" cellspacing="0"> -->
					<table id="studentTableIndex" cellpadding="0" cellspacing="0" class="table">
						<thead>
							<tr>
								<?php
								echo '<td class="center">#</td>';
								if (isset($this->request->data['Display']) && !empty($this->request->data['Display']) && $this->Session->read('display_field_student')) {
									foreach ($this->request->data['Display'] as $dk => $dv) {
										if ($dv == 1) {
											echo $dk == 'full_name' ? '<td class="vcenter">' : '<td class="center">';
											if ($dk == 'gender') {
												echo $this->Paginator->sort($dk, 'Sex') . '</td>';
											} else if ($dk == 'department_id') {
												echo $this->Paginator->sort($dk, 'Department') . '</td>';
											} else if ($dk == 'academicyear') {
												echo $this->Paginator->sort($dk, 'Admission Year'). '</td>';
											} else if ($dk == 'studentnumber') {
												echo $this->Paginator->sort($dk, 'Student ID'). '</td>';
											} else if ($dk == 'student_national_id') {
												echo $this->Paginator->sort($dk, 'National ID'). '</td>';
											} else {
												echo $this->Paginator->sort($dk) . '</td>';
											}
										}
									}
								} else { ?>
									<td class="vcenter"><?= $this->Paginator->sort('full_name'); ?></td>
									<td class="center"><?= $this->Paginator->sort('gender', 'Sex'); ?></td>
									<td class="center"><?= $this->Paginator->sort('studentnumber'); ?></td>
									<td class="center"><?= $this->Paginator->sort('academicyear', 'Admission Year'); ?></td>
									<td class="center"><?= $this->Paginator->sort('Program'); ?></td>
									<td class="center"><?= $this->Paginator->sort('Program Type'); ?></td>
									<td class="center"><?= $this->Paginator->sort('Department'); ?></td>
									<?php
								} ?>
								<td class="center">Actions</td>
							</tr>
						</thead>
						<tbody>
							<?php

							$start = $this->Paginator->counter('%start%');

							foreach ($students as $student) { ?>
								<tr>
									<td class="center">
										<?= $start++; ?>
									</td>
									<?php
									if (isset($this->request->data['Display']) && !empty($this->request->data['Display']) && !empty($this->Session->read('display_field_student'))) {
										foreach ($this->request->data['Display'] as $dk => $dv) {
											if ($dv == 1) {
												if ($dk == 'full_name') {
													echo '<td class="vcenter">' . $student['Student']['full_name'] . '</td>';
												} else if ($dk == 'program_type_id') {
													echo '<td class="center">' . $student['ProgramType']['name'] . '</td>';
												} else if ($dk == 'gender') {
													echo '<td class="center">' . (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : $student['Student']['gender'])) . '</td>';
												} else if ($dk == 'program_id') {
													echo '<td class="center">' . $student['Program']['name'] . '</td>';
												} else if ($dk == 'college_id') {
													echo '<td class="center">' . $student['College']['name'] . '</td>';
												} else if ($dk == 'department_id') {
													echo '<td class="center">' . (!empty($student['Department']['name']) ? $student['Department']['name'] : ($student['Student']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial Program': 'Pre/Freshman')) . '</td>';
												} else if ($dk == 'region_id') {
													echo '<td class="center">' . $student['Region']['name'] . '</td>';
												} else if ($dk == 'zone_id') {
													echo '<td class="center">' . $student['Zone']['name'] . '</td>';
												} else if ($dk == 'woreda_id') {
													echo '<td class="center">' . $student['Woreda']['name'] . '</td>';
												} else if ($dk == 'city_id') {
													echo '<td class="center">' . $student['City']['name'] . '</td>';
												} else if ($dk == 'specialization_id') {
													echo '<td class="center">' . $student['Specialization']['name'] . '</td>';
												} else if ($dk == 'birthdate') {
													echo '<td class="center">' . (isset($student['Student']['birthdate']) && !empty($student['Student']['birthdate']) ? $this->Time->format("M j, Y", $student['Student']['birthdate'], NULL, NULL) : ''). '</td>';
												} else if ($dk == 'curriculum_id') {
													echo '<td class="center">' . $student['Curriculum']['english_degree_nomenclature'] . '</td>';
												} else {
													echo '<td class="center">' . $student['Student'][$dk] . '</td>';
												}
											}
										}
									} else { ?>
										<td class="vcenter"><?= $student['Student']['full_name']; ?></td>
										<td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : $student['Student']['gender'])); ?></td>
										<td class="center"><?= $student['Student']['studentnumber']; ?></td>
										<td class="center"><?= $student['Student']['academicyear']; ?></td>
										<td class="center"><?= $student['Program']['name']; ?></td>
										<td class="center"><?= $student['ProgramType']['name']; ?></td>
										<td class="center"><?= (!empty($student['Department']['name']) ? $student['Department']['name'] : ($student['Student']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Pre/Freshman')); ?></td>
										<?php
									} ?>
									<td class="center">
										<?= ($role_id != ROLE_STUDENT ? $this->Html->link('', '#', array('class' => 'jsview fontello-eye', 'title' => 'View', 'data-animation' => "fade", 'data-reveal-id' => 'myModal', 'data-reveal-ajax' => "/students/get_modal_box/" . $student['Student']['id'])) : ''); ?> &nbsp;
										<?= ($role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read('Auth.User')['Role']['parent_id'] ? $this->Html->link(__(''), array('action' => 'edit', $student['Student']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit Profile')) : ''); ?>
									</td>
								</tr>
								<?php
							} ?>
						</tbody>
					</table>
				</div>
				<br>

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
			} ?>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>

<script>
	function toggleViewFullId(id) {
		if ($('#' + id).css("display") == 'none') {
			$('#' + id + 'Img').attr("src", '/img/minus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append(' Hide Fields');
		} else {
			$('#' + id + 'Img').attr("src", '/img/plus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append(' Adjust Fields');
		}
		$('#' + id).toggle("slow");
	}
</script>