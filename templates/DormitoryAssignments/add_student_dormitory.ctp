<?php echo $this->Form->create('DormitoryAssignment',array('action'=>'add_student_dormitory_update', 'method'=>"GET"));
	echo $this->Form->input('Selected_student_id',array('label'=>'Students','id'=>'Selected_student_id','type'=>'select',
       'options'=>$unassigned_students_array,'onchange'=>"this.form.submit();",'empty'=>"--Select Student--"));
	echo $this->Form->hidden('dormitory_id', array('value'=>$dormitory_id));
	//echo $students;
?>
