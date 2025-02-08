<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
		
<div class="examSchedules form">
<?php echo $this->Form->create('ExamSchedule');?>
	<fieldset>
		<legend><?php echo __('Edit Exam Schedule'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('class_room_id');
		echo $this->Form->input('published_course_id');
		echo $this->Form->input('acadamic_year');
		echo $this->Form->input('semester');
		echo $this->Form->input('exam_date');
		echo $this->Form->input('session');
	?>
	</fieldset>
<?php 
echo $this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'));
?>
</div>            
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

