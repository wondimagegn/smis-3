<div class="surveyQuestions index">
	<h2><?php echo __('Survey Questions'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('question_english'); ?></th>
			<th><?php echo $this->Paginator->sort('question_amharic'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($surveyQuestions as $surveyQuestion): ?>
	<tr>
		<td><?php echo h($surveyQuestion['SurveyQuestion']['id']); ?>&nbsp;</td>
		<td><?php echo h($surveyQuestion['SurveyQuestion']['question_english']); ?>&nbsp;</td>
		<td><?php echo h($surveyQuestion['SurveyQuestion']['question_amharic']); ?>&nbsp;</td>
		<td><?php echo h($surveyQuestion['SurveyQuestion']['created']); ?>&nbsp;</td>
		<td><?php echo h($surveyQuestion['SurveyQuestion']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $surveyQuestion['SurveyQuestion']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $surveyQuestion['SurveyQuestion']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $surveyQuestion['SurveyQuestion']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $surveyQuestion['SurveyQuestion']['id']))); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
		'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
