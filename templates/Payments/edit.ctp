<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="payments form">
<?php echo $this->Form->create('Payment');?>
	<fieldset>
		<legend><?php echo __('Edit Payment'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('academic_year');
		echo $this->Form->input('semester');
		echo $this->Form->input('reference_number');
		echo $this->Form->input('fee_amount');
		echo $this->Form->input('tutition_fee');
		echo $this->Form->input('meal');
		echo $this->Form->input('accomodation');
		echo $this->Form->input('health');
		echo $this->Form->input('payment_date');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
