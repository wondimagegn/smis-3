<div class="examPeriods form">
<?php echo $this->Form->create('ExamPeriod');?>
<div class="smallheading"><?php echo __('Edit Exam Period'); ?></div>
<div class="font"><?php echo 'Colege/Institute: '.$college_name ?></div>
<table cellpadding="0" cellspacing="0">
	<?php
		echo $this->Form->input('id');
		echo $this->Form->hidden('college_id');
		echo '<tr><td>'.$this->Form->input('program_id',array('disabled'=>'disabled')).'</td>';
		echo '<td>'.$this->Form->input('program_type_id',array('disabled'=>'disabled')).'</td></tr>';
		echo '<tr><td>'.$this->Form->input('academic_year',array('disabled'=>'disabled')).'</td>';
		echo '<td>'.$this->Form->input('semester',array('disabled'=>'disabled')).'</td>';
		echo '<td>'.$this->Form->input('year_level_id',array('disabled'=>'disabled')).'</td></tr>';
		echo '<tr><td>'.$this->Form->input('start_date').'</td>';
		echo '<td>'.$this->Form->input('end_date').'</td></tr>';
		echo '<tr><td colspan="3" class="font">'.$this->Form->input('default_number_of_invigilator_per_exam',array('options'=>array('2'=>'2','3'=>'3', '4'=>'4','5'=>'5','6'=>'6', '7'=>'7','8'=>'8','9'=>'9', '10'=>'10','11'=>'11','12'=>'12'))).'</td></tr>'; 
	?>
<tr><td colspan=3><?php echo $this->Form->end(__('Submit'));?></td></tr>
</table>
</div>
