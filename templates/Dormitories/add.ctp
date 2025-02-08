<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="dormitories form">
<?php echo $this->Form->create('Dormitory');?>
	<fieldset>
		<legend><?php echo __('Add Dormitory'); ?></legend>
	<?php
		echo $this->Form->input('dormitory_block_id');
		echo $this->Form->input('dorm_number');
		echo $this->Form->input('floor');
		echo $this->Form->input('capacity');
		echo $this->Form->input('available');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>


	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
