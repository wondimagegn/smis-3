<div class="examPeriods form">
<?php echo $this->Form->create('ExamPeriod');?>
<div class="smallheading"><?php echo __('Add Exam Period'); ?></div>
<div class="font"><?php echo 'Colege/Institute: '.$college_name ?></div>
<table cellpadding="0" cellspacing="0">
	<?php 
		echo $this->Form->hidden('college_id',array('value'=>$college_id));
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('academic_year',array('label'=>false,'type'=>'select','options'=>$acyear_array_data,'empty'=>"--Select Academic Year--", 'style'=>'width:150PX')).'</td>';
        echo '<td class="font"> Semester</td>';
		echo '<td >'.$this->Form->input('semester',array('label'=>false,'options'=>array('I'=>'I','II'=>'II','III'=>'III'), 'empty'=>'--select semester--', 'style'=>'width:150PX')).'</td>'; 
		echo '<td class="font"> Program</td>';
        echo '<td>'. $this->Form->input('program_id',array('label' => false,'empty'=>"--Select Program--", 'style'=>'width:150PX')).'</td></tr>'; 
        echo '<tr><td class="font"> Program Type</td>';
        echo '<td>'. $this->Form->input('program_type_id',array('label' => false,'style'=>'width:150PX','type'=>'select','multiple'=>'checkbox')).'</td>'; 
        echo '<td class="font"> Year Level</td>';
		echo '<td>'. $this->Form->input('year_level_id',array('label' => false, 'style'=>'width:150PX', 'type'=>'select', 'multiple'=>'checkbox')).'</td></tr>';
		echo '<tr><td class="font"> Start Date</td>';
		echo '<td colspan="2">'.$this->Form->input('start_date', array('label' => false, 'style'=>'width:80PX')).'</td>';
		echo '<td class="font"> End Date</td>';
		echo '<td colspan="2">'.$this->Form->input('end_date', array('label' => false, 'style'=>'width:80PX')).'</td></tr>';
		echo '<tr><td colspan="2" class="font"> Default Number of Invigilator Per Exam</td>';
		echo '<td colspan="2" class="font">'.$this->Form->input('default_number_of_invigilator_per_exam',array('label' => false,'options'=>array('1'=>'1','2'=>'2','3'=>'3', '4'=>'4','5'=>'5','6'=>'6', '7'=>'7','8'=>'8', '9'=>'9','10'=>'10','11'=>'11','12'=>'12'), 'selected'=>'2', 'style'=>'width:100PX')).'</td></tr>'; 
        echo '<tr><td colspan="6">'.$this->Form->submit(__('Submit'), array('div' => false,'class'=>'tiny radius button bg-blue')).'</td></tr>'; 
	?> 
</table>
<?php
//echo $this->Form->end();
?>
</div>

