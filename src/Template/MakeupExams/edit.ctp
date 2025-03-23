<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="makeupExams form">
<?php echo $this->Form->create('MakeupExam');?>
	<fieldset>
		<legend><?php echo __('Edit Makeup Exam'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('minute_number');
		echo $this->Form->input('published_course_id');
		echo $this->Form->input('section_id');
		echo $this->Form->input('student_id');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
