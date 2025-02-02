<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-params" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('General Settings'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?php //debug(ClassRegistry::init('GeneralSetting')->getAllGeneralSettingsByStudentByProgramIdOrBySectionID(null, null, null, 18000)); ?>
				<?php debug(ClassRegistry::init('GeneralSetting')->getAllGeneralSettingsByStudentByProgramIdOrBySectionID(258088)); ?>
				<div style="overflow-x:auto;">
					<table cellpadding="0" cellspacing="0" class="table">
						<thead>
							<tr>
								<td class="vcenter"><?= $this->Paginator->sort('program_id'); ?></td>
								<td class="center"><?= $this->Paginator->sort('program_type_id'); ?></td>
								<td class="center"><?= $this->Paginator->sort('minimumCreditForStatus', 'Min. Credit for Status'); ?></td>
								<td class="center"><?= $this->Paginator->sort('maximumCreditPerSemester', 'Max. Credit per Semester'); ?></td>
								<td class="center"><?= $this->Paginator->sort('daysAvaiableForGradeChange', 'Grade Change'); ?></td>
								<td class="center"><?= $this->Paginator->sort('daysAvaiableForNgToF', 'NG To F'); ?></td>
								<td class="center"><?= $this->Paginator->sort('onlyAllowCourseAddForFailedGrades', 'Allow Course Add'); ?></td>
								<td class="center"><?= $this->Paginator->sort('allowCourseAddFromHigherYearLevelSections', 'Allow Course Add From'); ?></td>
								<!-- <td class="center"><?php //echo $this->Paginator->sort('daysAvaiableForDoToF', 'Do To F'); ?></td>
								<td class="center"><?php //echo $this->Paginator->sort('daysAvailableForFxToF', 'Fx To F'); ?></th>
								<td class="center"><?php //echo $this->Paginator->sort('allowMealWithoutCostsharing', 'Allow Meal Without Cost Sharing'); ?></td>
								<td class="center"><?php //echo $this->Paginator->sort('notifyStudentsGradeByEmail', 'Notify Grade for Students By Email'); ?></td>
								<td class="center"><?php //echo  $this->Paginator->sort('allowStudentsGradeViewWithouInstructorsEvalution', 'Grade view without Instructor Evaluation'); ?></td> -->
								<td class="center"><?= __('Actions'); ?></td>
							</tr>
						</thead>
						<tbody>
							<?php 
							foreach ($generalSettings as $generalSetting) { ?>
								<tr>
									<td class="vcenter">
										<?php
										foreach ($generalSetting['GeneralSetting']['program_id'] as $key => $value) {
											echo $value . '<br/>';
										} ?>
									</td>
									<td class="center">
										<?php
										foreach ($generalSetting['GeneralSetting']['program_type_id'] as $key => $value) {
											echo $value . '<br/>';
										} ?>
									</td>
									<td class="center"><?= h($generalSetting['GeneralSetting']['minimumCreditForStatus']); ?></td>
									<td class="center"><?= h($generalSetting['GeneralSetting']['maximumCreditPerSemester']); ?></td>
									<td class="center"><?= h($generalSetting['GeneralSetting']['daysAvaiableForGradeChange']); ?></td>
									<td class="center"><?= h($generalSetting['GeneralSetting']['daysAvaiableForNgToF']); ?></td>
									<td class="center"><?= h($generalSetting['GeneralSetting']['onlyAllowCourseAddForFailedGrades'] == 1 ? 'Failed Only' : 'Any Course'); ?></td>
									<td class="center"><?= h($generalSetting['GeneralSetting']['allowCourseAddFromHigherYearLevelSections'] == 1 ? 'Any Year Level' : 'Current Year Level & Below'); ?></td>
									<!-- <td class="center"><?php //echo h($generalSetting['GeneralSetting']['daysAvaiableForDoToF']); ?></td>
									<td class="center"><?php //echo h($generalSetting['GeneralSetting']['daysAvailableForFxToF']); ?></td>
									<td class="center"><?php //echo h($generalSetting['GeneralSetting']['allowMealWithoutCostsharing'] == 1 ? 'Yes' : 'No'); ?></td>
									<td class="center"><?php //echo h($generalSetting['GeneralSetting']['allowStudentsGradeViewWithouInstructorsEvalution'] == 1 ? 'Yes' : 'No'); ?></td>
									<td class="center"><?php //echo  h($generalSetting['GeneralSetting']['allowRegistrationWithoutPayment'] == 1 ? 'Yes' : 'No'); ?></td> -->
									<td class="center">
										<?= $this->Html->link(__(''), array('action' => 'view', $generalSetting['GeneralSetting']['id']), array('class' => 'fontello-eye', 'title' => 'view')); ?> &nbsp;
										<?= $this->Html->link(__(''), array('action' => 'edit', $generalSetting['GeneralSetting']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?> 
										<?= $this->Html->link(__(''), array('action' => 'delete', $generalSetting['GeneralSetting']['id']), array('class' => 'fontello-trash', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete this setting(%s)?'), $generalSetting['GeneralSetting']['id'])); ?>
									</td>
								</tr>
								<?php 
							} ?>
						</tbody>
					</table>
				</div>
				<br>

				<p><?= $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total') )); ?></p>

				<div class="paging">
					<?= $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled')); ?> | <?= $this->Paginator->numbers(array('separator' => '')); ?> | <?=  $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled')); ?>
				</div>
			</div>
		</div>
	</div>
</div>