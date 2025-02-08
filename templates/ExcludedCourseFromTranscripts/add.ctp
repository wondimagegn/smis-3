<div class="excludedCourseFromTranscripts form">
<?php echo $this->Form->create('ExcludedCourseFromTranscript');?>
	<fieldset>
		<legend><?php echo __('Add Excluded Course From Transcript'); ?></legend>
	<?php
		echo $this->Form->input('course_registration_id');
		echo $this->Form->input('course_exemption_id');
		echo $this->Form->input('minute_number');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Excluded Course From Transcripts'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Course Registrations'), array('controller' => 'course_registrations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Course Registration'), array('controller' => 'course_registrations', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Course Exemptions'), array('controller' => 'course_exemptions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Course Exemption'), array('controller' => 'course_exemptions', 'action' => 'add')); ?> </li>
	</ul>
</div>