<div class="instructorEvalutionQuestions view">
<h2><?php echo __('Instructor Evalution Question'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($instructorEvalutionQuestion['InstructorEvalutionQuestion']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Question In English'); ?></dt>
		<dd>
			<?php echo h($instructorEvalutionQuestion['InstructorEvalutionQuestion']['question']); ?>
			&nbsp;
		</dd>
		
		<dt><?php echo __('Question In Amharic'); ?></dt>
		<dd>
			<?php echo h($instructorEvalutionQuestion['InstructorEvalutionQuestion']['question_amharic']); ?>
			&nbsp;
		</dd>
		
		
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo h($instructorEvalutionQuestion['InstructorEvalutionQuestion']['type']); ?>
			&nbsp;
		</dd>

		<dt><?php echo __('For'); ?></dt>
		<dd>
			<?php echo h($instructorEvalutionQuestion['InstructorEvalutionQuestion']['for']); ?>
			&nbsp;
		</dd>
		
		<dt><?php echo __('Active'); ?></dt>
		<dd>
			<?php echo $instructorEvalutionQuestion['InstructorEvalutionQuestion']['active']==1 ? 'Yes':'No'; ?>
			&nbsp;
		</dd>

	</dl>
</div>
