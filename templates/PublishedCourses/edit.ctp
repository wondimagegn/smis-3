<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="publishedCourses form">
<?php echo $this->Form->create('PublishedCourse');?>
	<fieldset>
		<legend><?php echo __('Edit Published Course'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('year_level_id');
		echo $this->Form->input('semester');
		echo $this->Form->input('course_id');
		echo $this->Form->input('program_type_id');
		echo $this->Form->input('program_id');
		echo $this->Form->input('department_id');
		echo $this->Form->input('section_id');
		echo $this->Form->input('published');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
