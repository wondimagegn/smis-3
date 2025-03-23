<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="mealHallAssignments form">
<?php echo $this->Form->create('MealHallAssignment');?>
	<fieldset>
		<legend><?php echo __('Edit Meal Hall Assignment'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('meal_hall_id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('accepted_student_id');
		echo $this->Form->input('academic_year');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
