<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('List Exit Exam Results'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->Create('ExitExam', array('action'=> 'search')); ?>
				<div style="margin-top: -20px;">
					<fieldset style="padding-bottom: 5px;padding-top: 5px;">
						<legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
						<div class="row">
							<div class="large-6 columns">
								<?= $this->Form->input('Search.department_id', array('empty' => '[ Select Department ]', 'id' => 'department_id_1', 'onchange' => 'updateSection(1)', 'label' => 'Department:', 'style' => 'width:90%;')); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('Search.section_id', array('empty' => '[ Select Section ]', 'id' => 'section_id_1', 'label' => 'Section: ', 'style' => 'width:90%;')); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('Search.name_or_id', array('label' => 'Student Name/ ID No: ', 'type' => 'text', 'placeholder' => 'Student Name or ID..',  'style' => 'width:90%;')); ?>
							</div>
						</div>
						<div class="row">
							<div class="large-3 columns">
                                <?= $this->Form->input('Search.program_id', array('label' => 'Program: ', 'empty' => "All", 'style' => 'width:90%;')); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('Search.program_type_id', array('label' => 'Program Type: ', 'empty' => "All", 'style' => 'width:90%;')); ?>
                            </div>
							<div class="large-3 columns">
                                <?= $this->Form->input('Search.exam_date', array('label' => 'Exam Date: ', 'empty' => "All", 'style' => 'width:90%;', 'options' => $exam_date)); ?>
                            </div>
							<div class="large-3 column">
								<?= $this->Form->input('Search.limit', array('id' => 'limit ', 'type' => 'number', 'min' => '100',  'max' => '1000', 'value' => $limit, 'step' => '100', 'label' => ' Limit: ', 'style' => 'width:85%;')); ?>
							</div>
						</div>
						<hr>
						<?= $this->Form->hidden('Search.page', array('value' => $page)); ?>
						<?= $this->Form->submit(__('View Exit Exam Result'), array('name' => 'viewExitExams', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
					</fieldset>

					<div id="dialog-modal" title="Academic Profile "></div>

					<?php
					if (!empty($exitExams)) { ?>
						<hr>

						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<td class="center">#</td>
										<td class="vcenter"><?= $this->Paginator->sort('student_id', 'Student Name'); ?></td>
										<td class="center"><?= $this->Paginator->sort('Student.studentnumber', 'Student ID'); ?></td>
										<td class="center"><?= $this->Paginator->sort('Student.department_id', 'Department'); ?></td>
										<!-- <td class="center"><?php //echo $this->Paginator->sort('type', 'Exam Type'); ?></td> -->
										<!-- <td class="center"><?php //echo $this->Paginator->sort('course_id'); ?></td> -->
										<td class="center"><?= $this->Paginator->sort('result'); ?></td>
										<td class="center"><?= $this->Paginator->sort('exam_date'); ?></td>
										<td class="center"><?= __('Actions'); ?></td>
									</tr>
								</thead>
								<tbody>
									<?php
									$start = $this->Paginator->counter('%start%');
									foreach ($exitExams as $exitExam) { ?>
										<tr>
											<td class="center"><?= $start++; ?></td>
											<td class="vcenter"><?= $exitExam['Student']['full_name']; ?></td>
											<td class='jsView center' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?= $exitExam['Student']['id']; ?>"><?= $exitExam['Student']['studentnumber']; ?></td>
											<td class="center"><?= $exitExam['Student']['Department']['name']; ?></td>
											<!-- <td class="center"><?php //echo $exitExam['ExitExam']['type']; ?></td> -->
											<!-- <td class="center"><?php //echo $this->Html->link($exitExam['Course']['course_title'] . '' . $exitExam['Course']['course_code'], array('controller' => 'courses', 'action' => 'view', $exitExam['Course']['id'])); ?></td> -->
											<td class="center"><?= $exitExam['ExitExam']['result']; ?></td>
											<td class="center"><?= (!empty($exitExam['ExitExam']['exam_date']) ? $this->Time->format("M j, Y", $exitExam['ExitExam']['exam_date'], NULL, NULL) : 'N/A');  ?></td>
											<td class="center">
												<?= $this->Html->link(__(''), array('action' => 'view', $exitExam['ExitExam']['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?> &nbsp;
												<?php //echo $this->Html->link(__(''), array('action' => 'edit', $exitExam['ExitExam']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?> &nbsp;
												<?php //echo $this->Html->link(__(''), array('action' => 'delete', $exitExam['ExitExam']['id']), array('class' => 'fontello-trash', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete exam result of  %s for %s Exam Date'), $exitExam['Student']['full_name'], $exitExam['ExitExam']['exam_date'] )); ?>
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
							<div class="large-7 columns">
								<div style="padding-left: 5%;">
									<?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total'))); ?>
								</div>
							</div>
							<div class="large-5 columns right">
								<div class="paging">
									<?= $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class' => 'disabled')); ?> | <?= $this->Paginator->numbers(); ?> | <?= $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled')); ?>
								</div>
							</div>
						</div>
						<hr>
						<?php
					} ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type='text/javascript'>
	function updateSection(id) {
		var formData = $("#department_id_" + id).val();
		$("#section_id_" + id).attr('disabled', true);
		$("#department_id_" + id).attr('disabled', true);
		//get form action
		var formUrl = '/sections/get_sections_by_dept/' + formData;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data, textStatus, xhr) {
				$("#section_id_" + id).attr('disabled', false);
				$("#department_id_" + id).attr('disabled', false);
				$("#section_id_" + id).empty();
				$("#section_id_" + id).append(data);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}
</script>