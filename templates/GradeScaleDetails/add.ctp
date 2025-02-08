<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="gradeScaleDetails form">
<?php echo $this->Form->create('GradeScaleDetail');?>
	<fieldset>
		<legend><?php echo __('Add Grade Scale Detail'); ?></legend>
	<?php
		echo $this->Form->input('minimum_result');
		echo $this->Form->input('maximum_result');
		echo $this->Form->input('grade_scale_id');
		echo $this->Form->input('grade_id');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'));?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
