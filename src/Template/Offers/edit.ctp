<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="offers form">
<?php echo $this->Form->create('Offer');?>
	<fieldset>
		<legend><?php echo __('Edit Offer'); ?></legend>
	<?php
echo $this->Form->input('id');
		echo $this->Form->input('department_id');
		echo $this->Form->input('program_type_id');
		echo $this->Form->input('acadamicyear');
	?>
	</fieldset>
	
<?php echo $this->Form->end(array('label'=>'Submit','class'=>'tiny radius button bg-blue'));
?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
