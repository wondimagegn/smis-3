<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
              
<div class="classPeriods form">
<?php echo $this->Form->create('ClassPeriod');?>
	<fieldset>
 		<legend><?php echo __('Edit Class Period'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('week_day');
		echo $this->Form->input('period_setting_id');
		echo $this->Form->input('college_id');
		echo $this->Form->input('program_type_id');
		echo $this->Form->input('program_id');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
