<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="programTypeTransfers form">
<?php echo $this->Form->create('ProgramTypeTransfer');?>
	<fieldset>
		<legend><?php echo __('Edit Program Type Transfer'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('program_type_id');
		echo $this->Form->input('transfer_date');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
