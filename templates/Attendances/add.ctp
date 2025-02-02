<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="attendances form">
<?php echo $this->Form->create('Attendance');?>
	<fieldset>
		<legend><?php echo __('Add Attendance'); ?></legend>
	<?php
		echo $this->Form->input('student_id');
		echo $this->Form->input('published_course_id');
		echo $this->Form->input('attendace_type');
		echo $this->Form->input('attendance_date');
		echo $this->Form->input('attendance');
		echo $this->Form->input('remark');
	?>
	</fieldset>
<?php echo $this->Form->end(array('class'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Attendances'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
	</ul>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
