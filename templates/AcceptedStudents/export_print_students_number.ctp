<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-vcard" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Export Student IDs') ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
			
				<?= $this->Form->create('AcceptedStudent', array('action' => 'export_print_students_number')); ?>
				<div style="margin-top: -20px;">
					<?php
					if (!isset($acceptedStudents) || $this->Session->read('search_data')) { ?>
						<hr>
						<div onclick="toggleViewFullId('ListPublishedCourse')">
							<?php
							if (!empty($acceptedStudents) || $this->Session->check('search_data')) {
								echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
								<?php
							} else {
								echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
								<?php
							} ?>
						</div>

						<div id="ListPublishedCourse" style="display:<?= (isset($acceptedStudents) || $this->Session->check('search_data') ? 'none' : 'display'); ?>">
                            <fieldset style="padding-bottom: 10px;padding-top: 5px;">
                                <legend>&nbsp;&nbsp; Search Filter &nbsp;&nbsp;</legend>
                                <div class="row">
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('AcceptedStudent.academicyear', array('id' => 'academicyear', 'style' => 'width:90%;', 'label' => 'Academic Year: ', 'type' => 'select', 'options' => $acyear_array_data, 'empty' => "[ Select Academic Year ]", 'default' => isset($selectedsacdemicyear) ? $selectedsacdemicyear : '')); ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('AcceptedStudent.program_id', array('style' => 'width:90%;', 'label' => 'Program: '/* , 'empty' => "[ Select Program ]" */)); ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('AcceptedStudent.program_type_id', array('style' => 'width:90%;', 'label' => 'Program Type: ', /* 'empty' => "[ Select Program Type ]" */)); ?>
                                    </div>
									<div class="large-3 columns">
										<?= $this->Form->input('AcceptedStudent.region_id', array('style' => 'width:90%;', 'label' => 'Region: ', 'empty' => "[ All Regions ]")); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="large-6 columns">
                                        <?php
                                        if (!empty($colleges)) {
                                            echo $this->Form->input('AcceptedStudent.college_id', array('style' => 'width:90%;', 'label' => 'College: ', 'empty' => "[ Select College ]"));
                                        } else if (!empty($departments)) {
                                            echo $this->Form->input('AcceptedStudent.department_id', array('style' => 'width:90%;', 'label' => 'Department: ', 'empty' => "[ Select Department ]"));
                                        } ?>
                                    </div>
									<div class="large-3 columns">
										<?= $this->Form->input('AcceptedStudent.admitted', array('label' => 'Admitted: ', 'style' => 'width:90%', 'options' => array('0' => 'All', '1' => 'No', '2' => 'Yes'), 'default' => '2')); ?>
									</div>
									<div class="large-3 columns">
                                        <?= $this->Form->input('AcceptedStudent.limit', array('style' => 'width:90%;', 'label' => 'Limit: ','type' => 'number', 'min' => '100',  'max' => '10000', 'value' => $limit, 'step' => '100')); ?>
                                    </div>
                                </div>
                            </fieldset>

                            <?= $this->Form->submit('Search', array('name' => 'search', 'div' => 'false', 'class' => 'tiny radius button bg-blue')); ?>
                            <br>
                        </div>

						<?php
					}

					if (!empty($acceptedStudents)) { ?>

						<hr>

						<div style="overflow-x:auto;">
							<table cellpadding="0" cellspacing="0" class="table">
								<thead>
									<tr>
                                        <td colspan="4" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);">
                                            <br style="line-height: 0.5;">
                                            <span style="font-size:16px;font-weight:bold; margin-top: 25px;"> College: <?= $selected_college_name; ?></span>
                                            <br style="line-height: 0.35;">
                                            <span class="text-gray" style="padding-top: 15px; font-size: 13px; font-weight: normal"> 
												Campus:	<?= $selected_campus_name; ?><br>
												Admission Year:	<?= $selectedsacdemicyear; ?><br>
                                                Program:<?= $selected_program_name . ' / '. $selected_program_type_name; ?><br>
                                            </span>
                                        </td>
                                        <td colspan="3" style="text-align: right; vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);">
											<?= $this->Html->link($this->Html->image("/img/pdf_icon.gif", array("alt" => "Print To Pdf")) . ' PDF', array('action' => 'print_students_number_pdf'), array('escape' => false)); ?>  &nbsp;&nbsp;
                                            <?= $this->Html->link($this->Html->image("/img/xls-icon.gif", array("alt" => "Export TO Excel")) . ' Excel', array('action' => 'export_students_number_xls'), array('escape' => false)); ?>&nbsp;&nbsp;
											<?= $this->Html->link($this->Html->image("/img/csv_icon.png", array("alt" => "Export TO CSV")) . ' CSV', array('action' => 'download_csv'), array('escape' => false)); ?>&nbsp;&nbsp;
                                        </td>
                                    </tr>
									<tr>
										<td class="center">#</td>
										<td class="vcenter"><?= $this->Paginator->sort('full_name', 'Full Name'); ?></td>
										<td class="center"><?= $this->Paginator->sort('sex', "Sex"); ?></td>
										<td class="center"><?= $this->Paginator->sort('studentnumber', "Student ID"); ?></td>
										<td class="center"><?= $this->Paginator->sort('department_id', "Department"); ?></td>
										<td class="center"><?= $this->Paginator->sort('region_id', "Region"); ?></td>
										<td class="center">National ID</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = $this->Paginator->counter('%start%');
									foreach ($acceptedStudents as $acceptedStudent) { ?>
										<tr>
											<td class="center"><?= $count++; ?></td>
											<td class="vcenter"><?= $acceptedStudent['AcceptedStudent']['full_name']; ?></td>
											<td class="center"><?= (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'male') == 0 ? 'M' : (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'female') == 0 ? 'F' : '')); ?></td>
											<td class="center"><?= $acceptedStudent['AcceptedStudent']['studentnumber']; ?></td>
											<td class="center"><?= (isset($acceptedStudent['Department']) && !is_null($acceptedStudent['Department']['name']) ? $acceptedStudent['Department']['name'] : 'Pre/Freshman'); ?></td>
											<td class="center"><?= $acceptedStudent['Region']['name']; ?></td>
											<td class="center"><?= (isset($acceptedStudent['Student']) && !empty($acceptedStudent['Student']['student_national_id']) ? $acceptedStudent['Student']['student_national_id'] : ''); ?></td>
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
						<div class='info-box info-message'> <span style='margin-right: 15px;'></span> No Accepted student found that is <?= (isset($this->request->data['AcceptedStudent']['admitted']) && $this->request->data['AcceptedStudent']['admitted'] == 2 ? ' admitted ' : (isset($this->request->data['AcceptedStudent']['admitted']) && $this->request->data['AcceptedStudent']['admitted'] == 1 ? ' not admitted ' : ' admitted or not admitted')) ; ?> and have student identification with the given criteria. If you have students in these criteria which are <?= (isset($this->request->data['AcceptedStudent']['admitted']) && $this->request->data['AcceptedStudent']['admitted'] == 2 ? ' admitted, ' : (isset($this->request->data['AcceptedStudent']['admitted']) && $this->request->data['AcceptedStudent']['admitted'] == 1 ? ' not admitted, Admit the students first or ' : ' admitted or not admitted,')) ; ?> change Admitted Field to "All" or Generate Student IDs.</div>
						<?php 
					} ?>
				</div>
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