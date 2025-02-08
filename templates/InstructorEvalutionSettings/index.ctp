<div class="instructorEvalutionSettings index">
	<h2><?= __('Instructor Evalution Settings'); ?></h2>
	<table cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th><?= $this->Paginator->sort('id'); ?></th>
				<th><?= $this->Paginator->sort('academic_year'); ?></th>
				<th><?= $this->Paginator->sort('head_percent'); ?></th>
				<th><?= $this->Paginator->sort('colleague_percent'); ?></th>
				<th><?= $this->Paginator->sort('student_percent'); ?></th>
				<th><?= $this->Paginator->sort('created'); ?></th>
				<th><?= $this->Paginator->sort('modified'); ?></th>
				<th class="actions"><?= __('Actions'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php 
			foreach ($instructorEvalutionSettings as $instructorEvalutionSetting){ ?>
				<tr>
					<td><?= __($instructorEvalutionSetting['InstructorEvalutionSetting']['id']); ?></td>
					<td><?= __($instructorEvalutionSetting['InstructorEvalutionSetting']['academic_year']); ?></td>
					<td><?= __($instructorEvalutionSetting['InstructorEvalutionSetting']['head_percent']); ?></td>
					<td><?= __($instructorEvalutionSetting['InstructorEvalutionSetting']['colleague_percent']); ?></td>
					<td><?= __($instructorEvalutionSetting['InstructorEvalutionSetting']['student_percent']); ?></td>
					<td><?= __($instructorEvalutionSetting['InstructorEvalutionSetting']['created']); ?></td>
					<td><?= __($instructorEvalutionSetting['InstructorEvalutionSetting']['modified']); ?></td>
					<td class="actions">
						<?= $this->Html->link(__('View'), array('action' => 'view', $instructorEvalutionSetting['InstructorEvalutionSetting']['id'])); ?>
						<?= $this->Html->link(__('Edit'), array('action' => 'edit', $instructorEvalutionSetting['InstructorEvalutionSetting']['id'])); ?>
						<?= $this->Form->postLink(__('Delete'), array('action' => 'delete', $instructorEvalutionSetting['InstructorEvalutionSetting']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $instructorEvalutionSetting['InstructorEvalutionSetting']['id']))); ?>
					</td>
				</tr>
				<?php 
			} ?>
		</tbody>
	</table>
	<p>
		<?= $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'))); ?>
	</p>
	<div class="paging">
		<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
		?>
	</div>
</div>
<div class="actions">
	<h3><?= __('Actions'); ?></h3>
	<ul>
		<li><?= $this->Html->link(__('New Instructor Evalution Setting'), array('action' => 'add')); ?></li>
	</ul>
</div>