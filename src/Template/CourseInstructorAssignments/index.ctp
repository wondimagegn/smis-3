<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Course Instructor Assignments'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				
				<?= $this->Form->create('CourseInstructorAssignment', array('action'=> 'search')); ?>

				<div style="margin-top: -30px;">
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
						<fieldset style="padding-bottom: 0px;padding-top: 5px;">
							<legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('Search.academicyear', array('label' => 'Academic Year: ', 'style' => 'width:90%', 'options' => $acyear_array_data, 'empty' => "All", 'default' => (isset($selected_academic_year) ? $selected_academic_year : ''))); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Search.semester', array('options' => Configure::read('semesters'), 'label' => 'Semester: ', 'style' => 'width:90%;', 'empty' => 'All', 'required' => false)); ?>
								</div>
								<div class="large-6 columns">
									<?php
									if (isset($departments) && !empty($departments)) {
										if ($this->Session->read('Auth.User')['role_id'] != ROLE_DEPARTMENT) {
											echo $this->Form->input('Search.department_id', array('label' => 'Department: ', 'empty' => 'All', 'style' => 'width:90%'));
										} else {
											echo $this->Form->input('Search.department_id', array('label' => 'Department: ', 'style' => 'width:90%'));
										}
									} ?>
								</div>
							</div>
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('Search.instructor_name', array('label' => 'Instructor Name: ', 'placeholder' => 'Type name to filter:', 'style' => 'width:90%;')); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Search.course_name', array('label' => 'Course Title: ', 'placeholder' => 'Type Course Title to filter:', 'style' => 'width:90%;')); ?>
								</div>
								<div class="large-3 columns">
									<?php echo $this->Form->input('Search.limit', array('id' => 'limit ', 'type' => 'number', 'min' => '20',  'max' => '500', 'value' => (isset($this->data['Search']['limit']) ? $this->data['Search']['limit'] : ''), 'step' => 'any', 'label' => 'Limit: ', 'style' => 'width:40%;')); ?>
									
									<?= (isset($this->data['Search']['page']) ? $this->Form->hidden('page', array('value' => $this->data['Search']['page'])) : ''); ?>
									<?= (isset($this->data['Search']['sort']) ? $this->Form->hidden('sort', array('value' => $this->data['Search']['sort'])) : ''); ?>
									<?= (isset($this->data['Search']['direction']) ? $this->Form->hidden('direction', array('value' => $this->data['Search']['direction'])) : ''); ?>

								</div>
								<div class="large-3 columns">
									&nbsp;
								</div>
							</div>
							<hr>

							<?= $this->Form->Submit('Search', array('name' => 'search', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
						</fieldset>

						<?php //echo $this->Form->end(); ?>

					</div>
				</div>
				<hr>

				<?php

				if (isset($courseInstructorAssignments) && !empty($courseInstructorAssignments)) { ?>
					<div style="overflow-x:auto;">
						<table cellpadding="0" cellspacing="0" class="table">
							<thead>
								<tr>
									<th class="center">#</th>
									<th class="vcenter"><?= $this->Paginator->sort('course', 'Course Title'); ?></th>
									<th class="center"><?= $this->Paginator->sort('credit', 'Cr'); ?></th>
									<!-- <th class="center"><?php //echo $this->Paginator->sort('course_detail', 'LTL'); ?></th> -->
									<th class="center"><?= $this->Paginator->sort('academic_year', 'ACY'); ?></th>
									<th class="center"><?= $this->Paginator->sort('semester', 'SEM'); ?></th>
									<th class="center"><?= $this->Paginator->sort('instructor', 'Assigned Instructor'); ?></th>
									<th class="center"><?= $this->Paginator->sort('position', 'Position'); ?></th>
									<th class="center"><?= $this->Paginator->sort('type', 'Assigned For'); ?></th>
									<th class="center"><?= $this->Paginator->sort('section_id', 'Assigned Section'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$start = $this->Paginator->counter('%start%');
								foreach ($courseInstructorAssignments as $courseInstructorAssignment) { ?>
									<tr>
										<td class="center"><?= $start++; ?></td>
										<td class="vcenter"><?= $courseInstructorAssignment['PublishedCourse']['Course']['course_code_title']; ?></td>
										<td class="center"><?= $courseInstructorAssignment['PublishedCourse']['Course']['credit']; ?></td>
										<!-- <td class="center"><?php //echo $courseInstructorAssignment['PublishedCourse']['Course']['course_detail_hours']; ?></td> -->
										<td class="center"><?= $courseInstructorAssignment['CourseInstructorAssignment']['academic_year']; ?></td>
										<td class="center"><?= $courseInstructorAssignment['CourseInstructorAssignment']['semester']; ?></td>
										<td class="center"><?= (isset($courseInstructorAssignment['Staff']) && !empty($courseInstructorAssignment['Staff'] ['full_name']) ? (isset($courseInstructorAssignment['Staff']['Title']['title']) && !empty($courseInstructorAssignment['Staff']['Title']['title']) ? $courseInstructorAssignment['Staff']['Title']['title'] . '. ' : '') . $courseInstructorAssignment['Staff']['full_name'] . '<br><i class="text-gray">' . $courseInstructorAssignment['Staff']['Department']['name'] . '</i>'  : ''); ?></td>
										<td class="center"><?= (isset($courseInstructorAssignment['Staff']) && !empty($courseInstructorAssignment['Staff']['Position']['position']) ? $courseInstructorAssignment['Staff']['Position']['position'] : ''); ?></td>
										<td class="center"><?= (isset($courseInstructorAssignment['Staff']) && !empty($courseInstructorAssignment['CourseInstructorAssignment']['type']) ? $courseInstructorAssignment['CourseInstructorAssignment']['type'] : ''); ?></td>
										<td class="center"><?= (empty($courseInstructorAssignment['CourseSplitSection']['section_name']) ? $courseInstructorAssignment['Section']['name'] : (isset($courseInstructorAssignment['CourseSplitSection']['section_name']) ? $courseInstructorAssignment['CourseSplitSection']['section_name'] : '')); ?></td>
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
				}  else { ?>
					<!-- <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No Course Instructor Assignamet is found with the given search criteria.</div> -->
					<?php
				} ?>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none')
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		else
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		$('#c' + obj.id).toggle("slow");
	}

	function toggleViewFullId(id) {
		if ($('#' + id).css("display") == 'none') {
			$('#' + id + 'Img').attr("src", '/img/minus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append('Hide Filter');
		} else {
			$('#' + id + 'Img').attr("src", '/img/plus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append('Display Filter');
		}
		$('#' + id).toggle("slow");
	}
</script>