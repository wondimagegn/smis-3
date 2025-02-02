<div class="examSplitSections form">
<?php echo $this->Form->create('ExamSplitSection');?>
	<fieldset>
 		<legend><?php echo __('Add Exam Split Section'); ?></legend>
	<?php
		echo $this->Form->input('section_split_for_exam_id');
		echo $this->Form->input('section_name');
		echo $this->Form->input('Student');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Exam Split Sections'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Section Split For Exams'), array('controller' => 'section_split_for_exams', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Section Split For Exam'), array('controller' => 'section_split_for_exams', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
	</ul>
</div>