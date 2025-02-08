<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="courseRegistrations form">
<?php echo $this->Form->create('CourseRegistration');?>
	<fieldset>
		<legend><?php echo __('Edit Course Registration'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('year_level_id');
		echo $this->Form->input('registration_date');
		echo $this->Form->input('academic_year');
		echo $this->Form->input('student_id');
		echo $this->Form->input('course_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('CourseRegistration.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('CourseRegistration.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Course Registrations'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Year Levels'), array('controller' => 'year_levels', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Year Level'), array('controller' => 'year_levels', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Courses'), array('controller' => 'courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Course'), array('controller' => 'courses', 'action' => 'add')); ?> </li>
	</ul>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
