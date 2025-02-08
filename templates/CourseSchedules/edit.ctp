<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="courseSchedules form">
<?php echo $this->Form->create('CourseSchedule');?>
	<fieldset>
		<legend><?php echo __('Edit Course Schedule'); ?></legend>
	<?php
		echo $this->Form->input('id');
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
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
