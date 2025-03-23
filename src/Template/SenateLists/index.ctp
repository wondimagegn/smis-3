<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-search" style="font-size: larger; font-weight: bold;"></i> 
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('View Senate List'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns" style="margin-top: -30px;">
				<?= $this->Form->create('SenateList', array('action' => 'search')); ?>
				<hr>
				<fieldset style="padding-bottom: 0px;padding-top: 15px;">
					<!-- <legend>&nbsp;&nbsp; Search &nbsp;&nbsp;</legend> -->
					<div class="row">
						<div class="large-3 columns">
							<?= $this->Form->input('Search.program_id', array('id' => 'Program', 'class' => 'fs13', 'label' => 'Program:', 'type' => 'select', 'options' => $programs, 'style' => 'width:90%;')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('Search.program_type_id', array('id' => 'ProgramType', 'class' => 'fs13', 'label' => ' Program Type: ', 'type' => 'select', 'options' => $program_types, 'style' => 'width:90%;')); ?>
						</div>
						<div class="large-6 columns">
							<?= $this->Form->input('Search.department_id', array('id' => 'Department', 'class' => 'fs13', 'label' => 'College/Department: ', 'type' => 'select', 'options' => $departments, 'style' => 'width:90%;')); ?>
						</div>
					</div>
					<div class="row">
						<div class="large-6 columns">
							<?= $this->Form->input('Search.senate_date_from', array('label' => 'Senate From: ', 'type' => 'date', 'minYear' => $senate_date_from, 'maxYear' => $senate_date_to, 'default' => false, 'style' => 'width:25%;')); ?>
						</div>
						<div class="large-6 columns">
							<?= $this->Form->input('Search.senate_date_to', array('label' => 'Senate Approval To: ', 'type' => 'date', 'minYear' => $senate_date_from, 'maxYear' => $senate_date_to, 'default' => date('Y-m-d'), 'style' => 'width:25%;')); ?>
						</div>
					</div>
					<div class="row">
						<div class="large-3 columns">
							<?= $this->Form->input('Search.minute_number', array('id' => 'MinuteNumber', 'class' => 'fs13', 'label' => 'Minute No.', 'value' => $minute_number, 'style' => 'width:90%;')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('Search.limit', array('id' => 'Limit', 'class' => 'fs13', 'type' => 'number', 'min' => '100', 'max' => '5000', 'step' => '100', 'style' => 'width:50%;', 'value' => $limit, 'label' => ' Limit: ')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('Search.sort_by', array('options' => array('Student.full_name~ASC' => 'Full name A-Z', 'Student.full_name~DESC' => 'Full name Z-A', 'SenateList.created~DESC' => 'Recent First', 'SenateList.created~ASC' => 'Recent Last'), 'label' => 'Sort By:', 'style' => 'width:90%;')); ?>
						</div>
						<div class="large-3 columns">
							<br> <br>
							<?= $this->Form->input('Search.exclude_major', array('id' => 'exclude_major', 'type' => 'checkbox', 'class' => 'fs13', 'default' => $excludeMajor, 'label' => 'Exclude Major', 'div' => false)); ?>
							<?php //echo $this->Form->hidden('Search.page', array('value' => $page)); ?>

							<?= (isset($this->data['Search']['page']) ? $this->Form->hidden('page', array('value' => $this->data['Search']['page'])) : ''); ?>
							<?= (isset($this->data['Search']['sort']) ? $this->Form->hidden('sort', array('value' => $this->data['Search']['sort'])) : ''); ?>
							<?= (isset($this->data['Search']['direction']) ? $this->Form->hidden('direction', array('value' => $this->data['Search']['direction'])) : ''); ?>
						</div>
					</div>
					<hr>
					<?= $this->Form->submit(__('List Students', true), array('name' => 'listStudentsForSenateList', 'class' => 'tiny radius button bg-blue', 'div' => true)); ?>
				</fieldset>

				<?php

				if (!empty($senateLists)) {
					
					if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) { ?>
						<hr>
						<div class="row">
							<div class="large-7 columns">
								<?php // $this->Form->submit(__('Generate PDF', true), array('name' => 'viewPDF', 'class' => 'tiny radius button bg-blue', 'div' => true)); ?>
								<?=  $this->Html->link($this->Html->image("/img/pdf_icon.gif", array("alt" => "Print To Pdf")) . '  &nbsp;  &nbsp; Generate PDF', array('action' => 'generate_pdf'), array('escape' => false)); ?>
							</div>
							<div class="large-5 columns">
								<br><strong style="color:red">Credit Taken in Red: </strong><strong> Requires Status Generation</strong><br>
							</div>
						</div>
						<hr>
						<?php
					} ?>

					<div id="dialog-modal" title="Academic Profile "></div>

					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<td style="width:3%" class="center">&nbsp;</th>
									<td style="width:3%" class="center">#</td>
									<td style="width:18%" class="vcenter"><?= $this->Paginator->sort('Student.first_name', 'Student Name'); ?></td>
									<td style="width:9%" class="center"><?= $this->Paginator->sort('student_id', 'Student ID'); ?></td>
									<td style="width:5%" class="center"><?= $this->Paginator->sort('Student.gender', 'Sex'); ?></td>
									<td style="width:15%" class="center"><?= $this->Paginator->sort('minute_number'); ?></td>
									<td style="width:14%" class="center"><?= $this->Paginator->sort('approved_date'); ?></td>
									<td style="width:9%" class="center"><?= $this->Paginator->sort('credit_hour_sum', 'Credit Taken'); ?></td>
									<td style="width:7%" class="center"><?= $this->Paginator->sort('cgpa', 'CGPA'); ?></td>
									<td class="center">National ID</td>
									<?php
									if ((strcasecmp($this->request->data['Search']['exclude_major'], '0') == 0) || $excludeMajor == 0) { ?>
										<td style="width:7%" class="center"><?= $this->Paginator->sort('mcgpa', 'MCGPA'); ?></td>
										<?php
									} ?>
									<td class="center">Action</td>
								</tr>
							</thead>
							<tbody>
								<?php
								//$count = 1;
								$count = $this->Paginator->counter('%start%');

								foreach ($senateLists as $senateList) {

									$valid_deletion_time =  date('Y-m-d H:i:s', mktime(
                                        substr($senateList['SenateList']['created'], 11, 2),
                                        substr($senateList['SenateList']['created'], 14, 2),
                                        substr($senateList['SenateList']['created'], 17, 2),
                                        substr($senateList['SenateList']['created'], 5, 2),
                                        substr($senateList['SenateList']['created'], 8, 2) + Configure::read('Calendar.daysAvaiableForGraduateDeletion'),
                                        substr($senateList['SenateList']['created'], 0, 4)
                                    ));
									
									$credit_hour_sum = 0;
									$st_credit_hour_sum = 0;
									$not_used_gpa_sum = 0;
									$dropped_credit_sum = 0;

									foreach ($senateList['Student']['StudentExamStatus'] as $ses_key => $ses_value) {
										$st_credit_hour_sum += $ses_value['credit_hour_sum'];
									}

									if (isset($senateList['Student']['CourseDrop']) && !empty($senateList['Student']['CourseDrop'])) {
										foreach ($senateList['Student']['CourseDrop'] as $drop_key => $drop_value) {
											if (isset($drop_value['CourseRegistration']['PublishedCourse']['Course']) && !empty($drop_value['CourseRegistration']['PublishedCourse']['Course'])) {
												if ($drop_value['CourseRegistration']['PublishedCourse']['Course']) {
													if ($drop_value['registrar_confirmation'] == 1 && $drop_value['department_approval'] == 1) {
														$dropped_credit_sum += $drop_value['CourseRegistration']['PublishedCourse']['Course']['credit'];
													}
												}
											}
										}
									}

									if (isset($senateList['Student']['CourseAdd']) && !empty($senateList['Student']['CourseAdd'])) {
										foreach ($senateList['Student']['CourseAdd'] as $ses_key => $ses_value) {
											if ($ses_value['PublishedCourse']['Course']['GradeType']['used_in_gpa'] == false) {
												$not_used_gpa_sum += $ses_value['PublishedCourse']['Course']['credit'];
											}
											$credit_hour_sum += $ses_value['PublishedCourse']['Course']['credit'];
										}
									}

									if (isset($senateList['Student']['CourseRegistration']) && !empty($senateList['Student']['CourseRegistration'])) {
										foreach ($senateList['Student']['CourseRegistration'] as $ses_key => $ses_value) {
											if ($ses_value['PublishedCourse']['Course']['GradeType']['used_in_gpa'] == false) {
												$not_used_gpa_sum += $ses_value['PublishedCourse']['Course']['credit'];
											}
											$credit_hour_sum += $ses_value['PublishedCourse']['Course']['credit'];
										}
									}

									$label = '';

									if ((($credit_hour_sum - $dropped_credit_sum) != ($st_credit_hour_sum + $not_used_gpa_sum))) {
										$label = "color:red;";
									} ?>

									<tr>
										<td onclick="toggleView(this)" id="<?= $count; ?>" class="center"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $count, 'div' => false, 'align' => 'left')); ?></td>
										<td class="center"><?= $count; ?></td>
										<td class="vcenter"><?= $this->Html->link($senateList['Student']['full_name'], '#', array('class' => 'jsview', 'data-animation' => "fade", 'data-reveal-id' => 'myModal', 'data-reveal-ajax' => "/students/get_modal_box/" . $senateList['Student']['id'])); ?></td>
										<td class="center"><?= $senateList['Student']['studentnumber']; ?></td>
										<td class="center"><?= (strcasecmp($senateList['Student']['gender'], 'male') == 0 ? 'M' : (strcasecmp($senateList['Student']['gender'], 'female') == 0 ? 'F' : $senateList['Student']['gender'])); ?></td>
										<td class="center"><?= $senateList['SenateList']['minute_number']; ?></td>
										<td class="center"><?= $this->Format->humanize_date_short_extended($senateList['SenateList']['approved_date']); ?></td>
										<td style="<?= $label; ?>" class="center">
											<?php
											if (($credit_hour_sum - $dropped_credit_sum) > $senateList['Student']['Curriculum']['minimum_credit_points']) {
												echo $senateList['Student']['Curriculum']['minimum_credit_points'];
											} else {
												echo $credit_hour_sum - $dropped_credit_sum;
											} ?>
										</td>
										<td class="center"><?= $senateList['Student']['StudentExamStatus'][0]['cgpa']; ?></td>
										<?php
										if ((strcasecmp($this->request->data['Search']['exclude_major'], '0') == 0) || $excludeMajor ==  0) { ?>
											<td class="center"><?= $senateList['Student']['StudentExamStatus'][0]['mcgpa']; ?></td>
											<?php
										} ?>
										<td class="center"><?= (!empty($senateList['Student']['student_national_id']) ? $senateList['Student']['student_national_id'] : '--'); ?></td>
										<td class="center">
											<?php
                                            if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) {
                                                if ($valid_deletion_time > date('Y-m-d')) {
													echo $this->Html->link(__('Delete', true), array('action' => 'delete', $senateList['SenateList']['id']), null, sprintf(__('Are you sure you want to delete %s (%s) from the senate list?', $senateList['Student']['full_name'], $senateList['Student']['studentnumber']))); 
												} else  {
													echo '---';
												}
											} ?>	
										</td>
									</tr>
									<tr id="c<?= $count++; ?>" style="display:none">
										<td colspan="2" style="background-color: white;"></td>
										<td colspan="9" style="background-color: white;">
											<table cellpadding="0" cellspacing="0" class="table">
												<tbody>
													<tr>
														<td class="vcenter"><span class="text-gray">Curriculum Name: </span>&nbsp; <b><?= $senateList['Student']['Curriculum']['name']; ?></b></td>
													</tr>
													<tr>
														<td class="vcenter"><span class="text-gray">Degree Designation: </span>&nbsp; <b><?= $senateList['Student']['Curriculum']['english_degree_nomenclature']; ?></b></td>
														<?php
														if (!empty($senateList['Student']['Curriculum']['specialization_english_degree_nomenclature'])) { ?>
															<tr>
																<td class="vcenter"><span class="text-gray">Specialization: </span>&nbsp; <b><?= $senateList['Student']['Curriculum']['specialization_english_degree_nomenclature']; ?></b></td>
															</tr>
															<?php
														} ?>
													<tr>
														<td class="vcenter"><span class="text-gray">Degree Designation (Amharic): </span>&nbsp; <b><?= $senateList['Student']['Curriculum']['amharic_degree_nomenclature']; ?></b></td>
													</tr>
													<?php
													if (!empty($senateList['Student']['Curriculum']['specialization_amharic_degree_nomenclature'])) { ?>
														<tr>
															<td class="vcenter"><span class="text-gray">Specialization (Amharic): </span>&nbsp; <b><?= $senateList['Student']['Curriculum']['specialization_amharic_degree_nomenclature']; ?></b></td>
														</tr>
														<?php
													} ?>
													<tr>
														<td class="vcenter"><span class="text-gray">Credit Type: </span>&nbsp; <b><?= (count(explode('ECTS', $senateList['Student']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit'); ?></b></td>
													</tr>
													<tr>
														<td class="vcenter"><span class="text-gray">Required <?= (count(explode('ECTS', $senateList['Student']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit'); ?> for Graduation: </span>&nbsp; <b><?= $senateList['Student']['Curriculum']['minimum_credit_points']; ?></b></td>
													</tr>
													<tr>
														<td class="vcenter"><span class="text-gray">Department: </span>&nbsp; <b><?= $senateList['Student']['Department']['name']; ?></b></td>
													</tr>
													<tr>
														<td class="vcenter"><span class="text-gray">Program: </span>&nbsp; <b><?= $senateList['Student']['Program']['name']; ?></b></td>
													</tr>
													<tr>
														<td class="vcenter"><span class="text-gray">Program Type: </span>&nbsp; <b><?= $senateList['Student']['ProgramType']['name']; ?></b></td>
													</tr>
												</tbody>
											</table>
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
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script>
	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none') {
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		} else {
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		}
		$('#c' + obj.id).toggle("slow");
	}
</script>