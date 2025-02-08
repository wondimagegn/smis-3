<?php echo $this->Form->create('MealHallAssignment',array('action'=>'add_student_meal_hall_update', 'method'=>"GET"));
	echo $this->Form->input('Selected_student_id',array('label'=>'Students','id'=>'Selected_student_id', 'type'=>'select','options'=>$unassigned_students_array,'onchange'=>"this.form.submit();",'empty'=>"--Select Student--"));
	echo $this->Form->hidden('meal_hall_id', array('value'=>$meal_hall_id));
	echo $this->Form->hidden('selected_academicyear', array('value'=>$selected_academicyear));
	//echo $students;
?>
