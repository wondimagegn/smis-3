<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="courseSchedules form">
<?php echo $this->Form->create('CourseSchedule');?>
	<fieldset>
		<legend><?php echo __('Add Course Schedule'); ?></legend>
	<?php
		echo $this->Form->input('class_room_id');
		echo $this->Form->input('section_id');
		echo $this->Form->input('published_course_id');
		echo $this->Form->input('course_split_section_id');
		echo $this->Form->input('acadamic_year');
		echo $this->Form->input('semester');
		echo $this->Form->input('type');
		echo $this->Form->input('ClassPeriod');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Course Schedules'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Class Rooms'), array('controller' => 'class_rooms', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Class Room'), array('controller' => 'class_rooms', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Sections'), array('controller' => 'sections', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Section'), array('controller' => 'sections', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Course Split Sections'), array('controller' => 'course_split_sections', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Course Split Section'), array('controller' => 'course_split_sections', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Class Periods'), array('controller' => 'class_periods', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Class Period'), array('controller' => 'class_periods', 'action' => 'add')); ?> </li>
	</ul>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
