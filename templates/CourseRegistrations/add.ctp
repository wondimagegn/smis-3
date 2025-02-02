<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="courseRegistrations form">
<?php echo $this->Form->create('CourseRegistration');?>
	<fieldset>
		<legend><?php echo __('Add Course Registration'); ?></legend>
	<?php
		echo $this->Form->input('year_level_id');
		echo $this->Form->input('academic_calendar_id');
		echo $this->Form->input('academic_year');
		echo $this->Form->input('student_id');
		echo $this->Form->input('course_id');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
