<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Department Study Programs'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->Create('DepartmentStudyProgram'); ?>
				<div style="margin-top: -30px;">
                    <fieldset style="padding-bottom: 5px;padding-top: 5px;">
                        <legend>&nbsp;&nbsp; Search / Filter &nbsp;&nbsp;</legend>
                        <div class="row">
							<div class="large-6 columns">
								<?php
								if (!empty($department_name) && $this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) { ?>
									<h6 class='fs13 text-gray'>Department: <?= $department_name; ?></h6>
									<?php
								} else {
									echo $this->Form->input('department_id', array('label' => 'Department: ', 'style' => 'width:90%', 'empty' => '[ Select Department ]', 'onchange' => 'getStudyProgram(1)', 'id' => 'department_id'));
								} ?>
                            </div>
							<div class="large-6 columns">
							<?= $this->Form->input('academic_year', array('label' => 'From Academic Year: ', 'options' => $academic_year, 'style' => 'width:42%', 'empty' => '[ Select Academic Year ]')); ?>
							</div>
                        </div>
						<div class="row">
							<div class="large-3 columns">
								<?= $this->Form->input('study_program_id', array('id' => 'study_program_id_1', 'label' => 'Study Program: ', 'style' => 'width:90%', 'empty' => '[ Select Study Program ]')); ?>
							</div>
                            <div class="large-3 columns">
								<?= $this->Form->input('qualification_id', array('label' => 'Qualification: ', 'style' => 'width:90%', 'empty' => '[ Select Qualification ]')); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('program_modality_id', array('label' => 'Program Modality: ', 'style' => 'width:90%', 'empty' => '[ Select Program Modality ]')); ?>
							</div>
							<div class="large-3 columns">
								<br><?= $this->Form->input('apply_for_current_students', array('label' => 'Applied for current Students', 'type' =>  'checkbox' , 'checked' => (isset($apply_for_current_students) && $apply_for_current_students ? 'checked' :  false))); ?>
							</div>
						</div>
                    </fieldset>
					<hr>
					<?= $this->Form->submit(__('Search'), array('name' => 'search', 'id' => 'search', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
					<?= $this->Form->end(); ?>
					<hr>
                </div>

                <?php
                //debug($departmentStudyPrograms);
                if (!empty($departmentStudyPrograms)) { ?>
					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<th class="center">#</th>
									<th class="vcenter">Department</th>
									<th class="vcenter">Study Program</th>
									<th class="center">Code</th>
									<th class="center">Modality</th>
									<th class="center">Qualification</th>
									<th class="center">ACY</th>
									<th class="center">For Current</th>
									<th class="center"><?= __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$count = $this->Paginator->counter('%start%');
								foreach ($departmentStudyPrograms as $departmentStudyProgram) { ?>
									<tr>
										<td class="center"><?= $count++; ?></td>
										<td class="vcenter"><?= $departmentStudyProgram['Department']['name']; ?></td>
										<td class="vcenter"><?= $departmentStudyProgram['StudyProgram']['study_program_name']; ?></td>
										<td class="center"><?= $departmentStudyProgram['StudyProgram']['code']; ?></td>
										<td class="center"><?= $departmentStudyProgram['ProgramModality']['code']; ?></td>
										<td class="center"><?= $departmentStudyProgram['Qualification']['code']; ?></td>
										<td class="center"><?= $departmentStudyProgram['DepartmentStudyProgram']['academic_year']; ?></td>
										<td class="center"><?= (isset($departmentStudyProgram['DepartmentStudyProgram']['apply_for_current_students']) && $departmentStudyProgram['DepartmentStudyProgram']['apply_for_current_students'] == 1 ? 'Yes' : 'No'); ?></td>
										<td class="center">
											<?= $this->Html->link(__(''), array('action' => 'view', $departmentStudyProgram['DepartmentStudyProgram']['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?> &nbsp;
											<?php
											if (($this->Session->read('Auth.User')['Role']['id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) || $this->Session->read('Auth.User')['Role']['id'] ==ROLE_SYSADMIN ) {  ?>
												<?= $this->Html->link(__(''), array('action' => 'edit', $departmentStudyProgram['DepartmentStudyProgram']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?> &nbsp;
												<?= $this->Html->link(__(''), array('action' => 'delete', $departmentStudyProgram['DepartmentStudyProgram']['id']), array('class' => 'fontello-trash', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete %s study program from %s department?'), $departmentStudyProgram['StudyProgram']['study_program_name'], $departmentStudyProgram['Department']['name'])); ?>
												<?php
											} ?>
										</td>
									</tr>
									<?php 
								} ?>
							</tbody>
						</table>
					</div>
					<br>

					<p><?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'))); ?> </p>

					<div class="paging">
						<?= $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class' => 'disabled')); ?> | <?= $this->Paginator->numbers(); ?> | <?= $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled')); ?>
					</div>

					<?php
                } else { ?>
                    <div class='info-box info-message'><span style='margin-right: 15px;'></span>No Department Study Program(s) found. try using changing search filters.</div>
                	<?php
                } ?>
			</div>
		</div>
	</div>
</div>

<script type='text/javascript'>
	function getStudyProgram(id) {
		//serialize form data
		var formData = $("#department_id").val();
		$("#study_program_id_" + id).empty();
		//$("#study_program_id_" + id).append('<option style="width:100px">loading...</option>');
		$("#study_program_id_" + id).attr('disabled', true);
		//get form action
		var formUrl = '/departmentStudyPrograms/get_selected_department_department_study_programs/' + formData;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data, textStatus, xhr) {
				$("#study_program_id_" + id).attr('disabled', false);
				$("#study_program_id_" + id).empty();
				//$("#study_program_id_" + id).append('<option style="width:100px"></option>');
				$("#study_program_id_" + id).append(data);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}
</script>