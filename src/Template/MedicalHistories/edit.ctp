<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="medicalHistories form">
<?php echo $this->Form->create('MedicalHistory');?>
<div class="smallheading"><?php echo __('Edit Medical History'); ?></div>
	<table cellpadding="0" cellspacing="0">
<?php 

		echo $this->Form->input('id');
		echo $this->Form->hidden('student_id');
		echo $this->Form->hidden('user_id');
		//echo $this->Form->hidden('student_id',array('value'=>$student_id));
		echo '<tr><td class="font">Recod Type</td><td>'.$this->Form->input('record_type',array('id'=>'id_record_type','label'=>false,'type'=>'select', 'options'=>array('chef complaint'=>'Chef Complaint', 'laboratory instruction'=>'Laboratory Instruction', 'laboratory result'=>'Laboratory Result', 'prescriptions'=>'Prescriptions', 'other'=>'Other'), 'empty'=>'---Please Select Record Type---'
)).'</td></tr>';
		echo '<tr><td class="font"> Details </td><td>'.$this->Form->input('details',array('id'=>'id_details','label'=>false,'cols'=>'80','rows'=>'10')).'</td></tr>';
?>
	</table>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
