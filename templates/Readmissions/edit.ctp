<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="readmissions form">
<?php echo $this->Form->create('Readmission');?>
	<fieldset>
		<legend><?php echo __('Edit Readmission'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('academic_year');
		echo $this->Form->input('semester');
		echo $this->Form->input('minute_number');
		echo $this->Form->input('registrar_approval');
		echo $this->Form->input('registrar_approval_date');
		echo $this->Form->input('registrar_approved_by');
		echo $this->Form->input('academic_commision_approval');
		echo $this->Form->input('academic_commision_approval_date');
		echo $this->Form->input('remark');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
