<div class="surveyQuestions view">
<h2><?php echo __('Survey Question'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($surveyQuestion['SurveyQuestion']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Question English'); ?></dt>
		<dd>
			<?php echo h($surveyQuestion['SurveyQuestion']['question_english']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Question Amharic'); ?></dt>
		<dd>
			<?php echo h($surveyQuestion['SurveyQuestion']['question_amharic']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($surveyQuestion['SurveyQuestion']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($surveyQuestion['SurveyQuestion']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>

<div class="related">
	<h3><?php echo __('Related Survey Question Answers'); ?></h3>
	<?php if (!empty($surveyQuestion['SurveyQuestionAnswer'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Survey Question Id'); ?></th>
		<th><?php echo __('Answer English'); ?></th>
		<th><?php echo __('Answer Amharic'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($surveyQuestion['SurveyQuestionAnswer'] as $surveyQuestionAnswer): ?>
		<tr>
			<td><?php echo $surveyQuestionAnswer['id']; ?></td>
			<td><?php echo $surveyQuestionAnswer['survey_question_id']; ?></td>
			<td><?php echo $surveyQuestionAnswer['answer_english']; ?></td>
			<td><?php echo $surveyQuestionAnswer['answer_amharic']; ?></td>
			<td><?php echo $surveyQuestionAnswer['created']; ?></td>
			<td><?php echo $surveyQuestionAnswer['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'survey_question_answers', 'action' => 'view', $surveyQuestionAnswer['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'survey_question_answers', 'action' => 'edit', $surveyQuestionAnswer['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'survey_question_answers', 'action' => 'delete', $surveyQuestionAnswer['id']), array('confirm' => __('Are you sure you want to delete # %s?', $surveyQuestionAnswer['id']))); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Survey Question Answer'), array('controller' => 'survey_question_answers', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
