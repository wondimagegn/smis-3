<div class="examRoomNumberOfInvigilators form">
<?php echo $this->Form->create('ExamRoomNumberOfInvigilator');?>
<div class="smallheading"><?php echo __('Edit Exam Room Number Of Invigilator'); ?></div>
<div class="font"><?php echo 'Institute/College: '.$college_name?></div>
<table>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->hidden('class_room_id');
		echo "<tr><td>".$this->Form->input('academic_year',array('label' => 'Academic Year','id'=>'academicyear','type'=>'select','options'=>$acyear_array_data))."</td>";
		echo "<td>".$this->Form->input('semester',array('id'=>'semester', 'options'=>array('I'=>'I','II'=>'II', 'III'=>'III')))."</td>";

		echo "<td>".$this->Form->input('number_of_invigilator')."</td></tr>";
		echo "<tr><td colspan='3'>".$this->Form->Submit('Submit', array('name'=>'submit','class'=>'tiny radius button bg-blue','div'=>false))."</td></tr>";
		
	?>
	</table>
</div>
