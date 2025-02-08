<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('List Graduation Works'); ?></span>
		</div>
	</div>
    <div class="box-body">
    	<div class="row">
	  		<div class="large-12 columns">

	  			<div style="margin-top: -30px;"><hr></div>

				<div class="graduationWorks index">
					<?= $this->Form->Create('GraduationWork'); ?>
					
					<fieldset style="padding-bottom: 0px;padding-top: 15px;">
						
						<div class="row">
							<div class="large-6 columns">	
								<?= $this->Form->input('Search.department_id', array('empty'=>'All Departments', 'id'=>'department_id_1','onchange'=>'updateSection(1)',  'class' => 'fs13', 'label' => 'Department: ', 'type' => 'select', 'style' => 'width:90%')); ?>
							</div>
							<div class="large-6 columns">	
								<?= $this->Form->input('Search.section_id', array('empty'=>'Any Section', 'id'=>'section_id_1', 'class' => 'fs13', 'label' => 'Section: ', 'type' => 'select', 'style' => 'width:90%')); ?>
							</div>
						</div>
						<div class="row">
							<div class="large-3 columns">
								<?= $this->Form->input('Search.name', array('id' => 'StudentName', 'class' => 'fs13', 'label' => 'Student Name.', 'maxlength' => 50, 'style' => 'width:90%')); ?> 
							</div>
							<div class="large-3 columns">	
								<?= $this->Form->input('limit', array('id' => 'limit ', 'type' => 'number', 'min' => '100',  'max' => '50000', 'value' => (!empty($selectedLimit) ? $selectedLimit : ''), 'step' => '100', 'class' => 'fs13', 'label' =>'Limit: ', 'style' => 'width:45%')); ?>

								<?= (isset($this->data['Search']['page']) ? $this->Form->hidden('page', array('value' => $this->data['Search']['page'])) : ''); ?>
								<?= (isset($this->data['Search']['sort']) ? $this->Form->hidden('sort', array('value' => $this->data['Search']['sort'])) : ''); ?>
								<?= (isset($this->data['Search']['direction']) ? $this->Form->hidden('direction', array('value' => $this->data['Search']['direction'])) : ''); ?>
							</div>
							<div class="large-6 columns">
								&nbsp;
							</div>
						</div>
						<hr>
						<?= $this->Form->submit(__('View Graduation Works'), array('name' => 'viewGraduationWorks', 'div' => false, 'class'=>'tiny radius button bg-blue')); ?>
					</fieldset>

					<div id="dialog-modal" title="Academic Profile "></div>

					<?php 
					if (!empty($graduationWorks)) { ?> 
						<!-- <h6 class="fs15 text-gray"><?= __('Graduation Works');?></h6> -->
						<hr>
						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
										<th class="center" style="width: 3%;">#</th>
										<th class="vcenter" style="width: 20%;"><?= $this->Paginator->sort('student_id');?></th>
										<th class="center"><?= $this->Paginator->sort('type');?></th>
										<th class="vcenter"><?= $this->Paginator->sort('title');?></th>
										<th class="center"><?= $this->Paginator->sort('course_id');?></th>
										<th class="center"><?= $this->Paginator->sort('created');?></th>
										<th class="center"><?= $this->Paginator->sort('modified');?></th>
										<th class="center"><?= __('Actions');?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i = 0;
									$start = $this->Paginator->counter('%start%');
									foreach ($graduationWorks as $graduationWork ) { ?>
										<tr>
											<td class="center"><?= $start++; ?></td>
											<td class='vcenter jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?= $graduationWork['Student']['id'];?>">
												<?= $graduationWork['Student']['full_name']; ?>
											</td>
											<td class="center"><?= $graduationWork['GraduationWork']['type']; ?></td>
											<td class="vcenter"><?= $graduationWork['GraduationWork']['title']; ?></td>
											<td class="center"><?= $this->Html->link($graduationWork['Course']['course_title'].''.$graduationWork['Course']['course_code'], array('controller' => 'courses', 'action' => 'view', $graduationWork['Course']['id'])); ?></td>
											<td class="center"><?= $this->Time->format("M j, Y h:i A", $graduationWork['GraduationWork']['created'], NULL, NULL); ?></td>
											<td class="center"><?= $this->Time->format("M j, Y h:i A", $graduationWork['GraduationWork']['modified'], NULL, NULL); ?></td>
											<td  class="center">
												<?= $this->Html->link(__('Delete'), array('action' => 'delete', $graduationWork['GraduationWork']['id']), null, sprintf(__('Are you sure you want to delete  %s?'), $graduationWork['GraduationWork']['title'])); ?>
											</td>
										</tr>
										<?php
									} ?>
								</tbody>
							</table>
						</div>

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
	</div>
</div>

<script type='text/javascript'>
	function updateSection(id) {
		//serialize form data
		var formData = $("#department_id_"+id).val();
		$("#section_id_"+id).attr('disabled', true);
		$("#department_id_"+id).attr('disabled',true);	
		//get form action
		var formUrl = '/sections/get_sections_by_dept/'+formData;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: formData,
			success: function(data,textStatus,xhr){
				$("#section_id_"+id).attr('disabled', false);
				$("#department_id_"+id).attr('disabled',false);	
				$("#section_id_"+id).empty();
				$("#section_id_"+id).append(data);
			},
			error: function(xhr,textStatus,error){
				alert(textStatus);
			}
		});
		return false;
	}
</script>