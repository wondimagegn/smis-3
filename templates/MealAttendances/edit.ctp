<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="mealAttendances form">
<?php echo $this->Form->create('MealAttendance');?>
	<fieldset>
		<legend><?php echo __('Edit Meal Attendance'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('meal_type_id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('accepted_student_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
