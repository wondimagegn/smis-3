<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="excludedPublishedCourseExams form">
<?php echo $this->Form->create('ExcludedPublishedCourseExam');?>
	<fieldset>
 		<legend><?php echo __('Add Excluded Published Course Exam'); ?></legend>
	<?php
		echo $this->Form->input('published_course_id');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'));?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
