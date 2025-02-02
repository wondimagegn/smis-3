<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Generate Student  ID Number') ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('AcceptedStudent', array('action' => 'generate', 'data-abide', 'onSubmit' => 'return checkForm(this);')); ?>
				<?php
				if (!isset($show_list_generated)) { ?>
					<div class="smallheading"></div>
					<h6 class="text-gray">Table: Summary of students those haven\'t student identification</h6>
					<br>
					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<tbody>
								<tr>
									<?php
									$college_count = count($colleges);
									$count_program = count($programs);
									$count_program_type = count($programTypes);

									debug($count_program_type);
									debug($college_count);

									for ($i = 1; $i <= $college_count; $i++) { ?>
										<td style="width: 50%;">
											<table cellpadding="0" cellspacing="0" class="table">
												<thead>
													<tr>
														<td class="vcenter" colspan=<?= $count_program + 1; ?>><h6 class="text-gray fs14"><?= $colleges[$i]; ?></h6></th>
													</tr>
													<tr>
														<td class="vcenter"> Program/Type </th>
														<?php
														foreach ($programs as $kp => $vp) { ?>
															<td  class="center"><?= (strcasecmp(trim($vp), 'Undergraduate') == 0 || strcasecmp(trim($vp), 'Under graduate') == 0  ? 'UG' : (strcasecmp(trim($vp), 'Postgraduate') == 0 || strcasecmp(trim($vp), 'Post graduate') == 0 ? 'PG' : $vp ))?></th>
															<?php
														} ?>
													</tr>
												</thead>
												<tbody>
													<?php
													for ($j = 1; $j <= $count_program_type; $j++) {
														if (isset($programTypes[$j])) { ?>
															<tr>
																<td class="vcenter"><?= $programTypes[$j]; ?></td>
																<?php
																for ($k = 1; $k <= $count_program; $k++) {
																	if (isset($programs[$k])) { ?>
																		<td class="center"><?= ($data[$colleges[$i]][$programs[$k]][$programTypes[$j]] != 0 ? '<b>'. $data[$colleges[$i]][$programs[$k]][$programTypes[$j]] .'</b>' : '--'); ?></td>
																	<?php
																	}
																} ?>
															</tr>
															<?php
														}
													} ?>
												</tbody>
											</table>
										</td>
										<?php
										if (($i % 2) == 0) {
											echo '<tr></tr>';
										}
									} ?>
								</tr>
							</tbody>
						</table>
					</div>
					<br>
					<?php
				} ?>

				<?php
				if (!isset($show_list_generated)) { ?>
					<div>
						<fieldset style="padding-bottom: 10px;padding-top: 5px;">
							<legend>&nbsp;&nbsp; Search Filter &nbsp;&nbsp;</legend>
							<div class="row">
								<div class="large-4 columns">
									<?= $this->Form->input('AcceptedStudent.academicyear', array('id' => 'academicyear', 'style' => 'width:90%;', 'label' => 'Academic Year: ', 'type' => 'select', 'options' => $acyear_array_data, /* 'empty' => "[ Select Academic Year ]", */ 'default' => isset($selectedsacdemicyear) ? $selectedsacdemicyear : '' )); ?>
								</div>
								<div class="large-4 columns">
									<?= $this->Form->input('AcceptedStudent.program_id', array('style' => 'width:90%;', 'label' => 'Program: '/* , 'empty' => "[ Select Program ]" */)); ?>
								</div>
								<div class="large-4 columns">
									<?= $this->Form->input('AcceptedStudent.program_type_id', array('style' => 'width:90%;', 'label' => 'Program Type: ', /* 'empty' => "[ Select Program Type ]" */)); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-4 columns">
									<?= $this->Form->input('AcceptedStudent.college_id', array('style' => 'width:90%;', 'label' => 'College: ', 'empty' => "[ Select College ]")); ?>
								</div>
								<div class="large-4 columns">
									<?= $this->Form->input('AcceptedStudent.limit', array('style' => 'width:90%;', 'label' => 'Limit: ','type' => 'number', 'min' => '100',  'max' => '2000', 'value' => $limit, 'step' => '100')); ?>
								</div>
							</div>
						</fieldset>

						<?= $this->Form->submit('Search', array('name' => 'search', 'div' => 'false', 'class' => 'tiny radius button bg-blue')); ?> 
						<br>
					</div>
					<?php
				} ?>

				<?php
				if (!empty($acceptedStudents)) { ?>
					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<td class="center"><?= $this->Form->checkbox(null, array('id' => 'select-all', 'checked' => '')); ?> </td>
									<td class="center">#</td>
									<td class="vcenter"><?= $this->Paginator->sort('full_name', 'Full Name'); ?></td>
									<td class="center"><?= $this->Paginator->sort("sex", "Sex"); ?></td>
									<td class="center"><?= $this->Paginator->sort("studentnumber", "Student ID"); ?></td>
									<td class="center"><?= $this->Paginator->sort("EHEECE_total_results", "EHEECE Result"); ?></td>
									<td class="center"><?= $this->Paginator->sort('department_id', 'Department'); ?></td>
									<td class="center"><?= $this->Paginator->sort('program_type_id', 'Program Type'); ?></td>
									<td class="center"><?= $this->Paginator->sort('academicyear', 'ACY'); ?></td>
									<td class="center"><?= $this->Paginator->sort('Placement_Approved_By_Department', "Department Approval"); ?></td>
									<td class="center"><?= $this->Paginator->sort('placementtype', 'Placement Type'); ?></td>
								</tr>
							</thead>
							<tbody>
								<?php
								$start = $this->Paginator->counter('%start%');

								foreach ($acceptedStudents as $acceptedStudent) { ?>
									<tr>
										<td class="center"><?= $this->Form->checkbox('AcceptedStudent.generate.' . $acceptedStudent['AcceptedStudent']['id'], array('class' => 'checkbox1')); ?></td>
										<td class="center"><?= $start++; ?></td>
										<td class="vcenter"><?= $acceptedStudent['AcceptedStudent']['full_name']; ?></td>
										<td class="center"><?= (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'male') == 0 ? 'M' : (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'female') == 0 ? 'F' : '')); ?></td>
										<td class="center"><?= $acceptedStudent['AcceptedStudent']['studentnumber']; ?></td>
										<td class="center"><?= (int) $acceptedStudent['AcceptedStudent']['EHEECE_total_results']; ?></td>
										<td class="center"><?= $this->Html->link($acceptedStudent['Department']['name'], array('controller' => 'departments', 'action' => 'view', $acceptedStudent['Department']['id'])); ?></td>
										<td class="center"><?= $this->Html->link($acceptedStudent['ProgramType']['name'], array('controller' => 'program_types', 'action' => 'view', $acceptedStudent['ProgramType']['id'])); ?></td>
										<td class="center"><?= $acceptedStudent['AcceptedStudent']['academicyear']; ?></td>
										<td class="center"><?= (isset($acceptedStudent['AcceptedStudent']['Placement_Approved_By_Department']) && $acceptedStudent['AcceptedStudent']['Placement_Approved_By_Department'] == 1 ? '<span class="accepted">Yes</span>' : ''); ?></td>
										<td class="center"><?= $acceptedStudent['AcceptedStudent']['placementtype']; ?></td>
									</tr>
									<?php
								} ?>
							</tbody>
						</table>
					</div>
					<hr>

					<?= $this->Form->Submit('Generate ID', array('name' => 'generateid', 'id' => 'generateID', 'div' => 'false', 'class' => 'tiny radius button bg-blue')); ?>

					<div class="row">
						<div class="large-5 columns">
							<?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total'))); ?>
						</div>
						<div class="large-7 columns">
							<div class="pagination-centered">
								<ul class="pagination">
									<?= $this->Paginator->prev('<< ' . __(''), array('tag' => 'li'), null, array('class' => 'arrow unavailable '));
									echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li'));
									echo $this->Paginator->next(__('') . ' >>', array('tag' => 'li'), null, array('class' => 'arrow unavailable'));
									?>
								</ul>
							</div>
						</div>
					</div>
					<?php
				} else if (empty($acceptedStudents) && !($isbeforesearch)) { ?>
					<div class='info-box info-message'><span style='margin-right: 15px;'></span> No Accepted students without student identification in these selected criteria</div>
					<?php
				} ?>

				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script>

	var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Generating IDs, please wait a moment...");
			form.generateID.disabled = true;
			return false;
		}

		form.generateID.value = 'Generating IDs...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>