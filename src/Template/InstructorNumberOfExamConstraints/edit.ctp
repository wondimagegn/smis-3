<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="instructorNumberOfExamConstraints form">
<?php echo $this->Form->create('InstructorNumberOfExamConstraint');?>
	<fieldset>
		<legend><?php echo __('Edit Instructor Number Of Exam Constraint'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('staff_id');
		echo $this->Form->input('staff_for_exam_id');
		echo $this->Form->input('academic_year');
		echo $this->Form->input('semester');
		echo $this->Form->input('year_level_id');
		echo $this->Form->input('max_number_of_exam');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
